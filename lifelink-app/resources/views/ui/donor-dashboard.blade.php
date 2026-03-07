<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Donor Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Public+Sans:wght@400;500;600;700&display=swap');

        :root {
            --bg-1: #f0f9ff;
            --bg-2: #fff7ed;
            --ink: #0f172a;
            --muted: #475569;
            --line: rgba(15, 23, 42, 0.12);
            --card: rgba(255, 255, 255, 0.88);
            --primary: #0369a1;
            --primary-strong: #0c4a6e;
            --accent: #ea580c;
            --ok: #166534;
            --warn: #9a3412;
            --danger: #b91c1c;
            --shadow: 0 18px 36px rgba(2, 6, 23, 0.15);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "Public Sans", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at 10% 10%, rgba(14, 165, 233, 0.2), transparent 40%),
                radial-gradient(circle at 90% 0%, rgba(249, 115, 22, 0.2), transparent 38%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
        }

        h1, h2, h3 {
            margin: 0;
            font-family: "Space Grotesk", "Trebuchet MS", sans-serif;
            letter-spacing: -0.01em;
        }

        .shell {
            max-width: 1360px;
            margin: 0 auto;
            padding: 16px 12px 24px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .topbar a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        .chip {
            border-radius: 999px;
            background: rgba(3, 105, 161, 0.15);
            color: var(--primary-strong);
            font-size: 12px;
            font-weight: 800;
            padding: 7px 12px;
        }

        .hero {
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 18px;
            background: linear-gradient(130deg, rgba(255, 255, 255, 0.94), rgba(255, 255, 255, 0.65));
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
            max-width: 820px;
        }

        .clock {
            text-align: right;
            min-width: 170px;
        }

        .clock strong {
            display: block;
            font-size: 25px;
        }

        .clock small {
            color: var(--muted);
            font-size: 12px;
        }

        .layout {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
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
        }

        .main {
            padding: 11px;
            display: grid;
            gap: 10px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.94);
            padding: 12px;
        }

        .hint {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        label {
            display: block;
            margin: 0 0 5px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        input, select, textarea {
            width: 100%;
            border-radius: 10px;
            border: 1px solid rgba(15, 23, 42, 0.2);
            background: rgba(255, 255, 255, 0.96);
            color: var(--ink);
            font: inherit;
            padding: 9px 10px;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.15);
        }

        textarea { min-height: 74px; resize: vertical; }

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
            background: rgba(255, 255, 255, 0.95);
            text-align: center;
            padding: 9px;
        }

        .stat .num {
            font-family: "Space Grotesk", "Trebuchet MS", sans-serif;
            font-size: 20px;
            font-weight: 700;
        }

        .stat .lbl {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge.ok { color: var(--ok); background: rgba(22, 101, 52, 0.15); }
        .badge.warn { color: var(--warn); background: rgba(154, 52, 18, 0.16); }
        .badge.danger { color: var(--danger); background: rgba(185, 28, 28, 0.14); }

        .table-wrap {
            margin-top: 8px;
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: auto;
            background: rgba(255, 255, 255, 0.94);
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
            border-bottom: 1px solid rgba(15, 23, 42, 0.09);
        }

        th {
            position: sticky;
            top: 0;
            background: rgba(247, 250, 255, 0.97);
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        pre {
            margin: 0;
            min-height: 110px;
            max-height: 260px;
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
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 860px) {
            .split, .row { grid-template-columns: 1fr; }
            .hero { flex-direction: column; align-items: flex-start; }
            .clock { text-align: left; }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="topbar">
        <a href="/ui"><- Back to UI Home</a>
        <div class="chip">Phase 6 Issue 17: Donor Dashboard & Tracking</div>
    </div>

    <section class="hero">
        <div>
            <h1>Donor Availability + Health + Bag Logging</h1>
            <p>Track weekly availability, store donor weight/temperature checks, and log donated bags while auto-updating blood inventory.</p>
        </div>
        <div class="clock">
            <strong id="clockNow">--:--</strong>
            <small>Local donor dashboard time</small>
        </div>
    </section>

    <section class="layout">
        <aside class="panel sidebar">
            <div class="card">
                <h3>Auth Context</h3>
                <p class="hint">Use a token from a user with <code>Donor</code> role.</p>
                <label for="tokenInput">Bearer token</label>
                <input id="tokenInput" placeholder="Paste donor token">
                <label for="enrollBloodGroup" style="margin-top:8px;">Enroll blood group</label>
                <select id="enrollBloodGroup">
                    <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                    <option>AB+</option><option>AB-</option><option selected>O+</option><option>O-</option>
                </select>
                <div class="btns">
                    <button class="btn-soft" onclick="useStoredDonorToken()">Use DONOR_TOKEN</button>
                    <button class="btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    <button id="btnEnroll" class="btn-soft" onclick="enrollDonorRole()">Enable Donor Role</button>
                    <button id="btnRefresh" class="btn-main" onclick="refreshAll()">Refresh All</button>
                </div>
            </div>

            <div class="card">
                <h3>Eligibility</h3>
                <p class="hint">Rule used by API: weight >= 45kg and temperature between 36.0C and 37.8C.</p>
                <div id="eligibilityBadge" class="badge warn">Unknown</div>
            </div>
        </aside>

        <main class="panel main">
            <div class="card">
                <h3>Donor Snapshot</h3>
                <p class="hint">Live from <code>GET /api/donor/dashboard</code>.</p>
                <div class="stats">
                    <div class="stat"><div class="num" id="stUnits">0</div><div class="lbl">Total Units</div></div>
                    <div class="stat"><div class="num" id="stDonations">0</div><div class="lbl">Total Donations</div></div>
                    <div class="stat"><div class="num" id="stPendingReq">0</div><div class="lbl">Pending Group Requests</div></div>
                    <div class="stat"><div class="num" id="stWeekBags">0</div><div class="lbl">Week Max Bags</div></div>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Weekly Availability</h3>
                    <div class="row">
                        <div>
                            <label for="weekStartDate">Week start date</label>
                            <input id="weekStartDate" type="date">
                        </div>
                        <div>
                            <label for="isAvailable">Availability</label>
                            <select id="isAvailable">
                                <option value="true" selected>Available</option>
                                <option value="false">Not Available</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <label for="maxBagsPossible">Max bags possible</label>
                            <input id="maxBagsPossible" type="number" min="0" max="10" value="1">
                        </div>
                        <div>
                            <label for="availabilityNotes">Notes (optional)</label>
                            <input id="availabilityNotes" placeholder="Free this week after 5pm">
                        </div>
                    </div>
                    <div class="btns">
                        <button id="btnAvailability" class="btn-main" onclick="upsertAvailability()">Save Availability</button>
                        <button class="btn-soft" onclick="loadAvailability()">Refresh Availability</button>
                    </div>
                </div>

                <div class="card">
                    <h3>Health Check</h3>
                    <div class="row">
                        <div>
                            <label for="checkDateTime">Check datetime</label>
                            <input id="checkDateTime" type="datetime-local">
                        </div>
                        <div>
                            <label for="weightKg">Weight (kg)</label>
                            <input id="weightKg" type="number" min="30" max="250" step="0.1" value="60">
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <label for="temperatureC">Temperature (C)</label>
                            <input id="temperatureC" type="number" min="34" max="43" step="0.1" value="36.8">
                        </div>
                        <div>
                            <label for="hemoglobin">Hemoglobin (optional)</label>
                            <input id="hemoglobin" type="number" min="5" max="25" step="0.1" placeholder="13.5">
                        </div>
                    </div>
                    <label for="healthNotes">Notes (optional)</label>
                    <textarea id="healthNotes" placeholder="Donor is fit today"></textarea>
                    <div class="btns">
                        <button id="btnHealthCheck" class="btn-accent" onclick="logHealthCheck()">Log Health Check</button>
                        <button class="btn-soft" onclick="loadHealthChecks()">Refresh Health Checks</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Bag Donation Logging</h3>
                <div class="row">
                    <div>
                        <label for="bankId">Blood bank</label>
                        <select id="bankId"></select>
                    </div>
                    <div>
                        <label for="donationDateTime">Donation datetime</label>
                        <input id="donationDateTime" type="datetime-local">
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="donationBloodGroup">Blood group</label>
                        <select id="donationBloodGroup">
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                        </select>
                    </div>
                    <div>
                        <label for="componentType">Component</label>
                        <select id="componentType">
                            <option selected>WholeBlood</option>
                            <option>Plasma</option>
                            <option>Platelets</option>
                            <option>RBC</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="unitsDonated">Units donated (bags)</label>
                        <input id="unitsDonated" type="number" min="1" max="5" value="1">
                    </div>
                    <div>
                        <label for="linkedRequestId">Linked request ID (optional)</label>
                        <input id="linkedRequestId" type="number" min="1" placeholder="e.g. 5">
                    </div>
                </div>
                <label for="donationNotes">Notes (optional)</label>
                <textarea id="donationNotes" placeholder="Donation drive log"></textarea>
                <div class="btns">
                    <button id="btnDonation" class="btn-main" onclick="logDonation()">Log Donation</button>
                    <button class="btn-soft" onclick="loadDonations()">Refresh Donations</button>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Availability History</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Week</th><th>Status</th><th>Max Bags</th><th>Updated</th></tr>
                            </thead>
                            <tbody id="availabilityBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h3>Health Checks</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Date</th><th>Weight</th><th>Temp</th><th>Hb</th></tr>
                            </thead>
                            <tbody id="healthBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Donation History</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr><th>ID</th><th>Date</th><th>Bank</th><th>Group</th><th>Component</th><th>Units</th><th>Linked Request</th></tr>
                        </thead>
                        <tbody id="donationBody"></tbody>
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

<div id="toastStack" class="toast-stack"></div>

<script>
const API = '/api';
const out = document.getElementById('out');

function byId(id) { return document.getElementById(id); }

function write(value) {
    out.textContent = typeof value === 'string' ? value : JSON.stringify(value, null, 2);
}

function html(value) {
    if (value === null || value === undefined) return '';
    return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
}

function toast(message, type = 'ok') {
    const element = document.createElement('div');
    element.className = `toast ${type === 'error' ? 'error' : 'ok'}`;
    element.textContent = message;
    byId('toastStack').appendChild(element);
    setTimeout(() => element.remove(), 2600);
}

function setClock() {
    byId('clockNow').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function setBusy(id, busy) {
    const button = byId(id);
    if (!button) return;
    button.disabled = busy;
    button.dataset.label = button.dataset.label || button.textContent;
    button.textContent = busy ? 'Working...' : button.dataset.label;
}

function useStoredDonorToken() { byId('tokenInput').value = localStorage.getItem('DONOR_TOKEN') || ''; }
function useStoredUserToken() { byId('tokenInput').value = localStorage.getItem('USER_TOKEN') || ''; }

async function call(path, method = 'GET', body = null, query = null) {
    const token = byId('tokenInput').value.trim();
    if (!token) {
        return { status: 401, data: { message: 'Token missing. Use DONOR_TOKEN or USER_TOKEN.' } };
    }

    const queryString = query ? new URLSearchParams(query).toString() : '';
    const endpoint = `${API}${path}${queryString ? `?${queryString}` : ''}`;

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

function renderEligibility(isEligible) {
    const badge = byId('eligibilityBadge');
    if (isEligible === true) {
        badge.className = 'badge ok';
        badge.textContent = 'Eligible';
        return;
    }
    if (isEligible === false) {
        badge.className = 'badge danger';
        badge.textContent = 'Not Eligible';
        return;
    }
    badge.className = 'badge warn';
    badge.textContent = 'Unknown';
}

async function loadBanks() {
    const r = await call('/donor/banks', 'GET');
    if (r.status >= 300) { write(r); toast(r.data?.message || 'Could not load banks', 'error'); return; }

    const banks = r.data?.banks || [];
    byId('bankId').innerHTML = banks.length
        ? banks.map((row) => `<option value="${row.id}">${html(row.bank_name)} (#${row.id})</option>`).join('')
        : '<option value="">No active banks found</option>';
}

async function loadDashboard() {
    const r = await call('/donor/dashboard', 'GET');
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not load donor dashboard', 'error'); return; }

    const stats = r.data?.stats || {};
    byId('stUnits').textContent = stats.total_units_donated || 0;
    byId('stDonations').textContent = stats.total_donations || 0;
    byId('stPendingReq').textContent = stats.pending_group_requests || 0;
    byId('stWeekBags').textContent = r.data?.current_week_availability?.max_bags_possible || 0;

    renderEligibility(r.data?.donor?.is_eligible);

    const donorGroup = r.data?.donor?.blood_group;
    if (donorGroup) byId('donationBloodGroup').value = donorGroup;
}

async function loadAvailability() {
    const r = await call('/donor/availability', 'GET');
    if (r.status >= 300) { write(r); toast(r.data?.message || 'Could not load availability', 'error'); return; }
    const rows = r.data?.availabilities || [];
    byId('availabilityBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.week_start_date || '-')}</td>
                <td>${row.is_available ? '<span class="badge ok">Available</span>' : '<span class="badge danger">Not Available</span>'}</td>
                <td>${row.max_bags_possible}</td>
                <td>${row.updated_at ? new Date(row.updated_at).toLocaleString() : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No availability records found.</td></tr>';
}

async function upsertAvailability() {
    const payload = {
        weekStartDate: byId('weekStartDate').value || null,
        isAvailable: byId('isAvailable').value === 'true',
        maxBagsPossible: Number(byId('maxBagsPossible').value || 0),
        notes: byId('availabilityNotes').value.trim() || null,
    };
    setBusy('btnAvailability', true);
    const r = await call('/donor/availability', 'POST', payload);
    setBusy('btnAvailability', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not upsert availability', 'error'); return; }
    toast('Availability updated');
    await loadAvailability();
    await loadDashboard();
}

async function loadHealthChecks() {
    const r = await call('/donor/health-checks', 'GET');
    if (r.status >= 300) { write(r); toast(r.data?.message || 'Could not load health checks', 'error'); return; }
    const rows = r.data?.health_checks || [];
    byId('healthBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${row.check_datetime ? new Date(row.check_datetime).toLocaleString() : '-'}</td>
                <td>${row.weight_kg}</td>
                <td>${row.temperature_c}</td>
                <td>${row.hemoglobin ?? '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No health checks found.</td></tr>';
}

async function logHealthCheck() {
    const payload = {
        checkDateTime: byId('checkDateTime').value || null,
        weightKg: Number(byId('weightKg').value || 0),
        temperatureC: Number(byId('temperatureC').value || 0),
        hemoglobin: byId('hemoglobin').value ? Number(byId('hemoglobin').value) : null,
        notes: byId('healthNotes').value.trim() || null,
    };
    setBusy('btnHealthCheck', true);
    const r = await call('/donor/health-checks', 'POST', payload);
    setBusy('btnHealthCheck', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not log health check', 'error'); return; }
    byId('healthNotes').value = '';
    toast('Health check logged');
    await loadHealthChecks();
    await loadDashboard();
}

async function loadDonations() {
    const r = await call('/donor/donations', 'GET');
    if (r.status >= 300) { write(r); toast(r.data?.message || 'Could not load donations', 'error'); return; }
    const rows = r.data?.donations || [];
    byId('donationBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${row.donation_datetime ? new Date(row.donation_datetime).toLocaleString() : '-'}</td>
                <td>${html(row.bank_name || '-')}</td>
                <td>${html(row.blood_group || '-')}</td>
                <td>${html(row.component_type || '-')}</td>
                <td>${row.units_donated}</td>
                <td>${row.linked_request_id ?? '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="7">No donations logged yet.</td></tr>';
}

async function logDonation() {
    const payload = {
        bankId: Number(byId('bankId').value || 0),
        donationDateTime: byId('donationDateTime').value || null,
        bloodGroup: byId('donationBloodGroup').value,
        componentType: byId('componentType').value,
        unitsDonated: Number(byId('unitsDonated').value || 1),
        linkedRequestId: byId('linkedRequestId').value ? Number(byId('linkedRequestId').value) : null,
        notes: byId('donationNotes').value.trim() || null,
    };
    setBusy('btnDonation', true);
    const r = await call('/donor/donations', 'POST', payload);
    setBusy('btnDonation', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not log donation', 'error'); return; }
    byId('donationNotes').value = '';
    byId('linkedRequestId').value = '';
    toast('Donation logged');
    await loadDonations();
    await loadDashboard();
}

async function refreshAll() {
    setBusy('btnRefresh', true);
    try {
        await loadBanks();
        await loadDashboard();
        await loadAvailability();
        await loadHealthChecks();
        await loadDonations();
        toast('Dashboard refreshed');
    } finally {
        setBusy('btnRefresh', false);
    }
}

async function enrollDonorRole() {
    const payload = {
        bloodGroup: byId('enrollBloodGroup').value,
        notes: 'Self-enrolled from donor dashboard UI',
    };
    setBusy('btnEnroll', true);
    const r = await call('/donor/enroll', 'POST', payload);
    setBusy('btnEnroll', false);
    write(r);
    if (r.status >= 300) { toast(r.data?.message || 'Could not enable donor role', 'error'); return; }
    toast('Donor role enabled');
    await refreshAll();
}

function boot() {
    setClock();
    setInterval(setClock, 1000);
    useStoredDonorToken();
    refreshAll();
}

boot();
</script>
</body>
</html>
