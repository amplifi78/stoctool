<?php
session_start();
$col = $_POST['col'] ?? 'location';
$prev = $_SESSION['sort_col'] ?? '';
$dir = $_SESSION['sort_dir'] ?? 'ASC';

if ($col === $prev) {
    $dir = ($dir === 'ASC') ? 'DESC' : 'ASC';
} else {
    $dir = 'ASC';
}

$_SESSION['sort_col'] = $col;
$_SESSION['sort_dir'] = $dir;

echo json_encode(['success' => true]);