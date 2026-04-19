<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Ward Setup UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1180px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; }
        input, select { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; }
        button { border: 0; border-radius: 8px; background: #1d4ed8; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #334155; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 120px; overflow: auto; }
        .hint { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">? UI Home</a></div>

    <div class="grid">
        <div class="card">
            <h3>Auth Context</h3>
            <input id="tokenInput" placeholder="ADMIN / IT token">
            <button onclick="useStoredAdminToken()">Use ADMIN_TOKEN</button>
            <button class="alt" onclick="useStoredUserToken()">Use USER_TOKEN</button>
            <p class="hint">Create actions require Admin or ITWorker role.</p>
        </div>

        <div class="card">
            <h3>Create Care Unit</h3>
            <input id="departmentId" type="number" placeholder="department id (e.g. 1)">
            <select id="unitType">
                <option value="Ward">Ward</option>
                <option value="ICU">ICU</option>
                <option value="NICU">NICU</option>
                <option value="CCU">CCU</option>
            </select>
            <input id="unitName" placeholder="unit name (optional)">
            <input id="floor" type="number" placeholder="floor (optional)">
            <button onclick="createCareUnit()">Create Care Unit</button>
            <button class="alt" onclick="listCareUnits()">List Care Units</button>
        </div>

        <div class="card">
            <h3>Create Bed</h3>
            <input id="careUnitId" type="number" placeholder="care unit id">
            <input id="bedCode" placeholder="bed code (e.g. ICU-01)">
            <select id="bedStatus">
                <option value="Available">Available</option>
                <option value="Occupied">Occupied</option>
                <option value="Maintenance">Maintenance</option>
                <option value="Reserved">Reserved</option>
            </select>
            <button onclick="createBed()">Create Bed</button>
            <button class="alt" onclick="listBeds()">List Beds</button>
        </div>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>Read APIs</h3>
        <button class="alt" onclick="listDepartments()">GET /ward/departments</button>
        <button class="alt" onclick="listCareUnits()">GET /ward/care-units</button>
        <button class="alt" onclick="listBeds()">GET /ward/beds</button>
        <button class="alt" onclick="loadSummary()">GET /ward/beds/summary</button>
        <p class="hint">For filtered beds, fill department/care-unit/unit-type/status query directly in Postman.</p>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>Latest IDs</h3>
        <pre id="ctx"></pre>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>API Response</h3>
        <pre id="out"></pre>
    </div>
</div>

<script>
const API = '/api';
const out = document.getElementById('out');
const ctx = document.getElementById('ctx');

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function refreshCtx() {
    ctx.textContent = JSON.stringify({
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        USER_TOKEN_PRESENT: !!localStorage.getItem('USER_TOKEN'),
        LAST_CARE_UNIT_ID: localStorage.getItem('LAST_CARE_UNIT_ID'),
        LAST_BED_ID: localStorage.getItem('LAST_BED_ID')
    }, null, 2);
}

function useStoredAdminToken() {
    document.getElementById('tokenInput').value = localStorage.getItem('ADMIN_TOKEN') || '';
}

function useStoredUserToken() {
    document.getElementById('tokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

async function call(path, method = 'GET', body = null) {
    const token = document.getElementById('tokenInput').value.trim();
    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const res = await fetch(API + path, {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined
    });

    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

async function listDepartments() {
    write(await call('/ward/departments'));
}

async function listCareUnits() {
    write(await call('/ward/care-units'));
}

async function listBeds() {
    write(await call('/ward/beds'));
}

async function loadSummary() {
    write(await call('/ward/beds/summary'));
}

async function createCareUnit() {
    const payload = {
        departmentId: Number(document.getElementById('departmentId').value),
        unitType: document.getElementById('unitType').value,
        unitName: document.getElementById('unitName').value.trim() || null,
        floor: document.getElementById('floor').value ? Number(document.getElementById('floor').value) : null
    };
    const r = await call('/ward/care-units', 'POST', payload);
    const id = r.data?.care_unit?.id;
    if (id) {
        localStorage.setItem('LAST_CARE_UNIT_ID', String(id));
        document.getElementById('careUnitId').value = String(id);
    }
    refreshCtx();
    write(r);
}

async function createBed() {
    const payload = {
        careUnitId: Number(document.getElementById('careUnitId').value),
        bedCode: document.getElementById('bedCode').value.trim(),
        status: document.getElementById('bedStatus').value
    };
    const r = await call('/ward/beds', 'POST', payload);
    const id = r.data?.bed?.id;
    if (id) {
        localStorage.setItem('LAST_BED_ID', String(id));
    }
    refreshCtx();
    write(r);
}

useStoredAdminToken();
refreshCtx();
</script>
</body>
</html>

