<?php
include '../includes/db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$website = $_POST['website'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$notes = $_POST['notes'] ?? '';

try {
    if ($id) {
        $stmt = $db->prepare("UPDATE suppliers SET name = ?, website = ?, email = ?, phone = ?, notes = ? WHERE id = ?");
        $success = $stmt->execute([$name, $website, $email, $phone, $notes, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO suppliers (name, website, email, phone, notes) VALUES (?, ?, ?, ?, ?)");
        $success = $stmt->execute([$name, $website, $email, $phone, $notes]);
    }
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
