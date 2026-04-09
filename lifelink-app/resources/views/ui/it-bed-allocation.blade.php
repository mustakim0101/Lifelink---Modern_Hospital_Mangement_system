@extends('ui.layouts.app')

@section('title', 'IT Worker Dashboard')
@section('workspace_label', '')
@section('hero_badge', '')
@section('hero_title', 'IT Dashboard')
@section('hero_description', '')
@section('hide_meta_card', '1')
@section('meta_title', 'IT Workflow')
@section('meta_copy', 'Department assignment, ward setup, admissions, and beds')

@section('sidebar_nav')
    <a class="is-active" href="#it-overview" data-panel="it-overview" data-mode="all">
        <strong>Overview</strong>
    </a>
    <a href="#it-directory" data-panel="it-directory" data-mode="regular">
        <strong>Doctor + Patient Lookup</strong>
    </a>
    <a href="#it-admission" data-panel="it-admission" data-mode="regular">
        <strong>Admission + Bed Flow</strong>
    </a>
    <a href="#it-reference" data-panel="it-reference" data-mode="regular">
        <strong>Reference Tables</strong>
    </a>
    <a href="#it-blood-bank" data-panel="it-blood-bank" data-mode="blood">
        <strong>Blood Bank Operations</strong>
    </a>
    <a href="#it-debug" data-panel="it-debug" data-mode="all">
        <strong>API Response</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="it-grid">
        <div id="it-overview" class="it-split ll-section it-panel-switch" data-display="grid">
            <div class="it-panel it-col-4">
                <h3>IT worker session</h3>
                <p class="it-note">Use the logged-in IT worker token here.</p>
                <label class="it-label" for="tokenInput">IT worker token</label>
                <input id="tokenInput" class="it-input" placeholder="IT worker token">
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="useUserToken()">Use USER_TOKEN</button>
                    <button class="it-button primary" type="button" onclick="loadDepartmentsScope()">Reload my departments</button>
                </div>
            </div>

            <div class="it-panel it-col-4">
                <h3>Detected scope</h3>
                <p id="scopeModeSummary" class="it-note">Load your departments to open the correct IT workspace.</p>
            </div>

            <div class="it-panel it-col-4">
                <h3>Current totals</h3>
                <div class="it-summary">
                    <div class="it-stat"><small>Scoped depts</small><strong id="scopeCount">0</strong></div>
                    <div class="it-stat"><small>Care units</small><strong id="careUnitCount">0</strong></div>
                    <div class="it-stat"><small>Beds shown</small><strong id="bedCount">0</strong></div>
                    <div class="it-stat"><small>Admissions shown</small><strong id="admissionCount">0</strong></div>
                </div>
            </div>
        </div>

        <div id="it-blood-bank" class="it-panel it-panel-switch ll-section is-initially-hidden" data-display="block">
            <h3>Blood Bank operations</h3>
            <p class="it-note">Blood Bank IT mode for donor coordination, donation logging, and request fulfillment.</p>
            <div class="it-summary">
                <div class="it-stat"><small>Blood Bank access</small><strong id="bloodBankScopeStatus">Locked</strong></div>
                <div class="it-stat"><small>Blood Bank departments</small><strong id="bloodBankScopeCount">0</strong></div>
            </div>
            <div class="it-actions u-mt-4">
                <a class="it-button primary" href="/ui/blood-matching">Open Blood Matching Center</a>
                <a class="it-button soft" href="/ui/blood-matching#request-board">Open Request Board</a>
                <a class="it-button soft" href="/ui/blood-matching#selected-request-actions">Open Approval + Fulfillment</a>
                <a class="it-button soft" href="/ui/blood-matching#staff-donor-search">Open Donor Search</a>
                <a class="it-button soft" href="/ui/blood-matching#staff-donation-logging">Open Donation Logging</a>
                <a class="it-button soft" href="/ui/blood-bank-schema">Open Blood Bank Schema</a>
            </div>
        </div>

        <div id="regularItLocked" class="it-panel ll-section it-panel-switch" data-display="block">
            <h3>Regular IT operations</h3>
            <p id="regularItLockedMessage" class="it-note">Load your departments to open the correct IT workspace.</p>
        </div>

        <div id="standardItWorkArea">
        <div id="it-directory" class="it-split ll-section it-panel-switch" data-display="grid">
            <div class="it-panel it-col-6">
                <h3>Doctor lookup for admission context</h3>
                <p class="it-note">If admission should be tied to a doctor, search doctors here and use one of their ids in the admission form. The doctor must belong to the same department as the admission.</p>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="doctorSearchDepartmentId">Doctor department</label>
                        <select id="doctorSearchDepartmentId" class="it-select">
                            <option value="">All accessible departments</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="doctorSearchQuery">Doctor search</label>
                        <input id="doctorSearchQuery" class="it-input" placeholder="Doctor name or email">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="loadDoctors()">Load doctors</button>
                </div>
                <div id="doctorCards" class="it-card-grid"></div>
            </div>

            <div class="it-panel it-col-6">
                <h3>Patient directory</h3>
                <p class="it-note">Use this to find patient account ids before creating an admission. Patient accounts themselves are not fixed to a department; department belongs to the admission.</p>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="patientSearchDepartmentId">Current department filter</label>
                        <select id="patientSearchDepartmentId" class="it-select">
                            <option value="">All patients</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="patientSearchQuery">Patient search</label>
                        <input id="patientSearchQuery" class="it-input" placeholder="Patient name or email">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="loadPatients()">Load patients</button>
                </div>
                <div id="patientCards" class="it-card-grid"></div>
            </div>
        </div>

        <div id="it-admission" class="it-split ll-section it-panel-switch" data-display="grid">
            <div class="it-panel it-col-6">
                <h3>Ward setup</h3>
                <p class="it-note">Create care units and beds here instead of jumping to a separate prototype page.</p>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="wardDepartmentId">Department</label>
                        <select id="wardDepartmentId" class="it-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="unitType">Unit type</label>
                        <select id="unitType" class="it-select">
                            <option value="Ward">Ward</option>
                            <option value="ICU">ICU</option>
                            <option value="NICU">NICU</option>
                            <option value="CCU">CCU</option>
                        </select>
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="unitName">Unit name</label>
                        <input id="unitName" class="it-input" placeholder="Optional unit name">
                    </div>
                    <div>
                        <label class="it-label" for="floor">Floor</label>
                        <input id="floor" class="it-input" type="number" placeholder="Optional floor">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button primary" type="button" onclick="createCareUnit()">Create care unit</button>
                    <button class="it-button soft" type="button" onclick="listCareUnits()">List care units</button>
                </div>

                <div class="it-controls u-mt-4">
                    <div>
                        <label class="it-label" for="careUnitId">Care unit ID</label>
                        <input id="careUnitId" class="it-input" type="number" placeholder="Care unit ID">
                    </div>
                    <div>
                        <label class="it-label" for="bedCode">Bed code</label>
                        <input id="bedCode" class="it-input" placeholder="e.g. ICU-01">
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="bedStatus">Bed status</label>
                        <select id="bedStatus" class="it-select">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Reserved">Reserved</option>
                        </select>
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button accent" type="button" onclick="createBed()">Create bed</button>
                    <button class="it-button soft" type="button" onclick="listBeds()">List beds</button>
                </div>
            </div>

            <div class="it-panel it-col-6">
                <h3>Admission intake and allocation filters</h3>
                <p class="it-note">Start here for patient movement. "Create admission" opens a hospital stay for a patient inside a department. The filter area below then lets you view department admissions and available beds before you assign a bed.</p>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="patientUserId">Patient user ID</label>
                        <input id="patientUserId" class="it-input" type="number" placeholder="Patient user ID">
                    </div>
                    <div>
                        <label class="it-label" for="admissionDepartmentId">Admission department</label>
                        <select id="admissionDepartmentId" class="it-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="admittedByDoctorId">Admitted by doctor ID</label>
                        <input id="admittedByDoctorId" class="it-input" type="number" placeholder="Optional doctor ID from search">
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="careLevel">Care level requested</label>
                        <select id="careLevel" class="it-select">
                            <option value="Ward">Ward</option>
                            <option value="ICU">ICU</option>
                            <option value="NICU">NICU</option>
                            <option value="CCU">CCU</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="diagnosis">Diagnosis</label>
                        <input id="diagnosis" class="it-input" placeholder="Diagnosis">
                    </div>
                </div>
                <label class="it-label" for="admissionNotes">Admission notes</label>
                <textarea id="admissionNotes" class="it-textarea" placeholder="Optional notes"></textarea>
                <div class="it-actions">
                    <button class="it-button warm" type="button" onclick="createAdmission()">Create admission</button>
                </div>

                <div class="it-controls u-mt-4">
                    <div>
                        <label class="it-label" for="filterDepartmentId">Filter department</label>
                        <select id="filterDepartmentId" class="it-select">
                            <option value="">All accessible departments</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="filterStatus">Admission status</label>
                        <select id="filterStatus" class="it-select">
                            <option value="">All statuses</option>
                            <option value="Admitted">Admitted</option>
                            <option value="Discharged">Discharged</option>
                            <option value="Transferred">Transferred</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="listAdmissions()">Load admissions</button>
                    <button class="it-button soft" type="button" onclick="availableBeds()">Load available beds</button>
                    <button class="it-button soft" type="button" onclick="listDepartments()">Load departments</button>
                </div>
            </div>
        </div>

        <div class="it-split">
            <div class="it-panel it-col-5">
                <h3>Bed assignment actions</h3>
                <p class="it-note">Use this after you already created or loaded an admission and already loaded available beds. "Assign bed" links one selected bed to one selected admission. "Discharge and release bed" closes the admission and makes that bed available again.</p>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="assignAdmissionId">Admission ID</label>
                        <input id="assignAdmissionId" class="it-input" type="number" placeholder="Admission ID">
                    </div>
                    <div>
                        <label class="it-label" for="assignBedId">Bed ID</label>
                        <input id="assignBedId" class="it-input" type="number" placeholder="Bed ID">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button accent" type="button" onclick="assignBed()">Assign bed</button>
                </div>

                <div class="it-controls u-mt-4">
                    <div>
                        <label class="it-label" for="dischargeAdmissionId">Discharge admission ID</label>
                        <input id="dischargeAdmissionId" class="it-input" type="number" placeholder="Admission to discharge">
                    </div>
                    <div>
                        <label class="it-label" for="releaseReason">Release reason</label>
                        <input id="releaseReason" class="it-input" placeholder="Default: Discharge">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button danger" type="button" onclick="dischargeAdmission()">Discharge and release bed</button>
                </div>
            </div>

            <div class="it-panel it-col-7">
                <h3>Latest IDs and stored context</h3>
                <pre id="ctx" class="it-console"></pre>
            </div>
        </div>

        <div class="it-split">
            <div class="it-panel it-col-6">
                <h3>Admissions queue</h3>
                <p class="it-note">Recent admissions are shown as cards so the IT worker can pick ids visually instead of searching only in raw JSON.</p>
                <div id="admissionCards" class="it-card-grid"></div>
            </div>

            <div class="it-panel it-col-6">
                <h3>Available beds</h3>
                <p class="it-note">Use these bed cards to quickly copy a bed into the assignment form.</p>
                <div id="bedCards" class="it-card-grid"></div>
            </div>
        </div>

        <div id="it-reference" class="it-panel ll-section it-panel-switch" data-display="block">
            <h3>Reference tables</h3>
            <div class="it-table-grid">
                <div>
                    <p class="it-note">Care units</p>
                    <div class="it-table-wrap">
                        <table class="it-table">
                            <thead>
                                <tr><th>ID</th><th>Department</th><th>Type</th><th>Name</th><th>Floor</th></tr>
                            </thead>
                            <tbody id="careUnitsBody"></tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <p class="it-note">Beds</p>
                    <div class="it-table-wrap">
                        <table class="it-table">
                            <thead>
                                <tr><th>ID</th><th>Code</th><th>Status</th><th>Unit</th><th>Department</th></tr>
                            </thead>
                            <tbody id="bedsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div id="it-debug" class="it-panel ll-section it-panel-switch" data-display="block">
            <details class="ll-debug" open>
                <summary>API response</summary>
                <pre id="out" class="it-console"></pre>
            </details>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const ctx = document.getElementById('ctx');
const itPanelIds = ['it-overview', 'it-directory', 'it-admission', 'it-reference', 'it-blood-bank', 'it-debug'];
const itNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[data-panel]'));

const state = {
    activePanel: 'it-overview',
    scopeLoaded: false,
    departments: [],
    scopeDepartments: [],
    doctors: [],
    patients: [],
    admissions: [],
    beds: [],
    careUnits: [],
};

const BLOOD_BANK_DEPARTMENT = 'Blood Bank';

function allowedPanels() {
    if (!state.scopeLoaded) {
        return ['it-overview', 'it-debug'];
    }

    const blood = hasBloodBankScope();
    const regular = hasNonBloodBankScope();

    if (blood && !regular) {
        return ['it-overview', 'it-blood-bank', 'it-debug'];
    }

    if (blood && regular) {
        return ['it-overview', 'it-directory', 'it-admission', 'it-reference', 'it-blood-bank', 'it-debug'];
    }

    return ['it-overview', 'it-directory', 'it-admission', 'it-reference', 'it-debug'];
}

function updateSidebarByScope() {
    const allowed = allowedPanels();

    itNavLinks.forEach((link) => {
        const mode = link.dataset.mode || 'all';
        const panel = link.dataset.panel || '';
        const visible = mode === 'all'
            || (mode === 'regular' && allowed.includes(panel))
            || (mode === 'blood' && allowed.includes(panel));
        link.style.display = visible ? '' : 'none';
    });
}

function setActivePanel(panelId) {
    const allowed = allowedPanels();
    if (!allowed.includes(panelId)) {
        panelId = allowed[0];
    }

    state.activePanel = panelId;

    itPanelIds.forEach((id) => {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.style.display = id === panelId ? (panel.dataset.display || 'block') : 'none';
    });

    itNavLinks.forEach((link) => {
        const targetId = link.dataset.panel || '';
        link.classList.toggle('is-active', targetId === panelId);
    });

    renderDepartmentMode();
}

function setupSidebarPanelNav() {
    itNavLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const panelId = link.dataset.panel || '';
            if (!itPanelIds.includes(panelId)) return;
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        });
    });

    updateSidebarByScope();
    const initialHash = (window.location.hash || '').replace('#', '');
    const initialPanel = itPanelIds.includes(initialHash) ? initialHash : allowedPanels()[0];
    setActivePanel(initialPanel);
}

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function useUserToken() {
    document.getElementById('tokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

function selectedToken() {
    return document.getElementById('tokenInput').value.trim();
}

function refreshCtx() {
    ctx.textContent = JSON.stringify({
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        USER_TOKEN_PRESENT: !!localStorage.getItem('USER_TOKEN'),
        LAST_ADMISSION_ID: localStorage.getItem('LAST_ADMISSION_ID'),
        LAST_ASSIGNED_BED_ID: localStorage.getItem('LAST_ASSIGNED_BED_ID'),
        LAST_CARE_UNIT_ID: localStorage.getItem('LAST_CARE_UNIT_ID'),
        LAST_BED_ID: localStorage.getItem('LAST_BED_ID'),
        SCOPE_DEPARTMENTS: state.scopeDepartments.map((department) => ({ id: department.id, dept_name: department.dept_name })),
    }, null, 2);
}

async function call(path, method = 'GET', body = null) {
    const token = selectedToken();
    if (!token) return { status: 401, data: { message: 'Token missing in Auth Context.' } };

    const headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
    };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

async function publicDepartments() {
    const res = await fetch('/api/public/departments', { headers: { Accept: 'application/json' } });
    const text = await res.text();
    let data = {};
    try { data = JSON.parse(text); } catch {}
    return Array.isArray(data?.departments) ? data.departments : [];
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function setDepartmentOptions(selectId, departments, includeBlankLabel) {
    const select = document.getElementById(selectId);
    select.innerHTML = `<option value="">${includeBlankLabel}</option>${departments.map((department) => `
        <option value="${department.id}">${escapeHtml(department.dept_name)}</option>
    `).join('')}`;
}

function statusClass(status) {
    const normalized = String(status || '').toLowerCase();
    if (normalized === 'admitted') return 'admitted';
    if (normalized === 'discharged') return 'discharged';
    if (normalized === 'cancelled') return 'cancelled';
    if (normalized === 'transferred') return 'transferred';
    return 'default';
}

function syncCounters() {
    document.getElementById('scopeCount').textContent = String(state.scopeDepartments.length);
    document.getElementById('careUnitCount').textContent = String(state.careUnits.length);
    document.getElementById('bedCount').textContent = String(state.beds.length);
    document.getElementById('admissionCount').textContent = String(state.admissions.length);
}

function hasBloodBankScope() {
    return state.scopeDepartments.some((department) => String(department.dept_name || '').trim() === BLOOD_BANK_DEPARTMENT);
}

function hasNonBloodBankScope() {
    return state.scopeDepartments.some((department) => String(department.dept_name || '').trim() !== BLOOD_BANK_DEPARTMENT);
}

function renderDepartmentMode() {
    const bloodBankAccess = hasBloodBankScope();
    const regularAccess = hasNonBloodBankScope();
    const selectedPanel = state.activePanel || 'it-overview';
    const allowed = allowedPanels();
    const modeSummary = document.getElementById('scopeModeSummary');
    const regularLockedMessage = document.getElementById('regularItLockedMessage');

    if (!allowed.includes(selectedPanel)) {
        setActivePanel(allowed[0]);
        return;
    }

    document.getElementById('it-blood-bank').style.display = bloodBankAccess && selectedPanel === 'it-blood-bank' ? '' : 'none';
    document.getElementById('standardItWorkArea').style.display = regularAccess ? '' : 'none';
    document.getElementById('regularItLocked').style.display = regularAccess ? 'none' : '';
    document.getElementById('bloodBankScopeStatus').textContent = bloodBankAccess ? 'Enabled' : 'Locked';
    document.getElementById('bloodBankScopeCount').textContent = String(state.scopeDepartments.filter((department) => String(department.dept_name || '').trim() === BLOOD_BANK_DEPARTMENT).length);
    regularLockedMessage.textContent = !state.scopeLoaded
        ? 'Load your departments to open the correct IT workspace.'
        : bloodBankAccess
            ? 'This account is scoped only to Blood Bank, so ward and bed operations stay hidden.'
            : 'No non-Blood-Bank department scope was detected for this account.';
    modeSummary.textContent = !state.scopeLoaded
        ? 'Load your departments to open the correct IT workspace.'
        : bloodBankAccess && regularAccess
            ? 'Mixed IT scope is active: regular operations and Blood Bank operations are both available.'
            : bloodBankAccess
                ? 'Blood Bank IT mode is active for this account.'
                : 'Regular IT mode is active for this account.';
    updateSidebarByScope();
}

function renderDoctors() {
    const root = document.getElementById('doctorCards');
    if (!state.doctors.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No doctors loaded yet.</p></div>';
        return;
    }

    root.innerHTML = state.doctors.map((doctor) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(doctor.full_name || 'Unnamed doctor')}</h3>
                    <p class="it-note">${escapeHtml(doctor.email || '')}</p>
                </div>
                <span class="it-status default">${escapeHtml(doctor.department || 'Unknown department')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Doctor ID</small><strong>#${doctor.doctor_id}</strong></span>
            </div>
            <div class="it-actions">
                <button class="it-button accent" type="button" onclick="pickDoctor(${doctor.doctor_id}, ${doctor.department_id})">Use doctor</button>
            </div>
        </article>
    `).join('');
}

function renderPatientsDirectory() {
    const root = document.getElementById('patientCards');
    if (!state.patients.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No patients loaded yet.</p></div>';
        return;
    }

    root.innerHTML = state.patients.map((patient) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(patient.full_name || 'Unnamed patient')}</h3>
                    <p class="it-note">${escapeHtml(patient.email || '')}</p>
                </div>
                <span class="it-status default">${escapeHtml(patient.blood_group || 'Blood group not set')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Patient ID</small><strong>#${patient.patient_user_id}</strong></span>
            </div>
            <div class="it-actions">
                <button class="it-button accent" type="button" onclick="pickPatient(${patient.patient_user_id})">Use patient</button>
            </div>
        </article>
    `).join('');
}

function renderAdmissions() {
    const root = document.getElementById('admissionCards');
    if (!state.admissions.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No admissions loaded yet.</p></div>';
        return;
    }

    root.innerHTML = state.admissions.map((admission) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(admission.patient_name || 'Unknown patient')}</h3>
                    <p class="it-note">${escapeHtml(admission.department || 'Unknown department')}</p>
                </div>
                <span class="it-status ${statusClass(admission.status)}">${escapeHtml(admission.status || 'Unknown')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Admission</small><strong>#${admission.id}</strong></span>
                <span class="it-chip"><small>Care level</small><strong>${escapeHtml(admission.care_level_assigned || admission.care_level_requested || '-')}</strong></span>
                <span class="it-chip"><small>Bed</small><strong>${escapeHtml(admission.active_bed_assignment?.bed_code || 'Not assigned')}</strong></span>
            </div>
            <p class="it-note u-mt-3">${escapeHtml(admission.diagnosis || 'No diagnosis')}</p>
            <div class="it-actions">
                <button class="it-button soft" type="button" onclick="pickAdmission(${admission.id})">Use admission ID</button>
                <button class="it-button danger" type="button" onclick="prefillDischarge(${admission.id})">Prepare discharge</button>
            </div>
        </article>
    `).join('');
}

function renderBeds() {
    const root = document.getElementById('bedCards');
    if (!state.beds.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No available beds loaded yet.</p></div>';
        return;
    }

    root.innerHTML = state.beds.map((bed) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(bed.bed_code || 'Unnamed bed')}</h3>
                    <p class="it-note">${escapeHtml(bed.department || 'Unknown department')}</p>
                </div>
                <span class="it-status default">${escapeHtml(bed.status || 'Unknown')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Bed ID</small><strong>#${bed.id}</strong></span>
                <span class="it-chip"><small>Unit</small><strong>${escapeHtml(bed.unit_type || '-')}</strong></span>
                <span class="it-chip"><small>Floor</small><strong>${escapeHtml(bed.floor ?? '-')}</strong></span>
            </div>
            <div class="it-actions">
                <button class="it-button accent" type="button" onclick="pickBed(${bed.id})">Use bed ID</button>
            </div>
        </article>
    `).join('');
}

function renderCareUnitsTable() {
    const body = document.getElementById('careUnitsBody');
    body.innerHTML = state.careUnits.length
        ? state.careUnits.map((unit) => `
            <tr>
                <td>${unit.id ?? '-'}</td>
                <td>${escapeHtml(unit.department?.dept_name || unit.department || '-')}</td>
                <td>${escapeHtml(unit.unit_type || '-')}</td>
                <td>${escapeHtml(unit.unit_name || '-')}</td>
                <td>${escapeHtml(unit.floor ?? '-')}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No care units loaded.</td></tr>';
}

function renderBedsTable() {
    const body = document.getElementById('bedsBody');
    body.innerHTML = state.beds.length
        ? state.beds.map((bed) => `
            <tr>
                <td>${bed.id ?? '-'}</td>
                <td>${escapeHtml(bed.bed_code || '-')}</td>
                <td>${escapeHtml(bed.status || '-')}</td>
                <td>${escapeHtml(bed.unit_name || bed.unit_type || '-')}</td>
                <td>${escapeHtml(bed.department || '-')}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No beds loaded.</td></tr>';
}

function pickAdmission(admissionId) {
    document.getElementById('assignAdmissionId').value = String(admissionId);
}

function prefillDischarge(admissionId) {
    document.getElementById('dischargeAdmissionId').value = String(admissionId);
}

function pickBed(bedId) {
    document.getElementById('assignBedId').value = String(bedId);
}

function pickDoctor(doctorId, departmentId) {
    document.getElementById('admittedByDoctorId').value = String(doctorId);
    if (!document.getElementById('admissionDepartmentId').value && departmentId) {
        document.getElementById('admissionDepartmentId').value = String(departmentId);
    }
}

function pickPatient(patientId) {
    document.getElementById('patientUserId').value = String(patientId);
}

async function loadDepartmentSelectors() {
    state.departments = await publicDepartments();
    setDepartmentOptions('wardDepartmentId', state.departments, 'Select department');
    setDepartmentOptions('admissionDepartmentId', state.departments, 'Select department');
    setDepartmentOptions('filterDepartmentId', state.departments, 'All accessible departments');
    setDepartmentOptions('doctorSearchDepartmentId', state.departments, 'All accessible departments');
    setDepartmentOptions('patientSearchDepartmentId', state.departments, 'All patients');
}

async function listDepartments() {
    const result = await call('/ward/departments');
    write(result);
}

async function loadDepartmentsScope() {
    const result = await call('/ward/it/departments');
    write(result);
    state.scopeLoaded = true;
    if (result.status < 300) {
        state.scopeDepartments = Array.isArray(result.data?.departments) ? result.data.departments : [];
        syncCounters();
        refreshCtx();
    } else {
        state.scopeDepartments = [];
    }
    renderDepartmentMode();

    if (!allowedPanels().includes(state.activePanel) || state.activePanel === 'it-overview') {
        const initialHash = (window.location.hash || '').replace('#', '');
        const nextPanel = allowedPanels().includes(initialHash) ? initialHash : allowedPanels()[1] || allowedPanels()[0];
        setActivePanel(nextPanel);
        history.replaceState(null, '', `#${nextPanel}`);
    }

    if (result.status < 300 && hasNonBloodBankScope()) {
        await Promise.all([loadDoctors(), loadPatients()]);
    }
}

async function loadDoctors() {
    const departmentId = document.getElementById('doctorSearchDepartmentId').value.trim();
    const q = document.getElementById('doctorSearchQuery').value.trim();
    const params = new URLSearchParams();
    if (departmentId) params.set('departmentId', departmentId);
    if (q) params.set('q', q);
    const result = await call(`/ward/it/doctors${params.toString() ? `?${params.toString()}` : ''}`);
    write(result);
    if (result.status < 300) {
        state.doctors = Array.isArray(result.data?.doctors) ? result.data.doctors : [];
        renderDoctors();
    }
}

async function loadPatients() {
    const departmentId = document.getElementById('patientSearchDepartmentId').value.trim();
    const q = document.getElementById('patientSearchQuery').value.trim();
    const params = new URLSearchParams();
    if (departmentId) params.set('departmentId', departmentId);
    if (q) params.set('q', q);
    const result = await call(`/ward/it/patients${params.toString() ? `?${params.toString()}` : ''}`);
    write(result);
    if (result.status < 300) {
        state.patients = Array.isArray(result.data?.patients) ? result.data.patients : [];
        renderPatientsDirectory();
    }
}

async function createCareUnit() {
    const payload = {
        departmentId: Number(document.getElementById('wardDepartmentId').value),
        unitType: document.getElementById('unitType').value,
        unitName: document.getElementById('unitName').value.trim() || null,
        floor: document.getElementById('floor').value ? Number(document.getElementById('floor').value) : null,
    };
    const result = await call('/ward/care-units', 'POST', payload);
    write(result);
    const id = result.data?.care_unit?.id;
    if (id) {
        localStorage.setItem('LAST_CARE_UNIT_ID', String(id));
        document.getElementById('careUnitId').value = String(id);
        await listCareUnits();
    }
    refreshCtx();
}

async function listCareUnits() {
    const result = await call('/ward/care-units');
    write(result);
    if (result.status < 300) {
        state.careUnits = Array.isArray(result.data?.care_units) ? result.data.care_units : [];
        renderCareUnitsTable();
        syncCounters();
    }
}

async function createBed() {
    const payload = {
        careUnitId: Number(document.getElementById('careUnitId').value),
        bedCode: document.getElementById('bedCode').value.trim(),
        status: document.getElementById('bedStatus').value,
    };
    const result = await call('/ward/beds', 'POST', payload);
    write(result);
    const id = result.data?.bed?.id;
    if (id) {
        localStorage.setItem('LAST_BED_ID', String(id));
        await listBeds();
    }
    refreshCtx();
}

async function listBeds() {
    const result = await call('/ward/beds');
    write(result);
    if (result.status < 300) {
        state.beds = Array.isArray(result.data?.beds) ? result.data.beds : [];
        renderBedsTable();
        renderBeds();
        syncCounters();
    }
}

async function createAdmission() {
    const body = {
        patientUserId: Number(document.getElementById('patientUserId').value),
        departmentId: Number(document.getElementById('admissionDepartmentId').value),
        admittedByDoctorId: document.getElementById('admittedByDoctorId').value ? Number(document.getElementById('admittedByDoctorId').value) : null,
        diagnosis: document.getElementById('diagnosis').value.trim(),
        careLevelRequested: document.getElementById('careLevel').value,
        notes: document.getElementById('admissionNotes').value.trim() || null,
    };
    const result = await call('/ward/it/admissions', 'POST', body);
    write(result);
    const id = result.data?.admission?.id;
    if (id) {
        localStorage.setItem('LAST_ADMISSION_ID', String(id));
        document.getElementById('assignAdmissionId').value = String(id);
        await listAdmissions();
    }
    refreshCtx();
}

async function listAdmissions() {
    const departmentId = document.getElementById('filterDepartmentId').value.trim();
    const status = document.getElementById('filterStatus').value.trim();
    const params = new URLSearchParams();
    if (departmentId) params.set('departmentId', departmentId);
    if (status) params.set('status', status);
    const result = await call(`/ward/it/admissions${params.toString() ? `?${params.toString()}` : ''}`);
    write(result);
    if (result.status < 300) {
        state.admissions = Array.isArray(result.data?.admissions) ? result.data.admissions : [];
        renderAdmissions();
        syncCounters();
    }
}

async function availableBeds() {
    const departmentId = document.getElementById('filterDepartmentId').value.trim();
    const unitType = document.getElementById('careLevel').value;
    if (!departmentId) {
        write({ status: 422, data: { message: 'Set a filter department before loading available beds.' } });
        return;
    }
    const result = await call(`/ward/it/available-beds?departmentId=${encodeURIComponent(departmentId)}&unitType=${encodeURIComponent(unitType)}`);
    write(result);
    if (result.status < 300) {
        state.beds = Array.isArray(result.data?.beds) ? result.data.beds : [];
        renderBeds();
        renderBedsTable();
        syncCounters();
    }
}

async function assignBed() {
    const body = {
        admissionId: Number(document.getElementById('assignAdmissionId').value),
        bedId: Number(document.getElementById('assignBedId').value),
    };
    const result = await call('/ward/it/assign-bed', 'POST', body);
    write(result);
    const bedId = result.data?.admission?.active_bed_assignment?.bed_id;
    if (bedId) localStorage.setItem('LAST_ASSIGNED_BED_ID', String(bedId));
    const admissionId = result.data?.admission?.id;
    if (admissionId) document.getElementById('dischargeAdmissionId').value = String(admissionId);
    refreshCtx();
    await listAdmissions();
    if (document.getElementById('filterDepartmentId').value.trim()) await availableBeds();
}

async function dischargeAdmission() {
    const admissionId = Number(document.getElementById('dischargeAdmissionId').value);
    const releaseReason = document.getElementById('releaseReason').value.trim();
    if (!admissionId) {
        write({ status: 422, data: { message: 'Discharge admission id is required.' } });
        return;
    }
    const body = releaseReason ? { releaseReason } : {};
    const result = await call(`/ward/it/admissions/${admissionId}/discharge`, 'POST', body);
    write(result);
    refreshCtx();
    await listAdmissions();
    if (document.getElementById('filterDepartmentId').value.trim()) await availableBeds();
}

function initializeEmptyTables() {
    renderDoctors();
    renderPatientsDirectory();
    renderAdmissions();
    renderBeds();
    renderCareUnitsTable();
    renderBedsTable();
    syncCounters();
    refreshCtx();
    renderDepartmentMode();
}

async function bootItDashboard() {
    setupSidebarPanelNav();
    useUserToken();
    initializeEmptyTables();
    await loadDepartmentSelectors();

    if (selectedToken()) {
        await loadDepartmentsScope();
    } else {
        write('Login first or use USER_TOKEN so the IT dashboard can auto-load your department scope.');
    }
}

bootItDashboard();
</script>
@endpush
