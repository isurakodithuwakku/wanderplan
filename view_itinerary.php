<?php
/**
 * View & Manage Itinerary Timeline (view_itinerary.php)
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

// Fetch itinerary and ensure it belongs to the logged in user (or allow demo)
$stmt = $pdo->prepare("SELECT * FROM itineraries WHERE id = ? AND (user_id = ? OR ? = 1)");
$stmt->execute([$itinerary_id, $user_id, $user_id]);
$itinerary = $stmt->fetch();

if (!$itinerary) {
    set_flash_message('danger', 'You do not have permission to view this itinerary.');
    header('Location: dashboard.php');
    exit;
}

$page_title = htmlspecialchars($itinerary['title']) . " - Itinerary Details";

// Handle adding a new activity stop via modal form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_activity') {
    $day_number = max(1, (int)($_POST['day_number'] ?? 1));
    $activity_time = !empty($_POST['activity_time']) ? $_POST['activity_time'] : '09:00:00';
    $activity_title = trim($_POST['activity_title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category = trim($_POST['category'] ?? 'Sightseeing');
    $cost = (float)($_POST['cost'] ?? 0.00);
    $act_notes = trim($_POST['notes'] ?? '');

    if (!empty($activity_title)) {
        $insert_act = $pdo->prepare("INSERT INTO itinerary_items (itinerary_id, day_number, activity_time, activity_title, location, category, cost, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_act->execute([
            $itinerary_id,
            $day_number,
            $activity_time,
            $activity_title,
            $location,
            $category,
            $cost,
            $act_notes
        ]);
        set_flash_message('success', 'Added activity "' . htmlspecialchars($activity_title) . '" to Day ' . $day_number . '.');
    } else {
        set_flash_message('danger', 'Activity title is required.');
    }
    header('Location: view_itinerary.php?id=' . $itinerary_id);
    exit;
}

// Fetch all activities sorted by day and time
$items_stmt = $pdo->prepare("SELECT * FROM itinerary_items WHERE itinerary_id = ? ORDER BY day_number ASC, activity_time ASC");
$items_stmt->execute([$itinerary_id]);
$activities = $items_stmt->fetchAll();

// Group activities by day
$grouped_days = [];
foreach ($activities as $act) {
    $grouped_days[$act['day_number']][] = $act;
}

$duration = calculate_trip_days($itinerary['start_date'], $itinerary['end_date']);
$total_budget = (float)$itinerary['budget'];
$total_spent = get_itinerary_total_spent($pdo, $itinerary_id);
$remaining_budget = $total_budget - $total_spent;
$budget_percent = $total_budget > 0 ? min(100, round(($total_spent / $total_budget) * 100)) : 0;

require_once __DIR__ . '/includes/header.php';
?>

<!-- Destination Hero Banner for this trip -->
<div class="position-relative text-white py-5" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.75) 0%, rgba(15, 23, 42, 0.92) 100%), url('<?= htmlspecialchars($itinerary['cover_image'] ?: 'images/dest_srilanka.jpg') ?>'); background-size: cover; background-position: center;">
    <div class="container py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill mb-2 no-print">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary px-3 py-1 rounded-pill"><?= htmlspecialchars($itinerary['travel_type']) ?> Trip</span>
                    <span class="badge bg-<?= $itinerary['status'] === 'Completed' ? 'secondary' : 'success' ?> px-3 py-1 rounded-pill">
                        <?= htmlspecialchars($itinerary['status']) ?>
                    </span>
                </div>
                <h1 class="display-5 fw-bold mb-1"><?= htmlspecialchars($itinerary['title']) ?></h1>
                <p class="fs-5 text-white-50 mb-0">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($itinerary['destination']) ?> 
                    <span class="mx-2">•</span> 
                    <i class="bi bi-calendar-range text-info me-1"></i> <?= date('M d, Y', strtotime($itinerary['start_date'])) ?> – <?= date('M d, Y', strtotime($itinerary['end_date'])) ?> 
                    <span class="badge bg-light text-dark ms-2"><?= $duration['label'] ?></span>
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="d-flex flex-wrap gap-2 no-print">
                <button type="button" class="btn btn-light rounded-pill px-3 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                    <i class="bi bi-plus-circle-fill text-primary me-1"></i> Add Stop / Activity
                </button>
                <a href="edit_itinerary.php?id=<?= $itinerary['id'] ?>" class="btn btn-outline-light rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Trip
                </a>
                <button type="button" class="btn btn-accent rounded-pill px-3 fw-semibold shadow-sm" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> Print / PDF Export
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Main Timeline Schedule Column -->
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5 border-0 shadow-sm mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1">Day-by-Day Daily Schedule</h4>
                        <p class="text-muted small mb-0">Chronological travel schedule with locations, times, and activities.</p>
                    </div>

                    <!-- Interactive Day Filter Buttons -->
                    <div class="mt-2 mt-md-0 no-print">
                        <div class="btn-group shadow-sm flex-wrap" role="group">
                            <button type="button" class="btn btn-primary btn-sm day-filter-btn active" data-day="all">All Days</button>
                            <?php for ($d = 1; $d <= $duration['days']; $d++): ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm day-filter-btn" data-day="<?= $d ?>">Day <?= $d ?></button>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- Category Filter Selection -->
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <span class="small fw-semibold text-muted"><i class="bi bi-funnel me-1"></i> Filter Activity Type:</span>
                    <select id="categoryFilterSelect" class="form-select form-select-sm w-auto">
                        <option value="all">All Activity Categories</option>
                        <option value="Sightseeing">Sightseeing</option>
                        <option value="Dining">Dining</option>
                        <option value="Transport">Transport</option>
                        <option value="Accommodation">Accommodation</option>
                        <option value="Activity">Activity & Adventure</option>
                    </select>
                </div>

                <!-- Timeline Display -->
                <?php if (empty($activities)): ?>
                    <div class="text-center py-5 my-3">
                        <i class="bi bi-calendar-plus fs-1 text-muted"></i>
                        <h5 class="fw-bold mt-2">No Activities Added Yet</h5>
                        <p class="text-muted small mb-3">Begin tailoring your journey by adding morning, afternoon, or evening stops.</p>
                        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                            <i class="bi bi-plus-lg me-1"></i> Add First Activity
                        </button>
                    </div>
                <?php else: ?>
                    <div class="timeline-track">
                        <?php for ($d = 1; $d <= $duration['days']; $d++): ?>
                            <?php $day_activities = $grouped_days[$d] ?? []; ?>
                            <div class="day-timeline-group mb-4" data-day="<?= $d ?>">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-dark px-3 py-2 rounded-pill fs-6">
                                        <i class="bi bi-calendar-event me-1"></i> Day <?= $d ?> Schedule
                                    </span>
                                    <span class="text-muted small">
                                        <?= date('l, M d', strtotime($itinerary['start_date'] . ' +' . ($d - 1) . ' days')) ?>
                                    </span>
                                </div>

                                <?php if (empty($day_activities)): ?>
                                    <div class="p-3 bg-light rounded-3 text-muted small mb-3 border border-dashed text-center">
                                        No stops scheduled for Day <?= $d ?> yet. 
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#addActivityModal" class="fw-bold text-primary ms-1" onclick="document.getElementById('modal_day_number').value = '<?= $d ?>'">+ Add Stop</a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($day_activities as $act): ?>
                                        <div class="timeline-item" data-category="<?= htmlspecialchars($act['category']) ?>">
                                            <div class="timeline-node"></div>
                                            <div class="timeline-card">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <span class="badge-category badge-<?= strtolower(htmlspecialchars($act['category'])) ?>">
                                                            <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($act['category']) ?>
                                                        </span>
                                                        <h5 class="fw-bold mt-1 mb-1 text-dark"><?= htmlspecialchars($act['activity_title']) ?></h5>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-clock text-primary me-1"></i> <?= date('h:i A', strtotime($act['activity_time'])) ?>
                                                            <?php if ($act['location']): ?>
                                                                <span class="mx-2">•</span>
                                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($act['location']) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="fw-bold text-success fs-6">$<?= number_format($act['cost'], 2) ?></span>
                                                        <div class="no-print mt-1">
                                                            <a href="delete_activity.php?id=<?= $act['id'] ?>&itinerary_id=<?= $itinerary['id'] ?>" 
                                                               class="text-danger small" 
                                                               onclick="return confirm('Remove this activity from your itinerary?');" 
                                                               title="Remove activity">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if (!empty($act['notes'])): ?>
                                                    <div class="bg-light p-2 rounded-2 small text-secondary mt-2">
                                                        <i class="bi bi-chat-left-text me-1 text-muted"></i> <?= htmlspecialchars($act['notes']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Notes Section -->
            <?php if (!empty($itinerary['notes'])): ?>
                <div class="glass-card p-4 border-0 shadow-sm">
                    <h5 class="fw-bold mb-2"><i class="bi bi-journal-text text-primary me-2"></i>Trip Notes & Packing Checklist</h5>
                    <p class="text-secondary small mb-0" style="white-space: pre-line;"><?= htmlspecialchars($itinerary['notes']) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Budget & Summary Column -->
        <div class="col-lg-4">
            <!-- Dynamic Budget Tracker Card (Live Calculations JS Feature) -->
            <div class="budget-summary-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0"><i class="bi bi-wallet2 text-warning me-2"></i>Budget Overview</h5>
                    <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($itinerary['currency']) ?></span>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-white-50 mb-1">
                        <span>Budget Consumed</span>
                        <span><?= $budget_percent ?>%</span>
                    </div>
                    <div class="budget-progress">
                        <div class="budget-progress-bar" id="budgetProgressBar" 
                             style="width: <?= $budget_percent ?>%; background-color: <?= $budget_percent > 90 ? '#f43f5e' : ($budget_percent > 70 ? '#f59e0b' : '#10b981') ?>;"></div>
                    </div>
                </div>

                <div class="row g-2 text-center pt-2">
                    <div class="col-4">
                        <div class="bg-white bg-opacity-10 p-2 rounded-3">
                            <div class="text-white-50 small">Allocated</div>
                            <div class="fw-bold text-white fs-6" id="displayTotalBudget" data-budget="<?= $total_budget ?>">$<?= number_format($total_budget, 0) ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-white bg-opacity-10 p-2 rounded-3">
                            <div class="text-white-50 small">Spent</div>
                            <div class="fw-bold text-warning fs-6" id="displayTotalSpent" data-spent="<?= $total_spent ?>">$<?= number_format($total_spent, 0) ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-white bg-opacity-10 p-2 rounded-3">
                            <div class="text-white-50 small">Remaining</div>
                            <div class="fw-bold <?= $remaining_budget < 0 ? 'text-danger' : 'text-success' ?> fs-6" id="displayRemainingBudget">
                                $<?= number_format($remaining_budget, 0) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Add Activity Card (no-print) -->
            <div class="glass-card p-4 border-0 shadow-sm mb-4 no-print text-center">
                <i class="bi bi-clock-history fs-2 text-primary mb-2"></i>
                <h5 class="fw-bold">Add Another Activity</h5>
                <p class="text-muted small mb-3">Map out museums, scenic viewpoints, meals, or train tickets.</p>
                <button type="button" class="btn btn-primary rounded-pill w-100 py-2" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Stop
                </button>
            </div>

            <!-- Trip Metadata Card -->
            <div class="glass-card p-4 border-0 shadow-sm">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Trip Details</h5>
                <ul class="list-unstyled small text-secondary mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span><i class="bi bi-geo-alt me-1 text-danger"></i> Destination</span>
                        <strong class="text-dark"><?= htmlspecialchars($itinerary['destination']) ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span><i class="bi bi-clock me-1 text-info"></i> Duration</span>
                        <strong class="text-dark"><?= $duration['label'] ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span><i class="bi bi-list-task me-1 text-primary"></i> Total Stops</span>
                        <strong class="text-dark"><?= count($activities) ?> items</strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span><i class="bi bi-people me-1 text-warning"></i> Travel Style</span>
                        <strong class="text-dark"><?= htmlspecialchars($itinerary['travel_type']) ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span><i class="bi bi-calendar-check me-1 text-success"></i> Created</span>
                        <strong class="text-dark"><?= date('M d, Y', strtotime($itinerary['created_at'])) ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add New Activity Stop -->
<div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold" id="addActivityModalLabel">
                    <i class="bi bi-calendar-plus text-primary me-1"></i> Add Activity to Itinerary
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="view_itinerary.php?id=<?= $itinerary['id'] ?>" method="POST">
                <input type="hidden" name="action" value="add_activity">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Day Number *</label>
                            <select name="day_number" id="modal_day_number" class="form-select" required>
                                <?php for ($d = 1; $d <= $duration['days']; $d++): ?>
                                    <option value="<?= $d ?>">Day <?= $d ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Scheduled Time</label>
                            <input type="time" name="activity_time" class="form-control" value="10:00" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Activity / Stop Title *</label>
                            <input type="text" name="activity_title" class="form-control" placeholder="e.g. Visit Nine Arch Bridge or Lunch at Bistro" required>
                        </div>

                        <div class="col-6">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" class="form-select">
                                <option value="Sightseeing">Sightseeing</option>
                                <option value="Dining">Dining</option>
                                <option value="Transport">Transport</option>
                                <option value="Accommodation">Accommodation</option>
                                <option value="Activity">Activity / Adventure</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Est. Cost ($)</label>
                            <input type="number" step="0.01" min="0" name="cost" class="form-control" value="0.00">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Location / Address</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Ella Pass, Badulla District">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Activity Notes & Tips</label>
                            <textarea name="notes" class="form-control" placeholder="Bring sunglasses, reserve tickets in advance..." rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
