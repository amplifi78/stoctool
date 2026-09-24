# Stoc

A simple stock tracker: record stock levels across locations, compare them with minimum
quantities, and keep supplier details. Plain PHP, Bootstrap 5 and SQLite.

## Setup
1. Copy the folder to a PHP 8 web server with the `pdo_sqlite` extension.
2. Create an empty `database/inventory.db` that the web server can write to. The schema in
   `database/schema.sql` is applied on first load.
3. Keep `database/` blocked from the web. Apache uses the included `.htaccess`.

See `app_plan.md` for the data model.
