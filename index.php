<?php
/**
 * Main Home Page (index.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "WanderPlan - Smart Travel Itinerary Planner & Trip Organizer";

// Fetch featured destinations from database
$dest_stmt = $pdo->query("SELECT * FROM destinations ORDER BY id ASC");
$destinations = $dest_stmt->fetchAll();

// Fetch sample itinerary for interactive preview walkthrough
$sample_itin_stmt = $pdo->query("SELECT * FROM itineraries WHERE id = 1 LIMIT 1");
$sample_itinerary = $sample_itin_stmt->fetch();

$sample_items_stmt = $pdo->query("SELECT * FROM itinerary_items WHERE itinerary_id = 1 ORDER BY day_number ASC, activity_time ASC");
$sample_items = $sample_items_stmt->fetchAll();

// Group sample activities by day
$sample_days = [];
foreach ($sample_items as $item) {
    $sample_days[$item['day_number']][] = $item;
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with Background Parallax & Quick Search -->
<section class="hero-wrapper">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="bi bi-stars text-warning"></i>
                    <span>Next-Gen Travel Itinerary Planning</span>
                </div>
                <h1 class="hero-title">
                    Design Your Dream Journey, <br>
                    <span class="gradient-text">Day By Day.</span>
                </h1>
                <p class="hero-subtitle">
                    Organize daily activities, balance travel expenses in real-time, and discover breathtaking destinations with our all-in-one trip planner.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= is_logged_in() ? 'create_itinerary.php' : 'auth/register.php' ?>" class="btn btn-primary btn-lg rounded-pill px-4 shadow-lg">
                        <i class="bi bi-compass me-2"></i> Start Planning Free
                    </a>
                    <a href="#featured-slider" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="bi bi-play-circle me-2"></i> Explore Top Destinations
                    </a>
                </div>

                <!-- Live Quick Stats Banner -->
                <div class="d-flex flex-wrap gap-4 mt-5 pt-3 border-top border-white border-opacity-10 text-white-50 small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <span><strong class="text-white">100% Free</strong> & No ads</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-range text-info fs-5"></i>
                        <span><strong class="text-white">Day-by-Day</strong> Timelines</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-wallet2 text-warning fs-5"></i>
                        <span><strong class="text-white">Live Budget</strong> Tracking</span>
                    </div>
                </div>
            </div>

            <!-- Quick Trip Launcher Card -->
            <div class="col-lg-5">
                <div class="hero-quick-search">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="bi bi-send-fill me-1"></i> Quick Start</span>
                        <h4 class="fw-bold mb-0">Plan A New Trip</h4>
                    </div>
                    <p class="text-muted small mb-4">Select your destination and dates to generate your custom trip planner.</p>

                    <form action="create_itinerary.php" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Destination</label>
                            <input type="text" name="dest" class="form-control" placeholder="e.g. Ella, Sri Lanka or Tokyo, Japan" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark"><i class="bi bi-calendar-event me-1"></i> Start Date</label>
                                <input type="date" name="start" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark"><i class="bi bi-calendar-check me-1"></i> End Date</label>
                                <input type="date" name="end" class="form-control" value="<?= date('Y-m-d', strtotime('+12 days')) ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark"><i class="bi bi-people me-1"></i> Travel Style</label>
                            <select name="type" class="form-select">
                                <option value="Solo">Solo Traveler</option>
                                <option value="Couple" selected>Couple Gateway</option>
                                <option value="Friends">Friends Group Trip</option>
                                <option value="Family">Family Holiday</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                            <i class="bi bi-arrow-right-circle me-1"></i> Create My Itinerary
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Interactive Destination Slider (JavaScript Carousel Feature) -->
<section class="py-5 bg-white" id="featured-slider">
    <div class="container py-4">
        <div class="row justify-content-between align-items-end mb-4">
            <div class="col-md-8">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-images me-1"></i> Curated Inspiration
                </span>
                <h2 class="display-6 fw-bold">Featured Destination Showcase</h2>
                <p class="text-muted mb-0">Use the interactive slider to discover hand-picked travel paradises with verified daily budgets.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Auto-playing (Hover to pause)</span>
            </div>
        </div>

        <!-- Custom Interactive Slider -->
        <div class="slider-container" aria-label="Featured Travel Destinations Slider">
            <div class="slider-track">
                <?php foreach ($destinations as $dest): ?>
                <div class="slider-slide">
                    <img src="<?= htmlspecialchars($dest['image_url']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>">
                    <div class="slider-overlay">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                                <i class="bi bi-star-fill text-dark me-1"></i> <?= htmlspecialchars($dest['rating']) ?> Rating
                            </span>
                            <span class="badge bg-light text-dark px-3 py-1 rounded-pill">
                                <?= htmlspecialchars($dest['category']) ?>
                            </span>
                        </div>
                        <h2 class="display-5 fw-bold text-white mb-2"><?= htmlspecialchars($dest['name']) ?></h2>
                        <p class="text-white-50 fs-5 mb-3" style="max-width: 600px;">
                            <?= htmlspecialchars(substr($dest['description'], 0, 140)) ?>...
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold"
                                data-bs-toggle="modal" data-bs-target="#destinationModal"
                                data-title="<?= htmlspecialchars($dest['name']) ?>"
                                data-country="<?= htmlspecialchars($dest['country']) ?>"
                                data-category="<?= htmlspecialchars($dest['category']) ?>"
                                data-desc="<?= htmlspecialchars($dest['description']) ?>"
                                data-img="<?= htmlspecialchars($dest['image_url']) ?>"
                                data-cost="<?= htmlspecialchars($dest['estimated_cost_per_day']) ?>"
                                data-season="<?= htmlspecialchars($dest['best_season']) ?>">
                                <i class="bi bi-eye me-1"></i> Quick View Details
                            </button>
                            <a href="create_itinerary.php?dest=<?= urlencode($dest['name'] . ', ' . $dest['country']) ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                <i class="bi bi-calendar-plus me-1"></i> Plan This Trip
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Slider Controls -->
            <button class="slider-nav-btn slider-prev" aria-label="Previous destination">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="slider-nav-btn slider-next" aria-label="Next destination">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- Slider Dot Indicators -->
            <div class="slider-dots"></div>
        </div>
    </div>
</section>

<!-- Section 3: Dynamic Filterable Destinations (JavaScript Dynamic Filter Feature) -->
<section class="py-5 bg-light" id="explore-destinations">
    <div class="container py-4">
        <div class="text-center max-w-xl mx-auto mb-4">
            <span class="badge bg-info-subtle text-info-emphasis px-3 py-2 rounded-pill fw-semibold mb-2">
                <i class="bi bi-funnel-fill me-1"></i> Dynamic Discovery
            </span>
            <h2 class="display-6 fw-bold">Explore Destinations by Category</h2>
            <p class="text-muted">Filter by travel interest or type to find your next unforgettable experience.</p>
        </div>

        <!-- Live Search Bar -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-6 col-lg-5">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="destinationSearchInput" class="form-control border-start-0 py-2" placeholder="Search destination, country, or keyword...">
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="filter-btn-group">
            <button type="button" class="filter-btn active" data-filter="all">All Destinations</button>
            <button type="button" class="filter-btn" data-filter="mountain"><i class="bi bi-triangle me-1"></i> Mountain & Nature</button>
            <button type="button" class="filter-btn" data-filter="beach"><i class="bi bi-water me-1"></i> Tropical & Beach</button>
            <button type="button" class="filter-btn" data-filter="cultural"><i class="bi bi-bank me-1"></i> Cultural Heritage</button>
            <button type="button" class="filter-btn" data-filter="city"><i class="bi bi-buildings me-1"></i> City Escapes</button>
        </div>

        <!-- Destinations Grid -->
        <div class="row g-4 mt-2" id="destinationsGrid">
            <?php foreach ($destinations as $dest): ?>
            <div class="col-lg-4 col-md-6 destination-item" 
                 data-category="<?= strtolower(htmlspecialchars($dest['category'])) ?>"
                 data-title="<?= strtolower(htmlspecialchars($dest['name'])) ?>"
                 data-country="<?= strtolower(htmlspecialchars($dest['country'])) ?>">
                <div class="destination-card">
                    <div class="dest-img-wrap">
                        <img src="<?= htmlspecialchars($dest['image_url']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>" loading="lazy">
                        <span class="dest-category-badge"><?= htmlspecialchars($dest['category']) ?></span>
                        <span class="dest-rating-badge"><i class="bi bi-star-fill text-warning"></i> <?= htmlspecialchars($dest['rating']) ?></span>
                    </div>
                    <div class="dest-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h4 class="fw-bold mb-1"><?= htmlspecialchars($dest['name']) ?></h4>
                                <p class="text-muted small mb-0"><i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($dest['country']) ?></p>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary fs-5">$<?= number_format($dest['estimated_cost_per_day'], 0) ?></span>
                                <div class="text-muted small">/ day est.</div>
                            </div>
                        </div>
                        <p class="text-secondary small mb-3 flex-grow-1">
                            <?= htmlspecialchars(substr($dest['description'], 0, 110)) ?>...
                        </p>
                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-custom btn-sm rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#destinationModal"
                                data-title="<?= htmlspecialchars($dest['name']) ?>"
                                data-country="<?= htmlspecialchars($dest['country']) ?>"
                                data-category="<?= htmlspecialchars($dest['category']) ?>"
                                data-desc="<?= htmlspecialchars($dest['description']) ?>"
                                data-img="<?= htmlspecialchars($dest['image_url']) ?>"
                                data-cost="<?= htmlspecialchars($dest['estimated_cost_per_day']) ?>"
                                data-season="<?= htmlspecialchars($dest['best_season']) ?>">
                                <i class="bi bi-info-circle me-1"></i> Details
                            </button>
                            <a href="create_itinerary.php?dest=<?= urlencode($dest['name'] . ', ' . $dest['country']) ?>" class="btn btn-primary btn-sm rounded-pill">
                                <i class="bi bi-calendar-plus me-1"></i> Plan Trip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Fallback Message -->
        <div id="noDestinationsFound" class="text-center py-5" style="display: none;">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h4 class="fw-bold mt-2">No matching destinations found</h4>
            <p class="text-muted">Try changing your search terms or filter category.</p>
        </div>
    </div>
</section>

<!-- Section 4: Interactive Sample Itinerary Walkthrough (Real-world Demonstration) -->
<section class="py-5 bg-white border-top border-bottom" id="interactive-preview">
    <div class="container py-4">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Live Feature Demonstration
                </span>
                <h2 class="display-6 fw-bold">How An Itinerary Works in WanderPlan</h2>
                <p class="text-muted mb-0">Experience our day-by-day activity timelines, category classification, and live budget tracking.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="<?= is_logged_in() ? 'create_itinerary.php' : 'auth/register.php' ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Build Your Own Itinerary
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Interactive Day Tabs and Activities -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-primary mb-1">Sample Trip</span>
                            <h4 class="fw-bold mb-0">Enchanting Sri Lanka Hill Country</h4>
                            <div class="text-muted small mt-1"><i class="bi bi-calendar-range me-1"></i> Oct 10 - Oct 14, 2026 (5 Days / 4 Nights)</div>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <!-- Day Filter Buttons -->
                            <div class="btn-group shadow-sm" role="group">
                                <button type="button" class="btn btn-primary btn-sm day-filter-btn active" data-day="all">All Days</button>
                                <?php foreach (array_keys($sample_days) as $day_num): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm day-filter-btn" data-day="<?= $day_num ?>">Day <?= $day_num ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small fw-bold text-muted"><i class="bi bi-clock-history me-1"></i> Daily Schedule</span>
                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted mb-0">Filter Category:</label>
                            <select id="categoryFilterSelect" class="form-select form-select-sm w-auto">
                                <option value="all">All Categories</option>
                                <option value="Sightseeing">Sightseeing</option>
                                <option value="Dining">Dining</option>
                                <option value="Transport">Transport</option>
                                <option value="Accommodation">Accommodation</option>
                                <option value="Activity">Activity</option>
                            </select>
                        </div>
                    </div>

                    <!-- Timeline List -->
                    <div class="timeline-track mt-3">
                        <?php foreach ($sample_days as $day_num => $activities): ?>
                            <div class="day-timeline-group mb-4" data-day="<?= $day_num ?>">
                                <div class="badge bg-dark px-3 py-2 rounded-pill mb-3">
                                    <i class="bi bi-calendar3 me-1"></i> Day <?= $day_num ?> Program
                                </div>

                                <?php foreach ($activities as $act): ?>
                                    <div class="timeline-item" data-category="<?= htmlspecialchars($act['category']) ?>">
                                        <div class="timeline-node"></div>
                                        <div class="timeline-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge-category badge-<?= strtolower(htmlspecialchars($act['category'])) ?>">
                                                        <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($act['category']) ?>
                                                    </span>
                                                    <h5 class="fw-bold mt-1 mb-0"><?= htmlspecialchars($act['activity_title']) ?></h5>
                                                    <div class="text-muted small">
                                                        <i class="bi bi-clock text-primary me-1"></i> <?= date('h:i A', strtotime($act['activity_time'])) ?>
                                                        <?php if ($act['location']): ?>
                                                            <span class="mx-2">•</span>
                                                            <i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($act['location']) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="fw-bold text-success fs-6">$<?= number_format($act['cost'], 2) ?></span>
                                                </div>
                                            </div>
                                            <?php if ($act['notes']): ?>
                                                <p class="text-secondary small mb-0 mt-2 bg-light p-2 rounded">
                                                    <i class="bi bi-chat-left-text me-1 text-muted"></i> <?= htmlspecialchars($act['notes']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Live Budget Tracker Widget -->
            <div class="col-lg-4">
                <div class="budget-summary-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-white mb-0"><i class="bi bi-wallet2 text-warning me-2"></i>Trip Budget Tracker</h5>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    </div>

                    <?php
                    $budget_total = (float)$sample_itinerary['budget'];
                    $spent_total = get_itinerary_total_spent($pdo, $sample_itinerary['id']);
                    $remaining = $budget_total - $spent_total;
                    $percent = $budget_total > 0 ? min(100, round(($spent_total / $budget_total) * 100)) : 0;
                    ?>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-white-50 mb-1">
                            <span>Budget Consumed</span>
                            <span><?= $percent ?>%</span>
                        </div>
                        <div class="budget-progress">
                            <div class="budget-progress-bar" id="budgetProgressBar" 
                                 style="width: <?= $percent ?>%; background-color: <?= $percent > 85 ? '#f43f5e' : '#10b981' ?>;"></div>
                        </div>
                    </div>

                    <div class="row g-2 text-center pt-2">
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 p-2 rounded-3">
                                <div class="text-white-50 small">Allocated</div>
                                <div class="fw-bold text-white fs-6" id="displayTotalBudget" data-budget="<?= $budget_total ?>">$<?= number_format($budget_total, 0) ?></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 p-2 rounded-3">
                                <div class="text-white-50 small">Spent</div>
                                <div class="fw-bold text-warning fs-6" id="displayTotalSpent" data-spent="<?= $spent_total ?>">$<?= number_format($spent_total, 0) ?></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 p-2 rounded-3">
                                <div class="text-white-50 small">Remaining</div>
                                <div class="fw-bold text-success fs-6" id="displayRemainingBudget">$<?= number_format($remaining, 0) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips & Features Box -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Trip Planner Benefits</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex gap-2 mb-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-dark">Never Forget a Landmark</strong>
                                <span class="small text-muted">Arrange your morning, afternoon, and evening stops chronologically.</span>
                            </div>
                        </li>
                        <li class="d-flex gap-2 mb-3">
                            <i class="bi bi-check-circle-fill text-primary fs-5 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-dark">Avoid Overspending</strong>
                                <span class="small text-muted">Track ticket prices, hotel bookings, and restaurant bills in real-time.</span>
                            </div>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill text-primary fs-5 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-dark">Print-Ready Itineraries</strong>
                                <span class="small text-muted">One-click export allows physical pocket printing or PDF saving.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Why Travelers Love WanderPlan (Stats & Value) -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">14.2K+</div>
                    <div class="text-muted fw-semibold">Itineraries Planned</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">95+</div>
                    <div class="text-muted fw-semibold">Global Destinations</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">$3.8M</div>
                    <div class="text-muted fw-semibold">Travel Budgets Managed</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">4.9 / 5</div>
                    <div class="text-muted fw-semibold">Community Satisfaction</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Call To Action -->
<section class="py-5 bg-white text-center">
    <div class="container py-4">
        <div class="card border-0 glass-card-dark p-5 rounded-4 shadow-lg mx-auto" style="max-width: 900px;">
            <h2 class="display-6 fw-bold text-white mb-3">Ready to Plan Your Next Grand Adventure?</h2>
            <p class="text-white-50 fs-5 mb-4 mx-auto" style="max-width: 650px;">
                Create your customized itinerary with date pickers, interactive activities, and expense control in just a few minutes.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?= is_logged_in() ? 'create_itinerary.php' : 'auth/register.php' ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                    <i class="bi bi-compass-fill me-1"></i> Start Planning Now
                </a>
                <a href="contact.php" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    <i class="bi bi-question-circle me-1"></i> Have Questions?
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
