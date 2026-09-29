<?php
include '../includes/db.php';
$loc_id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM locations WHERE id = ?");
$stmt->execute([$loc_id]);
$loc = $stmt->fetch();
$stmt = $db->prepare("SELECT i.*, ig.min_required, ig.id as group_id FROM items i JOIN item_groups ig ON i.id = ig.item_id WHERE ig.location_id = ? ORDER BY i.name");
$stmt->execute([$loc_id]);
$items = $stmt->fetchAll();
$all_items = $db->query("SELECT * FROM items ORDER BY name")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1 class="display-5 mb-0"><?= htmlspecialchars($loc['location']) ?></h1>
        <p class="text-muted"><?= htmlspecialchars($loc['building']) ?> <?= htmlspecialchars($loc['site']) ?></p>
    </div>
    <button class="btn btn-secondary btn-sm" onclick="loadPartial('locations_manage')">Back</button>
</div>

<p class="lead">Manage Minimum Levels</p>
<div class="card mb-4 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="width:120px">Min Req.</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $i): ?>
                <tr>
                    <td class="align-middle"><?= htmlspecialchars($i['name']) ?></td>
                    <td><input type="number" id="min-<?= (int)$i['group_id'] ?>" class="form-control form-control-sm" value="<?= (int)$i['min_required'] ?>"></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="updateMin(<?= (int)$i['group_id'] ?>)">Save</button>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDeleteGroup(this)"
                                    data-group-id="<?= (int)$i['group_id'] ?>"
                                    data-item-name="<?= htmlspecialchars($i['name']) ?>"
                                    data-loc-name="<?= htmlspecialchars($loc['location']) ?>"
                                    data-loc-details="<?= htmlspecialchars($loc['building'] . ' ' . $loc['site']) ?>"
                                    data-loc-id="<?= $loc_id ?>">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Add Item to Location</div>
    <div class="card-body">
        <form onsubmit="addItemToLoc(event, <?= $loc_id ?>)">
            <input type="hidden" name="location_id" value="<?= $loc_id ?>">
            <div class="input-group">
                <select name="item_id" class="form-select" required>
                    <option value="">Select Item...</option>
                    <?php foreach($all_items as $ai): ?>
                    <option value="<?= (int)$ai['id'] ?>"><?= htmlspecialchars($ai['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="min_required" class="form-control" placeholder="Min" style="max-width:80px" required>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</div>
