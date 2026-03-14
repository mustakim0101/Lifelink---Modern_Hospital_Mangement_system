@extends('ui.layouts.app')

@section('title', 'Application Reviews')
@section('workspace_label', 'Admin approval workspace')
@section('hero_badge', 'Hiring Review')
@section('hero_title', 'Review pending applications without hunting through raw JSON.')
@section('hero_description', 'This page keeps the raw API response visible for debugging, but the main admin flow now uses a card queue so pending applications can be reviewed directly from the page.')
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
    .review-form-actions {
        display: grid;
        gap: 12px;
    }

    .review-grid {
        gap: 14px;
    }

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
        grid-template-columns: auto auto auto;
        justify-content: start;
        margin-top: 16px;
    }

    .review-form-actions {
        grid-template-columns: auto auto;
        justify-content: start;
        margin-top: 12px;
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
    <a href="/ui/admin-users">
        <strong>Admin Control</strong>
        <span>Back to admin landing</span>
    </a>
    <a class="is-active" href="/ui/application-reviews">
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
        <div class="review-panel">
            <h3>Review queue</h3>
            <p class="review-note">Load applications by status, scan them as cards, and review directly from the selected applicant instead of copying IDs out of raw JSON.</p>

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

        <div class="review-panel">
            <h3>Applications</h3>
            <p class="review-note">The queue below is the main admin view. Click any card to load it into the review form. Pending items are shown first when you keep the default filter.</p>
            <div id="applicationCards" class="review-card-list" style="margin-top: 14px;"></div>
        </div>

        <div class="review-panel">
            <h3>Review action</h3>
            <p class="review-note">The manual form is still here for direct review work and debugging, but card actions now prefill it for you.</p>

            <label class="review-label" for="applicationId">Application ID</label>
            <input id="applicationId" class="review-input" placeholder="Application ID">

            <label class="review-label" for="reviewNotes" style="margin-top: 12px;">Review notes</label>
            <textarea id="reviewNotes" class="review-textarea" placeholder="Optional notes for approval or rejection"></textarea>

            <div class="review-form-actions">
                <button class="review-button accent" type="button" onclick="approveApplication()">Approve selected</button>
                <button class="review-button reject" type="button" onclick="rejectApplication()">Reject selected</button>
            </div>
        </div>

        <div class="review-panel">
            <h3>Stored admin context</h3>
            <pre id="ctx" class="review-console"></pre>
        </div>

        <div class="review-panel">
            <h3>API response</h3>
            <p class="review-note">Keeping the raw response visible for now, as requested.</p>
            <pre id="out" class="review-console"></pre>
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
const API = '/api';
let state = {
    applications: [],
    selectedId: null,
};

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function refreshContext() {
    const data = {
        ADMIN_USER_ID: localStorage.getItem('ADMIN_USER_ID'),
        ADMIN_EMAIL: localStorage.getItem('ADMIN_EMAIL'),
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        CURRENT_USER_EMAIL: localStorage.getItem('CURRENT_USER_EMAIL'),
        CURRENT_USER_ROLES: JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]'),
    };
    ctx.textContent = JSON.stringify(data, null, 2);
}

function adminToken() {
    return localStorage.getItem('ADMIN_TOKEN');
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

function selectApplication(application, notesOverride = null) {
    state.selectedId = Number(application.id);
    document.getElementById('applicationId').value = String(application.id);
    document.getElementById('reviewNotes').value = notesOverride !== null
        ? notesOverride
        : (application.review_notes || '');
    syncSummary();
    renderCards();
}

function renderCards() {
    if (!state.applications.length) {
        cards.innerHTML = '<div class="review-card"><p class="review-empty">No applications found for this filter.</p></div>';
        syncSummary();
        return;
    }

    cards.innerHTML = state.applications.map(application => `
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
                    <small>Department</small>
                    <strong>${formatValue(application.applied_department, 'Admin will assign later')}</strong>
                </div>
                <div class="review-chip">
                    <small>Applied At</small>
                    <strong>${formatDate(application.applied_at)}</strong>
                </div>
            </div>

            <div class="review-chip" style="margin-top: 14px;">
                <small>Review notes</small>
                <strong>${formatValue(application.review_notes, 'No review note yet')}</strong>
            </div>

            <div class="review-actions">
                <button class="review-button soft" type="button" onclick="selectCardApplication(${application.id})">Select</button>
                <button class="review-button accent" type="button" onclick="approveFromCard(${application.id})" ${application.status !== 'Pending' ? 'disabled' : ''}>Approve</button>
                <button class="review-button reject" type="button" onclick="rejectFromCard(${application.id})" ${application.status !== 'Pending' ? 'disabled' : ''}>Reject</button>
            </div>
        </article>
    `).join('');

    syncSummary();
}

function selectCardApplication(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) return;
    selectApplication(application);
}

async function loadApplications() {
    const status = document.getElementById('statusFilter').value.trim();
    const query = status ? `?status=${encodeURIComponent(status)}` : '';
    const result = await call(`/admin/applications${query}`, 'GET');
    write(result);

    if (result.status >= 200 && result.status < 300) {
        state.applications = result.data?.applications || [];
        if (!state.applications.find(item => Number(item.id) === Number(state.selectedId))) {
            state.selectedId = null;
            document.getElementById('applicationId').value = '';
            document.getElementById('reviewNotes').value = '';
        }
        renderCards();
        return;
    }

    state.applications = [];
    state.selectedId = null;
    renderCards();
}

function loadPendingApplications() {
    document.getElementById('statusFilter').value = 'Pending';
    loadApplications();
}

async function approveApplication() {
    const id = document.getElementById('applicationId').value.trim();
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    if (!id) {
        write('applicationId is required.');
        return;
    }

    const body = reviewNotes ? { review_notes: reviewNotes } : {};
    const result = await call(`/admin/applications/${id}/approve`, 'POST', body);
    write(result);
    await loadApplications();
}

async function rejectApplication() {
    const id = document.getElementById('applicationId').value.trim();
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    if (!id) {
        write('applicationId is required.');
        return;
    }

    const body = reviewNotes ? { review_notes: reviewNotes } : {};
    const result = await call(`/admin/applications/${id}/reject`, 'POST', body);
    write(result);
    await loadApplications();
}

function approveFromCard(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) return;
    selectApplication(application);
    approveApplication();
}

function rejectFromCard(applicationId) {
    const application = state.applications.find(item => Number(item.id) === Number(applicationId));
    if (!application) return;
    selectApplication(application);
    rejectApplication();
}

refreshContext();
loadPendingApplications();
</script>
@endpush
