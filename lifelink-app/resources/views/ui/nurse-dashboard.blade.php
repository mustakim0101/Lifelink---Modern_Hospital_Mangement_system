@extends('ui.layouts.app')

@section('title', 'Nurse Workspace')
@section('workspace_label', 'Nursing operations workspace')
@section('hero_badge', 'Nurse')
@section('hero_title', 'Monitor admissions, chart vitals, and support safe bedside care.')
@section('hero_description', 'Keep patient monitoring, recent records, and blood bank screening in one consistent workflow without exposing technical response logs to nursing staff.')
@section('meta_title', 'Nurse Workspace')

@push('styles')
<style>
    :root {
        --nurse-ink: #14233a;
        --nurse-muted: #5f718c;
        --nurse-card: rgba(255, 255, 255, 0.92);
        --nurse-line: rgba(20, 35, 58, 0.12);
        --nurse-teal: #0d9488;
        --nurse-teal-dark: #0b746b;
        --nurse-orange: #f97316;
        --nurse-alert: #dc2626;
        --nurse-ok: #16a34a;
        --nurse-shadow: 0 18px 35px rgba(16, 29, 57, 0.12);
    }

    .nurse-grid,
    .nurse-control-grid,
    .nurse-stat-grid,
    .nurse-summary-grid,
    .nurse-actions,
    .nurse-split {
        display: grid;
        gap: 12px;
    }

    .nurse-grid {
        gap: 14px;
    }
    .nurse-panel-switch { display: none; }

    .nurse-panel {
        border: 1px solid var(--nurse-line);
        background: var(--nurse-card);
        border-radius: 18px;
        box-shadow: var(--nurse-shadow);
        padding: 16px;
    }

    .nurse-panel h3 {
        margin: 0;
    }

    .nurse-note {
        margin: 6px 0 0;
        color: var(--nurse-muted);
        font-size: 0.94rem;
        line-height: 1.7;
    }

    .nurse-split {
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }

    .nurse-col-4 { grid-column: span 4; }
    .nurse-col-5 { grid-column: span 5; }
    .nurse-col-7 { grid-column: span 7; }
    .nurse-col-12 { grid-column: span 12; }

    .nurse-control-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 12px;
    }

    .nurse-label {
        display: block;
        margin-bottom: 6px;
        color: var(--nurse-muted);
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .nurse-input,
    .nurse-select,
    .nurse-textarea {
        width: 100%;
        border: 1px solid rgba(20, 35, 58, 0.2);
        background: rgba(255, 255, 255, 0.92);
        border-radius: 12px;
        padding: 11px 12px;
        font: inherit;
        color: var(--nurse-ink);
        outline: none;
    }

    .nurse-input:focus,
    .nurse-select:focus,
    .nurse-textarea:focus {
        border-color: var(--nurse-teal);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .nurse-textarea {
        min-height: 90px;
        resize: vertical;
    }

    .nurse-actions {
        grid-template-columns: repeat(3, max-content);
        margin-top: 12px;
        justify-content: start;
    }

    .nurse-button {
        border: 0;
        border-radius: 12px;
        padding: 10px 14px;
        font: inherit;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
    }

    .nurse-button.primary { background: var(--nurse-teal); color: #fff; }
    .nurse-button.primary:hover { background: var(--nurse-teal-dark); }
    .nurse-button.soft { background: rgba(20, 35, 58, 0.08); color: var(--nurse-ink); }
    .nurse-button.warm { background: var(--nurse-orange); color: #fff; }

    .nurse-stat-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        margin-top: 12px;
    }

    .nurse-stat,
    .nurse-summary,
    .nurse-pill,
    .nurse-list-item {
        border: 1px solid var(--nurse-line);
        background: rgba(255, 255, 255, 0.86);
        border-radius: 14px;
    }

    .nurse-stat {
        padding: 12px;
        text-align: center;
    }

    .nurse-stat strong {
        display: block;
        font-size: 1.5rem;
    }

    .nurse-stat span {
        color: var(--nurse-muted);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        font-weight: 800;
    }

    .nurse-list {
        display: grid;
        gap: 10px;
        margin-top: 12px;
        max-height: 520px;
        overflow: auto;
        padding-right: 4px;
    }

    .nurse-list-item {
        padding: 12px;
        cursor: pointer;
    }

    .nurse-list-item.is-active {
        border-color: rgba(13, 148, 136, 0.44);
        background: rgba(13, 148, 136, 0.08);
    }

    .nurse-item-head,
    .nurse-item-meta {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }

    .nurse-item-head strong {
        font-size: 1rem;
    }

    .nurse-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        font-size: 0.74rem;
        font-weight: 800;
    }

    .nurse-pill.live { color: var(--nurse-ok); background: rgba(22, 163, 74, 0.12); }
    .nurse-pill.off { color: var(--nurse-alert); background: rgba(220, 38, 38, 0.1); }
    .nurse-pill.bed { color: var(--nurse-teal-dark); background: rgba(13, 148, 136, 0.12); }

    .nurse-mini {
        border-radius: 999px;
        background: rgba(20, 35, 58, 0.08);
        color: var(--nurse-ink);
        font-size: 0.74rem;
        padding: 4px 8px;
        font-weight: 700;
    }

    .nurse-section-title {
        margin: 14px 0 8px;
        color: var(--nurse-muted);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
    }

    .nurse-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .nurse-summary {
        padding: 10px 11px;
    }

    .nurse-summary small {
        display: block;
        color: var(--nurse-muted);
        font-size: 0.72rem;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 800;
    }

    .nurse-summary strong {
        font-size: 0.95rem;
        word-break: break-word;
    }

    .nurse-table-wrap {
        overflow: auto;
        border: 1px solid var(--nurse-line);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.9);
    }

    .nurse-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .nurse-table th,
    .nurse-table td {
        padding: 9px 10px;
        border-bottom: 1px solid rgba(20, 35, 58, 0.08);
        text-align: left;
        white-space: nowrap;
    }

    .nurse-table th {
        background: rgba(246, 250, 255, 0.95);
        color: var(--nurse-muted);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .nurse-console {
        margin: 12px 0 0;
        min-height: 140px;
        max-height: 300px;
        overflow: auto;
        border-radius: 14px;
        border: 1px solid var(--nurse-line);
        background: #11203a;
        color: #d7e3ff;
        padding: 12px;
        font-size: 12px;
    }

    @media (max-width: 1100px) {
        .nurse-col-4,
        .nurse-col-5,
        .nurse-col-7 {
            grid-column: span 12;
        }

        .nurse-stat-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 780px) {
        .nurse-control-grid,
        .nurse-summary-grid,
        .nurse-stat-grid,
        .nurse-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#nurse-overview">
        <strong>Overview</strong>
    </a>
    <a href="#nurse-monitoring">
        <strong>Patient Monitoring</strong>
    </a>
    <a href="#nurse-blood-bank">
        <strong>Blood Bank Screening</strong>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Shift flow</strong>
        <p>Refresh your profile, review active admissions, select a patient, then chart vitals or donor screening updates from the same workspace.</p>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Care safety</strong>
        <p>Use patient cards and admission detail first. The interface now keeps technical session handling in the background so bedside tasks stay readable.</p>
    </div>
@endsection

@section('content')
    <div class="nurse-grid">
        <div id="nurseSessionAlert" class="ll-inline-alert is-warning ll-hidden-debug">
            <strong>Nurse session required</strong>
            <p>Sign in first so profile loading, monitoring, and Blood Bank actions can use your active workspace session.</p>
        </div>

        <div id="nurseActionAlert" class="ll-inline-alert is-success ll-hidden-debug">
            <strong id="nurseActionTitle">Workspace ready</strong>
            <p id="nurseActionBody">Recent nurse actions and status updates will appear here.</p>
        </div>

        <div id="nurse-overview" class="nurse-split ll-section nurse-panel-switch" data-display="grid">
            <div class="nurse-panel nurse-col-4">
                <h3>Shift readiness</h3>
                <p id="nurseSessionCopy" class="nurse-note">This workspace uses your signed-in session. If profile loading fails, admin likely still needs to finish nurse setup.</p>
                <div class="nurse-summary-grid" style="margin-top: 12px;">
                    <div class="nurse-summary"><small>Session</small><strong id="nurseSessionState">Checking</strong></div>
                    <div class="nurse-summary"><small>Department</small><strong id="nurseDepartmentState">Waiting for profile</strong></div>
                </div>
                <div class="nurse-actions">
                    <button class="nurse-button primary" type="button" onclick="useStoredUserToken(); loadNurseProfile()">Refresh session</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-4">
                <h3>Profile and scope</h3>
                <p class="nurse-note">Loads the nurse profile that admin provisioned for this account, including department scope and Blood Bank access.</p>
                <div class="nurse-actions">
                    <button class="nurse-button soft" type="button" onclick="loadNurseProfile()">Reload profile</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-4">
                <h3>Department filters</h3>
                <p class="nurse-note">Filters admissions in your own department by status or search text.</p>
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

            <div id="regularNurseWorkArea" class="nurse-split" style="margin-top: 12px;">
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
            <p class="nurse-note">Visible only for nurses assigned to the Blood Bank department.</p>

            <div id="bloodBankLocked" class="nurse-note">Load your nurse profile first to see whether Blood Bank donor screening is available for this account.</div>
            <div id="bloodBankSection" style="display:none;">
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
                <div class="nurse-actions">
                    <button class="nurse-button primary" type="button" onclick="loadBloodBankDonors()">Load donors</button>
                    <button class="nurse-button soft" type="button" onclick="loadSelectedDonorHealthChecks()">Refresh selected donor history</button>
                </div>

                <div class="nurse-split" style="margin-top: 14px;">
                    <div class="nurse-panel nurse-col-5">
                        <h3>Blood Bank donor list</h3>
                        <div id="bloodBankDonorList" class="nurse-list"></div>
                    </div>

                    <div class="nurse-panel nurse-col-7">
                        <h3>Donor health check entry</h3>
                        <div id="bbSelectedDonor" class="nurse-summary-grid">
                            <div class="nurse-note">Select a donor card from the left list.</div>
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

        <div class="ll-hidden-debug" aria-hidden="true">
            <input id="nurseTokenInput" class="nurse-input" placeholder="Hidden nurse session input">
            <pre id="out" class="nurse-console"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const nursePanelIds = ['nurse-overview', 'nurse-monitoring', 'nurse-blood-bank'];
const nurseNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[href^="#nurse-"]'));

const state = {
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

function nurseMessage(data) {
    if (!data) return '';
    if (typeof data === 'string') return data;
    if (typeof data?.message === 'string') return data.message;
    if (typeof data?.error === 'string') return data.error;
    if (typeof data?.status === 'string') return data.status;
    return '';
}

function setNurseAlert(tone, title, body) {
    const root = document.getElementById('nurseActionAlert');
    root.classList.remove('ll-hidden-debug', 'is-success', 'is-warning', 'is-danger');
    root.classList.add(tone === 'danger' ? 'is-danger' : tone === 'warning' ? 'is-warning' : 'is-success');
    document.getElementById('nurseActionTitle').textContent = title;
    document.getElementById('nurseActionBody').textContent = body;
}

function refreshSessionState() {
    const hasSession = !!document.getElementById('nurseTokenInput').value.trim();
    const sessionAlert = document.getElementById('nurseSessionAlert');
    sessionAlert.classList.toggle('ll-hidden-debug', hasSession);
    document.getElementById('nurseSessionState').textContent = hasSession ? 'Ready' : 'Sign in needed';
    document.getElementById('nurseSessionCopy').textContent = hasSession
        ? 'Your nurse workspace is using the active session from sign-in. Refresh the profile any time you need the latest department context.'
        : 'Sign in first so the nurse workspace can load your profile, department scope, and bedside actions safely.';
    document.getElementById('nurseDepartmentState').textContent = state.nurse?.department || 'Waiting for profile';
}

function write(data, config = {}) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
    if (config.skipAlert) return;
    const status = Number(data?.status || 0);
    const tone = config.tone || (!status || status < 300 ? 'success' : status === 401 || status === 403 ? 'warning' : 'danger');
    const title = config.title || (tone === 'danger' ? 'Nurse action needs attention' : tone === 'warning' ? 'Nurse session check' : 'Nurse workspace updated');
    const body = config.body || nurseMessage(data?.data || data) || 'The nurse workspace completed the latest action.';
    setNurseAlert(tone, title, body);
}

function setActivePanel(panelId) {
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
            const panelId = (link.getAttribute('href') || '').replace('#', '');
            if (!nursePanelIds.includes(panelId)) return;
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        });
    });

    const initialHash = (window.location.hash || '').replace('#', '');
    const initialPanel = nursePanelIds.includes(initialHash) ? initialHash : nursePanelIds[0];
    setActivePanel(initialPanel);
}

async function maybeLoadPanelData(panelId) {
    if (!document.getElementById('nurseTokenInput').value.trim()) {
        return;
    }

    await loadNurseProfile({ force: false });

    if (panelId === 'nurse-monitoring' && state.nurse?.department !== 'Blood Bank') {
        await loadPatients({ force: false });
        return;
    }

    if (panelId === 'nurse-blood-bank' && state.nurse?.department === 'Blood Bank') {
        await loadBloodBankDonors({ force: false });
    }
}

function useStoredUserToken() {
    document.getElementById('nurseTokenInput').value = localStorage.getItem('USER_TOKEN') || '';
    refreshSessionState();
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
        return { status: 401, data: { message: `${tokenType} session missing. Sign in again before continuing.` } };
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
                <div class="nurse-item-meta" style="margin-top: 6px;">${latest}</div>
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
    const isBloodBank = state.nurse?.department === 'Blood Bank';
    document.getElementById('regularNurseSection').style.display = profileLoaded && isBloodBank ? 'none' : '';
    document.getElementById('regularNurseWorkArea').style.display = profileLoaded && isBloodBank ? 'none' : '';
    document.getElementById('bloodBankSection').style.display = isBloodBank ? '' : 'none';
    document.getElementById('bloodBankLocked').textContent = !profileLoaded
        ? 'Load your nurse profile first to see whether Blood Bank donor screening is available for this account.'
        : isBloodBank
            ? 'Blood Bank donor screening is enabled for this nurse profile.'
            : 'Blood Bank donor screening is available only when the nurse profile belongs to the Blood Bank department.';
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
            <div class="nurse-item-meta" style="margin-top: 6px;">
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
        return;
    }

    document.getElementById('bbDonorId').value = String(donor.donor_id);
    root.innerHTML = `
        <div class="nurse-summary"><small>Donor</small><strong>${escapeHtml(donor.full_name || '-')}</strong></div>
        <div class="nurse-summary"><small>Email</small><strong>${escapeHtml(donor.email || '-')}</strong></div>
        <div class="nurse-summary"><small>Donor ID</small><strong>#${Number(donor.donor_id)}</strong></div>
        <div class="nurse-summary"><small>Blood group</small><strong>${escapeHtml(donor.blood_group || '-')}</strong></div>
    `;
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
        refreshSessionState();
        renderBloodBankAccess();
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
    if (search) query.q = search;
    if (requestId) query.requestId = Number(requestId);
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
            if (state.selectedDonorId && !state.bloodBankDonors.some((entry) => Number(entry.donor_id) === Number(state.selectedDonorId))) {
                state.selectedDonorId = null;
                renderSelectedBloodBankDonor(null);
                renderBloodBankHealthChecks([]);
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

    if (!weight || !temp) {
        result.className = 'nurse-pill bed';
        result.textContent = 'Waiting for staff entry';
        return;
    }

    const eligible = weight >= 45 && temp >= 36.0 && temp <= 37.8 && (hb === null || hb >= 12.5);
    result.className = `nurse-pill ${eligible ? 'live' : 'off'}`;
    result.textContent = eligible ? 'Eligible by current values' : 'Not eligible by current values';
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
        previewEligibility();
        delete state.donorHealthChecksByDonor[donorId];
        state.bloodBankDonorsLoaded = false;
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
    renderBloodBankHealthChecks([]);
    useStoredUserToken();
    refreshSessionState();

    if (document.getElementById('nurseTokenInput').value.trim()) {
        await loadNurseProfile({ force: false });
    } else {
        write('Sign in to load your nurse profile and start the monitoring workspace.', {
            tone: 'warning',
            title: 'Nurse session required'
        });
    }
}

document.getElementById('bbWeightKg').addEventListener('input', previewEligibility);
document.getElementById('bbTemperatureC').addEventListener('input', previewEligibility);
document.getElementById('bbHemoglobin').addEventListener('input', previewEligibility);
bootNurseDashboard();
</script>
@endpush
