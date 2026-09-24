<?php
include '../includes/db.php';
header('Content-Type: application/json');
$id = $_POST['id'];

try {
    $db->beginTransaction();
    
    // First remove references from item_groups (which links items to locations)
    $db->prepare("DELETE FROM item_groups WHERE item_id = ?")->execute([$id]);
    
    // Then delete the item itself
    $success = $db->prepare("DELETE FROM items WHERE id = ?")->execute([$id]);
    
    $db->commit();
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}