/*
 This is done to ensure the last commit went through
*/
@extends('ui.layouts.app')

@section('title', 'Doctor Dashboard')
@section('workspace_label', '')
@section('hero_badge', '')
@section('hero_title', 'Doctor Dashboard')
@section('hero_description', '')
@section('hide_meta_card', '1')
@section('meta_title', 'Doctor Dashboard')
@section('meta_copy', 'Clinical actions, appointments, and admissions support')

@push('styles')
<style>
    :root {
        --doctor-ink: #172436;
        --doctor-muted: #5a6d7b;
        --doctor-line: rgba(23, 36, 54, 0.12);
        --doctor-card: rgba(255, 255, 255, 0.92);
        --doctor-primary: #1d4ed8;
        --doctor-primary-strong: #1e40af;
        --doctor-accent: #0f766e;
        --doctor-danger: #b91c1c;
        --doctor-shadow: 0 16px 36px rgba(18, 34, 50, 0.14);
    }

    .doctor-grid { display: grid; gap: 10px; }
    .doctor-panel { display: none; }
    .doctor-card {
        border: 1px solid var(--doctor-line);
        border-radius: 16px;
        background: var(--doctor-card);
        box-shadow: var(--doctor-shadow);
        padding: 14px;
    }

    .doctor-card h3 { margin: 0; }
    .doctor-hint { margin: 5px 0 0; color: var(--doctor-muted); font-size: 12px; }
    .doctor-split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .doctor-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }

    .doctor-label {
        display: block;
        margin: 0 0 5px;
        color: var(--doctor-muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .doctor-input,
    .doctor-select,
    .doctor-textarea {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(23, 36, 54, 0.18);
        background: rgba(255, 255, 255, 0.96);
        color: var(--doctor-ink);
        font: inherit;
        padding: 10px 11px;
        outline: none;
    }

    .doctor-input:focus,
    .doctor-select:focus,
    .doctor-textarea:focus {
        border-color: var(--doctor-primary);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.14);
    }

    .doctor-textarea { min-height: 88px; resize: vertical; }
    .doctor-btns { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }

    .doctor-btn {
        border: 0;
        border-radius: 10px;
        padding: 10px 12px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .doctor-btn-main { background: var(--doctor-primary); color: #fff; }
    .doctor-btn-main:hover { background: var(--doctor-primary-strong); }
    .doctor-btn-soft { background: rgba(23, 36, 54, 0.08); color: var(--doctor-ink); }
    .doctor-btn-accent { background: var(--doctor-accent); color: #fff; }

    .doctor-section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .doctor-response {
        margin: 0;
        min-height: 120px;
        max-height: 320px;
        overflow: auto;
        border-radius: 11px;
        border: 1px solid var(--doctor-line);
        background: #101c33;
        color: #d7e3ff;
        padding: 11px;
        font-size: 12px;
    }

    @media (max-width: 860px) {
        .doctor-split, .doctor-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#doctor-request">
        <strong>Bed Request</strong>
    </a>
    <a href="#doctor-actions">
        <strong>Actions</strong>
    </a>
    <a href="#doctor-debug">
        <strong>API Response</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="doctor-grid">
        <div id="doctor-request" class="doctor-split ll-section doctor-panel" data-display="grid">
            <div class="doctor-card">
                <h3>Doctor: create bed request</h3>
                <p class="doctor-hint">Submit an admission or bed request for a patient.</p>
                <label class="doctor-label" for="doctorTokenInput">Doctor token</label>
                <input id="doctorTokenInput" class="doctor-input" placeholder="doctor token for dashboard actions">
                <div class="doctor-btns">
                    <button class="doctor-btn doctor-btn-soft" type="button" onclick="useUserToken()">Use USER_TOKEN</button>
                    <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorProfile()">Doctor Profile</button>
                    <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorPatients()">Doctor Patients</button>
                    <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorBedRequests()">Bed Requests</button>
                </div>
                <div class="doctor-row">
                    <div>
                        <label class="doctor-label" for="patientUserId">Patient user id</label>
                        <input id="patientUserId" class="doctor-input" type="number" placeholder="patient user id">
                    </div>
                    <div>
                        <label class="doctor-label" for="careLevelRequested">Care level</label>
                        <select id="careLevelRequested" class="doctor-select">
                            <option value="Ward">Ward</option>
                            <option value="ICU">ICU</option>
                            <option value="NICU">NICU</option>
                            <option value="CCU">CCU</option>
                        </select>
                    </div>
                </div>
                <label class="doctor-label" for="diagnosis">Diagnosis</label>
                <input id="diagnosis" class="doctor-input" placeholder="diagnosis">
                <label class="doctor-label" for="requestNotes">Request notes</label>
                <textarea id="requestNotes" class="doctor-textarea" placeholder="notes (optional)"></textarea>
                <div class="doctor-btns">
                    <button class="doctor-btn doctor-btn-accent" type="button" onclick="createBedRequest()">Create Bed Request</button>
                </div>
            </div>
        </div>

        <div id="doctor-actions" class="doctor-card ll-section doctor-panel" data-display="block">
            <div class="doctor-section-title">
                <div>
                    <h3>Doctor actions</h3>
                    <p class="doctor-hint">Run doctor endpoints without leaving this page.</p>
                </div>
            </div>

            <div class="doctor-btns">
                <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorProfile()">GET /doctor/profile</button>
                <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorPatients()">GET /doctor/patients</button>
                <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorBedRequests()">GET /doctor/bed-requests</button>
            </div>

            <div class="doctor-row" style="margin-top:10px;">
                <div>
                    <label class="doctor-label" for="appointmentStatusFilter">Appointment status filter</label>
                    <select id="appointmentStatusFilter" class="doctor-select">
                        <option value="">All appointment statuses</option>
                        <option value="Booked">Booked</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Completed">Completed</option>
                        <option value="NoShow">NoShow</option>
                    </select>
                </div>
                <div style="display:flex; align-items:end;">
                    <button class="doctor-btn doctor-btn-soft" type="button" onclick="doctorAppointments()">GET /doctor/appointments</button>
                </div>
            </div>

            <div class="doctor-row" style="margin-top:10px;">
                <div>
                    <label class="doctor-label" for="cancelAppointmentId">Appointment id</label>
                    <input id="cancelAppointmentId" class="doctor-input" type="number" placeholder="appointment id">
                </div>
                <div>
                    <label class="doctor-label" for="cancelReason">Cancel reason</label>
                    <input id="cancelReason" class="doctor-input" placeholder="cancel reason (optional)">
                </div>
            </div>
            <div class="doctor-btns">
                <button class="doctor-btn doctor-btn-main" type="button" onclick="cancelAppointment()">Cancel Appointment</button>
            </div>
        </div>

        <div id="doctor-debug" class="doctor-card ll-section doctor-panel" data-display="block">
            <details class="ll-debug" open>
                <summary>API response</summary>
                <pre id="out" class="doctor-response"></pre>
            </details>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const doctorPanelIds = ['doctor-request', 'doctor-actions', 'doctor-debug'];
const doctorNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[href^="#doctor-"]'));

function setActivePanel(panelId) {
    doctorPanelIds.forEach((id) => {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.style.display = id === panelId ? (panel.dataset.display || 'block') : 'none';
    });

    doctorNavLinks.forEach((link) => {
        const targetId = (link.getAttribute('href') || '').replace('#', '');
        link.classList.toggle('is-active', targetId === panelId);
    });
}

function setupSidebarPanelNav() {
    doctorNavLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const panelId = (link.getAttribute('href') || '').replace('#', '');
            if (!doctorPanelIds.includes(panelId)) return;
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        });
    });

    const initialHash = (window.location.hash || '').replace('#', '');
    const initialPanel = doctorPanelIds.includes(initialHash) ? initialHash : doctorPanelIds[0];
    setActivePanel(initialPanel);
}

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function useUserToken() {
    document.getElementById('doctorTokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

async function call(path, method = 'GET', body = null, tokenKind = 'doctor') {
    const token = document.getElementById('doctorTokenInput').value.trim();

    if (!token) return { status: 401, data: { message: `${tokenKind} token missing` } };

    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
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

setupSidebarPanelNav();
useUserToken();
</script>
@endpush
