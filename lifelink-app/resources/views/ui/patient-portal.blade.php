<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Patient Portal</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Sora:wght@500;600;700&display=swap');

        :root {
            --bg-a: #ecf8f5;
            --bg-b: #fff7eb;
            --ink: #122b42;
            --muted: #607189;
            --line: rgba(18, 43, 66, 0.14);
            --card: rgba(255, 255, 255, 0.88);
            --primary: #0f766e;
            --primary-strong: #0d615a;
            --accent: #ea580c;
            --ok: #166534;
            --warn: #a16207;
            --danger: #b91c1c;
            --shadow: 0 16px 36px rgba(18, 43, 66, 0.15);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "Manrope", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at 14% 12%, rgba(15, 118, 110, 0.21), transparent 42%),
                radial-gradient(circle at 85% 7%, rgba(234, 88, 12, 0.18), transparent 41%),
                linear-gradient(145deg, var(--bg-a), var(--bg-b));
        }

        h1, h2, h3 { margin: 0; font-family: "Sora", "Trebuchet MS", sans-serif; letter-spacing: -0.01em; }
        .wrap { max-width: 1320px; margin: 0 auto; padding: 18px 14px 30px; }
        .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .top a { color: var(--primary); text-decoration: none; font-weight: 700; font-size: 14px; }
        .phase { border-radius: 999px; background: rgba(15, 118, 110, 0.15); color: var(--primary-strong); font-size: 12px; font-weight: 800; padding: 7px 11px; }

        .hero {
            border: 1px solid rgba(255, 255, 255, 0.74);
            border-radius: 18px;
            background: linear-gradient(130deg, rgba(255, 255, 255, 0.93), rgba(255, 255, 255, 0.62));
            box-shadow: var(--shadow);
            padding: 15px 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .hero p { margin: 7px 0 0; color: var(--muted); font-size: 14px; max-width: 760px; }
        .clock { text-align: right; min-width: 160px; }
        .clock strong { font-size: 24px; display: block; }
        .clock small { color: var(--muted); font-size: 12px; }

        .layout { display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 12px; }
        .panel { border: 1px solid var(--line); border-radius: 16px; background: var(--card); box-shadow: var(--shadow); }
        .side { padding: 12px; height: fit-content; position: sticky; top: 14px; }
        .content { padding: 11px; display: grid; gap: 10px; }

        .card { border: 1px solid var(--line); border-radius: 13px; background: rgba(255, 255, 255, 0.9); padding: 11px; }
        .hint { margin: 4px 0 0; color: var(--muted); font-size: 12px; }
        .row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }

        label {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        input, select, textarea {
            width: 100%;
            border-radius: 10px;
            border: 1px solid rgba(18, 43, 66, 0.18);
            background: rgba(255, 255, 255, 0.94);
            color: var(--ink);
            font: inherit;
            padding: 9px 10px;
            outline: none;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14); }
        textarea { min-height: 78px; resize: vertical; }

        .btn-row { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 9px; }
        button {
            border: 0;
            border-radius: 10px;
            padding: 9px 11px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        button[disabled] { opacity: 0.6; pointer-events: none; }
        .btn-main { background: var(--primary); color: #fff; }
        .btn-main:hover { background: var(--primary-strong); }
        .btn-soft { background: rgba(18, 43, 66, 0.1); color: var(--ink); }
        .btn-accent { background: var(--accent); color: #fff; }

        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin-top: 8px; }
        .stat { border: 1px solid var(--line); border-radius: 10px; background: rgba(255, 255, 255, 0.9); text-align: center; padding: 9px; }
        .num { font-family: "Sora", "Trebuchet MS", sans-serif; font-size: 21px; font-weight: 700; }
        .lbl { margin-top: 2px; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }

        .summary { margin-top: 9px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .summary .item { border: 1px solid var(--line); border-radius: 10px; background: rgba(255, 255, 255, 0.92); padding: 8px; }
        .summary small { display: block; color: var(--muted); font-size: 11px; margin-bottom: 2px; }
        .summary strong { font-size: 13px; }

        .filters { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 8px; }
        .filters button { background: rgba(18, 43, 66, 0.09); color: var(--ink); padding: 7px 10px; font-size: 12px; border: 1px solid transparent; }
        .filters button.active { background: rgba(15, 118, 110, 0.16); border-color: rgba(15, 118, 110, 0.34); color: var(--primary-strong); }

        .table-wrap { overflow: auto; border: 1px solid var(--line); border-radius: 10px; background: rgba(255, 255, 255, 0.92); margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { text-align: left; white-space: nowrap; padding: 8px; border-bottom: 1px solid rgba(18, 43, 66, 0.09); }
        th { position: sticky; top: 0; background: rgba(246, 251, 255, 0.96); color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }

        .badge { display: inline-flex; border-radius: 999px; font-size: 11px; font-weight: 700; padding: 4px 8px; }
        .badge.success { color: var(--ok); background: rgba(22, 101, 52, 0.15); }
        .badge.pending { color: var(--warn); background: rgba(161, 98, 7, 0.16); }
        .badge.danger { color: var(--danger); background: rgba(185, 28, 28, 0.14); }

        pre { margin: 0; min-height: 110px; max-height: 260px; overflow: auto; border-radius: 11px; border: 1px solid var(--line); background: #111f37; color: #d7e3ff; padding: 10px; font-size: 12px; }
        .mini { margin-top: 4px; color: var(--muted); font-size: 12px; }
        .toast-stack { position: fixed; right: 12px; bottom: 12px; display: grid; gap: 8px; z-index: 30; }
        .toast { border-radius: 9px; padding: 9px 11px; color: #fff; font-size: 12px; box-shadow: 0 10px 22px rgba(18, 43, 66, 0.3); }
        .toast.ok { background: #166534; }
        .toast.error { background: #b91c1c; }

        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
            .side { position: static; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 860px) {
            .split, .row, .summary { grid-template-columns: 1fr; }
            .hero { flex-direction: column; align-items: flex-start; }
            .clock { text-align: left; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a href="/ui"><- Back to UI Home</a>
        <div class="phase">Phase 5 Issue 15: Patient Portal</div>
    </div>

    <section class="hero">
        <div>
            <h1>Patient Portal</h1>
            <p>View medical records, manage appointments, and request blood from one simple dashboard.</p>
        </div>
        <div class="clock">
            <strong id="clockNow">--:--</strong>
            <small>Local dashboard time</small>
        </div>
    </section>

    <section class="layout">
        <aside class="panel side">
            <div class="card">
                <h3>Auth Context</h3>
                <p class="hint">Use patient token from <code>/ui/auth</code>.</p>
                <label for="tokenInput">Patient token</label>
                <input id="tokenInput" placeholder="Bearer token">
                <div class="btn-row">
                    <button id="btnStored" class="btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    <button id="btnRefresh" class="btn-main" onclick="refreshAll()">Refresh All</button>
                </div>
            </div>

            <div class="card">
                <h3>Appointment Filter</h3>
                <div id="appointmentFilters" class="filters">
                    <button data-status="" class="active" onclick="setAppointmentStatus('')">All</button>
                    <button data-status="Booked" onclick="setAppointmentStatus('Booked')">Booked</button>
                    <button data-status="Cancelled" onclick="setAppointmentStatus('Cancelled')">Cancelled</button>
                    <button data-status="Completed" onclick="setAppointmentStatus('Completed')">Completed</button>
                    <button data-status="NoShow" onclick="setAppointmentStatus('NoShow')">No Show</button>
                </div>
            </div>

            <div class="card">
                <h3>Blood Request Filter</h3>
                <div id="bloodFilters" class="filters">
                    <button data-status="" class="active" onclick="setBloodStatus('')">All</button>
                    <button data-status="Pending" onclick="setBloodStatus('Pending')">Pending</button>
                    <button data-status="Fulfilled" onclick="setBloodStatus('Fulfilled')">Fulfilled</button>
                    <button data-status="Rejected" onclick="setBloodStatus('Rejected')">Rejected</button>
                    <button data-status="Cancelled" onclick="setBloodStatus('Cancelled')">Cancelled</button>
                </div>
            </div>
        </aside>

        <main class="panel content">
            <div class="card">
                <h3>Snapshot</h3>
                <p class="hint">Live summary from <code>GET /api/patient/portal</code>.</p>
                <div class="stats">
                    <div class="stat"><div class="num" id="stRecords">0</div><div class="lbl">Records</div></div>
                    <div class="stat"><div class="num" id="stUpcoming">0</div><div class="lbl">Upcoming</div></div>
                    <div class="stat"><div class="num" id="stRequests">0</div><div class="lbl">Blood Requests</div></div>
                    <div class="stat"><div class="num" id="stRoleCount">0</div><div class="lbl">Roles</div></div>
                </div>
                <div class="summary" id="patientSummary"></div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Book Appointment</h3>
                    <p class="hint">Department and time are required. Doctor is optional.</p>
                    <div class="row">
                        <div>
                            <label for="appointmentDepartmentId">Department</label>
                            <select id="appointmentDepartmentId"></select>
                        </div>
                        <div>
                            <label for="appointmentDoctorUserId">Doctor</label>
                            <select id="appointmentDoctorUserId"></select>
                            <div class="mini" id="doctorMeta">Doctors load from active profiles.</div>
                        </div>
                    </div>
                    <label for="appointmentDateTime">Appointment datetime</label>
                    <input id="appointmentDateTime" type="datetime-local">
                    <div class="btn-row">
                        <button id="btnBook" class="btn-main" onclick="bookAppointment()">Book Appointment</button>
                        <button class="btn-soft" onclick="loadAppointments()">Refresh Appointments</button>
                    </div>
                </div>

                <div class="card">
                    <h3>Request Blood</h3>
                    <p class="hint">Submit request directly from patient account.</p>
                    <div class="row">
                        <div>
                            <label for="bloodGroupNeeded">Blood group</label>
                            <select id="bloodGroupNeeded">
                                <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                                <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                            </select>
                        </div>
                        <div>
                            <label for="bloodUnits">Units required</label>
                            <input id="bloodUnits" type="number" min="1" value="1">
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <label for="bloodComponentType">Component</label>
                            <select id="bloodComponentType">
                                <option selected>WholeBlood</option>
                                <option>Plasma</option>
                                <option>Platelets</option>
                                <option>RBC</option>
                            </select>
                        </div>
                        <div>
                            <label for="bloodUrgency">Urgency</label>
                            <select id="bloodUrgency">
                                <option>Normal</option>
                                <option selected>Urgent</option>
                                <option>Emergency</option>
                            </select>
                        </div>
                    </div>
                    <label for="bloodDepartmentId">Department (optional)</label>
                    <select id="bloodDepartmentId"></select>
                    <label for="bloodNotes">Note</label>
                    <textarea id="bloodNotes" placeholder="Optional note for blood bank team"></textarea>
                    <div class="btn-row">
                        <button id="btnBlood" class="btn-accent" onclick="submitBloodRequest()">Submit Blood Request</button>
                        <button class="btn-soft" onclick="loadBloodRequests()">Refresh Blood Requests</button>
                    </div>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Appointments</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Department</th><th>Doctor</th><th>Datetime</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody id="appointmentsBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h3>Blood Requests</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Group</th><th>Component</th><th>Units</th><th>Urgency</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody id="bloodRequestsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="row">
                    <div>
                        <h3>Medical Records</h3>
                        <p class="hint">Search by diagnosis, treatment plan, or clinician.</p>
                    </div>
                    <div>
                        <label for="recordSearch">Search</label>
                        <input id="recordSearch" placeholder="Type to filter records">
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr><th>ID</th><th>Datetime</th><th>Diagnosis</th><th>Treatment</th><th>Created By</th></tr>
                        </thead>
                        <tbody id="recordsBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3>API Response</h3>
                <pre id="out"></pre>
            </div>
        </main>
    </section>
</div>

<div id="toastStack" class="toast-stack"></div>

<script>
const API = '/api';
const out = document.getElementById('out');
const state = {
    appointmentStatus: '',
    bloodStatus: '',
    departments: [],
    doctors: [],
    records: [],
    recordSearch: ''
};

function byId(id) { return document.getElementById(id); }
function write(data) { out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2); }
function html(value) {
    if (value === null || value === undefined) return '';
    return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
}

function showToast(message, type = 'ok') {
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'error' ? 'error' : 'ok'}`;
    toast.textContent = message;
    byId('toastStack').appendChild(toast);
    setTimeout(() => toast.remove(), 2600);
}

function setClock() {
    byId('clockNow').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function setButtonBusy(id, busy) {
    const b = byId(id);
    if (!b) return;
    b.disabled = busy;
    b.dataset.label = b.dataset.label || b.textContent;
    b.textContent = busy ? 'Working...' : b.dataset.label;
}

function useStoredUserToken() { byId('tokenInput').value = localStorage.getItem('USER_TOKEN') || ''; }

async function call(path, method = 'GET', body = null, query = null) {
    const token = byId('tokenInput').value.trim();
    if (!token) return { status: 401, data: { message: 'USER_TOKEN is missing. Login first from /ui/auth.' } };

    const q = query ? new URLSearchParams(query).toString() : '';
    const endpoint = `${API}${path}${q ? `?${q}` : ''}`;
    const res = await fetch(endpoint, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: body ? JSON.stringify(body) : undefined
    });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

function statusBadge(status) {
    const value = status || '-';
    let type = 'pending';
    if (['Fulfilled', 'Completed', 'Approved', 'Matched'].includes(value)) type = 'success';
    if (['Cancelled', 'Rejected', 'NoShow'].includes(value)) type = 'danger';
    return `<span class="badge ${type}">${html(value)}</span>`;
}

function setFilterActive(containerId, status) {
    byId(containerId).querySelectorAll('button[data-status]').forEach((b) => {
        b.classList.toggle('active', (b.getAttribute('data-status') || '') === status);
    });
}

function populateDoctorOptions() {
    const departmentId = Number(byId('appointmentDepartmentId').value || 0);
    const doctors = state.doctors.filter((d) => !departmentId || Number(d.department_id) === departmentId);
    const options = ['<option value="">Unassigned (hospital can assign later)</option>']
        .concat(doctors.map((d) => `<option value="${d.user_id}">${html(d.full_name || 'Doctor')} (${html(d.specialization || 'General')})</option>`))
        .join('');
    const select = byId('appointmentDoctorUserId');
    const prev = select.value;
    select.innerHTML = options;
    if (prev && doctors.some((d) => String(d.user_id) === prev)) select.value = prev;
    byId('doctorMeta').textContent = doctors.length
        ? `${doctors.length} active doctor(s) in selected department.`
        : 'No active doctors in this department. Appointment can stay unassigned.';
}

function populateDepartmentOptions() {
    const ap = byId('appointmentDepartmentId');
    const bl = byId('bloodDepartmentId');
    const currentAp = ap.value;
    const currentBl = bl.value;
    if (!state.departments.length) {
        ap.innerHTML = '<option value="">No departments</option>';
        bl.innerHTML = '<option value="">Auto</option>';
        byId('appointmentDoctorUserId').innerHTML = '<option value="">Unassigned</option>';
        return;
    }
    const options = state.departments.map((d) => `<option value="${d.id}">${html(d.dept_name)} (#${d.id})</option>`).join('');
    ap.innerHTML = options;
    bl.innerHTML = `<option value="">Auto</option>${options}`;
    ap.value = state.departments.some((d) => String(d.id) === currentAp) ? currentAp : String(state.departments[0].id);
    bl.value = state.departments.some((d) => String(d.id) === currentBl) ? currentBl : '';
    populateDoctorOptions();
}

function setDefaultAppointmentTime() {
    const next = new Date(Date.now() + (24 * 60 * 60 * 1000));
    next.setHours(10, 0, 0, 0);
    const min = new Date(Date.now() + (5 * 60 * 1000));
    byId('appointmentDateTime').min = min.toISOString().slice(0, 16);
    byId('appointmentDateTime').value = next.toISOString().slice(0, 16);
}

async function loadBookingOptions() {
    const r = await call('/patient/booking-options', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load booking options', 'error'); return; }
    state.departments = r.data?.departments || [];
    state.doctors = r.data?.doctors || [];
    populateDepartmentOptions();
}

async function loadPortal() {
    const r = await call('/patient/portal', 'GET');
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not load portal snapshot', 'error'); return; }
    const patient = r.data?.patient || {};
    const stats = r.data?.stats || {};
    byId('stRecords').textContent = stats.medical_records || 0;
    byId('stUpcoming').textContent = stats.upcoming_appointments || 0;
    byId('stRequests').textContent = stats.blood_requests || 0;
    byId('stRoleCount').textContent = (patient.roles || []).length;
    byId('patientSummary').innerHTML = `
        <div class="item"><small>Patient</small><strong>${html(patient.full_name || '-')}</strong></div>
        <div class="item"><small>Email</small><strong>${html(patient.email || '-')}</strong></div>
        <div class="item"><small>Blood Group</small><strong>${html(patient.blood_group || 'Not set')}</strong></div>
        <div class="item"><small>Emergency Contact</small><strong>${html(patient.emergency_contact_name || '-')}</strong></div>
        <div class="item"><small>Emergency Phone</small><strong>${html(patient.emergency_contact_phone || '-')}</strong></div>
        <div class="item"><small>Roles</small><strong>${html((patient.roles || []).join(', ') || '-')}</strong></div>
    `;
}

function renderMedicalRecords() {
    const search = state.recordSearch.trim().toLowerCase();
    const rows = search
        ? state.records.filter((row) => `${row.diagnosis || ''} ${row.treatment_plan || ''} ${row.created_by || ''}`.toLowerCase().includes(search))
        : state.records;
    byId('recordsBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${row.record_datetime ? new Date(row.record_datetime).toLocaleString() : '-'}</td>
                <td>${html(row.diagnosis || '-')}</td>
                <td>${html(row.treatment_plan || '-')}</td>
                <td>${html(row.created_by || '-')}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No medical records found.</td></tr>';
}

async function loadMedicalRecords() {
    const r = await call('/patient/medical-records', 'GET', null, { limit: 40 });
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load medical records', 'error'); return; }
    state.records = r.data?.medical_records || [];
    renderMedicalRecords();
}

async function loadAppointments() {
    const query = {};
    if (state.appointmentStatus) query.status = state.appointmentStatus;
    const r = await call('/patient/appointments', 'GET', null, query);
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load appointments', 'error'); return; }
    const rows = r.data?.appointments || [];
    byId('appointmentsBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.department || '-')}</td>
                <td>${html(row.doctor_name || 'Unassigned')}</td>
                <td>${row.appointment_datetime ? new Date(row.appointment_datetime).toLocaleString() : '-'}</td>
                <td>${statusBadge(row.status)}</td>
                <td>${row.status === 'Booked' ? `<button class="btn-soft" onclick="cancelAppointment(${row.id})">Cancel</button>` : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="6">No appointments found.</td></tr>';
}

async function loadBloodRequests() {
    const query = {};
    if (state.bloodStatus) query.status = state.bloodStatus;
    const r = await call('/patient/blood-requests', 'GET', null, query);
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load blood requests', 'error'); return; }
    const rows = r.data?.blood_requests || [];
    byId('bloodRequestsBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.blood_group_needed || '-')}</td>
                <td>${html(row.component_type || '-')}</td>
                <td>${row.units_required ?? '-'}</td>
                <td>${html(row.urgency || '-')}</td>
                <td>${statusBadge(row.status)}</td>
                <td>${row.request_date ? new Date(row.request_date).toLocaleString() : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="7">No blood requests found.</td></tr>';
}

async function bookAppointment() {
    const payload = {
        departmentId: Number(byId('appointmentDepartmentId').value || 0),
        doctorUserId: byId('appointmentDoctorUserId').value ? Number(byId('appointmentDoctorUserId').value) : null,
        appointmentDateTime: byId('appointmentDateTime').value
    };
    setButtonBusy('btnBook', true);
    const r = await call('/patient/appointments', 'POST', payload);
    setButtonBusy('btnBook', false);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Appointment booking failed', 'error'); return; }
    showToast('Appointment booked');
    await loadAppointments();
    await loadPortal();
}

async function cancelAppointment(id) {
    const reason = prompt('Cancel reason (optional):', 'Cancelled by patient');
    const payload = reason ? { cancelReason: reason } : {};
    const r = await call(`/patient/appointments/${id}/cancel`, 'POST', payload);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not cancel appointment', 'error'); return; }
    showToast('Appointment cancelled');
    await loadAppointments();
    await loadPortal();
}

async function submitBloodRequest() {
    const departmentId = byId('bloodDepartmentId').value.trim();
    const payload = {
        bloodGroup: byId('bloodGroupNeeded').value,
        unitsRequested: Number(byId('bloodUnits').value || 0),
        componentType: byId('bloodComponentType').value,
        urgency: byId('bloodUrgency').value,
        departmentId: departmentId ? Number(departmentId) : null,
        notes: byId('bloodNotes').value.trim() || null
    };
    setButtonBusy('btnBlood', true);
    const r = await call('/patient/blood-requests', 'POST', payload);
    setButtonBusy('btnBlood', false);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Blood request failed', 'error'); return; }
    showToast('Blood request submitted');
    byId('bloodNotes').value = '';
    await loadBloodRequests();
    await loadPortal();
}

function setAppointmentStatus(status) { state.appointmentStatus = status; setFilterActive('appointmentFilters', status); loadAppointments(); }
function setBloodStatus(status) { state.bloodStatus = status; setFilterActive('bloodFilters', status); loadBloodRequests(); }

async function refreshAll() {
    setButtonBusy('btnRefresh', true);
    try {
        await loadBookingOptions();
        await loadPortal();
        await loadMedicalRecords();
        await loadAppointments();
        await loadBloodRequests();
        showToast('Dashboard refreshed');
    } finally {
        setButtonBusy('btnRefresh', false);
    }
}

function boot() {
    setClock();
    setInterval(setClock, 1000);
    useStoredUserToken();
    setDefaultAppointmentTime();
    byId('appointmentDepartmentId').addEventListener('change', populateDoctorOptions);
    byId('recordSearch').addEventListener('input', (event) => { state.recordSearch = event.target.value; renderMedicalRecords(); });
    refreshAll();
}

boot();
</script>
</body>
</html>
