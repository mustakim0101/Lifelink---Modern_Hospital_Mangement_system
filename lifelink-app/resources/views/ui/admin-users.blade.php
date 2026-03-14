@extends('ui.layouts.app')

@section('title', 'Admin Control Center')
@section('workspace_label', 'Admin operations workspace')
@section('hero_badge', 'Admin Mode')
@section('hero_title', 'Approve staff applications and finish staff setup from one place.')
@section('hero_description', 'This page now handles the real admin flow: review pending applicants, leave review notes, approve or reject, and then finish doctor, nurse, or IT worker setup with the correct approved user id.')
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
    }

    .admin-grid,
    .admin-row,
    .admin-actions,
    .admin-controls,
    .admin-card-grid,
    .admin-summary {
        display: grid;
        gap: 12px;
    }

    .admin-grid { gap: 14px; }
    .admin-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .admin-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .admin-controls { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .admin-actions { grid-template-columns: repeat(3, max-content); justify-content: start; }
    .admin-summary { grid-template-columns: repeat(3, minmax(0, 1fr)); }

    .admin-card,
    .admin-pending-card {
        border: 1px solid var(--admin-line);
        border-radius: 16px;
        background: var(--admin-card);
        box-shadow: var(--admin-shadow);
        padding: 14px;
    }

    .admin-card h3,
    .admin-pending-card h3 { margin: 0; }

    .admin-hint { margin: 6px 0 0; color: var(--admin-muted); font-size: 0.92rem; line-height: 1.7; }

    .admin-label {
        display: block;
        margin-bottom: 6px;
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

    @media (max-width: 980px) {
        .admin-row,
        .admin-card-grid,
        .admin-controls,
        .admin-actions,
        .admin-summary {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="/ui/admin-users">
        <strong>Admin Control</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/application-reviews">
        <strong>Application Reviews</strong>
        <span>Full review workspace</span>
    </a>
    <a href="/ui/dev-tools">
        <strong>Advanced Tools</strong>
        <span>Controlled diagnostics</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Admin order of work</strong>
        <p>1. Check pending applications. 2. Leave a note and approve or reject. 3. Use the approved user id, not the application id, to finish doctor, nurse, or IT worker setup here before that staff member starts using their own dashboard.</p>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Role boundary</strong>
        <p>Nurse and IT dashboards should be operational pages only. Staff department assignment belongs here in admin control because it is part of approval and provisioning.</p>
    </div>
@endsection

@section('content')
    <div class="admin-grid">
        <div class="admin-summary">
            <div class="admin-stat"><small>Pending applicants</small><strong id="pendingCount">0</strong></div>
            <div class="admin-stat"><small>Departments loaded</small><strong id="departmentCount">0</strong></div>
            <div class="admin-stat"><small>Admin token</small><strong id="tokenState">Missing</strong></div>
        </div>

        <div class="admin-row">
            <div class="admin-card">
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

            <div class="admin-card">
                <h3>Quick pending refresh</h3>
                <p class="admin-hint">This pulls pending applicants directly into cards here. Use the full review page if you want a larger dedicated review workspace.</p>
                <div class="admin-actions">
                    <button class="admin-btn admin-btn-main" type="button" onclick="loadPendingApplications()">Load pending applicants</button>
                    <a class="admin-btn admin-btn-soft" href="/ui/application-reviews">Open Application Reviews</a>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <h3>Pending applicant cards</h3>
            <p class="admin-hint">Each pending card includes a notes area so admin can leave a review note before approving or rejecting. Cards show both the application id and the linked user id so setup uses the right identifier.</p>
            <div id="pendingCards" class="admin-card-grid" style="margin-top: 12px;"></div>
        </div>

        <div class="admin-row">
            <div class="admin-card">
                <h3>Doctor department setup</h3>
                <p class="admin-hint">Doctor ids are not new ids. The doctor profile uses the same user id as the approved account. Approving a doctor now auto-fills this form with the correct user id and selected department.</p>
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
                <p class="admin-hint">Use this after approving a nurse application. This creates or updates the nurse profile and binds the nurse to a department.</p>
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
                <p class="admin-hint">Use this after approving an IT worker application. This gives the IT worker department scope for ward and bed operations.</p>
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

        <div class="admin-card">
            <h3>Stored session context</h3>
            <pre id="ctx" class="admin-context"></pre>
        </div>

        <div class="admin-card">
            <h3>API response</h3>
            <pre id="out" class="admin-context"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const out = document.getElementById('out');
const ctx = document.getElementById('ctx');
const API = '/api';
const state = { pendingApplications: [], departments: [] };

function write(data) {
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
    document.getElementById('tokenState').textContent = tokenPresent ? 'Ready' : 'Missing';
    ctx.textContent = JSON.stringify(data, null, 2);
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

async function loadDepartments() {
    const response = await fetch('/api/public/departments', { headers: { Accept: 'application/json' } });
    const text = await response.text();
    let data = {};
    try { data = JSON.parse(text); } catch {}
    state.departments = Array.isArray(data?.departments) ? data.departments : [];
    document.getElementById('departmentCount').textContent = String(state.departments.length);
    const options = `<option value="">Select department</option>${state.departments.map((department) => `
        <option value="${department.id}">${escapeHtml(department.dept_name)}</option>
    `).join('')}`;
        document.getElementById('doctorDepartmentId').innerHTML = options;
        document.getElementById('nurseDepartmentId').innerHTML = options;
        document.getElementById('itDepartmentId').innerHTML = options;
}

function renderPendingCards() {
    const root = document.getElementById('pendingCards');
    document.getElementById('pendingCount').textContent = String(state.pendingApplications.length);

    if (!state.pendingApplications.length) {
        root.innerHTML = '<div class="admin-card"><p class="admin-hint">No pending applicants right now.</p></div>';
        return;
    }

    root.innerHTML = state.pendingApplications.map((application) => `
        <article class="admin-pending-card">
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
            <label class="admin-label" for="reviewNote-${application.id}" style="margin-top: 12px;">Review note</label>
            <textarea id="reviewNote-${application.id}" class="admin-textarea" placeholder="Write a note for this applicant">${escapeHtml(application.review_notes || '')}</textarea>
            <div class="admin-actions">
                <button class="admin-btn admin-btn-accent" type="button" onclick="approveApplication(${application.id})">Approve</button>
                <button class="admin-btn admin-btn-danger" type="button" onclick="rejectApplication(${application.id})">Reject</button>
                <button class="admin-btn admin-btn-soft" type="button" onclick="prefillSetup(${application.id})">Use in setup</button>
            </div>
        </article>
    `).join('');
}

async function loadPendingApplications() {
    const result = await call('/admin/applications?status=Pending', 'GET');
    write(result);
    if (result.status < 300) {
        state.pendingApplications = Array.isArray(result.data?.applications) ? result.data.applications : [];
        renderPendingCards();
    }
}

function applicationNote(applicationId) {
    const field = document.getElementById(`reviewNote-${applicationId}`);
    return field ? field.value.trim() : '';
}

function prefillSetupFromApplication(application) {
    if (!application) return;

    const role = application.applied_role;
    const userId = application.user?.id || '';
    const departmentId = application.applied_department_id || '';

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
    const review_notes = applicationNote(applicationId);
    const body = review_notes ? { review_notes } : {};
    const result = await call(`/admin/applications/${applicationId}/approve`, 'POST', body);
    write(result);
    if (result.status < 300 && result.data?.application) {
        prefillSetupFromApplication(result.data.application);
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
loadPatientId();
loadDepartments();
loadPendingApplications();
</script>
@endpush
