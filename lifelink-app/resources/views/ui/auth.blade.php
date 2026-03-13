<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Authentication</title>
    <style>
        :root {
            --bg: #eff5f8;
            --surface: rgba(255, 255, 255, 0.84);
            --surface-strong: #ffffff;
            --line: rgba(22, 49, 67, 0.12);
            --text: #163143;
            --muted: #607482;
            --primary: #0f766e;
            --secondary: #1d4ed8;
            --danger: #b91c1c;
            --success: #15803d;
            --warning: #b45309;
            --shadow: 0 30px 70px rgba(17, 42, 60, 0.13);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.14), transparent 24rem),
                radial-gradient(circle at right, rgba(15, 118, 110, 0.14), transparent 26rem),
                linear-gradient(180deg, #f8fbfd 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1220px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 44px;
        }

        .topline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
        }

        .crumbs {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-weight: 600;
        }

        .crumbs a:hover { color: var(--text); }

        .mini-link {
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.72);
        }

        .intro {
            border: 1px solid var(--line);
            border-radius: 28px;
            background:
                radial-gradient(circle at top left, rgba(249, 115, 22, 0.18), transparent 18rem),
                linear-gradient(165deg, rgba(255, 255, 255, 0.88), rgba(229, 245, 244, 0.82));
            box-shadow: var(--shadow);
            padding: 34px;
            margin-bottom: 22px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.1);
            color: #0b625d;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .intro h1 {
            margin: 18px 0 14px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(2.2rem, 4vw, 4rem);
            line-height: 1.05;
            max-width: 13ch;
        }

        .intro p {
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
            max-width: 72ch;
        }

        .notice-bar {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .notice-chip {
            padding: 16px;
            border-radius: 20px;
            border: 1px solid rgba(22, 49, 67, 0.1);
            background: rgba(255, 255, 255, 0.7);
        }

        .notice-chip strong {
            display: block;
            margin-bottom: 6px;
        }

        .notice-chip p {
            margin: 0;
            font-size: 0.95rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 26px;
            background: var(--surface);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
            padding: 24px;
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 1.3rem;
        }

        .card p {
            margin: 0 0 16px;
            color: var(--muted);
            line-height: 1.7;
        }

        .field {
            display: grid;
            gap: 8px;
            margin-bottom: 14px;
        }

        .field label {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid rgba(22, 49, 67, 0.14);
            background: #fbfdff;
            color: var(--text);
            font: inherit;
        }

        .field textarea {
            min-height: 92px;
            resize: vertical;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: 2px solid rgba(29, 78, 216, 0.18);
            border-color: rgba(29, 78, 216, 0.28);
        }

        .button-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 13px 18px;
            border-radius: 999px;
            border: 0;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        .button-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            box-shadow: 0 18px 30px rgba(29, 78, 216, 0.22);
        }

        .button-secondary {
            color: var(--text);
            background: rgba(22, 49, 67, 0.06);
        }

        .button-warm {
            color: #fff;
            background: linear-gradient(135deg, #ea580c, #c2410c);
        }

        .session-card,
        .message,
        .advanced-card {
            margin-top: 18px;
            padding: 18px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.72);
        }

        .session-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 12px;
        }

        .session-grid div {
            padding: 14px;
            border-radius: 16px;
            background: rgba(22, 49, 67, 0.05);
        }

        .session-grid small {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .session-grid strong { font-size: 1rem; }

        .message {
            display: none;
            line-height: 1.7;
        }

        .message.show { display: block; }
        .message.success {
            color: var(--success);
            background: rgba(21, 128, 61, 0.08);
            border-color: rgba(21, 128, 61, 0.18);
        }

        .message.error {
            color: var(--danger);
            background: rgba(185, 28, 28, 0.08);
            border-color: rgba(185, 28, 28, 0.18);
        }

        .message.info {
            color: var(--warning);
            background: rgba(180, 83, 9, 0.08);
            border-color: rgba(180, 83, 9, 0.18);
        }

        .advanced-toggle {
            margin-top: 18px;
            padding: 0;
            border: 0;
            background: none;
            color: var(--secondary);
            font-weight: 700;
            cursor: pointer;
        }

        .advanced-card { display: none; }
        .advanced-card.show { display: block; }
        .advanced-card h3 { margin: 0 0 10px; }
        .advanced-card p { margin: 0 0 14px; color: var(--muted); line-height: 1.7; }

        .focus-target {
            outline: 2px solid rgba(29, 78, 216, 0.2);
        }

        @media (max-width: 960px) {
            .grid,
            .notice-bar,
            .session-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .shell { width: min(100% - 24px, 1220px); }
            .topline { flex-direction: column; align-items: flex-start; }
            .intro,
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="topline">
            <div class="crumbs">
                <a href="/">Public Home</a>
                <span>/</span>
                <span>Authentication</span>
            </div>
            <a class="mini-link" href="/ui">Prototype Directory</a>
        </div>

        <section class="intro">
            <span class="eyebrow">Authenticated Mode</span>
            <h1>One login path, multiple registration paths.</h1>
            <p>
                Login is shared for all users. Registration is split by intent so a visitor can join as a patient,
                start the donor journey, or create a job applicant profile. Registration no longer stores passwords
                in browser local storage, and it no longer auto-logs users into the dashboard.
            </p>

            <div class="notice-bar">
                <div class="notice-chip">
                    <strong>Login is unified</strong>
                    <p>Any existing account uses the same login card. The system decides the dashboard by role after sign-in.</p>
                </div>
                <div class="notice-chip">
                    <strong>Registration is separated</strong>
                    <p>Public users can now choose a registration path based on why they are entering the system.</p>
                </div>
                <div class="notice-chip">
                    <strong>Passwords are not stored</strong>
                    <p>Only the last used email is remembered to make relogin easier after registration.</p>
                </div>
            </div>
        </section>

        <section class="grid">
            <article id="login-card" class="card">
                <h2>Login for all users</h2>
                <p>Use your email and password to access the correct role-based dashboard.</p>

                <div class="field">
                    <label for="loginEmail">Email</label>
                    <input id="loginEmail" type="email" placeholder="name@example.com">
                </div>

                <div class="field">
                    <label for="loginPassword">Password</label>
                    <input id="loginPassword" type="password" placeholder="Enter your password">
                </div>

                <div class="button-row">
                    <button class="button button-primary" type="button" onclick="loginUser()">Login</button>
                    <button class="button button-secondary" type="button" onclick="useLastEmail()">Use last email</button>
                </div>

                <div class="session-card">
                    <strong>Current session</strong>
                    <div class="session-grid">
                        <div>
                            <small>User</small>
                            <strong id="session-user">No active session</strong>
                        </div>
                        <div>
                            <small>Roles</small>
                            <strong id="session-roles">None</strong>
                        </div>
                    </div>

                    <div class="button-row">
                        <a class="button button-secondary" href="/ui/dashboard">Go to dashboard</a>
                        <button class="button button-secondary" type="button" onclick="clearStorage()">Clear session</button>
                    </div>
                </div>

                <button class="advanced-toggle" type="button" onclick="toggleAdvanced()">Need bootstrap or setup tools?</button>

                <div id="advanced-card" class="advanced-card">
                    <h3>Advanced setup</h3>
                    <p>This stays outside the normal public flow. Use it only when you need to bootstrap the first Admin account in a fresh environment.</p>

                    <div class="field">
                        <label for="adminName">Admin full name</label>
                        <input id="adminName" type="text" value="Admin UI">
                    </div>

                    <div class="field">
                        <label for="adminEmail">Admin email</label>
                        <input id="adminEmail" type="email" value="admin_ui@demo.com">
                    </div>

                    <div class="field">
                        <label for="adminPassword">Admin password</label>
                        <input id="adminPassword" type="password" value="admin12345">
                    </div>

                    <div class="button-row">
                        <button class="button button-warm" type="button" onclick="createAdmin()">Create first admin</button>
                    </div>
                </div>
            </article>

            <article id="patient-card" class="card">
                <h2>Register as patient</h2>
                <p>Create a standard user account for patient-side access such as appointments, records, and blood requests.</p>

                <div class="field">
                    <label for="patientName">Full name</label>
                    <input id="patientName" type="text" value="Patient UI">
                </div>

                <div class="field">
                    <label for="patientEmail">Email</label>
                    <input id="patientEmail" type="email" value="patient_ui@demo.com">
                </div>

                <div class="field">
                    <label for="patientPassword">Password</label>
                    <input id="patientPassword" type="password" value="patient12345">
                </div>

                <div class="button-row">
                    <button class="button button-primary" type="button" onclick="registerPatient()">Register patient</button>
                </div>
            </article>

            <article id="donor-card" class="card">
                <h2>Register as blood donor</h2>
                <p>Create the account and immediately initialize the donor profile so the user can later log in and continue donor activities.</p>

                <div class="field">
                    <label for="donorName">Full name</label>
                    <input id="donorName" type="text" value="Donor UI">
                </div>

                <div class="field">
                    <label for="donorEmail">Email</label>
                    <input id="donorEmail" type="email" value="donor_ui@demo.com">
                </div>

                <div class="field">
                    <label for="donorPassword">Password</label>
                    <input id="donorPassword" type="password" value="donor12345">
                </div>

                <div class="field">
                    <label for="donorBloodGroup">Blood group</label>
                    <select id="donorBloodGroup">
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+" selected>O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="field">
                    <label for="donorNotes">Notes</label>
                    <textarea id="donorNotes" placeholder="Optional donor notes"></textarea>
                </div>

                <div class="button-row">
                    <button class="button button-primary" type="button" onclick="registerDonor()">Register donor</button>
                </div>
            </article>

            <article id="applicant-card" class="card">
                <h2>Register as job applicant</h2>
                <p>Create the account and submit an initial job application flow so the user can later log in and track status.</p>

                <div class="field">
                    <label for="applicantName">Full name</label>
                    <input id="applicantName" type="text" value="Applicant UI">
                </div>

                <div class="field">
                    <label for="applicantEmail">Email</label>
                    <input id="applicantEmail" type="email" value="applicant_ui@demo.com">
                </div>

                <div class="field">
                    <label for="applicantPassword">Password</label>
                    <input id="applicantPassword" type="password" value="applicant12345">
                </div>

                <div class="field">
                    <label for="applicantRole">Applied role</label>
                    <input id="applicantRole" type="text" value="ITWorker" placeholder="Example: ITWorker, Nurse, Doctor">
                </div>

                <div class="field">
                    <label for="applicantDepartment">Department ID (optional)</label>
                    <input id="applicantDepartment" type="number" placeholder="Example: 1">
                </div>

                <div class="button-row">
                    <button class="button button-primary" type="button" onclick="registerApplicant()">Register applicant</button>
                </div>
            </article>
        </section>

        <div id="message" class="message"></div>
    </div>

    <script>
    const API = '/api';
    const message = document.getElementById('message');
    const sessionUser = document.getElementById('session-user');
    const sessionRoles = document.getElementById('session-roles');
    const advancedCard = document.getElementById('advanced-card');

    function showMessage(kind, text) {
        message.className = `message show ${kind}`;
        message.textContent = text;
    }

    function clearMessage() {
        message.className = 'message';
        message.textContent = '';
    }

    async function call(path, method, body, token = null) {
        const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };
        if (token) headers.Authorization = `Bearer ${token}`;

        const response = await fetch(API + path, {
            method,
            headers,
            body: body ? JSON.stringify(body) : undefined,
        });

        const text = await response.text();
        try {
            return { status: response.status, data: JSON.parse(text) };
        } catch {
            return { status: response.status, data: text };
        }
    }

    function persistLoginContext(responseData, submittedEmail) {
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
            //localStorage.setItem('ADMIN_PASSWORD', submittedPassword || '');  -->>this causes to store password under inspect/application/local storage 
        }
    }

    function clearStorage() {
        [
            'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
            'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
            'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
        ].forEach(key => localStorage.removeItem(key));

        refreshSessionCard();
        showMessage('info', 'Stored session cleared.');
    }

    function rememberLastEmail(email) {
        if (email) {
            localStorage.setItem('LAST_USED_EMAIL', email);
        }
    }

    function useLastEmail() {
        document.getElementById('loginEmail').value = localStorage.getItem('LAST_USED_EMAIL') || '';
    }

    function refreshSessionCard() {
        const email = localStorage.getItem('CURRENT_USER_EMAIL');
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        sessionUser.textContent = email || 'No active session';
        sessionRoles.textContent = roles.length ? roles.join(', ') : 'None';
    }

    function toggleAdvanced() {
        advancedCard.classList.toggle('show');
    }

    function fillLoginEmail(email) {
        document.getElementById('loginEmail').value = email || '';
        document.getElementById('loginPassword').value = '';
        document.getElementById('login-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function extractMessage(result, fallback) {
        if (typeof result?.data === 'string') return result.data;
        if (result?.data?.message) return result.data.message;
        if (result?.data?.errors) {
            const firstKey = Object.keys(result.data.errors)[0];
            if (firstKey && Array.isArray(result.data.errors[firstKey])) {
                return result.data.errors[firstKey][0];
            }
        }
        return fallback;
    }

    function clearTransientSession() {
        [
            'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
            'USER_TOKEN', 'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
        ].forEach(key => localStorage.removeItem(key));
        refreshSessionCard();
    }

    async function createAdmin() {
        clearMessage();
        const payload = {
            email: document.getElementById('adminEmail').value.trim(),
            password: document.getElementById('adminPassword').value.trim(),
            fullName: document.getElementById('adminName').value.trim(),
        };

        const result = await call('/dev/create-admin', 'POST', payload);
        if (result.status >= 200 && result.status < 300 && result.data?.token) {
            persistLoginContext(result.data, payload.email);
            rememberLastEmail(payload.email);
            refreshSessionCard();
            showMessage('success', 'Admin account created successfully. You can continue into the dashboard now.');
            return;
        }

        showMessage('error', extractMessage(result, 'Unable to create admin account.'));
    }

    async function loginUser() {
        clearMessage();
        const payload = {
            email: document.getElementById('loginEmail').value.trim(),
            password: document.getElementById('loginPassword').value.trim(),
        };

        const result = await call('/auth/login', 'POST', payload);
        if (result.status >= 200 && result.status < 300 && result.data?.token) {
            persistLoginContext(result.data, payload.email);
            rememberLastEmail(payload.email);
            refreshSessionCard();
            showMessage('success', 'Login successful. Redirecting to your dashboard.');
            setTimeout(() => window.location.href = '/ui/dashboard', 700);
            return;
        }

        showMessage('error', extractMessage(result, 'Login failed.'));
    }

    async function registerBase(name, email, password) {
        const result = await call('/auth/register', 'POST', {
            fullName: name,
            email,
            password,
        });

        if (!(result.status >= 200 && result.status < 300 && result.data?.token)) {
            throw new Error(extractMessage(result, 'Registration failed.'));
        }

        rememberLastEmail(email);
        return result.data;
    }

    async function registerPatient() {
        clearMessage();
        const name = document.getElementById('patientName').value.trim();
        const email = document.getElementById('patientEmail').value.trim();
        const password = document.getElementById('patientPassword').value.trim();

        try {
            await registerBase(name, email, password);
            clearTransientSession();
            fillLoginEmail(email);
            showMessage('success', 'Patient account created. Please log in with your new account to continue.');
        } catch (error) {
            showMessage('error', error.message);
        }
    }

    async function registerDonor() {
        clearMessage();
        const name = document.getElementById('donorName').value.trim();
        const email = document.getElementById('donorEmail').value.trim();
        const password = document.getElementById('donorPassword').value.trim();
        const bloodGroup = document.getElementById('donorBloodGroup').value;
        const notes = document.getElementById('donorNotes').value.trim();

        try {
            const authData = await registerBase(name, email, password);
            const enrollResult = await call('/donor/enroll', 'POST', { bloodGroup, notes }, authData.token);
            if (!(enrollResult.status >= 200 && enrollResult.status < 300)) {
                throw new Error(extractMessage(enrollResult, 'Donor profile setup failed after account creation.'));
            }

            clearTransientSession();
            fillLoginEmail(email);
            showMessage('success', 'Donor account created and donor profile initialized. Please log in to continue.');
        } catch (error) {
            showMessage('error', error.message);
        }
    }

    async function registerApplicant() {
        clearMessage();
        const name = document.getElementById('applicantName').value.trim();
        const email = document.getElementById('applicantEmail').value.trim();
        const password = document.getElementById('applicantPassword').value.trim();
        const appliedRole = document.getElementById('applicantRole').value.trim();
        const departmentRaw = document.getElementById('applicantDepartment').value.trim();

        try {
            const authData = await registerBase(name, email, password);
            const payload = { appliedRole };
            if (departmentRaw !== '') {
                payload.departmentId = Number(departmentRaw);
            }

            const applicationResult = await call('/applications', 'POST', payload, authData.token);
            if (!(applicationResult.status >= 200 && applicationResult.status < 300)) {
                throw new Error(extractMessage(applicationResult, 'Application submission failed after account creation.'));
            }

            clearTransientSession();
            fillLoginEmail(email);
            showMessage('success', 'Applicant account created and initial application submitted. Please log in to continue.');
        } catch (error) {
            showMessage('error', error.message);
        }
    }

    function focusCardFromHash() {
        const hash = window.location.hash;
        const targetMap = {
            '#login': 'login-card',
            '#patient': 'patient-card',
            '#donor': 'donor-card',
            '#applicant': 'applicant-card',
        };

        const targetId = targetMap[hash];
        if (!targetId) return;

        const element = document.getElementById(targetId);
        if (!element) return;

        element.classList.add('focus-target');
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => element.classList.remove('focus-target'), 1800);
    }

    refreshSessionCard();
    useLastEmail();
    focusCardFromHash();
    </script>
</body>
</html>
