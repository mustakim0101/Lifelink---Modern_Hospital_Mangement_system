<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink IT Bed Allocation UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1200px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; }
        input, select, textarea { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; box-sizing: border-box; }
        textarea { min-height: 70px; }
        button { border: 0; border-radius: 8px; background: #1d4ed8; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #334155; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 100px; overflow: auto; }
        .hint { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">-> UI Home</a></div>

    <div class="grid">
        <div class="card">
            <h3>Auth Context</h3>
            <input id="tokenInput" placeholder="Admin or ITWorker token">
            <button onclick="useAdminToken()">Use ADMIN_TOKEN</button>
            <button class="alt" onclick="useUserToken()">Use USER_TOKEN</button>
            <p class="hint">Use admin token for assigning IT worker to departments.</p>
        </div>

        <div class="card">
            <h3>Assign Department to IT Worker (Admin)</h3>
            <input id="itUserId" type="number" placeholder="IT worker user id">
            <input id="itDepartmentId" type="number" placeholder="department id">
            <button onclick="assignDepartment()">Assign</button>
        </div>

        <div class="card">
            <h3>Create Admission</h3>
            <input id="patientUserId" type="number" placeholder="patient user id">
            <input id="admissionDepartmentId" type="number" placeholder="department id">
            <select id="careLevel">
                <option value="Ward">Ward</option>
                <option value="ICU">ICU</option>
                <option value="NICU">NICU</option>
                <option value="CCU">CCU</option>
            </select>
            <input id="diagnosis" placeholder="diagnosis">
            <textarea id="admissionNotes" placeholder="notes (optional)"></textarea>
            <button onclick="createAdmission()">Create Admission</button>
        </div>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>Allocation Actions</h3>
        <input id="filterDepartmentId" type="number" placeholder="department id for list">
        <select id="filterStatus">
            <option value="">All statuses</option>
            <option value="Admitted">Admitted</option>
            <option value="Discharged">Discharged</option>
            <option value="Transferred">Transferred</option>
            <option value="Cancelled">Cancelled</option>
        </select>
        <button class="alt" onclick="myDepartments()">GET My Departments</button>
        <button class="alt" onclick="listAdmissions()">GET Admissions</button>
        <button class="alt" onclick="availableBeds()">GET Available Beds</button>
        <hr>
        <input id="assignAdmissionId" type="number" placeholder="admission id">
        <input id="assignBedId" type="number" placeholder="bed id">
        <button onclick="assignBed()">Assign Bed</button>
        <hr>
        <input id="dischargeAdmissionId" type="number" placeholder="admission id to discharge">
        <input id="releaseReason" placeholder="release reason (optional, default Discharge)">
        <button onclick="dischargeAdmission()">Discharge + Auto Release Bed</button>
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

function useAdminToken() {
    document.getElementById('tokenInput').value = localStorage.getItem('ADMIN_TOKEN') || '';
}

function useUserToken() {
    document.getElementById('tokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

function refreshCtx() {
    ctx.textContent = JSON.stringify({
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        USER_TOKEN_PRESENT: !!localStorage.getItem('USER_TOKEN'),
        LAST_ADMISSION_ID: localStorage.getItem('LAST_ADMISSION_ID'),
        LAST_ASSIGNED_BED_ID: localStorage.getItem('LAST_ASSIGNED_BED_ID')
    }, null, 2);
}

async function call(path, method = 'GET', body = null) {
    const token = document.getElementById('tokenInput').value.trim();
    if (!token) return { status: 401, data: { message: 'Token missing in Auth Context.' } };

    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

async function assignDepartment() {
    const body = {
        userId: Number(document.getElementById('itUserId').value),
        departmentId: Number(document.getElementById('itDepartmentId').value)
    };
    write(await call('/ward/it/department-admins', 'POST', body));
}

async function createAdmission() {
    const body = {
        patientUserId: Number(document.getElementById('patientUserId').value),
        departmentId: Number(document.getElementById('admissionDepartmentId').value),
        diagnosis: document.getElementById('diagnosis').value.trim(),
        careLevelRequested: document.getElementById('careLevel').value,
        notes: document.getElementById('admissionNotes').value.trim() || null
    };
    const r = await call('/ward/it/admissions', 'POST', body);
    const id = r.data?.admission?.id;
    if (id) {
        localStorage.setItem('LAST_ADMISSION_ID', String(id));
        document.getElementById('assignAdmissionId').value = String(id);
    }
    refreshCtx();
    write(r);
}

async function myDepartments() {
    write(await call('/ward/it/departments'));
}

async function listAdmissions() {
    const departmentId = document.getElementById('filterDepartmentId').value.trim();
    const status = document.getElementById('filterStatus').value.trim();
    const qs = new URLSearchParams();
    if (departmentId) qs.set('departmentId', departmentId);
    if (status) qs.set('status', status);
    write(await call(`/ward/it/admissions${qs.toString() ? `?${qs.toString()}` : ''}`));
}

async function availableBeds() {
    const departmentId = document.getElementById('filterDepartmentId').value.trim();
    const unitType = document.getElementById('careLevel').value;
    if (!departmentId) {
        write({ status: 422, data: { message: 'Set department id in Allocation Actions.' } });
        return;
    }
    write(await call(`/ward/it/available-beds?departmentId=${encodeURIComponent(departmentId)}&unitType=${encodeURIComponent(unitType)}`));
}

async function assignBed() {
    const body = {
        admissionId: Number(document.getElementById('assignAdmissionId').value),
        bedId: Number(document.getElementById('assignBedId').value)
    };
    const r = await call('/ward/it/assign-bed', 'POST', body);
    const bedId = r.data?.admission?.active_bed_assignment?.bed_id;
    if (bedId) localStorage.setItem('LAST_ASSIGNED_BED_ID', String(bedId));
    const admissionId = r.data?.admission?.id;
    if (admissionId) document.getElementById('dischargeAdmissionId').value = String(admissionId);
    refreshCtx();
    write(r);
}

async function dischargeAdmission() {
    const admissionId = Number(document.getElementById('dischargeAdmissionId').value);
    const releaseReason = document.getElementById('releaseReason').value.trim();
    if (!admissionId) {
        write({ status: 422, data: { message: 'discharge admission id is required' } });
        return;
    }
    const body = releaseReason ? { releaseReason } : {};
    const r = await call(`/ward/it/admissions/${admissionId}/discharge`, 'POST', body);
    refreshCtx();
    write(r);
}

useAdminToken();
refreshCtx();
</script>
</body>
</html>
