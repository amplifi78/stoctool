function loadPartial(name, params = {}) {
    const content = document.getElementById('main-content');
    content.innerHTML = '<div class="text-center mt-5"><div class="spinner-border"></div></div>';
    
    let url = `partials/${name}.php`;
    const query = new URLSearchParams(params).toString();
    if (query) url += `?${query}`;

    fetch(url)
        .then(res => res.text())
        .then(html => content.innerHTML = html)
        .catch(err => content.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`);
}

async function postAction(url, body, partial, params = {}) {
    const res = await fetch(`actions/${url}`, { method: 'POST', body: body });
    const d = await res.json();
    if (d.success) loadPartial(partial, params);
    else alert('Error: ' + d.message);
}

const saveCheck = (e) => {
    e.preventDefault();
    postAction('save_check.php', new FormData(e.target), 'locations');
};

const updateMin = async (groupId) => {
    const val = document.getElementById(`min-${groupId}`).value;
    const body = new URLSearchParams({ group_id: groupId, min: val });
    const res = await fetch('actions/update_min.php', { method: 'POST', body: body });
    if ((await res.json()).success) alert('Updated');
};

const addItemToLoc = (e, locId) => {
    e.preventDefault();
    postAction('assign_item.php', new FormData(e.target), 'location_items', { id: locId });
};

const saveItem = (e) => {
    e.preventDefault();
    postAction('save_item.php', new FormData(e.target), 'items_manage');
};

const saveSupplier = (e) => {
    e.preventDefault();
    postAction('save_supplier.php', new FormData(e.target), 'suppliers_manage');
};

const saveLocation = (e) => {
    e.preventDefault();
    postAction('save_location.php', new FormData(e.target), 'locations_manage');
};

const saveLocationFromModal = (e) => {
    e.preventDefault();
    postAction('save_location.php', new FormData(e.target), 'locations');
    const modal = bootstrap.Modal.getInstance(document.getElementById('locationModal'));
    if (modal) modal.hide();
};

const showLocationModal = (btn) => {
    const data = btn.dataset;
    document.getElementById('loc-id').value = data.id || '';
    document.getElementById('loc-name').value = data.location || '';
    document.getElementById('loc-building').value = data.building || '';
    document.getElementById('loc-site').value = data.site || '';
    document.getElementById('loc-modal-title').innerText = data.id ? 'Edit Location' : 'Add New Location';
    new bootstrap.Modal(document.getElementById('locationModal')).show();
};

const filterInventory = () => {
    const loc = document.getElementById('filter-location').value;
    const status = document.getElementById('filter-status').value;
    document.querySelectorAll('.inventory-row').forEach(tr => {
        const matchesLoc = !loc || tr.dataset.location === loc;
        const matchesStatus = !status || tr.dataset.status === status;
        tr.style.display = (matchesLoc && matchesStatus) ? '' : 'none';
    });
};

const filterLocations = () => {
    const site = document.getElementById('filter-site').value;
    const building = document.getElementById('filter-building').value;
    document.querySelectorAll('.location-card').forEach(card => {
        const matchesSite = !site || card.dataset.site === site;
        const matchesBuilding = !building || card.dataset.building === building;
        card.style.display = (matchesSite && matchesBuilding) ? '' : 'none';
    });
};

const resetLocationFilters = () => {
    document.getElementById('filter-site').value = '';
    document.getElementById('filter-building').value = '';
    filterLocations();
};

const sortInventory = async (col) => {
    const res = await fetch('actions/set_sort.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `col=${col}`
    });
    if ((await res.json()).success) loadPartial('inventory');
};

const sortLocations = async (col) => {
    const res = await fetch('actions/set_sort.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `col=${col}`
    });
    if ((await res.json()).success) loadPartial('locations');
};

const showSupplierModal = (btn) => {
    const data = btn.dataset;
    document.getElementById('sup-name').innerText = data.name;
    document.getElementById('sup-website').innerText = data.website;
    document.getElementById('sup-website').href = data.website.startsWith('http') ? data.website : 'https://' + data.website;
    document.getElementById('sup-phone').innerText = data.phone;
    document.getElementById('sup-email').innerText = data.email;
    document.getElementById('sup-email').href = 'mailto:' + data.email;
    document.getElementById('sup-notes').innerText = data.notes;
    new bootstrap.Modal(document.getElementById('supplierModal')).show();
};

let itemToDelete = null;
let deleteType = null;

const confirmDelete = (id, name, type) => {
    itemToDelete = id;
    deleteType = type;
    document.getElementById('delete-item-name').innerText = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
};

const doDelete = async () => {
    let url, partial, params = {};
    if (deleteType === 'item') {
        url = 'actions/delete_item.php';
        partial = 'items_manage';
    } else if (deleteType === 'location') {
        url = 'actions/delete_location.php';
        partial = 'locations_manage';
    } else if (deleteType === 'supplier') {
        url = 'actions/delete_supplier.php';
        partial = 'suppliers_manage';
    } else if (deleteType === 'item_group') {
        url = 'actions/delete_item_group.php';
        partial = 'location_items';
        params = { id: currentLocId };
    }
    
    const res = await fetch(url, { 
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
        body: `id=${itemToDelete}` 
    });
    if ((await res.json()).success) {
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        loadPartial(partial, params);
    }
};

let currentLocId = null;
const confirmDeleteGroup = (groupId, itemName, locName, locDetails, locId) => {
    itemToDelete = groupId;
    deleteType = 'item_group';
    currentLocId = locId;
    document.getElementById('delete-item-name').innerText = `${itemName} from ${locName} (${locDetails})`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
};

document.addEventListener('DOMContentLoaded', () => loadPartial('locations'));
