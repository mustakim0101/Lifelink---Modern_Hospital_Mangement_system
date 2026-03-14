@php
    $mode = $mode ?? 'login';
    $config = [
        'login' => [
            'title' => 'Login',
            'badge' => 'Existing Account',
            'headline' => 'Login and continue into the correct role workspace.',
            'copy' => 'Every existing account uses the same login flow. Registration is split into separate entry pages by user type.',
        ],
        'patient' => [
            'title' => 'Patient Registration',
            'badge' => 'Patient Entry',
            'headline' => 'Create a patient account with patient-related details only.',
            'copy' => 'This path is for patient-side access such as appointments, records, and blood requests.',
        ],
        'donor' => [
            'title' => 'Donor Registration',
            'badge' => 'Donor Entry',
            'headline' => 'Create a donor account and initialize the donor profile.',
            'copy' => 'This path is for blood donor onboarding and donor-specific profile details.',
        ],
        'applicant' => [
            'title' => 'Applicant Registration',
            'badge' => 'Applicant Entry',
            'headline' => 'Create an applicant account and submit the first application.',
            'copy' => 'This path is for hiring flow entry, not for donor or patient profile setup.',
        ],
    ][$mode] ?? null;

    $otherLinks = [
        'login' => [
            ['label' => 'Patient registration', 'href' => '/ui/register/patient'],
            ['label' => 'Donor registration', 'href' => '/ui/register/donor'],
            ['label' => 'Applicant registration', 'href' => '/ui/register/applicant'],
        ],
        'patient' => [
            ['label' => 'Login', 'href' => '/ui/login'],
            ['label' => 'Donor registration', 'href' => '/ui/register/donor'],
            ['label' => 'Applicant registration', 'href' => '/ui/register/applicant'],
        ],
        'donor' => [
            ['label' => 'Login', 'href' => '/ui/login'],
            ['label' => 'Patient registration', 'href' => '/ui/register/patient'],
            ['label' => 'Applicant registration', 'href' => '/ui/register/applicant'],
        ],
        'applicant' => [
            ['label' => 'Login', 'href' => '/ui/login'],
            ['label' => 'Patient registration', 'href' => '/ui/register/patient'],
            ['label' => 'Donor registration', 'href' => '/ui/register/donor'],
        ],
    ][$mode] ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | {{ $config['title'] }}</title>
    <style>
        :root {
            --bg: #eef5f8;
            --surface: rgba(255,255,255,0.88);
            --line: rgba(22,49,67,0.12);
            --text: #163143;
            --muted: #607482;
            --primary: #0f766e;
            --secondary: #1d4ed8;
            --danger: #b91c1c;
            --success: #15803d;
            --warning: #b45309;
            --shadow: 0 24px 60px rgba(17,42,60,0.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29,78,216,0.14), transparent 24rem),
                radial-gradient(circle at right, rgba(15,118,110,0.14), transparent 24rem),
                linear-gradient(180deg, #f8fbfd 0%, var(--bg) 100%);
        }
        a { color: inherit; text-decoration: none; }
        .shell { width: min(1100px, calc(100% - 32px)); margin: 0 auto; padding: 28px 0 40px; }
        .topline, .row, .session-grid { display: grid; gap: 14px; }
        .topline { grid-template-columns: 1fr auto; align-items: center; margin-bottom: 20px; }
        .crumbs { display: flex; gap: 10px; color: var(--muted); font-weight: 600; }
        .mini-link, .path-link, .ghost-link {
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.74);
        }
        .layout { display: grid; grid-template-columns: minmax(300px, 0.9fr) minmax(0, 1.1fr); gap: 20px; }
        .panel {
            border: 1px solid var(--line);
            border-radius: 26px;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 24px;
        }
        .hero {
            background:
                radial-gradient(circle at top left, rgba(249,115,22,0.16), transparent 18rem),
                linear-gradient(160deg, rgba(255,255,255,0.92), rgba(229,245,244,0.84));
        }
        .badge {
            display: inline-flex;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(15,118,110,0.1);
            color: #0b625d;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        h1 {
            margin: 18px 0 12px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(2rem, 4vw, 3.3rem);
            line-height: 1.06;
        }
        h2 { margin: 0 0 8px; font-size: 1.35rem; }
        p { margin: 0; color: var(--muted); line-height: 1.75; }
        .path-list { display: grid; gap: 12px; margin-top: 20px; }
        .path-link { display: flex; justify-content: space-between; align-items: center; font-weight: 700; }
        .path-link span { color: var(--muted); font-size: 0.92rem; }
        .field { display: grid; gap: 8px; margin-top: 14px; }
        .field label { font-weight: 700; font-size: 0.94rem; }
        .field input, .field select, .field textarea {
            width: 100%;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid rgba(22,49,67,0.14);
            background: #fbfdff;
            color: var(--text);
            font: inherit;
        }
        .field textarea { min-height: 90px; resize: vertical; }
        .button-row { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 18px;
            border-radius: 999px;
            border: 0;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }
        .button-primary { color: #fff; background: linear-gradient(135deg, var(--secondary), var(--primary)); }
        .button-secondary { color: var(--text); background: rgba(22,49,67,0.06); }
        .button-warm { color: #fff; background: linear-gradient(135deg, #ea580c, #c2410c); }
        .message, .session-card, .advanced-card, .help-card {
            margin-top: 18px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.72);
        }
        .message { display: none; }
        .message.show { display: block; }
        .message.success { color: var(--success); background: rgba(21,128,61,0.08); border-color: rgba(21,128,61,0.18); }
        .message.error { color: var(--danger); background: rgba(185,28,28,0.08); border-color: rgba(185,28,28,0.18); }
        .message.info { color: var(--warning); background: rgba(180,83,9,0.08); border-color: rgba(180,83,9,0.18); }
        .session-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top: 12px; }
        .session-grid div { padding: 12px; border-radius: 14px; background: rgba(22,49,67,0.05); }
        .session-grid small { display: block; margin-bottom: 6px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; font-size: 0.72rem; font-weight: 800; }
        .advanced-toggle { margin-top: 18px; border: 0; background: none; color: var(--secondary); font-weight: 700; cursor: pointer; padding: 0; }
        .advanced-card { display: none; }
        .advanced-card.show { display: block; }
        .help-card strong { display: block; margin-bottom: 8px; }
        @media (max-width: 920px) {
            .layout, .session-grid, .topline { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="topline">
            <div class="crumbs">
                <a href="/">Public Home</a>
                <span>/</span>
                <span>{{ $config['title'] }}</span>
            </div>
            <a class="mini-link" href="/ui">Prototype Directory</a>
        </div>

        <section class="layout">
            <article class="panel hero">
                <span class="badge">{{ $config['badge'] }}</span>
                <h1>{{ $config['headline'] }}</h1>
                <p>{{ $config['copy'] }}</p>

                <div class="path-list">
                    @foreach ($otherLinks as $link)
                        <a class="path-link" href="{{ $link['href'] }}">
                            <strong>{{ $link['label'] }}</strong>
                            <span>Open page</span>
                        </a>
                    @endforeach
                </div>

                <div class="help-card">
                    <strong>Flow direction</strong>
                    <p>Login remains one shared page. Registration now has separate entry pages so each user type sees only its own form fields.</p>
                </div>
            </article>

            <article class="panel">
                @if ($mode === 'login')
                    <h2>Login for all users</h2>
                    <p>Use your email and password to access the correct role-based workspace.</p>

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
                            <div><small>User</small><strong id="session-user">No active session</strong></div>
                            <div><small>Roles</small><strong id="session-roles">None</strong></div>
                        </div>
                        <div class="button-row">
                            <a class="ghost-link" href="/ui/dashboard">Open auth hub</a>
                            <button class="button button-secondary" type="button" onclick="clearStorage()">Clear session</button>
                        </div>
                    </div>

                    <button class="advanced-toggle" type="button" onclick="toggleAdvanced()">Need bootstrap or setup tools?</button>
                    <div id="advanced-card" class="advanced-card">
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
                @elseif ($mode === 'patient')
                    <h2>Register as patient</h2>
                    <p>Create a patient account for appointments, records, and blood request actions.</p>
                    <div class="field"><label for="patientName">Full name</label><input id="patientName" type="text" value="Patient UI"></div>
                    <div class="field"><label for="patientEmail">Email</label><input id="patientEmail" type="email" value="patient_ui@demo.com"></div>
                    <div class="field"><label for="patientPassword">Password</label><input id="patientPassword" type="password" value="patient12345"></div>
                    <div class="field">
                        <label for="patientBloodGroup">Blood group</label>
                        <select id="patientBloodGroup">
                            <option value="">Prefer not to say</option>
                            <option value="A+">A+</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B-">B-</option>
                            <option value="AB+">AB+</option><option value="AB-">AB-</option>
                            <option value="O+">O+</option><option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="field"><label for="patientEmergencyName">Emergency contact name</label><input id="patientEmergencyName" type="text" placeholder="Optional emergency contact"></div>
                    <div class="field"><label for="patientEmergencyPhone">Emergency contact phone</label><input id="patientEmergencyPhone" type="text" placeholder="Optional contact number"></div>
                    <div class="button-row">
                        <button class="button button-primary" type="button" onclick="registerPatient()">Create patient account</button>
                        <a class="ghost-link" href="/ui/login">Already have an account?</a>
                    </div>
                @elseif ($mode === 'donor')
                    <h2>Register as blood donor</h2>
                    <p>Create the account and initialize the donor profile.</p>
                    <div class="field"><label for="donorName">Full name</label><input id="donorName" type="text" value="Donor UI"></div>
                    <div class="field"><label for="donorEmail">Email</label><input id="donorEmail" type="email" value="donor_ui@demo.com"></div>
                    <div class="field"><label for="donorPassword">Password</label><input id="donorPassword" type="password" value="donor12345"></div>
                    <div class="field">
                        <label for="donorBloodGroup">Blood group</label>
                        <select id="donorBloodGroup">
                            <option value="A+">A+</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B-">B-</option>
                            <option value="AB+">AB+</option><option value="AB-">AB-</option>
                            <option value="O+" selected>O+</option><option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="field"><label for="donorNotes">Notes</label><textarea id="donorNotes" placeholder="Optional donor notes"></textarea></div>
                    <div class="button-row">
                        <button class="button button-primary" type="button" onclick="registerDonor()">Create donor account</button>
                        <a class="ghost-link" href="/ui/login">Already have an account?</a>
                    </div>
                @else
                    <h2>Register as job applicant</h2>
                    <p>Create the account and submit the first job application. Only doctor applicants choose a preferred department here; nurse and IT worker department assignment will be handled by admin review.</p>
                    <div class="field"><label for="applicantName">Full name</label><input id="applicantName" type="text" value="Applicant UI"></div>
                    <div class="field"><label for="applicantEmail">Email</label><input id="applicantEmail" type="email" value="applicant_ui@demo.com"></div>
                    <div class="field"><label for="applicantPassword">Password</label><input id="applicantPassword" type="password" value="applicant12345"></div>
                    <div class="field">
                        <label for="applicantRole">Applied role</label>
                        <select id="applicantRole">
                            <option value="Doctor">Doctor</option>
                            <option value="Nurse">Nurse</option>
                            <option value="ITWorker" selected>IT Worker</option>
                        </select>
                    </div>
                    <div id="applicantDepartmentField" class="field">
                        <label for="applicantDepartment">Department</label>
                        <select id="applicantDepartment">
                            <option value="">Select department</option>
                        </select>
                    </div>
                    <div class="button-row">
                        <button class="button button-primary" type="button" onclick="registerApplicant()">Create applicant account</button>
                        <a class="ghost-link" href="/ui/login">Already have an account?</a>
                    </div>
                @endif

                <div id="message" class="message"></div>
            </article>
        </section>
    </div>

    <script>
    const currentMode = @json($mode);
    const API = '/api';
    const message = document.getElementById('message');
    const sessionUser = document.getElementById('session-user');
    const sessionRoles = document.getElementById('session-roles');
    const advancedCard = document.getElementById('advanced-card');
    const applicantRolesWithDepartment = ['Doctor'];
    const rolePriority = ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient'];
    const roleDestinations = {
        Admin: '/ui/admin-users',
        ITWorker: '/ui/it-bed-allocation',
        Doctor: '/ui/doctor-dashboard',
        Nurse: '/ui/nurse-dashboard',
        Patient: '/ui/patient-portal',
        Donor: '/ui/donor-dashboard',
        Applicant: '/ui/applications',
    };

    function showMessage(kind, text) {
        message.className = `message show ${kind}`;
        message.textContent = text;
    }

    function call(path, method, body, token = null) {
        const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };
        if (token) headers.Authorization = `Bearer ${token}`;
        return fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined })
            .then(async response => {
                const text = await response.text();
                try {
                    return { status: response.status, data: JSON.parse(text) };
                } catch {
                    return { status: response.status, data: text };
                }
            });
    }

    function extractMessage(result, fallback) {
        if (typeof result?.data === 'string') return result.data;
        if (result?.data?.message) return result.data.message;
        if (result?.data?.errors) {
            const firstKey = Object.keys(result.data.errors)[0];
            if (firstKey && Array.isArray(result.data.errors[firstKey])) return result.data.errors[firstKey][0];
        }
        return fallback;
    }

    function getPrimaryDestination(roles) {
        const targetRole = rolePriority.find(role => roles.includes(role));
        return roleDestinations[targetRole] || '/ui/dashboard';
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
        }
    }

    function rememberLastEmail(email) {
        if (email) localStorage.setItem('LAST_USED_EMAIL', email);
    }

    function useLastEmail() {
        const input = document.getElementById('loginEmail');
        if (input) input.value = localStorage.getItem('LAST_USED_EMAIL') || '';
    }

    function clearTransientSession() {
        ['ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'USER_TOKEN', 'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES']
            .forEach(key => localStorage.removeItem(key));
    }

    function clearStorage() {
        ['ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL', 'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES', 'LAST_USED_EMAIL']
            .forEach(key => localStorage.removeItem(key));
        refreshSessionCard();
        showMessage('info', 'Stored session cleared.');
    }

    function refreshSessionCard() {
        if (!sessionUser || !sessionRoles) return;
        const email = localStorage.getItem('CURRENT_USER_EMAIL');
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        sessionUser.textContent = email || 'No active session';
        sessionRoles.textContent = roles.length ? roles.join(', ') : 'None';
    }

    function toggleAdvanced() {
        if (advancedCard) advancedCard.classList.toggle('show');
    }

    function goToLogin(email, source) {
        const params = new URLSearchParams();
        if (email) params.set('email', email);
        if (source) params.set('from', source);
        window.location.href = `/ui/login?${params.toString()}`;
    }

    function applicantRoleNeedsDepartment() {
        const roleInput = document.getElementById('applicantRole');
        return roleInput ? applicantRolesWithDepartment.includes(roleInput.value) : false;
    }

    function toggleApplicantDepartmentField() {
        const field = document.getElementById('applicantDepartmentField');
        const select = document.getElementById('applicantDepartment');
        if (!field || !select) return;

        const isVisible = applicantRoleNeedsDepartment();
        field.style.display = isVisible ? 'grid' : 'none';
        if (!isVisible) {
            select.value = '';
        }
    }

    function loadApplicantDepartments() {
        if (currentMode !== 'applicant') return;

        call('/public/departments', 'GET').then(result => {
            const select = document.getElementById('applicantDepartment');
            if (!select || result.status >= 300) return;

            const departments = result.data?.departments || [];
            select.innerHTML = `<option value="">Select department</option>${departments.map(dept =>
                `<option value="${dept.id}">${dept.dept_name}</option>`
            ).join('')}`;
        });
    }

    function registerBase(payload) {
        return call('/auth/register', 'POST', payload).then(result => {
            if (!(result.status >= 200 && result.status < 300 && result.data?.token)) {
                throw new Error(extractMessage(result, 'Registration failed.'));
            }
            rememberLastEmail(payload.email);
            return result.data;
        });
    }

    function createAdmin() {
        const payload = {
            email: document.getElementById('adminEmail').value.trim(),
            password: document.getElementById('adminPassword').value.trim(),
            fullName: document.getElementById('adminName').value.trim(),
        };
        call('/dev/create-admin', 'POST', payload).then(result => {
            if (result.status >= 200 && result.status < 300 && result.data?.token) {
                persistLoginContext(result.data, payload.email);
                rememberLastEmail(payload.email);
                refreshSessionCard();
                showMessage('success', 'Admin account created. Redirecting to the admin workspace.');
                setTimeout(() => window.location.href = getPrimaryDestination(result.data.user?.roles || []), 700);
                return;
            }
            showMessage('error', extractMessage(result, 'Unable to create admin account.'));
        });
    }

    function loginUser() {
        const payload = {
            email: document.getElementById('loginEmail').value.trim(),
            password: document.getElementById('loginPassword').value.trim(),
        };
        call('/auth/login', 'POST', payload).then(result => {
            if (result.status >= 200 && result.status < 300 && result.data?.token) {
                persistLoginContext(result.data, payload.email);
                rememberLastEmail(payload.email);
                refreshSessionCard();
                showMessage('success', 'Login successful. Redirecting to your workspace.');
                setTimeout(() => window.location.href = getPrimaryDestination(result.data.user?.roles || []), 700);
                return;
            }
            showMessage('error', extractMessage(result, 'Login failed.'));
        });
    }

    function registerPatient() {
        const payload = {
            fullName: document.getElementById('patientName').value.trim(),
            email: document.getElementById('patientEmail').value.trim(),
            password: document.getElementById('patientPassword').value.trim(),
            bloodGroup: document.getElementById('patientBloodGroup').value || undefined,
            emergencyContactName: document.getElementById('patientEmergencyName').value.trim() || undefined,
            emergencyContactPhone: document.getElementById('patientEmergencyPhone').value.trim() || undefined,
        };
        registerBase(payload).then(() => {
            clearTransientSession();
            showMessage('success', 'Patient account created. Redirecting to login.');
            setTimeout(() => goToLogin(payload.email, 'patient'), 700);
        }).catch(error => showMessage('error', error.message));
    }

    function registerDonor() {
        const payload = {
            fullName: document.getElementById('donorName').value.trim(),
            email: document.getElementById('donorEmail').value.trim(),
            password: document.getElementById('donorPassword').value.trim(),
            bloodGroup: document.getElementById('donorBloodGroup').value,
        };
        registerBase(payload).then(authData => {
            return call('/donor/enroll', 'POST', {
                bloodGroup: document.getElementById('donorBloodGroup').value,
                notes: document.getElementById('donorNotes').value.trim(),
            }, authData.token).then(result => {
                if (!(result.status >= 200 && result.status < 300)) {
                    throw new Error(extractMessage(result, 'Donor profile setup failed after account creation.'));
                }
                clearTransientSession();
                showMessage('success', 'Donor account created. Redirecting to login.');
                setTimeout(() => goToLogin(payload.email, 'donor'), 700);
            });
        }).catch(error => showMessage('error', error.message));
    }

    function registerApplicant() {
        const payload = {
            fullName: document.getElementById('applicantName').value.trim(),
            email: document.getElementById('applicantEmail').value.trim(),
            password: document.getElementById('applicantPassword').value.trim(),
        };
        registerBase(payload).then(authData => {
            const body = { appliedRole: document.getElementById('applicantRole').value };
            const departmentRaw = document.getElementById('applicantDepartment').value.trim();
            if (applicantRoleNeedsDepartment() && departmentRaw !== '') body.departmentId = Number(departmentRaw);
            return call('/applications', 'POST', body, authData.token).then(result => {
                if (!(result.status >= 200 && result.status < 300)) {
                    throw new Error(extractMessage(result, 'Application submission failed after account creation.'));
                }
                clearTransientSession();
                showMessage('success', 'Applicant account created. Redirecting to login.');
                setTimeout(() => goToLogin(payload.email, 'applicant'), 700);
            });
        }).catch(error => showMessage('error', error.message));
    }

    function hydrateLoginPage() {
        if (currentMode !== 'login') return;
        const params = new URLSearchParams(window.location.search);
        const email = params.get('email') || localStorage.getItem('LAST_USED_EMAIL') || '';
        const source = params.get('from');
        if (email) document.getElementById('loginEmail').value = email;
        const notes = {
            patient: 'Patient account created. Log in to enter the patient flow.',
            donor: 'Donor account created. Log in to continue to donor tools.',
            applicant: 'Applicant account created. Log in to track application progress.',
        };
        if (source && notes[source]) showMessage('info', notes[source]);
    }

    refreshSessionCard();
    hydrateLoginPage();
    loadApplicantDepartments();
    toggleApplicantDepartmentField();
    const applicantRoleSelect = document.getElementById('applicantRole');
    if (applicantRoleSelect) applicantRoleSelect.addEventListener('change', toggleApplicantDepartmentField);
    </script>
</body>
</html>
