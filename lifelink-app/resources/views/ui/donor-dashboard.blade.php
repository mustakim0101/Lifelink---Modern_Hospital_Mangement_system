@extends('ui.layouts.app')

@section('title', 'Donor Dashboard')
@section('workspace_label', 'Blood donor workspace')
@section('hero_badge', 'Donor Mode')
@section('hero_title', 'Donor dashboard for availability, health checks, and donation logging.')
@section('hero_description', 'This is the second role page moved into the shared authenticated shell. Donors can manage eligibility-related data, weekly availability, and donation history inside one connected workspace.')
@section('meta_title', 'Donor Dashboard')
@section('meta_copy', 'Availability, health, donations, and blood support')

@push('styles')
<style>
    :root {
        --donor-ink: #0f172a;
        --donor-muted: #475569;
        --donor-line: rgba(15, 23, 42, 0.12);
        --donor-card: rgba(255, 255, 255, 0.92);
        --donor-primary: #0369a1;
        --donor-primary-strong: #0c4a6e;
        --donor-accent: #ea580c;
        --donor-ok: #166534;
        --donor-warn: #9a3412;
        --donor-danger: #b91c1c;
        --donor-shadow: 0 18px 36px rgba(2, 6, 23, 0.15);
    }

    .donor-grid { display: grid; gap: 10px; }
    .donor-card {
        border: 1px solid var(--donor-line);
        border-radius: 16px;
        background: var(--donor-card);
        box-shadow: var(--donor-shadow);
        padding: 12px;
    }

    .donor-card h3 { margin: 0; }
    .donor-hint { margin: 5px 0 0; color: var(--donor-muted); font-size: 12px; }
    .donor-split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .donor-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }

    .donor-label {
        display: block;
        margin: 0 0 5px;
        color: var(--donor-muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .donor-input,
    .donor-select,
    .donor-textarea {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(15, 23, 42, 0.2);
        background: rgba(255, 255, 255, 0.96);
        color: var(--donor-ink);
        font: inherit;
        padding: 9px 10px;
        outline: none;
    }

    .donor-input:focus,
    .donor-select:focus,
    .donor-textarea:focus {
        border-color: var(--donor-primary);
        box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.15);
    }

    .donor-textarea { min-height: 74px; resize: vertical; }
    .donor-btns { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 9px; }

    .donor-btn {
        border: 0;
        border-radius: 10px;
        padding: 9px 12px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .donor-btn[disabled] { opacity: 0.62; pointer-events: none; }
    .donor-btn-main { background: var(--donor-primary); color: #fff; }
    .donor-btn-main:hover { background: var(--donor-primary-strong); }
    .donor-btn-soft { background: rgba(15, 23, 42, 0.1); color: var(--donor-ink); }
    .donor-btn-accent { background: var(--donor-accent); color: #fff; }

    .donor-stats { margin-top: 8px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; }
    .donor-stat {
        border: 1px solid var(--donor-line);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.95);
        text-align: center;
        padding: 9px;
    }

    .donor-stat .num {
        font-family: "Sora", "Trebuchet MS", sans-serif;
        font-size: 20px;
        font-weight: 700;
    }

    .donor-stat .lbl {
        color: var(--donor-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .donor-badge {
        display: inline-flex;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 700;
    }

    .donor-badge.ok { color: var(--donor-ok); background: rgba(22, 101, 52, 0.15); }
    .donor-badge.warn { color: var(--donor-warn); background: rgba(154, 52, 18, 0.16); }
    .donor-badge.danger { color: var(--donor-danger); background: rgba(185, 28, 28, 0.14); }

    .donor-table-wrap {
        margin-top: 8px;
        border: 1px solid var(--donor-line);
        border-radius: 10px;
        overflow: auto;
        background: rgba(255, 255, 255, 0.94);
    }

    .donor-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .donor-table th,
    .donor-table td {
        text-align: left;
        white-space: nowrap;
        padding: 8px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.09);
    }

    .donor-table th {
        position: sticky;
        top: 0;
        background: rgba(247, 250, 255, 0.97);
        color: var(--donor-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .donor-pre {
        margin: 0;
        min-height: 110px;
        max-height: 260px;
        overflow: auto;
        border-radius: 11px;
        border: 1px solid var(--donor-line);
        background: #0f1f3b;
        color: #d7e3ff;
        padding: 10px;
        font-size: 12px;
    }

    .donor-toast-stack {
        position: fixed;
        right: 12px;
        bottom: 12px;
        display: grid;
        gap: 8px;
        z-index: 30;
    }

    .donor-toast {
        border-radius: 9px;
        padding: 9px 11px;
        color: #fff;
        font-size: 12px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.3);
    }

    .donor-toast.ok { background: #166534; }
    .donor-toast.error { background: #b91c1c; }
    .donor-clock { font-size: 1.7rem; }

    @media (max-width: 1200px) {
        .donor-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 860px) {
        .donor-split, .donor-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="/ui/donor-dashboard">
        <strong>Donor Dashboard</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/dashboard">
        <strong>Workspace Hub</strong>
        <span>Role redirect center</span>
    </a>
    <a href="/ui/patient-portal">
        <strong>Patient Portal</strong>
        <span>Shared shell reference</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Auth context</strong>
        <p>Use a token from a user with the <code>Donor</code> role. You can also enable donor role from this page if the current signed-in user needs the donor profile initialized.</p>
        <label class="donor-label" for="tokenInput">Bearer token</label>
        <input id="tokenInput" class="donor-input" placeholder="Paste donor token">
        <label class="donor-label" for="enrollBloodGroup" style="margin-top:8px;">Enroll blood group</label>
        <select id="enrollBloodGroup" class="donor-select">
            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
            <option>AB+</option><option>AB-</option><option selected>O+</option><option>O-</option>
        </select>
        <div class="donor-btns">
            <button class="donor-btn donor-btn-soft" onclick="useStoredDonorToken()">Use DONOR_TOKEN</button>
            <button class="donor-btn donor-btn-soft" onclick="useStoredUserToken()">Use USER_TOKEN</button>
            <button id="btnEnroll" class="donor-btn donor-btn-soft" onclick="enrollDonorRole()">Enable Donor Role</button>
            <button id="btnRefresh" class="donor-btn donor-btn-main" onclick="refreshAll()">Refresh All</button>
        </div>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Eligibility</strong>
        <p>Rule used by API: weight >= 45kg and temperature between 36.0C and 37.8C.</p>
        <div id="eligibilityBadge" class="donor-badge warn">Unknown</div>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Session clock</strong>
        <p>Track donor activity inside the shared workspace shell while keeping the same live donor endpoints and update flows.</p>
        <strong id="clockNow" class="donor-clock">--:--</strong>
    </div>
@endsection

@section('content')
    <div class="donor-grid">
        <div class="donor-card">
            <h3>Donor snapshot</h3>
            <p class="donor-hint">Live from <code>GET /api/donor/dashboard</code>.</p>
            <div class="donor-stats">
                <div class="donor-stat"><div class="num" id="stUnits">0</div><div class="lbl">Total Units</div></div>
                <div class="donor-stat"><div class="num" id="stDonations">0</div><div class="lbl">Total Donations</div></div>
                <div class="donor-stat"><div class="num" id="stPendingReq">0</div><div class="lbl">Pending Group Requests</div></div>
                <div class="donor-stat"><div class="num" id="stWeekBags">0</div><div class="lbl">Week Max Bags</div></div>
            </div>
        </div>

        <div class="donor-split">
            <div class="donor-card">
                <h3>Weekly availability</h3>
                <div class="donor-row">
                    <div>
                        <label class="donor-label" for="weekStartDate">Week start date</label>
                        <input id="weekStartDate" class="donor-input" type="date">
                    </div>
                    <div>
                        <label class="donor-label" for="isAvailable">Availability</label>
                        <select id="isAvailable" class="donor-select">
                            <option value="true" selected>Available</option>
                            <option value="false">Not Available</option>
                        </select>
                    </div>
                </div>
                <div class="donor-row">
                    <div>
                        <label class="donor-label" for="maxBagsPossible">Max bags possible</label>
                        <input id="maxBagsPossible" class="donor-input" type="number" min="0" max="10" value="1">
                    </div>
                    <div>
                        <label class="donor-label" for="availabilityNotes">Notes (optional)</label>
                        <input id="availabilityNotes" class="donor-input" placeholder="Free this week after 5pm">
                    </div>
                </div>
                <div class="donor-btns">
                    <button id="btnAvailability" class="donor-btn donor-btn-main" onclick="upsertAvailability()">Save Availability</button>
                    <button class="donor-btn donor-btn-soft" onclick="loadAvailability()">Refresh Availability</button>
                </div>
            </div>

            <div class="donor-card">
                <h3>Health check</h3>
                <div class="donor-row">
                    <div>
                        <label class="donor-label" for="checkDateTime">Check datetime</label>
                        <input id="checkDateTime" class="donor-input" type="datetime-local">
                    </div>
                    <div>
                        <label class="donor-label" for="weightKg">Weight (kg)</label>
                        <input id="weightKg" class="donor-input" type="number" min="30" max="250" step="0.1" value="60">
                    </div>
                </div>
                <div class="donor-row">
                    <div>
                        <label class="donor-label" for="temperatureC">Temperature (C)</label>
                        <input id="temperatureC" class="donor-input" type="number" min="34" max="43" step="0.1" value="36.8">
                    </div>
                    <div>
                        <label class="donor-label" for="hemoglobin">Hemoglobin (optional)</label>
                        <input id="hemoglobin" class="donor-input" type="number" min="5" max="25" step="0.1" placeholder="13.5">
                    </div>
                </div>
                <label class="donor-label" for="healthNotes">Notes (optional)</label>
                <textarea id="healthNotes" class="donor-textarea" placeholder="Donor is fit today"></textarea>
                <div class="donor-btns">
                    <button id="btnHealthCheck" class="donor-btn donor-btn-accent" onclick="logHealthCheck()">Log Health Check</button>
                    <button class="donor-btn donor-btn-soft" onclick="loadHealthChecks()">Refresh Health Checks</button>
                </div>
            </div>
        </div>

        <div class="donor-card">
            <h3>Bag donation logging</h3>
            <div class="donor-row">
                <div>
                    <label class="donor-label" for="bankId">Blood bank</label>
                    <select id="bankId" class="donor-select"></select>
                </div>
                <div>
                    <label class="donor-label" for="donationDateTime">Donation datetime</label>
                    <input id="donationDateTime" class="donor-input" type="datetime-local">
                </div>
            </div>
            <div class="donor-row">
                <div>
                    <label class="donor-label" for="donationBloodGroup">Blood group</label>
                    <select id="donationBloodGroup" class="donor-select">
                        <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                        <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                    </select>
                </div>
                <div>
                    <label class="donor-label" for="componentType">Component</label>
                    <select id="componentType" class="donor-select">
                        <option selected>WholeBlood</option>
                        <option>Plasma</option>
                        <option>Platelets</option>
                        <option>RBC</option>
                    </select>
                </div>
            </div>
            <div class="donor-row">
                <div>
                    <label class="donor-label" for="unitsDonated">Units donated (bags)</label>
                    <input id="unitsDonated" class="donor-input" type="number" min="1" max="5" value="1">
                </div>
                <div>
                    <label class="donor-label" for="linkedRequestId">Linked request ID (optional)</label>
                    <input id="linkedRequestId" class="donor-input" type="number" min="1" placeholder="e.g. 5">
                </div>
            </div>
            <label class="donor-label" for="donationNotes">Notes (optional)</label>
            <textarea id="donationNotes" class="donor-textarea" placeholder="Donation drive log"></textarea>
            <div class="donor-btns">
                <button id="btnDonation" class="donor-btn donor-btn-main" onclick="logDonation()">Log Donation</button>
                <button class="donor-btn donor-btn-soft" onclick="loadDonations()">Refresh Donations</button>
            </div>
        </div>

        <div class="donor-split">
            <div class="donor-card">
                <h3>Availability history</h3>
                <div class="donor-table-wrap">
                    <table class="donor-table">
                        <thead>
                            <tr><th>ID</th><th>Week</th><th>Status</th><th>Max Bags</th><th>Updated</th></tr>
                        </thead>
                        <tbody id="availabilityBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="donor-card">
                <h3>Health checks</h3>
                <div class="donor-table-wrap">
                    <table class="donor-table">
                        <thead>
                            <tr><th>ID</th><th>Date</th><th>Weight</th><th>Temp</th><th>Hb</th></tr>
                        </thead>
                        <tbody id="healthBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="donor-card">
            <h3>Donation history</h3>
            <div class="donor-table-wrap">
                <table class="donor-table">
                    <thead>
                        <tr><th>ID</th><th>Date</th><th>Bank</th><th>Group</th><th>Component</th><th>Units</th><th>Linked Request</th></tr>
                    </thead>
                    <tbody id="donationBody"></tbody>
                </table>
            </div>
        </div>

        <div class="donor-card">
            <h3>API response</h3>
            <pre id="out" class="donor-pre"></pre>
        </div>
    </div>

    <div id="toastStack" class="donor-toast-stack"></div>
@endsection

@push('scripts')
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
    element.className = `donor-toast ${type === 'error' ? 'error' : 'ok'}`;
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
        badge.className = 'donor-badge ok';
        badge.textContent = 'Eligible';
        return;
    }
    if (isEligible === false) {
        badge.className = 'donor-badge danger';
        badge.textContent = 'Not Eligible';
        return;
    }
    badge.className = 'donor-badge warn';
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
                <td>${row.is_available ? '<span class="donor-badge ok">Available</span>' : '<span class="donor-badge danger">Not Available</span>'}</td>
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
@endpush
