<?php
include '../includes/db.php';
header('Content-Type: application/json');

try {
    $loc_id = $_POST['location_id'];
    $items = $_POST['items'] ?? [];

    $db->beginTransaction();

    // Create a new stock check event
    $stmt = $db->prepare("INSERT INTO stock_checks (location_id) VALUES (?)");
    $stmt->execute([$loc_id]);
    $check_id = $db->lastInsertId();

    // Record individual counts
    $stmt = $db->prepare("INSERT INTO stock_records (check_id, item_id, item_count) VALUES (?, ?, ?)");
    foreach ($items as $item_id => $count) {
        $count = (trim($count) === '') ? 0 : $count;
        $stmt->execute([$check_id, $item_id, $count]);
    }

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
