<?php
include '../includes/db.php';
$locations = $db->query("SELECT *, (SELECT COUNT(*) FROM item_groups WHERE location_id = locations.id) as item_count FROM locations ORDER BY site, building, location")->fetchAll();
$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_l = null;
if ($edit_id) {
    $stmt = $db->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_l = $stmt->fetch();
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Location Management</h3>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header"><?= $edit_l ? 'Edit Location' : 'Add New Location' ?></div>
    <div class="card-body">
        <form id="location-form" onsubmit="saveLocation(event)" class="row g-3">
            <input type="hidden" name="id" value="<?= $edit_l['id'] ?? '' ?>">
            <div class="col-md-4">
                <input type="text" name="location" class="form-control" placeholder="Location Name (e.g. Hall cupboard 1)" value="<?= htmlspecialchars($edit_l['location'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="building" class="form-control" placeholder="Building (e.g. Office)" value="<?= htmlspecialchars($edit_l['building'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="text" name="site" class="form-control" placeholder="Site (e.g. Garden Suburb)" value="<?= htmlspecialchars($edit_l['site'] ?? '') ?>">
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary"><?= $edit_l ? 'Update' : 'Create' ?></button>
                <?php if($edit_l): ?>
                    <button type="button" class="btn btn-secondary" onclick="loadPartial('locations_manage')">Cancel</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Site</th>
            <th>Building</th>
            <th>Location</th>
            <th>Items</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($locations as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['site']) ?></td>
            <td><?= htmlspecialchars($l['building']) ?></td>
            <td><?= htmlspecialchars($l['location']) ?></td>
            <td><a href="javascript:void(0)" onclick="loadPartial('location_items', {id: <?= (int)$l['id'] ?>})"><?= (int)$l['item_count'] ?></a></td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadPartial('locations_manage', {edit_id: <?= (int)$l['id'] ?>})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(this)" data-id="<?= (int)$l['id'] ?>" data-name="<?= htmlspecialchars($l['location']) ?>" data-type="location">Delete</button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
