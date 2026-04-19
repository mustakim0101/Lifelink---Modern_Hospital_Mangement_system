@extends('ui.layouts.app')

@section('title', 'Applicant Workspace')
@section('role_theme', 'applicant')
@section('workspace_label', '')
@section('hero_badge', '')
@section('hero_title', 'Applicant Dashboard')
@section('hero_description', '')
@section('hide_meta_card', '1')
@section('meta_title', 'Applicant Workspace')
@section('meta_copy', 'Status tracking, review notes, and next steps')

@push('styles')
<style>
    .applicant-shell { display: grid; gap: 16px; }
    .applicant-panel { display: none; }

    .applicant-header {
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(245, 250, 254, 0.94));
        padding: 16px;
    }

    .applicant-header h2 {
        margin: 0;
        font-size: clamp(1.7rem, 2.6vw, 2.3rem);
        color: #0f172a;
    }

    .applicant-header p {
        margin: 8px 0 0;
        color: #475569;
    }

    .applicant-status-hero {
        border: 2px solid rgba(217, 119, 6, 0.5);
        border-radius: 18px;
        background: linear-gradient(180deg, #fef8d8, #fdf2ba);
        padding: 26px 18px;
        text-align: center;
    }

    .applicant-status-hero.pending {
        border-color: rgba(217, 119, 6, 0.48);
        background: linear-gradient(180deg, #fef8d8, #fdf2ba);
    }

    .applicant-status-hero.approved {
        border-color: rgba(5, 150, 105, 0.45);
        background: linear-gradient(180deg, #e7fbef, #d7f7e5);
    }

    .applicant-status-hero.rejected {
        border-color: rgba(185, 28, 28, 0.54);
        background: linear-gradient(180deg, #fee9e9, #fdd5d5);
    }

    .applicant-status-icon {
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        font-weight: 900;
        font-size: 1.35rem;
        border: 2px solid currentColor;
    }

    .applicant-status-icon.pending { color: #b45309; }
    .applicant-status-icon.approved { color: #047857; }
    .applicant-status-icon.rejected { color: #b91c1c; }

    .applicant-status-title {
        margin: 0;
        font-size: clamp(1.55rem, 2.5vw, 2rem);
        color: #0f172a;
    }

    .applicant-status-copy {
        margin: 10px auto 0;
        max-width: 62ch;
        color: #334155;
        line-height: 1.7;
    }

    .applicant-status {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .applicant-status.pending { color: #b45309; background: rgba(245, 158, 11, 0.2); }
    .applicant-status.approved { color: #047857; background: rgba(5, 150, 105, 0.2); }
    .applicant-status.rejected { color: #b91c1c; background: rgba(220, 38, 38, 0.22); }

    .applicant-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .applicant-card {
        border: 1px solid rgba(148, 163, 184, 0.26);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.96);
        padding: 16px;
    }

    .applicant-card h3 {
        margin: 0;
        color: #0f172a;
        font-size: 1.5rem;
    }

    .applicant-kv {
        margin-top: 14px;
        display: grid;
        gap: 12px;
    }

    .applicant-kv div {
        display: grid;
        gap: 4px;
    }

    .applicant-kv span {
        color: #64748b;
        font-size: 0.9rem;
    }

    .applicant-kv strong {
        color: #0f172a;
        font-size: 1.02rem;
    }

    .applicant-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .applicant-btn {
        border: 0;
        border-radius: 10px;
        min-height: 40px;
        padding: 10px 12px;
        font: inherit;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
    }

    .applicant-btn-main { background: #0369a1; color: #fff; }
    .applicant-btn-main:hover { background: #075985; }
    .applicant-btn-soft { background: rgba(15, 23, 42, 0.08); color: #0f172a; }

    .applicant-history-card {
        border: 1px solid rgba(148, 163, 184, 0.26);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.96);
        padding: 16px;
    }

    .applicant-table-wrap {
        margin-top: 10px;
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 12px;
        overflow: auto;
        background: rgba(255, 255, 255, 0.98);
    }

    .applicant-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.83rem;
    }

    .applicant-table th,
    .applicant-table td {
        text-align: left;
        white-space: nowrap;
        padding: 10px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    }

    .applicant-table th {
        position: sticky;
        top: 0;
        background: rgba(247, 250, 255, 0.98);
        color: #475569;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .applicant-pre {
        margin: 0;
        min-height: 120px;
        max-height: 280px;
        overflow: auto;
        border-radius: 10px;
        border: 1px solid rgba(15, 23, 42, 0.2);
        background: #0f1e34;
        color: #d7e3ff;
        padding: 10px;
        font-size: 12px;
    }

    @media (max-width: 980px) {
        .applicant-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#app-status">
        <strong>Overview</strong>
    </a>
    <a href="#app-profile">
        <strong>Personal Profile</strong>
    </a>
    <a href="#app-waiting">
        <strong>Waiting State</strong>
    </a>
    <a href="#app-history">
        <strong>History</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="applicant-shell">
        <section id="app-status" class="ll-section applicant-panel" data-display="block">
            <div class="applicant-header">
                <h2>Application Status</h2>
                <p>Track your staff application progress.</p>
            </div>

            <div id="statusHero" class="applicant-status-hero pending" style="margin-top:14px;">
                <div id="statusIcon" class="applicant-status-icon pending">!</div>
                <h3 id="statusTitle" class="applicant-status-title">Application Under Review</h3>
                <p id="statusMessage" class="applicant-status-copy">
                    Your application is currently being reviewed by our team.
                </p>
                <div id="waitingBadge" class="applicant-status pending">Pending</div>
            </div>

            <div class="applicant-actions">
                <button class="applicant-btn applicant-btn-main" type="button" onclick="loadLatest()">Refresh status</button>
                <button class="applicant-btn applicant-btn-soft" type="button" onclick="loadAll()">Load history</button>
            </div>
        </section>

        <section id="app-profile" class="applicant-history-card ll-section applicant-panel" data-display="block">
            <div id="applicantProfileMount"></div>
        </section>

        <section id="app-waiting" class="applicant-row ll-section applicant-panel" data-display="grid">
            <div class="applicant-card">
                <h3>Application Details</h3>
                <div class="applicant-kv">
                    <div><span>Applicant Email</span><strong id="applicantEmail">No applicant session found.</strong></div>
                    <div><span>Applied Position</span><strong id="latestRole">-</strong></div>
                    <div><span>Department</span><strong id="latestDepartment">-</strong></div>
                    <div><span>Current Status</span><strong id="latestStatus">-</strong></div>
                </div>
            </div>

            <div class="applicant-card">
                <h3>Review and Next Step</h3>
                <div class="applicant-kv">
                    <div><span>Latest Review Note</span><strong id="latestReviewNote">No review note available yet.</strong></div>
                    <div><span>Role Assignment</span><strong>If approved, your account is assigned to Doctor, Nurse, or IT Worker and future login routes to that dashboard.</strong></div>
                </div>
            </div>
        </section>

        <section id="app-history" class="applicant-history-card ll-section applicant-panel" data-display="block">
            <h3>Application history</h3>
            <div class="applicant-table-wrap">
                <table class="applicant-table">
                    <thead>
                        <tr><th>ID</th><th>Status</th><th>Applied Role</th><th>Department</th><th>Applied At</th><th>Review Note</th></tr>
                    </thead>
                    <tbody id="applicationsBody"></tbody>
                </table>
            </div>
            <div id="applicationsPagination" class="ui-list-pagination"></div>
        </section>

    </div>
@endsection

@push('scripts')
<script>
const out = document.getElementById('out');
const API = '/api';
const applicantPanelIds = ['app-status', 'app-profile', 'app-waiting', 'app-history'];
const applicantNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[href^="#app-"]'));
const applicantState = {
    applications: [],
    pagination: {
        historyPageSize: 10,
        historyPage: 1,
    },
};

function write(data) {
    if (!window.lifeLinkShell?.isDebugEnabled() || !out) return;
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function setActivePanel(panelId) {
    applicantPanelIds.forEach((id) => {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.style.display = id === panelId ? (panel.dataset.display || 'block') : 'none';
    });

    applicantNavLinks.forEach((link) => {
        const targetId = (link.getAttribute('href') || '').replace('#', '');
        link.classList.toggle('is-active', targetId === panelId);
    });
}

function setupSidebarPanelNav() {
    applicantNavLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const panelId = (link.getAttribute('href') || '').replace('#', '');
            if (!applicantPanelIds.includes(panelId)) return;
            setActivePanel(panelId);
            history.replaceState(null, '', `#${panelId}`);
        });
    });

    const initialHash = (window.location.hash || '').replace('#', '');
    const initialPanel = applicantPanelIds.includes(initialHash) ? initialHash : applicantPanelIds[0];
    setActivePanel(initialPanel);
}

function userToken() {
    return localStorage.getItem('USER_TOKEN');
}

function applicantStatusClass(status) {
    if (status === 'Approved') return 'approved';
    if (status === 'Rejected') return 'rejected';
    return 'pending';
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

function renderHistoryPagination(pageData) {
    const root = document.getElementById('applicationsPagination');
    if (!root) return;
    if (pageData.totalRows <= applicantState.pagination.historyPageSize) {
        root.innerHTML = '';
        return;
    }
    root.innerHTML = `
        <div class="ui-list-pagination__meta">Page ${pageData.page} of ${pageData.totalPages} (${pageData.totalRows} total)</div>
        <div class="ui-list-pagination__controls">
            <button class="applicant-btn applicant-btn-soft" type="button" ${pageData.page <= 1 ? 'disabled' : ''} onclick="prevApplicationsHistoryPage()">Previous</button>
            <button class="applicant-btn applicant-btn-soft" type="button" ${pageData.page >= pageData.totalPages ? 'disabled' : ''} onclick="nextApplicationsHistoryPage()">Next</button>
        </div>
    `;
}

function prevApplicationsHistoryPage() {
    applicantState.pagination.historyPage = Math.max(1, applicantState.pagination.historyPage - 1);
    renderApplicationHistory();
}

function nextApplicationsHistoryPage() {
    applicantState.pagination.historyPage += 1;
    renderApplicationHistory();
}

function renderApplicationHistory() {
    const body = document.getElementById('applicationsBody');
    const pageData = paginateRows(
        applicantState.applications,
        applicantState.pagination.historyPage,
        applicantState.pagination.historyPageSize
    );
    applicantState.pagination.historyPage = pageData.page;

    body.innerHTML = pageData.totalRows
        ? pageData.rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td><span class="applicant-status ${applicantStatusClass(row.status)}">${row.status || '-'}</span></td>
                <td>${row.applied_role || '-'}</td>
                <td>${row.applied_department || '-'}</td>
                <td>${row.applied_at ? new Date(row.applied_at).toLocaleString() : '-'}</td>
                <td>${row.review_notes || '-'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="6">No applications found.</td></tr>';
    renderHistoryPagination(pageData);
}

function renderWaitingState(application) {
    const status = application?.status || 'No application';
    const role = application?.applied_role || '-';
    const department = application?.applied_department || 'General';
    const reviewNote = application?.review_notes || 'No review note available yet.';

    document.getElementById('latestStatus').textContent = status;
    document.getElementById('latestRole').textContent = role;
    document.getElementById('latestDepartment').textContent = department;
    document.getElementById('latestReviewNote').textContent = reviewNote;

    const badge = document.getElementById('waitingBadge');
    badge.className = `applicant-status ${applicantStatusClass(status)}`;
    badge.textContent = status;

    const hero = document.getElementById('statusHero');
    const icon = document.getElementById('statusIcon');
    const title = document.getElementById('statusTitle');
    hero.className = `applicant-status-hero ${applicantStatusClass(status)}`;
    icon.className = `applicant-status-icon ${applicantStatusClass(status)}`;

    const message = document.getElementById('statusMessage');
    if (status === 'Approved') {
        icon.textContent = 'OK';
        title.textContent = 'Application Approved';
        message.textContent = 'Your application has been approved. Your account is now role-assigned, and future logins will route you to your staff dashboard.';
    } else if (status === 'Rejected') {
        icon.textContent = 'X';
        title.textContent = 'Application Rejected';
        message.textContent = 'Your application was reviewed and rejected. Please wait for further instruction before reapplying.';
    } else {
        icon.textContent = '!';
        title.textContent = 'Application Under Review';
        message.textContent = 'Your application is currently being reviewed by our admin team. You will receive updates here once a decision is made.';
    }
}

async function call(path, method, body = null) {
    const token = userToken();
    if (!token) return { status: 401, data: { message: 'USER_TOKEN missing. Login first from /ui/login.' } };

    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    try { return { status: res.status, data: JSON.parse(text) }; } catch { return { status: res.status, data: text }; }
}

async function loadLatest() {
    const r = await call('/applications/my/latest', 'GET');
    write(r);
    const application = r.data?.latestApplication || null;
    renderWaitingState(application);
}

async function loadAll() {
    const r = await call('/applications/my', 'GET');
    write(r);
    applicantState.applications = r.data?.applications || [];
    applicantState.pagination.historyPage = 1;
    renderApplicationHistory();
}

function hydrateApplicantIdentity() {
    const applicantEmail = localStorage.getItem('CURRENT_USER_EMAIL') || 'No applicant session found.';
    const applicantName = localStorage.getItem('CURRENT_USER_FULL_NAME') || applicantEmail || 'Applicant';
    document.getElementById('applicantEmail').textContent = applicantEmail;
    if (window.lifeLinkShell) {
        window.lifeLinkShell.mountProfileEditor({
            containerId: 'applicantProfileMount',
            role: 'Applicant',
            userId: localStorage.getItem('CURRENT_USER_ID') || '-',
            hideDepartment: true,
        });
    }
}

hydrateApplicantIdentity();
if (window.lifeLinkShell) {
    window.lifeLinkShell.updateIdentityContext({
        name: localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Applicant',
        userId: localStorage.getItem('CURRENT_USER_ID') || '-',
        email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
        role: 'Applicant',
        hideDepartment: true,
    });
}
setupSidebarPanelNav();
loadLatest();
loadAll();
</script>
@endpush
