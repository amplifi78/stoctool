# Inventory Management System - App Plan

## 1. Project Overview
A simple, efficient web-based inventory management system for tracking stock levels across multiple locations, comparing them against minimum requirements, and managing supplier information.

**Stack:**
- **Backend:** PHP (Native)
- **Frontend:** HTML5, Bootstrap 5 (CSS)
- **Database:** SQLite 3

---

## 2. Database Schema (SQLite)

### `Suppliers`
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Unique identifier |
| `name` | TEXT | Supplier name |
| `website` | TEXT | Website URL |
| `phone` | TEXT | Primary contact number |
| `email` | TEXT | Contact email |
| `notes` | TEXT | General notes |

### `Items`
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Unique identifier |
| `photo` | TEXT | Filename/Path to item image |
| `name` | TEXT | Name of the item |
| `code` | TEXT | Internal barcode or SKU |
| `description` | TEXT | Detailed description |
| `supplier_id` | INTEGER (FK) | References `Suppliers.id` |

### `Locations`
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Unique identifier |
| `location` | TEXT | Specific spot (e.g., "Hall cupboard 1") |
| `building` | TEXT | Building name (e.g., "Office") |
| `site` | TEXT | General site (e.g., "Garden Suburb") |

### `ItemGroups`
*Defines which items are supposed to be at which location and their targets.*
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Unique identifier |
| `location_id` | INTEGER (FK) | References `Locations.id` |
| `item_id` | INTEGER (FK) | References `Items.id` |
| `min_required` | INTEGER | Minimum quantity required at this location |

### `StockChecks`
*Records the event of a stock take.*
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Unique identifier |
| `location_id` | INTEGER (FK) | References `Locations.id` |
| `check_timestamp` | DATETIME | Date and time of the check (default: current) |

### `StockRecords`
*Records the actual counts for each item during an event.*
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INTEGER (PK) | Unique identifier |
| `check_id` | INTEGER (FK) | References `StockChecks.id` |
| `item_id` | INTEGER (FK) | References `Items.id` |
| `item_count` | INTEGER | The number of items found |

---

## 3. Application Structure

```text
/public_html/stoc/
│
├── database/
│   └── schema.sql        # Database initialization script
│   └── inventory.db      # SQLite database file
├── includes/
│   ├── db.php            # PDO connection (Procedural)
│   └── functions.php     # Core helper functions
├── partials/             # Small UI components loaded via JS
│   ├── locations.php     # List of locations (Dashboard)
│   ├── check_form.php    # Form for entering stock levels
│   ├── inventory.php     # Filterable stock level report
│   └── supplier.php      # Supplier detail view
├── actions/              # Backend PHP logic (Called via Fetch)
│   ├── get_locations.php # Returns location data
│   ├── get_items.php     # Returns items for a location
│   └── save_check.php    # Processes and saves stock take
├── index.php             # Main shell (Loads Bootstrap & App JS)
└── js/
    └── app.js            # Main logic: Fetch partials & handle events
```

---

## 4. Key Features & Workflows

### Phase 1: Adding a Stock Check
1.  **Select Location:** User visits `index.php`, sees a list of locations, and clicks "Perform Check" next to one (or clicks a global "Add Check" and selects from a dropdown).
2.  **Enter Levels:** `add_check.php` loads all `Items` linked to that `location_id` via `ItemGroups`.
3.  **Form Input:** For each item, an input field allows entry of the current count.
4.  **Auto-timestamp:** PHP handles the current date/time upon submission.
5.  **Submission:** Data is saved to `StockChecks` and multiple rows in `StockRecords`.

### Phase 2: Inventory Monitoring & Reporting
1.  **Filterable View:** `view_inventory.php` displays a table of items.
2.  **Calculated Fields:** 
    - `Current Stock`: Fetched from the *latest* `StockRecord` for that item + location.
    - `Net Needed`: `min_required` - `Current Stock`.
3.  **Color Coding:** Bootstrap classes (`text-danger` for negative, `text-success` for sufficient) to highlight shortages.
4.  **Filters:** Dropdowns for Site, Building, and Location to narrow the report.

### Phase 3: Detailed Inquiry
1.  **Item Modal/Page:** Clicking an item name opens `item_detail.php`.
2.  **Supplier Info:** Join `Items` with `Suppliers` to show contact details (Email, Phone, Website) directly to the user for easy reordering.

---

## 5. UI Requirements (Bootstrap)
- **Responsive Layout:** Must work on mobile/tablets for users walking around cupboards.
- **Card-based Dashboard:** At-a-glance summaries.
- **Tables:** Styled with `table-hover` and `table-striped` for readability.
- **Modals:** Use for quick supplier info views without leaving the report page.

---

## 6. Coding Rules
- **Procedural PHP:** No classes allowed. Use procedural functions only.
- **Condensed Code:** Keep logic and markup as minimal and short as possible.
- **Single Page Feel:** Use JavaScript `fetch()` to call PHP action/partial files.
- **Dynamic Content:** Do not refresh the page; use JS to update specific `div` containers.
- **Modular Components:** Break the UI into small partial PHP files that are loaded dynamically into the main view via JS.
