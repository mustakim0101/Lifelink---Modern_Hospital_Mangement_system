<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Application Reviews UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1100px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        input, select, textarea { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; box-sizing: border-box; }
        textarea { min-height: 90px; }
        button { border: 0; border-radius: 8px; background: #1d4ed8; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #334155; }
        button.reject { background: #b91c1c; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 90px; overflow: auto; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">-> UI Home</a></div>

    <div class="card">
        <h3>Admin/IT Context</h3>
        <pre id="ctx"></pre>
    </div>

    <div class="card">
        <h3>Load Applications</h3>
        <select id="statusFilter">
            <option value="">All</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
        <button onclick="loadApplications()">Load</button>
    </div>

    <div class="card">
        <h3>Review Action</h3>
        <input id="applicationId" placeholder="Application ID (numeric)">
        <textarea id="reviewNotes" placeholder="review_notes (optional)"></textarea>
        <button onclick="approveApplication()">Approve</button>
        <button class="reject" onclick="rejectApplication()">Reject</button>
        <p>Uses `ADMIN_TOKEN` from browser localStorage.</p>
    </div>

    <div class="card">
        <h3>API Response</h3>
        <pre id="out"></pre>
    </div>
</div>

<script>
const out = document.getElementById('out');
const ctx = document.getElementById('ctx');
const API = '/api';

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function refreshContext() {
    const data = {
        ADMIN_USER_ID: localStorage.getItem('ADMIN_USER_ID'),
        ADMIN_EMAIL: localStorage.getItem('ADMIN_EMAIL'),
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN')
    };
    ctx.textContent = JSON.stringify(data, null, 2);
}

function adminToken() {
    return localStorage.getItem('ADMIN_TOKEN');
}

async function call(path, method, body = null) {
    const token = adminToken();
    if (!token) return { status: 401, data: { message: 'ADMIN_TOKEN missing. Create admin/login from /ui/auth first.' } };

    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    try { return { status: res.status, data: JSON.parse(text) }; } catch { return { status: res.status, data: text }; }
}

async function loadApplications() {
    const status = document.getElementById('statusFilter').value.trim();
    const qs = status ? `?status=${encodeURIComponent(status)}` : '';
    const r = await call(`/admin/applications${qs}`, 'GET');
    write(r);
}

async function approveApplication() {
    const id = document.getElementById('applicationId').value.trim();
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    if (!id) return write('applicationId is required.');

    const body = reviewNotes ? { review_notes: reviewNotes } : {};
    const r = await call(`/admin/applications/${id}/approve`, 'POST', body);
    write(r);
}

async function rejectApplication() {
    const id = document.getElementById('applicationId').value.trim();
    const reviewNotes = document.getElementById('reviewNotes').value.trim();
    if (!id) return write('applicationId is required.');

    const body = reviewNotes ? { review_notes: reviewNotes } : {};
    const r = await call(`/admin/applications/${id}/reject`, 'POST', body);
    write(r);
}

refreshContext();
</script>
</body>
</html>

