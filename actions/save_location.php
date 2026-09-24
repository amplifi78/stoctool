<?php
include '../includes/db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$location = $_POST['location'] ?? '';
$building = $_POST['building'] ?? '';
$site = $_POST['site'] ?? '';

try {
    if ($id) {
        $stmt = $db->prepare("UPDATE locations SET location = ?, building = ?, site = ? WHERE id = ?");
        $success = $stmt->execute([$location, $building, $site, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO locations (location, building, site) VALUES (?, ?, ?)");
        $success = $stmt->execute([$location, $building, $site]);
    }
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
