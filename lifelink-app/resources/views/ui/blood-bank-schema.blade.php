<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Blood Bank Schema</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Sora:wght@500;600;700&display=swap');

        :root {
            --bg-a: #eff8fb;
            --bg-b: #fef5ea;
            --ink: #132741;
            --muted: #5f7088;
            --line: rgba(19, 39, 65, 0.13);
            --card: rgba(255, 255, 255, 0.88);
            --primary: #0369a1;
            --primary-strong: #075985;
            --accent: #ea580c;
            --ok: #166534;
            --warn: #a16207;
            --danger: #b91c1c;
            --shadow: 0 16px 35px rgba(19, 39, 65, 0.15);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "Manrope", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at 12% 12%, rgba(3, 105, 161, 0.2), transparent 42%),
                radial-gradient(circle at 84% 8%, rgba(234, 88, 12, 0.15), transparent 40%),
                linear-gradient(145deg, var(--bg-a), var(--bg-b));
        }

        h1, h2, h3, h4 {
            margin: 0;
            font-family: "Sora", "Trebuchet MS", sans-serif;
            letter-spacing: -0.01em;
        }

        .shell {
            max-width: 1340px;
            margin: 0 auto;
            padding: 18px 14px 30px;
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

        .phase {
            border-radius: 999px;
            background: rgba(3, 105, 161, 0.14);
            color: var(--primary-strong);
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 800;
        }

        .hero {
            border: 1px solid rgba(255, 255, 255, 0.74);
            border-radius: 18px;
            background: linear-gradient(130deg, rgba(255, 255, 255, 0.93), rgba(255, 255, 255, 0.64));
            box-shadow: var(--shadow);
            padding: 16px;
            margin-bottom: 13px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
        }

        .hero p {
            margin: 7px 0 0;
            color: var(--muted);
            max-width: 760px;
            font-size: 14px;
        }

        .clock {
            text-align: right;
            min-width: 170px;
        }

        .clock strong {
            display: block;
            font-size: 24px;
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

        .side {
            padding: 12px;
            height: fit-content;
            position: sticky;
            top: 14px;
        }

        .content {
            padding: 11px;
            display: grid;
            gap: 10px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.92);
            padding: 11px;
        }

        .hint {
            margin: 4px 0 0;
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
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        input, select, textarea {
            width: 100%;
            border-radius: 10px;
            border: 1px solid rgba(19, 39, 65, 0.18);
            background: rgba(255, 255, 255, 0.94);
            color: var(--ink);
            font: inherit;
            padding: 9px 10px;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.15);
        }

        textarea {
            min-height: 78px;
            resize: vertical;
        }

        .btn-row {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 9px;
        }

        button {
            border: 0;
            border-radius: 10px;
            padding: 9px 11px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        button[disabled] {
            opacity: 0.62;
            pointer-events: none;
        }

        .btn-main { background: var(--primary); color: #fff; }
        .btn-main:hover { background: var(--primary-strong); }
        .btn-soft { background: rgba(19, 39, 65, 0.1); color: var(--ink); }
        .btn-accent { background: var(--accent); color: #fff; }

        .stats {
            margin-top: 9px;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
        }

        .stat {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.92);
            text-align: center;
            padding: 9px;
        }

        .stat .num {
            font-family: "Sora", "Trebuchet MS", sans-serif;
            font-size: 20px;
            font-weight: 700;
        }

        .stat .lbl {
            margin-top: 2px;
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 8px;
        }

        .filters button {
            background: rgba(19, 39, 65, 0.09);
            color: var(--ink);
            padding: 7px 10px;
            font-size: 12px;
            border: 1px solid transparent;
        }

        .filters button.active {
            background: rgba(3, 105, 161, 0.16);
            border-color: rgba(3, 105, 161, 0.35);
            color: var(--primary-strong);
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.92);
            margin-top: 8px;
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
            border-bottom: 1px solid rgba(19, 39, 65, 0.09);
        }

        th {
            position: sticky;
            top: 0;
            background: rgba(245, 250, 255, 0.96);
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge {
            display: inline-flex;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
        }

        .badge.ok { color: var(--ok); background: rgba(22, 101, 52, 0.15); }
        .badge.warn { color: var(--warn); background: rgba(161, 98, 7, 0.16); }
        .badge.danger { color: var(--danger); background: rgba(185, 28, 28, 0.14); }

        pre {
            margin: 0;
            min-height: 110px;
            max-height: 260px;
            overflow: auto;
            border-radius: 11px;
            border: 1px solid var(--line);
            background: #111f37;
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
            box-shadow: 0 10px 22px rgba(19, 39, 65, 0.3);
        }

        .toast.ok { background: #166534; }
        .toast.error { background: #b91c1c; }

        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
            .side { position: static; }
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
        <div class="phase">Phase 6 Issue 16: Blood Bank Schema</div>
    </div>

    <section class="hero">
        <div>
            <h1>Blood Bank Schema Setup</h1>
            <p>Manage blood banks, donor profiles, and inventory records from one admin/IT dashboard.</p>
        </div>
        <div class="clock">
            <strong id="clockNow">--:--</strong>
            <small>Local dashboard time</small>
        </div>
    </section>

    <section class="layout">
        <aside class="panel side">
            <div class="card">
                <h3>Auth Context</h3>
                <p class="hint">Use Admin/IT token from <code>/ui/auth</code>.</p>
                <label for="tokenInput">Token</label>
                <input id="tokenInput" placeholder="Bearer token">
                <div class="btn-row">
                    <button class="btn-soft" onclick="useStoredAdminToken()">Use ADMIN_TOKEN</button>
                    <button class="btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    <button id="btnRefresh" class="btn-main" onclick="refreshAll()">Refresh All</button>
                </div>
            </div>

            <div class="card">
                <h3>Request Status Filter</h3>
                <div id="requestFilters" class="filters">
                    <button data-status="" class="active" onclick="setRequestStatus('')">All</button>
                    <button data-status="Pending" onclick="setRequestStatus('Pending')">Pending</button>
                    <button data-status="Fulfilled" onclick="setRequestStatus('Fulfilled')">Fulfilled</button>
                    <button data-status="Rejected" onclick="setRequestStatus('Rejected')">Rejected</button>
                </div>
            </div>
        </aside>

        <main class="panel content">
            <div class="card">
                <h3>Schema Snapshot</h3>
                <p class="hint">Live stats from <code>GET /api/blood/schema/overview</code>.</p>
                <div class="stats">
                    <div class="stat"><div class="num" id="stBanks">0</div><div class="lbl">Banks</div></div>
                    <div class="stat"><div class="num" id="stDonors">0</div><div class="lbl">Donors</div></div>
                    <div class="stat"><div class="num" id="stInventory">0</div><div class="lbl">Inventory Rows</div></div>
                    <div class="stat"><div class="num" id="stRequests">0</div><div class="lbl">Requests</div></div>
                    <div class="stat"><div class="num" id="stUnits">0</div><div class="lbl">Total Units</div></div>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Create Blood Bank</h3>
                    <div class="row">
                        <div>
                            <label for="bankName">Bank name</label>
                            <input id="bankName" placeholder="LifeLink Central Bank">
                        </div>
                        <div>
                            <label for="bankLocation">Location</label>
                            <input id="bankLocation" placeholder="Dhaka Main Building">
                        </div>
                    </div>
                    <label for="bankActive">Status</label>
                    <select id="bankActive">
                        <option value="true" selected>Active</option>
                        <option value="false">Inactive</option>
                    </select>
                    <div class="btn-row">
                        <button id="btnCreateBank" class="btn-main" onclick="createBank()">Create Bank</button>
                        <button class="btn-soft" onclick="loadBanks()">Refresh Banks</button>
                    </div>
                </div>

                <div class="card">
                    <h3>Upsert Donor Profile</h3>
                    <div class="row">
                        <div>
                            <label for="donorUserId">Donor user ID</label>
                            <input id="donorUserId" type="number" min="1" placeholder="e.g. 12">
                        </div>
                        <div>
                            <label for="donorBloodGroup">Blood group</label>
                            <select id="donorBloodGroup">
                                <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                                <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <label for="donorLastDate">Last donation date (optional)</label>
                            <input id="donorLastDate" type="datetime-local">
                        </div>
                        <div>
                            <label for="donorEligible">Eligibility</label>
                            <select id="donorEligible">
                                <option value="true" selected>Eligible</option>
                                <option value="false">Not eligible</option>
                            </select>
                        </div>
                    </div>
                    <label for="donorNotes">Notes (optional)</label>
                    <textarea id="donorNotes" placeholder="Donor profile notes"></textarea>
                    <div class="btn-row">
                        <button id="btnDonor" class="btn-main" onclick="upsertDonorProfile()">Upsert Donor</button>
                        <button class="btn-soft" onclick="loadDonorProfiles()">Refresh Donors</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Upsert Inventory Row</h3>
                <div class="row">
                    <div>
                        <label for="inventoryBankId">Blood bank</label>
                        <select id="inventoryBankId"></select>
                    </div>
                    <div>
                        <label for="inventoryBloodGroup">Blood group</label>
                        <select id="inventoryBloodGroup">
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="inventoryComponent">Component</label>
                        <select id="inventoryComponent">
                            <option selected>WholeBlood</option>
                            <option>Plasma</option>
                            <option>Platelets</option>
                            <option>RBC</option>
                        </select>
                    </div>
                    <div>
                        <label for="inventoryUnits">Units available</label>
                        <input id="inventoryUnits" type="number" min="0" value="0">
                    </div>
                </div>
                <div class="btn-row">
                    <button id="btnInventory" class="btn-accent" onclick="upsertInventory()">Upsert Inventory</button>
                    <button class="btn-soft" onclick="loadInventory()">Refresh Inventory</button>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Blood Banks</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Name</th><th>Location</th><th>Status</th><th>Rows</th><th>Units</th></tr>
                            </thead>
                            <tbody id="banksBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h3>Donor Profiles</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>User</th><th>Name</th><th>Blood</th><th>Eligible</th><th>Last Donation</th></tr>
                            </thead>
                            <tbody id="donorsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="split">
                <div class="card">
                    <h3>Inventory</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Bank</th><th>Group</th><th>Component</th><th>Units</th><th>Updated</th></tr>
                            </thead>
                            <tbody id="inventoryBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h3>Blood Requests</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr><th>ID</th><th>Patient</th><th>Group</th><th>Component</th><th>Units</th><th>Status</th></tr>
                            </thead>
                            <tbody id="requestsBody"></tbody>
                        </table>
                    </div>
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
const state = {
    requestStatus: '',
    banks: []
};

function byId(id) { return document.getElementById(id); }

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function html(value) {
    if (value === null || value === undefined) return '';
    return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
}

function showToast(message, type = 'ok') {
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'error' ? 'error' : 'ok'}`;
    toast.textContent = message;
    byId('toastStack').appendChild(toast);
    setTimeout(() => toast.remove(), 2600);
}

function setClock() {
    byId('clockNow').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function setButtonBusy(id, busy) {
    const button = byId(id);
    if (!button) return;
    button.disabled = busy;
    button.dataset.label = button.dataset.label || button.textContent;
    button.textContent = busy ? 'Working...' : button.dataset.label;
}

function useStoredAdminToken() { byId('tokenInput').value = localStorage.getItem('ADMIN_TOKEN') || ''; }
function useStoredUserToken() { byId('tokenInput').value = localStorage.getItem('USER_TOKEN') || ''; }

async function call(path, method = 'GET', body = null, query = null) {
    const token = byId('tokenInput').value.trim();
    if (!token) {
        return { status: 401, data: { message: 'Token missing. Use ADMIN_TOKEN or USER_TOKEN first.' } };
    }

    const queryString = query ? new URLSearchParams(query).toString() : '';
    const endpoint = `${API}${path}${queryString ? `?${queryString}` : ''}`;
    const response = await fetch(endpoint, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: body ? JSON.stringify(body) : undefined
    });

    const text = await response.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: response.status, data };
}

function setFilterActive(containerId, status) {
    byId(containerId).querySelectorAll('button[data-status]').forEach((button) => {
        button.classList.toggle('active', (button.getAttribute('data-status') || '') === status);
    });
}

function statusBadge(status) {
    const value = status || '-';
    let type = 'warn';
    if (['Fulfilled', 'Approved', 'Matched'].includes(value)) type = 'ok';
    if (['Rejected', 'Cancelled'].includes(value)) type = 'danger';
    return `<span class="badge ${type}">${html(value)}</span>`;
}

function populateBankSelect() {
    const select = byId('inventoryBankId');
    if (!state.banks.length) {
        select.innerHTML = '<option value="">No banks available</option>';
        return;
    }
    select.innerHTML = state.banks.map((bank) => `<option value="${bank.id}">${html(bank.bank_name)} (#${bank.id})</option>`).join('');
}

async function loadOverview() {
    const r = await call('/blood/schema/overview', 'GET');
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not load overview', 'error'); return; }

    const stats = r.data?.stats || {};
    byId('stBanks').textContent = stats.banks || 0;
    byId('stDonors').textContent = stats.donor_profiles || 0;
    byId('stInventory').textContent = stats.inventory_rows || 0;
    byId('stRequests').textContent = stats.blood_requests || 0;
    byId('stUnits').textContent = stats.total_units_available || 0;
}

async function loadBanks() {
    const r = await call('/blood/schema/banks', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load banks', 'error'); return; }
    state.banks = r.data?.banks || [];
    populateBankSelect();
    byId('banksBody').innerHTML = state.banks.length
        ? state.banks.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.bank_name)}</td>
                <td>${html(row.location || '-')}</td>
                <td>${row.is_active ? '<span class="badge ok">Active</span>' : '<span class="badge danger">Inactive</span>'}</td>
                <td>${row.inventory_rows}</td>
                <td>${row.units_total}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="6">No blood banks found.</td></tr>';
}

async function loadDonorProfiles() {
    const r = await call('/blood/schema/donor-profiles', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load donor profiles', 'error'); return; }
    const rows = r.data?.donor_profiles || [];
    byId('donorsBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.donor_id}</td>
                <td>${html(row.full_name || '-')}</td>
                <td>${html(row.blood_group || '-')}</td>
                <td>${row.is_eligible ? '<span class="badge ok">Eligible</span>' : '<span class="badge danger">Not Eligible</span>'}</td>
                <td>${row.last_donation_date ? new Date(row.last_donation_date).toLocaleString() : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No donor profiles found.</td></tr>';
}

async function loadInventory() {
    const r = await call('/blood/schema/inventory', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load inventory', 'error'); return; }
    const rows = r.data?.inventory || [];
    byId('inventoryBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.bank_name || '-')}</td>
                <td>${html(row.blood_group || '-')}</td>
                <td>${html(row.component_type || '-')}</td>
                <td>${row.units_available}</td>
                <td>${row.last_updated_at ? new Date(row.last_updated_at).toLocaleString() : '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="6">No inventory rows found.</td></tr>';
}

async function loadRequests() {
    const query = {};
    if (state.requestStatus) query.status = state.requestStatus;
    const r = await call('/blood/schema/requests', 'GET', null, query);
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load requests', 'error'); return; }
    const rows = r.data?.blood_requests || [];
    byId('requestsBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.patient_name || '-')}</td>
                <td>${html(row.blood_group_needed || '-')}</td>
                <td>${html(row.component_type || '-')}</td>
                <td>${row.units_required}</td>
                <td>${statusBadge(row.status)}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="6">No blood requests found.</td></tr>';
}

async function createBank() {
    const payload = {
        bankName: byId('bankName').value.trim(),
        location: byId('bankLocation').value.trim() || null,
        isActive: byId('bankActive').value === 'true'
    };
    setButtonBusy('btnCreateBank', true);
    const r = await call('/blood/schema/banks', 'POST', payload);
    setButtonBusy('btnCreateBank', false);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not create blood bank', 'error'); return; }
    byId('bankName').value = '';
    byId('bankLocation').value = '';
    showToast('Blood bank created');
    await loadBanks();
    await loadOverview();
}

async function upsertDonorProfile() {
    const payload = {
        donorUserId: Number(byId('donorUserId').value || 0),
        bloodGroup: byId('donorBloodGroup').value,
        lastDonationDate: byId('donorLastDate').value || null,
        isEligible: byId('donorEligible').value === 'true',
        notes: byId('donorNotes').value.trim() || null
    };
    setButtonBusy('btnDonor', true);
    const r = await call('/blood/schema/donor-profiles', 'POST', payload);
    setButtonBusy('btnDonor', false);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not upsert donor profile', 'error'); return; }
    byId('donorNotes').value = '';
    showToast('Donor profile upserted');
    await loadDonorProfiles();
    await loadOverview();
}

async function upsertInventory() {
    const payload = {
        bankId: Number(byId('inventoryBankId').value || 0),
        bloodGroup: byId('inventoryBloodGroup').value,
        componentType: byId('inventoryComponent').value,
        unitsAvailable: Number(byId('inventoryUnits').value || 0)
    };
    setButtonBusy('btnInventory', true);
    const r = await call('/blood/schema/inventory', 'POST', payload);
    setButtonBusy('btnInventory', false);
    write(r);
    if (r.status >= 300) { showToast(r.data?.message || 'Could not upsert inventory', 'error'); return; }
    showToast('Inventory row upserted');
    await loadInventory();
    await loadBanks();
    await loadOverview();
}

function setRequestStatus(status) {
    state.requestStatus = status;
    setFilterActive('requestFilters', status);
    loadRequests();
}

async function refreshAll() {
    setButtonBusy('btnRefresh', true);
    try {
        await loadOverview();
        await loadBanks();
        await loadDonorProfiles();
        await loadInventory();
        await loadRequests();
        showToast('Dashboard refreshed');
    } finally {
        setButtonBusy('btnRefresh', false);
    }
}

function boot() {
    setClock();
    setInterval(setClock, 1000);
    useStoredAdminToken();
    refreshAll();
}

boot();
</script>
</body>
</html>
