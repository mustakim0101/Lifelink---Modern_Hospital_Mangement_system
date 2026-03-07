<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Nurse Care Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

        :root {
            --bg-a: #eef6ff;
            --bg-b: #fef8ee;
            --ink: #14233a;
            --muted: #5f718c;
            --card: rgba(255, 255, 255, 0.82);
            --line: rgba(20, 35, 58, 0.12);
            --teal: #0d9488;
            --teal-dark: #0b746b;
            --orange: #f97316;
            --alert: #dc2626;
            --ok: #16a34a;
            --shadow: 0 18px 35px rgba(16, 29, 57, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "Plus Jakarta Sans", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at 18% 12%, rgba(13, 148, 136, 0.18), transparent 48%),
                radial-gradient(circle at 82% 10%, rgba(249, 115, 22, 0.16), transparent 44%),
                linear-gradient(145deg, var(--bg-a), var(--bg-b));
        }

        h1, h2, h3, h4 {
            margin: 0;
            font-family: "Space Grotesk", "Century Gothic", sans-serif;
            letter-spacing: -0.01em;
        }

        .wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 22px 16px 34px;
        }

        .topline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .topline a {
            text-decoration: none;
            color: var(--teal-dark);
            font-weight: 700;
            font-size: 14px;
        }

        .status-pill {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(13, 148, 136, 0.12);
            color: var(--teal-dark);
            font-size: 12px;
            font-weight: 700;
        }

        .hero {
            margin-bottom: 16px;
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.6));
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: var(--shadow);
            animation: rise 0.45s ease both;
        }

        .hero h1 { font-size: clamp(22px, 3.1vw, 32px); }
        .hero p { margin: 8px 0 0; color: var(--muted); max-width: 760px; line-height: 1.45; font-size: 14px; }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(12, minmax(0, 1fr));
        }

        .card {
            border: 1px solid var(--line);
            background: var(--card);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 14px;
            animation: rise 0.5s ease both;
        }

        .card h3 { font-size: 17px; margin-bottom: 6px; }
        .hint { color: var(--muted); font-size: 12px; line-height: 1.35; margin: 0; }

        .col-4 { grid-column: span 4; }
        .col-5 { grid-column: span 5; }
        .col-7 { grid-column: span 7; }
        .col-12 { grid-column: span 12; }

        .control-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid rgba(20, 35, 58, 0.2);
            background: rgba(255, 255, 255, 0.85);
            border-radius: 11px;
            padding: 10px 11px;
            font: inherit;
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .btn-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        button {
            border: 0;
            border-radius: 12px;
            padding: 10px 13px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.14s ease, box-shadow 0.14s ease, background 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(16, 29, 57, 0.16);
        }

        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: var(--teal-dark); }
        .btn-soft { background: rgba(20, 35, 58, 0.1); color: var(--ink); }
        .btn-orange { background: var(--orange); color: #fff; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .stat {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.86);
            border-radius: 12px;
            padding: 10px;
            text-align: center;
        }

        .stat .num {
            font-family: "Space Grotesk", "Century Gothic", sans-serif;
            font-size: 21px;
            font-weight: 700;
        }

        .stat .lbl {
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .patient-list {
            margin-top: 10px;
            display: grid;
            gap: 8px;
            max-height: 470px;
            overflow: auto;
            padding-right: 4px;
        }

        .patient-item {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 10px;
            cursor: pointer;
            transition: border-color 0.2s ease, transform 0.12s ease;
        }

        .patient-item:hover {
            border-color: rgba(13, 148, 136, 0.45);
            transform: translateY(-1px);
        }

        .patient-item.active {
            border-color: var(--teal);
            background: rgba(13, 148, 136, 0.07);
        }

        .patient-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
            margin-bottom: 5px;
        }

        .patient-name { font-size: 15px; font-weight: 700; }

        .tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .tag.live { background: rgba(22, 163, 74, 0.14); color: var(--ok); }
        .tag.off { background: rgba(220, 38, 38, 0.13); color: var(--alert); }
        .tag.bed { background: rgba(13, 148, 136, 0.14); color: var(--teal-dark); }

        .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 6px;
        }

        .mini {
            border-radius: 8px;
            background: rgba(20, 35, 58, 0.08);
            color: var(--ink);
            font-size: 11px;
            padding: 3px 7px;
        }

        .section-title {
            margin-top: 12px;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .summary {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 9px;
        }

        .summary small { display: block; color: var(--muted); font-size: 11px; margin-bottom: 2px; }
        .summary strong { font-size: 13px; line-height: 1.25; word-break: break-word; }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.85);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid rgba(20, 35, 58, 0.09);
            text-align: left;
            white-space: nowrap;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: rgba(246, 250, 255, 0.95);
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        pre {
            margin: 10px 0 0;
            min-height: 130px;
            max-height: 280px;
            overflow: auto;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #11203a;
            color: #d7e3ff;
            padding: 11px;
            font-size: 12px;
        }

        .line-break { border-top: 1px dashed rgba(20, 35, 58, 0.18); margin: 10px 0; }

        @keyframes rise {
            from { opacity: 0; transform: translateY(8px) scale(0.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @media (max-width: 1050px) {
            .col-4, .col-5, .col-7 { grid-column: span 12; }
            .stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 700px) {
            .control-grid, .summary-grid { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .btn-row button { flex: 1 1 100%; }
            .topline { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="topline">
        <a href="/ui"><- Back to UI Home</a>
        <div class="status-pill">Phase 5 Issue 14: Nurse Care Dashboard</div>
    </div>

    <section class="hero">
        <h1>Nurse Department Monitor</h1>
        <p>Track department admissions, view bed placement quickly, and log patient vital signs in a focused single screen.</p>
    </section>

    <section class="grid">
        <div class="card col-4">
            <h3>Token Context</h3>
            <p class="hint">Use <code>ADMIN_TOKEN</code> to configure nurse profiles and <code>USER_TOKEN</code> for nurse actions.</p>
            <div class="control-grid">
                <div>
                    <label for="adminTokenInput">Admin token</label>
                    <input id="adminTokenInput" placeholder="Bearer token for admin">
                </div>
                <div>
                    <label for="nurseTokenInput">Nurse token</label>
                    <input id="nurseTokenInput" placeholder="Bearer token for nurse">
                </div>
            </div>
            <div class="btn-row">
                <button class="btn-soft" onclick="useStoredAdminToken()">Use ADMIN_TOKEN</button>
                <button class="btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
            </div>
        </div>

        <div class="card col-4">
            <h3>Admin Setup Nurse Profile</h3>
            <p class="hint">A user must already have <code>Nurse</code> role before profile setup.</p>
            <div class="control-grid">
                <div>
                    <label for="nurseUserId">Nurse user ID</label>
                    <input id="nurseUserId" type="number" placeholder="e.g. 17">
                </div>
                <div>
                    <label for="nurseDepartmentId">Department ID</label>
                    <input id="nurseDepartmentId" type="number" placeholder="e.g. 1">
                </div>
            </div>
            <label for="wardAssignmentNote">Ward assignment note</label>
            <input id="wardAssignmentNote" placeholder="Optional ward / shift note">
            <div class="btn-row">
                <button class="btn-primary" onclick="upsertNurseProfile()">Upsert Nurse Profile</button>
            </div>
        </div>

        <div class="card col-4">
            <h3>Nurse Filters</h3>
            <p class="hint">Use quick filters to monitor only relevant admissions.</p>
            <div class="control-grid">
                <div>
                    <label for="statusFilter">Admission status</label>
                    <select id="statusFilter">
                        <option value="">All</option>
                        <option value="Admitted">Admitted</option>
                        <option value="Discharged">Discharged</option>
                        <option value="Transferred">Transferred</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="queryFilter">Search</label>
                    <input id="queryFilter" placeholder="Name, email, diagnosis, bed code">
                </div>
            </div>
            <div class="btn-row">
                <button class="btn-soft" onclick="loadNurseProfile()">Load Profile</button>
                <button class="btn-primary" onclick="loadPatients()">Refresh Patients</button>
            </div>
        </div>

        <div class="card col-12">
            <h3>Department Snapshot</h3>
            <div class="stat-grid">
                <div class="stat"><div class="num" id="stTotal">0</div><div class="lbl">Total</div></div>
                <div class="stat"><div class="num" id="stActive">0</div><div class="lbl">Admitted</div></div>
                <div class="stat"><div class="num" id="stBed">0</div><div class="lbl">Has Bed</div></div>
                <div class="stat"><div class="num" id="stNoBed">0</div><div class="lbl">No Bed</div></div>
                <div class="stat"><div class="num" id="stMonitored">0</div><div class="lbl">Monitored 24h</div></div>
            </div>
        </div>

        <div class="card col-5">
            <h3>Patient Monitoring List</h3>
            <p class="hint">Click a patient admission to open full monitoring details on the right.</p>
            <div id="patientList" class="patient-list"></div>
        </div>

        <div class="card col-7">
            <h3>Admission Monitor</h3>
            <p class="hint">Selected admission details, latest vitals, and quick record access.</p>

            <div class="section-title">Selected Admission</div>
            <div id="admissionSummary" class="summary-grid"></div>

            <div class="line-break"></div>

            <div class="section-title">Log Vital Signs</div>
            <div class="control-grid">
                <div>
                    <label for="vAdmissionId">Admission ID</label>
                    <input id="vAdmissionId" type="number" placeholder="auto-filled on selection">
                </div>
                <div>
                    <label for="vPatientUserId">Patient User ID</label>
                    <input id="vPatientUserId" type="number" placeholder="auto-filled on selection">
                </div>
            </div>
            <div class="control-grid">
                <div>
                    <label for="vTemp">Temperature (C)</label>
                    <input id="vTemp" type="number" step="0.1" placeholder="37.2">
                </div>
                <div>
                    <label for="vPulse">Pulse (bpm)</label>
                    <input id="vPulse" type="number" placeholder="76">
                </div>
            </div>
            <div class="control-grid">
                <div>
                    <label for="vSys">Systolic BP</label>
                    <input id="vSys" type="number" placeholder="120">
                </div>
                <div>
                    <label for="vDia">Diastolic BP</label>
                    <input id="vDia" type="number" placeholder="80">
                </div>
            </div>
            <div class="control-grid">
                <div>
                    <label for="vResp">Respiration</label>
                    <input id="vResp" type="number" placeholder="16">
                </div>
                <div>
                    <label for="vSpo2">SpO2 (%)</label>
                    <input id="vSpo2" type="number" placeholder="98">
                </div>
            </div>
            <label for="vNote">Note</label>
            <textarea id="vNote" placeholder="Optional notes for this vital-sign entry"></textarea>
            <div class="btn-row">
                <button class="btn-orange" onclick="logVitals()">Save Vital Signs</button>
                <button class="btn-soft" onclick="loadSelectedAdmissionVitals()">Refresh Vitals</button>
            </div>

            <div class="section-title">Recent Vitals</div>
            <div class="table-wrap">
                <table>
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

            <div class="section-title">Recent Medical Records</div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Datetime</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody id="recordsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="card col-12">
            <h3>API Response Log</h3>
            <p class="hint">Latest request/response is shown here for quick debugging.</p>
            <pre id="out"></pre>
        </div>
    </section>
</div>

<script>
const API = '/api';
const out = document.getElementById('out');

const state = {
    nurse: null,
    patients: [],
    selectedAdmissionId: null,
    selectedPatientUserId: null,
    selectedDetail: null
};

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function useStoredAdminToken() {
    document.getElementById('adminTokenInput').value = localStorage.getItem('ADMIN_TOKEN') || '';
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
    const token = tokenType === 'admin'
        ? document.getElementById('adminTokenInput').value.trim()
        : document.getElementById('nurseTokenInput').value.trim();

    if (!token) {
        return { status: 401, data: { message: `${tokenType} token missing` } };
    }

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    const res = await fetch(buildUrl(path, query), {
        method,
        headers,
        body: body ? JSON.stringify(body) : undefined
    });

    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

function badgeForStatus(status) {
    return status === 'Admitted'
        ? '<span class="tag live">Admitted</span>'
        : `<span class="tag off">${escapeHtml(status || 'Unknown')}</span>`;
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

function renderPatients() {
    const holder = document.getElementById('patientList');
    if (!state.patients.length) {
        holder.innerHTML = '<div class="hint">No admissions found for this filter.</div>';
        return;
    }

    holder.innerHTML = state.patients.map((p) => {
        const active = Number(state.selectedAdmissionId) === Number(p.id) ? 'active' : '';
        const bed = p.active_bed_assignment
            ? `<span class="tag bed">${escapeHtml(p.active_bed_assignment.bed_code || 'Assigned')}</span>`
            : '<span class="mini">No bed assigned</span>';

        const latest = p.latest_vital_sign
            ? `<span class="mini">Last vitals: ${new Date(p.latest_vital_sign.measured_at).toLocaleString()}</span>`
            : '<span class="mini">No vitals yet</span>';

        return `
            <article class="patient-item ${active}" onclick="selectAdmission(${Number(p.id)}, ${Number(p.patient_user_id)})">
                <div class="patient-head">
                    <div class="patient-name">${escapeHtml(p.patient_name || 'Unknown Patient')}</div>
                    ${badgeForStatus(p.status)}
                </div>
                <div class="hint">${escapeHtml(p.diagnosis || 'No diagnosis')}</div>
                <div class="meta-row">
                    <span class="mini">Admission #${Number(p.id)}</span>
                    <span class="mini">${escapeHtml(p.care_level_assigned || p.care_level_requested || 'Care TBD')}</span>
                    ${bed}
                </div>
                <div class="meta-row">${latest}</div>
            </article>
        `;
    }).join('');
}

function renderAdmissionSummary(admission) {
    const root = document.getElementById('admissionSummary');
    if (!admission) {
        root.innerHTML = '<div class="hint">Select an admission from the left panel.</div>';
        return;
    }

    root.innerHTML = `
        <div class="summary"><small>Patient</small><strong>${escapeHtml(admission.patient_name || '-')}</strong></div>
        <div class="summary"><small>Blood Group</small><strong>${escapeHtml(admission.blood_group || '-')}</strong></div>
        <div class="summary"><small>Department</small><strong>${escapeHtml(admission.department || '-')}</strong></div>
        <div class="summary"><small>Status</small><strong>${escapeHtml(admission.status || '-')}</strong></div>
        <div class="summary"><small>Diagnosis</small><strong>${escapeHtml(admission.diagnosis || '-')}</strong></div>
        <div class="summary"><small>Bed</small><strong>${escapeHtml(admission.active_bed_assignment?.bed_code || 'Not assigned')}</strong></div>
        <div class="summary"><small>Unit</small><strong>${escapeHtml(admission.active_bed_assignment?.unit_type || admission.care_level_requested || '-')}</strong></div>
        <div class="summary"><small>Admit Date</small><strong>${admission.admit_date ? new Date(admission.admit_date).toLocaleString() : '-'}</strong></div>
    `;
}

function renderVitals(vitals = []) {
    const body = document.getElementById('vitalsBody');
    if (!vitals.length) {
        body.innerHTML = '<tr><td colspan="7">No vital records yet.</td></tr>';
        return;
    }

    body.innerHTML = vitals.map((v) => `
        <tr>
            <td>${v.measured_at ? new Date(v.measured_at).toLocaleString() : '-'}</td>
            <td>${v.temperature_c ?? '-'}</td>
            <td>${v.pulse_bpm ?? '-'}</td>
            <td>${v.systolic_bp && v.diastolic_bp ? `${v.systolic_bp}/${v.diastolic_bp}` : '-'}</td>
            <td>${v.respiration_rate ?? '-'}</td>
            <td>${v.spo2_percent ?? '-'}</td>
            <td>${escapeHtml(v.nurse_name || '-')}</td>
        </tr>
    `).join('');
}

function renderRecords(records = []) {
    const body = document.getElementById('recordsBody');
    if (!records.length) {
        body.innerHTML = '<tr><td colspan="4">No recent records.</td></tr>';
        return;
    }

    body.innerHTML = records.map((r) => `
        <tr>
            <td>${r.record_datetime ? new Date(r.record_datetime).toLocaleString() : '-'}</td>
            <td>${escapeHtml(r.diagnosis || '-')}</td>
            <td>${escapeHtml(r.treatment_plan || '-')}</td>
            <td>${escapeHtml(r.created_by || '-')}</td>
        </tr>
    `).join('');
}

async function upsertNurseProfile() {
    const payload = {
        userId: Number(document.getElementById('nurseUserId').value),
        departmentId: Number(document.getElementById('nurseDepartmentId').value),
        wardAssignmentNote: document.getElementById('wardAssignmentNote').value.trim() || null
    };
    const res = await call('/admin/nurses/profile', 'POST', payload, 'admin');
    write(res);
}

async function loadNurseProfile() {
    const res = await call('/nurse/profile');
    if (res.status < 300 && res.data?.nurse) {
        state.nurse = res.data.nurse;
    }
    write(res);
}

async function loadPatients() {
    const status = document.getElementById('statusFilter').value.trim();
    const q = document.getElementById('queryFilter').value.trim();
    const query = {};
    if (status) query.status = status;
    if (q) query.q = q;

    const res = await call('/nurse/patients', 'GET', null, 'nurse', query);
    if (res.status < 300) {
        state.patients = Array.isArray(res.data?.patients) ? res.data.patients : [];
        renderStats(res.data?.stats || null);
        renderPatients();
    } else {
        state.patients = [];
        renderStats(null);
        renderPatients();
    }
    write(res);
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

    const res = await call(`/nurse/admissions/${state.selectedAdmissionId}`, 'GET', null, 'nurse', { vitalsLimit: 10, recordsLimit: 10 });
    if (res.status < 300) {
        state.selectedDetail = res.data;
        renderAdmissionSummary(res.data?.admission || null);
        renderVitals(res.data?.vital_sign_logs || []);
        renderRecords(res.data?.medical_records || []);
    }
    write(res);
}

async function loadSelectedAdmissionVitals() {
    const admissionId = Number(document.getElementById('vAdmissionId').value);
    if (!admissionId) {
        write({ status: 422, data: { message: 'Admission ID required for vital refresh.' } });
        return;
    }

    const res = await call(`/nurse/admissions/${admissionId}/vitals`, 'GET', null, 'nurse', { limit: 10 });
    if (res.status < 300) {
        renderVitals(res.data?.vital_sign_logs || []);
    }
    write(res);
}

function maybeNumber(id) {
    const value = document.getElementById(id).value.trim();
    if (!value) return null;
    const n = Number(value);
    return Number.isFinite(n) ? n : null;
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
        note: document.getElementById('vNote').value.trim() || null
    };

    const res = await call(`/nurse/admissions/${admissionId}/vitals`, 'POST', payload, 'nurse');
    write(res);

    if (res.status < 300) {
        await loadAdmissionDetail();
        await loadPatients();
    }
}

renderStats(null);
renderPatients();
renderAdmissionSummary(null);
renderVitals([]);
renderRecords([]);
useStoredAdminToken();
useStoredUserToken();
</script>
</body>
</html>
