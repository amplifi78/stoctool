<?php
include '../includes/db.php';
header('Content-Type: application/json');
$gid = $_POST['group_id'];
$min = $_POST['min'];
$success = $db->prepare("UPDATE item_groups SET min_required = ? WHERE id = ?")->execute([$min, $gid]);
echo json_encode(['success' => $success]);
