@extends('ui.layouts.app')

@section('title', 'Nurse Dashboard')
@section('workspace_label', '')
@section('hero_badge', '')
@section('hero_title', 'Nurse Dashboard')
@section('hero_description', '')
@section('hide_meta_card', '1')
@section('meta_title', 'Nurse Workflow')
@section('meta_copy', 'Department monitoring and bedside updates')

@section('sidebar_nav')
    <a class="is-active" href="#nurse-overview" data-panel="nurse-overview" data-mode="all">
        <strong>Overview</strong>
    </a>
    <a href="#nurse-monitoring" data-panel="nurse-monitoring" data-mode="regular">
        <strong>Patient Monitoring</strong>
    </a>
    <a href="#nurse-blood-bank" data-panel="nurse-blood-bank" data-mode="blood">
        <strong>Blood Bank Screening</strong>
    </a>
    <a href="#nurse-debug" data-panel="nurse-debug" data-mode="all">
        <strong>API Response</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="nurse-grid">
        <div id="nurse-overview" class="nurse-split ll-section nurse-panel-switch" data-display="grid">
            <div class="nurse-panel nurse-col-4">
                <h3>Nurse session</h3>
                <p class="nurse-note">Use the logged-in nurse token here.</p>
                <label class="nurse-label" for="nurseTokenInput">Nurse token</label>
                <input id="nurseTokenInput" class="nurse-input" placeholder="Bearer token for nurse">
                <div class="nurse-actions">
                    <button class="nurse-button soft" type="button" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-4">
                <h3>Current mode</h3>
                <p id="nurseModeSummary" class="nurse-note">Load your profile to unlock the correct nurse workspace.</p>
                <div class="nurse-actions">
                    <button class="nurse-button soft" type="button" onclick="loadNurseProfile()">Reload profile</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-4">
                <h3>Department filters</h3>
                <p class="nurse-note">Filter department admissions by status or search.</p>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="statusFilter">Admission status</label>
                        <select id="statusFilter" class="nurse-select">
                            <option value="">All</option>
                            <option value="Admitted">Admitted</option>
                            <option value="Discharged">Discharged</option>
                            <option value="Transferred">Transferred</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="nurse-label" for="queryFilter">Search</label>
                        <input id="queryFilter" class="nurse-input" placeholder="Name, email, diagnosis, bed code">
                    </div>
                </div>
                <div class="nurse-actions">
                    <button class="nurse-button primary" type="button" onclick="loadPatients()">Refresh patients</button>
                </div>
            </div>
        </div>

        <div id="nurse-monitoring" class="ll-section nurse-panel-switch" data-display="block">
            <div id="regularNurseLocked" class="nurse-panel nurse-mode-state">
                <h3>Patient monitoring</h3>
                <p id="regularNurseLockedMessage" class="nurse-note">Load your nurse profile to open the correct workflow.</p>
            </div>
            <div id="regularNurseSection" class="nurse-panel">
                <h3>Department snapshot</h3>
                <div class="nurse-stat-grid">
                    <div class="nurse-stat"><strong id="stTotal">0</strong><span>Total</span></div>
                    <div class="nurse-stat"><strong id="stActive">0</strong><span>Admitted</span></div>
                    <div class="nurse-stat"><strong id="stBed">0</strong><span>Has bed</span></div>
                    <div class="nurse-stat"><strong id="stNoBed">0</strong><span>No bed</span></div>
                    <div class="nurse-stat"><strong id="stMonitored">0</strong><span>Monitored 24h</span></div>
                </div>
            </div>

            <div id="regularNurseWorkArea" class="nurse-split u-mt-3">
                <div class="nurse-panel nurse-col-5">
                    <h3>Patient monitoring list</h3>
                    <p class="nurse-note">Select an admission to open monitoring detail, recent vitals, and linked records.</p>
                    <div id="patientList" class="nurse-list"></div>
                </div>

                <div class="nurse-panel nurse-col-7">
                <h3>Admission monitor</h3>
                <p class="nurse-note">Record vitals for the selected admission and review recent entries.</p>

                <div class="nurse-section-title">Selected admission</div>
                <div id="admissionSummary" class="nurse-summary-grid"></div>

                <div class="nurse-section-title">Log vital signs</div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="vAdmissionId">Admission ID</label>
                        <input id="vAdmissionId" class="nurse-input" type="number" placeholder="Auto-filled on selection">
                    </div>
                    <div>
                        <label class="nurse-label" for="vPatientUserId">Patient user ID</label>
                        <input id="vPatientUserId" class="nurse-input" type="number" placeholder="Auto-filled on selection">
                    </div>
                </div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="vTemp">Temperature (C)</label>
                        <input id="vTemp" class="nurse-input" type="number" step="0.1" placeholder="37.2">
                    </div>
                    <div>
                        <label class="nurse-label" for="vPulse">Pulse (bpm)</label>
                        <input id="vPulse" class="nurse-input" type="number" placeholder="76">
                    </div>
                </div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="vSys">Systolic BP</label>
                        <input id="vSys" class="nurse-input" type="number" placeholder="120">
                    </div>
                    <div>
                        <label class="nurse-label" for="vDia">Diastolic BP</label>
                        <input id="vDia" class="nurse-input" type="number" placeholder="80">
                    </div>
                </div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="vResp">Respiration</label>
                        <input id="vResp" class="nurse-input" type="number" placeholder="16">
                    </div>
                    <div>
                        <label class="nurse-label" for="vSpo2">SpO2 (%)</label>
                        <input id="vSpo2" class="nurse-input" type="number" placeholder="98">
                    </div>
                </div>
                <label class="nurse-label" for="vNote">Note</label>
                <textarea id="vNote" class="nurse-textarea" placeholder="Optional notes for this vital-sign entry"></textarea>
                <div class="nurse-actions">
                    <button class="nurse-button warm" type="button" onclick="logVitals()">Save vital signs</button>
                    <button class="nurse-button soft" type="button" onclick="loadSelectedAdmissionVitals()">Refresh vitals</button>
                </div>

                <div class="nurse-section-title">Recent vitals</div>
                <div class="nurse-table-wrap">
                    <table class="nurse-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Temp</th>
                                <th>Pulse</th>
                                <th>BP</th>
                                <th>Resp</th>
                                <th>SpO2</th>
                                <th>Nurse</th>
                            </tr>
                        </thead>
                        <tbody id="vitalsBody"></tbody>
                    </table>
                </div>

                <div class="nurse-section-title">Recent medical records</div>
                <div class="nurse-table-wrap">
                    <table class="nurse-table">
                        <thead>
                            <tr>
                                <th>Datetime</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                                <th>Created by</th>
                            </tr>
                        </thead>
                        <tbody id="recordsBody"></tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <div id="nurse-blood-bank" class="nurse-panel ll-section nurse-panel-switch" data-display="block">
            <h3>Blood Bank donor screening</h3>
            <p class="nurse-note">Blood Bank-only donor screening workflow for nurse-owned health checks and eligibility updates.</p>

            <div id="bloodBankLocked" class="nurse-note">Load your nurse profile first to see whether Blood Bank donor screening is available for this account.</div>
            <div id="bloodBankSection" hidden>
                <div class="nurse-summary-grid">
                    <div class="nurse-summary"><small>Step 1</small><strong>Search and select donor</strong></div>
                    <div class="nurse-summary"><small>Step 2</small><strong>Review latest screening history</strong></div>
                    <div class="nurse-summary"><small>Step 3</small><strong>Log current health check</strong></div>
                    <div class="nurse-summary"><small>Step 4</small><strong>Confirm backend eligibility result</strong></div>
                </div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="bbDonorQuery">Search donor</label>
                        <input id="bbDonorQuery" class="nurse-input" placeholder="Donor name, email, id, blood group">
                    </div>
                    <div>
                        <label class="nurse-label" for="bbRequestId">Filter by request ID</label>
                        <input id="bbRequestId" class="nurse-input" type="number" min="1" placeholder="Optional accepted request id">
                    </div>
                </div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="bbBloodGroupFilter">Blood group</label>
                        <select id="bbBloodGroupFilter" class="nurse-select">
                            <option value="">All groups</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div>
                        <label class="nurse-label" for="bbEligibilityFilter">Eligibility</label>
                        <select id="bbEligibilityFilter" class="nurse-select">
                            <option value="">All donors</option>
                            <option value="true">Eligible</option>
                            <option value="false">Not eligible</option>
                        </select>
                    </div>
                </div>
                <div class="nurse-actions">
                    <button class="nurse-button primary" type="button" onclick="loadBloodBankDonors()">Load donors</button>
                    <button class="nurse-button soft" type="button" onclick="loadSelectedDonorHealthChecks()">Refresh selected donor history</button>
                </div>

                <div class="nurse-split u-mt-3">
                    <div class="nurse-panel nurse-col-5">
                        <h3>Blood Bank donor list</h3>
                        <div id="bloodBankDonorList" class="nurse-list"></div>
                    </div>

                    <div class="nurse-panel nurse-col-7">
                        <h3>Donor health check entry</h3>
                        <div id="bbSelectedDonor" class="nurse-summary-grid">
                            <div class="nurse-note">Select a donor card from the left list.</div>
                        </div>
                        <div class="nurse-section-title">Latest screening status</div>
                        <div id="bbSelectedDonorStatus" class="nurse-summary-grid">
                            <div class="nurse-note">Latest donor eligibility and last screening details will appear here.</div>
                        </div>
                        <div class="nurse-control-grid">
                            <div>
                                <label class="nurse-label" for="bbDonorId">Donor ID</label>
                                <input id="bbDonorId" class="nurse-input" type="number" placeholder="Auto-filled on selection">
                            </div>
                            <div>
                                <label class="nurse-label" for="bbCheckDateTime">Check datetime</label>
                                <input id="bbCheckDateTime" class="nurse-input" type="datetime-local">
                            </div>
                        </div>
                        <div class="nurse-control-grid">
                            <div>
                                <label class="nurse-label" for="bbWeightKg">Weight (kg)</label>
                                <input id="bbWeightKg" class="nurse-input" type="number" step="0.1" value="60">
                            </div>
                            <div>
                                <label class="nurse-label" for="bbTemperatureC">Temperature (C)</label>
                                <input id="bbTemperatureC" class="nurse-input" type="number" step="0.1" value="36.8">
                            </div>
                        </div>
                        <div class="nurse-control-grid">
                            <div>
                                <label class="nurse-label" for="bbHemoglobin">Hemoglobin</label>
                                <input id="bbHemoglobin" class="nurse-input" type="number" step="0.1" placeholder="13.5">
                            </div>
                            <div>
                                <label class="nurse-label">Eligibility result</label>
                                <div id="bbEligibilityResult" class="nurse-pill bed">Waiting for staff entry</div>
                                <p id="bbEligibilityReason" class="nurse-note u-mt-1">Backend evaluation reason will appear after save.</p>
                            </div>
                        </div>
                        <label class="nurse-label" for="bbHealthNote">Health check note</label>
                        <textarea id="bbHealthNote" class="nurse-textarea" placeholder="Blood Bank nurse screening note"></textarea>
                        <div class="nurse-actions">
                            <button class="nurse-button warm" type="button" onclick="logBloodBankHealthCheck()">Save donor health check</button>
                        </div>

                        <div class="nurse-section-title">Recent donor health checks</div>
                        <div class="nurse-table-wrap">
                            <table class="nurse-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Weight</th>
                                        <th>Temp</th>
                                        <th>Hb</th>
                                        <th>Checked by</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody id="bbHealthChecksBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="nurse-debug" class="nurse-panel ll-section nurse-panel-switch" data-display="block">
            <details class="ll-debug" open>
                <summary>API response log</summary>
                <pre id="out" class="nurse-console"></pre>
            </details>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const nursePanelIds = ['nurse-overview', 'nurse-monitoring', 'nurse-blood-bank', 'nurse-debug'];
const nurseNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[data-panel]'));

const state = {
    activePanel: 'nurse-overview',
    nurse: null,
    nurseProfileLoaded: false,
    profileRequested: false,
    patients: [],
    patientsLoaded: false,
    patientsQueryKey: null,
    selectedAdmissionId: null,
    selectedPatientUserId: null,
    selectedDetail: null,
    bloodBankDonors: [],
    bloodBankDonorsLoaded: false,
    bloodBankDonorsQueryKey: null,
    selectedDonorId: null,
    donorHealthChecksByDonor: {},
    inFlight: {
        profile: null,
        patients: null,
        patientsQueryKey: null,
        donors: null,
        donorsQueryKey: null,
        donorChecks: null,
        donorChecksDonorId: null,
    },
};

function normalizeDepartmentName(value) {
    if (!value) return '';
    if (typeof value === 'string') return value.trim().toLowerCase();
    if (typeof value === 'object') {
        return String(
            value.dept_name
            || value.department
            || value.department_name
            || value.name
            || ''
        ).trim().toLowerCase();
    }
    return String(value).trim().toLowerCase();
}

function isBloodBankNurse() {
    return normalizeDepartmentName(
        state.nurse?.department
        || state.nurse?.department_name
        || state.nurse?.dept_name
    ) === 'blood bank';
}

function allowedNursePanels() {
    if (!state.nurseProfileLoaded || !state.nurse) {
        return ['nurse-overview', 'nurse-debug'];
    }

    if (isBloodBankNurse()) {
        return ['nurse-overview', 'nurse-blood-bank', 'nurse-debug'];
    }

    return ['nurse-overview', 'nurse-monitoring', 'nurse-debug'];
}

function preferredNursePanel() {
    const allowed = allowedNursePanels();
    return allowed.find((panelId) => panelId !== 'nurse-overview' && panelId !== 'nurse-debug') || allowed[0];
}

function updateNurseSidebarByMode() {
    const allowed = allowedNursePanels();
    nurseNavLinks.forEach((link) => {
        const panelId = link.dataset.panel || '';
        link.style.display = allowed.includes(panelId) ? '' : 'none';
    });
}

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function setVisibility(elementId, visible, displayValue = 'block') {
    const element = document.getElementById(elementId);
    if (!element) return;
    element.hidden = !visible;
    element.style.display = visible ? displayValue : 'none';
}

function setActivePanel(panelId) {
    const allowed = allowedNursePanels();
    if (!allowed.includes(panelId)) {
        panelId = preferredNursePanel();
    }

    state.activePanel = panelId;

    nursePanelIds.forEach((id) => {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.style.display = id === panelId ? (panel.dataset.display || 'block') : 'none';
    });

    nurseNavLinks.forEach((link) => {
        const targetId = (link.getAttribute('href') || '').replace('#', '');
        link.classList.toggle('is-active', targetId === panelId);
    });

    maybeLoadPanelData(panelId).catch((error) => {
        write({ message: 'Panel lazy-load failed', error: String(error) });
    });
}

function setupSidebarPanelNav() {
    nurseNavLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const panelId = link.dataset.panel || '';
            if (!nursePanelIds.includes(panelId)) return;
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        });
    });

    const initialHash = (window.location.hash || '').replace('#', '');
    updateNurseSidebarByMode();
    const initialPanel = nursePanelIds.includes(initialHash) ? initialHash : preferredNursePanel();
    setActivePanel(initialPanel);
}

async function maybeLoadPanelData(panelId) {
    if (!document.getElementById('nurseTokenInput').value.trim()) {
        return;
    }

    await loadNurseProfile({ force: false });

    if (panelId === 'nurse-monitoring' && !isBloodBankNurse()) {
        await loadPatients({ force: false });
        return;
    }

    if (panelId === 'nurse-blood-bank' && isBloodBankNurse()) {
        await loadBloodBankDonors({ force: false });
    }
}

function useStoredUserToken() {
    document.getElementById('nurseTokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

function buildUrl(path, query = null) {
    if (!query) return `${API}${path}`;
    const qs = new URLSearchParams(query);
    const stringQuery = qs.toString();
    return stringQuery ? `${API}${path}?${stringQuery}` : `${API}${path}`;
}

async function call(path, method = 'GET', body = null, tokenType = 'nurse', query = null) {
    const token = document.getElementById('nurseTokenInput').value.trim();

    if (!token) {
        return { status: 401, data: { message: `${tokenType} token missing` } };
    }

    const headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
    };

    const res = await fetch(buildUrl(path, query), {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined,
    });

    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
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

function renderStats(stats = null) {
    document.getElementById('stTotal').textContent = stats?.total_admissions ?? 0;
    document.getElementById('stActive').textContent = stats?.active_admissions ?? 0;
    document.getElementById('stBed').textContent = stats?.with_bed_assignment ?? 0;
    document.getElementById('stNoBed').textContent = stats?.without_bed_assignment ?? 0;
    document.getElementById('stMonitored').textContent = stats?.monitored_last_24h ?? 0;
}

function badgeForStatus(status) {
    return status === 'Admitted'
        ? '<span class="nurse-pill live">Admitted</span>'
        : `<span class="nurse-pill off">${escapeHtml(status || 'Unknown')}</span>`;
}

function renderPatients() {
    const holder = document.getElementById('patientList');
    if (!state.patients.length) {
        holder.innerHTML = '<div class="nurse-note">No admissions found for this filter.</div>';
        return;
    }

    holder.innerHTML = state.patients.map((patient) => {
        const isActive = Number(state.selectedAdmissionId) === Number(patient.id) ? 'is-active' : '';
        const bed = patient.active_bed_assignment
            ? `<span class="nurse-pill bed">${escapeHtml(patient.active_bed_assignment.bed_code || 'Assigned')}</span>`
            : '<span class="nurse-mini">No bed assigned</span>';

        const latest = patient.latest_vital_sign
            ? `<span class="nurse-mini">Last vitals: ${new Date(patient.latest_vital_sign.measured_at).toLocaleString()}</span>`
            : '<span class="nurse-mini">No vitals yet</span>';

        return `
            <article class="nurse-list-item ${isActive}" onclick="selectAdmission(${Number(patient.id)}, ${Number(patient.patient_user_id)})">
                <div class="nurse-item-head">
                    <strong>${escapeHtml(patient.patient_name || 'Unknown patient')}</strong>
                    ${badgeForStatus(patient.status)}
                </div>
                <p class="nurse-note">${escapeHtml(patient.diagnosis || 'No diagnosis')}</p>
                <div class="nurse-item-meta">
                    <span class="nurse-mini">Admission #${Number(patient.id)}</span>
                    <span class="nurse-mini">${escapeHtml(patient.care_level_assigned || patient.care_level_requested || 'Care TBD')}</span>
                    ${bed}
                </div>
                <div class="nurse-item-meta u-mt-1">${latest}</div>
            </article>
        `;
    }).join('');
}

function renderAdmissionSummary(admission) {
    const root = document.getElementById('admissionSummary');
    if (!admission) {
        root.innerHTML = '<div class="nurse-note">Select an admission from the left panel.</div>';
        return;
    }

    root.innerHTML = `
        <div class="nurse-summary"><small>Patient</small><strong>${escapeHtml(admission.patient_name || '-')}</strong></div>
        <div class="nurse-summary"><small>Blood group</small><strong>${escapeHtml(admission.blood_group || '-')}</strong></div>
        <div class="nurse-summary"><small>Department</small><strong>${escapeHtml(admission.department || '-')}</strong></div>
        <div class="nurse-summary"><small>Status</small><strong>${escapeHtml(admission.status || '-')}</strong></div>
        <div class="nurse-summary"><small>Diagnosis</small><strong>${escapeHtml(admission.diagnosis || '-')}</strong></div>
        <div class="nurse-summary"><small>Bed</small><strong>${escapeHtml(admission.active_bed_assignment?.bed_code || 'Not assigned')}</strong></div>
        <div class="nurse-summary"><small>Unit</small><strong>${escapeHtml(admission.active_bed_assignment?.unit_type || admission.care_level_requested || '-')}</strong></div>
        <div class="nurse-summary"><small>Admit date</small><strong>${admission.admit_date ? new Date(admission.admit_date).toLocaleString() : '-'}</strong></div>
    `;
}

function renderVitals(vitals = []) {
    const body = document.getElementById('vitalsBody');
    if (!vitals.length) {
        body.innerHTML = '<tr><td colspan="7">No vital records yet.</td></tr>';
        return;
    }

    body.innerHTML = vitals.map((vital) => `
        <tr>
            <td>${vital.measured_at ? new Date(vital.measured_at).toLocaleString() : '-'}</td>
            <td>${vital.temperature_c ?? '-'}</td>
            <td>${vital.pulse_bpm ?? '-'}</td>
            <td>${vital.systolic_bp && vital.diastolic_bp ? `${vital.systolic_bp}/${vital.diastolic_bp}` : '-'}</td>
            <td>${vital.respiration_rate ?? '-'}</td>
            <td>${vital.spo2_percent ?? '-'}</td>
            <td>${escapeHtml(vital.nurse_name || '-')}</td>
        </tr>
    `).join('');
}

function renderRecords(records = []) {
    const body = document.getElementById('recordsBody');
    if (!records.length) {
        body.innerHTML = '<tr><td colspan="4">No recent records.</td></tr>';
        return;
    }

    body.innerHTML = records.map((record) => `
        <tr>
            <td>${record.record_datetime ? new Date(record.record_datetime).toLocaleString() : '-'}</td>
            <td>${escapeHtml(record.diagnosis || '-')}</td>
            <td>${escapeHtml(record.treatment_plan || '-')}</td>
            <td>${escapeHtml(record.created_by || '-')}</td>
        </tr>
    `).join('');
}

function renderBloodBankAccess() {
    const profileLoaded = state.nurseProfileLoaded;
    const isBloodBank = isBloodBankNurse();
    const hasRegularMode = profileLoaded && state.nurse && !isBloodBank;
    const regularLockedMessage = document.getElementById('regularNurseLockedMessage');
    const modeSummary = document.getElementById('nurseModeSummary');
    const bloodBankLocked = document.getElementById('bloodBankLocked');
    const bloodBankVisible = profileLoaded && isBloodBank;

    setVisibility('regularNurseLocked', !hasRegularMode, 'block');
    setVisibility('regularNurseSection', hasRegularMode, 'block');
    setVisibility('regularNurseWorkArea', hasRegularMode, 'grid');
    setVisibility('bloodBankLocked', !bloodBankVisible, 'block');
    setVisibility('bloodBankSection', bloodBankVisible, 'block');
    regularLockedMessage.textContent = !profileLoaded
        ? 'Load your nurse profile to open the correct workflow.'
        : isBloodBank
            ? 'This nurse account is in Blood Bank mode, so regular patient monitoring is hidden.'
            : 'Regular patient monitoring is unavailable until nurse setup is complete.';
    bloodBankLocked.textContent = !profileLoaded
        ? 'Load your nurse profile first to see whether Blood Bank donor screening is available for this account.'
        : 'Blood Bank donor screening is available only when the nurse profile belongs to the Blood Bank department.';
    modeSummary.textContent = !profileLoaded
        ? 'Load your profile to unlock the correct nurse workspace.'
        : isBloodBank
            ? 'Blood Bank nurse mode is active for this account.'
            : `Regular nurse mode is active for ${state.nurse?.department || 'this department'}.`;
    updateNurseSidebarByMode();
}

function renderBloodBankDonors() {
    const holder = document.getElementById('bloodBankDonorList');
    if (!state.bloodBankDonors.length) {
        holder.innerHTML = '<div class="nurse-note">No Blood Bank donors found for this filter.</div>';
        return;
    }

    holder.innerHTML = state.bloodBankDonors.map((donor) => `
        <article class="nurse-list-item ${Number(state.selectedDonorId) === Number(donor.donor_id) ? 'is-active' : ''}" onclick="selectBloodBankDonor(${Number(donor.donor_id)})">
            <div class="nurse-item-head">
                <strong>${escapeHtml(donor.full_name || 'Unknown donor')}</strong>
                <span class="nurse-pill ${donor.is_eligible ? 'live' : 'off'}">${donor.is_eligible ? 'Eligible' : 'Not Eligible'}</span>
            </div>
            <p class="nurse-note">${escapeHtml(donor.email || '-')}</p>
            <div class="nurse-item-meta">
                <span class="nurse-mini">Donor #${Number(donor.donor_id)}</span>
                <span class="nurse-mini">${escapeHtml(donor.blood_group || '-')}</span>
            </div>
            <div class="nurse-item-meta u-mt-1">
                <span class="nurse-mini">Latest check: ${donor.latest_health_check?.check_datetime ? new Date(donor.latest_health_check.check_datetime).toLocaleString() : 'None'}</span>
            </div>
        </article>
    `).join('');
}

function renderSelectedBloodBankDonor(donor = null) {
    const root = document.getElementById('bbSelectedDonor');
    if (!donor) {
        root.innerHTML = '<div class="nurse-note">Select a donor card from the left list.</div>';
        document.getElementById('bbDonorId').value = '';
        renderSelectedBloodBankDonorStatus(null);
        resetBloodBankEligibilityFeedback();
        return;
    }

    document.getElementById('bbDonorId').value = String(donor.donor_id);
    root.innerHTML = `
        <div class="nurse-summary"><small>Donor</small><strong>${escapeHtml(donor.full_name || '-')}</strong></div>
        <div class="nurse-summary"><small>Email</small><strong>${escapeHtml(donor.email || '-')}</strong></div>
        <div class="nurse-summary"><small>Donor ID</small><strong>#${Number(donor.donor_id)}</strong></div>
        <div class="nurse-summary"><small>Blood group</small><strong>${escapeHtml(donor.blood_group || '-')}</strong></div>
    `;
    renderSelectedBloodBankDonorStatus(donor);
    const latestCheck = donor.latest_health_check || null;
    if (latestCheck) {
        applyEligibilityFeedback({
            is_eligible: !!donor.is_eligible,
            reason: latestCheck.notes || 'Latest Blood Bank nurse screening loaded.',
        });
    } else {
        resetBloodBankEligibilityFeedback();
    }
}

function renderBloodBankHealthChecks(checks = []) {
    const body = document.getElementById('bbHealthChecksBody');
    if (!checks.length) {
        body.innerHTML = '<tr><td colspan="6">No donor health checks yet.</td></tr>';
        return;
    }

    body.innerHTML = checks.map((check) => `
        <tr>
            <td>${check.check_datetime ? new Date(check.check_datetime).toLocaleString() : '-'}</td>
            <td>${check.weight_kg ?? '-'}</td>
            <td>${check.temperature_c ?? '-'}</td>
            <td>${check.hemoglobin ?? '-'}</td>
            <td>${escapeHtml(check.checked_by_name || '-')}</td>
            <td>${escapeHtml(check.notes || '-')}</td>
        </tr>
    `).join('');
}

function renderSelectedBloodBankDonorStatus(donor = null) {
    const root = document.getElementById('bbSelectedDonorStatus');
    if (!root) return;

    if (!donor) {
        root.innerHTML = '<div class="nurse-note">Latest donor eligibility and last screening details will appear here.</div>';
        return;
    }

    const latestCheck = donor.latest_health_check || null;
    root.innerHTML = `
        <div class="nurse-summary"><small>Eligibility</small><strong>${donor.is_eligible ? 'Eligible' : 'Not Eligible'}</strong></div>
        <div class="nurse-summary"><small>Last donation</small><strong>${donor.last_donation_date ? new Date(donor.last_donation_date).toLocaleDateString() : 'No donation logged'}</strong></div>
        <div class="nurse-summary"><small>Latest check</small><strong>${latestCheck?.check_datetime ? new Date(latestCheck.check_datetime).toLocaleString() : 'No screening yet'}</strong></div>
        <div class="nurse-summary"><small>Latest note</small><strong>${escapeHtml(latestCheck?.notes || 'No screening note yet')}</strong></div>
    `;
}

function resetBloodBankEligibilityFeedback() {
    const result = document.getElementById('bbEligibilityResult');
    const reason = document.getElementById('bbEligibilityReason');
    result.className = 'nurse-pill bed';
    result.textContent = 'Waiting for staff entry';
    reason.textContent = 'Backend evaluation reason will appear after save.';
}

function applyEligibilityFeedback(eligibility = null) {
    const result = document.getElementById('bbEligibilityResult');
    const reason = document.getElementById('bbEligibilityReason');

    if (!eligibility || typeof eligibility.is_eligible !== 'boolean') {
        resetBloodBankEligibilityFeedback();
        return;
    }

    result.className = `nurse-pill ${eligibility.is_eligible ? 'live' : 'off'}`;
    result.textContent = eligibility.is_eligible ? 'Eligible by backend' : 'Not eligible by backend';
    reason.textContent = eligibility.reason || 'Eligibility evaluated by the Blood Bank nurse workflow.';
}

async function loadNurseProfile(options = {}) {
    const force = options.force !== false;
    if (!force && state.nurseProfileLoaded) {
        return state.nurse;
    }
    if (state.inFlight.profile) {
        return state.inFlight.profile;
    }

    state.inFlight.profile = (async () => {
        const result = await call('/nurse/profile');
        state.profileRequested = true;
        state.nurseProfileLoaded = true;
        if (result.status < 300 && result.data?.nurse) {
            state.nurse = result.data.nurse;
        } else {
            state.nurse = null;
            state.patientsLoaded = false;
            state.bloodBankDonorsLoaded = false;
        }
        renderBloodBankAccess();
        if (!allowedNursePanels().includes(state.activePanel) || state.activePanel === 'nurse-overview') {
            const panelId = preferredNursePanel();
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        }
        write(result);
        return state.nurse;
    })();

    try {
        return await state.inFlight.profile;
    } finally {
        state.inFlight.profile = null;
    }
}

async function loadPatients(options = {}) {
    const force = options.force !== false;
    const status = document.getElementById('statusFilter').value.trim();
    const queryValue = document.getElementById('queryFilter').value.trim();
    const query = {};
    if (status) query.status = status;
    if (queryValue) query.q = queryValue;
    const queryKey = JSON.stringify(query);

    if (!force && state.patientsLoaded && state.patientsQueryKey === queryKey) {
        return;
    }

    if (state.inFlight.patients && state.inFlight.patientsQueryKey === queryKey) {
        return state.inFlight.patients;
    }

    state.inFlight.patientsQueryKey = queryKey;
    state.inFlight.patients = (async () => {
        const result = await call('/nurse/patients', 'GET', null, 'nurse', query);
        if (result.status < 300) {
            state.patients = Array.isArray(result.data?.patients) ? result.data.patients : [];
            state.patientsLoaded = true;
            state.patientsQueryKey = queryKey;
            renderStats(result.data?.stats || null);
            renderPatients();
        } else {
            state.patients = [];
            state.patientsLoaded = false;
            renderStats(null);
            renderPatients();
        }
        write(result);
    })();

    try {
        await state.inFlight.patients;
    } finally {
        state.inFlight.patients = null;
        state.inFlight.patientsQueryKey = null;
    }
}

async function selectAdmission(admissionId, patientUserId) {
    state.selectedAdmissionId = Number(admissionId);
    state.selectedPatientUserId = Number(patientUserId);
    document.getElementById('vAdmissionId').value = String(state.selectedAdmissionId);
    document.getElementById('vPatientUserId').value = String(state.selectedPatientUserId);
    renderPatients();
    await loadAdmissionDetail();
}

async function loadAdmissionDetail() {
    if (!state.selectedAdmissionId) {
        write({ status: 422, data: { message: 'Select an admission first.' } });
        return;
    }

    const result = await call(`/nurse/admissions/${state.selectedAdmissionId}`, 'GET', null, 'nurse', { vitalsLimit: 10, recordsLimit: 10 });
    if (result.status < 300) {
        state.selectedDetail = result.data;
        renderAdmissionSummary(result.data?.admission || null);
        renderVitals(result.data?.vital_sign_logs || []);
        renderRecords(result.data?.medical_records || []);
    }
    write(result);
}

async function loadSelectedAdmissionVitals() {
    const admissionId = Number(document.getElementById('vAdmissionId').value);
    if (!admissionId) {
        write({ status: 422, data: { message: 'Admission ID required for vital refresh.' } });
        return;
    }

    const result = await call(`/nurse/admissions/${admissionId}/vitals`, 'GET', null, 'nurse', { limit: 10 });
    if (result.status < 300) {
        renderVitals(result.data?.vital_sign_logs || []);
    }
    write(result);
}

function maybeNumber(id) {
    const value = document.getElementById(id).value.trim();
    if (!value) return null;
    const number = Number(value);
    return Number.isFinite(number) ? number : null;
}

async function logVitals() {
    const admissionId = Number(document.getElementById('vAdmissionId').value);
    const patientUserId = Number(document.getElementById('vPatientUserId').value);
    if (!admissionId || !patientUserId) {
        write({ status: 422, data: { message: 'Admission ID and Patient User ID are required.' } });
        return;
    }

    const payload = {
        patientUserId,
        temperatureC: maybeNumber('vTemp'),
        pulseBpm: maybeNumber('vPulse'),
        systolicBp: maybeNumber('vSys'),
        diastolicBp: maybeNumber('vDia'),
        respirationRate: maybeNumber('vResp'),
        spo2Percent: maybeNumber('vSpo2'),
        note: document.getElementById('vNote').value.trim() || null,
    };

    const result = await call(`/nurse/admissions/${admissionId}/vitals`, 'POST', payload, 'nurse');
    write(result);

    if (result.status < 300) {
        state.patientsLoaded = false;
        await Promise.all([
            loadAdmissionDetail(),
            loadPatients({ force: true }),
        ]);
    }
}

async function loadBloodBankDonors(options = {}) {
    const force = options.force !== false;
    const query = {};
    const search = document.getElementById('bbDonorQuery').value.trim();
    const requestId = document.getElementById('bbRequestId').value.trim();
    const bloodGroup = document.getElementById('bbBloodGroupFilter').value.trim();
    const eligible = document.getElementById('bbEligibilityFilter').value;
    if (search) query.q = search;
    if (requestId) query.requestId = Number(requestId);
    if (bloodGroup) query.bloodGroup = bloodGroup;
    if (eligible) query.eligible = eligible === 'true';
    const queryKey = JSON.stringify(query);

    if (!force && state.bloodBankDonorsLoaded && state.bloodBankDonorsQueryKey === queryKey) {
        return;
    }

    if (state.inFlight.donors && state.inFlight.donorsQueryKey === queryKey) {
        return state.inFlight.donors;
    }

    state.inFlight.donorsQueryKey = queryKey;
    state.inFlight.donors = (async () => {
        const result = await call('/nurse/blood-bank/donors', 'GET', null, 'nurse', query);
        if (result.status < 300) {
            state.bloodBankDonors = Array.isArray(result.data?.donors) ? result.data.donors : [];
            state.bloodBankDonorsLoaded = true;
            state.bloodBankDonorsQueryKey = queryKey;
            renderBloodBankDonors();
            const selectedDonor = state.bloodBankDonors.find((entry) => Number(entry.donor_id) === Number(state.selectedDonorId)) || null;
            if (state.selectedDonorId && !selectedDonor) {
                state.selectedDonorId = null;
                renderSelectedBloodBankDonor(null);
                renderBloodBankHealthChecks([]);
            } else if (selectedDonor) {
                renderSelectedBloodBankDonor(selectedDonor);
            }
        }
        write(result);
    })();

    try {
        await state.inFlight.donors;
    } finally {
        state.inFlight.donors = null;
        state.inFlight.donorsQueryKey = null;
    }
}

async function selectBloodBankDonor(donorId) {
    state.selectedDonorId = Number(donorId);
    renderBloodBankDonors();
    const donor = state.bloodBankDonors.find((entry) => Number(entry.donor_id) === Number(donorId)) || null;
    renderSelectedBloodBankDonor(donor);
    await loadSelectedDonorHealthChecks({ force: false });
}

async function loadSelectedDonorHealthChecks(options = {}) {
    const force = options.force !== false;
    const donorId = Number(document.getElementById('bbDonorId').value || state.selectedDonorId || 0);
    if (!donorId) {
        renderBloodBankHealthChecks([]);
        return;
    }

    if (!force && state.donorHealthChecksByDonor[donorId]) {
        renderBloodBankHealthChecks(state.donorHealthChecksByDonor[donorId]);
        return;
    }

    if (state.inFlight.donorChecks && state.inFlight.donorChecksDonorId === donorId) {
        return state.inFlight.donorChecks;
    }

    state.inFlight.donorChecksDonorId = donorId;
    state.inFlight.donorChecks = (async () => {
        const result = await call(`/nurse/blood-bank/donors/${donorId}/health-checks`, 'GET', null, 'nurse', { limit: 12 });
        if (result.status < 300) {
            const checks = Array.isArray(result.data?.health_checks) ? result.data.health_checks : [];
            state.donorHealthChecksByDonor[donorId] = checks;
            renderBloodBankHealthChecks(checks);
        }
        write(result);
    })();

    try {
        await state.inFlight.donorChecks;
    } finally {
        state.inFlight.donorChecks = null;
        state.inFlight.donorChecksDonorId = null;
    }
}

function previewEligibility() {
    const weight = Number(document.getElementById('bbWeightKg').value || 0);
    const temp = Number(document.getElementById('bbTemperatureC').value || 0);
    const hbRaw = document.getElementById('bbHemoglobin').value.trim();
    const hb = hbRaw ? Number(hbRaw) : null;
    const result = document.getElementById('bbEligibilityResult');
    const reason = document.getElementById('bbEligibilityReason');

    if (!weight || !temp) {
        resetBloodBankEligibilityFeedback();
        return;
    }

    const eligible = weight >= 45 && temp >= 36.0 && temp <= 37.8 && (hb === null || hb >= 12.5);
    result.className = `nurse-pill ${eligible ? 'live' : 'off'}`;
    result.textContent = eligible ? 'Eligible by current values' : 'Not eligible by current values';
    reason.textContent = eligible
        ? 'Preview only. Saving will request the backend eligibility decision.'
        : 'Preview only. Saving will request the backend eligibility decision and rejection reason.';
}

async function logBloodBankHealthCheck() {
    const donorId = Number(document.getElementById('bbDonorId').value);
    if (!donorId) {
        write({ status: 422, data: { message: 'Select a donor first.' } });
        return;
    }

    const payload = {
        checkDateTime: document.getElementById('bbCheckDateTime').value || null,
        weightKg: Number(document.getElementById('bbWeightKg').value || 0),
        temperatureC: Number(document.getElementById('bbTemperatureC').value || 0),
        hemoglobin: document.getElementById('bbHemoglobin').value ? Number(document.getElementById('bbHemoglobin').value) : null,
        notes: document.getElementById('bbHealthNote').value.trim() || null,
    };

    const result = await call(`/nurse/blood-bank/donors/${donorId}/health-checks`, 'POST', payload, 'nurse');
    write(result);

    if (result.status < 300) {
        applyEligibilityFeedback(result.data?.eligibility || null);
        delete state.donorHealthChecksByDonor[donorId];
        state.bloodBankDonorsLoaded = false;
        const donor = result.data?.donor || null;
        if (donor) {
            state.selectedDonorId = Number(donor.donor_id);
            renderSelectedBloodBankDonor(donor);
        }
        await Promise.all([
            loadSelectedDonorHealthChecks({ force: true }),
            loadBloodBankDonors({ force: true }),
        ]);
    }
}

async function bootNurseDashboard() {
    setupSidebarPanelNav();
    renderStats(null);
    renderPatients();
    renderAdmissionSummary(null);
    renderVitals([]);
    renderRecords([]);
    renderBloodBankAccess();
    renderBloodBankDonors();
    renderSelectedBloodBankDonor(null);
    renderSelectedBloodBankDonorStatus(null);
    renderBloodBankHealthChecks([]);
    resetBloodBankEligibilityFeedback();
    useStoredUserToken();

    if (document.getElementById('nurseTokenInput').value.trim()) {
        await loadNurseProfile({ force: false });
    } else {
        write('Login first or use USER_TOKEN so the nurse dashboard can auto-load your profile.');
    }
}

document.getElementById('bbWeightKg').addEventListener('input', previewEligibility);
document.getElementById('bbTemperatureC').addEventListener('input', previewEligibility);
document.getElementById('bbHemoglobin').addEventListener('input', previewEligibility);
bootNurseDashboard();
</script>
@endpush
