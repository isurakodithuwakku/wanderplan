<?php
/**
 * Explore Destinations & Features Page (explore.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Explore Top Destinations & Travel Guides";

// Fetch destinations
$stmt = $pdo->query("SELECT * FROM destinations ORDER BY rating DESC");
$destinations = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Explore Header Banner -->
<div class="py-5 bg-dark text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-4 text-center">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-3">
            <i class="bi bi-compass me-1"></i> Global Destinations & Ideas
        </span>
        <h1 class="display-5 fw-bold mb-3">Discover Where To Go Next</h1>
        <p class="text-white-50 fs-5 max-w-xl mx-auto" style="max-width: 650px;">
            Browse curated itineraries, verified local daily costs, and handpicked sights across tropical islands, alpine peaks, and cultural capitals.
        </p>

        <!-- Search Bar with Live Filter -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-6 col-md-8">
                <div class="input-group input-group-lg shadow-lg">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-primary"></i></span>
                    <input type="text" id="destinationSearchInput" class="form-control border-0" placeholder="Type destination, country or season...">
                    <button class="btn btn-primary px-4 fw-semibold" type="button">Search</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Destination Catalog & Category Filters -->
<div class="container py-5">
    <!-- Category Filter Buttons (Dynamic Content Filter JS) -->
    <div class="filter-btn-group">
        <button type="button" class="filter-btn active" data-filter="all"><i class="bi bi-grid-fill me-1"></i> All Guides</button>
        <button type="button" class="filter-btn" data-filter="mountain"><i class="bi bi-triangle me-1"></i> Mountains</button>
        <button type="button" class="filter-btn" data-filter="beach"><i class="bi bi-water me-1"></i> Beaches & Islands</button>
        <button type="button" class="filter-btn" data-filter="cultural"><i class="bi bi-bank me-1"></i> Cultural Heritage</button>
        <button type="button" class="filter-btn" data-filter="city"><i class="bi bi-buildings me-1"></i> City Breaks</button>
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
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($dest['country']) ?></p>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-primary fs-5">$<?= number_format($dest['estimated_cost_per_day'], 0) ?></span>
                            <div class="text-muted small">/ day est.</div>
                        </div>
                    </div>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        <?= htmlspecialchars($dest['description']) ?>
                    </p>
                    <div class="bg-light p-2 rounded-3 small mb-3 d-flex justify-content-between">
                        <span><i class="bi bi-sun text-warning me-1"></i> Best time:</span>
                        <strong class="text-dark"><?= htmlspecialchars($dest['best_season']) ?></strong>
                    </div>
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
                            <i class="bi bi-eye me-1"></i> Quick View
                        </button>
                        <a href="create_itinerary.php?dest=<?= urlencode($dest['name'] . ', ' . $dest['country']) ?>" class="btn btn-primary btn-sm rounded-pill">
                            <i class="bi bi-calendar-plus me-1"></i> Plan Itinerary
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Fallback if filtered out -->
    <div id="noDestinationsFound" class="text-center py-5" style="display: none;">
        <i class="bi bi-geo-alt fs-1 text-muted"></i>
        <h4 class="fw-bold mt-2">No matching destination found</h4>
        <p class="text-muted">Try changing your search term or select "All Guides".</p>
    </div>
</div>

<!-- Features Section (Detailed Requirements Alignment) -->
<div class="py-5 bg-light border-top">
    <div class="container py-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-2">Core Features</span>
            <h2 class="display-6 fw-bold">Built For Seamless Travel Planning</h2>
            <p class="text-muted">Everything you need from initial brainstorming to the moment you board your flight.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 border-0 shadow-sm">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex p-3 fs-3 mb-3">
                        <i class="bi bi-calendar-range"></i>
                    </div>
                    <h4 class="fw-bold">Interactive Day-by-Day Timelines</h4>
                    <p class="text-muted">Schedule your morning sights, afternoon lunch spots, and evening excursions with exact timestamps and locations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 border-0 shadow-sm">
                    <div class="rounded-circle bg-success-subtle text-success d-inline-flex p-3 fs-3 mb-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h4 class="fw-bold">Live Budget & Expense Control</h4>
                    <p class="text-muted">Keep an eye on ticket costs, hotel stays, and dining expenses with dynamic calculation bars and warning indicators.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 border-0 shadow-sm">
                    <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex p-3 fs-3 mb-3">
                        <i class="bi bi-printer"></i>
                    </div>
                    <h4 class="fw-bold">Offline Export & Printing</h4>
                    <p class="text-muted">Print your completed travel itinerary cleanly on paper or save it as a PDF for offline access when traveling without Wi-Fi.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
