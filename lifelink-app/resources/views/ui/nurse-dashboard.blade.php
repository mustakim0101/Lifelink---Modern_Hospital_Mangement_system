@extends('ui.layouts.app')

@section('title', 'Nurse Dashboard')
@section('role_theme', 'nurse')
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
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="nurse-grid">
        <div id="nurse-overview" class="nurse-split ll-section nurse-panel-switch" data-display="grid">
            <div class="nurse-panel nurse-col-12">
                <section class="ll-overview-welcome">
                    <h2>Welcome back</h2>
                    <div id="nurseWelcomeName" class="ll-welcome-name">Nurse</div>
                    <div id="nurseWelcomeMeta" class="ll-welcome-meta">Loading profile and department context...</div>
                </section>
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

            <div class="nurse-panel nurse-col-4">
                <h3>Shift quick actions</h3>
                <p id="overviewModeHint" class="nurse-note">Load profile and patient data to see live nurse priorities for this shift.</p>
                <div class="nurse-actions">
                    <button id="quickOpenMonitoring" class="nurse-button soft" type="button" onclick="goToNursePanel('nurse-monitoring')">Open monitoring</button>
                    <button id="quickOpenBloodBank" class="nurse-button soft" type="button" onclick="goToNursePanel('nurse-blood-bank')">Open blood bank</button>
                    <button id="quickFilterAdmitted" class="nurse-button primary" type="button" onclick="applyQuickPatientFilter('Admitted')">Show admitted only</button>
                    <button id="quickClearFilters" class="nurse-button soft" type="button" onclick="clearDepartmentFilters()">Clear filters</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-12">
                <h3>Workload snapshot</h3>
                <div class="nurse-overview-stat-grid">
                    <div class="nurse-stat"><strong id="ovTotal">-</strong><span>Total admissions</span></div>
                    <div class="nurse-stat"><strong id="ovAdmitted">-</strong><span>Admitted now</span></div>
                    <div class="nurse-stat"><strong id="ovNoBed">-</strong><span>Without bed</span></div>
                    <div class="nurse-stat"><strong id="ovVitalsDue">-</strong><span>Vitals due (6h)</span></div>
                    <div class="nurse-stat"><strong id="ovAlerts">-</strong><span>Alert vitals</span></div>
                    <div class="nurse-stat"><strong id="ovTransfers">-</strong><span>Transfers</span></div>
                </div>
            </div>

            <div class="nurse-panel nurse-col-6">
                <h3>Attention queue</h3>
                <p class="nurse-note">Top admissions that likely need nurse follow-up first.</p>
                <div id="overviewAttentionQueue" class="nurse-overview-list">
                    <div class="nurse-note">Load profile and admissions to generate the queue.</div>
                </div>
            </div>

            <div class="nurse-panel nurse-col-6">
                <h3>Shift checklist</h3>
                <p class="nurse-note">A quick non-access checklist to keep handoff and bedside tasks on track.</p>
                <ul class="nurse-overview-checklist">
                    <li>Review new admissions and verify first-vitals coverage.</li>
                    <li>Prioritize admissions with no bed assignment.</li>
                    <li>Flag abnormal temperature, pulse, and SpO2 readings.</li>
                    <li>Confirm transfer/discharge candidates with records review.</li>
                    <li>Complete shift handoff notes for unresolved cases.</li>
                </ul>
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
                    <div id="patientList" class="nurse-list ui-list-window"></div>
                    <div id="patientListPagination" class="ui-list-pagination"></div>
                </div>

                <div class="nurse-panel nurse-col-7">
                <h3>Admission monitor</h3>
                <p class="nurse-note">Record vitals for the selected admission and review recent entries.</p>

                <div class="nurse-section-title">Selected admission</div>
                <div id="admissionSummary" class="nurse-summary-grid"></div>

                <div class="nurse-section-title">Log vital signs</div>
                <div class="nurse-control-grid">
                    <div>
                        <label class="nurse-label" for="vAdmissionId">Selected admission</label>
                        <input id="vAdmissionId" class="nurse-input" type="number" placeholder="Selected admission" readonly>
                    </div>
                    <div>
                        <label class="nurse-label" for="vPatientUserId">Patient</label>
                        <input id="vPatientUserId" class="nurse-input" type="number" placeholder="Selected patient" readonly>
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

    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const nursePanelIds = ['nurse-overview', 'nurse-monitoring', 'nurse-blood-bank'];
const nurseNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[data-panel]'));

const state = {
    activePanel: 'nurse-overview',
    nurse: null,
    nurseProfileLoaded: false,
    profileRequested: false,
    patients: [],
    patientsLoaded: false,
    patientsQueryKey: null,
    pagination: {
        patientsPageSize: 8,
        patientsPage: 1,
    },
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
    if (typeof value === 'string') {
        return value.trim().toLowerCase().replace(/[_-]+/g, ' ').replace(/\s+/g, ' ');
    }
    if (typeof value === 'object') {
        return String(
            value.dept_name
            || value.department
            || value.department_name
            || value.departmentName
            || value.deptName
            || value.label
            || value.name
            || ''
        ).trim().toLowerCase().replace(/[_-]+/g, ' ').replace(/\s+/g, ' ');
    }
    return String(value).trim().toLowerCase().replace(/[_-]+/g, ' ').replace(/\s+/g, ' ');
}

function nurseDepartmentCandidates(nurse) {
    if (!nurse || typeof nurse !== 'object') return [];
    return [
        nurse.department,
        nurse.department_name,
        nurse.dept_name,
        nurse.departmentName,
        nurse.deptName,
        nurse.department_label,
        nurse.departmentLabel,
        nurse.profile_department,
        nurse.profileDepartment,
        nurse.meta?.department,
        nurse.department_info,
    ];
}

function resolvedNurseDepartmentName() {
    for (const candidate of nurseDepartmentCandidates(state.nurse)) {
        const normalized = normalizeDepartmentName(candidate);
        if (normalized) {
            return normalized;
        }
    }
    return '';
}

function resolvedNurseDepartmentLabel() {
    const normalized = resolvedNurseDepartmentName();
    if (!normalized) return '';
    return normalized
        .split(' ')
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

function resolvedNurseDepartmentKey() {
    return resolvedNurseDepartmentName().replace(/[^a-z0-9]/g, '');
}

function isBloodBankNurse() {
    return resolvedNurseDepartmentKey() === 'bloodbank';
}

function canUseBloodBankWorkflow() {
    return state.nurseProfileLoaded && !!state.nurse && isBloodBankNurse();
}

function canUseRegularWorkflow() {
    return state.nurseProfileLoaded && !!state.nurse && !isBloodBankNurse();
}

function allowedNursePanels() {
    if (!state.nurseProfileLoaded || !state.nurse) {
        return ['nurse-overview'];
    }

    if (isBloodBankNurse()) {
        return ['nurse-overview', 'nurse-blood-bank'];
    }

    return ['nurse-overview', 'nurse-monitoring'];
}

function preferredNursePanel() {
    const allowed = allowedNursePanels();
    return allowed.find((panelId) => panelId !== 'nurse-overview') || allowed[0];
}

function updateNurseSidebarByMode() {
    const allowed = allowedNursePanels();
    nurseNavLinks.forEach((link) => {
        const panelId = link.dataset.panel || '';
        const canShow = allowed.includes(panelId);
        link.hidden = !canShow;
        link.style.display = canShow ? '' : 'none';
    });

    if (!allowed.includes(state.activePanel)) {
        const panelId = preferredNursePanel();
        setActivePanel(panelId);
        history.replaceState(null, '', `#${panelId}`);
    }
}

function updateOverviewQuickActions() {
    const profileLoaded = state.nurseProfileLoaded && !!state.nurse;
    const bloodMode = canUseBloodBankWorkflow();
    const regularMode = canUseRegularWorkflow();

    setVisibility('quickOpenMonitoring', profileLoaded && regularMode, 'inline-flex');
    setVisibility('quickOpenBloodBank', profileLoaded && bloodMode, 'inline-flex');
    setVisibility('quickFilterAdmitted', profileLoaded && regularMode, 'inline-flex');
    setVisibility('quickClearFilters', profileLoaded && regularMode, 'inline-flex');
}

function write(data) {
    if (!window.lifeLinkShell?.isDebugEnabled() || !out) return;
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
    if (!currentNurseToken()) {
        return;
    }

    await loadNurseProfile({ force: false });

    if (panelId === 'nurse-overview') {
        if (canUseBloodBankWorkflow()) {
            await loadBloodBankDonors({ force: false });
            return;
        }
        if (canUseRegularWorkflow()) {
            await loadPatients({ force: false });
        }
        return;
    }

    if (panelId === 'nurse-monitoring' && canUseRegularWorkflow()) {
        await loadPatients({ force: false });
        return;
    }

    if (panelId === 'nurse-blood-bank' && canUseBloodBankWorkflow()) {
        await loadBloodBankDonors({ force: false });
    }
}

function useStoredUserToken() {
    const tokenInput = document.getElementById('nurseTokenInput');
    if (tokenInput) {
        tokenInput.value = localStorage.getItem('USER_TOKEN') || '';
    }
}

function currentNurseToken() {
    const tokenInput = document.getElementById('nurseTokenInput');
    const typedToken = tokenInput ? tokenInput.value.trim() : '';
    if (typedToken) return typedToken;
    return (localStorage.getItem('USER_TOKEN') || '').trim();
}

function buildUrl(path, query = null) {
    if (!query) return `${API}${path}`;
    const qs = new URLSearchParams(query);
    const stringQuery = qs.toString();
    return stringQuery ? `${API}${path}?${stringQuery}` : `${API}${path}`;
}

async function call(path, method = 'GET', body = null, tokenType = 'nurse', query = null) {
    const token = currentNurseToken();

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

function setTextById(id, value) {
    const element = document.getElementById(id);
    if (!element) return;
    element.textContent = String(value);
}

function hoursSinceTimestamp(value) {
    if (!value) return null;
    const timestamp = new Date(value).getTime();
    if (!Number.isFinite(timestamp)) return null;
    return (Date.now() - timestamp) / 3600000;
}

function getAlertReason(patient) {
    const latest = patient.latest_vital_sign || null;
    if (!latest) return null;
    if (latest.temperature_c !== null && Number(latest.temperature_c) >= 38) return 'High temperature detected';
    if (latest.spo2_percent !== null && Number(latest.spo2_percent) <= 93) return 'Low SpO2 detected';
    if (latest.pulse_bpm !== null && Number(latest.pulse_bpm) >= 120) return 'Pulse above threshold';
    return null;
}

function renderOverviewInsights() {
    const hint = document.getElementById('overviewModeHint');
    const queueRoot = document.getElementById('overviewAttentionQueue');
    if (!hint || !queueRoot) return;

    if (!state.nurseProfileLoaded || !state.nurse) {
        setTextById('ovTotal', '-');
        setTextById('ovAdmitted', '-');
        setTextById('ovNoBed', '-');
        setTextById('ovVitalsDue', '-');
        setTextById('ovAlerts', '-');
        setTextById('ovTransfers', '-');
        hint.textContent = 'Load profile and patient data to see live nurse priorities for this shift.';
        queueRoot.innerHTML = '<div class="nurse-note">Load profile and admissions to generate the queue.</div>';
        return;
    }

    if (isBloodBankNurse()) {
        setTextById('ovTotal', state.bloodBankDonors.length || 0);
        setTextById('ovAdmitted', '-');
        setTextById('ovNoBed', '-');
        setTextById('ovVitalsDue', '-');
        setTextById('ovAlerts', '-');
        setTextById('ovTransfers', '-');
        hint.textContent = 'Blood Bank mode is active. Use donor screening from the Blood Bank panel.';
        queueRoot.innerHTML = '<div class="nurse-note">This account is in Blood Bank mode, so inpatient attention queue is not active.</div>';
        return;
    }

    const admissions = Array.isArray(state.patients) ? state.patients : [];
    const admitted = admissions.filter((entry) => String(entry.status || '').toLowerCase() === 'admitted');
    const noBed = admitted.filter((entry) => !entry.active_bed_assignment);
    const vitalsDue = admitted.filter((entry) => {
        const hours = hoursSinceTimestamp(entry.latest_vital_sign?.measured_at);
        return hours === null || hours >= 6;
    });
    const alertVitals = admitted.filter((entry) => !!getAlertReason(entry));
    const transfers = admissions.filter((entry) => String(entry.status || '').toLowerCase() === 'transferred');

    setTextById('ovTotal', admissions.length);
    setTextById('ovAdmitted', admitted.length);
    setTextById('ovNoBed', noBed.length);
    setTextById('ovVitalsDue', vitalsDue.length);
    setTextById('ovAlerts', alertVitals.length);
    setTextById('ovTransfers', transfers.length);

    hint.textContent = admissions.length
        ? `Live priorities generated from ${admissions.length} admissions in the current filter.`
        : 'No admissions in this filter yet. Update filter or refresh patient data.';

    const queue = [];
    alertVitals.slice(0, 2).forEach((entry) => {
        queue.push({ entry, reason: getAlertReason(entry) || 'Vital signs outside preferred range' });
    });
    noBed.slice(0, 2).forEach((entry) => {
        queue.push({ entry, reason: 'Bed assignment missing' });
    });
    vitalsDue.slice(0, 2).forEach((entry) => {
        const hours = hoursSinceTimestamp(entry.latest_vital_sign?.measured_at);
        const reason = hours === null
            ? 'No vitals logged yet'
            : `Vitals overdue by ${Math.floor(hours)}h`;
        queue.push({ entry, reason });
    });

    const uniqueQueue = [];
    const seenAdmissionIds = new Set();
    queue.forEach((item) => {
        const admissionId = Number(item.entry?.id || 0);
        if (!admissionId || seenAdmissionIds.has(admissionId)) return;
        seenAdmissionIds.add(admissionId);
        uniqueQueue.push(item);
    });

    if (!uniqueQueue.length) {
        queueRoot.innerHTML = '<div class="nurse-note">No urgent admissions detected from the current filter.</div>';
        return;
    }

    queueRoot.innerHTML = uniqueQueue.slice(0, 5).map((item) => {
        const admissionId = Number(item.entry.id || 0);
        const patientUserId = Number(item.entry.patient_user_id || 0);
        const openAction = admissionId && patientUserId
            ? `<button class="nurse-button soft" type="button" onclick="focusAdmissionFromOverview(${admissionId}, ${patientUserId})">Open</button>`
            : '';

        return `
            <article class="nurse-overview-item">
                <div>
                    <strong>${escapeHtml(item.entry.patient_name || 'Unknown patient')}</strong>
                    <p class="nurse-note">${escapeHtml(item.reason)}</p>
                </div>
                ${openAction}
            </article>
        `;
    }).join('');
}

function goToNursePanel(panelId) {
    setActivePanel(panelId);
    history.replaceState(null, '', `#${state.activePanel}`);
}

async function focusAdmissionFromOverview(admissionId, patientUserId) {
    if (!canUseRegularWorkflow()) return;
    goToNursePanel('nurse-monitoring');
    await loadPatients({ force: false });
    await selectAdmission(admissionId, patientUserId);
}

async function applyQuickPatientFilter(statusValue) {
    if (!canUseRegularWorkflow()) return;
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) statusFilter.value = statusValue;
    await loadPatients({ force: true });
}

async function clearDepartmentFilters() {
    if (!canUseRegularWorkflow()) return;
    const statusFilter = document.getElementById('statusFilter');
    const queryFilter = document.getElementById('queryFilter');
    if (statusFilter) statusFilter.value = '';
    if (queryFilter) queryFilter.value = '';
    await loadPatients({ force: true });
}

function badgeForStatus(status) {
    return status === 'Admitted'
        ? '<span class="nurse-pill live">Admitted</span>'
        : `<span class="nurse-pill off">${escapeHtml(status || 'Unknown')}</span>`;
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

function renderPatientListPagination(pageData) {
    const root = document.getElementById('patientListPagination');
    if (!root) return;

    if (pageData.totalRows <= state.pagination.patientsPageSize) {
        root.innerHTML = '';
        return;
    }

    root.innerHTML = `
        <div class="ui-list-pagination__meta">Page ${pageData.page} of ${pageData.totalPages} (${pageData.totalRows} total)</div>
        <div class="ui-list-pagination__controls">
            <button class="nurse-button soft" type="button" ${pageData.page <= 1 ? 'disabled' : ''} onclick="prevPatientPage()">Previous</button>
            <button class="nurse-button soft" type="button" ${pageData.page >= pageData.totalPages ? 'disabled' : ''} onclick="nextPatientPage()">Next</button>
        </div>
    `;
}

function prevPatientPage() {
    state.pagination.patientsPage = Math.max(1, state.pagination.patientsPage - 1);
    renderPatients();
}

function nextPatientPage() {
    state.pagination.patientsPage += 1;
    renderPatients();
}

function renderPatients() {
    const holder = document.getElementById('patientList');
    if (!state.patients.length) {
        holder.innerHTML = '<div class="nurse-note">No admissions found for this filter.</div>';
        renderPatientListPagination({ page: 1, totalPages: 1, totalRows: 0 });
        renderOverviewInsights();
        return;
    }

    const pageData = paginateRows(state.patients, state.pagination.patientsPage, state.pagination.patientsPageSize);
    state.pagination.patientsPage = pageData.page;

    holder.innerHTML = pageData.rows.map((patient) => {
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

    renderPatientListPagination(pageData);
    renderOverviewInsights();
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
            : `Regular nurse mode is active for ${resolvedNurseDepartmentLabel() || 'this department'}.`;
    const nurseName = localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Nurse';
    const nurseDepartment = resolvedNurseDepartmentLabel() || '-';
    setTextById('nurseWelcomeName', nurseName);
    setTextById(
        'nurseWelcomeMeta',
        `Role: Nurse | Department: ${nurseDepartment} | Email: ${localStorage.getItem('CURRENT_USER_EMAIL') || '-'} | ID: ${localStorage.getItem('CURRENT_USER_ID') || '-'}`
    );
    if (window.lifeLinkShell) {
        window.lifeLinkShell.updateIdentityContext({
            name: nurseName,
            userId: localStorage.getItem('CURRENT_USER_ID') || '-',
            email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
            role: 'Nurse',
            department: resolvedNurseDepartmentLabel() || null,
        });
    }
    updateNurseSidebarByMode();
    updateOverviewQuickActions();
    renderOverviewInsights();
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
            const normalizedDepartment = resolvedNurseDepartmentLabel();
            if (normalizedDepartment) {
                state.nurse.department = normalizedDepartment;
            }
        } else {
            state.nurse = null;
            state.patients = [];
            state.bloodBankDonors = [];
            state.selectedAdmissionId = null;
            state.selectedPatientUserId = null;
            state.selectedDonorId = null;
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
    if (!canUseRegularWorkflow()) {
        return;
    }

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
            state.pagination.patientsPage = 1;
            renderStats(result.data?.stats || null);
            renderPatients();
        } else {
            state.patients = [];
            state.patientsLoaded = false;
            state.pagination.patientsPage = 1;
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
    if (!canUseRegularWorkflow()) return;
    state.selectedAdmissionId = Number(admissionId);
    state.selectedPatientUserId = Number(patientUserId);
    document.getElementById('vAdmissionId').value = String(state.selectedAdmissionId);
    document.getElementById('vPatientUserId').value = String(state.selectedPatientUserId);
    renderPatients();
    await loadAdmissionDetail();
}

async function loadAdmissionDetail() {
    if (!canUseRegularWorkflow()) return;
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
    if (!canUseRegularWorkflow()) return;
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
    if (!canUseRegularWorkflow()) return;
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
    if (!canUseBloodBankWorkflow()) {
        return;
    }

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
            } else if (!state.selectedDonorId && state.bloodBankDonors.length) {
                state.selectedDonorId = Number(state.bloodBankDonors[0].donor_id);
                renderBloodBankDonors();
                renderSelectedBloodBankDonor(state.bloodBankDonors[0]);
                await loadSelectedDonorHealthChecks({ force: false });
            }
        } else {
            state.bloodBankDonors = [];
            state.bloodBankDonorsLoaded = false;
        }
        renderOverviewInsights();
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
    if (!canUseBloodBankWorkflow()) return;
    state.selectedDonorId = Number(donorId);
    renderBloodBankDonors();
    const donor = state.bloodBankDonors.find((entry) => Number(entry.donor_id) === Number(donorId)) || null;
    renderSelectedBloodBankDonor(donor);
    await loadSelectedDonorHealthChecks({ force: false });
}

async function loadSelectedDonorHealthChecks(options = {}) {
    if (!canUseBloodBankWorkflow()) {
        renderBloodBankHealthChecks([]);
        return;
    }

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
    if (!canUseBloodBankWorkflow()) return;
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
    renderOverviewInsights();
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

    if (currentNurseToken()) {
        await loadNurseProfile({ force: false });
    } else {
        write('Login first or use USER_TOKEN so the nurse dashboard can auto-load your profile.');
    }
}

document.getElementById('bbWeightKg').addEventListener('input', previewEligibility);
document.getElementById('bbTemperatureC').addEventListener('input', previewEligibility);
document.getElementById('bbHemoglobin').addEventListener('input', previewEligibility);
document.getElementById('statusFilter').addEventListener('change', () => loadPatients({ force: true }));
document.getElementById('queryFilter').addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        loadPatients({ force: true });
    }
});
bootNurseDashboard();
</script>
@endpush
