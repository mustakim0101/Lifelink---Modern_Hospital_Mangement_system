@extends('ui.layouts.app')

@section('title', 'Donor Dashboard')
@section('workspace_label', 'Blood donor workspace')
@section('hero_badge', 'Donor Mode')
@section('hero_title', 'Manage availability, review blood requests, and track donation history.')
@section('hero_description', 'This donor workspace now stays focused on donor-owned actions only. Staff-entered health checks and donation logging are handled by Blood Bank nurses and Blood Bank IT workers.')
@section('meta_title', 'Donor Dashboard')
@section('meta_copy', 'Availability, request response, and donation history')

@push('styles')
<style>
    :root {
        --donor-ink: #0f172a;
        --donor-muted: #475569;
        --donor-line: rgba(15, 23, 42, 0.12);
        --donor-card: rgba(255, 255, 255, 0.94);
        --donor-primary: #0369a1;
        --donor-primary-strong: #075985;
        --donor-accent: #dc2626;
        --donor-ok: #166534;
        --donor-warn: #b45309;
        --donor-shadow: 0 18px 36px rgba(2, 6, 23, 0.14);
    }

    .donor-grid,
    .donor-row,
    .donor-actions,
    .donor-stats,
    .donor-cards {
        display: grid;
        gap: 12px;
    }

    .donor-grid { gap: 14px; }
    .donor-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .donor-actions { grid-template-columns: repeat(3, max-content); justify-content: start; }
    .donor-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .donor-cards { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }

    .donor-card {
        border: 1px solid var(--donor-line);
        border-radius: 18px;
        background: var(--donor-card);
        box-shadow: var(--donor-shadow);
        padding: 14px;
    }

    .donor-card h3 { margin: 0; }
    .donor-hint { margin: 6px 0 0; color: var(--donor-muted); font-size: .94rem; line-height: 1.7; }

    .donor-label {
        display: block;
        margin-bottom: 6px;
        color: var(--donor-muted);
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .donor-input,
    .donor-select,
    .donor-textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, .18);
        background: rgba(255, 255, 255, .96);
        color: var(--donor-ink);
        font: inherit;
        padding: 10px 11px;
        outline: none;
    }

    .donor-input:focus,
    .donor-select:focus,
    .donor-textarea:focus {
        border-color: var(--donor-primary);
        box-shadow: 0 0 0 3px rgba(3, 105, 161, .14);
    }

    .donor-textarea { min-height: 90px; resize: vertical; }

    .donor-btn {
        border: 0;
        border-radius: 11px;
        padding: 10px 13px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .donor-btn[disabled] { opacity: .62; pointer-events: none; }
    .donor-btn-main { background: var(--donor-primary); color: #fff; }
    .donor-btn-main:hover { background: var(--donor-primary-strong); }
    .donor-btn-soft { background: rgba(15, 23, 42, .08); color: var(--donor-ink); }
    .donor-btn-accent { background: var(--donor-accent); color: #fff; }

    .donor-stat {
        border: 1px solid var(--donor-line);
        border-radius: 14px;
        background: rgba(255, 255, 255, .9);
        padding: 12px;
        text-align: center;
    }

    .donor-stat small {
        display: block;
        margin-bottom: 6px;
        color: var(--donor-muted);
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
    }

    .donor-stat strong {
        display: block;
        font-size: 1.5rem;
        font-family: "Sora", "Trebuchet MS", sans-serif;
    }

    .donor-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .05em;
    }

    .donor-badge.ok { color: var(--donor-ok); background: rgba(22, 101, 52, .14); }
    .donor-badge.warn { color: var(--donor-warn); background: rgba(180, 83, 9, .14); }
    .donor-badge.soft { color: var(--donor-primary-strong); background: rgba(3, 105, 161, .14); }

    .donor-table-wrap {
        margin-top: 10px;
        border: 1px solid var(--donor-line);
        border-radius: 12px;
        overflow: auto;
        background: rgba(255, 255, 255, .96);
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
        padding: 9px;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .donor-table th {
        position: sticky;
        top: 0;
        background: rgba(246, 250, 255, .98);
        color: var(--donor-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .donor-notification {
        border: 1px solid var(--donor-line);
        border-radius: 16px;
        background: rgba(255, 255, 255, .9);
        padding: 13px;
    }

    .donor-notification h4 {
        margin: 0 0 4px;
        font-size: 1rem;
    }

    .donor-meta {
        color: var(--donor-muted);
        font-size: .9rem;
        line-height: 1.7;
    }

    .donor-pre {
        margin: 0;
        min-height: 120px;
        max-height: 320px;
        overflow: auto;
        border-radius: 12px;
        border: 1px solid var(--donor-line);
        background: #101c33;
        color: #d7e3ff;
        padding: 11px;
        font-size: 12px;
    }

    .donor-toast-stack {
        position: fixed;
        right: 12px;
        bottom: 12px;
        display: grid;
        gap: 8px;
        z-index: 40;
    }

    .donor-toast {
        border-radius: 10px;
        padding: 9px 12px;
        color: #fff;
        font-size: 12px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .3);
    }

    .donor-toast.ok { background: #166534; }
    .donor-toast.error { background: #b91c1c; }

    @media (max-width: 1100px) {
        .donor-row,
        .donor-actions,
        .donor-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="/ui/donor-dashboard">
        <strong>Donor Dashboard</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/blood-matching">
        <strong>Blood Matching</strong>
        <span>Staff-side operations</span>
    </a>
    <a href="/ui/patient-portal">
        <strong>Patient Portal</strong>
        <span>Shared shell reference</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Donor-owned tasks</strong>
        <p>This page now keeps only donor-owned actions: donor profile, weekly availability, request notifications, and donation history.</p>
    </div>
    <div class="app-shell__sidebar-card">
        <strong>Staff-owned tasks</strong>
        <p>Health screening is entered by a Blood Bank nurse, and actual donation logging is entered by Blood Bank IT/Admin staff after you physically arrive.</p>
    </div>
@endsection

@section('content')
    <div class="donor-grid">
        <div class="donor-stats">
            <div class="donor-stat"><small>Total Donations</small><strong id="stDonations">0</strong></div>
            <div class="donor-stat"><small>Total Units</small><strong id="stUnits">0</strong></div>
            <div class="donor-stat"><small>Pending Requests</small><strong id="stPendingReq">0</strong></div>
            <div class="donor-stat"><small>Week Max Bags</small><strong id="stWeekBags">0</strong></div>
        </div>

        <div class="donor-row">
            <div class="donor-card">
                <h3>Access and donor role</h3>
                <p class="donor-hint">This page now prefers your normal `USER_TOKEN` automatically. If donor role is not enabled yet for the logged-in user, you can initialize it here.</p>
                <label class="donor-label" for="tokenInput">Bearer token</label>
                <input id="tokenInput" class="donor-input" placeholder="Paste donor token">
                <div class="donor-row" style="margin-top: 12px;">
                    <div>
                        <label class="donor-label" for="enrollBloodGroup">Enroll blood group</label>
                        <select id="enrollBloodGroup" class="donor-select">
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>AB+</option><option>AB-</option><option selected>O+</option><option>O-</option>
                        </select>
                    </div>
                    <div>
                        <label class="donor-label">Eligibility state</label>
                        <div id="eligibilityBadge" class="donor-badge warn">Unknown</div>
                    </div>
                </div>
                <div class="donor-actions" style="margin-top: 12px;">
                    <button id="btnUseUserToken" class="donor-btn donor-btn-soft" type="button" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    <button id="btnUseDonorToken" class="donor-btn donor-btn-soft" type="button" onclick="useStoredDonorToken()">Use DONOR_TOKEN</button>
                    <button id="btnRefresh" class="donor-btn donor-btn-main" type="button" onclick="refreshAll()">Refresh</button>
                    <button id="btnEnroll" class="donor-btn donor-btn-soft" type="button" onclick="enrollDonorRole()">Enable Donor Role</button>
                </div>
            </div>

            <div class="donor-card">
                <h3>Donor profile</h3>
                <p class="donor-hint">This is the donor-side summary only. The latest health screening shown here is staff-entered reference, not donor self-entry.</p>
                <div id="profileMeta" class="donor-meta">No donor profile loaded yet.</div>
            </div>
        </div>

        <div class="donor-card">
            <h3>Weekly availability</h3>
            <div class="donor-row" style="margin-top: 12px;">
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
                <div>
                    <label class="donor-label" for="maxBagsPossible">Max bags possible</label>
                    <input id="maxBagsPossible" class="donor-input" type="number" min="0" max="10" value="1">
                </div>
                <div>
                    <label class="donor-label" for="availabilityNotes">Availability note</label>
                    <input id="availabilityNotes" class="donor-input" placeholder="Free this week after 5pm">
                </div>
            </div>
            <div class="donor-actions" style="margin-top: 12px;">
                <button id="btnAvailability" class="donor-btn donor-btn-main" type="button" onclick="upsertAvailability()">Save Availability</button>
                <button class="donor-btn donor-btn-soft" type="button" onclick="loadAvailability()">Refresh Availability</button>
            </div>
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
            <h3>Request notifications</h3>
            <p class="donor-hint">Accept or decline blood requests here. If you accept, the next steps happen physically at the hospital with Blood Bank staff.</p>
            <div class="donor-actions" style="margin-top: 12px;">
                <button id="btnNotifications" class="donor-btn donor-btn-main" type="button" onclick="loadNotifications()">Refresh Notifications</button>
            </div>
            <div id="notificationsGrid" class="donor-cards" style="margin-top: 12px;"></div>
        </div>

        <div class="donor-card">
            <h3>Donation history</h3>
            <p class="donor-hint">This is read-only donor history. Actual donation records are now entered by Blood Bank staff after successful screening.</p>
            <div class="donor-actions" style="margin-top: 12px;">
                <button id="btnDonations" class="donor-btn donor-btn-main" type="button" onclick="loadDonations()">Refresh Donations</button>
            </div>
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
function write(value) { out.textContent = typeof value === 'string' ? value : JSON.stringify(value, null, 2); }
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
function setBusy(id, busy) {
    const button = byId(id);
    if (!button) return;
    button.disabled = busy;
    button.dataset.label = button.dataset.label || button.textContent;
    button.textContent = busy ? 'Working...' : button.dataset.label;
}
function useStoredDonorToken() { byId('tokenInput').value = localStorage.getItem('DONOR_TOKEN') || ''; }
function useStoredUserToken() { byId('tokenInput').value = localStorage.getItem('USER_TOKEN') || ''; }
function bootstrapToken() {
    const userToken = localStorage.getItem('USER_TOKEN') || '';
    const donorToken = localStorage.getItem('DONOR_TOKEN') || '';
    byId('tokenInput').value = userToken || donorToken;
}
function hasToken() { return !!byId('tokenInput').value.trim(); }

async function call(path, method = 'GET', body = null, query = null, { requireToken = true } = {}) {
    const token = byId('tokenInput').value.trim();
    if (requireToken && !token) {
        return { status: 401, data: { message: 'Token missing. Use USER_TOKEN or DONOR_TOKEN.' } };
    }

    const queryString = query ? new URLSearchParams(query).toString() : '';
    const endpoint = `${API}${path}${queryString ? `?${queryString}` : ''}`;
    const response = await fetch(endpoint, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
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
        badge.className = 'donor-badge warn';
        badge.textContent = 'Needs staff review';
        return;
    }
    badge.className = 'donor-badge warn';
    badge.textContent = 'Unknown';
}

function renderProfile(data) {
    if (!data?.donor) {
        byId('profileMeta').textContent = 'No donor profile loaded yet.';
        renderEligibility(null);
        return;
    }

    const donor = data.donor;
    const latestCheck = data.latest_health_check;
    byId('profileMeta').innerHTML = `
        <strong>${html(donor.full_name || 'Unknown donor')}</strong><br>
        Email: ${html(donor.email || '-')}<br>
        Donor ID: <strong>#${html(donor.donor_id)}</strong><br>
        Blood group: <strong>${html(donor.blood_group || '-')}</strong><br>
        Last donation: ${donor.last_donation_date ? new Date(donor.last_donation_date).toLocaleString() : 'No donation yet'}<br>
        Latest staff health check: ${latestCheck?.check_datetime ? new Date(latestCheck.check_datetime).toLocaleString() : 'No staff check logged yet'}
    `;
    renderEligibility(donor.is_eligible);
}

async function loadDashboard() {
    const result = await call('/donor/dashboard');
    write(result);
    if (result.status >= 300) {
        toast(result.data?.message || 'Could not load donor dashboard', 'error');
        return;
    }

    const stats = result.data?.stats || {};
    byId('stDonations').textContent = stats.total_donations || 0;
    byId('stUnits').textContent = stats.total_units_donated || 0;
    byId('stPendingReq').textContent = stats.pending_group_requests || 0;
    byId('stWeekBags').textContent = result.data?.current_week_availability?.max_bags_possible || 0;
    renderProfile(result.data);
}

async function loadAvailability() {
    const result = await call('/donor/availability');
    if (result.status >= 300) {
        write(result);
        toast(result.data?.message || 'Could not load availability', 'error');
        return;
    }

    const rows = result.data?.availabilities || [];
    byId('availabilityBody').innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${html(row.week_start_date || '-')}</td>
                <td>${row.is_available ? '<span class="donor-badge ok">Available</span>' : '<span class="donor-badge warn">Not available</span>'}</td>
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
    const result = await call('/donor/availability', 'POST', payload);
    setBusy('btnAvailability', false);
    write(result);

    if (result.status >= 300) {
        toast(result.data?.message || 'Could not update availability', 'error');
        return;
    }

    toast('Availability updated');
    await loadAvailability();
    await loadDashboard();
}

async function loadNotifications() {
    setBusy('btnNotifications', true);
    const result = await call('/donor/notifications', 'GET', null, { limit: 12 });
    setBusy('btnNotifications', false);
    write(result);

    if (result.status >= 300) {
        toast(result.data?.message || 'Could not load notifications', 'error');
        return;
    }

    const rows = result.data?.notifications || [];
    byId('notificationsGrid').innerHTML = rows.length
        ? rows.map((row) => `
            <article class="donor-notification">
                <h4>${html(row.title || 'Blood request notification')}</h4>
                <div class="donor-meta">
                    Request #${row.request_id} | Need ${html(row.request?.blood_group_needed || '-')} ${html(row.request?.component_type || '-')} | Units ${row.request?.units_required ?? '-'}<br>
                    Department: ${html(row.request?.department_name || '-')} | Urgency: ${html(row.request?.urgency || '-')}<br>
                    Status: <strong>${html(row.status || '-')}</strong>${row.response_status ? ` | Response: <strong>${html(row.response_status)}</strong>` : ''}<br>
                    Sent: ${row.sent_at ? new Date(row.sent_at).toLocaleString() : '-'}
                </div>
                <p class="donor-hint">${html(row.message || 'No message')}</p>
                <div class="donor-actions" style="margin-top: 12px;">
                    <button class="donor-btn donor-btn-soft" type="button" onclick="markNotificationRead(${row.id})">Mark Read</button>
                    <button class="donor-btn donor-btn-main" type="button" onclick="respondToNotification(${row.id}, 'Accepted')">Accept</button>
                    <button class="donor-btn donor-btn-accent" type="button" onclick="respondToNotification(${row.id}, 'Declined')">Decline</button>
                </div>
            </article>
        `).join('')
        : '<div class="donor-card"><p class="donor-hint">No notifications yet.</p></div>';
}

async function markNotificationRead(notificationId) {
    const result = await call(`/donor/notifications/${notificationId}/read`, 'POST');
    write(result);
    if (result.status >= 300) {
        toast(result.data?.message || 'Could not mark notification as read', 'error');
        return;
    }
    toast('Notification marked as read');
    await loadNotifications();
}

async function respondToNotification(notificationId, response) {
    const result = await call(`/donor/notifications/${notificationId}/respond`, 'POST', { response });
    write(result);
    if (result.status >= 300) {
        toast(result.data?.message || `Could not ${response.toLowerCase()} notification`, 'error');
        return;
    }
    toast(`Notification ${response.toLowerCase()}`);
    await Promise.all([loadNotifications(), loadDashboard()]);
}

async function loadDonations() {
    setBusy('btnDonations', true);
    const result = await call('/donor/donations', 'GET', null, { limit: 20 });
    setBusy('btnDonations', false);
    if (result.status >= 300) {
        write(result);
        toast(result.data?.message || 'Could not load donations', 'error');
        return;
    }

    const rows = result.data?.donations || [];
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

async function enrollDonorRole() {
    const payload = {
        bloodGroup: byId('enrollBloodGroup').value,
        notes: 'Self-enrolled from donor dashboard UI',
    };

    setBusy('btnEnroll', true);
    const result = await call('/donor/enroll', 'POST', payload);
    setBusy('btnEnroll', false);
    write(result);

    if (result.status >= 300) {
        toast(result.data?.message || 'Could not enable donor role', 'error');
        return;
    }

    toast('Donor role enabled');
    await refreshAll();
}

async function refreshAll({ silentIfMissingToken = false } = {}) {
    if (!hasToken()) {
        if (!silentIfMissingToken) {
            write({ status: 401, data: { message: 'Token missing. Use USER_TOKEN or DONOR_TOKEN.' } });
            toast('Token missing. Use USER_TOKEN or DONOR_TOKEN.', 'error');
        }
        return;
    }

    setBusy('btnRefresh', true);
    try {
        await Promise.all([
            loadDashboard(),
            loadAvailability(),
            loadNotifications(),
            loadDonations(),
        ]);
        toast('Donor dashboard refreshed');
    } finally {
        setBusy('btnRefresh', false);
    }
}

function boot() {
    bootstrapToken();
    if (hasToken()) {
        refreshAll({ silentIfMissingToken: true });
    } else {
        write('Paste a donor-capable token or use stored USER_TOKEN to load your donor workspace.');
    }
}

boot();
</script>
@endpush
