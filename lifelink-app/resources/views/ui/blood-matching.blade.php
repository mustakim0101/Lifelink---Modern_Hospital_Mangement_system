<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Blood Matching Center</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap');

        :root {
            --ink: #0f172a;
            --muted: #475569;
            --line: rgba(15, 23, 42, 0.12);
            --card: rgba(255, 255, 255, 0.9);
            --shell: #f8fafc;
            --primary: #0369a1;
            --primary-strong: #075985;
            --accent: #ea580c;
            --success: #166534;
            --warn: #9a3412;
            --danger: #b91c1c;
            --shadow: 0 18px 38px rgba(2, 6, 23, 0.14);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Manrope", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 0%, rgba(56, 189, 248, 0.22), transparent 36%),
                radial-gradient(circle at 88% 8%, rgba(251, 146, 60, 0.2), transparent 34%),
                linear-gradient(165deg, #f0f9ff 0%, #fff7ed 48%, #f8fafc 100%);
        }

        h1, h2, h3 {
            margin: 0;
            font-family: "Sora", "Segoe UI", sans-serif;
            letter-spacing: -0.01em;
        }

        .shell {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 12px 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .topbar a {
            text-decoration: none;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
        }

        .chip {
            border-radius: 999px;
            background: rgba(3, 105, 161, 0.14);
            color: var(--primary-strong);
            font-size: 12px;
            font-weight: 800;
            padding: 7px 12px;
        }

        .hero {
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.68));
            box-shadow: var(--shadow);
            padding: 16px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .hero p {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 14px;
            max-width: 860px;
        }

        .hero-clock { text-align: right; min-width: 170px; }
        .hero-clock strong { display: block; font-size: 24px; }
        .hero-clock small { color: var(--muted); font-size: 12px; }

        .layout {
            display: grid;
            grid-template-columns: 310px minmax(0, 1fr);
            gap: 12px;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--card);
            box-shadow: var(--shadow);
        }

        .sidebar {
            padding: 12px;
            position: sticky;
            top: 12px;
            height: fit-content;
            display: grid;
            gap: 10px;
        }

        .main {
            padding: 10px;
            display: grid;
            gap: 10px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px;
        }

        .hint {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .split {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 10px;
        }

        label {
            display: block;
            margin: 0 0 5px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input, select, textarea {
            width: 100%;
            border-radius: 10px;
            border: 1px solid rgba(15, 23, 42, 0.2);
            background: rgba(255, 255, 255, 0.97);
            color: var(--ink);
            font: inherit;
            padding: 9px 10px;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.14);
        }

        textarea { min-height: 82px; resize: vertical; }

        .btns {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 9px;
        }

        button {
            border: 0;
            border-radius: 10px;
            padding: 9px 12px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        button[disabled] { opacity: 0.62; pointer-events: none; }

        .btn-main { background: var(--primary); color: #fff; }
        .btn-main:hover { background: var(--primary-strong); }
        .btn-soft { background: rgba(15, 23, 42, 0.1); color: var(--ink); }
        .btn-accent { background: var(--accent); color: #fff; }

        .stats {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .stat {
            border: 1px solid var(--line);
            border-radius: 10px;
            text-align: center;
            padding: 9px;
            background: rgba(255, 255, 255, 0.95);
        }

        .stat .num {
            font-family: "Sora", "Segoe UI", sans-serif;
            font-size: 20px;
            font-weight: 700;
        }

        .stat .lbl {
            color: var(--muted);
            font-size: 11px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge.ok { color: var(--success); background: rgba(22, 101, 52, 0.14); }
        .badge.warn { color: var(--warn); background: rgba(154, 52, 18, 0.15); }
        .badge.pending { color: var(--primary-strong); background: rgba(3, 105, 161, 0.15); }
        .badge.danger { color: var(--danger); background: rgba(185, 28, 28, 0.13); }

        .table-wrap {
            margin-top: 8px;
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: auto;
            background: rgba(255, 255, 255, 0.95);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            text-align: left;
            white-space: nowrap;
            padding: 8px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.1);
        }

        th {
            position: sticky;
            top: 0;
            background: rgba(248, 250, 252, 0.98);
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr.clickable { cursor: pointer; }
        tr.clickable:hover { background: rgba(14, 165, 233, 0.08); }
        tr.active-request { background: rgba(59, 130, 246, 0.14); }

        .cards {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 8px;
        }

        .donor-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.96);
            padding: 10px;
            display: grid;
            gap: 6px;
        }

        .donor-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
        }

        .donor-name {
            font-size: 13px;
            font-weight: 800;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        pre {
            margin: 0;
            min-height: 110px;
            max-height: 250px;
            overflow: auto;
            border-radius: 11px;
            border: 1px solid var(--line);
            background: #0f1f3b;
            color: #d7e3ff;
            padding: 10px;
            font-size: 12px;
        }

        .toast-stack {
            position: fixed;
            right: 12px;
            bottom: 12px;
            display: grid;
            gap: 8px;
            z-index: 30;
        }

        .toast {
            border-radius: 9px;
            padding: 9px 11px;
            color: #fff;
            font-size: 12px;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.3);
        }

        .toast.ok { background: #166534; }
        .toast.error { background: #b91c1c; }

        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .split { grid-template-columns: 1fr; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 860px) {
            .hero { flex-direction: column; align-items: flex-start; }
            .hero-clock { text-align: left; }
            .row { grid-template-columns: 1fr; }
            .stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="topbar">
        <a href="/ui"><- Back to UI Home</a>
        <div class="chip">Phase 6 Issue 18: Blood Matching + Donor Notifications</div>
    </div>

    <section class="hero">
        <div>
            <h1>Blood Matching Center</h1>
            <p>IT worker flow: filter pending requests, review compatibility-scored donors, notify selected donors, and monitor acceptance/decline responses in real time.</p>
        </div>
        <div class="hero-clock">
            <strong id="clockNow">--:--</strong>
            <small>Local matching dashboard time</small>
        </div>
    </section>

    <section class="layout">
        <aside class="panel sidebar">
            <div class="card">
                <h3>Auth Context</h3>
                <p class="hint">Use <code>Admin</code> or <code>ITWorker</code> bearer token.</p>
                <label for="tokenInput">Bearer token</label>
                <input id="tokenInput" placeholder="Paste token">
                <div class="btns">
                    <button class="btn-soft" onclick="useStoredAdminToken()">Use ADMIN_TOKEN</button>
                    <button class="btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    <button id="btnRefreshAll" class="btn-main" onclick="refreshAll()">Refresh All</button>
                </div>
            </div>

            <div class="card">
                <h3>Request Filters</h3>
                <div class="row">
                    <div>
                        <label for="statusFilter">Status</label>
                        <select id="statusFilter">
                            <option value="">All</option>
                            <option>Pending</option>
                            <option>Matched</option>
                            <option>Approved</option>
                            <option>Fulfilled</option>
                            <option>Rejected</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="bloodGroupFilter">Blood group</label>
                        <select id="bloodGroupFilter">
                            <option value="">All</option>
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="departmentFilter">Department ID (optional)</label>
                        <input id="departmentFilter" type="number" min="1" placeholder="e.g. 1">
                    </div>
                    <div>
                        <label for="requestLimit">Limit</label>
                        <input id="requestLimit" type="number" min="1" max="150" value="40">
                    </div>
                </div>
                <div class="btns">
                    <button id="btnLoadRequests" class="btn-main" onclick="loadRequests()">Load Requests</button>
                </div>
            </div>

            <div class="card">
                <h3>Notify Setup</h3>
                <label for="notifyLimit">Auto suggest limit</label>
                <input id="notifyLimit" type="number" min="1" max="30" value="6">
                <label for="notifyTitle" style="margin-top:8px;">Custom title (optional)</label>
                <input id="notifyTitle" placeholder="Blood request needs your response">
                <label for="notifyMessage" style="margin-top:8px;">Custom message (optional)</label>
                <textarea id="notifyMessage" placeholder="Leave empty to use default request message"></textarea>
                <div class="btns">
                    <button id="btnNotifySelected" class="btn-accent" onclick="notifySelected()">Notify Selected Donors</button>
                    <button id="btnNotifyAuto" class="btn-main" onclick="notifyAuto()">Auto Notify Best Matches</button>
                </div>
            </div>
        </aside>

        <main class="panel main">
            <div class="card">
                <h3>Matching Snapshot</h3>
                <div class="stats">
                    <div class="stat"><div class="num" id="stRequests">0</div><div class="lbl">Requests Loaded</div></div>
                    <div class="stat"><div class="num" id="stPending">0</div><div class="lbl">Pending</div></div>
                    <div class="stat"><div class="num" id="stMatched">0</div><div class="lbl">Matched</div></div>
                    <div class="stat"><div class="num" id="stSelected">0</div><div class="lbl">Selected Donors</div></div>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Blood Requests</h3>
                    <p class="hint">Select a request row to load donor suggestions and match history.</p>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>ID</th><th>Patient</th><th>Dept</th><th>Need</th><th>Units</th><th>Status</th><th>Inventory</th><th>Accepted</th>
                            </tr>
                            </thead>
                            <tbody id="requestsBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <h3>Selected Request</h3>
                    <p class="hint" id="selectedHint">Pick a request to start donor matching.</p>
                    <div id="selectedMeta" class="muted">No request selected.</div>
                </div>
            </div>

            <div class="card">
                <h3>Compatible Donor Suggestions</h3>
                <p class="hint">Donors are scored by compatibility + availability + donation freshness.</p>
                <div class="cards" id="suggestionsGrid"></div>
            </div>

            <div class="card">
                <h3>Match Timeline</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Match ID</th><th>Donor</th><th>Group</th><th>Compatibility</th><th>Score</th><th>Status</th><th>Notified At</th><th>Responded At</th>
                        </tr>
                        </thead>
                        <tbody id="matchesBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3>API Response</h3>
                <pre id="out"></pre>
            </div>
        </main>
    </section>
</div>

<div class="toast-stack" id="toastStack"></div>

<script>
const API = '/api';
const out = document.getElementById('out');

let requestsCache = [];
let selectedRequestId = null;
let selectedDonorIds = new Set();

function byId(id) { return document.getElementById(id); }

function write(value) {
    out.textContent = typeof value === 'string' ? value : JSON.stringify(value, null, 2);
}

function html(value) {
    if (value === null || value === undefined) return '';
    return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
}

function toast(message, type = 'ok') {
    const el = document.createElement('div');
    el.className = `toast ${type === 'error' ? 'error' : 'ok'}`;
    el.textContent = message;
    byId('toastStack').appendChild(el);
    setTimeout(() => el.remove(), 2600);
}

function setBusy(id, busy) {
    const btn = byId(id);
    if (!btn) return;
    btn.disabled = busy;
    btn.dataset.label = btn.dataset.label || btn.textContent;
    btn.textContent = busy ? 'Working...' : btn.dataset.label;
}

function useStoredAdminToken() { byId('tokenInput').value = localStorage.getItem('ADMIN_TOKEN') || ''; }
function useStoredUserToken() { byId('tokenInput').value = localStorage.getItem('USER_TOKEN') || ''; }

function updateClock() {
    byId('clockNow').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function statusBadge(status) {
    if (status === 'Accepted' || status === 'Completed' || status === 'Fulfilled') return '<span class="badge ok">' + html(status) + '</span>';
    if (status === 'Pending' || status === 'Matched' || status === 'Notified' || status === 'Approved') return '<span class="badge pending">' + html(status) + '</span>';
    if (status === 'Declined' || status === 'Rejected' || status === 'Cancelled') return '<span class="badge danger">' + html(status) + '</span>';
    return '<span class="badge warn">' + html(status || '-') + '</span>';
}

async function call(path, method = 'GET', body = null, query = null) {
    const token = byId('tokenInput').value.trim();
    if (!token) return { status: 401, data: { message: 'Token missing. Paste token first.' } };

    const q = query ? new URLSearchParams(query).toString() : '';
    const endpoint = `${API}${path}${q ? `?${q}` : ''}`;
    const response = await fetch(endpoint, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        body: body ? JSON.stringify(body) : undefined,
    });
    const text = await response.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: response.status, data };
}

function selectedRequest() {
    return requestsCache.find((row) => row.id === selectedRequestId) || null;
}

function updateSelectedStats() {
    byId('stSelected').textContent = selectedDonorIds.size;
}

function renderSnapshot(rows) {
    byId('stRequests').textContent = rows.length;
    byId('stPending').textContent = rows.filter((r) => r.status === 'Pending').length;
    byId('stMatched').textContent = rows.filter((r) => r.status === 'Matched').length;
}

function renderRequests(rows) {
    requestsCache = rows;
    renderSnapshot(rows);

    byId('requestsBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr class="clickable ${selectedRequestId === row.id ? 'active-request' : ''}" onclick="selectRequest(${row.id})">
                <td>#${row.id}</td>
                <td>${html(row.patient_name || '-')}</td>
                <td>${html(row.department_name || '-')}</td>
                <td>${html(row.blood_group_needed)} / ${html(row.component_type)}</td>
                <td>${row.units_required}</td>
                <td>${statusBadge(row.status)}</td>
                <td>${row.available_units}</td>
                <td>${row.accepted_count}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="8">No requests found for current filter.</td></tr>';
}

function renderSelectedMeta() {
    const req = selectedRequest();
    if (!req) {
        byId('selectedHint').textContent = 'Pick a request to start donor matching.';
        byId('selectedMeta').textContent = 'No request selected.';
        return;
    }

    byId('selectedHint').textContent = `Request #${req.id} selected. Use "Notify Selected" or "Auto Notify".`;
    byId('selectedMeta').innerHTML = `
        <strong>#${req.id}</strong> | ${html(req.blood_group_needed)} ${html(req.component_type)} | Units ${req.units_required}<br>
        Patient: ${html(req.patient_name || '-')} | Department: ${html(req.department_name || '-')}<br>
        Current status: ${statusBadge(req.status)} | Available units: <strong>${req.available_units}</strong>
    `;
}

function renderSuggestions(rows) {
    const grid = byId('suggestionsGrid');
    if (!rows.length) {
        grid.innerHTML = '<div class="muted">No compatible, available donors found for this request right now.</div>';
        return;
    }

    grid.innerHTML = rows.map((row) => {
        const checked = selectedDonorIds.has(row.donor_id) ? 'checked' : '';
        const existing = row.existing_match_status ? `Existing: ${row.existing_match_status}` : 'New match';
        return `
            <article class="donor-card">
                <div class="donor-top">
                    <label style="display:flex;align-items:center;gap:6px;margin:0;text-transform:none;font-size:12px;letter-spacing:0;">
                        <input type="checkbox" ${checked} onchange="toggleDonor(${row.donor_id}, this.checked)">
                        <span class="donor-name">${html(row.donor_name || `Donor #${row.donor_id}`)}</span>
                    </label>
                    ${statusBadge(row.compatibility_label)}
                </div>
                <div class="muted">${html(row.donor_email || '-')}</div>
                <div class="muted">Group: <strong>${html(row.donor_blood_group)}</strong> | Score: <strong>${Number(row.match_score || 0).toFixed(1)}</strong></div>
                <div class="muted">Week capacity: <strong>${row.max_bags_possible}</strong> | ${html(existing)}</div>
                <div class="muted">Last check: ${row.last_check_datetime ? new Date(row.last_check_datetime).toLocaleString() : '-'}</div>
            </article>
        `;
    }).join('');
}

function renderMatches(rows) {
    byId('matchesBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>#${row.id}</td>
                <td>${html(row.donor_name || '-')}</td>
                <td>${html(row.donor_blood_group || '-')}</td>
                <td>${statusBadge(row.compatibility_label)}</td>
                <td>${row.match_score !== null ? Number(row.match_score).toFixed(1) : '-'}</td>
                <td>${statusBadge(row.status)}</td>
                <td>${row.notified_at ? new Date(row.notified_at).toLocaleString() : '-'}</td>
                <td>${row.responded_at ? new Date(row.responded_at).toLocaleString() : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="8">No match records for selected request.</td></tr>';
}

function buildFilterQuery() {
    const query = {};
    const status = byId('statusFilter').value;
    const bloodGroup = byId('bloodGroupFilter').value;
    const dept = byId('departmentFilter').value;
    const limit = byId('requestLimit').value;
    if (status) query.status = status;
    if (bloodGroup) query.bloodGroup = bloodGroup;
    if (dept) query.departmentId = Number(dept);
    if (limit) query.limit = Number(limit);
    return query;
}

async function loadRequests() {
    setBusy('btnLoadRequests', true);
    const r = await call('/blood/matching/requests', 'GET', null, buildFilterQuery());
    setBusy('btnLoadRequests', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not load requests', 'error'); return; }

    renderRequests(r.data?.requests || []);
    if (!selectedRequestId && (r.data?.requests || []).length) {
        await selectRequest(r.data.requests[0].id);
    } else {
        renderSelectedMeta();
    }
    toast('Requests loaded');
}

async function selectRequest(id) {
    selectedRequestId = id;
    selectedDonorIds = new Set();
    updateSelectedStats();
    renderRequests(requestsCache);
    renderSelectedMeta();
    await Promise.all([loadSuggestions(), loadMatches()]);
}

async function loadSuggestions() {
    if (!selectedRequestId) {
        renderSuggestions([]);
        return;
    }
    const r = await call(`/blood/matching/requests/${selectedRequestId}/suggestions`, 'GET', null, { limit: 40 });
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not load donor suggestions', 'error'); return; }
    renderSuggestions(r.data?.suggestions || []);
}

async function loadMatches() {
    if (!selectedRequestId) {
        renderMatches([]);
        return;
    }
    const r = await call(`/blood/matching/requests/${selectedRequestId}/matches`, 'GET');
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not load matches', 'error'); return; }
    renderMatches(r.data?.matches || []);
}

function toggleDonor(donorId, checked) {
    if (checked) selectedDonorIds.add(donorId);
    else selectedDonorIds.delete(donorId);
    updateSelectedStats();
}

function notifyPayload(auto) {
    return {
        donorIds: auto ? [] : Array.from(selectedDonorIds),
        title: byId('notifyTitle').value.trim() || null,
        message: byId('notifyMessage').value.trim() || null,
        suggestedLimit: Number(byId('notifyLimit').value || 6),
    };
}

async function notifySelected() {
    if (!selectedRequestId) { toast('Select a request first', 'error'); return; }
    if (!selectedDonorIds.size) { toast('Select at least one donor card first', 'error'); return; }

    setBusy('btnNotifySelected', true);
    const r = await call(`/blood/matching/requests/${selectedRequestId}/notify`, 'POST', notifyPayload(false));
    setBusy('btnNotifySelected', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not notify selected donors', 'error'); return; }
    toast(`Notifications sent: ${r.data?.sent_count ?? 0}`);
    await Promise.all([loadRequests(), loadSuggestions(), loadMatches()]);
}

async function notifyAuto() {
    if (!selectedRequestId) { toast('Select a request first', 'error'); return; }

    setBusy('btnNotifyAuto', true);
    const r = await call(`/blood/matching/requests/${selectedRequestId}/notify`, 'POST', notifyPayload(true));
    setBusy('btnNotifyAuto', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not auto notify donors', 'error'); return; }
    toast(`Auto notifications sent: ${r.data?.sent_count ?? 0}`);
    await Promise.all([loadRequests(), loadSuggestions(), loadMatches()]);
}

async function refreshAll() {
    setBusy('btnRefreshAll', true);
    try {
        await loadRequests();
        if (selectedRequestId) {
            await loadSuggestions();
            await loadMatches();
        }
        toast('Matching board refreshed');
    } finally {
        setBusy('btnRefreshAll', false);
    }
}

function boot() {
    updateClock();
    setInterval(updateClock, 1000);
    useStoredAdminToken();
    refreshAll();
}

boot();
</script>
</body>
</html>
