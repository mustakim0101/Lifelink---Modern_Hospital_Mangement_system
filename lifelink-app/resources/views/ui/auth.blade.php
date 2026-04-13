@php
    $mode = $mode ?? 'login';
    $config = [
        'login' => [
            'title' => 'Login',
            'headline' => 'Welcome Back',
            'copy' => 'Sign in to continue.',
        ],
        'patient' => [
            'title' => 'Patient Registration',
            'headline' => 'Patient Registration',
            'copy' => 'Create your account to continue.',
        ],
        'donor' => [
            'title' => 'Donor Registration',
            'headline' => 'Donor Registration',
            'copy' => 'Create your account to continue.',
        ],
        'applicant' => [
            'title' => 'Applicant Registration',
            'headline' => 'Applicant Registration',
            'copy' => 'Create your account, then submit your application.',
        ],
    ][$mode] ?? null;

@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | {{ $config['title'] }}</title>
    <link rel="stylesheet" href="/css/ui-system.css">
</head>
<body class="auth-page auth-page--{{ $mode }}">
    <header class="topbar topbar--public">
        <div class="shell topbar-inner auth-shell__topbar">
                <div class="brand">
                    <div class="brand-mark">LL</div>
                    <div class="brand-copy">
                        <strong>LifeLink</strong>
                    </div>
                </div>
                <nav class="topnav">
                    <a href="/">Home</a>
                    <a class="{{ $mode === 'login' ? 'is-active' : '' }}" href="/ui/login">Login</a>
                    <a class="{{ $mode === 'patient' ? 'is-active' : '' }}" href="/ui/register/patient">Patient Register</a>
                    <a class="{{ $mode === 'donor' ? 'is-active' : '' }}" href="/ui/register/donor">Donor Register</a>
                    <a class="{{ $mode === 'applicant' ? 'is-active' : '' }}" href="/ui/register/applicant">Join Team</a>
                </nav>
        </div>
    </header>

    <div class="shell auth-shell">
        <section class="auth-layout auth-layout--login">
            <article class="auth-card">
                <div class="auth-card__header">
                    <h1>{{ $config['headline'] }}</h1>
                    <p>{{ $config['copy'] }}</p>
                </div>

                @if ($mode === 'login')
                    <div class="field">
                        <label for="loginEmail">Email</label>
                        <input id="loginEmail" type="email" placeholder="">
                    </div>
                    <div class="field field--password">
                        <label for="loginPassword">Password</label>
                        <div class="password-input-wrap">
                            <input id="loginPassword" type="password" placeholder="Enter your password">
                            <button class="password-toggle" type="button" data-password-target="loginPassword" aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                    </div>

                    <div class="button-row auth-login-actions">
                        <button class="button button-primary auth-login-cta" type="button" onclick="loginUser()">Sign In</button>
                    </div>

                    <div class="auth-divider">
                        <span>Don't have an account?</span>
                    </div>

                    <div class="auth-register-stack">
                        <a class="button button-secondary auth-register-btn auth-register-btn--patient" href="/ui/register/patient">Register as Patient</a>
                        <a class="button button-secondary auth-register-btn auth-register-btn--donor" href="/ui/register/donor">Register as Donor</a>
                        <a class="button button-secondary auth-register-btn auth-register-btn--applicant" href="/ui/register/applicant">Apply to Join Staff</a>
                        <a class="button button-secondary auth-home-link" href="/">Back to Home</a>
                    </div>

                    <details class="auth-dev-tools">
                        <summary>Session and bootstrap tools</summary>
                        <div class="session-card auth-session-card">
                            <strong>Current session</strong>
                            <div class="session-grid">
                                <div><small>User</small><strong id="session-user">No active session</strong></div>
                                <div><small>Roles</small><strong id="session-roles">None</strong></div>
                            </div>
                            <div class="button-row">
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
                    </details>
                @elseif ($mode === 'patient')
                    <div class="field"><label for="patientName">Full name</label><input id="patientName" type="text" placeholder="Full name"></div>
                    <div class="field"><label for="patientEmail">Email</label><input id="patientEmail" type="email" placeholder="patient@example.com"></div>
                    <div class="field field--password">
                        <label for="patientPassword">Password</label>
                        <div class="password-input-wrap">
                            <input id="patientPassword" type="password" value="patient12345">
                            <button class="password-toggle" type="button" data-password-target="patientPassword" aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                    </div>
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
                        <button class="button button-primary auth-submit-btn" type="button" onclick="registerPatient()">Create patient account</button>
                        <a class="ghost-link" href="/ui/login">Already have an account?</a>
                    </div>
                @elseif ($mode === 'donor')
                    <div class="field"><label for="donorName">Full name</label><input id="donorName" type="text" placeholder="Full name"></div>
                    <div class="field"><label for="donorEmail">Email</label><input id="donorEmail" type="email" placeholder="donor@example.com"></div>
                    <div class="field field--password">
                        <label for="donorPassword">Password</label>
                        <div class="password-input-wrap">
                            <input id="donorPassword" type="password" value="donor12345">
                            <button class="password-toggle" type="button" data-password-target="donorPassword" aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                    </div>
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
                        <button class="button button-primary auth-submit-btn" type="button" onclick="registerDonor()">Create donor account</button>
                        <a class="ghost-link" href="/ui/login">Already have an account?</a>
                    </div>
                @else
                    <div class="field"><label for="applicantName">Full name</label><input id="applicantName" type="text" placeholder="Full name"></div>
                    <div class="field"><label for="applicantEmail">Email</label><input id="applicantEmail" type="email" placeholder="applicant@example.com"></div>
                    <div class="field field--password">
                        <label for="applicantPassword">Password</label>
                        <div class="password-input-wrap">
                            <input id="applicantPassword" type="password" value="applicant12345">
                            <button class="password-toggle" type="button" data-password-target="applicantPassword" aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                    </div>
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
                        <button class="button button-primary auth-submit-btn" type="button" onclick="registerApplicant()">Create applicant account</button>
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
    const pageQueryParams = new URLSearchParams(window.location.search);
    const applicantRolesWithDepartment = ['Doctor'];
    const TEST_MODE_PASSWORD = '12345678';
    const allowRawServerErrors = @json(app()->environment(['local', 'development']) && config('app.debug')) && pageQueryParams.get('showRawErrors') === '1';
    const internalErrorPatterns = [
        /secret is not set/i,
        /stack trace/i,
        /sqlstate/i,
        /exception/i,
        /error in/i,
        /failed to open stream/i,
    ];
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

    function looksLikeInternalError(text) {
        if (!text) return false;
        const trimmed = String(text).trim();
        if (!trimmed) return false;
        if (trimmed.includes('<!DOCTYPE') || trimmed.includes('<html')) return true;
        return internalErrorPatterns.some(pattern => pattern.test(trimmed));
    }

    function sanitizeServerMessage(rawText, fallback) {
        if (typeof rawText !== 'string') return fallback;
        const trimmed = rawText.trim();
        if (!trimmed) return fallback;
        if (allowRawServerErrors) return trimmed;
        if (looksLikeInternalError(trimmed)) return fallback;
        return trimmed;
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
        if (typeof result?.data === 'string') return sanitizeServerMessage(result.data, fallback);
        if (result?.data?.message) return sanitizeServerMessage(result.data.message, fallback);
        if (result?.data?.errors) {
            const firstKey = Object.keys(result.data.errors)[0];
            if (firstKey && Array.isArray(result.data.errors[firstKey])) {
                return sanitizeServerMessage(result.data.errors[firstKey][0], fallback);
            }
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
        const email = pageQueryParams.get('email') || localStorage.getItem('LAST_USED_EMAIL') || '';
        const source = pageQueryParams.get('from');
        if (email) document.getElementById('loginEmail').value = email;
        const notes = {
            patient: 'Patient account created. Log in to enter the patient flow.',
            donor: 'Donor account created. Log in to continue to donor tools.',
            applicant: 'Applicant account created. Log in to track application progress.',
        };
        if (source && notes[source]) showMessage('info', notes[source]);
    }

    function setupPasswordToggles() {
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            const targetId = toggle.dataset.passwordTarget;
            const targetInput = targetId ? document.getElementById(targetId) : null;
            if (!targetInput) return;

            toggle.addEventListener('click', () => {
                const showPassword = targetInput.type === 'password';
                targetInput.type = showPassword ? 'text' : 'password';
                toggle.textContent = showPassword ? 'Hide' : 'Show';
                toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
                toggle.setAttribute('aria-pressed', String(showPassword));
            });
        });
    }

    refreshSessionCard();
    applyTestModePasswords();
    hydrateLoginPage();
    setupPasswordToggles();
    loadApplicantDepartments();
    toggleApplicantDepartmentField();
    const applicantRoleSelect = document.getElementById('applicantRole');
    if (applicantRoleSelect) applicantRoleSelect.addEventListener('change', toggleApplicantDepartmentField);
    </script>
</body>
</html>
