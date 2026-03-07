<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Doctor Dashboard UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1200px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(330px, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; }
        input, select, textarea { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; box-sizing: border-box; }
        textarea { min-height: 80px; }
        button { border: 0; border-radius: 8px; background: #1d4ed8; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #334155; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 90px; overflow: auto; }
        .hint { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">-> UI Home</a></div>

    <div class="grid">
        <div class="card">
            <h3>Token Context</h3>
            <input id="adminTokenInput" placeholder="admin token for doctor profile setup">
            <button onclick="useAdminToken()">Use ADMIN_TOKEN</button>
            <input id="doctorTokenInput" placeholder="doctor token for dashboard actions" style="margin-top:8px;">
            <button class="alt" onclick="useUserToken()">Use USER_TOKEN</button>
            <p class="hint">Login as doctor from /ui/auth to populate USER_TOKEN.</p>
        </div>

        <div class="card">
            <h3>Admin: Upsert Doctor Profile</h3>
            <input id="doctorUserId" type="number" placeholder="doctor user id">
            <input id="doctorDepartmentId" type="number" placeholder="department id">
            <input id="doctorSpecialization" placeholder="specialization (optional)">
            <input id="doctorLicenseNumber" placeholder="license number (optional)">
            <button onclick="upsertDoctorProfile()">Upsert Doctor Profile</button>
        </div>

        <div class="card">
            <h3>Doctor: Bed Request</h3>
            <input id="patientUserId" type="number" placeholder="patient user id">
            <select id="careLevelRequested">
                <option value="Ward">Ward</option>
                <option value="ICU">ICU</option>
                <option value="NICU">NICU</option>
                <option value="CCU">CCU</option>
            </select>
            <input id="diagnosis" placeholder="diagnosis">
            <textarea id="requestNotes" placeholder="notes (optional)"></textarea>
            <button onclick="createBedRequest()">Create Bed Request</button>
        </div>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>Doctor Actions</h3>
        <button class="alt" onclick="doctorProfile()">GET /doctor/profile</button>
        <button class="alt" onclick="doctorPatients()">GET /doctor/patients</button>
        <select id="appointmentStatusFilter" style="max-width:300px;">
            <option value="">All appointment statuses</option>
            <option value="Booked">Booked</option>
            <option value="Cancelled">Cancelled</option>
            <option value="Completed">Completed</option>
            <option value="NoShow">NoShow</option>
        </select>
        <button class="alt" onclick="doctorAppointments()">GET /doctor/appointments</button>
        <hr>
        <input id="cancelAppointmentId" type="number" placeholder="appointment id">
        <input id="cancelReason" placeholder="cancel reason (optional)">
        <button onclick="cancelAppointment()">Cancel Appointment</button>
        <hr>
        <button class="alt" onclick="doctorBedRequests()">GET /doctor/bed-requests</button>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>API Response</h3>
        <pre id="out"></pre>
    </div>
</div>

<script>
const API = '/api';
const out = document.getElementById('out');

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function useAdminToken() {
    document.getElementById('adminTokenInput').value = localStorage.getItem('ADMIN_TOKEN') || '';
}

function useUserToken() {
    document.getElementById('doctorTokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

async function call(path, method = 'GET', body = null, tokenKind = 'doctor') {
    const token = tokenKind === 'admin'
        ? document.getElementById('adminTokenInput').value.trim()
        : document.getElementById('doctorTokenInput').value.trim();

    if (!token) return { status: 401, data: { message: `${tokenKind} token missing` } };

    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

async function upsertDoctorProfile() {
    const body = {
        userId: Number(document.getElementById('doctorUserId').value),
        departmentId: Number(document.getElementById('doctorDepartmentId').value),
        specialization: document.getElementById('doctorSpecialization').value.trim() || null,
        licenseNumber: document.getElementById('doctorLicenseNumber').value.trim() || null
    };
    write(await call('/admin/doctors/profile', 'POST', body, 'admin'));
}

async function doctorProfile() {
    write(await call('/doctor/profile', 'GET'));
}

async function doctorPatients() {
    write(await call('/doctor/patients', 'GET'));
}

async function doctorAppointments() {
    const status = document.getElementById('appointmentStatusFilter').value.trim();
    const qs = status ? `?status=${encodeURIComponent(status)}` : '';
    write(await call(`/doctor/appointments${qs}`, 'GET'));
}

async function cancelAppointment() {
    const id = Number(document.getElementById('cancelAppointmentId').value);
    const reason = document.getElementById('cancelReason').value.trim();
    if (!id) {
        write({ status: 422, data: { message: 'appointment id required' } });
        return;
    }
    const body = reason ? { cancelReason: reason } : {};
    write(await call(`/doctor/appointments/${id}/cancel`, 'POST', body));
}

async function createBedRequest() {
    const body = {
        patientUserId: Number(document.getElementById('patientUserId').value),
        diagnosis: document.getElementById('diagnosis').value.trim(),
        careLevelRequested: document.getElementById('careLevelRequested').value,
        notes: document.getElementById('requestNotes').value.trim() || null
    };
    write(await call('/doctor/bed-requests', 'POST', body));
}

async function doctorBedRequests() {
    write(await call('/doctor/bed-requests', 'GET'));
}

useAdminToken();
useUserToken();
</script>
</body>
</html>

