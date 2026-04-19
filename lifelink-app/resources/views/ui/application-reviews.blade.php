@extends('ui.layouts.app')

@section('title', 'Application Reviews')
@section('role_theme', 'admin')
@section('workspace_label', 'Admin approval workspace')
@section('hero_badge', 'Hiring Review')
@section('hero_title', 'Review pending applications without hunting through raw JSON.')
@section('hero_description', 'Run reviews from cards first, then complete approval actions in one place.')
@section('meta_title', 'Application Queue')
@section('meta_copy', 'Approve or reject applicant role requests')

@push('styles')
<style>
    :root {
        --review-ink: #16283a;
        --review-muted: #617585;
        --review-line: rgba(22, 40, 58, 0.12);
        --review-card: rgba(255, 255, 255, 0.94);
        --review-primary: #1d4ed8;
        --review-primary-strong: #1e40af;
        --review-accent: #0f766e;
        --review-danger: #b91c1c;
        --review-warning: #b45309;
        --review-shadow: 0 18px 38px rgba(15, 34, 48, 0.12);
    }

    .review-grid,
    .review-toolbar,
    .review-summary,
    .review-card__meta,
    .review-actions,
    .review-form-actions,
    .review-department-editor {
        display: grid;
        gap: 12px;
    }

    .review-grid {
        gap: 14px;
    }
    .review-panel-switch { display: none; }

    .review-toolbar {
        grid-template-columns: minmax(0, 220px) auto auto;
        align-items: end;
    }

    .review-summary {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .review-panel,
    .review-card {
        border: 1px solid var(--review-line);
        border-radius: 18px;
        background: var(--review-card);
        box-shadow: var(--review-shadow);
        padding: 16px;
    }

    .review-panel h3,
    .review-card h3 {
        margin: 0;
    }

    .review-note,
    .review-empty,
    .review-help {
        color: var(--review-muted);
        line-height: 1.7;
        font-size: 0.94rem;
    }

    .review-flash {
        border-radius: 14px;
        border: 1px solid transparent;
        padding: 12px 14px;
        font-size: 0.92rem;
        font-weight: 600;
        display: none;
    }

    .review-flash.show {
        display: block;
    }

    .review-flash.info {
        background: #eff6ff;
        border-color: rgba(29, 78, 216, 0.25);
        color: #1d4ed8;
    }

    .review-flash.success {
        background: #ecfdf5;
        border-color: rgba(15, 118, 110, 0.25);
        color: #0f766e;
    }

    .review-flash.error {
        background: #fef2f2;
        border-color: rgba(185, 28, 28, 0.25);
        color: #b91c1c;
    }

    .review-label {
        display: block;
        margin-bottom: 6px;
        color: var(--review-muted);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .review-input,
    .review-select,
    .review-textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid rgba(22, 40, 58, 0.16);
        background: #fbfdff;
        color: var(--review-ink);
        font: inherit;
        padding: 11px 12px;
        outline: none;
    }

    .review-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .review-input:focus,
    .review-select:focus,
    .review-textarea:focus {
        border-color: var(--review-primary);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .review-button,
    .review-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 12px;
        padding: 10px 14px;
        font: inherit;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
    }

    .review-button.primary { background: var(--review-primary); color: #fff; }
    .review-button.primary:hover { background: var(--review-primary-strong); }
    .review-button.soft { background: rgba(22, 40, 58, 0.08); color: var(--review-ink); }
    .review-button.accent { background: var(--review-accent); color: #fff; }
    .review-button.reject { background: var(--review-danger); color: #fff; }
    .review-button.warn { background: #fff7ed; color: var(--review-warning); border: 1px solid rgba(180, 83, 9, 0.18); }

    .review-link {
        text-decoration: none;
        background: rgba(22, 40, 58, 0.06);
        color: var(--review-ink);
    }

    .review-stat {
        border-radius: 16px;
        border: 1px solid var(--review-line);
        padding: 14px;
        background: rgba(255, 255, 255, 0.78);
    }

    .review-stat small {
        display: block;
        margin-bottom: 6px;
        color: var(--review-muted);
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        font-weight: 800;
    }

    .review-stat strong {
        display: block;
        font-size: 1.5rem;
    }

    .review-card-list {
        display: grid;
        gap: 14px;
    }

    .review-card__top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .review-card__identity strong {
        display: block;
        font-size: 1.1rem;
    }

    .review-card__identity span {
        color: var(--review-muted);
        font-size: 0.95rem;
    }

    .review-card__status {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .review-card__status.pending { background: rgba(180, 83, 9, 0.12); color: var(--review-warning); }
    .review-card__status.approved { background: rgba(15, 118, 110, 0.12); color: var(--review-accent); }
    .review-card__status.rejected { background: rgba(185, 28, 28, 0.12); color: var(--review-danger); }

    .review-card__meta {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-top: 14px;
    }

    .review-chip {
        border-radius: 14px;
        border: 1px solid var(--review-line);
        background: rgba(255, 255, 255, 0.82);
        padding: 12px;
    }

    .review-chip small {
        display: block;
        margin-bottom: 5px;
        color: var(--review-muted);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
    }

    .review-chip strong {
        display: block;
        word-break: break-word;
    }

    .review-actions {
        grid-template-columns: repeat(4, auto);
        justify-content: start;
        margin-top: 16px;
    }

    .review-form-actions {
        grid-template-columns: repeat(3, auto);
        justify-content: start;
        margin-top: 12px;
    }

    .review-department-editor {
        margin-top: 12px;
        border-radius: 14px;
        border: 1px solid var(--review-line);
        padding: 12px;
        background: rgba(255, 255, 255, 0.86);
    }

    .review-department-editor .review-label {
        margin-bottom: 0;
    }

    .review-console {
        margin: 0;
        min-height: 140px;
        max-height: 320px;
        overflow: auto;
        border-radius: 14px;
        border: 1px solid var(--review-line);
        background: #101c33;
        color: #d7e3ff;
        padding: 12px;
        font-size: 12px;
    }

    @media (max-width: 980px) {
        .review-toolbar,
        .review-summary,
        .review-card__meta,
        .review-actions,
        .review-form-actions {
            grid-template-columns: 1fr;
        }

        .review-card__top {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('sidebar_nav')
    <a href="#review-queue-panel" class="is-active" data-panel="review-queue-panel">
        <strong>Queue</strong>
        <span>Filter + summary</span>
    </a>
    <a href="#review-cards-panel" data-panel="review-cards-panel">
        <strong>Cards</strong>
        <span>Review cards</span>
    </a>
    <a href="#review-action-panel" data-panel="review-action-panel">
        <strong>Action</strong>
        <span>Approve or reject</span>
    </a>
    <a href="/ui/admin-users">
        <strong>Admin Control</strong>
        <span>Back to admin landing</span>
    </a>
    <a href="/ui/application-reviews">
        <strong>Application Reviews</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/dev-tools">
        <strong>Advanced Tools</strong>
        <span>Debug endpoints</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Approval flow</strong>
        <p>Applicants stay in the applicant workspace until approval. Approving here is what activates the real doctor, nurse, or IT worker role for future logins.</p>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Department rule</strong>
        <p>Doctor applicants may propose a department during registration. Nurse and IT worker department assignment should be confirmed by admin later, so their applications may appear without a department here.</p>
    </div>
@endsection

@section('content')
    <div class="review-grid">
        <div id="reviewFlash" class="review-flash" role="status" aria-live="polite"></div>

        <div id="review-queue-panel" class="review-panel ll-section review-panel-switch" data-display="block">
            <h3>Review queue</h3>
            <p class="review-note">Filter by status, then review from cards.</p>

            <div class="review-toolbar" style="margin-top: 14px;">
                <div>
                    <label class="review-label" for="statusFilter">Status</label>
                    <select id="statusFilter" class="review-select">
                        <option value="Pending" selected>Pending</option>
                        <option value="">All</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <button class="review-button primary" type="button" onclick="loadApplications()">Load applications</button>
                <button class="review-button soft" type="button" onclick="loadPendingApplications()">Reload pending</button>
            </div>
            <div class="review-summary">
                <div class="review-stat">
                    <small>Loaded</small>
                    <strong id="loadedCount">0</strong>
                    <span class="review-help">Cards currently shown</span>
                </div>
                <div class="review-stat">
                    <small>Pending</small>
                    <strong id="pendingCount">0</strong>
                    <span class="review-help">Waiting for review</span>
                </div>
                <div class="review-stat">
                    <small>Selected</small>
                    <strong id="selectedApplicationLabel">None</strong>
                    <span class="review-help">Chosen for action</span>
                </div>
            </div>
        </div>

        <div id="review-cards-panel" class="review-panel ll-section review-panel-switch" data-display="block">
            <h3>Applications</h3>
            <p class="review-note">The queue below is the main admin view. Click any card to load it into the review form. Pending items are shown first when you keep the default filter.</p>
            <div id="applicationCards" class="review-card-list ui-list-window" style="margin-top: 14px;"></div>
            <div id="applicationCardsPagination" class="ui-list-pagination"></div>
        </div>

        <div id="review-action-panel" class="review-panel ll-section review-panel-switch" data-display="block">
            <h3>Review action</h3>
            <p class="review-note">Use this form for direct review updates, or prefill it by selecting a card.</p>

            <label class="review-label" for="applicationId">Application ID</label>
            <input id="applicationId" class="review-input" placeholder="Application ID">

            <div id="reviewDepartmentField" class="review-department-editor">
                <label class="review-label" for="reviewDepartment">Department assignment</label>
                <select id="reviewDepartment" class="review-select">
                    <option value="">Select department</option>
                </select>
                <p id="reviewDepartmentHelp" class="review-help">Select an application to configure department assignment.</p>
            </div>

            <label class="review-label" for="reviewNotes" style="margin-top: 12px;">Review notes</label>
            <textarea id="reviewNotes" class="review-textarea" placeholder="Optional notes for approval or rejection"></textarea>

            <div class="review-form-actions">
                <button class="review-button warn" type="button" onclick="saveSelectedDepartment()">Save department</button>
                <button class="review-button accent" type="button" onclick="approveApplication()">Approve selected</button>
                <button class="review-button reject" type="button" onclick="rejectApplication()">Reject selected</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const out = document.getElementById('out');
const ctx = document.getElementById('ctx');
const cards = document.getElementById('applicationCards');
const loadedCount = document.getElementById('loadedCount');
const pendingCount = document.getElementById('pendingCount');
const selectedApplicationLabel = document.getElementById('selectedApplicationLabel');
const reviewFlash = document.getElementById('reviewFlash');
const reviewDepartmentField = document.getElementById('reviewDepartmentField');
const reviewDepartmentInput = document.getElementById('reviewDepartment');
const reviewDepartmentHelp = document.getElementById('reviewDepartmentHelp');
const API = '/api';
const reviewPanelIds = ['review-queue-panel', 'review-cards-panel', 'review-action-panel'];
const DEPARTMENT_REQUIRED_ROLES = ['Doctor', 'Nurse', 'ITWorker'];
let state = {
    applications: [],
    departments: [],
    departmentDrafts: {},
    selectedId: null,
    pagination: {
        cardsPageSize: 6,
        cardsPage: 1,
    },
};

function write(data) {
    if (!window.lifeLinkShell?.isDebugEnabled() || !out) return;
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function showFlash(kind, text) {
    if (!reviewFlash) return;
    reviewFlash.className = `review-flash show ${kind}`;
    reviewFlash.textContent = text;
}

function clearFlash() {
    if (!reviewFlash) return;
    reviewFlash.className = 'review-flash';
    reviewFlash.textContent = '';
}

function extractMessage(result, fallback) {
    if (typeof result?.data === 'string' && result.data.trim()) return result.data.trim();
    if (result?.data?.message) return result.data.message;
    if (result?.data?.errors) {
        const firstKey = Object.keys(result.data.errors)[0];
        if (firstKey && Array.isArray(result.data.errors[firstKey]) && result.data.errors[firstKey][0]) {
            return result.data.errors[firstKey][0];
        }
    }
    return fallback;
}

function refreshContext() {
    const data = {
        ADMIN_USER_ID: localStorage.getItem('ADMIN_USER_ID'),
        ADMIN_EMAIL: localStorage.getItem('ADMIN_EMAIL'),
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        CURRENT_USER_EMAIL: localStorage.getItem('CURRENT_USER_EMAIL'),
        CURRENT_USER_ROLES: JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]'),
    };
    if (ctx) {
        ctx.textContent = JSON.stringify(data, null, 2);
    }
}

function adminToken() {
    return localStorage.getItem('ADMIN_TOKEN');
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function normalizeId(value) {
    if (value === null || value === undefined || value === '') return null;
    const parsed = Number(value);
    return Number.isInteger(parsed) && parsed > 0 ? parsed : null;
}

function roleRequiresDepartment(roleName) {
    return DEPARTMENT_REQUIRED_ROLES.includes(String(roleName || '').trim());
}

function statusClass(status) {
    if (status === 'Approved') return 'approved';
    if (status === 'Rejected') return 'rejected';
    return 'pending';
}

function formatValue(value, fallback = 'Not set') {
    return value === null || value === undefined || value === '' ? fallback : value;
}

function formatDate(value) {
    if (!value) return 'Not set';
    const parsed = new Date(value);
    return Number.isNaN(parsed.getTime()) ? value : parsed.toLocaleString();
}

function selectedApplication() {
    return state.applications.find(item => Number(item.id) === Number(state.selectedId)) || null;
}

function departmentIdForApplication(application) {
    const appId = normalizeId(application?.id);
    const draftDepartmentId = appId ? normalizeId(state.departmentDrafts[String(appId)] ?? null) : null;

    if (draftDepartmentId !== null) {
        return draftDepartmentId;
    }

    return normalizeId(application?.assigned_department_id)
        ?? normalizeId(application?.applied_department_id)
        ?? null;
}

function setDepartmentDraft(applicationId, departmentId) {
    const normalizedApplicationId = normalizeId(applicationId);
    if (!normalizedApplicationId) return;

    const normalizedDepartmentId = normalizeId(departmentId);
    if (normalizedDepartmentId === null) {
        delete state.departmentDrafts[String(normalizedApplicationId)];
        return;
    }

    state.departmentDrafts[String(normalizedApplicationId)] = normalizedDepartmentId;
}

function onCardDepartmentChange(applicationId, rawValue) {
    const normalizedApplicationId = normalizeId(applicationId);
    if (!normalizedApplicationId) return;

    setDepartmentDraft(normalizedApplicationId, rawValue);

    if (Number(state.selectedId) === Number(normalizedApplicationId) && reviewDepartmentInput) {
        const draft = normalizeId(state.departmentDrafts[String(normalizedApplicationId)] ?? null);
        reviewDepartmentInput.value = draft !== null ? String(draft) : '';
    }
}

function onActionDepartmentChange(rawValue) {
    const selected = selectedApplication();
    if (!selected) return;

    const selectedId = normalizeId(selected.id);
    if (!selectedId) return;

    setDepartmentDraft(selectedId, rawValue);

    const cardInput = document.getElementById(`cardDepartment-${selectedId}`);
    if (cardInput) {
        const draft = normalizeId(state.departmentDrafts[String(selectedId)] ?? null);
        cardInput.value = draft !== null ? String(draft) : '';
    }
}

function departmentOptionsMarkup(selectedDepartmentId = null) {
    const selectedId = normalizeId(selectedDepartmentId);
    const baseOption = '<option value="">Select department</option>';
    if (!Array.isArray(state.departments) || state.departments.length === 0) {
        return `${baseOption}<option value="" disabled>No active departments found</option>`;
    }

    return baseOption + state.departments.map(department => {
        const departmentId = Number(department.id);
        const selected = selectedId === departmentId ? 'selected' : '';
        return `<option value="${departmentId}" ${selected}>${escapeHtml(department.dept_name)}</option>`;
    }).join('');
}

function syncActionDepartmentField(application) {
    if (!reviewDepartmentField || !reviewDepartmentInput || !reviewDepartmentHelp) return;

    if (!application) {
        reviewDepartmentInput.innerHTML = departmentOptionsMarkup(null);
        reviewDepartmentInput.disabled = true;
        reviewDepartmentHelp.textContent = 'Select an application to configure department assignment.';
        return;
    }

    const requiresDepartment = roleRequiresDepartment(application.applied_role) || !!application.department_required;
    const resolvedDepartmentId = departmentIdForApplication(application);

    reviewDepartmentInput.innerHTML = departmentOptionsMarkup(resolvedDepartmentId);
    reviewDepartmentInput.disabled = !requiresDepartment;

    if (!requiresDepartment) {
        reviewDepartmentHelp.textContent = 'Department assignment is optional for this role.';
        return;
    }

    if (application.status === 'Approved') {
        reviewDepartmentHelp.textContent = 'This record is approved. Save department to reassign and update the live staff profile.';
        return;
    }

    reviewDepartmentHelp.textContent = 'Department is required before approving Doctor, Nurse, and ITWorker applications.';
}

async function call(path, method, body = null) {
    const token = adminToken();
    if (!token) return { status: 401, data: { message: 'ADMIN_TOKEN missing. Create or login admin from /ui/login first.' } };

    const headers = { Accept: 'application/json', 'Content-Type': 'application/json', Authorization: `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    try {
        return { status: res.status, data: JSON.parse(text) };
    } catch {
        return { status: res.status, data: text };
    }
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

function renderApplicationCardsPagination(pageData) {
    const root = document.getElementById('applicationCardsPagination');
    if (!root) return;
    if (pageData.totalRows <= state.pagination.cardsPageSize) {
        root.innerHTML = '';
        return;
    }
    root.innerHTML = `
        <div class="ui-list-pagination__meta">Page ${pageData.page} of ${pageData.totalPages} (${pageData.totalRows} total)</div>
        <div class="ui-list-pagination__controls">
            <button class="review-button soft" type="button" ${pageData.page <= 1 ? 'disabled' : ''} onclick="prevApplicationCardsPage()">Previous</button>
            <button class="review-button soft" type="button" ${pageData.page >= pageData.totalPages ? 'disabled' : ''} onclick="nextApplicationCardsPage()">Next</button>
        </div>
    `;
}

function prevApplicationCardsPage() {
    state.pagination.cardsPage = Math.max(1, state.pagination.cardsPage - 1);
    renderCards();
}

function nextApplicationCardsPage() {
    state.pagination.cardsPage += 1;
    renderCards();
}

function syncSummary() {
    loadedCount.textContent = String(state.applications.length);
    pendingCount.textContent = String(state.applications.filter(item => item.status === 'Pending').length);

    if (!state.selectedId) {
        selectedApplicationLabel.textContent = 'None';
        return;
    }

    const selected = state.applications.find(item => Number(item.id) === Number(state.selectedId));
    selectedApplicationLabel.textContent = selected ? `#${selected.id}` : 'None';
}

function setSelectedApplication(application, notesOverride = null) {
    if (!application) {
        state.selectedId = null;
        document.getElementById('applicationId').value = '';
        document.getElementById('reviewNotes').value = '';
        syncActionDepartmentField(null);
        syncSummary();
        return;
    }

    state.selectedId = Number(application.id);
    document.getElementById('applicationId').value = String(application.id);
    document.getElementById('reviewNotes').value = notesOverride !== null
        ? notesOverride
        : (application.review_notes || '');
    syncActionDepartmentField(application);
    syncSummary();
}

function selectApplication(application, notesOverride = null) {
    setSelectedApplication(application, notesOverride);
    renderCards();
}

function renderCards() {
    if (!state.applications.length) {
        cards.innerHTML = '<div class="review-card"><p class="review-empty">No applications found for this filter.</p></div>';
        renderApplicationCardsPagination({ page: 1, totalPages: 1, totalRows: 0 });
        syncSummary();
        return;
    }

    const pageData = paginateRows(
        state.applications,
        state.pagination.cardsPage,
        state.pagination.cardsPageSize
    );
    state.pagination.cardsPage = pageData.page;

    cards.innerHTML = pageData.rows.map(application => {
        const roleNeedsDepartment = roleRequiresDepartment(application.applied_role) || !!application.department_required;
        const canEditDepartment = roleNeedsDepartment && (application.status === 'Pending' || application.status === 'Approved');
        const cardSelectId = `cardDepartment-${application.id}`;

        return `
        <article class="review-card" data-id="${application.id}" style="${Number(state.selectedId) === Number(application.id) ? 'outline: 2px solid rgba(29, 78, 216, 0.22);' : ''}">
            <div class="review-card__top">
                <div class="review-card__identity">
                    <strong>${formatValue(application.user?.full_name, 'Unnamed applicant')}</strong>
                    <span>${formatValue(application.user?.email)}</span>
                </div>
                <span class="review-card__status ${statusClass(application.status)}">${application.status || 'Pending'}</span>
            </div>

            <div class="review-card__meta">
                <div class="review-chip">
                    <small>Application</small>
                    <strong>#${application.id}</strong>
                </div>
                <div class="review-chip">
                    <small>Applied Role</small>
                    <strong>${formatValue(application.applied_role)}</strong>
                </div>
                <div class="review-chip">
                    <small>Assigned Department</small>
                    <strong>${formatValue(application.assigned_department, 'Not assigned yet')}</strong>
                </div>
                <div class="review-chip">
                    <small>Application Department</small>
                    <strong>${formatValue(application.applied_department, 'Admin will assign later')}</strong>
                </div>
                <div class="review-chip">
                    <small>Applied At</small>
                    <strong>${formatDate(application.applied_at)}</strong>
                </div>
            </div>

            ${roleNeedsDepartment ? `
                <div class="review-department-editor">
                    <label class="review-label" for="${cardSelectId}">Department assignment</label>
                    <select id="${cardSelectId}" class="review-select" onchange="onCardDepartmentChange(${application.id}, this.value)" ${canEditDepartment ? '' : 'disabled'}>
                        ${departmentOptionsMarkup(departmentIdForApplication(application))}
                    </select>
                    <p class="review-help">${application.status === 'Approved' ? 'Save to reassign this approved staff member.' : 'Required before approval for this role.'}</p>
                </div>
            ` : ''}

            <div class="review-chip" style="margin-top: 14px;">
                <small>Review notes</small>
                <strong>${formatValue(application.review_notes, 'No review note yet')}</strong>
            </div>

            <div class="review-actions">
                <button class="review-button soft" type="button" onclick="selectCardApplication(${application.id})">Select</button>
                ${roleNeedsDepartment ? `<button class="review-button warn" type="button" onclick="saveDepartmentFromCard(${application.id})" ${canEditDepartment ? '' : 'disabled'}>Save Department</button>` : ''}
                <button class="review-button accent" type="button" onclick="approveFromCard(${application.id})" ${application.status !== 'Pending' ? 'disabled' : ''}>Approve</button>
                <button class="review-button reject" type="button" onclick="rejectFromCard(${application.id})" ${application.status !== 'Pending' ? 'disabled' : ''}>Reject</button>
            </div>
        </article>
    `;
    }).join('');

    renderApplicationCardsPagination(pageData);
    syncSummary();
}

function selectCardApplication(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) return;

    const roleNeedsDepartment = roleRequiresDepartment(application.applied_role) || !!application.department_required;
    if (roleNeedsDepartment) {
        const cardDepartmentInput = document.getElementById(`cardDepartment-${applicationId}`);
        if (cardDepartmentInput) {
            onCardDepartmentChange(applicationId, cardDepartmentInput.value);
        }
    }

    selectApplication(application);
}

async function loadApplications(options = {}) {
    if (options?.clearFlash) {
        clearFlash();
    }

    const status = document.getElementById('statusFilter').value.trim();
    const query = status ? `?status=${encodeURIComponent(status)}` : '';
    const result = await call(`/admin/applications${query}`, 'GET');
    write(result);

    if (result.status >= 200 && result.status < 300) {
        state.applications = result.data?.applications || [];
        state.departments = Array.isArray(result.data?.departments) ? result.data.departments : state.departments;
        const loadedIds = new Set(state.applications.map(item => Number(item.id)));
        Object.keys(state.departmentDrafts).forEach((applicationId) => {
            if (!loadedIds.has(Number(applicationId))) {
                delete state.departmentDrafts[applicationId];
            }
        });

        if (!state.departments.length) {
            const departmentResult = await call('/ward/departments', 'GET');
            if (departmentResult.status >= 200 && departmentResult.status < 300) {
                state.departments = departmentResult.data?.departments || [];
            }
        }

        state.pagination.cardsPage = 1;
        const matchingSelection = state.applications.find(item => Number(item.id) === Number(state.selectedId));
        if (!matchingSelection) {
            setSelectedApplication(null);
        } else {
            setSelectedApplication(matchingSelection);
        }
        renderCards();
        return;
    }

    showFlash('error', extractMessage(result, 'Unable to load applications.'));
    state.applications = [];
    setSelectedApplication(null);
    state.pagination.cardsPage = 1;
    renderCards();
}

function loadPendingApplications() {
    document.getElementById('statusFilter').value = 'Pending';
    loadApplications({ clearFlash: true });
}

async function approveApplication() {
    const id = document.getElementById('applicationId').value.trim();
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    if (!id) {
        showFlash('error', 'Application ID is required.');
        return;
    }

    const application = state.applications.find(item => Number(item.id) === Number(id));
    const requiresDepartment = roleRequiresDepartment(application?.applied_role) || !!application?.department_required;
    const selectedDepartmentId = normalizeId(reviewDepartmentInput?.value ?? null)
        ?? departmentIdForApplication(application);

    if (requiresDepartment && !selectedDepartmentId) {
        showFlash('error', 'Department is required before approving Doctor, Nurse, and ITWorker applications.');
        return;
    }

    const body = reviewNotes ? { review_notes: reviewNotes } : {};
    if (selectedDepartmentId) {
        body.departmentId = selectedDepartmentId;
    }

    const result = await call(`/admin/applications/${id}/approve`, 'POST', body);
    write(result);

    if (result.status >= 200 && result.status < 300) {
        setDepartmentDraft(id, selectedDepartmentId);
        showFlash('success', extractMessage(result, 'Application approved and role assigned.'));
        await loadApplications();
    } else {
        showFlash('error', extractMessage(result, 'Unable to approve application.'));
    }
}

async function rejectApplication() {
    const id = document.getElementById('applicationId').value.trim();
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    if (!id) {
        showFlash('error', 'Application ID is required.');
        return;
    }

    const body = reviewNotes ? { review_notes: reviewNotes } : {};
    const result = await call(`/admin/applications/${id}/reject`, 'POST', body);
    write(result);

    if (result.status >= 200 && result.status < 300) {
        showFlash('success', extractMessage(result, 'Application rejected.'));
        await loadApplications();
    } else {
        showFlash('error', extractMessage(result, 'Unable to reject application.'));
    }
}

async function saveDepartmentAssignment(applicationId, departmentId) {
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    const body = { departmentId: departmentId };
    if (reviewNotes) {
        body.review_notes = reviewNotes;
    }

    const result = await call(`/admin/applications/${applicationId}/department`, 'PATCH', body);
    write(result);

    if (result.status >= 200 && result.status < 300) {
        setDepartmentDraft(applicationId, departmentId);
        showFlash('success', extractMessage(result, 'Department assignment updated.'));
        await loadApplications();
    } else {
        showFlash('error', extractMessage(result, 'Unable to update department assignment.'));
    }
}

async function saveSelectedDepartment() {
    const id = document.getElementById('applicationId').value.trim();
    if (!id) {
        showFlash('error', 'Select an application first.');
        return;
    }

    const application = state.applications.find(item => Number(item.id) === Number(id));
    if (!application) {
        showFlash('error', 'Selected application is not in the loaded queue.');
        return;
    }

    if (!(roleRequiresDepartment(application.applied_role) || !!application.department_required)) {
        showFlash('info', 'Department reassignment is only available for Doctor, Nurse, and ITWorker applications.');
        return;
    }

    const departmentId = normalizeId(reviewDepartmentInput?.value ?? null);
    if (!departmentId) {
        showFlash('error', 'Please choose a valid department.');
        return;
    }

    await saveDepartmentAssignment(Number(id), departmentId);
}

async function saveDepartmentFromCard(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) {
        showFlash('error', 'Application not found in the current view.');
        return;
    }

    const departmentInput = document.getElementById(`cardDepartment-${applicationId}`);
    const departmentId = normalizeId(departmentInput?.value ?? null);
    if (!departmentId) {
        showFlash('error', 'Please choose a valid department before saving.');
        return;
    }

    onCardDepartmentChange(applicationId, String(departmentId));
    selectApplication(application);

    await saveDepartmentAssignment(Number(applicationId), departmentId);
}

function approveFromCard(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) return;

    const roleNeedsDepartment = roleRequiresDepartment(application.applied_role) || !!application.department_required;
    if (roleNeedsDepartment) {
        const cardDepartmentInput = document.getElementById(`cardDepartment-${applicationId}`);
        if (cardDepartmentInput) {
            onCardDepartmentChange(applicationId, cardDepartmentInput.value);
        }
    }

    selectApplication(application);
    approveApplication();
}

function rejectFromCard(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) return;
    selectApplication(application);
    rejectApplication();
}

if (reviewDepartmentInput) {
    reviewDepartmentInput.addEventListener('change', (event) => {
        onActionDepartmentChange(event.target.value);
    });
}

refreshContext();
if (window.lifeLinkShell) {
    window.lifeLinkShell.updateIdentityContext({
        name: localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Admin',
        userId: localStorage.getItem('CURRENT_USER_ID') || '-',
        email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
        role: 'Admin',
        hideDepartment: true,
    });
    window.lifeLinkShell.initPanelNavigation({
        panelIds: reviewPanelIds,
        defaultPanel: 'review-queue-panel',
    });
}
loadPendingApplications();
</script>
@endpush
