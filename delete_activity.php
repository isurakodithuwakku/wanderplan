<?php
/**
 * Delete Individual Activity Stop (delete_activity.php)
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user_id = get_current_user_id();
$activity_id = (int)($_GET['id'] ?? 0);
$itinerary_id = (int)($_GET['itinerary_id'] ?? 0);

if ($activity_id > 0 && $itinerary_id > 0) {
    // Verify itinerary ownership
    $check = $pdo->prepare("SELECT id FROM itineraries WHERE id = ? AND (user_id = ? OR ? = 1)");
    $check->execute([$itinerary_id, $user_id, $user_id]);

    if ($check->fetch()) {
        $del = $pdo->prepare("DELETE FROM itinerary_items WHERE id = ? AND itinerary_id = ?");
        $del->execute([$activity_id, $itinerary_id]);
        set_flash_message('success', 'Activity removed from itinerary.');
    } else {
        set_flash_message('danger', 'Unauthorized action.');
    }
}

header('Location: view_itinerary.php?id=' . $itinerary_id);
exit;
