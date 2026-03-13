<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Advanced Tools</title>
    <style>
        :root {
            --bg: #0f172a;
            --surface: #111f34;
            --surface-soft: #17283f;
            --line: rgba(173, 192, 216, 0.18);
            --text: #e5eefb;
            --muted: #a9bbd3;
            --danger: #f87171;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Consolas, "Courier New", monospace;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.16), transparent 20rem),
                linear-gradient(180deg, #08111f 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1100px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 40px;
        }

        .topbar,
        .panel,
        .card {
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(17, 31, 52, 0.92);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.24);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .topbar strong {
            display: block;
            font-size: 1.1rem;
        }

        .topbar span {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--surface-soft);
            color: var(--text);
            font: inherit;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            gap: 18px;
        }

        .panel,
        .card {
            padding: 20px;
        }

        .panel h1,
        .card h2 {
            margin: 0 0 12px;
            font-size: 1.1rem;
        }

        .panel p,
        .card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        pre {
            margin: 16px 0 0;
            padding: 16px;
            border-radius: 18px;
            background: #08111f;
            border: 1px solid rgba(173, 192, 216, 0.1);
            color: #d7e6fa;
            min-height: 220px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .notice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid rgba(248, 113, 113, 0.26);
            color: var(--danger);
            background: rgba(248, 113, 113, 0.08);
        }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <strong>LifeLink Advanced Tools</strong>
                <span>Restricted prototype diagnostics for Admin and IT Worker use.</span>
            </div>

            <div class="actions">
                <a class="button" href="/ui/dashboard">Back to dashboard</a>
                <a class="button" href="/ui">Prototype directory</a>
                <button class="button" type="button" onclick="logoutSession()">Clear session</button>
            </div>
        </header>

        <div class="grid">
            <section class="panel">
                <h1>Session context</h1>
                <p>This page is intentionally technical. It exists so Admin and IT can verify session, role, and API state without exposing these internals to general users.</p>
                <pre id="context"></pre>
            </section>

            <section class="card">
                <h2>Verification actions</h2>
                <p>Use these tools only for controlled inspection and debugging.</p>

                <div class="actions" style="margin-top: 16px;">
                    <button class="button" type="button" onclick="loadMe()">GET /auth/me</button>
                    <button class="button" type="button" onclick="refreshContext()">Refresh context</button>
                </div>

                <pre id="output"></pre>
                <div id="warning" class="notice" style="display:none;"></div>
            </section>
        </div>
    </div>

    <script>
    const API = '/api';
    const contextPre = document.getElementById('context');
    const outputPre = document.getElementById('output');
    const warning = document.getElementById('warning');

    function sessionData() {
        return {
            current_user_id: localStorage.getItem('CURRENT_USER_ID'),
            current_user_email: localStorage.getItem('CURRENT_USER_EMAIL'),
            current_user_roles: JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]'),
            user_token_present: !!localStorage.getItem('USER_TOKEN'),
            admin_token_present: !!localStorage.getItem('ADMIN_TOKEN')
        };
    }

    function refreshContext() {
        contextPre.textContent = JSON.stringify(sessionData(), null, 2);
    }

    async function call(path, method, body, token = null) {
        const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };
        if (token) headers.Authorization = `Bearer ${token}`;
        const response = await fetch(API + path, {
            method,
            headers,
            body: body ? JSON.stringify(body) : undefined
        });
        const text = await response.text();
        try {
            return { status: response.status, data: JSON.parse(text) };
        } catch {
            return { status: response.status, data: text };
        }
    }

    async function loadMe() {
        const token = localStorage.getItem('USER_TOKEN');
        if (!token) {
            outputPre.textContent = JSON.stringify({ message: 'USER_TOKEN missing.' }, null, 2);
            return;
        }

        const result = await call('/auth/me', 'GET', null, token);
        outputPre.textContent = typeof result.data === 'string'
            ? result.data
            : JSON.stringify(result, null, 2);
    }

    function logoutSession() {
        [
            'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
            'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
            'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
        ].forEach(key => localStorage.removeItem(key));
        window.location.href = '/ui/auth';
    }

    const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
    if (!(roles.includes('Admin') || roles.includes('ITWorker'))) {
        warning.style.display = 'block';
        warning.textContent = 'Access note: this page is meant for Admin or IT Worker sessions only. Return to the authenticated dashboard if this role does not apply.';
    }

    refreshContext();
    </script>
</body>
</html>
