CREATE TABLE IF NOT EXISTS suppliers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    website TEXT,
    phone TEXT,
    email TEXT,
    notes TEXT
);

CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    photo TEXT,
    name TEXT NOT NULL,
    code TEXT,
    description TEXT,
    supplier_id INTEGER,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS locations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    location TEXT NOT NULL,
    building TEXT,
    site TEXT
);

CREATE TABLE IF NOT EXISTS item_groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    location_id INTEGER,
    item_id INTEGER,
    min_required INTEGER DEFAULT 0,
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (item_id) REFERENCES items(id),
    UNIQUE (location_id, item_id)
);

CREATE TABLE IF NOT EXISTS stock_checks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    location_id INTEGER,
    check_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

CREATE TABLE IF NOT EXISTS stock_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    check_id INTEGER,
    item_id INTEGER,
    item_count INTEGER,
    FOREIGN KEY (check_id) REFERENCES stock_checks(id),
    FOREIGN KEY (item_id) REFERENCES items(id)
);
