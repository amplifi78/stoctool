<?php
include '../includes/db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$name = $_POST['name'];
$sid = $_POST['supplier_id'];
$code = $_POST['code'] ?? '';

if ($id) {
    $stmt = $db->prepare("UPDATE items SET name = ?, supplier_id = ?, code = ? WHERE id = ?");
    $success = $stmt->execute([$name, $sid, $code, $id]);
} else {
    $stmt = $db->prepare("INSERT INTO items (name, supplier_id, code) VALUES (?, ?, ?)");
    $success = $stmt->execute([$name, $sid, $code]);
}

echo json_encode(['success' => $success]);