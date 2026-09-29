<?php
include '../includes/db.php';

$sort_col = $_SESSION['sort_col'] ?? 'l.location';
$sort_dir = ($_SESSION['sort_dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

// Allow mapping simple names to DB columns
$sort_map = [
    'location' => 'l.site, l.building, l.location',
    'item' => 'i.name',
    'min' => 'ig.min_required',
    'current' => 'current_stock',
    'needed' => '(ig.min_required - IFNULL((SELECT sr.item_count FROM stock_records sr JOIN stock_checks sc ON sr.check_id = sc.id WHERE sr.item_id = i.id AND sc.location_id = l.id ORDER BY sc.check_timestamp DESC LIMIT 1), 0))',
    'checked' => 'last_checked',
    'supplier' => 's.name'
];

$order_by = $sort_map[$sort_col] ?? 'l.site, l.building, l.location';

$sql = "SELECT 
            i.name as item_name, 
            l.location, l.building, l.site,
            ig.min_required,
            (SELECT sr.item_count FROM stock_records sr 
             JOIN stock_checks sc ON sr.check_id = sc.id 
             WHERE sr.item_id = i.id AND sc.location_id = l.id 
             ORDER BY sc.check_timestamp DESC LIMIT 1) as current_stock,
            (SELECT sc.check_timestamp FROM stock_records sr 
             JOIN stock_checks sc ON sr.check_id = sc.id 
             WHERE sr.item_id = i.id AND sc.location_id = l.id 
             ORDER BY sc.check_timestamp DESC LIMIT 1) as last_checked,
            s.name as supplier_name, s.id as supplier_id,
            s.website, s.phone, s.email, s.notes
        FROM item_groups ig
        JOIN items i ON ig.item_id = i.id
        JOIN locations l ON ig.location_id = l.id
        LEFT JOIN suppliers s ON i.supplier_id = s.id
        ORDER BY $order_by $sort_dir, i.name";

$rows = $db->query($sql)->fetchAll();
$all_locations = $db->query("SELECT DISTINCT site, building, location FROM locations ORDER BY site, building, location")->fetchAll();
$pre_filter_loc = $_GET['location_filter'] ?? '';
?>
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3>Inventory Status</h3>
        <button class="btn btn-secondary btn-sm" onclick="loadPartial('locations')">Back</button>
    </div>
    <div class="row g-2">
        <div class="col-md-6">
            <select id="filter-location" class="form-select form-select-sm" onchange="filterInventory()">
                <option value="">All Locations</option>
                <?php foreach ($all_locations as $loc): 
                    $val = "{$loc['site']} / {$loc['location']}"; ?>
                    <option value="<?= htmlspecialchars($val) ?>" <?= $pre_filter_loc == $val ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <select id="filter-status" class="form-select form-select-sm" onchange="filterInventory()">
                <option value="">All Statuses</option>
                <option value="needed">Needed (Shortage)</option>
                <option value="good">Good (Sufficient)</option>
                <option value="overstocked">Overstocked</option>
            </select>
        </div>
    </div>
</div>

<table class="table table-hover mt-3" id="inventory-table">
    <thead>
        <tr>
            <th class="pointer" onclick="sortInventory('location')">Location <?= $sort_col == 'location' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
            <th class="pointer" onclick="sortInventory('item')">Item <?= $sort_col == 'item' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
            <th class="pointer" onclick="sortInventory('checked')">Checked <?= $sort_col == 'checked' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
            <th class="pointer text-center" onclick="sortInventory('min')">Min <?= $sort_col == 'min' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
            <th class="pointer text-center" onclick="sortInventory('current')">Current <?= $sort_col == 'current' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
            <th class="pointer text-center" onclick="sortInventory('needed')">Need <?= $sort_col == 'needed' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
            <th class="pointer" onclick="sortInventory('supplier')">Supplier <?= $sort_col == 'supplier' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): 
            $current = $r['current_stock'] ?? 0;
            $net = $r['min_required'] - $current;
            $status = 'good';
            $class = '';
            if ($net > 0) { $status = 'needed'; $class = 'table-danger'; }
            elseif ($net < 0) { $status = 'overstocked'; $class = 'table-success'; }
            
            $loc_class = htmlspecialchars("{$r['site']} / {$r['location']}");
            $loc_val = "<b>" . htmlspecialchars($r['location']) . "</b> <small>" . htmlspecialchars($r['building']) . ", " . htmlspecialchars($r['site']) . "</small>";
            $checked = $r['last_checked'] ? local_time($r['last_checked'], 'G:i d/m') : 'Never';
        ?>
        <tr class="inventory-row <?= $class ?>" data-location="<?= $loc_class ?>" data-status="<?= $status ?>">
            <td><?= $loc_val ?></td>
            <td><?= htmlspecialchars($r['item_name']) ?></td>
            <td><small><?= $checked ?></small></td>
            <td class="text-center"><?= (int)$r['min_required'] ?></td>
            <td class="text-center"><?= (int)$current ?></td>
            <td class="text-center"><b><?= (int)$net ?></b></td>
            <td>
                <?php if ($r['supplier_id']): ?>
                <button class="btn btn-outline-primary btn-sm w-100" 
                        onclick="showSupplierModal(this)"
                        data-name="<?= htmlspecialchars($r['supplier_name']) ?>"
                        data-website="<?= htmlspecialchars($r['website']) ?>"
                        data-phone="<?= htmlspecialchars($r['phone']) ?>"
                        data-email="<?= htmlspecialchars($r['email']) ?>"
                        data-notes="<?= htmlspecialchars($r['notes']) ?>">
                    <?= htmlspecialchars($r['supplier_name']) ?>
                </button>
                <?php else: ?>
                <span class="text-muted small">No supplier</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
