@extends('ui.layouts.app')

@section('title', 'Applicant Workspace')
@section('workspace_label', 'Applicant status workspace')
@section('hero_badge', 'Applicant Mode')
@section('hero_title', 'Application status and next steps stay in one place.')
@section('hero_description', 'Applicants should not land in a staff dashboard before approval. This workspace focuses on application status, review updates, and the message that the applicant should wait for admin response.')
@section('meta_title', 'Applicant Workspace')
@section('meta_copy', 'Status tracking, review notes, and next steps')

@push('styles')
<style>
    :root {
        --app-ink: #172436;
        --app-muted: #5a6d7b;
        --app-line: rgba(23, 36, 54, 0.12);
        --app-card: rgba(255, 255, 255, 0.92);
        --app-primary: #1d4ed8;
        --app-primary-strong: #1e40af;
        --app-accent: #0f766e;
        --app-ok: #166534;
        --app-warn: #a16207;
        --app-danger: #b91c1c;
        --app-shadow: 0 16px 36px rgba(18, 34, 50, 0.14);
    }

    .applicant-grid { display: grid; gap: 10px; }
    .applicant-card {
        border: 1px solid var(--app-line);
        border-radius: 16px;
        background: var(--app-card);
        box-shadow: var(--app-shadow);
        padding: 14px;
    }

    .applicant-card h3 { margin: 0; }
    .applicant-hint { margin: 5px 0 0; color: var(--app-muted); font-size: 12px; line-height: 1.7; }
    .applicant-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .applicant-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-top: 10px; }
    .applicant-stat {
        border: 1px solid var(--app-line);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.94);
        text-align: center;
        padding: 12px;
    }
    .applicant-stat strong { display: block; font-size: 1.35rem; font-family: "Sora", "Trebuchet MS", sans-serif; }
    .applicant-stat span { color: var(--app-muted); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.06em; }

    .applicant-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
    }
    .applicant-status.pending { color: var(--app-warn); background: rgba(161, 98, 7, 0.14); }
    .applicant-status.approved { color: var(--app-ok); background: rgba(22, 101, 52, 0.14); }
    .applicant-status.rejected { color: var(--app-danger); background: rgba(185, 28, 28, 0.14); }

    .applicant-table-wrap {
        margin-top: 10px;
        border: 1px solid var(--app-line);
        border-radius: 12px;
        overflow: auto;
        background: rgba(255, 255, 255, 0.95);
    }
    .applicant-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .applicant-table th,
    .applicant-table td {
        text-align: left;
        white-space: nowrap;
        padding: 10px;
        border-bottom: 1px solid rgba(23, 36, 54, 0.08);
    }
    .applicant-table th {
        position: sticky;
        top: 0;
        background: rgba(247, 250, 255, 0.98);
        color: var(--app-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .applicant-btns { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .applicant-btn {
        border: 0;
        border-radius: 10px;
        padding: 10px 12px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }
    .applicant-btn-main { background: var(--app-primary); color: #fff; }
    .applicant-btn-main:hover { background: var(--app-primary-strong); }
    .applicant-btn-soft { background: rgba(23, 36, 54, 0.08); color: var(--app-ink); }

    .applicant-pre {
        margin: 0;
        min-height: 110px;
        max-height: 280px;
        overflow: auto;
        border-radius: 11px;
        border: 1px solid var(--app-line);
        background: #101c33;
        color: #d7e3ff;
        padding: 11px;
        font-size: 12px;
    }

    @media (max-width: 860px) {
        .applicant-row, .applicant-stats { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="/ui/applications">
        <strong>Applicant Workspace</strong>
        <span>Current area</span>
    </a>
    <a href="/ui/dashboard">
        <strong>Workspace Hub</strong>
        <span>Role redirect center</span>
    </a>
    <a href="/ui/login">
        <strong>Login Page</strong>
        <span>Switch account</span>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Applicant message</strong>
        <p id="statusMessage">Your application has been submitted. Please wait for admin review. You will be contacted soon or your status will update here.</p>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Current user</strong>
        <p id="applicantEmail">No applicant session found.</p>
        <div class="applicant-btns">
            <button class="applicant-btn applicant-btn-soft" type="button" onclick="loadLatest()">Refresh latest status</button>
            <button class="applicant-btn applicant-btn-soft" type="button" onclick="loadAll()">Load application history</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="applicant-grid">
        <div class="applicant-card">
            <h3>Latest application status</h3>
            <p class="applicant-hint">This page is the correct landing area after applicant login. Staff-role dashboards should only unlock after admin approval assigns the real role.</p>
            <div class="applicant-stats">
                <div class="applicant-stat">
                    <strong id="latestStatus">-</strong>
                    <span>Status</span>
                </div>
                <div class="applicant-stat">
                    <strong id="latestRole">-</strong>
                    <span>Applied Role</span>
                </div>
                <div class="applicant-stat">
                    <strong id="latestDepartment">-</strong>
                    <span>Department</span>
                </div>
            </div>
        </div>

        <div class="applicant-row">
            <div class="applicant-card">
                <h3>Waiting state</h3>
                <p class="applicant-hint">Pending applicants should remain here and wait for admin review. Approved applicants will receive the actual staff role, and only then should login redirect them into the matching staff dashboard.</p>
                <div id="waitingBadge" class="applicant-status pending">Pending review</div>
            </div>

            <div class="applicant-card">
                <h3>Latest review note</h3>
                <p class="applicant-hint" id="latestReviewNote">No review note available yet.</p>
            </div>
        </div>

        <div class="applicant-card">
            <h3>Application history</h3>
            <div class="applicant-table-wrap">
                <table class="applicant-table">
                    <thead>
                        <tr><th>ID</th><th>Status</th><th>Applied Role</th><th>Department</th><th>Applied At</th><th>Review Note</th></tr>
                    </thead>
                    <tbody id="applicationsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="applicant-card">
            <h3>API response</h3>
            <pre id="out" class="applicant-pre"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const out = document.getElementById('out');
const API = '/api';

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function userToken() {
    return localStorage.getItem('USER_TOKEN');
}

function applicantStatusClass(status) {
    if (status === 'Approved') return 'approved';
    if (status === 'Rejected') return 'rejected';
    return 'pending';
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

    const message = document.getElementById('statusMessage');
    if (status === 'Approved') {
        message.textContent = 'Your application was approved. Your staff role should now be active, and future logins will take you into the assigned role dashboard.';
    } else if (status === 'Rejected') {
        message.textContent = 'Your application was reviewed and rejected. Please wait for further instruction or apply again when appropriate.';
    } else {
        message.textContent = 'Your application is pending. Please wait for admin review. You will be contacted soon or your status will update here.';
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
    const rows = r.data?.applications || [];
    document.getElementById('applicationsBody').innerHTML = rows.length
        ? rows.map((row) => `
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
}

function hydrateApplicantIdentity() {
    document.getElementById('applicantEmail').textContent = localStorage.getItem('CURRENT_USER_EMAIL') || 'No applicant session found.';
}

hydrateApplicantIdentity();
loadLatest();
loadAll();
</script>
@endpush
