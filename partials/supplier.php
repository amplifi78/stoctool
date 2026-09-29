<?php
include '../includes/db.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();
if (!$s) {
    echo '<div class="alert alert-warning">Supplier not found.</div>';
    return;
}
?>
<div class="card shadow">
    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">Supplier Details</h5>
        <button type="button" class="btn-close btn-close-white" onclick="loadPartial('inventory')"></button>
    </div>
    <div class="card-body">
        <h4><?= htmlspecialchars($s['name']) ?></h4>
        <hr>
        <p><strong>Website:</strong> <a href="<?= htmlspecialchars($s['website']) ?>" target="_blank"><?= htmlspecialchars($s['website']) ?></a></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($s['phone']) ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($s['email']) ?>"><?= htmlspecialchars($s['email']) ?></a></p>
        <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($s['notes'])) ?></p>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary" onclick="loadPartial('inventory')">Back to Report</button>
    </div>
</div>
