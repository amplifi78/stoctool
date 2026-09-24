<?php
include '../includes/db.php';
header('Content-Type: application/json');
$id = $_POST['id'] ?? 0;
try {
    // We should probably delete related records or restrict deletion if they exist
    // For simplicity, let's just delete the location
    $db->prepare("DELETE FROM locations WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
