<?php
/**
 * Delete Itinerary Action (delete_itinerary.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user_id = get_current_user_id();
$itinerary_id = (int)($_GET['id'] ?? 0);

if ($itinerary_id > 0) {
    // Delete itinerary only if owned by user (or demo user)
    $stmt = $pdo->prepare("DELETE FROM itineraries WHERE id = ? AND (user_id = ? OR ? = 1)");
    $stmt->execute([$itinerary_id, $user_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        set_flash_message('success', 'Trip itinerary and its activities were successfully deleted.');
    } else {
        set_flash_message('danger', 'Itinerary could not be deleted or does not exist.');
    }
}

header('Location: dashboard.php');
exit;
