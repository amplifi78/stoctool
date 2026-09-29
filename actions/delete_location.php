<?php
include '../includes/db.php';
header('Content-Type: application/json');
$id = (int)($_POST['id'] ?? 0);

try {
    // Refuse deletion while dependents exist, rather than orphaning
    // item assignments and stock history. Foreign keys are enforced,
    // but we check first so we can give a clear message.
    $stmt = $db->prepare("SELECT COUNT(*) FROM item_groups WHERE location_id = ?");
    $stmt->execute([$id]);
    $item_count = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM stock_checks WHERE location_id = ?");
    $stmt->execute([$id]);
    $check_count = (int)$stmt->fetchColumn();

    if ($item_count > 0 || $check_count > 0) {
        echo json_encode([
            'success' => false,
            'message' => "This location still has $item_count assigned item(s) and $check_count stock check(s). Remove them before deleting the location."
        ]);
        exit;
    }

    $db->prepare("DELETE FROM locations WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Could not delete location.']);
}
