<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Admin Account Control UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1000px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        input { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; }
        button { border: 0; border-radius: 8px; background: #b91c1c; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #0f766e; }
        button.mid { background: #334155; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 80px; overflow: auto; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">? UI Home</a></div>

    <div class="card">
        <h3>Stored Context</h3>
        <pre id="ctx"></pre>
        <button class="mid" onclick="loadPatientId()">Use Stored PATIENT_ID</button>
        <button class="mid" onclick="testPatientLogin()">Test Patient Login (frozen check)</button>
    </div>

    <div class="card">
        <h3>Admin Freeze / Unfreeze</h3>
        <input id="userId" placeholder="target user id (e.g. 10004)">
        <button onclick="freezeUser()">Freeze</button>
        <button class="alt" onclick="unfreezeUser()">Unfreeze</button>
        <button class="mid" onclick="statusUser()">Status</button>
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
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        PATIENT_ID: localStorage.getItem('PATIENT_ID'),
        PATIENT_EMAIL: localStorage.getItem('PATIENT_EMAIL'),
        PATIENT_PASSWORD_PRESENT: !!localStorage.getItem('PATIENT_PASSWORD')
    };
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

async function call(path, method) {
    const token = adminToken();
    if (!token) return { status: 401, data: { message: 'ADMIN_TOKEN missing. Create/login admin from /ui/auth first.' } };

    const headers = { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers });
    const text = await res.text();
    try { return { status: res.status, data: JSON.parse(text) }; } catch { return { status: res.status, data: text }; }
}

async function freezeUser() {
    const id = targetId();
    const r = await call(`/admin/users/${id}/freeze`, 'POST');
    write(r);
}

async function unfreezeUser() {
    const id = targetId();
    const r = await call(`/admin/users/${id}/unfreeze`, 'POST');
    write(r);
}

async function statusUser() {
    const id = targetId();
    const r = await call(`/admin/users/${id}/status`, 'GET');
    write(r);
}

async function testPatientLogin() {
    const email = localStorage.getItem('PATIENT_EMAIL');
    const password = localStorage.getItem('PATIENT_PASSWORD');

    if (!email || !password) {
        write({ status: 400, data: { message: 'PATIENT_EMAIL/PATIENT_PASSWORD missing. Register patient from /ui/auth first.' } });
        return;
    }

    const res = await fetch(API + '/auth/login', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });

    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch { data = text; }
    write({ status: res.status, data });
}

refreshContext();
loadPatientId();
</script>
</body>
</html>
