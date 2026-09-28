<?php
/**
 * Edit Trip Itinerary (edit_itinerary.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user_id = get_current_user_id();
$itinerary_id = (int)($_GET['id'] ?? 0);

if (!$itinerary_id) {
    set_flash_message('danger', 'Itinerary not found.');
    header('Location: dashboard.php');
    exit;
}

// Fetch itinerary
$stmt = $pdo->prepare("SELECT * FROM itineraries WHERE id = ? AND (user_id = ? OR ? = 1)");
$stmt->execute([$itinerary_id, $user_id, $user_id]);
$itinerary = $stmt->fetch();

if (!$itinerary) {
    set_flash_message('danger', 'You do not have permission to edit this trip.');
    header('Location: dashboard.php');
    exit;
}

$page_title = "Edit Itinerary - " . htmlspecialchars($itinerary['title']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $budget = (float)($_POST['budget'] ?? 0);
    $currency = trim($_POST['currency'] ?? 'USD');
    $travel_type = trim($_POST['travel_type'] ?? 'Solo');
    $status = trim($_POST['status'] ?? 'Planned');
    $cover_image = trim($_POST['cover_image'] ?? $itinerary['cover_image']);
    $notes = trim($_POST['notes'] ?? '');

    if (empty($title) || strlen($title) < 3) {
        $errors[] = "Trip title must be at least 3 characters.";
    }
    if (empty($destination)) {
        $errors[] = "Destination is required.";
    }
    if (empty($start_date) || empty($end_date)) {
        $errors[] = "Both start and end dates are required.";
    } elseif ($end_date < $start_date) {
        $errors[] = "End date cannot be earlier than start date.";
    }

    if (empty($errors)) {
        $update_stmt = $pdo->prepare("UPDATE itineraries SET title = ?, destination = ?, start_date = ?, end_date = ?, budget = ?, currency = ?, travel_type = ?, status = ?, cover_image = ?, notes = ? WHERE id = ?");
        try {
            $update_stmt->execute([
                $title,
                $destination,
                $start_date,
                $end_date,
                $budget,
                $currency,
                $travel_type,
                $status,
                $cover_image,
                $notes,
                $itinerary_id
            ]);

            set_flash_message('success', 'Trip details updated successfully!');
            header('Location: view_itinerary.php?id=' . $itinerary_id);
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
} else {
    $title = $itinerary['title'];
    $destination = $itinerary['destination'];
    $start_date = $itinerary['start_date'];
    $end_date = $itinerary['end_date'];
    $budget = $itinerary['budget'];
    $currency = $itinerary['currency'];
    $travel_type = $itinerary['travel_type'];
    $status = $itinerary['status'];
    $cover_image = $itinerary['cover_image'];
    $notes = $itinerary['notes'];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-3">
        <div class="d-flex align-items-center gap-3">
            <a href="view_itinerary.php?id=<?= $itinerary_id ?>" class="btn btn-outline-light btn-sm rounded-pill"><i class="bi bi-arrow-left"></i> View Itinerary</a>
            <div>
                <h1 class="h3 fw-bold mb-0">Edit Itinerary</h1>
                <p class="text-white-50 small mb-0">Update dates, budget goals, or destination for "<?= htmlspecialchars($itinerary['title']) ?>".</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 my-2">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-4 mb-4">
                    <ul class="mb-0 small ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="glass-card p-4 p-md-5 border-0 shadow-sm">
                <form action="edit_itinerary.php?id=<?= $itinerary_id ?>" method="POST" id="tripForm" novalidate>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
                                <label for="title"><i class="bi bi-pencil me-1"></i> Trip Title *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="status" name="status">
                                    <option value="Planned" <?= $status === 'Planned' ? 'selected' : '' ?>>Planned</option>
                                    <option value="Ongoing" <?= $status === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                                    <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <label for="status"><i class="bi bi-flag me-1"></i> Status</label>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="destination" name="destination" value="<?= htmlspecialchars($destination) ?>" required>
                                <label for="destination"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Destination *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="travel_type" name="travel_type">
                                    <option value="Solo" <?= $travel_type === 'Solo' ? 'selected' : '' ?>>Solo Traveler</option>
                                    <option value="Couple" <?= $travel_type === 'Couple' ? 'selected' : '' ?>>Couple</option>
                                    <option value="Friends" <?= $travel_type === 'Friends' ? 'selected' : '' ?>>Friends</option>
                                    <option value="Family" <?= $travel_type === 'Family' ? 'selected' : '' ?>>Family</option>
                                    <option value="Business" <?= $travel_type === 'Business' ? 'selected' : '' ?>>Business</option>
                                </select>
                                <label for="travel_type"><i class="bi bi-people me-1"></i> Travel Style</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
                                <label for="start_date"><i class="bi bi-calendar-event me-1"></i> Start Date *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
                                <label for="end_date"><i class="bi bi-calendar-check me-1"></i> End Date *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill" id="tripDurationBadge">
                                <i class="bi bi-calendar-check me-1"></i> Calculating duration...
                            </span>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" min="0" class="form-control" id="budget" name="budget" value="<?= htmlspecialchars($budget) ?>" required>
                                <label for="budget"><i class="bi bi-wallet2 text-success me-1"></i> Budget *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" <?= $currency === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                    <option value="EUR" <?= $currency === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                    <option value="GBP" <?= $currency === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                    <option value="LKR" <?= $currency === 'LKR' ? 'selected' : '' ?>>LKR (Rs)</option>
                                    <option value="JPY" <?= $currency === 'JPY' ? 'selected' : '' ?>>JPY (¥)</option>
                                    <option value="AUD" <?= $currency === 'AUD' ? 'selected' : '' ?>>AUD (A$)</option>
                                </select>
                                <label for="currency"><i class="bi bi-cash me-1"></i> Currency</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="notes" name="notes" style="height: 120px"><?= htmlspecialchars($notes) ?></textarea>
                                <label for="notes"><i class="bi bi-sticky me-1"></i> Trip Notes</label>
                            </div>
                        </div>

                        <div class="col-12 pt-3 d-flex justify-content-between">
                            <a href="view_itinerary.php?id=<?= $itinerary_id ?>" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                                <i class="bi bi-check2-circle me-1"></i> Update Itinerary
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
