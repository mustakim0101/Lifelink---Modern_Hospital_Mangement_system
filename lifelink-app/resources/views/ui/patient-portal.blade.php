@extends('ui.layouts.app')

@section('title', 'Patient Portal')
@section('workspace_label', 'Patient self-service workspace')
@section('hero_badge', 'Patient Mode')
@section('hero_title', 'Patient portal for records, appointments, and blood requests.')
@section('hero_description', 'This is the first role page moved into the shared authenticated shell. Patients can stay inside one connected workspace instead of bouncing between isolated prototype screens.')
@section('meta_title', 'Patient Portal')
@section('meta_copy', 'Appointments, records, and blood support')

@push('styles')
<style>
    :root {
        --portal-ink: #122b42;
        --portal-muted: #607189;
        --portal-line: rgba(18, 43, 66, 0.14);
        --portal-card: rgba(255, 255, 255, 0.9);
        --portal-primary: #0f766e;
        --portal-primary-strong: #0d615a;
        --portal-accent: #ea580c;
        --portal-ok: #166534;
        --portal-warn: #a16207;
        --portal-danger: #b91c1c;
        --portal-shadow: 0 16px 36px rgba(18, 43, 66, 0.15);
    }

    .portal-grid { display: grid; gap: 10px; }
    .portal-card {
        border: 1px solid var(--portal-line);
        border-radius: 16px;
        background: var(--portal-card);
        box-shadow: var(--portal-shadow);
        padding: 12px;
    }

    .portal-card h3 { margin: 0; }
    .portal-hint { margin: 4px 0 0; color: var(--portal-muted); font-size: 12px; }
    .portal-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    .portal-split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }

    .portal-label {
        display: block;
        margin-bottom: 4px;
        color: var(--portal-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .portal-input,
    .portal-select,
    .portal-textarea {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(18, 43, 66, 0.18);
        background: rgba(255, 255, 255, 0.96);
        color: var(--portal-ink);
        font: inherit;
        padding: 9px 10px;
        outline: none;
    }

    .portal-input:focus,
    .portal-select:focus,
    .portal-textarea:focus {
        border-color: var(--portal-primary);
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14);
    }

    .portal-textarea { min-height: 78px; resize: vertical; }

    .portal-btn-row { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 9px; }
    .portal-btn {
        border: 0;
        border-radius: 10px;
        padding: 9px 11px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .portal-btn[disabled] { opacity: 0.6; pointer-events: none; }
    .portal-btn-main { background: var(--portal-primary); color: #fff; }
    .portal-btn-main:hover { background: var(--portal-primary-strong); }
    .portal-btn-soft { background: rgba(18, 43, 66, 0.1); color: var(--portal-ink); }
    .portal-btn-accent { background: var(--portal-accent); color: #fff; }

    .portal-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin-top: 8px; }
    .portal-stat { border: 1px solid var(--portal-line); border-radius: 10px; background: rgba(255, 255, 255, 0.92); text-align: center; padding: 9px; }
    .portal-num { font-family: "Sora", "Trebuchet MS", sans-serif; font-size: 21px; font-weight: 700; }
    .portal-lbl { margin-top: 2px; color: var(--portal-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }

    .portal-summary { margin-top: 9px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
    .portal-summary .item { border: 1px solid var(--portal-line); border-radius: 10px; background: rgba(255, 255, 255, 0.92); padding: 8px; }
    .portal-summary small { display: block; color: var(--portal-muted); font-size: 11px; margin-bottom: 2px; }
    .portal-summary strong { font-size: 13px; }

    .portal-filters { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 8px; }
    .portal-filters button { background: rgba(18, 43, 66, 0.09); color: var(--portal-ink); padding: 7px 10px; font-size: 12px; border: 1px solid transparent; }
    .portal-filters button.active { background: rgba(15, 118, 110, 0.16); border-color: rgba(15, 118, 110, 0.34); color: var(--portal-primary-strong); }

    .portal-table-wrap { overflow: auto; border: 1px solid var(--portal-line); border-radius: 10px; background: rgba(255, 255, 255, 0.94); margin-top: 8px; }
    .portal-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .portal-table th, .portal-table td { text-align: left; white-space: nowrap; padding: 8px; border-bottom: 1px solid rgba(18, 43, 66, 0.09); }
    .portal-table th { position: sticky; top: 0; background: rgba(246, 251, 255, 0.98); color: var(--portal-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }

    .portal-badge { display: inline-flex; border-radius: 999px; font-size: 11px; font-weight: 700; padding: 4px 8px; }
    .portal-badge.success { color: var(--portal-ok); background: rgba(22, 101, 52, 0.15); }
    .portal-badge.pending { color: var(--portal-warn); background: rgba(161, 98, 7, 0.16); }
    .portal-badge.danger { color: var(--portal-danger); background: rgba(185, 28, 28, 0.14); }

    .portal-pre { margin: 0; min-height: 110px; max-height: 260px; overflow: auto; border-radius: 11px; border: 1px solid var(--portal-line); background: #111f37; color: #d7e3ff; padding: 10px; font-size: 12px; }
    .portal-mini { margin-top: 4px; color: var(--portal-muted); font-size: 12px; }
    .portal-toast-stack { position: fixed; right: 12px; bottom: 12px; display: grid; gap: 8px; z-index: 30; }
    .portal-toast { border-radius: 9px; padding: 9px 11px; color: #fff; font-size: 12px; box-shadow: 0 10px 22px rgba(18, 43, 66, 0.3); }
    .portal-toast.ok { background: #166534; }
    .portal-toast.error { background: #b91c1c; }

    .portal-clock { font-size: 1.7rem; }

    @media (max-width: 1200px) {
        .portal-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 860px) {
        .portal-split, .portal-row, .portal-summary { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="/ui/patient-portal">
        <strong>Patient Portal</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/dashboard">
        <strong>Workspace Hub</strong>
        <span>Role redirect center</span>
    </a>
    <a href="/ui/applications">
        <strong>Applications</strong>
        <span>Track applicant-side flow</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Auth context</strong>
        <p>Use the stored patient session token from <code>/ui/login</code> and refresh the portal data from here.</p>
        <label class="portal-label" for="tokenInput">Patient token</label>
        <input id="tokenInput" class="portal-input" placeholder="Bearer token">
        <div class="portal-btn-row">
            <button id="btnStored" class="portal-btn portal-btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
            <button id="btnRefresh" class="portal-btn portal-btn-main" onclick="refreshAll()">Refresh All</button>
        </div>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Appointment filter</strong>
        <div id="appointmentFilters" class="portal-filters">
            <button data-status="" class="active" onclick="setAppointmentStatus('')">All</button>
            <button data-status="Booked" onclick="setAppointmentStatus('Booked')">Booked</button>
            <button data-status="Cancelled" onclick="setAppointmentStatus('Cancelled')">Cancelled</button>
            <button data-status="Completed" onclick="setAppointmentStatus('Completed')">Completed</button>
            <button data-status="NoShow" onclick="setAppointmentStatus('NoShow')">No Show</button>
        </div>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Blood request filter</strong>
        <div id="bloodFilters" class="portal-filters">
            <button data-status="" class="active" onclick="setBloodStatus('')">All</button>
            <button data-status="Pending" onclick="setBloodStatus('Pending')">Pending</button>
            <button data-status="Fulfilled" onclick="setBloodStatus('Fulfilled')">Fulfilled</button>
            <button data-status="Rejected" onclick="setBloodStatus('Rejected')">Rejected</button>
            <button data-status="Cancelled" onclick="setBloodStatus('Cancelled')">Cancelled</button>
        </div>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Session clock</strong>
        <p>The portal stays in the shared workspace shell now, but the patient tools still refresh live from the same API endpoints.</p>
        <div class="portal-mini">Local dashboard time</div>
        <strong id="clockNow" class="portal-clock">--:--</strong>
    </div>
@endsection

@section('content')
    <div class="portal-grid">
        <div class="portal-card">
            <h3>Snapshot</h3>
            <p class="portal-hint">Live summary from <code>GET /api/patient/portal</code>.</p>
            <div class="portal-stats">
                <div class="portal-stat"><div class="portal-num" id="stRecords">0</div><div class="portal-lbl">Records</div></div>
                <div class="portal-stat"><div class="portal-num" id="stUpcoming">0</div><div class="portal-lbl">Upcoming</div></div>
                <div class="portal-stat"><div class="portal-num" id="stRequests">0</div><div class="portal-lbl">Blood Requests</div></div>
                <div class="portal-stat"><div class="portal-num" id="stRoleCount">0</div><div class="portal-lbl">Roles</div></div>
            </div>
            <div class="portal-summary" id="patientSummary"></div>
        </div>

        <div class="portal-split">
            <div class="portal-card">
                <h3>Book appointment</h3>
                <p class="portal-hint">Department and time are required. Doctor is optional.</p>
                <div class="portal-row">
                    <div>
                        <label class="portal-label" for="appointmentDepartmentId">Department</label>
                        <select id="appointmentDepartmentId" class="portal-select"></select>
                    </div>
                    <div>
                        <label class="portal-label" for="appointmentDoctorUserId">Doctor</label>
                        <select id="appointmentDoctorUserId" class="portal-select"></select>
                        <div class="portal-mini" id="doctorMeta">Doctors load from active profiles.</div>
                    </div>
                </div>
                <label class="portal-label" for="appointmentDateTime">Appointment datetime</label>
                <input id="appointmentDateTime" class="portal-input" type="datetime-local">
                <div class="portal-btn-row">
                    <button id="btnBook" class="portal-btn portal-btn-main" onclick="bookAppointment()">Book Appointment</button>
                    <button class="portal-btn portal-btn-soft" onclick="loadAppointments()">Refresh Appointments</button>
                </div>
            </div>

            <div class="portal-card">
                <h3>Request blood</h3>
                <p class="portal-hint">Submit request directly from patient account.</p>
                <div class="portal-row">
                    <div>
                        <label class="portal-label" for="bloodGroupNeeded">Blood group</label>
                        <select id="bloodGroupNeeded" class="portal-select">
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                        </select>
                    </div>
                    <div>
                        <label class="portal-label" for="bloodUnits">Units required</label>
                        <input id="bloodUnits" class="portal-input" type="number" min="1" value="1">
                    </div>
                </div>
                <div class="portal-row">
                    <div>
                        <label class="portal-label" for="bloodComponentType">Component</label>
                        <select id="bloodComponentType" class="portal-select">
                            <option selected>WholeBlood</option>
                            <option>Plasma</option>
                            <option>Platelets</option>
                            <option>RBC</option>
                        </select>
                    </div>
                    <div>
                        <label class="portal-label" for="bloodUrgency">Urgency</label>
                        <select id="bloodUrgency" class="portal-select">
                            <option>Normal</option>
                            <option selected>Urgent</option>
                            <option>Emergency</option>
                        </select>
                    </div>
                </div>
                <label class="portal-label" for="bloodDepartmentId">Department (optional)</label>
                <select id="bloodDepartmentId" class="portal-select"></select>
                <label class="portal-label" for="bloodNotes">Note</label>
                <textarea id="bloodNotes" class="portal-textarea" placeholder="Optional note for blood bank team"></textarea>
                <div class="portal-btn-row">
                    <button id="btnBlood" class="portal-btn portal-btn-accent" onclick="submitBloodRequest()">Submit Blood Request</button>
                    <button class="portal-btn portal-btn-soft" onclick="loadBloodRequests()">Refresh Blood Requests</button>
                </div>
            </div>
        </div>

        <div class="portal-split">
            <div class="portal-card">
                <h3>Appointments</h3>
                <div class="portal-table-wrap">
                    <table class="portal-table">
                        <thead>
                            <tr><th>ID</th><th>Department</th><th>Doctor</th><th>Datetime</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody id="appointmentsBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="portal-card">
                <h3>Blood requests</h3>
                <div class="portal-table-wrap">
                    <table class="portal-table">
                        <thead>
                            <tr><th>ID</th><th>Group</th><th>Component</th><th>Units</th><th>Urgency</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody id="bloodRequestsBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="portal-card">
            <div class="portal-row">
                <div>
                    <h3>Medical records</h3>
                    <p class="portal-hint">Search by diagnosis, treatment plan, or clinician.</p>
                </div>
                <div>
                    <label class="portal-label" for="recordSearch">Search</label>
                    <input id="recordSearch" class="portal-input" placeholder="Type to filter records">
                </div>
            </div>
            <div class="portal-table-wrap">
                <table class="portal-table">
                    <thead>
                        <tr><th>ID</th><th>Datetime</th><th>Diagnosis</th><th>Treatment</th><th>Created By</th></tr>
                    </thead>
                    <tbody id="recordsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="portal-card">
            <h3>API response</h3>
            <pre id="out" class="portal-pre"></pre>
        </div>
    </div>

    <div id="toastStack" class="portal-toast-stack"></div>
@endsection

@push('scripts')
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
    toast.className = `portal-toast ${type === 'error' ? 'error' : 'ok'}`;
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
    if (!token) return { status: 401, data: { message: 'USER_TOKEN is missing. Login first from /ui/login.' } };

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
    return `<span class="portal-badge ${type}">${html(value)}</span>`;
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
                <td>${row.status === 'Booked' ? `<button class="portal-btn portal-btn-soft" onclick="cancelAppointment(${row.id})">Cancel</button>` : '-'}</td>
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
@endpush
