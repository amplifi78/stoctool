<?php
include '../includes/db.php';
$items = $db->query("SELECT i.*, s.name as s_name FROM items i LEFT JOIN suppliers s ON i.supplier_id = s.id ORDER BY i.name")->fetchAll();
$suppliers = $db->query("SELECT * FROM suppliers ORDER BY name")->fetchAll();

$edit_id = $_GET['edit_id'] ?? 0;
$edit_i = $edit_id ? $db->query("SELECT * FROM items WHERE id = $edit_id")->fetch() : null;
?>
<h3>Global Item Management</h3>

<div class="card mb-4 shadow-sm">
    <div class="card-header"><?= $edit_i ? 'Edit Item' : 'Create New Item' ?></div>
    <div class="card-body">
        <form onsubmit="saveItem(event)" class="row g-3">
            <input type="hidden" name="id" value="<?= $edit_i['id'] ?? '' ?>">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Item Name" value="<?= htmlspecialchars($edit_i['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <select name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier...</option>
                    <?php foreach($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (isset($edit_i['supplier_id']) && $edit_i['supplier_id'] == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="code" class="form-control" placeholder="SKU/Code" value="<?= htmlspecialchars($edit_i['code'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><?= $edit_i ? 'Update' : 'Create' ?></button>
            </div>
            <?php if ($edit_i): ?>
            <div class="col-12 mt-2">
                <button type="button" class="btn btn-sm btn-secondary" onclick="loadPartial('items_manage')">Cancel Edit</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Supplier</th>
            <th>Code</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $i): ?>
        <tr>
            <td><?= htmlspecialchars($i['name']) ?></td>
            <td><?= htmlspecialchars($i['s_name']) ?></td>
            <td><?= htmlspecialchars($i['code'] ?? '-') ?></td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadPartial('items_manage', {edit_id: <?= $i['id'] ?>})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $i['id'] ?>, '<?= addslashes(htmlspecialchars($i['name'])) ?>', 'item')">Delete</button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
// Scripts moved to js/app.js
</script>
