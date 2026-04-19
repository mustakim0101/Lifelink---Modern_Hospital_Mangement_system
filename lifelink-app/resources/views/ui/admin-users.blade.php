@extends('ui.layouts.app')

@section('title', 'Admin Control Center')
@section('role_theme', 'admin')
@section('workspace_label', 'Admin operations workspace')
@section('hero_badge', 'Admin Mode')
@section('hero_title', 'Approve staff applications and finish staff setup from one place.')
@section('hero_description', 'Review applicants, decide status, and complete staff setup with the approved user ID.')
@section('meta_title', 'Admin Control Center')
@section('meta_copy', 'Account state, staff approval, and role setup')

@push('styles')
<style>
    :root {
        --admin-ink: #172436;
        --admin-muted: #5a6d7b;
        --admin-line: rgba(23, 36, 54, 0.12);
        --admin-card: rgba(255, 255, 255, 0.92);
        --admin-primary: #1d4ed8;
        --admin-primary-strong: #1e40af;
        --admin-accent: #0f766e;
        --admin-danger: #b91c1c;
        --admin-warm: #c2410c;
        --admin-shadow: 0 16px 36px rgba(18, 34, 50, 0.14);
        --admin-space: 12px;
        --admin-space-md: 14px;
    }

    .admin-grid,
    .admin-row,
    .admin-actions,
    .admin-controls,
    .admin-card-grid,
    .admin-summary {
        display: grid;
        gap: var(--admin-space);
    }
    .admin-panel { display: none; }

    .admin-grid { gap: var(--admin-space-md); }
    .admin-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .admin-card-grid { grid-template-columns: 1fr; }
    .admin-controls { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .admin-actions { grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); }
    .admin-actions {
        align-items: stretch;
    }
    .admin-summary { grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); }

    .admin-card,
    .admin-pending-card {
        border: 1px solid var(--admin-line);
        border-radius: 16px;
        background: var(--admin-card);
        box-shadow: var(--admin-shadow);
        padding: 14px;
    }

    .admin-card {
        display: grid;
        gap: var(--admin-space);
        align-content: start;
    }

    .admin-section-stack {
        display: grid;
        gap: var(--admin-space);
        margin-top: var(--admin-space);
    }

    .admin-card h3,
    .admin-pending-card h3 { margin: 0; }

    .admin-hint { margin: 0; color: var(--admin-muted); font-size: 0.92rem; line-height: 1.7; }

    .admin-label {
        display: block;
        margin-bottom: 5px;
        color: var(--admin-muted);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .admin-input,
    .admin-select,
    .admin-textarea {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(23, 36, 54, 0.18);
        background: rgba(255, 255, 255, 0.96);
        color: var(--admin-ink);
        font: inherit;
        padding: 10px 11px;
        outline: none;
    }

    .admin-input:focus,
    .admin-select:focus,
    .admin-textarea:focus {
        border-color: var(--admin-primary);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.14);
    }

    .admin-textarea {
        min-height: 84px;
        resize: vertical;
    }

    .admin-btn {
        border: 0;
        border-radius: 10px;
        padding: 10px 12px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        min-height: 40px;
    }

    .admin-btn-main { background: var(--admin-primary); color: #fff; }
    .admin-btn-main:hover { background: var(--admin-primary-strong); }
    .admin-btn-soft { background: rgba(23, 36, 54, 0.08); color: var(--admin-ink); }
    .admin-btn-danger { background: var(--admin-danger); color: #fff; }
    .admin-btn-accent { background: var(--admin-accent); color: #fff; }
    .admin-btn-warm { background: var(--admin-warm); color: #fff; }

    .admin-stat {
        border-radius: 14px;
        border: 1px solid var(--admin-line);
        background: rgba(255, 255, 255, 0.84);
        padding: 12px;
    }

    .admin-stat small {
        display: block;
        margin-bottom: 6px;
        color: var(--admin-muted);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
    }

    .admin-stat strong { display: block; font-size: 1.5rem; }

    .admin-context {
        margin: 0;
        min-height: 120px;
        max-height: 260px;
        overflow: auto;
        border-radius: 11px;
        border: 1px solid var(--admin-line);
        background: #101c33;
        color: #d7e3ff;
        padding: 11px;
        font-size: 12px;
    }

    .admin-pending-head,
    .admin-pending-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .admin-pending-meta {
        margin-top: 12px;
    }

    .admin-pending-card {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.2fr) auto;
        align-items: start;
        gap: 12px;
    }

    .admin-pending-side {
        min-width: 0;
    }

    .admin-pending-actions {
        display: flex;
        flex-direction: column;
        gap: var(--admin-space);
        align-items: stretch;
        justify-content: center;
        min-width: 124px;
    }

    .admin-pending-actions .admin-btn {
        width: 100%;
    }

    #shell-sidebar {
        align-self: start;
    }

    .admin-chip {
        border-radius: 999px;
        background: rgba(23, 36, 54, 0.08);
        color: var(--admin-ink);
        padding: 5px 9px;
        font-size: 0.76rem;
        font-weight: 700;
    }

    .admin-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(194, 65, 12, 0.12);
        color: var(--admin-warm);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .admin-search-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: var(--admin-space);
    }

    .admin-user-search-results {
        display: grid;
        gap: var(--admin-space);
        margin-top: var(--admin-space);
    }

    .admin-user-result {
        border: 1px solid var(--admin-line);
        border-radius: 12px;
        padding: 12px;
        display: grid;
        gap: 8px;
        background: rgba(255, 255, 255, 0.88);
    }

    .admin-user-result-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media (max-width: 980px) {
        .admin-row,
        .admin-card-grid,
        .admin-controls,
        .admin-actions,
        .admin-summary,
        .admin-pending-card,
        .admin-search-grid {
            grid-template-columns: 1fr;
        }

        .admin-pending-actions {
            flex-direction: row;
            min-width: 0;
        }

        .admin-pending-actions .admin-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#admin-overview-panel" data-panel="admin-overview-panel">
        <strong>Overview</strong>
    </a>
    <a href="#admin-account-panel" data-panel="admin-account-panel">
        <strong>Account control</strong>
    </a>
    <a href="#admin-profile-panel" data-panel="admin-profile-panel">
        <strong>Personal Profile</strong>
    </a>
    <a href="#admin-queue-panel" data-panel="admin-queue-panel">
        <strong>Pending Queue</strong>
    </a>
    <a href="#admin-setup-panel" data-panel="admin-setup-panel">
        <strong>Staff Setup</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="admin-grid">
        <div id="admin-overview-panel" class="ll-section admin-panel" data-display="block">
            <div class="admin-summary">
                <div class="admin-stat"><small>Patients</small><strong id="totalPatients">0</strong></div>
                <div class="admin-stat"><small>Doctors</small><strong id="totalDoctors">0</strong></div>
                <div class="admin-stat"><small>Nurses</small><strong id="totalNurses">0</strong></div>
                <div class="admin-stat"><small>IT workers</small><strong id="totalItWorkers">0</strong></div>
                <div class="admin-stat"><small>Donors</small><strong id="totalDonors">0</strong></div>
                <div class="admin-stat"><small>Departments</small><strong id="totalDepartments">0</strong></div>
                <div class="admin-stat"><small>Applications</small><strong id="totalApplications">0</strong></div>
                <div class="admin-stat"><small>Admissions</small><strong id="totalAdmissions">0</strong></div>
                <div class="admin-stat"><small>Blood requests</small><strong id="totalBloodRequests">0</strong></div>
                <div class="admin-stat"><small>Inventory rows</small><strong id="totalInventoryRows">0</strong></div>
                <div class="admin-stat"><small>Blood units</small><strong id="totalInventoryUnits">0</strong></div>
            </div>
        </div>

        <div id="admin-account-panel" class="ll-section admin-panel" data-display="block">
            <div class="admin-card">
                <h3>User search</h3>
                <p class="admin-hint">Find any user by name, role, or department, then send the ID into account control.</p>
                <div class="admin-search-grid">
                    <div>
                        <label class="admin-label" for="accountSearchName">Name or email</label>
                        <input id="accountSearchName" class="admin-input" placeholder="e.g. Sarah, john@lifelink.com">
                    </div>
                    <div>
                        <label class="admin-label" for="accountSearchRole">Role</label>
                        <input id="accountSearchRole" class="admin-input" placeholder="e.g. Doctor, Nurse, ITWorker">
                    </div>
                    <div>
                        <label class="admin-label" for="accountSearchDepartment">Department</label>
                        <input id="accountSearchDepartment" class="admin-input" placeholder="e.g. Cardiology, Blood Bank">
                    </div>
                </div>
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-main" type="button" onclick="searchAdminUsers()">Search users</button>
                    <button class="admin-btn admin-btn-soft" type="button" onclick="resetAdminUserSearch()">Reset search</button>
                </div>
                <div id="adminUserSearchResults" class="admin-user-search-results"></div>

                <h3>Account control</h3>
                <p class="admin-hint">Freeze, unfreeze, or inspect a user account using the stored admin token.</p>
                <label class="admin-label" for="userId">Target user id</label>
                <input id="userId" class="admin-input" placeholder="target user id">
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-danger" type="button" onclick="freezeUser()">Freeze</button>
                    <button class="admin-btn admin-btn-accent" type="button" onclick="unfreezeUser()">Unfreeze</button>
                    <button class="admin-btn admin-btn-soft" type="button" onclick="statusUser()">Check status</button>
                </div>
            </div>
        </div>

        <div id="admin-profile-panel" class="admin-card ll-section admin-panel" data-display="block">
            <div id="adminProfileMount"></div>
        </div>

        <div id="admin-queue-panel" class="admin-card ll-section admin-panel" data-display="block">
            <h3>Pending applicant queue</h3>
            <p class="admin-hint">Review from cards first, then push approved users into setup.</p>
            <div class="admin-summary admin-section-stack">
                <div class="admin-stat"><small>Loaded</small><strong id="queueLoadedCount">0</strong></div>
                <div class="admin-stat"><small>Cards currently shown</small><strong id="queueShownCount">0</strong></div>
                <div class="admin-stat"><small>Pending</small><strong id="queuePendingCount">0</strong></div>
                <div class="admin-stat"><small>Waiting for review</small><strong id="queueWaitingCount">0</strong></div>
            </div>
            <div class="admin-controls">
                <div>
                    <label class="admin-label" for="queueSearchInput">Applicant search</label>
                    <input id="queueSearchInput" class="admin-input" placeholder="Applicant name or email">
                </div>
                <div>
                    <label class="admin-label" for="queueApplicationIdFilter">Application ID</label>
                    <input id="queueApplicationIdFilter" class="admin-input" type="number" min="1" placeholder="Optional application id">
                </div>
                <div>
                    <label class="admin-label" for="queueStatusFilter">Status</label>
                    <select id="queueStatusFilter" class="admin-select">
                        <option value="Pending" selected>Pending</option>
                        <option value="">All</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label" for="queueRoleFilter">Applied role</label>
                    <select id="queueRoleFilter" class="admin-select">
                        <option value="">All roles</option>
                        <option value="Doctor">Doctor</option>
                        <option value="Nurse">Nurse</option>
                        <option value="ITWorker">IT Worker</option>
                        <option value="Applicant">Applicant</option>
                        <option value="Donor">Donor</option>
                        <option value="Patient">Patient</option>
                    </select>
                </div>
            </div>
            <div class="admin-actions">
                <button class="admin-btn admin-btn-main" type="button" onclick="loadPendingApplications()">Apply filters</button>
                <button class="admin-btn admin-btn-soft" type="button" onclick="resetPendingFilters()">Reset filters</button>
            </div>
            <div id="pendingCards" class="admin-card-grid"></div>
            <div id="pendingCardsPagination" class="ui-list-pagination"></div>
        </div>

        <div id="admin-setup-panel" class="admin-grid ll-section admin-panel" data-display="grid">
            <div class="admin-card">
                <h3>Doctor department setup</h3>
                <p class="admin-hint">Doctor profile uses the same user ID as the approved account.</p>
                <div class="admin-controls">
                    <div>
                        <label class="admin-label" for="doctorUserId">Doctor user ID</label>
                        <input id="doctorUserId" class="admin-input" type="number" placeholder="Approved doctor user id">
                    </div>
                    <div>
                        <label class="admin-label" for="doctorDepartmentId">Department</label>
                        <select id="doctorDepartmentId" class="admin-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                </div>
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-warm" type="button" onclick="upsertDoctorProfile()">Save doctor setup</button>
                </div>
            </div>

            <div class="admin-card">
                <h3>Nurse department setup</h3>
                <p class="admin-hint">Create or update nurse profile and bind department scope.</p>
                <div class="admin-controls">
                    <div>
                        <label class="admin-label" for="nurseUserId">Nurse user ID</label>
                        <input id="nurseUserId" class="admin-input" type="number" placeholder="Approved nurse user id">
                    </div>
                    <div>
                        <label class="admin-label" for="nurseDepartmentId">Department</label>
                        <select id="nurseDepartmentId" class="admin-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                </div>
                <label class="admin-label" for="wardAssignmentNote">Ward assignment note</label>
                <textarea id="wardAssignmentNote" class="admin-textarea" placeholder="Optional shift, floor, or ward note"></textarea>
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-accent" type="button" onclick="upsertNurseProfile()">Save nurse setup</button>
                </div>
            </div>

            <div class="admin-card">
                <h3>IT worker department setup</h3>
                <p class="admin-hint">Assign department scope for IT ward and bed operations.</p>
                <div class="admin-controls">
                    <div>
                        <label class="admin-label" for="itUserId">IT worker user ID</label>
                        <input id="itUserId" class="admin-input" type="number" placeholder="Approved IT worker user id">
                    </div>
                    <div>
                        <label class="admin-label" for="itDepartmentId">Department</label>
                        <select id="itDepartmentId" class="admin-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                </div>
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-main" type="button" onclick="assignItDepartment()">Assign IT department</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const out = document.getElementById('out');
const ctx = document.getElementById('ctx');
const API = '/api';
const adminPanelIds = ['admin-overview-panel', 'admin-account-panel', 'admin-profile-panel', 'admin-queue-panel', 'admin-setup-panel'];
let adminPanelNavigation = null;
const state = {
    pendingApplications: [],
    departments: [],
    adminSearchResults: [],
    queueStats: {
        loaded: 0,
        pending: 0,
        waiting_for_review: 0,
    },
    overviewTotals: {},
    pagination: {
        pendingCardsPageSize: 5,
        pendingCardsPage: 1,
    },
};

function write(data) {
    if (!window.lifeLinkShell?.isDebugEnabled() || !out) return;
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function refreshContext() {
    const tokenPresent = !!localStorage.getItem('ADMIN_TOKEN');
    const data = {
        ADMIN_USER_ID: localStorage.getItem('ADMIN_USER_ID'),
        ADMIN_EMAIL: localStorage.getItem('ADMIN_EMAIL'),
        ADMIN_TOKEN_PRESENT: tokenPresent,
        CURRENT_USER_EMAIL: localStorage.getItem('CURRENT_USER_EMAIL'),
        CURRENT_USER_ROLES: JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]'),
        PATIENT_ID: localStorage.getItem('PATIENT_ID'),
        PATIENT_EMAIL: localStorage.getItem('PATIENT_EMAIL'),
    };
    if (ctx) {
        ctx.textContent = JSON.stringify(data, null, 2);
    }
}

function adminToken() {
    return localStorage.getItem('ADMIN_TOKEN');
}

function targetId() {
    return document.getElementById('userId').value.trim();
}

function loadPatientId() {
    document.getElementById('userId').value = localStorage.getItem('PATIENT_ID') || '';
}

async function call(path, method, body = null) {
    const token = adminToken();
    if (!token) return { status: 401, data: { message: 'ADMIN_TOKEN missing. Create or login admin from /ui/login first.' } };

    const headers = { Accept: 'application/json', 'Content-Type': 'application/json', Authorization: `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    try { return { status: res.status, data: JSON.parse(text) }; } catch { return { status: res.status, data: text }; }
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

function normalizeId(value) {
    const parsed = Number(value);
    if (!Number.isFinite(parsed) || parsed <= 0) return null;
    return Math.trunc(parsed);
}

function roleRequiresDepartment(roleName) {
    return ['Doctor', 'Nurse', 'ITWorker'].includes(String(roleName || ''));
}

function departmentIdForApplication(application) {
    if (!application) return null;
    return normalizeId(application.assigned_department_id)
        ?? normalizeId(application.applied_department_id);
}

function departmentOptionsMarkup(selectedId) {
    const normalizedSelectedId = normalizeId(selectedId);
    const options = state.departments.map((department) => {
        const departmentId = normalizeId(department.id);
        const selected = normalizedSelectedId !== null && departmentId === normalizedSelectedId ? 'selected' : '';
        return `<option value="${departmentId}" ${selected}>${escapeHtml(department.dept_name)}</option>`;
    }).join('');
    return `<option value="">Select department</option>${options}`;
}

function extractMessage(result, fallbackMessage) {
    const payload = result?.data;
    if (typeof payload === 'string' && payload.trim()) return payload;
    if (payload?.message) return payload.message;
    const errors = payload?.errors;
    if (errors && typeof errors === 'object') {
        const first = Object.values(errors).find((items) => Array.isArray(items) && items.length);
        if (first) return String(first[0]);
    }
    return fallbackMessage;
}

function queueFilters() {
    const filters = {};
    const search = document.getElementById('queueSearchInput').value.trim();
    const applicationId = document.getElementById('queueApplicationIdFilter').value.trim();
    const status = document.getElementById('queueStatusFilter').value.trim();
    const role = document.getElementById('queueRoleFilter').value.trim();

    if (search) filters.q = search;
    if (applicationId) filters.applicationId = Number(applicationId);
    if (status) filters.status = status;
    if (role) filters.role = role;
    return filters;
}

function accountSearchFilters() {
    return {
        name: document.getElementById('accountSearchName').value.trim(),
        role: document.getElementById('accountSearchRole').value.trim(),
        department: document.getElementById('accountSearchDepartment').value.trim(),
    };
}

function useAccountSearchUser(userId) {
    document.getElementById('userId').value = String(userId || '');
}

function renderAdminUserSearchResults() {
    const root = document.getElementById('adminUserSearchResults');
    if (!root) return;

    if (!state.adminSearchResults.length) {
        root.innerHTML = '<div class="admin-card"><p class="admin-hint">Run a search to find users by name, role, or department.</p></div>';
        return;
    }

    root.innerHTML = state.adminSearchResults.map((user) => {
        const roles = Array.isArray(user.roles) ? user.roles : [];
        const departments = Array.isArray(user.departments) ? user.departments : [];
        return `
            <article class="admin-user-result">
                <div class="admin-user-result-head">
                    <div>
                        <strong>${escapeHtml(user.full_name || 'Unnamed user')}</strong>
                        <p class="admin-hint">${escapeHtml(user.email || '')}</p>
                    </div>
                    <span class="admin-chip">User #${escapeHtml(user.id)}</span>
                </div>
                <div class="admin-pending-meta">
                    <span class="admin-status">${escapeHtml(user.account_status || 'Unknown')}</span>
                    <span class="admin-chip">Roles: ${escapeHtml(roles.length ? roles.join(', ') : 'None')}</span>
                    <span class="admin-chip">Dept: ${escapeHtml(departments.length ? departments.join(', ') : 'Not assigned')}</span>
                </div>
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-soft" type="button" onclick="useAccountSearchUser(${Number(user.id) || 0})">Use ID in account control</button>
                </div>
            </article>
        `;
    }).join('');
}

async function searchAdminUsers() {
    const filters = accountSearchFilters();
    const params = new URLSearchParams();
    if (filters.name) params.set('name', filters.name);
    if (filters.role) params.set('role', filters.role);
    if (filters.department) params.set('department', filters.department);
    params.set('limit', '20');

    const result = await call(`/admin/users/search?${params.toString()}`, 'GET');
    write(result);
    if (result.status < 300) {
        state.adminSearchResults = Array.isArray(result.data?.users) ? result.data.users : [];
        renderAdminUserSearchResults();
        return;
    }

    state.adminSearchResults = [];
    renderAdminUserSearchResults();
    window.alert(extractMessage(result, 'Unable to search users right now.'));
}

function resetAdminUserSearch() {
    document.getElementById('accountSearchName').value = '';
    document.getElementById('accountSearchRole').value = '';
    document.getElementById('accountSearchDepartment').value = '';
    state.adminSearchResults = [];
    renderAdminUserSearchResults();
}

function resetPendingFilters() {
    document.getElementById('queueSearchInput').value = '';
    document.getElementById('queueApplicationIdFilter').value = '';
    document.getElementById('queueStatusFilter').value = 'Pending';
    document.getElementById('queueRoleFilter').value = '';
    loadPendingApplications();
}

function updateOverviewTotals() {
    const totals = state.overviewTotals || {};
    document.getElementById('totalPatients').textContent = String(totals.total_patients ?? 0);
    document.getElementById('totalDoctors').textContent = String(totals.total_doctors ?? 0);
    document.getElementById('totalNurses').textContent = String(totals.total_nurses ?? 0);
    document.getElementById('totalItWorkers').textContent = String(totals.total_it_workers ?? 0);
    document.getElementById('totalDonors').textContent = String(totals.total_donors ?? 0);
    document.getElementById('totalDepartments').textContent = String(totals.total_departments ?? 0);
    document.getElementById('totalApplications').textContent = String(totals.total_applications ?? 0);
    document.getElementById('totalAdmissions').textContent = String(totals.total_admissions ?? 0);
    document.getElementById('totalBloodRequests').textContent = String(totals.total_blood_requests ?? 0);
    document.getElementById('totalInventoryRows').textContent = String(totals.total_inventory_rows ?? 0);
    document.getElementById('totalInventoryUnits').textContent = String(totals.total_inventory_units ?? 0);
}

function syncQueueCounters(shownCount = 0) {
    document.getElementById('queueLoadedCount').textContent = String(state.queueStats.loaded ?? state.pendingApplications.length);
    document.getElementById('queueShownCount').textContent = String(shownCount);
    document.getElementById('queuePendingCount').textContent = String(state.queueStats.pending ?? 0);
    document.getElementById('queueWaitingCount').textContent = String(state.queueStats.waiting_for_review ?? state.queueStats.pending ?? 0);
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

function renderPendingCardsPagination(pageData) {
    const root = document.getElementById('pendingCardsPagination');
    if (!root) return;
    if (pageData.totalRows <= state.pagination.pendingCardsPageSize) {
        root.innerHTML = '';
        return;
    }
    root.innerHTML = `
        <div class="ui-list-pagination__meta">Page ${pageData.page} of ${pageData.totalPages} (${pageData.totalRows} total)</div>
        <div class="ui-list-pagination__controls">
            <button class="admin-btn admin-btn-soft" type="button" ${pageData.page <= 1 ? 'disabled' : ''} onclick="prevPendingCardsPage()">Previous</button>
            <button class="admin-btn admin-btn-soft" type="button" ${pageData.page >= pageData.totalPages ? 'disabled' : ''} onclick="nextPendingCardsPage()">Next</button>
        </div>
    `;
}

function prevPendingCardsPage() {
    state.pagination.pendingCardsPage = Math.max(1, state.pagination.pendingCardsPage - 1);
    renderPendingCards();
}

function nextPendingCardsPage() {
    state.pagination.pendingCardsPage += 1;
    renderPendingCards();
}

function openSetupPanel() {
    adminPanelNavigation?.setActivePanel('admin-setup-panel', true);
}

async function loadDepartments() {
    const response = await fetch('/api/public/departments', { headers: { Accept: 'application/json' } });
    const text = await response.text();
    let data = {};
    try { data = JSON.parse(text); } catch {}
    state.departments = Array.isArray(data?.departments) ? data.departments : [];
    const options = `<option value="">Select department</option>${state.departments.map((department) => `
        <option value="${department.id}">${escapeHtml(department.dept_name)}</option>
    `).join('')}`;
        document.getElementById('doctorDepartmentId').innerHTML = options;
        document.getElementById('nurseDepartmentId').innerHTML = options;
        document.getElementById('itDepartmentId').innerHTML = options;
}

function renderPendingCards() {
    const root = document.getElementById('pendingCards');

    if (!state.pendingApplications.length) {
        root.innerHTML = '<div class="admin-card"><p class="admin-hint">No pending applicants right now.</p></div>';
        renderPendingCardsPagination({ page: 1, totalPages: 1, totalRows: 0 });
        syncQueueCounters(0);
        return;
    }

    const pageData = paginateRows(
        state.pendingApplications,
        state.pagination.pendingCardsPage,
        state.pagination.pendingCardsPageSize
    );
    state.pagination.pendingCardsPage = pageData.page;

    root.innerHTML = pageData.rows.map((application) => `
        <article class="admin-pending-card">
            <div>
                <div class="admin-pending-head">
                    <div>
                        <h3>${escapeHtml(application.user?.full_name || 'Unnamed applicant')}</h3>
                        <p class="admin-hint">${escapeHtml(application.user?.email || '')}</p>
                    </div>
                    <span class="admin-status">${escapeHtml(application.status || 'Pending')}</span>
                </div>
                <div class="admin-pending-meta">
                    <span class="admin-chip">Application #${application.id}</span>
                    <span class="admin-chip">User #${escapeHtml(application.user?.id || 'Unknown')}</span>
                    <span class="admin-chip">${escapeHtml(application.applied_role || 'Unknown role')}</span>
                    <span class="admin-chip">${escapeHtml(application.applied_department || 'No department chosen')}</span>
                </div>
            </div>
            <div class="admin-pending-side">
                ${roleRequiresDepartment(application.applied_role) || application.department_required ? `
                <label class="admin-label" for="cardDepartment-${application.id}">Department assignment</label>
                <select id="cardDepartment-${application.id}" class="admin-select">
                    ${departmentOptionsMarkup(departmentIdForApplication(application))}
                </select>
                ` : ''}
                <label class="admin-label" for="reviewNote-${application.id}" style="margin-top: 10px;">Review note</label>
                <textarea id="reviewNote-${application.id}" class="admin-textarea" placeholder="Write a note for this applicant">${escapeHtml(application.review_notes || '')}</textarea>
            </div>
            <div class="admin-pending-actions">
                <button class="admin-btn admin-btn-accent" type="button" onclick="approveApplication(${application.id})">Approve</button>
                <button class="admin-btn admin-btn-danger" type="button" onclick="rejectApplication(${application.id})">Reject</button>
                <button class="admin-btn admin-btn-soft" type="button" onclick="prefillSetup(${application.id})">Use in setup</button>
            </div>
        </article>
    `).join('');
    renderPendingCardsPagination(pageData);
    syncQueueCounters(pageData.rows.length);
}

async function loadPendingApplications() {
    const params = new URLSearchParams(queueFilters()).toString();
    const result = await call(`/admin/applications${params ? `?${params}` : ''}`, 'GET');
    write(result);
    if (result.status < 300) {
        state.pendingApplications = Array.isArray(result.data?.applications) ? result.data.applications : [];
        state.queueStats = {
            loaded: Number(result.data?.queue_stats?.loaded ?? state.pendingApplications.length),
            pending: Number(result.data?.queue_stats?.pending ?? 0),
            waiting_for_review: Number(result.data?.queue_stats?.waiting_for_review ?? 0),
        };
        state.overviewTotals = result.data?.overview_totals || {};
        state.pagination.pendingCardsPage = 1;
        updateOverviewTotals();
        renderPendingCards();
    } else {
        state.pendingApplications = [];
        state.queueStats = { loaded: 0, pending: 0, waiting_for_review: 0 };
        renderPendingCards();
    }
}

function applicationNote(applicationId) {
    const field = document.getElementById(`reviewNote-${applicationId}`);
    return field ? field.value.trim() : '';
}

function selectedDepartmentFromCard(applicationId) {
    const field = document.getElementById(`cardDepartment-${applicationId}`);
    return field ? normalizeId(field.value) : null;
}

function prefillSetupFromApplication(application) {
    if (!application) return;

    const role = application.applied_role;
    const userId = application.user?.id || '';
    const departmentId = departmentIdForApplication(application) || '';

    if (role === 'Nurse') {
        document.getElementById('nurseUserId').value = String(userId || '');
        document.getElementById('nurseDepartmentId').value = departmentId ? String(departmentId) : '';
    }

    if (role === 'Doctor') {
        document.getElementById('doctorUserId').value = String(userId || '');
        document.getElementById('doctorDepartmentId').value = departmentId ? String(departmentId) : '';
    }

    if (role === 'ITWorker') {
        document.getElementById('itUserId').value = String(userId || '');
        document.getElementById('itDepartmentId').value = departmentId ? String(departmentId) : '';
    }
}

async function approveApplication(applicationId) {
    const application = state.pendingApplications.find((item) => Number(item.id) === Number(applicationId));
    if (!application) {
        window.alert('Application not found in current pending list. Refresh and try again.');
        return;
    }

    const requiresDepartment = roleRequiresDepartment(application.applied_role) || !!application.department_required;
    const selectedDepartmentId = selectedDepartmentFromCard(applicationId) ?? departmentIdForApplication(application);
    if (requiresDepartment && !selectedDepartmentId) {
        window.alert('Department is required before approving Doctor, Nurse, and ITWorker applicants.');
        return;
    }

    const review_notes = applicationNote(applicationId);
    const body = review_notes ? { review_notes } : {};
    if (selectedDepartmentId) {
        body.departmentId = selectedDepartmentId;
    }
    const result = await call(`/admin/applications/${applicationId}/approve`, 'POST', body);
    write(result);
    if (result.status < 300 && result.data?.application) {
        prefillSetupFromApplication(result.data.application);
        openSetupPanel();
        window.alert(extractMessage(result, 'Application approved and moved to staff setup.'));
    } else {
        window.alert(extractMessage(result, 'Unable to approve application.'));
    }
    await loadPendingApplications();
}

async function rejectApplication(applicationId) {
    const review_notes = applicationNote(applicationId);
    const body = review_notes ? { review_notes } : {};
    const result = await call(`/admin/applications/${applicationId}/reject`, 'POST', body);
    write(result);
    await loadPendingApplications();
}

function prefillSetup(applicationId) {
    const application = state.pendingApplications.find((item) => Number(item.id) === Number(applicationId));
    if (!application) return;
    prefillSetupFromApplication(application);
    openSetupPanel();
}

async function upsertDoctorProfile() {
    const payload = {
        userId: Number(document.getElementById('doctorUserId').value),
        departmentId: Number(document.getElementById('doctorDepartmentId').value),
    };
    write(await call('/admin/doctors/profile', 'POST', payload));
}

async function upsertNurseProfile() {
    const payload = {
        userId: Number(document.getElementById('nurseUserId').value),
        departmentId: Number(document.getElementById('nurseDepartmentId').value),
        wardAssignmentNote: document.getElementById('wardAssignmentNote').value.trim() || null,
    };
    write(await call('/admin/nurses/profile', 'POST', payload));
}

async function assignItDepartment() {
    const payload = {
        userId: Number(document.getElementById('itUserId').value),
        departmentId: Number(document.getElementById('itDepartmentId').value),
    };
    write(await call('/ward/it/department-admins', 'POST', payload));
}

async function freezeUser() {
    write(await call(`/admin/users/${targetId()}/freeze`, 'POST'));
}

async function unfreezeUser() {
    write(await call(`/admin/users/${targetId()}/unfreeze`, 'POST'));
}

async function statusUser() {
    write(await call(`/admin/users/${targetId()}/status`, 'GET'));
}

refreshContext();
if (window.lifeLinkShell) {
    const adminName = localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Admin';
    const adminEmail = localStorage.getItem('CURRENT_USER_EMAIL') || '-';
    const adminId = localStorage.getItem('CURRENT_USER_ID') || '-';
    window.lifeLinkShell.updateIdentityContext({
        name: adminName,
        userId: adminId,
        email: adminEmail,
        role: 'Admin',
        hideDepartment: true,
    });
    window.lifeLinkShell.mountProfileEditor({
        containerId: 'adminProfileMount',
        role: 'Admin',
        userId: adminId,
        hideDepartment: true,
    });
    adminPanelNavigation = window.lifeLinkShell.initPanelNavigation({
        panelIds: adminPanelIds,
        defaultPanel: 'admin-overview-panel',
    });
}
loadPatientId();
loadDepartments();
updateOverviewTotals();
syncQueueCounters(0);
renderAdminUserSearchResults();
loadPendingApplications();
</script>
@endpush
