<?php
include '../includes/db.php';
header('Content-Type: application/json');
$name = $_POST['name'];
$sid = $_POST['supplier_id'];
$code = $_POST['code'] ?? '';

$stmt = $db->prepare("INSERT INTO items (name, supplier_id, code) VALUES (?, ?, ?)");
$success = $stmt->execute([$name, $sid, $code]);

echo json_encode(['success' => $success]);
