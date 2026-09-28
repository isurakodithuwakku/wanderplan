<?php
/**
 * User Login Page & Logic
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

$page_title = "Sign In to WanderPlan";
$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier)) {
        $errors[] = "Please enter your username or email.";
    }
    if (empty($password)) {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        // Query user by username or email
        $stmt = $pdo->prepare("SELECT id, username, email, password, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID for security against session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'] ?: $user['username'];

            set_flash_message('success', 'Welcome back, ' . htmlspecialchars($_SESSION['full_name']) . '!');
            
            // Redirect to destination or dashboard
            $redirect = $_GET['redirect'] ?? '../dashboard.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $errors[] = "Invalid username/email or password. Please try again.";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5 my-3">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="glass-card p-4 p-md-5 border-0 shadow-lg">
                <div class="text-center mb-4">
                    <div class="logo-icon mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="text-muted">Sign in to manage and view your itineraries</p>
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

                <!-- Lecturer / Evaluator Quick Demo Box -->
                <div class="alert alert-info border-0 rounded-3 mb-4 small d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-info-circle-fill me-1"></i> <strong>Demo Credentials:</strong><br>
                        User: <code>demo_user</code> | Pass: <code>password123</code>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillDemoCredentials()">Auto-Fill</button>
                </div>

                <form action="login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" method="POST" id="loginForm" novalidate>
                    <!-- Identifier (Username or Email) -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="identifier" name="identifier" placeholder="Username or Email" value="<?= htmlspecialchars($identifier) ?>" required autocomplete="username">
                        <label for="identifier"><i class="bi bi-person me-1"></i> Username or Email</label>
                        <div class="validation-error"></div>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="current-password">
                        <label for="password"><i class="bi bi-lock me-1"></i> Password</label>
                        <div class="validation-error"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Account
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0">Don't have an account yet? 
                        <a href="register.php" class="fw-bold text-primary">Sign up for free</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillDemoCredentials() {
    document.getElementById('identifier').value = 'demo_user';
    document.getElementById('password').value = 'password123';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
