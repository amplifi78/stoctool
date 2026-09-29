<?php
/**
 * Migration 001 — enforce referential integrity.
 *
 * schema.sql only runs on a fresh database, so an existing live database
 * needs this once to:
 *   1. remove orphaned rows that would fail foreign-key enforcement
 *   2. de-duplicate item_groups (same item assigned to a location twice)
 *   3. rebuild item_groups with a UNIQUE(location_id, item_id) constraint
 *
 * Safe to run more than once. Run from the command line:
 *   php database/migrations/001_enforce_integrity.php
 *
 * Take a copy of database/inventory.db first.
 */

$db_path = __DIR__ . '/../inventory.db';
if (!file_exists($db_path) || filesize($db_path) === 0) {
    exit("No database at $db_path — nothing to migrate.\n");
}

$db = new PDO("sqlite:$db_path");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Do NOT enable foreign_keys yet — we need to delete orphans first,
// and a table rebuild below relies on legacy_alter_table behaviour.

function report($label, $n) { echo str_pad($label, 48) . ($n ? "removed $n\n" : "none\n"); }

$db->beginTransaction();

// 1. Orphaned item_groups (dangling location or item)
$n = $db->exec("DELETE FROM item_groups WHERE location_id NOT IN (SELECT id FROM locations) OR item_id NOT IN (SELECT id FROM items)");
report('Orphaned item assignments', $n);

// 2. Orphaned stock checks / records.
// Checks first: dropping a check orphans its records, so records must be
// swept afterwards or they survive the first run and fail foreign_key_check.
$n = $db->exec("DELETE FROM stock_checks WHERE location_id NOT IN (SELECT id FROM locations)");
report('Orphaned stock checks', $n);
$n = $db->exec("DELETE FROM stock_records WHERE check_id NOT IN (SELECT id FROM stock_checks) OR item_id NOT IN (SELECT id FROM items)");
report('Orphaned stock records', $n);

// 3. Duplicate item_groups — keep the lowest id for each (location_id, item_id)
$n = $db->exec("DELETE FROM item_groups WHERE id NOT IN (SELECT MIN(id) FROM item_groups GROUP BY location_id, item_id)");
report('Duplicate item assignments', $n);

$db->commit();

// 4. Add the UNIQUE constraint if it isn't already there.
$has_unique = false;
foreach ($db->query("PRAGMA index_list('item_groups')")->fetchAll(PDO::FETCH_ASSOC) as $idx) {
    if (!empty($idx['unique'])) {
        $cols = $db->query("PRAGMA index_info('{$idx['name']}')")->fetchAll(PDO::FETCH_COLUMN, 2);
        if (in_array('location_id', $cols) && in_array('item_id', $cols)) { $has_unique = true; break; }
    }
}

if ($has_unique) {
    echo "UNIQUE(location_id, item_id) already present — skipping rebuild.\n";
} else {
    // SQLite can't ALTER TABLE ADD CONSTRAINT — rebuild the table.
    $db->exec('PRAGMA foreign_keys = OFF');
    $db->beginTransaction();
    $db->exec("CREATE TABLE item_groups_new (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        location_id INTEGER,
        item_id INTEGER,
        min_required INTEGER DEFAULT 0,
        FOREIGN KEY (location_id) REFERENCES locations(id),
        FOREIGN KEY (item_id) REFERENCES items(id),
        UNIQUE (location_id, item_id)
    )");
    $db->exec("INSERT INTO item_groups_new (id, location_id, item_id, min_required)
               SELECT id, location_id, item_id, min_required FROM item_groups");
    $db->exec("DROP TABLE item_groups");
    $db->exec("ALTER TABLE item_groups_new RENAME TO item_groups");
    $db->commit();
    echo "Rebuilt item_groups with UNIQUE(location_id, item_id).\n";
}

// Verify integrity under enforcement
$db->exec('PRAGMA foreign_keys = ON');
$violations = $db->query("PRAGMA foreign_key_check")->fetchAll();
echo count($violations) === 0
    ? "Foreign key check: clean.\n"
    : "WARNING: " . count($violations) . " foreign key violation(s) remain.\n";

echo "Migration 001 complete.\n";
