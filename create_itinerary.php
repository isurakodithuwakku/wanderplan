<?php
/**
 * Create New Travel Itinerary (create_itinerary.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user_id = get_current_user_id();
$page_title = "Plan a New Travel Itinerary";
$errors = [];

// Pre-fill parameters if redirected from Explore or Quick Start
$pre_dest = trim($_GET['dest'] ?? '');
$pre_start = trim($_GET['start'] ?? date('Y-m-d', strtotime('+3 days')));
$pre_end = trim($_GET['end'] ?? date('Y-m-d', strtotime('+8 days')));
$pre_type = trim($_GET['type'] ?? 'Solo');

$title = $pre_dest ? "Trip to " . explode(',', $pre_dest)[0] : '';
$destination = $pre_dest;
$start_date = $pre_start;
$end_date = $pre_end;
$budget = '500.00';
$currency = 'USD';
$travel_type = $pre_type;
$cover_image = 'images/dest_srilanka.jpg';
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $budget = trim($_POST['budget'] ?? '0.00');
    $currency = trim($_POST['currency'] ?? 'USD');
    $travel_type = trim($_POST['travel_type'] ?? 'Solo');
    $cover_image = trim($_POST['cover_image'] ?? 'images/dest_srilanka.jpg');
    $notes = trim($_POST['notes'] ?? '');
    $activities = $_POST['activities'] ?? [];

    // Validation
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
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO itineraries (user_id, title, destination, start_date, end_date, budget, currency, travel_type, cover_image, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Planned')");
            $stmt->execute([
                $user_id,
                $title,
                $destination,
                $start_date,
                $end_date,
                (float)$budget,
                $currency,
                $travel_type,
                $cover_image,
                $notes
            ]);
            $itinerary_id = $pdo->lastInsertId();

            // Insert any dynamically created activities
            if (!empty($activities) && is_array($activities)) {
                $act_stmt = $pdo->prepare("INSERT INTO itinerary_items (itinerary_id, day_number, activity_time, activity_title, location, category, cost) VALUES (?, ?, ?, ?, ?, ?, ?)");
                foreach ($activities as $act) {
                    $act_title = trim($act['activity_title'] ?? '');
                    if (!empty($act_title)) {
                        $day_num = max(1, (int)($act['day_number'] ?? 1));
                        $act_time = !empty($act['activity_time']) ? $act['activity_time'] : '09:00:00';
                        $loc = trim($act['location'] ?? '');
                        $cat = trim($act['category'] ?? 'Sightseeing');
                        $cost = (float)($act['cost'] ?? 0.00);

                        $act_stmt->execute([
                            $itinerary_id,
                            $day_num,
                            $act_time,
                            $act_title,
                            $loc,
                            $cat,
                            $cost
                        ]);
                    }
                }
            }

            $pdo->commit();
            set_flash_message('success', 'Trip itinerary "' . htmlspecialchars($title) . '" created successfully!');
            header('Location: view_itinerary.php?id=' . $itinerary_id);
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Failed to save itinerary: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-3">
        <div class="d-flex align-items-center gap-3">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div>
                <h1 class="h3 fw-bold mb-0">Create New Travel Itinerary</h1>
                <p class="text-white-50 small mb-0">Set your dates, estimate your budget, and build your daily adventure plan.</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 my-2">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-4 mb-4">
                    <ul class="mb-0 small ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="create_itinerary.php" method="POST" id="tripForm" novalidate>
                <!-- Card 1: Core Trip Details -->
                <div class="glass-card p-4 p-md-5 border-0 shadow-sm mb-4">
                    <h4 class="fw-bold mb-3 text-dark"><i class="bi bi-info-circle text-primary me-2"></i>General Trip Information</h4>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="title" name="title" placeholder="Trip Title" value="<?= htmlspecialchars($title) ?>" required>
                                <label for="title"><i class="bi bi-pencil me-1"></i> Trip Title *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="travel_type" name="travel_type">
                                    <option value="Solo" <?= $travel_type === 'Solo' ? 'selected' : '' ?>>Solo Traveler</option>
                                    <option value="Couple" <?= $travel_type === 'Couple' ? 'selected' : '' ?>>Couple Gateway</option>
                                    <option value="Friends" <?= $travel_type === 'Friends' ? 'selected' : '' ?>>Friends Trip</option>
                                    <option value="Family" <?= $travel_type === 'Family' ? 'selected' : '' ?>>Family Vacation</option>
                                    <option value="Business" <?= $travel_type === 'Business' ? 'selected' : '' ?>>Business & Leisure</option>
                                </select>
                                <label for="travel_type"><i class="bi bi-people me-1"></i> Travel Style</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="destination" name="destination" placeholder="Destination" value="<?= htmlspecialchars($destination) ?>" required>
                                <label for="destination"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Destination (City, Country) *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <!-- Date Picker Section with Live Duration Badge -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
                                <label for="start_date"><i class="bi bi-calendar-event me-1"></i> Departure / Start Date *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
                                <label for="end_date"><i class="bi bi-calendar-check me-1"></i> Return / End Date *</label>
                                <div class="validation-error"></div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill" id="tripDurationBadge">
                                <i class="bi bi-calendar-check me-1"></i> Calculating duration...
                            </span>
                        </div>

                        <!-- Budget & Currency -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" min="0" class="form-control" id="budget" name="budget" placeholder="Budget" value="<?= htmlspecialchars($budget) ?>" required>
                                <label for="budget"><i class="bi bi-wallet2 text-success me-1"></i> Total Estimated Budget *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" <?= $currency === 'USD' ? 'selected' : '' ?>>USD ($ - United States Dollar)</option>
                                    <option value="EUR" <?= $currency === 'EUR' ? 'selected' : '' ?>>EUR (€ - Euro)</option>
                                    <option value="GBP" <?= $currency === 'GBP' ? 'selected' : '' ?>>GBP (£ - British Pound)</option>
                                    <option value="LKR" <?= $currency === 'LKR' ? 'selected' : '' ?>>LKR (Rs - Sri Lankan Rupee)</option>
                                    <option value="JPY" <?= $currency === 'JPY' ? 'selected' : '' ?>>JPY (¥ - Japanese Yen)</option>
                                    <option value="AUD" <?= $currency === 'AUD' ? 'selected' : '' ?>>AUD (A$ - Australian Dollar)</option>
                                </select>
                                <label for="currency"><i class="bi bi-cash me-1"></i> Currency</label>
                            </div>
                        </div>

                        <!-- Cover Theme Image Selector -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark"><i class="bi bi-image me-1"></i> Select Cover Photo Theme</label>
                            <div class="row g-2">
                                <div class="col-md-3 col-6">
                                    <label class="d-block border rounded-3 p-2 text-center cursor-pointer shadow-sm">
                                        <input type="radio" name="cover_image" value="images/dest_srilanka.jpg" checked class="me-1"> Sri Lanka
                                        <img src="images/dest_srilanka.jpg" class="w-100 rounded mt-1" style="height: 60px; object-fit: cover;">
                                    </label>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="d-block border rounded-3 p-2 text-center cursor-pointer shadow-sm">
                                        <input type="radio" name="cover_image" value="images/dest_bali.jpg" class="me-1"> Bali
                                        <img src="images/dest_bali.jpg" class="w-100 rounded mt-1" style="height: 60px; object-fit: cover;">
                                    </label>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="d-block border rounded-3 p-2 text-center cursor-pointer shadow-sm">
                                        <input type="radio" name="cover_image" value="images/dest_switzerland.jpg" class="me-1"> Alps
                                        <img src="images/dest_switzerland.jpg" class="w-100 rounded mt-1" style="height: 60px; object-fit: cover;">
                                    </label>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="d-block border rounded-3 p-2 text-center cursor-pointer shadow-sm">
                                        <input type="radio" name="cover_image" value="images/dest_japan.jpg" class="me-1"> Kyoto
                                        <img src="images/dest_japan.jpg" class="w-100 rounded mt-1" style="height: 60px; object-fit: cover;">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="notes" name="notes" placeholder="Notes" style="height: 100px"><?= htmlspecialchars($notes) ?></textarea>
                                <label for="notes"><i class="bi bi-sticky me-1"></i> Trip Notes & Reminders (Optional)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Dynamic Initial Activities Builder (Dynamic Content Updates JS Feature) -->
                <div class="glass-card p-4 p-md-5 border-0 shadow-sm mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-list-task text-primary me-2"></i>Initial Schedule & Stops</h4>
                            <p class="text-muted small mb-0">Add planned activities now, or add more later directly in the itinerary viewer.</p>
                        </div>
                        <button type="button" id="addDynamicActivityBtn" class="btn btn-outline-primary btn-sm rounded-pill mt-2 mt-sm-0">
                            <i class="bi bi-plus-circle me-1"></i> Add Another Stop
                        </button>
                    </div>

                    <div id="dynamicActivitiesList">
                        <!-- Default Activity Row 1 -->
                        <div class="card mb-3 p-3 dynamic-activity-item border border-gray-200 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">Activity #1</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Day Number</label>
                                    <input type="number" name="activities[1][day_number]" class="form-control form-control-sm" value="1" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Time</label>
                                    <input type="time" name="activities[1][activity_time]" class="form-control form-control-sm" value="09:00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Activity Title *</label>
                                    <input type="text" name="activities[1][activity_title]" class="form-control form-control-sm" placeholder="e.g. Airport Arrival & Hotel Check-in" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Category</label>
                                    <select name="activities[1][category]" class="form-select form-select-sm">
                                        <option value="Transport">Transport</option>
                                        <option value="Accommodation">Accommodation</option>
                                        <option value="Sightseeing" selected>Sightseeing</option>
                                        <option value="Dining">Dining</option>
                                        <option value="Activity">Activity</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Location</label>
                                    <input type="text" name="activities[1][location]" class="form-control form-control-sm" placeholder="e.g. City Center Terminal">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Est. Cost ($)</label>
                                    <input type="number" step="0.01" name="activities[1][cost]" class="form-control form-control-sm" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                        <i class="bi bi-check2-circle me-1"></i> Save & Generate Itinerary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
