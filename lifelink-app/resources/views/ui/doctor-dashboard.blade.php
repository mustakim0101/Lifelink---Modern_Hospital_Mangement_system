@extends('ui.layouts.app')

@section('title', 'Doctor Dashboard')
@section('role_theme', 'doctor')
@section('workspace_label', 'Doctor workspace')
@section('hero_badge', 'Doctor')
@section('hero_title', 'Doctor Dashboard')
@section('hero_description', 'Appointments, patient tracking, and bed-request coordination')
@section('hide_meta_card', '1')
@section('meta_title', 'Doctor Dashboard')
@section('meta_copy', 'Clinical actions and admissions support')

@push('styles')
<style>
    .doctor-shell { display: grid; gap: 16px; }
    .doctor-panel { display: none; }
    .doctor-head {
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(243, 248, 252, 0.94));
        padding: 18px;
    }

    .doctor-head h2 {
        margin: 0;
        font-size: clamp(1.7rem, 2.8vw, 2.4rem);
        color: #0f172a;
    }

    .doctor-head p {
        margin: 8px 0 0;
        color: #475569;
    }

    .doctor-stats {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .doctor-stat {
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.26);
        padding: 16px;
        background: #fff;
    }

    .doctor-stat--appointments {
        border-color: rgba(29, 78, 216, 0.45);
        background: rgba(219, 234, 254, 0.55);
    }

    .doctor-stat--active {
        border-color: rgba(5, 150, 105, 0.45);
        background: rgba(209, 250, 229, 0.55);
    }

    .doctor-stat--pending {
        border-color: rgba(217, 119, 6, 0.5);
        background: rgba(254, 243, 199, 0.65);
    }

    .doctor-stat__label {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 800;
        color: #475569;
    }

    .doctor-stat strong {
        display: block;
        margin-top: 12px;
        font-size: 2rem;
        color: #0f172a;
    }

    .doctor-stat small {
        display: block;
        margin-top: 5px;
        color: #475569;
    }

    .doctor-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .doctor-card {
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.96);
        padding: 16px;
    }

    .doctor-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .doctor-card h3 {
        margin: 0;
        color: #0f172a;
    }

    .doctor-chip-btn {
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 10px;
        padding: 8px 12px;
        background: #fff;
        color: #1e3a5f;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }

    .doctor-empty {
        padding: 24px 12px;
        color: #64748b;
        text-align: center;
    }

    .doctor-list {
        display: grid;
        gap: 10px;
    }

    .doctor-list-item {
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 12px;
        padding: 12px;
        background: rgba(248, 250, 252, 0.7);
    }

    .doctor-list-item strong {
        color: #0f172a;
        display: block;
    }

    .doctor-list-meta {
        margin-top: 6px;
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .doctor-actions-row {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .doctor-action-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 40px;
        padding: 10px 12px;
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 12px;
        background: #fff;
        color: #0f172a;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .doctor-workflow {
        display: grid;
        gap: 14px;
    }

    .doctor-form-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .doctor-field label {
        display: block;
        margin: 0 0 6px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
    }

    .doctor-field input,
    .doctor-field select,
    .doctor-field textarea {
        width: 100%;
        min-height: 42px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: #fff;
        color: #0f172a;
        font: inherit;
        padding: 10px 11px;
        outline: none;
    }

    .doctor-field textarea {
        min-height: 88px;
        resize: vertical;
    }

    .doctor-field input:focus,
    .doctor-field select:focus,
    .doctor-field textarea:focus {
        border-color: rgba(29, 78, 216, 0.6);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .doctor-submit {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 40px;
        padding: 10px 14px;
        border: 0;
        border-radius: 10px;
        background: #0369a1;
        color: #fff;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }

    .doctor-submit:hover {
        background: #075985;
    }

    .doctor-debug {
        margin: 0;
        min-height: 120px;
        max-height: 260px;
        overflow: auto;
        border-radius: 10px;
        border: 1px solid rgba(15, 23, 42, 0.2);
        background: #0f1e34;
        color: #d7e3ff;
        padding: 10px;
        font-size: 12px;
    }

    @media (max-width: 1100px) {
        .doctor-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .doctor-grid {
            grid-template-columns: 1fr;
        }

        .doctor-actions-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .doctor-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#doctor-overview" data-panel="doctor-overview"><strong>Dashboard</strong></a>
    <a href="#doctor-schedule" data-panel="doctor-schedule"><strong>Consultation Routine</strong></a>
    <a href="#doctor-load-summary" data-panel="doctor-load-summary"><strong>Daily Load Summary</strong></a>
    <a href="#doctor-appointments-workflow" data-panel="doctor-appointments-workflow"><strong>Appointment Controls</strong></a>
    <a href="#doctor-request" data-panel="doctor-request"><strong>Bed Requests</strong></a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="doctor-shell">
        <div id="doctor-overview" class="doctor-panel ll-section" data-display="block">
            <section class="ll-overview-welcome">
                <h2 id="doctorWelcome">Welcome back</h2>
                <div id="doctorWelcomeName" class="ll-welcome-name">Doctor</div>
                <div id="doctorMetaLine" class="ll-welcome-meta">Loading doctor profile...</div>
            </section>

            <section class="doctor-stats">
                <article class="doctor-stat doctor-stat--appointments">
                    <span class="doctor-stat__label">Today's Appointments</span>
                    <strong id="statAppointments">0</strong>
                    <small>Scheduled for today</small>
                </article>
                <article class="doctor-stat doctor-stat--active">
                    <span class="doctor-stat__label">Active Patients</span>
                    <strong id="statActivePatients">0</strong>
                    <small>Currently admitted</small>
                </article>
                <article class="doctor-stat doctor-stat--pending">
                    <span class="doctor-stat__label">Pending Bed Requests</span>
                    <strong id="statPendingRequests">0</strong>
                    <small>Awaiting assignment</small>
                </article>
                <article class="doctor-stat">
                    <span class="doctor-stat__label">Total Patients</span>
                    <strong id="statTotalPatients">0</strong>
                    <small>Under your care</small>
                </article>
            </section>

            <section class="doctor-grid">
                <article class="doctor-card">
                    <div class="doctor-card-head">
                        <h3>My Appointments</h3>
                        <button class="doctor-chip-btn" type="button" onclick="doctorAppointments()">Refresh</button>
                    </div>
                    <div id="appointmentsList" class="doctor-list ui-list-window"></div>
                    <div id="appointmentsPagination" class="ui-list-pagination"></div>
                </article>

                <article class="doctor-card">
                    <div class="doctor-card-head">
                        <h3>Active Patients</h3>
                        <button class="doctor-chip-btn" type="button" onclick="doctorPatients()">Refresh</button>
                    </div>
                    <div id="patientsList" class="doctor-list ui-list-window"></div>
                    <div id="patientsPagination" class="ui-list-pagination"></div>
                </article>
            </section>

        </div>

        <section id="doctor-schedule" class="doctor-card doctor-workflow ll-section doctor-panel" data-display="block">
            <div class="doctor-card-head">
                <h3>Consultation Routine Setup</h3>
                <button class="doctor-chip-btn" type="button" onclick="doctorAppointmentRules()">Load rules</button>
            </div>
            <p class="doctor-list-meta">Set recurring weekdays, consultation window, and daily patient capacity. Patients submit date-only requests from this routine.</p>

            <input type="hidden" id="ruleEditId">
            <div class="doctor-form-grid">
                <div class="doctor-field">
                    <label for="ruleDayOfWeek">Weekday</label>
                    <select id="ruleDayOfWeek">
                        <option value="0">Sunday</option>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                    </select>
                </div>
                <div class="doctor-field">
                    <label for="ruleDailyCapacity">Daily capacity</label>
                    <input id="ruleDailyCapacity" type="number" min="1" max="500" value="10">
                </div>
            </div>
            <div class="doctor-form-grid">
                <div class="doctor-field">
                    <label for="ruleStartTime">Start time</label>
                    <input id="ruleStartTime" type="time" value="09:00">
                </div>
                <div class="doctor-field">
                    <label for="ruleEndTime">End time</label>
                    <input id="ruleEndTime" type="time" value="13:00">
                </div>
            </div>
            <div class="doctor-actions-row">
                <button class="doctor-submit" type="button" onclick="saveAppointmentRule()">Save Routine</button>
                <button class="doctor-action-btn" type="button" onclick="resetAppointmentRuleForm()">Clear Edit</button>
            </div>

            <div id="appointmentRulesList" class="doctor-list ui-list-window"></div>
            <div id="appointmentRulesPagination" class="ui-list-pagination"></div>
        </section>

        <section id="doctor-load-summary" class="doctor-card doctor-workflow ll-section doctor-panel" data-display="block">
            <div class="doctor-card-head">
                <h3>Daily Appointment Load</h3>
                <button class="doctor-chip-btn" type="button" onclick="doctorAppointmentSummary()">Refresh summary</button>
            </div>
            <p class="doctor-list-meta">Monitor pending and approved demand by date, with remaining capacity from your configured routine.</p>
            <div class="doctor-form-grid">
                <div class="doctor-field">
                    <label for="summaryDateFrom">Date from</label>
                    <input id="summaryDateFrom" type="date">
                </div>
                <div class="doctor-field">
                    <label for="summaryDateTo">Date to</label>
                    <input id="summaryDateTo" type="date">
                </div>
            </div>
            <div class="doctor-actions-row">
                <button class="doctor-action-btn" type="button" onclick="doctorAppointmentSummary()">Apply Date Range</button>
            </div>
            <div id="appointmentSummaryList" class="doctor-list ui-list-window"></div>
            <div id="appointmentSummaryPagination" class="ui-list-pagination"></div>
        </section>

        <section id="doctor-request" class="doctor-card doctor-workflow ll-section doctor-panel" data-display="block">
            <div class="doctor-card-head">
                <h3>Create Bed Request</h3>
                <button class="doctor-chip-btn" type="button" onclick="doctorBedRequests()">Load Requests</button>
            </div>

            <div class="doctor-form-grid">
                <div class="doctor-field">
                    <label for="patientUserIdSelect">Patient</label>
                    <select id="patientUserIdSelect">
                        <option value="">Select patient</option>
                    </select>
                </div>
                <div class="doctor-field">
                    <label for="careLevelRequested">Care level</label>
                    <select id="careLevelRequested">
                        <option value="Ward">Ward</option>
                        <option value="ICU">ICU</option>
                        <option value="NICU">NICU</option>
                        <option value="CCU">CCU</option>
                    </select>
                </div>
            </div>

            <div class="doctor-field">
                <label for="diagnosis">Diagnosis</label>
                <input id="diagnosis" placeholder="Diagnosis summary">
            </div>

            <div class="doctor-field">
                <label for="requestNotes">Request notes</label>
                <textarea id="requestNotes" placeholder="Clinical notes (optional)"></textarea>
            </div>

            <button class="doctor-submit" type="button" onclick="createBedRequest()">Create Bed Request</button>
        </section>

        <section id="doctor-appointments-workflow" class="doctor-card doctor-workflow ll-section doctor-panel" data-display="block">
            <div class="doctor-card-head">
                <h3>Appointment Controls</h3>
                <button class="doctor-chip-btn" type="button" onclick="doctorAppointments()">Reload appointments</button>
            </div>

            <div class="doctor-form-grid">
                <div class="doctor-field">
                    <label for="appointmentStatusFilter">Status filter</label>
                    <select id="appointmentStatusFilter">
                        <option value="">All appointment statuses</option>
                        <option value="PendingApproval">PendingApproval</option>
                        <option value="Approved">Approved</option>
                        <option value="Booked">Booked</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Completed">Completed</option>
                        <option value="NoShow">NoShow</option>
                    </select>
                </div>
                <div class="doctor-field">
                    <label for="cancelAppointmentId">Cancel appointment</label>
                    <select id="cancelAppointmentId">
                        <option value="">Select appointment</option>
                    </select>
                </div>
            </div>

            <div class="doctor-field">
                <label for="cancelReason">Cancel reason</label>
                <input id="cancelReason" placeholder="Optional cancellation reason">
            </div>

            <div class="doctor-actions-row">
                <button class="doctor-action-btn" type="button" onclick="doctorAppointments()">Apply Filter</button>
                <button class="doctor-submit" type="button" onclick="cancelAppointment()">Cancel Appointment</button>
            </div>
        </section>

    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const doctorPanelIds = ['doctor-overview', 'doctor-schedule', 'doctor-load-summary', 'doctor-appointments-workflow', 'doctor-request'];
const out = document.getElementById('out');
const token = localStorage.getItem('USER_TOKEN') || '';
let doctorPanelControl = null;

const dashboardState = {
    profile: null,
    patients: [],
    appointments: [],
    bedRequests: [],
    appointmentRules: [],
    appointmentSummary: [],
    pagination: {
        pageSize: 5,
        appointmentsPage: 1,
        patientsPage: 1,
        rulesPage: 1,
        summaryPage: 1,
    }
};

function write(data) {
    if (!out) return;
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

async function call(path, method = 'GET', body = null) {
    if (!token) return { status: 401, data: { message: 'doctor token missing' } };

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

function getArrayPayload(result, preferredKeys) {
    if (!result || typeof result.data !== 'object' || !result.data) return [];
    for (const key of preferredKeys) {
        const value = result.data[key];
        if (Array.isArray(value)) return value;
    }
    return [];
}

function paginateRows(rows, page, pageSize) {
    const safeRows = Array.isArray(rows) ? rows : [];
    const safeSize = Math.max(1, Number(pageSize) || 1);
    const totalPages = Math.max(1, Math.ceil(safeRows.length / safeSize));
    const safePage = Math.min(Math.max(1, Number(page) || 1), totalPages);
    const start = (safePage - 1) * safeSize;
    return {
        rows: safeRows.slice(start, start + safeSize),
        page: safePage,
        totalPages,
        totalRows: safeRows.length,
    };
}

function renderDoctorPagination(rootId, pageData, prevHandler, nextHandler) {
    const root = document.getElementById(rootId);
    if (!root) return;

    if (pageData.totalRows <= dashboardState.pagination.pageSize) {
        root.innerHTML = '';
        return;
    }

    root.innerHTML = `
        <div class="ui-list-pagination__meta">Page ${pageData.page} of ${pageData.totalPages} (${pageData.totalRows} total)</div>
        <div class="ui-list-pagination__controls">
            <button class="doctor-chip-btn" type="button" ${pageData.page <= 1 ? 'disabled' : ''} onclick="${prevHandler}">Previous</button>
            <button class="doctor-chip-btn" type="button" ${pageData.page >= pageData.totalPages ? 'disabled' : ''} onclick="${nextHandler}">Next</button>
        </div>
    `;
}

function prevAppointmentsPage() {
    dashboardState.pagination.appointmentsPage = Math.max(1, dashboardState.pagination.appointmentsPage - 1);
    renderOverview();
}

function nextAppointmentsPage() {
    dashboardState.pagination.appointmentsPage += 1;
    renderOverview();
}

function prevPatientsPage() {
    dashboardState.pagination.patientsPage = Math.max(1, dashboardState.pagination.patientsPage - 1);
    renderOverview();
}

function nextPatientsPage() {
    dashboardState.pagination.patientsPage += 1;
    renderOverview();
}

function prevRulesPage() {
    dashboardState.pagination.rulesPage = Math.max(1, dashboardState.pagination.rulesPage - 1);
    renderAppointmentRules();
}

function nextRulesPage() {
    dashboardState.pagination.rulesPage += 1;
    renderAppointmentRules();
}

function prevSummaryPage() {
    dashboardState.pagination.summaryPage = Math.max(1, dashboardState.pagination.summaryPage - 1);
    renderAppointmentSummary();
}

function nextSummaryPage() {
    dashboardState.pagination.summaryPage += 1;
    renderAppointmentSummary();
}

function renderOverview() {
    const profile = dashboardState.profile || {};
    const patients = dashboardState.patients;
    const appointments = dashboardState.appointments;
    const bedRequests = dashboardState.bedRequests;

    const today = new Date().toISOString().slice(0, 10);
    const todaysAppointments = appointments.filter(item => String(item?.appointment_date || '').slice(0, 10) === today || String(item?.appointmentDate || '').slice(0, 10) === today);
    const activePatients = patients.filter(item => {
        const status = String(item?.admission_status || item?.admissionStatus || '').toLowerCase();
        return status === 'admitted';
    });
    const pendingRequests = bedRequests.filter(item => {
        const status = String(item?.status || '').toLowerCase();
        return status.includes('pending') || status.includes('requested');
    });

    document.getElementById('statAppointments').textContent = String(todaysAppointments.length);
    document.getElementById('statActivePatients').textContent = String(activePatients.length);
    document.getElementById('statPendingRequests').textContent = String(pendingRequests.length);
    document.getElementById('statTotalPatients').textContent = String(patients.length);

    const name = profile.full_name || profile.fullName || localStorage.getItem('CURRENT_USER_FULL_NAME') || 'Doctor';
    const dept = profile.department || profile.dept_name || profile.department_name || 'Department';
    const spec = profile.specialization || profile.speciality || 'Clinical Operations';
    document.getElementById('doctorWelcome').textContent = 'Welcome back';
    document.getElementById('doctorWelcomeName').textContent = name;
    document.getElementById('doctorMetaLine').textContent = `Role: Doctor | Department: ${dept} | Focus: ${spec} | Email: ${localStorage.getItem('CURRENT_USER_EMAIL') || '-'} | ID: ${localStorage.getItem('CURRENT_USER_ID') || '-'}`;
    if (window.lifeLinkShell) {
        window.lifeLinkShell.updateIdentityContext({
            name,
            userId: localStorage.getItem('CURRENT_USER_ID') || '-',
            email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
            role: 'Doctor',
            department: dept,
        });
    }

    renderAppointmentsList(appointments);
    renderPatientsList(activePatients.length ? activePatients : patients);
    populatePatientPicker(patients);
    populateAppointmentPicker(appointments);
    renderAppointmentRules();
    renderAppointmentSummary();
}

function renderAppointmentsList(items) {
    const box = document.getElementById('appointmentsList');
    if (!box) return;

    if (!items.length) {
        box.innerHTML = `<p class="doctor-empty">No appointments scheduled for this selection.</p>`;
        renderDoctorPagination('appointmentsPagination', { page: 1, totalPages: 1, totalRows: 0 }, 'prevAppointmentsPage()', 'nextAppointmentsPage()');
        return;
    }

    const pageData = paginateRows(items, dashboardState.pagination.appointmentsPage, dashboardState.pagination.pageSize);
    dashboardState.pagination.appointmentsPage = pageData.page;

    box.innerHTML = pageData.rows.map(item => {
        const date = item?.appointment_date || item?.appointmentDate || '-';
        const dateTime = item?.appointment_datetime ? new Date(item.appointment_datetime).toLocaleString() : '-';
        const status = item?.status || '-';
        const patient = item?.patient_name || item?.patientName || item?.patient_full_name || `Patient #${item?.patient_user_id || item?.patientUserId || '-'}`;
        const rejectionReason = item?.rejection_reason ? `<div class="doctor-list-meta">Rejection reason: ${item.rejection_reason}</div>` : '';
        return `
            <article class="doctor-list-item">
                <strong>${patient}</strong>
                <div class="doctor-list-meta">Date: ${date} | Assigned start: ${dateTime}</div>
                <div class="doctor-list-meta">Status: ${status}</div>
                ${rejectionReason}
            </article>
        `;
    }).join('');

    renderDoctorPagination('appointmentsPagination', pageData, 'prevAppointmentsPage()', 'nextAppointmentsPage()');
}

function renderPatientsList(items) {
    const box = document.getElementById('patientsList');
    if (!box) return;

    if (!items.length) {
        box.innerHTML = `<p class="doctor-empty">No active patients available.</p>`;
        renderDoctorPagination('patientsPagination', { page: 1, totalPages: 1, totalRows: 0 }, 'prevPatientsPage()', 'nextPatientsPage()');
        return;
    }

    const pageData = paginateRows(items, dashboardState.pagination.patientsPage, dashboardState.pagination.pageSize);
    dashboardState.pagination.patientsPage = pageData.page;

    box.innerHTML = pageData.rows.map(item => {
        const name = item?.full_name || item?.fullName || item?.patient_name || 'Unnamed Patient';
        const mrn = item?.mrn || item?.patient_mrn || item?.patientMrn || '-';
        const diagnosis = item?.diagnosis || item?.latest_diagnosis || 'No diagnosis available';
        const blood = item?.blood_group || item?.bloodGroup || '-';
        const bed = item?.bed_no || item?.bed_number || item?.bedNumber || 'Not assigned';
        return `
            <article class="doctor-list-item">
                <strong>${name}</strong>
                <div class="doctor-list-meta">MRN: ${mrn}</div>
                <div class="doctor-list-meta">${diagnosis}</div>
                <div class="doctor-list-meta">Blood: ${blood} | Bed: ${bed}</div>
            </article>
        `;
    }).join('');

    renderDoctorPagination('patientsPagination', pageData, 'prevPatientsPage()', 'nextPatientsPage()');
}

function populatePatientPicker(items) {
    const select = document.getElementById('patientUserIdSelect');
    if (!select) return;

    const options = items.map(item => {
        const id = item?.user_id || item?.patient_user_id || item?.patientUserId || item?.id;
        const name = item?.full_name || item?.fullName || item?.patient_name || `Patient #${id}`;
        return id ? `<option value="${id}">${name}</option>` : '';
    }).filter(Boolean);

    select.innerHTML = `<option value="">Select patient</option>${options.join('')}`;
}

function populateAppointmentPicker(items) {
    const select = document.getElementById('cancelAppointmentId');
    if (!select) return;

    const options = items.map(item => {
        const id = item?.appointment_id || item?.appointmentId || item?.id;
        const date = item?.appointment_date || item?.appointmentDate || '-';
        const patient = item?.patient_name || item?.patientName || 'Patient';
        return id ? `<option value="${id}">#${id} - ${patient} (${date})</option>` : '';
    }).filter(Boolean);

    select.innerHTML = `<option value="">Select appointment</option>${options.join('')}`;
}

function getRuleEditId() {
    return Number(document.getElementById('ruleEditId').value || 0);
}

function normalizeIsoDate(value) {
    if (!value) return '';
    return String(value).slice(0, 10);
}

function consultationWindowLabel(window) {
    if (!window) return 'Not configured';
    if (typeof window === 'string') return window;
    return window.label || `${window.start_time || '-'} - ${window.end_time || '-'}`;
}

function renderAppointmentRules() {
    const box = document.getElementById('appointmentRulesList');
    if (!box) return;

    if (!dashboardState.appointmentRules.length) {
        box.innerHTML = `<p class="doctor-empty">No recurring consultation routine configured yet.</p>`;
        renderDoctorPagination('appointmentRulesPagination', { page: 1, totalPages: 1, totalRows: 0 }, 'prevRulesPage()', 'nextRulesPage()');
        return;
    }

    const pageData = paginateRows(dashboardState.appointmentRules, dashboardState.pagination.rulesPage, dashboardState.pagination.pageSize);
    dashboardState.pagination.rulesPage = pageData.page;

    box.innerHTML = pageData.rows.map((rule) => `
        <article class="doctor-list-item">
            <strong>${rule.weekday} (${rule.start_time} - ${rule.end_time})</strong>
            <div class="doctor-list-meta">Capacity: ${rule.daily_capacity} patient(s) | Status: ${rule.is_active ? 'Active' : 'Inactive'}</div>
            <div class="doctor-actions-row">
                <button class="doctor-action-btn" type="button" onclick="editAppointmentRule(${rule.id})">Edit</button>
                <button class="doctor-action-btn" type="button" onclick="deactivateAppointmentRule(${rule.id})" ${!rule.is_active ? 'disabled' : ''}>Deactivate</button>
            </div>
        </article>
    `).join('');

    renderDoctorPagination('appointmentRulesPagination', pageData, 'prevRulesPage()', 'nextRulesPage()');
}

function renderAppointmentSummary() {
    const box = document.getElementById('appointmentSummaryList');
    if (!box) return;

    if (!dashboardState.appointmentSummary.length) {
        box.innerHTML = `<p class="doctor-empty">No summary rows in this range.</p>`;
        renderDoctorPagination('appointmentSummaryPagination', { page: 1, totalPages: 1, totalRows: 0 }, 'prevSummaryPage()', 'nextSummaryPage()');
        return;
    }

    const pageData = paginateRows(dashboardState.appointmentSummary, dashboardState.pagination.summaryPage, dashboardState.pagination.pageSize);
    dashboardState.pagination.summaryPage = pageData.page;

    box.innerHTML = pageData.rows.map((row) => `
        <article class="doctor-list-item">
            <strong>${row.date} (${row.weekday})</strong>
            <div class="doctor-list-meta">Consultation window: ${consultationWindowLabel(row.consultation_window)}</div>
            <div class="doctor-list-meta">Pending: ${row.pending_count} | Approved: ${row.approved_count} | Total: ${row.total_count}</div>
            <div class="doctor-list-meta">Capacity: ${row.daily_capacity} | Remaining: ${row.remaining_capacity}</div>
        </article>
    `).join('');

    renderDoctorPagination('appointmentSummaryPagination', pageData, 'prevSummaryPage()', 'nextSummaryPage()');
}

function editAppointmentRule(ruleId) {
    const rule = dashboardState.appointmentRules.find((item) => Number(item.id) === Number(ruleId));
    if (!rule) return;

    document.getElementById('ruleEditId').value = String(rule.id);
    document.getElementById('ruleDayOfWeek').value = String(rule.day_of_week);
    document.getElementById('ruleStartTime').value = String(rule.start_time || '').slice(0, 5);
    document.getElementById('ruleEndTime').value = String(rule.end_time || '').slice(0, 5);
    document.getElementById('ruleDailyCapacity').value = String(rule.daily_capacity || 1);
}

function resetAppointmentRuleForm() {
    document.getElementById('ruleEditId').value = '';
    document.getElementById('ruleDayOfWeek').value = '0';
    document.getElementById('ruleStartTime').value = '09:00';
    document.getElementById('ruleEndTime').value = '13:00';
    document.getElementById('ruleDailyCapacity').value = '10';
}

async function doctorProfile() {
    const result = await call('/doctor/profile');
    if (result.status < 300 && typeof result.data === 'object' && result.data) {
        dashboardState.profile = result.data.doctor || result.data.profile || result.data.user || result.data;
        renderOverview();
    }
    write(result);
    return result;
}

async function doctorPatients() {
    const result = await call('/doctor/patients');
    if (result.status < 300) {
        dashboardState.patients = getArrayPayload(result, ['patients', 'data', 'items']);
        dashboardState.pagination.patientsPage = 1;
        renderOverview();
    }
    write(result);
    return result;
}

async function doctorAppointments() {
    const status = document.getElementById('appointmentStatusFilter').value.trim();
    const qs = status ? `?status=${encodeURIComponent(status)}` : '';
    const result = await call(`/doctor/appointments${qs}`);
    if (result.status < 300) {
        dashboardState.appointments = getArrayPayload(result, ['appointments', 'data', 'items']);
        dashboardState.pagination.appointmentsPage = 1;
        renderOverview();
    }
    write(result);
    return result;
}

async function doctorAppointmentRules() {
    const result = await call('/doctor/appointment-rules?activeOnly=0');
    if (result.status < 300) {
        dashboardState.appointmentRules = getArrayPayload(result, ['rules', 'data', 'items']);
        dashboardState.pagination.rulesPage = 1;
        renderAppointmentRules();
    }
    write(result);
    return result;
}

async function saveAppointmentRule() {
    const editId = getRuleEditId();
    const dayOfWeek = Number(document.getElementById('ruleDayOfWeek').value || 0);
    const startTime = document.getElementById('ruleStartTime').value;
    const endTime = document.getElementById('ruleEndTime').value;
    const dailyCapacity = Number(document.getElementById('ruleDailyCapacity').value || 0);

    if (!startTime || !endTime || dailyCapacity < 1) {
        write({ status: 422, data: { message: 'Provide weekday, consultation start/end time, and positive daily capacity.' } });
        return;
    }

    const body = {
        dayOfWeek,
        startTime,
        endTime,
        dailyCapacity,
    };

    const path = editId ? `/doctor/appointment-rules/${editId}` : '/doctor/appointment-rules';
    const method = editId ? 'PUT' : 'POST';
    const result = await call(path, method, body);
    write(result);

    if (result.status < 300) {
        resetAppointmentRuleForm();
        await doctorAppointmentRules();
        await doctorAppointmentSummary();
    }
}

async function deactivateAppointmentRule(ruleId) {
    const result = await call(`/doctor/appointment-rules/${ruleId}/deactivate`, 'POST');
    write(result);
    if (result.status < 300) {
        await doctorAppointmentRules();
        await doctorAppointmentSummary();
    }
}

async function doctorAppointmentSummary() {
    const fromEl = document.getElementById('summaryDateFrom');
    const toEl = document.getElementById('summaryDateTo');
    const query = new URLSearchParams();
    if (fromEl?.value) query.set('dateFrom', normalizeIsoDate(fromEl.value));
    if (toEl?.value) query.set('dateTo', normalizeIsoDate(toEl.value));
    const qs = query.toString() ? `?${query.toString()}` : '';

    const result = await call(`/doctor/appointments/summary${qs}`);
    if (result.status < 300) {
        dashboardState.appointmentSummary = getArrayPayload(result, ['by_date', 'rows', 'data']);
        dashboardState.pagination.summaryPage = 1;
        renderAppointmentSummary();

        if (fromEl && !fromEl.value && result.data?.date_from) fromEl.value = result.data.date_from;
        if (toEl && !toEl.value && result.data?.date_to) toEl.value = result.data.date_to;
    }
    write(result);
    return result;
}

async function doctorBedRequests() {
    const result = await call('/doctor/bed-requests');
    if (result.status < 300) {
        dashboardState.bedRequests = getArrayPayload(result, ['bed_requests', 'requests', 'bedRequests', 'data', 'items']);
        renderOverview();
    }
    write(result);
    return result;
}

async function cancelAppointment() {
    const id = Number(document.getElementById('cancelAppointmentId').value);
    const reason = document.getElementById('cancelReason').value.trim();

    if (!id) {
        write({ status: 422, data: { message: 'Select an appointment first.' } });
        return;
    }

    const body = reason ? { cancelReason: reason } : {};
    const result = await call(`/doctor/appointments/${id}/cancel`, 'POST', body);
    write(result);
    await doctorAppointments();
}

async function createBedRequest() {
    const patientId = Number(document.getElementById('patientUserIdSelect').value);
    const diagnosis = document.getElementById('diagnosis').value.trim();
    const careLevel = document.getElementById('careLevelRequested').value;
    const notes = document.getElementById('requestNotes').value.trim();

    if (!patientId || !diagnosis) {
        write({ status: 422, data: { message: 'Select a patient and enter a diagnosis.' } });
        return;
    }

    const body = {
        patientUserId: patientId,
        diagnosis: diagnosis,
        careLevelRequested: careLevel,
        notes: notes || null
    };

    const result = await call('/doctor/bed-requests', 'POST', body);
    write(result);
    if (result.status < 300) {
        document.getElementById('requestNotes').value = '';
        await doctorBedRequests();
    }
}

async function initDoctorDashboard() {
    if (window.lifeLinkShell) {
        doctorPanelControl = window.lifeLinkShell.initPanelNavigation({
            panelIds: doctorPanelIds,
            defaultPanel: 'doctor-overview',
        });
    }

    const defaultFrom = new Date();
    const defaultTo = new Date();
    defaultTo.setDate(defaultTo.getDate() + 14);
    const summaryFromEl = document.getElementById('summaryDateFrom');
    const summaryToEl = document.getElementById('summaryDateTo');
    if (summaryFromEl && !summaryFromEl.value) summaryFromEl.value = defaultFrom.toISOString().slice(0, 10);
    if (summaryToEl && !summaryToEl.value) summaryToEl.value = defaultTo.toISOString().slice(0, 10);

    const requests = await Promise.all([
        doctorProfile(),
        doctorPatients(),
        doctorAppointments(),
        doctorBedRequests(),
        doctorAppointmentRules(),
        doctorAppointmentSummary()
    ]);

    if (requests.some(item => item.status >= 300)) {
        write({
            status: 207,
            data: {
                message: 'Some doctor endpoints returned non-success responses. Check token or role scope.'
            }
        });
    }
}

function openDoctorPanel(panelId) {
    if (doctorPanelControl?.setActivePanel) {
        doctorPanelControl.setActivePanel(panelId, true);
    }
}

initDoctorDashboard();
</script>
@endpush
