<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink Auth UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 1100px; margin: 24px auto; padding: 16px; }
        .top { margin-bottom: 16px; }
        .top a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 16px; }
        input { width: 100%; margin: 6px 0 10px; padding: 10px; border: 1px solid #cfd9e8; border-radius: 8px; }
        button { border: 0; border-radius: 8px; background: #1d4ed8; color: #fff; padding: 10px 12px; cursor: pointer; margin-right: 8px; margin-bottom: 8px; }
        button.alt { background: #334155; }
        button.warn { background: #b91c1c; }
        pre { background: #0b1220; color: #c9d5ea; border-radius: 8px; padding: 12px; min-height: 120px; overflow: auto; }
        .hint { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top"><a href="/ui">? UI Home</a></div>

    <div class="grid">
        <div class="card">
            <h3>Create Admin</h3>
            <input id="adminEmail" placeholder="admin email" value="admin_ui@demo.com">
            <input id="adminPassword" placeholder="password" value="admin12345">
            <input id="adminName" placeholder="full name" value="Admin UI">
            <button onclick="createAdmin()">Create Admin</button>
            <p class="hint">Stores `ADMIN_TOKEN`, `ADMIN_USER_ID`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`.</p>
        </div>

        <div class="card">
            <h3>Register Patient/User</h3>
            <input id="regEmail" placeholder="user email" value="patient_ui@demo.com">
            <input id="regPassword" placeholder="password" value="patient12345">
            <input id="regName" placeholder="full name" value="Patient UI">
            <button onclick="registerUser()">Register</button>
            <p class="hint">Stores `PATIENT_ID`, `PATIENT_EMAIL`, `PATIENT_PASSWORD`, `USER_TOKEN`.</p>
        </div>

        <div class="card">
            <h3>Login</h3>
            <input id="loginEmail" placeholder="email">
            <input id="loginPassword" placeholder="password">
            <button class="alt" onclick="loginUser()">Login</button>
            <button class="alt" onclick="useStoredPatient()">Use Stored Patient</button>
            <button class="alt" onclick="me()">GET /auth/me</button>
            <p class="hint">If logged-in user has Admin role, `ADMIN_TOKEN` is refreshed automatically.</p>
        </div>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3>Stored Test Context</h3>
        <pre id="ctx"></pre>
        <button class="warn" onclick="clearStorage()">Clear Stored Context</button>
    </div>

    <div class="card" style="margin-top:16px;">
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
        USER_TOKEN_PRESENT: !!localStorage.getItem('USER_TOKEN')
    };
    ctx.textContent = JSON.stringify(data, null, 2);
}

function clearStorage() {
    [
        'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'ADMIN_PASSWORD',
        'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL', 'PATIENT_PASSWORD',
        'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
    ].forEach(k => localStorage.removeItem(k));
    refreshContext();
    write({ message: 'Stored context cleared.' });
}

function useStoredPatient() {
    document.getElementById('loginEmail').value = localStorage.getItem('PATIENT_EMAIL') || '';
    document.getElementById('loginPassword').value = localStorage.getItem('PATIENT_PASSWORD') || '';
}

async function call(path, method, body, token = null) {
    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
    if (token) headers.Authorization = `Bearer ${token}`;
    const res = await fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    try { return { status: res.status, data: JSON.parse(text) }; } catch { return { status: res.status, data: text }; }
}

function persistLoginContext(responseData, submittedEmail, submittedPassword) {
    const user = responseData?.user || {};
    const roles = Array.isArray(user.roles) ? user.roles : [];

    localStorage.setItem('USER_TOKEN', responseData.token || '');
    localStorage.setItem('CURRENT_USER_ID', String(user.id || ''));
    localStorage.setItem('CURRENT_USER_EMAIL', user.email || submittedEmail || '');
    localStorage.setItem('CURRENT_USER_ROLES', JSON.stringify(roles));

    if (roles.includes('Admin')) {
        localStorage.setItem('ADMIN_TOKEN', responseData.token || '');
        localStorage.setItem('ADMIN_USER_ID', String(user.id || ''));
        localStorage.setItem('ADMIN_EMAIL', user.email || submittedEmail || '');
        localStorage.setItem('ADMIN_PASSWORD', submittedPassword || '');
    }
}

async function createAdmin() {
    const payload = {
        email: document.getElementById('adminEmail').value.trim(),
        password: document.getElementById('adminPassword').value.trim(),
        fullName: document.getElementById('adminName').value.trim()
    };
    const r = await call('/dev/create-admin', 'POST', payload);
    if (r.data && r.data.token) {
        localStorage.setItem('ADMIN_TOKEN', r.data.token);
        localStorage.setItem('ADMIN_USER_ID', String(r.data.user?.id || ''));
        localStorage.setItem('ADMIN_EMAIL', payload.email);
        localStorage.setItem('ADMIN_PASSWORD', payload.password);
    }
    refreshContext();
    write(r);
}

async function registerUser() {
    const payload = {
        email: document.getElementById('regEmail').value.trim(),
        password: document.getElementById('regPassword').value.trim(),
        fullName: document.getElementById('regName').value.trim()
    };
    const r = await call('/auth/register', 'POST', payload);
    if (r.data && r.data.token) {
        localStorage.setItem('USER_TOKEN', r.data.token);
        localStorage.setItem('PATIENT_ID', String(r.data.user?.id || ''));
        localStorage.setItem('PATIENT_EMAIL', payload.email);
        localStorage.setItem('PATIENT_PASSWORD', payload.password);
        localStorage.setItem('CURRENT_USER_ID', String(r.data.user?.id || ''));
        localStorage.setItem('CURRENT_USER_EMAIL', payload.email);
        localStorage.setItem('CURRENT_USER_ROLES', JSON.stringify(r.data.user?.roles || []));
    }
    refreshContext();
    write(r);
}

async function loginUser() {
    const payload = {
        email: document.getElementById('loginEmail').value.trim(),
        password: document.getElementById('loginPassword').value.trim()
    };
    const r = await call('/auth/login', 'POST', payload);
    if (r.data && r.data.token) {
        persistLoginContext(r.data, payload.email, payload.password);
    }
    refreshContext();
    write(r);
}

async function me() {
    const token = localStorage.getItem('USER_TOKEN');
    if (!token) {
        write({ status: 401, data: { message: 'USER_TOKEN missing. Login first.' } });
        return;
    }
    const r = await call('/auth/me', 'GET', null, token);
    write(r);
}

refreshContext();
useStoredPatient();
</script>
</body>
</html>
