<?php
include '../includes/db.php';
header('Content-Type: application/json');
$id = $_POST['id'];
$success = $db->prepare("DELETE FROM item_groups WHERE id = ?")->execute([$id]);
echo json_encode(['success' => $success]);