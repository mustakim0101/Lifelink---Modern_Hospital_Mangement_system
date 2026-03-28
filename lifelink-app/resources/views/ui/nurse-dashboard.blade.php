@extends('ui.layouts.app')

@section('title', 'Nurse Dashboard')
@section('workspace_label', 'Nurse monitoring workspace')
@section('hero_badge', 'Nurse Care')
@section('hero_title', 'Monitor department patients and log vital signs from one workspace.')
@section('hero_description', 'This dashboard is for the working nurse after admin has already approved the account and assigned the nurse profile to a department.')
@section('meta_title', 'Nurse Workflow')
@section('meta_copy', 'Department monitoring and bedside updates')

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
    <a class="is-active" href="/ui/nurse-dashboard">
        <strong>Nurse Dashboard</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/patient-portal">
        <strong>Patient Portal</strong>
        <span>Patient-side workflow</span>
    </a>
    <a href="/ui/doctor-dashboard">
        <strong>Doctor Dashboard</strong>
        <span>Clinical counterpart</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Role notes</strong>
        <p>Admin must finish nurse setup from the admin dashboard first. This page is only for the nurse's own daily work after that setup exists.</p>
    </div>
    <div class="app-shell__sidebar-card">
        <strong>Expected flow</strong>
        <p>Order: admin approves nurse application, admin assigns department, nurse logs in here, nurse loads department patients, nurse records vitals for admitted patients.</p>
    </div>
    <div class="app-shell__sidebar-card">
        <strong>Blood Bank rule</strong>
        <p>If this nurse belongs to the <code>Blood Bank</code> department, an extra donor screening section appears below for staff-entered donor health checks.</p>
    </div>
@endsection

@section('content')
    <div class="nurse-grid">
        <div class="nurse-split">
            <div class="nurse-panel nurse-col-4">
                <h3>Nurse session</h3>
                <p class="nurse-note">Use the logged-in nurse token here. If profile loading fails, it usually means admin has not finished nurse setup yet.</p>
                <label class="nurse-label" for="nurseTokenInput">Nurse token</label>
                <input id="nurseTokenInput" class="nurse-input" placeholder="Bearer token for nurse">
                <div class="nurse-actions">
                    <button class="nurse-button soft" type="button" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-4">
                <h3>What "load profile" means</h3>
                <p class="nurse-note">This checks the nurse profile that admin created for your account. The profile contains your department assignment, and that department is what limits which patients you can see here.</p>
                <div class="nurse-actions">
                    <button class="nurse-button soft" type="button" onclick="loadNurseProfile()">Reload profile</button>
                </div>
            </div>

            <div class="nurse-panel nurse-col-4">
                <h3>Department filters</h3>
                <p class="nurse-note">This does not filter other nurses. It filters the patient admissions inside your own assigned department so you can focus on admitted cases, discharged cases, or a specific patient/bed search.</p>
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

        <div id="regularNurseWorkArea" class="nurse-split">
            <div class="nurse-panel nurse-col-5">
                <h3>Patient monitoring list</h3>
                <p class="nurse-note">Select an admission to open monitoring detail, recent vitals, and linked records.</p>
                <div id="patientList" class="nurse-list"></div>
            </div>

            <div class="nurse-panel nurse-col-7">
                <h3>Admission monitor</h3>
                <p class="nurse-note">This is where you record temperature, pulse, blood pressure, respiration, and SpO2 for the selected admitted patient. Those values are saved into the nurse vital-sign log table for that admission.</p>

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

        <div class="nurse-panel">
            <h3>Blood Bank donor screening</h3>
            <p class="nurse-note">This section activates only for nurses assigned to the Blood Bank department. Regular nurses in other departments should keep seeing only patient-monitoring work.</p>

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

        <div class="nurse-panel">
            <h3>API response log</h3>
            <p class="nurse-note">Keeping raw request and response output visible while the role workflow is still being polished.</p>
            <pre id="out" class="nurse-console"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');

const state = {
    nurse: null,
    nurseProfileLoaded: false,
    patients: [],
    selectedAdmissionId: null,
    selectedPatientUserId: null,
    selectedDetail: null,
    bloodBankDonors: [],
    selectedDonorId: null,
};

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
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

async function loadNurseProfile() {
    const result = await call('/nurse/profile');
    state.nurseProfileLoaded = true;
    if (result.status < 300 && result.data?.nurse) {
        state.nurse = result.data.nurse;
    } else {
        state.nurse = null;
    }
    renderBloodBankAccess();
    write(result);

    if (result.status < 300 && state.nurse?.department !== 'Blood Bank') {
        await loadPatients();
    }

    if (result.status < 300 && state.nurse?.department === 'Blood Bank') {
        await loadBloodBankDonors();
    }
}

async function loadPatients() {
    const status = document.getElementById('statusFilter').value.trim();
    const queryValue = document.getElementById('queryFilter').value.trim();
    const query = {};
    if (status) query.status = status;
    if (queryValue) query.q = queryValue;

    const result = await call('/nurse/patients', 'GET', null, 'nurse', query);
    if (result.status < 300) {
        state.patients = Array.isArray(result.data?.patients) ? result.data.patients : [];
        renderStats(result.data?.stats || null);
        renderPatients();
    } else {
        state.patients = [];
        renderStats(null);
        renderPatients();
    }
    write(result);
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
        await loadAdmissionDetail();
        await loadPatients();
    }
}

async function loadBloodBankDonors() {
    const query = {};
    const search = document.getElementById('bbDonorQuery').value.trim();
    const requestId = document.getElementById('bbRequestId').value.trim();
    if (search) query.q = search;
    if (requestId) query.requestId = Number(requestId);

    const result = await call('/nurse/blood-bank/donors', 'GET', null, 'nurse', query);
    if (result.status < 300) {
        state.bloodBankDonors = Array.isArray(result.data?.donors) ? result.data.donors : [];
        renderBloodBankDonors();
        if (state.bloodBankDonors.length && !state.selectedDonorId) {
            selectBloodBankDonor(state.bloodBankDonors[0].donor_id);
        }
    }
    write(result);
}

function selectBloodBankDonor(donorId) {
    state.selectedDonorId = Number(donorId);
    renderBloodBankDonors();
    const donor = state.bloodBankDonors.find((entry) => Number(entry.donor_id) === Number(donorId)) || null;
    renderSelectedBloodBankDonor(donor);
    loadSelectedDonorHealthChecks();
}

async function loadSelectedDonorHealthChecks() {
    const donorId = Number(document.getElementById('bbDonorId').value || state.selectedDonorId || 0);
    if (!donorId) {
        renderBloodBankHealthChecks([]);
        return;
    }

    const result = await call(`/nurse/blood-bank/donors/${donorId}/health-checks`, 'GET', null, 'nurse', { limit: 12 });
    if (result.status < 300) {
        renderBloodBankHealthChecks(Array.isArray(result.data?.health_checks) ? result.data.health_checks : []);
    }
    write(result);
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
        await loadSelectedDonorHealthChecks();
        await loadBloodBankDonors();
    }
}

async function bootNurseDashboard() {
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

    if (document.getElementById('nurseTokenInput').value.trim()) {
        await loadNurseProfile();
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
