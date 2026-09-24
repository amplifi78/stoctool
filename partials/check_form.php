<?php
include '../includes/db.php';
$loc_id = $_GET['id'] ?? 0;
$loc = $db->query("SELECT * FROM locations WHERE id = $loc_id")->fetch();
$items = $db->query("SELECT i.*, ig.min_required FROM items i JOIN item_groups ig ON i.id = ig.item_id WHERE ig.location_id = $loc_id")->fetchAll();
?>
<h3>Stock Check: <?= htmlspecialchars($loc['location'] ?? 'Unknown') ?></h3>
<p class="text-muted"><?= date('l, jS F Y') ?></p>

<form id="stock-form" onsubmit="saveCheck(event)">
    <input type="hidden" name="location_id" value="<?= $loc_id ?>">
    <table class="table table-hover">
        <thead>
            <tr><th>Item</th><th style="width:150px">Count</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($i['name']) ?></strong><br>
                    <small class="text-muted">Min required: <?= $i['min_required'] ?></small>
                </td>
                <td>
                    <input type="number" name="items[<?= $i['id'] ?>]" class="form-control" placeholder="0" min="0">
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="2" class="text-center">No items assigned to this location.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="mt-3">
        <button type="submit" class="btn btn-success">Save Stock Check</button>
        <button type="button" class="btn btn-secondary" onclick="loadPartial('locations')">Cancel</button>
    </div>
</form>

<script>
// Scripts moved to js/app.js
</script>
