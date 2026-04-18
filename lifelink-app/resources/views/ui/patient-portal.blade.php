@extends('ui.layouts.app')

@section('title', 'Patient Portal')
@section('role_theme', 'patient')
@section('workspace_label', '')
@section('hero_badge', '')
@section('hero_title', 'Patient Dashboard')
@section('hero_description', '')
@section('hide_meta_card', '1')
@section('meta_title', 'Patient Portal')
@section('meta_copy', 'Appointments, records, and blood support')

@push('styles')
<style>
    :root {
        --portal-ink: #111111;
        --portal-muted: #57534e;
        --portal-line: rgba(17, 17, 17, 0.14);
        --portal-card: rgba(255, 255, 255, 0.9);
        --portal-primary: #1f2937;
        --portal-primary-strong: #000000;
        --portal-accent: #a16207;
        --portal-ok: #166534;
        --portal-warn: #92400e;
        --portal-danger: #b91c1c;
        --portal-shadow: 0 16px 36px rgba(17, 17, 17, 0.15);
    }

    .portal-grid { display: grid; gap: 10px; }
    .portal-panel { display: none; }
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

    .portal-summary {
        margin-top: 9px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 8px;
        align-items: stretch;
    }
    .portal-summary .item {
        border: 1px solid var(--portal-line);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.92);
        padding: 10px;
        min-height: 74px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .portal-summary small {
        display: block;
        color: var(--portal-muted);
        font-size: 11px;
        margin-bottom: 4px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .portal-summary strong {
        font-size: 14px;
        line-height: 1.35;
        word-break: break-word;
    }

    .portal-filters { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 8px; }
    .portal-filters button { background: rgba(18, 43, 66, 0.09); color: var(--portal-ink); padding: 7px 10px; font-size: 12px; border: 1px solid transparent; }
    .portal-filters button.active { background: rgba(15, 118, 110, 0.16); border-color: rgba(15, 118, 110, 0.34); color: var(--portal-primary-strong); }
    .portal-filter-card { grid-column: 1 / -1; }
    .portal-filter-groups { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    .portal-filter-groups > div { border: 1px solid var(--portal-line); border-radius: 10px; background: rgba(255, 255, 255, 0.94); padding: 8px; }
    .portal-filter-groups strong { display: block; font-size: 13px; }

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
        .portal-filter-groups { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#portal-snapshot">
        <strong>Overview</strong>
    </a>
    <a href="#portal-profile">
        <strong>Personal Profile</strong>
    </a>
    <a href="#portal-actions">
        <strong>Book + Request</strong>
    </a>
    <a href="#portal-tables">
        <strong>Appointments + Blood</strong>
    </a>
    <a href="#portal-records">
        <strong>Records</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="portal-grid">
        <div id="portal-snapshot" class="portal-card ll-section portal-panel" data-display="block">
            <h3>Overview</h3>
            <input id="tokenInput" type="hidden">
            <div class="portal-stats">
                <div class="portal-stat"><div class="portal-num" id="stRecords">0</div><div class="portal-lbl">Records</div></div>
                <div class="portal-stat"><div class="portal-num" id="stUpcoming">0</div><div class="portal-lbl">Upcoming</div></div>
                <div class="portal-stat"><div class="portal-num" id="stRequests">0</div><div class="portal-lbl">Blood Requests</div></div>
                <div class="portal-stat"><div class="portal-num" id="stRoleCount">0</div><div class="portal-lbl">Roles</div></div>
            </div>
            <div class="portal-summary" id="patientSummary"></div>
        </div>

        <div id="portal-profile" class="portal-card ll-section portal-panel" data-display="block">
            <div id="patientProfileMount"></div>
        </div>

        <div id="portal-actions" class="portal-split ll-section portal-panel" data-display="grid">
            <div class="portal-card portal-filter-card">
                <div class="portal-filter-groups">
                    <div>
                        <strong>Appointment filter</strong>
                        <div class="portal-filters" data-filter-group="appointment">
                            <button data-status="" class="active" onclick="setAppointmentStatus('')">All</button>
                            <button data-status="PendingApproval" onclick="setAppointmentStatus('PendingApproval')">Pending</button>
                            <button data-status="Approved" onclick="setAppointmentStatus('Approved')">Approved</button>
                            <button data-status="Booked" onclick="setAppointmentStatus('Booked')">Booked</button>
                            <button data-status="Rejected" onclick="setAppointmentStatus('Rejected')">Rejected</button>
                            <button data-status="Cancelled" onclick="setAppointmentStatus('Cancelled')">Cancelled</button>
                            <button data-status="Completed" onclick="setAppointmentStatus('Completed')">Completed</button>
                            <button data-status="NoShow" onclick="setAppointmentStatus('NoShow')">No Show</button>
                        </div>
                    </div>
                    <div>
                        <strong>Blood request filter</strong>
                        <div class="portal-filters" data-filter-group="blood">
                            <button data-status="" class="active" onclick="setBloodStatus('')">All</button>
                            <button data-status="Pending" onclick="setBloodStatus('Pending')">Pending</button>
                            <button data-status="Fulfilled" onclick="setBloodStatus('Fulfilled')">Fulfilled</button>
                            <button data-status="Rejected" onclick="setBloodStatus('Rejected')">Rejected</button>
                            <button data-status="Cancelled" onclick="setBloodStatus('Cancelled')">Cancelled</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="portal-card">
                <h3>Book appointment</h3>
                <p class="portal-hint">Choose department, doctor, and appointment date. Exact hour/minute selection is intentionally not required.</p>
                <div class="portal-row">
                    <div>
                        <label class="portal-label" for="appointmentDepartmentId">Department</label>
                        <select id="appointmentDepartmentId" class="portal-select"></select>
                    </div>
                    <div>
                        <label class="portal-label" for="appointmentDoctorUserId">Doctor</label>
                        <select id="appointmentDoctorUserId" class="portal-select"></select>
                        <div class="portal-mini" id="doctorMeta">Doctors load from active profiles in the selected department.</div>
                    </div>
                </div>
                <label class="portal-label" for="appointmentDate">Appointment date</label>
                <input id="appointmentDate" class="portal-input" type="date">
                <div class="portal-mini" id="doctorScheduleMeta">Select doctor and date to view consultation window guidance.</div>
                <div class="portal-btn-row">
                    <button id="btnBook" class="portal-btn portal-btn-main" onclick="bookAppointment()">Submit Appointment Request</button>
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

        <div id="portal-tables" class="portal-split ll-section portal-panel" data-display="grid">
            <div class="portal-card portal-filter-card">
                <div class="portal-filter-groups">
                    <div>
                        <strong>Appointment filter</strong>
                        <div class="portal-filters" data-filter-group="appointment">
                            <button data-status="" class="active" onclick="setAppointmentStatus('')">All</button>
                            <button data-status="PendingApproval" onclick="setAppointmentStatus('PendingApproval')">Pending</button>
                            <button data-status="Approved" onclick="setAppointmentStatus('Approved')">Approved</button>
                            <button data-status="Booked" onclick="setAppointmentStatus('Booked')">Booked</button>
                            <button data-status="Rejected" onclick="setAppointmentStatus('Rejected')">Rejected</button>
                            <button data-status="Cancelled" onclick="setAppointmentStatus('Cancelled')">Cancelled</button>
                            <button data-status="Completed" onclick="setAppointmentStatus('Completed')">Completed</button>
                            <button data-status="NoShow" onclick="setAppointmentStatus('NoShow')">No Show</button>
                        </div>
                    </div>
                    <div>
                        <strong>Blood request filter</strong>
                        <div class="portal-filters" data-filter-group="blood">
                            <button data-status="" class="active" onclick="setBloodStatus('')">All</button>
                            <button data-status="Pending" onclick="setBloodStatus('Pending')">Pending</button>
                            <button data-status="Fulfilled" onclick="setBloodStatus('Fulfilled')">Fulfilled</button>
                            <button data-status="Rejected" onclick="setBloodStatus('Rejected')">Rejected</button>
                            <button data-status="Cancelled" onclick="setBloodStatus('Cancelled')">Cancelled</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="portal-card">
                <h3>Appointments</h3>
                <div class="portal-table-wrap">
                    <table class="portal-table">
                        <thead>
                            <tr><th>ID</th><th>Department</th><th>Doctor</th><th>Date</th><th>Consultation Window</th><th>Status</th><th>Action</th></tr>
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

        <div id="portal-records" class="portal-card ll-section portal-panel" data-display="block">
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
    doctorsById: {},
    records: [],
    recordSearch: ''
};
const portalPanelIds = ['portal-snapshot', 'portal-profile', 'portal-actions', 'portal-tables', 'portal-records'];
const portalNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[href^="#portal-"]'));

function byId(id) { return document.getElementById(id); }
function write(data) {
    if (!window.lifeLinkShell?.isDebugEnabled() || !out) return;
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}
function setActivePanel(panelId) {
    portalPanelIds.forEach((id) => {
        const panel = byId(id);
        if (!panel) return;
        panel.style.display = id === panelId ? (panel.dataset.display || 'block') : 'none';
    });

    portalNavLinks.forEach((link) => {
        const targetId = (link.getAttribute('href') || '').replace('#', '');
        link.classList.toggle('is-active', targetId === panelId);
    });
}

function setupSidebarPanelNav() {
    portalNavLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const panelId = (link.getAttribute('href') || '').replace('#', '');
            if (!portalPanelIds.includes(panelId)) return;
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        });
    });

    const initialHash = (window.location.hash || '').replace('#', '');
    const initialPanel = portalPanelIds.includes(initialHash) ? initialHash : portalPanelIds[0];
    setActivePanel(initialPanel);
}
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
    const clock = byId('clockNow');
    if (!clock) return;
    clock.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
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

function setFilterActive(group, status) {
    document.querySelectorAll(`[data-filter-group="${group}"]`).forEach((container) => {
        container.querySelectorAll('button[data-status]').forEach((b) => {
            b.classList.toggle('active', (b.getAttribute('data-status') || '') === status);
        });
    });
}

function populateDoctorOptions() {
    const departmentId = Number(byId('appointmentDepartmentId').value || 0);
    const doctors = state.doctors.filter((d) => !departmentId || Number(d.department_id) === departmentId);
    const options = ['<option value="">Select doctor</option>']
        .concat(doctors.map((d) => `<option value="${d.user_id}">${html(d.full_name || 'Doctor')} (${html(d.specialization || 'General')})</option>`))
        .join('');
    const select = byId('appointmentDoctorUserId');
    const prev = select.value;
    select.innerHTML = options;
    if (prev && doctors.some((d) => String(d.user_id) === prev)) select.value = prev;
    byId('doctorMeta').textContent = doctors.length
        ? `${doctors.length} active doctor(s) in selected department.`
        : 'No active doctors in this department.';
    renderDoctorScheduleMeta();
}

function isBloodBankDepartment(department) {
    const raw = String(department?.dept_name || '').trim().toLowerCase();
    const normalized = raw.replace(/\s|_/g, '');
    return normalized === 'bloodbank';
}

function populateDepartmentOptions() {
    const ap = byId('appointmentDepartmentId');
    const bl = byId('bloodDepartmentId');
    const currentAp = ap.value;
    const currentBl = bl.value;
    if (!state.departments.length) {
        ap.innerHTML = '<option value="">No departments</option>';
        bl.innerHTML = '<option value="">Auto</option>';
        byId('appointmentDoctorUserId').innerHTML = '<option value="">Select doctor</option>';
        return;
    }

    const appointmentDepartments = state.departments.filter((department) => !isBloodBankDepartment(department));
    const appointmentOptions = appointmentDepartments.map((d) => `<option value="${d.id}">${html(d.dept_name)} (#${d.id})</option>`).join('');
    const bloodOptions = state.departments.map((d) => `<option value="${d.id}">${html(d.dept_name)} (#${d.id})</option>`).join('');

    ap.innerHTML = appointmentOptions || '<option value="">No appointment departments</option>';
    bl.innerHTML = `<option value="">Auto</option>${bloodOptions}`;
    ap.value = appointmentDepartments.some((d) => String(d.id) === currentAp)
        ? currentAp
        : (appointmentDepartments[0] ? String(appointmentDepartments[0].id) : '');
    bl.value = state.departments.some((d) => String(d.id) === currentBl) ? currentBl : '';
    populateDoctorOptions();
}

function weekdayNameFromDate(dateString) {
    if (!dateString) return '';
    const d = new Date(`${dateString}T00:00:00`);
    return Number.isNaN(d.getTime()) ? '' : d.toLocaleDateString(undefined, { weekday: 'long' });
}

function renderDoctorScheduleMeta() {
    const doctorId = Number(byId('appointmentDoctorUserId').value || 0);
    const appointmentDate = byId('appointmentDate').value;
    const target = byId('doctorScheduleMeta');
    if (!doctorId) {
        target.textContent = 'Choose a doctor to view consultation routine details.';
        return;
    }

    const doctor = state.doctorsById[doctorId];
    if (!doctor) {
        target.textContent = 'Doctor routine summary is not available yet.';
        return;
    }

    const summary = doctor.schedule_summary || {};
    const windowLabel = summary?.consultation_window?.label || 'Not configured';
    const weekday = weekdayNameFromDate(appointmentDate);
    const weekdayCapacity = (() => {
        if (!appointmentDate) return null;
        const dateObj = new Date(`${appointmentDate}T00:00:00`);
        if (Number.isNaN(dateObj.getTime())) return null;
        return summary?.daily_capacity_by_weekday?.[String(dateObj.getDay())] ?? null;
    })();

    const baseCapacity = Number.isFinite(Number(summary?.daily_capacity_default))
        ? Number(summary.daily_capacity_default)
        : 0;
    const capacityLabel = weekdayCapacity !== null
        ? `${weekday} capacity: ${weekdayCapacity}`
        : `Default daily capacity: ${baseCapacity}`;

    target.textContent = `Consultation window: ${windowLabel}. ${capacityLabel}. Remaining count is verified by backend when you submit the request.`;
}

async function loadBookingOptions() {
    const r = await call('/patient/booking-options', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load booking options', 'error'); return; }
    state.departments = r.data?.departments || [];
    state.doctors = r.data?.doctors || [];
    state.doctorsById = Object.fromEntries(state.doctors.map((doctor) => [Number(doctor.user_id), doctor]));
    populateDepartmentOptions();
    renderDoctorScheduleMeta();
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
                <td>${html(row.doctor_name || '-')}</td>
                <td>${html(row.appointment_date || '-')}</td>
                <td>${html(row.consultation_window?.label || 'Not configured')}</td>
                <td>${statusBadge(row.status)}</td>
                <td>${['PendingApproval', 'Approved', 'Booked'].includes(String(row.status || '')) ? `<button class="portal-btn portal-btn-soft" onclick="cancelAppointment(${row.id})">Cancel</button>` : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="7">No appointments found.</td></tr>';
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
    const departmentId = Number(byId('appointmentDepartmentId').value || 0);
    const doctorUserId = Number(byId('appointmentDoctorUserId').value || 0);
    const appointmentDate = byId('appointmentDate').value;
    if (!departmentId || !doctorUserId || !appointmentDate) {
        showToast('Choose department, doctor, and appointment date', 'error');
        return;
    }

    const payload = {
        departmentId,
        doctorUserId,
        appointmentDate,
    };
    setButtonBusy('btnBook', true);
    const r = await call('/patient/appointments', 'POST', payload);
    setButtonBusy('btnBook', false);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Appointment booking failed', 'error'); return; }
    const capacity = r.data?.capacity;
    showToast(capacity
        ? `Request submitted. Remaining capacity: ${capacity.remaining_count}`
        : 'Appointment request submitted');
    await Promise.all([loadAppointments(), loadPortal()]);
    renderDoctorScheduleMeta();
}

async function cancelAppointment(id) {
    const reason = prompt('Cancel reason (optional):', 'Cancelled by patient');
    const payload = reason ? { cancelReason: reason } : {};
    const r = await call(`/patient/appointments/${id}/cancel`, 'POST', payload);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not cancel appointment', 'error'); return; }
    showToast('Appointment cancelled');
    await Promise.all([loadAppointments(), loadPortal()]);
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
    await Promise.all([loadBloodRequests(), loadPortal()]);
}

function setAppointmentStatus(status) { state.appointmentStatus = status; setFilterActive('appointment', status); loadAppointments(); }
function setBloodStatus(status) { state.bloodStatus = status; setFilterActive('blood', status); loadBloodRequests(); }

async function refreshAll() {
    setButtonBusy('btnRefresh', true);
    try {
        await Promise.all([
            loadBookingOptions(),
            loadPortal(),
            loadMedicalRecords(),
            loadAppointments(),
            loadBloodRequests(),
        ]);
        showToast('Dashboard refreshed');
    } finally {
        setButtonBusy('btnRefresh', false);
    }
}

function boot() {
    setupSidebarPanelNav();
    const patientName = localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Patient';
    if (window.lifeLinkShell) {
        window.lifeLinkShell.updateIdentityContext({
            name: patientName,
            userId: localStorage.getItem('CURRENT_USER_ID') || '-',
            email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
            role: 'Patient',
            hideDepartment: true,
        });
        window.lifeLinkShell.mountProfileEditor({
            containerId: 'patientProfileMount',
            role: 'Patient',
            userId: localStorage.getItem('CURRENT_USER_ID') || '-',
            hideDepartment: true,
        });
    }
    setClock();
    setInterval(setClock, 1000);
    setFilterActive('appointment', state.appointmentStatus);
    setFilterActive('blood', state.bloodStatus);
    useStoredUserToken();
    const appointmentDateInput = byId('appointmentDate');
    const today = new Date();
    appointmentDateInput.min = today.toISOString().slice(0, 10);
    const tomorrow = new Date(today.getTime() + (24 * 60 * 60 * 1000));
    appointmentDateInput.value = tomorrow.toISOString().slice(0, 10);
    byId('appointmentDepartmentId').addEventListener('change', populateDoctorOptions);
    byId('appointmentDoctorUserId').addEventListener('change', renderDoctorScheduleMeta);
    byId('appointmentDate').addEventListener('change', renderDoctorScheduleMeta);
    byId('recordSearch').addEventListener('input', (event) => { state.recordSearch = event.target.value; renderMedicalRecords(); });
    refreshAll();
}

boot();
</script>
@endpush
