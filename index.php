<?php 
include 'includes/db.php'; 

// show me the date time
// echo date('Y-m-d H:i:s');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HBC Stoc Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> .pointer { cursor: pointer; } </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand pointer" onclick="loadPartial('locations')">HBC Stoc Tracker</a>
            <div class="dropdown">
                <button class="navbar-toggler" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item pointer" onclick="loadPartial('locations')">Home</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item pointer" onclick="loadPartial('locations_manage')">Locations</a></li>
                    <li><a class="dropdown-item pointer" onclick="loadPartial('items_manage')">Items</a></li>
                    <li><a class="dropdown-item pointer" onclick="loadPartial('suppliers_manage')">Suppliers</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item pointer" onclick="loadPartial('inventory')">Report</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="main-content" class="container">
        <div class="text-center mt-5"><div class="spinner-border"></div></div>
    </div>

    <!-- Supplier Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sup-name">Supplier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Website:</strong> <a id="sup-website" href="#" target="_blank"></a></p>
                    <p><strong>Phone:</strong> <span id="sup-phone"></span></p>
                    <p><strong>Email:</strong> <a id="sup-email" href=""></a></p>
                    <p><strong>Notes:</strong><br><span id="sup-notes" style="white-space: pre-wrap;"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loc-modal-title">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="modal-location-form" onsubmit="saveLocationFromModal(event)" class="row g-3">
                        <input type="hidden" name="id" id="loc-id">
                        <div class="col-12">
                            <label class="form-label">Location Name</label>
                            <input type="text" name="location" id="loc-name" class="form-control" placeholder="Location Name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Building</label>
                            <input type="text" name="building" id="loc-building" class="form-control" placeholder="Building">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Site</label>
                            <input type="text" name="site" id="loc-site" class="form-control" placeholder="Site">
                        </div>
                        <div class="col-12 mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Confirm Delete</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">Are you sure you want to delete <strong id="delete-item-name"></strong>?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="doDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
