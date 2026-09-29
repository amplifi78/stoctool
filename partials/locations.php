<?php
include '../includes/db.php';

$sort_col = $_SESSION['sort_col'] ?? 'l.site, l.building, l.location';
$sort_dir = ($_SESSION['sort_dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

$sort_map = [
    'location' => 'l.location',
    'last_check' => 'last_check',
    'items' => 'item_count'
];

$order_by = $sort_map[$sort_col] ?? 'l.site, l.building, l.location';

$stmt = $db->query("SELECT l.*, 
                    (SELECT MAX(check_timestamp) FROM stock_checks WHERE location_id = l.id) as last_check,
                    (SELECT COUNT(*) FROM item_groups WHERE location_id = l.id) as item_count
                    FROM locations l ORDER BY $order_by $sort_dir");
$locs = $stmt->fetchAll();

$sites = array_unique(array_column($locs, 'site'));
$buildings = array_unique(array_column($locs, 'building'));
sort($sites);
sort($buildings);
?>

<div class="row g-2 mb-4 bg-white p-3 rounded shadow-sm">
    <div class="col-md-5">
        <label class="form-label small text-muted mb-1">Filter Site</label>
        <select id="filter-site" class="form-select form-select-sm" onchange="filterLocations()">
            <option value="">All Sites</option>
            <?php foreach($sites as $s): if(!$s) continue; ?>
            <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-5">
        <label class="form-label small text-muted mb-1">Filter Building</label>
        <select id="filter-building" class="form-select form-select-sm" onchange="filterLocations()">
            <option value="">All Buildings</option>
            <?php foreach($buildings as $b): if(!$b) continue; ?>
            <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-sm btn-outline-secondary w-100" onclick="resetLocationFilters()">Clear</button>
    </div>
</div>

<div class="bg-white rounded shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="pointer" onclick="sortLocations('location')">Location <?= $sort_col == 'location' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
                    <th class="pointer" onclick="sortLocations('last_check')">Last Checked <?= $sort_col == 'last_check' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
                    <th class="pointer text-center" onclick="sortLocations('items')">Items <?= $sort_col == 'items' ? ($sort_dir == 'ASC' ? '&uarr;' : '&darr;') : '' ?></th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="location-list">
                <?php if (empty($locs)): ?>
                    <tr><td colspan="4"><div class="alert alert-info mb-0">No locations found. Add some to the database to begin.</div></td></tr>
                <?php endif; ?>
                <?php foreach ($locs as $l): ?>
                <tr class="location-card" data-site="<?= htmlspecialchars($l['site']) ?>" data-building="<?= htmlspecialchars($l['building']) ?>">
                    <td>
                        <div>
                            <span class="fs-5 fw-bold"><?= htmlspecialchars($l['location']) ?></span>
                            <a href="javascript:void(0)" class="ms-1 text-muted" 
                               onclick="showLocationModal(this)"
                               data-id="<?= (int)$l['id'] ?>"
                               data-location="<?= htmlspecialchars($l['location']) ?>"
                               data-building="<?= htmlspecialchars($l['building']) ?>"
                               data-site="<?= htmlspecialchars($l['site']) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </a>
                        </div>
                        <div class="text-muted small">
                            <?= htmlspecialchars($l['building']) ?>, <?= htmlspecialchars($l['site']) ?>
                        </div>
                    </td>
                    <td>
                        <span class="small text-muted">
                            <?= local_time($l['last_check']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="badge bg-secondary text-decoration-none" onclick="loadPartial('location_items', {id: <?= (int)$l['id'] ?>})">
                            <?= (int)$l['item_count'] ?> items
                        </a>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="loadPartial('check_form', {id: <?= (int)$l['id'] ?>})">
                                Do Stocktake
                            </button>
                            <button class="btn btn-outline-secondary" onclick="viewLocationReport(this)" data-loc-filter="<?= htmlspecialchars($l['site'] . ' / ' . $l['location']) ?>">
                                View Report
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
