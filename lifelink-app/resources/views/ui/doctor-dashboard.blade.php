@extends('ui.layouts.app')

@section('title', 'Doctor Dashboard')
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

    .doctor-quick-actions {
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.96);
        padding: 16px;
    }

    .doctor-quick-actions h3 {
        margin: 0 0 12px;
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
    <a class="is-active" href="#doctor-overview"><strong>Dashboard</strong></a>
    <a href="#doctor-appointments"><strong>My Appointments</strong></a>
    <a href="#doctor-patients"><strong>My Patients</strong></a>
    <a href="#doctor-request"><strong>Bed Requests</strong></a>
    <a href="#doctor-diagnostics"><strong>Diagnostics</strong></a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="doctor-shell">
        <section id="doctor-overview" class="doctor-head ll-section">
            <h2 id="doctorWelcome">Welcome, Doctor</h2>
            <p id="doctorMetaLine">Loading doctor profile...</p>
        </section>

        <section class="doctor-stats ll-section">
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
            <article id="doctor-appointments" class="doctor-card ll-section">
                <div class="doctor-card-head">
                    <h3>Today's Appointments</h3>
                    <button class="doctor-chip-btn" type="button" onclick="doctorAppointments()">Refresh</button>
                </div>
                <div id="appointmentsList" class="doctor-list"></div>
            </article>

            <article id="doctor-patients" class="doctor-card ll-section">
                <div class="doctor-card-head">
                    <h3>Active Patients</h3>
                    <button class="doctor-chip-btn" type="button" onclick="doctorPatients()">Refresh</button>
                </div>
                <div id="patientsList" class="doctor-list"></div>
            </article>
        </section>

        <section class="doctor-quick-actions ll-section">
            <h3>Quick Actions</h3>
            <div class="doctor-actions-row">
                <a class="doctor-action-btn" href="#doctor-request">Request Bed for Patient</a>
                <a class="doctor-action-btn" href="#doctor-appointments-workflow">Manage Appointments</a>
                <a class="doctor-action-btn" href="#doctor-patients">View All Patients</a>
            </div>
        </section>

        <section id="doctor-request" class="doctor-card doctor-workflow ll-section">
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

        <section id="doctor-appointments-workflow" class="doctor-card doctor-workflow ll-section">
            <div class="doctor-card-head">
                <h3>Appointment Controls</h3>
                <button class="doctor-chip-btn" type="button" onclick="doctorAppointments()">Reload appointments</button>
            </div>

            <div class="doctor-form-grid">
                <div class="doctor-field">
                    <label for="appointmentStatusFilter">Status filter</label>
                    <select id="appointmentStatusFilter">
                        <option value="">All appointment statuses</option>
                        <option value="Booked">Booked</option>
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

        <section id="doctor-diagnostics" class="doctor-card ll-section">
            <details class="ll-debug">
                <summary>API response diagnostics</summary>
                <pre id="out" class="doctor-debug"></pre>
            </details>
        </section>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const token = localStorage.getItem('USER_TOKEN') || '';

const dashboardState = {
    profile: null,
    patients: [],
    appointments: [],
    bedRequests: []
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
    document.getElementById('doctorWelcome').textContent = `Welcome, ${name}`;
    document.getElementById('doctorMetaLine').textContent = `${dept} - ${spec}`;

    renderAppointmentsList(todaysAppointments.length ? todaysAppointments : appointments.slice(0, 4));
    renderPatientsList(activePatients.length ? activePatients : patients.slice(0, 4));
    populatePatientPicker(patients);
    populateAppointmentPicker(appointments);
}

function renderAppointmentsList(items) {
    const box = document.getElementById('appointmentsList');
    if (!box) return;

    if (!items.length) {
        box.innerHTML = `<p class="doctor-empty">No appointments scheduled for this selection.</p>`;
        return;
    }

    box.innerHTML = items.map(item => {
        const date = item?.appointment_date || item?.appointmentDate || '-';
        const status = item?.status || '-';
        const patient = item?.patient_name || item?.patientName || item?.patient_full_name || `Patient #${item?.patient_user_id || item?.patientUserId || '-'}`;
        return `
            <article class="doctor-list-item">
                <strong>${patient}</strong>
                <div class="doctor-list-meta">${date} | Status: ${status}</div>
            </article>
        `;
    }).join('');
}

function renderPatientsList(items) {
    const box = document.getElementById('patientsList');
    if (!box) return;

    if (!items.length) {
        box.innerHTML = `<p class="doctor-empty">No active patients available.</p>`;
        return;
    }

    box.innerHTML = items.map(item => {
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

async function doctorProfile() {
    const result = await call('/doctor/profile');
    if (result.status < 300 && typeof result.data === 'object' && result.data) {
        dashboardState.profile = result.data.profile || result.data.user || result.data;
        renderOverview();
    }
    write(result);
    return result;
}

async function doctorPatients() {
    const result = await call('/doctor/patients');
    if (result.status < 300) {
        dashboardState.patients = getArrayPayload(result, ['patients', 'data', 'items']);
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
        renderOverview();
    }
    write(result);
    return result;
}

async function doctorBedRequests() {
    const result = await call('/doctor/bed-requests');
    if (result.status < 300) {
        dashboardState.bedRequests = getArrayPayload(result, ['requests', 'bedRequests', 'data', 'items']);
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
    const requests = await Promise.all([
        doctorProfile(),
        doctorPatients(),
        doctorAppointments(),
        doctorBedRequests()
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

initDoctorDashboard();
</script>
@endpush
