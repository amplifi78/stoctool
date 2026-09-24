<?php
include '../includes/db.php';
header('Content-Type: application/json');
$id = $_POST['id'];

try {
    $db->beginTransaction();
    
    // Set supplier_id to null for items associated with this supplier
    $db->prepare("UPDATE items SET supplier_id = NULL WHERE supplier_id = ?")->execute([$id]);
    
    // Then delete the supplier
    $success = $db->prepare("DELETE FROM suppliers WHERE id = ?")->execute([$id]);
    
    $db->commit();
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
