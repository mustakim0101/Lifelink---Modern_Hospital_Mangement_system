<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Applications UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1000px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        input { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; }
        button { border: 0; border-radius: 8px; background: #1d4ed8; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #334155; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 80px; overflow: auto; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">? UI Home</a></div>

    <div class="card">
        <h3>User Context</h3>
        <pre id="ctx"></pre>
    </div>

    <div class="card">
        <h3>Submit Job Application</h3>
        <input id="role" placeholder="appliedRole (e.g. ITWorker, Doctor)" value="Doctor">
        <input id="departmentId" placeholder="departmentId (optional, numeric)">
        <button onclick="submitApplication()">Submit</button>
        <button class="alt" onclick="loadLatest()">Get My Latest</button>
        <button class="alt" onclick="loadAll()">Get My Applications</button>
        <p>Uses `USER_TOKEN` from browser localStorage.</p>
    </div>

    <div class="card">
        <h3>Latest Application Snapshot</h3>
        <pre id="latest"></pre>
    </div>

    <div class="card">
        <h3>API Response</h3>
        <pre id="out"></pre>
    </div>
</div>

<script>
const out = document.getElementById('out');
const latest = document.getElementById('latest');
const ctx = document.getElementById('ctx');
const API = '/api';

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function refreshContext() {
    const data = {
        PATIENT_ID: localStorage.getItem('PATIENT_ID'),
        PATIENT_EMAIL: localStorage.getItem('PATIENT_EMAIL'),
        USER_TOKEN_PRESENT: !!localStorage.getItem('USER_TOKEN')
    };
    ctx.textContent = JSON.stringify(data, null, 2);
}

function userToken() {
    return localStorage.getItem('USER_TOKEN');
}

async function call(path, method, body = null) {
    const token = userToken();
    if (!token) return { status: 401, data: { message: 'USER_TOKEN missing. Login/register from /ui/auth first.' } };

    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    try { return { status: res.status, data: JSON.parse(text) }; } catch { return { status: res.status, data: text }; }
}

async function submitApplication() {
    const payload = {};
    const role = document.getElementById('role').value.trim();
    const departmentId = document.getElementById('departmentId').value.trim();
    if (role) payload.appliedRole = role;
    if (departmentId) payload.departmentId = Number(departmentId);

    const r = await call('/applications', 'POST', payload);
    if (r.status === 201 && r.data?.application) {
        localStorage.setItem('LAST_APPLICATION', JSON.stringify(r.data.application));
        latest.textContent = JSON.stringify(r.data.application, null, 2);
    }
    write(r);
}

async function loadLatest() {
    const r = await call('/applications/my/latest', 'GET');
    if (r.data?.latestApplication) {
        localStorage.setItem('LAST_APPLICATION', JSON.stringify(r.data.latestApplication));
        latest.textContent = JSON.stringify(r.data.latestApplication, null, 2);
    }
    write(r);
}

async function loadAll() {
    const r = await call('/applications/my', 'GET');
    write(r);
}

refreshContext();
latest.textContent = localStorage.getItem('LAST_APPLICATION') || '{}';
</script>
</body>
</html>
