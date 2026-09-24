<?php
include '../includes/db.php';
header('Content-Type: application/json');
$lid = $_POST['location_id'];
$iid = $_POST['item_id'];
$min = $_POST['min_required'];
$success = $db->prepare("INSERT INTO item_groups (location_id, item_id, min_required) VALUES (?, ?, ?)")->execute([$lid, $iid, $min]);
echo json_encode(['success' => $success]);
