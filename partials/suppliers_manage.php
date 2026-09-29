<?php
include '../includes/db.php';
$suppliers = $db->query("SELECT * FROM suppliers ORDER BY name")->fetchAll();
$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_s = null;
if ($edit_id) {
    $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_s = $stmt->fetch();
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Supplier Management</h3>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header"><?= $edit_s ? 'Edit Supplier' : 'Add New Supplier' ?></div>
    <div class="card-body">
        <form id="supplier-form" onsubmit="saveSupplier(event)" class="row g-3">
            <input type="hidden" name="id" value="<?= $edit_s['id'] ?? '' ?>">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Supplier Name" value="<?= htmlspecialchars($edit_s['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="website" class="form-control" placeholder="Website" value="<?= htmlspecialchars($edit_s['website'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($edit_s['email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="text" name="phone" class="form-control" placeholder="Phone" value="<?= htmlspecialchars($edit_s['phone'] ?? '') ?>">
            </div>
            <div class="col-md-8">
                <input type="text" name="notes" class="form-control" placeholder="Notes" value="<?= htmlspecialchars($edit_s['notes'] ?? '') ?>">
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary"><?= $edit_s ? 'Update' : 'Create' ?></button>
                <?php if($edit_s): ?>
                    <button type="button" class="btn btn-secondary" onclick="loadPartial('suppliers_manage')">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Website</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($suppliers as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['website']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['phone']) ?></td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadPartial('suppliers_manage', {edit_id: <?= (int)$s['id'] ?>})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(this)" data-id="<?= (int)$s['id'] ?>" data-name="<?= htmlspecialchars($s['name']) ?>" data-type="supplier">Delete</button>
                </div>
            </td>
        </tr><?php endforeach; ?>
    </tbody>
</table>
