<?php
/**
 * Helper Functions and Session Management
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize text input to prevent XSS attacks
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if the user is currently authenticated
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user ID
 */
function get_current_user_id() {
    return is_logged_in() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get full user profile data from database
 */
function get_current_user_data($pdo) {
    if (!is_logged_in()) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id, username, email, full_name, bio, created_at FROM users WHERE id = ?");
    $stmt->execute([get_current_user_id()]);
    return $stmt->fetch();
}

/**
 * Guard page: Require user authentication
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash_message('warning', 'Please log in to access this page.');
        header('Location: ' . get_base_url() . 'auth/login.php');
        exit;
    }
}

/**
 * Set a session flash message (success, danger, warning, info)
 */
function set_flash_message($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear session flash messages
 */
function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Calculate total days and nights between two dates
 */
function calculate_trip_days($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $days = (int)$interval->format('%a') + 1;
    $nights = max(0, $days - 1);
    return [
        'days' => $days,
        'nights' => $nights,
        'label' => "{$days} Days / {$nights} Nights"
    ];
}

/**
 * Format currency with symbol
 */
function format_currency($amount, $currency = 'USD') {
    $amount = (float)$amount;
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'LKR' => 'Rs. ',
        'JPY' => '¥',
        'AUD' => 'A$'
    ];
    $sym = $symbols[$currency] ?? '$';
    return $sym . number_format($amount, 2);
}

/**
 * Get total calculated expenditure for an itinerary
 */
function get_itinerary_total_spent($pdo, $itinerary_id) {
    $stmt = $pdo->prepare("SELECT SUM(cost) as total FROM itinerary_items WHERE itinerary_id = ?");
    $stmt->execute([(int)$itinerary_id]);
    $row = $stmt->fetch();
    return (float)($row['total'] ?? 0.00);
}

/**
 * Get dynamic base URL for seamless links across local servers
 */
function get_base_url() {
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // If inside /auth or /includes, get parent directory
    if (substr($script_dir, -5) === '/auth') {
        $base = substr($script_dir, 0, -5);
    } elseif (substr($script_dir, -9) === '/includes') {
        $base = substr($script_dir, 0, -9);
    } else {
        $base = $script_dir;
    }
    return rtrim($base, '/') . '/';
}
