@php
    $mode = $mode ?? 'login';
    $config = [
        'login' => [
            'title' => 'Login',
            'badge' => 'LifeLink Access',
            'headline' => 'Sign in to your workspace.',
            'copy' => 'Use one login for every existing account.',
        ],
        'patient' => [
            'title' => 'Patient Registration',
            'badge' => 'Patient Registration',
            'headline' => 'Create a patient account.',
            'copy' => 'Patient access for appointments, records, and blood requests.',
        ],
        'donor' => [
            'title' => 'Donor Registration',
            'badge' => 'Donor Registration',
            'headline' => 'Create a donor account.',
            'copy' => 'Blood donor onboarding and donor profile setup.',
        ],
        'applicant' => [
            'title' => 'Applicant Registration',
            'badge' => 'Applicant Registration',
            'headline' => 'Create an applicant account.',
            'copy' => 'Hiring flow entry for doctor, nurse, and IT worker applications.',
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
    <link rel="stylesheet" href="/css/ui-system.css">
</head>
<body class="auth-page">
    <div class="shell auth-shell">
        <header class="topbar topbar--public">
            <div class="topbar-inner auth-shell__topbar">
                <div class="brand">
                    <div class="brand-mark">LL</div>
                    <div class="brand-copy">
                        <strong>LifeLink</strong>
                        <span>Secure access and role-based registration.</span>
                    </div>
                </div>
                <nav class="topnav">
                    <a href="/">Public Home</a>
                    <a href="/ui/login">Login</a>
                    <a href="/ui/register/patient">Patient Register</a>
                    <a href="/ui/register/donor">Donor Register</a>
                    <a href="/ui/register/applicant">Applicant Register</a>
                </nav>
            </div>
        </header>

        <div class="topline auth-shell__crumbs">
            <div class="crumbs">
                <a href="/">Public Home</a>
                <span>/</span>
                <span>{{ $config['title'] }}</span>
            </div>
            <a class="mini-link" href="/ui/dashboard">Workspace Hub</a>
        </div>

        <section class="auth-layout">
            <article class="auth-intro">
                <div class="auth-intro__panel">
                    <span class="badge">{{ $config['badge'] }}</span>
                    <h1>{{ $config['headline'] }}</h1>
                    <p>{{ $config['copy'] }}</p>
                    <div class="auth-intro__trust">
                        <article class="auth-trust-card">
                            <strong>Single access layer</strong>
                            <span>One login can route into the correct workspace without changing the current auth behavior.</span>
                        </article>
                        <article class="auth-trust-card">
                            <strong>Dedicated public entry</strong>
                            <span>Patients, donors, and applicants still keep their own registration journeys.</span>
                        </article>
                    </div>
                </div>

                <div class="path-list auth-links">
                    @foreach ($otherLinks as $link)
                        <a class="path-link" href="{{ $link['href'] }}">
                            <strong>{{ $link['label'] }}</strong>
                            <span>Switch</span>
                        </a>
                    @endforeach
                </div>
            </article>

            <article class="auth-card">
                @if ($mode === 'login')
                    <div class="auth-card__header">
                        <span class="hub-label">Secure Login</span>
                        <h2>Sign in</h2>
                        <p>Enter your email and password to continue.</p>
                    </div>

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

                    <div class="session-card auth-session-card">
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

                    <button class="advanced-toggle" type="button" onclick="toggleAdvanced()">Need bootstrap tools?</button>
                    <div id="advanced-card" class="advanced-card">
                        <div class="field">
                            <label for="adminName">Admin full name</label>
                            <input id="adminName" type="text" placeholder="Admin full name">
                        </div>
                        <div class="field">
                            <label for="adminEmail">Admin email</label>
                            <input id="adminEmail" type="email" placeholder="admin@example.com">
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
                    <div class="auth-card__header">
                        <span class="hub-label">Patient Access</span>
                        <h2>Create a patient account</h2>
                        <p>Set up patient access for appointments, records, and blood requests.</p>
                    </div>
                    <div class="field"><label for="patientName">Full name</label><input id="patientName" type="text" placeholder="Full name"></div>
                    <div class="field"><label for="patientEmail">Email</label><input id="patientEmail" type="email" placeholder="patient@example.com"></div>
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
                    <div class="auth-card__header">
                        <span class="hub-label">Donor Access</span>
                        <h2>Create a donor account</h2>
                        <p>Create the account first, then continue with donor profile setup.</p>
                    </div>
                    <div class="field"><label for="donorName">Full name</label><input id="donorName" type="text" placeholder="Full name"></div>
                    <div class="field"><label for="donorEmail">Email</label><input id="donorEmail" type="email" placeholder="donor@example.com"></div>
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
                    <div class="auth-card__header">
                        <span class="hub-label">Applicant Access</span>
                        <h2>Create an applicant account</h2>
                        <p>Create the account, then submit the first job application.</p>
                    </div>
                    <div class="field"><label for="applicantName">Full name</label><input id="applicantName" type="text" placeholder="Full name"></div>
                    <div class="field"><label for="applicantEmail">Email</label><input id="applicantEmail" type="email" placeholder="applicant@example.com"></div>
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
                    <p class="ll-helper-tight">Doctor applicants can choose a preferred department here. Nurse and IT assignments are completed after admin review.</p>
                    <div class="button-row">
                        <button class="button button-primary" type="button" onclick="registerApplicant()">Create applicant account</button>
                        <a class="ghost-link" href="/ui/login">Already have an account?</a>
                    </div>
                @endif

                <div id="message" class="message auth-message"></div>
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
    const TEST_MODE_PASSWORD = '12345678';
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

    function applyTestModePasswords() {
        document.querySelectorAll('input[type="password"]').forEach((input) => {
            input.value = TEST_MODE_PASSWORD;
        });
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
        localStorage.setItem('CURRENT_USER_FULL_NAME', user.fullName || '');
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
        ['ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'USER_TOKEN', 'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES']
            .forEach(key => localStorage.removeItem(key));
    }

    function clearStorage() {
        ['ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL', 'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES', 'LAST_USED_EMAIL']
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
                window.location.href = getPrimaryDestination(result.data.user?.roles || []);
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
                window.location.href = getPrimaryDestination(result.data.user?.roles || []);
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
            goToLogin(payload.email, 'patient');
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
                goToLogin(payload.email, 'donor');
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
                goToLogin(payload.email, 'applicant');
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
    applyTestModePasswords();
    hydrateLoginPage();
    loadApplicantDepartments();
    toggleApplicantDepartmentField();
    const applicantRoleSelect = document.getElementById('applicantRole');
    if (applicantRoleSelect) applicantRoleSelect.addEventListener('change', toggleApplicantDepartmentField);
    </script>
</body>
</html>
