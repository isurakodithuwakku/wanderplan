<?php
/**
 * User Registration Page & Logic
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: ../dashboard.php');
    exit;
}

$page_title = "Create Traveler Account";
$errors = [];
$username = '';
$email = '';
$full_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (empty($username) || strlen($username) < 3 || strlen($username) > 20) {
        $errors[] = "Username must be between 3 and 20 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "A user with this username or email already exists. Please sign in or use another.";
        }
    }

    // Insert user into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        
        try {
            $insert_stmt->execute([$username, $email, $hashed_password, $full_name ?: $username]);
            $new_user_id = $pdo->lastInsertId();

            // Set session credentials
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = $full_name ?: $username;

            set_flash_message('success', 'Account created successfully! Welcome to WanderPlan, ' . htmlspecialchars($full_name ?: $username) . '.');
            header('Location: ../dashboard.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: Unable to create account. " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5 my-3">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="glass-card p-4 p-md-5 border-0 shadow-lg">
                <div class="text-center mb-4">
                    <div class="logo-icon mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h2 class="fw-bold">Join WanderPlan</h2>
                    <p class="text-muted">Start crafting your personalized travel itineraries today</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 small ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" id="registerForm" novalidate>
                    <!-- Full Name -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($full_name) ?>" required>
                        <label for="full_name"><i class="bi bi-card-text me-1"></i> Full Name</label>
                    </div>

                    <!-- Username -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required autocomplete="username">
                        <label for="username"><i class="bi bi-person me-1"></i> Username (letters, numbers, _)</label>
                        <div class="validation-error"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?= htmlspecialchars($email) ?>" required autocomplete="email">
                        <label for="email"><i class="bi bi-envelope me-1"></i> Email Address</label>
                        <div class="validation-error"></div>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-2">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="new-password">
                        <label for="password"><i class="bi bi-lock me-1"></i> Password (min 6 characters)</label>
                        <div class="validation-error"></div>
                    </div>

                    <!-- Password Strength Meter -->
                    <div class="mb-3 px-1">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Password Strength:</span>
                            <span id="passwordStrengthText" class="fw-semibold"></span>
                        </div>
                        <div class="password-meter">
                            <div class="password-meter-fill" id="passwordStrengthBar"></div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required autocomplete="new-password">
                        <label for="confirm_password"><i class="bi bi-lock-fill me-1"></i> Confirm Password</label>
                        <div class="validation-error"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                        <i class="bi bi-check2-circle me-1"></i> Create Free Account
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0">Already have an account? 
                        <a href="login.php" class="fw-bold text-primary">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
