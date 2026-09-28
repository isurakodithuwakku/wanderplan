<?php
/**
 * User Itineraries Dashboard (dashboard.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Guard page: Require user login (Requirement 3.3)
require_login();

$user_id = get_current_user_id();
$user = get_current_user_data($pdo);
$page_title = "My Travel Itineraries - Dashboard";

// Fetch all itineraries for this user
$stmt = $pdo->prepare("SELECT * FROM itineraries WHERE user_id = ? ORDER BY start_date ASC");
$stmt->execute([$user_id]);
$itineraries = $stmt->fetchAll();

// Calculate user statistics
$total_trips = count($itineraries);
$total_budget_allocated = 0;
$total_activities_count = 0;
$upcoming_trips_count = 0;
$today = date('Y-m-d');

foreach ($itineraries as &$itin) {
    $total_budget_allocated += (float)$itin['budget'];
    if ($itin['start_date'] >= $today) {
        $upcoming_trips_count++;
    }

    // Get total activities and spent for this trip
    $act_stmt = $pdo->prepare("SELECT COUNT(*) as act_count, SUM(cost) as total_spent FROM itinerary_items WHERE itinerary_id = ?");
    $act_stmt->execute([$itin['id']]);
    $act_row = $act_stmt->fetch();

    $itin['activity_count'] = (int)($act_row['act_count'] ?? 0);
    $itin['total_spent'] = (float)($act_row['total_spent'] ?? 0.00);
    $total_activities_count += $itin['activity_count'];

    $duration = calculate_trip_days($itin['start_date'], $itin['end_date']);
    $itin['duration_label'] = $duration['label'];
}
unset($itin);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Dashboard Header -->
<div class="py-5 bg-dark text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-2">
                    <i class="bi bi-person-badge me-1"></i> Traveler Control Center
                </span>
                <h1 class="display-6 fw-bold mb-1">Hello, <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>!</h1>
                <p class="text-white-50 mb-0">Manage your upcoming itineraries, track activity costs, and craft new journey schedules.</p>
            </div>
            <div>
                <a href="create_itinerary.php" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                    <i class="bi bi-plus-circle me-1"></i> Create New Trip
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Stat Widgets -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="glass-card p-3 p-md-4 text-center border-0 shadow-sm">
                <div class="text-primary fs-3 mb-2"><i class="bi bi-map-fill"></i></div>
                <div class="h2 fw-bold mb-1"><?= $total_trips ?></div>
                <div class="text-muted small fw-semibold">Total Trips</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="glass-card p-3 p-md-4 text-center border-0 shadow-sm">
                <div class="text-info fs-3 mb-2"><i class="bi bi-calendar-check-fill"></i></div>
                <div class="h2 fw-bold mb-1"><?= $upcoming_trips_count ?></div>
                <div class="text-muted small fw-semibold">Upcoming Journeys</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="glass-card p-3 p-md-4 text-center border-0 shadow-sm">
                <div class="text-warning fs-3 mb-2"><i class="bi bi-wallet-fill"></i></div>
                <div class="h2 fw-bold mb-1">$<?= number_format($total_budget_allocated, 0) ?></div>
                <div class="text-muted small fw-semibold">Total Budget Allocated</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="glass-card p-3 p-md-4 text-center border-0 shadow-sm">
                <div class="text-success fs-3 mb-2"><i class="bi bi-pin-map-fill"></i></div>
                <div class="h2 fw-bold mb-1"><?= $total_activities_count ?></div>
                <div class="text-muted small fw-semibold">Activities & Stops</div>
            </div>
        </div>
    </div>

    <!-- Section Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Your Trip Itineraries</h3>
            <p class="text-muted small mb-0">Click any trip to view its chronological daily timetable and manage budget items.</p>
        </div>
        <div>
            <a href="create_itinerary.php" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="bi bi-plus-lg me-1"></i> New Itinerary
            </a>
        </div>
    </div>

    <?php if (empty($itineraries)): ?>
        <!-- Empty State -->
        <div class="glass-card text-center py-5 px-4 my-4 border-0 shadow-sm">
            <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex p-4 fs-1 mb-3">
                <i class="bi bi-compass"></i>
            </div>
            <h4 class="fw-bold">No Travel Itineraries Yet</h4>
            <p class="text-muted max-w-md mx-auto mb-4" style="max-width: 500px;">
                You haven't scheduled any trips yet. Pick your dream destination and start building your custom daily travel plan!
            </p>
            <a href="create_itinerary.php" class="btn btn-primary rounded-pill px-4 py-2">
                <i class="bi bi-plus-circle me-1"></i> Create Your First Itinerary
            </a>
        </div>
    <?php else: ?>
        <!-- Itineraries Grid -->
        <div class="row g-4">
            <?php foreach ($itineraries as $itin): ?>
                <?php
                $budget = (float)$itin['budget'];
                $spent = (float)$itin['total_spent'];
                $percent = $budget > 0 ? min(100, round(($spent / $budget) * 100)) : 0;
                ?>
                <div class="col-lg-6">
                    <div class="glass-card overflow-hidden border-0 shadow-sm h-100 d-flex flex-column">
                        <div class="row g-0 flex-grow-1">
                            <div class="col-md-5 position-relative" style="min-height: 200px;">
                                <img src="<?= htmlspecialchars($itin['cover_image'] ?: 'images/dest_srilanka.jpg') ?>" 
                                     alt="<?= htmlspecialchars($itin['title']) ?>" 
                                     class="w-100 h-100" style="object-fit: cover;">
                                <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-3 px-3 py-1 rounded-pill small">
                                    <?= htmlspecialchars($itin['travel_type']) ?>
                                </span>
                            </div>
                            <div class="col-md-7 p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary mb-1">
                                            <?= htmlspecialchars($itin['duration_label']) ?>
                                        </span>
                                        <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($itin['title']) ?></h4>
                                        <div class="text-muted small">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($itin['destination']) ?>
                                        </div>
                                    </div>
                                    <span class="badge bg-<?= $itin['status'] === 'Completed' ? 'secondary' : ($itin['status'] === 'Ongoing' ? 'warning text-dark' : 'success') ?> rounded-pill">
                                        <?= htmlspecialchars($itin['status']) ?>
                                    </span>
                                </div>

                                <div class="small text-secondary mb-3 mt-1">
                                    <i class="bi bi-calendar-event me-1"></i> <?= date('M d, Y', strtotime($itin['start_date'])) ?> – <?= date('M d, Y', strtotime($itin['end_date'])) ?>
                                </div>

                                <!-- Budget mini bar -->
                                <div class="bg-light p-3 rounded-3 mb-3 mt-auto">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Budget: $<?= number_format($budget, 0) ?></span>
                                        <span class="fw-semibold <?= $spent > $budget ? 'text-danger' : 'text-success' ?>">
                                            Spent: $<?= number_format($spent, 0) ?> (<?= $percent ?>%)
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-<?= $percent > 90 ? 'danger' : ($percent > 75 ? 'warning' : 'primary') ?>" 
                                             role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mt-2">
                                        <span><i class="bi bi-list-check me-1"></i> <?= $itin['activity_count'] ?> Activities</span>
                                        <span>Remaining: $<?= number_format($budget - $spent, 0) ?></span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <a href="view_itinerary.php?id=<?= $itin['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-calendar2-range me-1"></i> View Itinerary
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="edit_itinerary.php?id=<?= $itin['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill" title="Edit Trip Details">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_itinerary.php?id=<?= $itin['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill" 
                                           onclick="return confirm('Are you sure you want to delete this trip itinerary? All activities will be removed.');" title="Delete Trip">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
