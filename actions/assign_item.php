<?php
include '../includes/db.php';
header('Content-Type: application/json');
$lid = (int)($_POST['location_id'] ?? 0);
$iid = (int)($_POST['item_id'] ?? 0);
$min = (int)($_POST['min_required'] ?? 0);

try {
    $stmt = $db->prepare("INSERT INTO item_groups (location_id, item_id, min_required) VALUES (?, ?, ?)");
    $success = $stmt->execute([$lid, $iid, $min]);
    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    // UNIQUE(location_id, item_id) violation, or FK failure
    $msg = str_contains($e->getMessage(), 'UNIQUE')
        ? 'That item is already assigned to this location.'
        : 'Could not assign item. Check the item and location exist.';
    echo json_encode(['success' => false, 'message' => $msg]);
}
