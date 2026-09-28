<?php
/**
 * Global Header Component
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['SCRIPT_NAME']);
$base_url = get_base_url();
$page_title = isset($page_title) ? $page_title . " | WanderPlan" : "WanderPlan - Smart Travel Itinerary Planner";
$current_user = is_logged_in() ? get_current_user_data($pdo) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plan, organize, and budget your dream travel adventures with WanderPlan interactive itinerary creator.">
    <meta name="author" content="Faculty of Technology, Rajarata University of Sri Lanka - ICT 2209">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg glass-nav sticky-top py-3" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url ?>index.php">
                <span class="logo-icon"><i class="bi bi-compass-fill"></i></span>
                <span>Wander<span class="text-primary">Plan</span></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="<?= $base_url ?>index.php">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'explore.php' ? 'active' : '' ?>" href="<?= $base_url ?>explore.php">
                            <i class="bi bi-globe-americas me-1"></i> Explore Destinations
                        </a>
                    </li>
                    <?php if (is_logged_in()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="<?= $base_url ?>dashboard.php">
                            <i class="bi bi-kanban me-1"></i> My Itineraries
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>" href="<?= $base_url ?>contact.php">
                            <i class="bi bi-envelope me-1"></i> Contact Us
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if (is_logged_in()): ?>
                        <a href="<?= $base_url ?>create_itinerary.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-none d-sm-inline-flex align-items-center gap-1">
                            <i class="bi bi-plus-circle"></i> Create Trip
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill border py-1 px-3 d-flex align-items-center gap-2 dropdown-toggle shadow-sm" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5 text-primary"></i>
                                <span class="fw-semibold small"><?= htmlspecialchars($current_user['username'] ?? 'User') ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="userMenuDropdown">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($current_user['full_name'] ?? 'Traveler') ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($current_user['email'] ?? '') ?></div>
                                </li>
                                <li><a class="dropdown-item py-2" href="<?= $base_url ?>dashboard.php"><i class="bi bi-grid me-2 text-primary"></i>Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="<?= $base_url ?>create_itinerary.php"><i class="bi bi-plus-lg me-2 text-success"></i>New Itinerary</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-danger" href="<?= $base_url ?>auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= $base_url ?>auth/login.php" class="btn btn-outline-custom btn-sm">Sign In</a>
                        <a href="<?= $base_url ?>auth/register.php" class="btn btn-primary btn-sm">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages Notification Banner -->
    <?php $flash_messages = get_flash_messages(); ?>
    <?php if (!empty($flash_messages)): ?>
    <div class="container mt-3">
        <?php foreach ($flash_messages as $flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm rounded-3 d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <?php if ($flash['type'] === 'success'): ?>
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <?php elseif ($flash['type'] === 'danger'): ?>
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                    <?php elseif ($flash['type'] === 'warning'): ?>
                        <i class="bi bi-exclamation-circle-fill fs-5 text-warning"></i>
                    <?php else: ?>
                        <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <main class="flex-grow-1">
