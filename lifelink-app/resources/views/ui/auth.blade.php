@php
    $mode = $mode ?? 'login';
    $config = [
        'login' => [
            'title' => 'Login',
            'badge' => 'Existing Account',
            'headline' => 'Sign in and continue into the correct role workspace.',
            'copy' => 'All existing accounts use the same login flow. Registration remains separated by user type so each onboarding path only shows the fields that matter.',
            'form_title' => 'Login for all users',
            'form_copy' => 'Use your email and password to access the correct LifeLink workspace.',
            'steps' => [
                'Sign in with an existing account.',
                'LifeLink reads your active role assignments.',
                'You are redirected into the correct workspace.',
            ],
        ],
        'patient' => [
            'title' => 'Patient Registration',
            'badge' => 'Patient Entry',
            'headline' => 'Create a patient account with a cleaner self-service onboarding flow.',
            'copy' => 'This path is for appointments, medical records, and blood-request access. Only patient-relevant fields stay visible here.',
            'form_title' => 'Register as patient',
            'form_copy' => 'Create a patient account for appointments, records, and blood support requests.',
            'steps' => [
                'Create the account using patient details.',
                'Sign in through the shared login page.',
                'Manage appointments and records from the patient portal.',
            ],
        ],
        'donor' => [
            'title' => 'Donor Registration',
            'badge' => 'Donor Entry',
            'headline' => 'Create a donor account and initialize the donor profile safely.',
            'copy' => 'This path is for donor onboarding, availability, notification response, and future donation history.',
            'form_title' => 'Register as blood donor',
            'form_copy' => 'Create the account and prepare the donor profile for future availability and notifications.',
            'steps' => [
                'Register the account with donor details.',
                'LifeLink initializes the donor profile after account creation.',
                'Return to login and continue into the donor workspace.',
            ],
        ],
        'applicant' => [
            'title' => 'Applicant Registration',
            'badge' => 'Applicant Entry',
            'headline' => 'Create an applicant account and submit the first staffing request.',
            'copy' => 'This path is for doctor, nurse, or IT staffing applications. Department selection remains limited to the roles that actually need it during first submission.',
            'form_title' => 'Register as job applicant',
            'form_copy' => 'Create the account and submit the first application for review.',
            'steps' => [
                'Create an applicant account.',
                'Submit the first role application.',
                'Track status inside the applicant workspace until review is complete.',
            ],
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

@extends('ui.layouts.public')

@section('title', $config['title'])
@section('public_tagline', 'Unified login and registration for the LifeLink healthcare platform')

@section('public_nav')
    <a href="/">Home</a>
    <a href="/#services">Services</a>
    <a href="/#roles">Workspaces</a>
@endsection

@push('scripts')
<script>
const currentMode = @json($mode);
const API = '/api';
const message = document.getElementById('message');
const sessionUser = document.getElementById('session-user');
const sessionRoles = document.getElementById('session-roles');
const advancedCard = document.getElementById('advanced-card');
const applicantRolesWithDepartment = ['Doctor'];
const rolePriority = window.lifeLinkUi?.rolePriority || ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient'];
const roleDestinations = window.lifeLinkUi?.roleDestinations || {
    Admin: '/ui/admin-users',
    ITWorker: '/ui/it-bed-allocation',
    Doctor: '/ui/doctor-dashboard',
    Nurse: '/ui/nurse-dashboard',
    Patient: '/ui/patient-portal',
    Donor: '/ui/donor-dashboard',
    Applicant: '/ui/applications',
};

function showMessage(kind, text) {
    message.className = `ll-auth-message show is-${kind}`;
    message.textContent = text;
}

function call(path, method, body, token = null) {
    const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };
    if (token) headers.Authorization = `Bearer ${token}`;
    return fetch(API + path, { method, headers, body: body ? JSON.stringify(body) : undefined })
        .then(async (response) => {
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
    const targetRole = rolePriority.find((role) => roles.includes(role));
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
        .forEach((key) => localStorage.removeItem(key));
}

function clearStorage() {
    ['ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL', 'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES', 'LAST_USED_EMAIL']
        .forEach((key) => localStorage.removeItem(key));
    refreshSessionCard();
    showMessage('info', 'Stored session cleared.');
    window.lifeLinkUi?.syncPublicChrome?.();
}

function refreshSessionCard() {
    if (!sessionUser || !sessionRoles) return;
    const email = localStorage.getItem('CURRENT_USER_EMAIL');
    const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
    sessionUser.textContent = email || 'No active session';
    sessionRoles.textContent = roles.length ? roles.join(', ') : 'None';
}

function toggleAdvanced() {
    if (advancedCard) {
        advancedCard.classList.toggle('ll-hidden');
    }
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

    call('/public/departments', 'GET').then((result) => {
        const select = document.getElementById('applicantDepartment');
        if (!select || result.status >= 300) return;

        const departments = result.data?.departments || [];
        select.innerHTML = `<option value="">Select department</option>${departments.map((dept) =>
            `<option value="${dept.id}">${dept.dept_name}</option>`
        ).join('')}`;
    });
}

function registerBase(payload) {
    return call('/auth/register', 'POST', payload).then((result) => {
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
    call('/dev/create-admin', 'POST', payload).then((result) => {
        if (result.status >= 200 && result.status < 300 && result.data?.token) {
            persistLoginContext(result.data, payload.email);
            rememberLastEmail(payload.email);
            refreshSessionCard();
            window.lifeLinkUi?.syncPublicChrome?.();
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
    call('/auth/login', 'POST', payload).then((result) => {
        if (result.status >= 200 && result.status < 300 && result.data?.token) {
            persistLoginContext(result.data, payload.email);
            rememberLastEmail(payload.email);
            refreshSessionCard();
            window.lifeLinkUi?.syncPublicChrome?.();
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
    }).catch((error) => showMessage('error', error.message));
}

function registerDonor() {
    const payload = {
        fullName: document.getElementById('donorName').value.trim(),
        email: document.getElementById('donorEmail').value.trim(),
        password: document.getElementById('donorPassword').value.trim(),
        bloodGroup: document.getElementById('donorBloodGroup').value,
    };
    registerBase(payload).then((authData) => {
        return call('/donor/enroll', 'POST', {
            bloodGroup: document.getElementById('donorBloodGroup').value,
            notes: document.getElementById('donorNotes').value.trim(),
        }, authData.token).then((result) => {
            if (!(result.status >= 200 && result.status < 300)) {
                throw new Error(extractMessage(result, 'Donor profile setup failed after account creation.'));
            }
            clearTransientSession();
            showMessage('success', 'Donor account created. Redirecting to login.');
            goToLogin(payload.email, 'donor');
        });
    }).catch((error) => showMessage('error', error.message));
}

function registerApplicant() {
    const payload = {
        fullName: document.getElementById('applicantName').value.trim(),
        email: document.getElementById('applicantEmail').value.trim(),
        password: document.getElementById('applicantPassword').value.trim(),
    };
    registerBase(payload).then((authData) => {
        const body = { appliedRole: document.getElementById('applicantRole').value };
        const departmentRaw = document.getElementById('applicantDepartment').value.trim();
        if (applicantRoleNeedsDepartment() && departmentRaw !== '') body.departmentId = Number(departmentRaw);
        return call('/applications', 'POST', body, authData.token).then((result) => {
            if (!(result.status >= 200 && result.status < 300)) {
                throw new Error(extractMessage(result, 'Application submission failed after account creation.'));
            }
            clearTransientSession();
            showMessage('success', 'Applicant account created. Redirecting to login.');
            goToLogin(payload.email, 'applicant');
        });
    }).catch((error) => showMessage('error', error.message));
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
@endpush

@push('styles')
<style>
    .ll-auth-panel .ll-inline-actions { align-items: center; }
    .ll-auth-fact-grid,
    .ll-auth-support-grid { display: grid; gap: 14px; }
    .ll-auth-fact-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); margin-top: 24px; }
    .ll-auth-support-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top: 18px; }
    .ll-auth-fact,
    .ll-auth-support-card { padding: 16px 18px; border-radius: 20px; border: 1px solid rgba(17, 35, 63, 0.08); background: rgba(255, 255, 255, 0.78); }
    .ll-auth-fact strong,
    .ll-auth-support-card strong { display: block; font-size: 0.98rem; }
    .ll-auth-fact p,
    .ll-auth-support-card p { margin: 8px 0 0; color: var(--ll-text-muted); line-height: 1.65; }
    .ll-auth-form-stack { display: grid; gap: 18px; }
    .ll-auth-steps { display: grid; gap: 12px; margin-top: 22px; }
    .ll-auth-step { display: grid; grid-template-columns: auto 1fr; gap: 12px; align-items: start; padding: 14px; border-radius: 18px; border: 1px solid rgba(17, 35, 63, 0.08); background: rgba(255, 255, 255, 0.76); }
    .ll-auth-step strong { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 12px; color: #fff; background: linear-gradient(135deg, #2b7fff 0%, var(--ll-primary) 75%, var(--ll-success) 100%); }
    .ll-auth-step p { margin: 0; color: var(--ll-text-muted); line-height: 1.6; }
    .ll-auth-switcher a strong { display: block; }
    .ll-auth-switcher a:hover { border-color: rgba(19, 114, 170, 0.18); background: rgba(255, 255, 255, 0.92); }
    .ll-auth-advanced-toggle { width: fit-content; }
    .ll-auth-advanced-card { display: grid; gap: 14px; }
    @media (max-width: 980px) {
        .ll-auth-fact-grid,
        .ll-auth-support-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    <div class="ll-auth-layout">
        <article class="ll-auth-panel ll-auth-panel--accent">
            <span class="ll-public-kicker">{{ $config['badge'] }}</span>
            <h1>{{ $config['headline'] }}</h1>
            <p class="ll-public-lead">{{ $config['copy'] }}</p>

            <div class="ll-auth-fact-grid">
                <div class="ll-auth-fact">
                    <strong>Role-aware routing</strong>
                    <p>Shared authentication still routes each active session into the correct workspace after sign-in.</p>
                </div>
                <div class="ll-auth-fact">
                    <strong>Focused onboarding</strong>
                    <p>Each registration path is smaller and clearer so users only see the fields relevant to them.</p>
                </div>
                <div class="ll-auth-fact">
                    <strong>Backend-safe UI</strong>
                    <p>The interface is modernized while the same login, token, redirect, and role logic stays intact.</p>
                </div>
            </div>

            <div class="ll-auth-switcher">
                @foreach ($otherLinks as $link)
                    <a href="{{ $link['href'] }}">
                        <strong>{{ $link['label'] }}</strong>
                        <span>Open page</span>
                    </a>
                @endforeach
            </div>

            <div class="ll-auth-help" style="margin-top: 24px;">
                <strong>Flow direction</strong>
                <p class="ll-public-copy">Login stays shared for all existing users. Registration is separated so patient, donor, and staffing applicants only see their own onboarding steps.</p>
            </div>

            <div class="ll-auth-steps">
                @foreach ($config['steps'] as $index => $step)
                    <div class="ll-auth-step">
                        <strong>{{ $index + 1 }}</strong>
                        <p>{{ $step }}</p>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="ll-auth-panel">
            <div class="ll-auth-surface" id="authSurface">
                <div>
                    <span class="ll-public-kicker">Secure Entry</span>
                    <h2 style="margin-top: 16px;">{{ $config['form_title'] }}</h2>
                    <p class="ll-public-copy" style="margin-top: 10px;">{{ $config['form_copy'] }}</p>
                </div>

                @if ($mode === 'login')
                    <div class="ll-form-grid">
                        <div class="ll-field">
                            <label class="ll-label" for="loginEmail">Email</label>
                            <input id="loginEmail" class="ll-input" type="email" placeholder="name@example.com">
                        </div>
                        <div class="ll-field">
                            <label class="ll-label" for="loginPassword">Password</label>
                            <input id="loginPassword" class="ll-input" type="password" placeholder="Enter your password">
                        </div>

                        <div class="ll-inline-actions">
                            <button class="ll-button" type="button" onclick="loginUser()">Login</button>
                            <button class="ll-button-ghost" type="button" onclick="useLastEmail()">Use last email</button>
                        </div>
                    </div>

                    <div class="ll-card">
                        <div class="ll-panel-heading">
                            <div>
                                <h3>Current session</h3>
                                <p>Stored browser session details stay visible here without exposing raw token text.</p>
                            </div>
                            <span class="ll-status-chip is-soft">Session</span>
                        </div>

                        <div class="ll-auth-session-grid" style="margin-top: 16px;">
                            <div>
                                <small>User</small>
                                <strong id="session-user">No active session</strong>
                            </div>
                            <div>
                                <small>Roles</small>
                                <strong id="session-roles">None</strong>
                            </div>
                        </div>

                        <div class="ll-inline-actions" style="margin-top: 16px;">
                            <a class="ll-button-ghost" href="/ui/dashboard">Open workspace hub</a>
                            <button class="ll-button-ghost" type="button" onclick="clearStorage()">Clear session</button>
                        </div>
                    </div>

                    <div class="ll-auth-support-grid">
                        <div class="ll-auth-support-card">
                            <strong>Need a new account?</strong>
                            <p>Choose the onboarding flow that matches the person using the system so the form stays smaller and clearer.</p>
                            <div class="ll-inline-actions" style="margin-top: 14px;">
                                <a class="ll-button-ghost" href="/ui/register/patient">Patient</a>
                                <a class="ll-button-ghost" href="/ui/register/donor">Donor</a>
                                <a class="ll-button-ghost" href="/ui/register/applicant">Applicant</a>
                            </div>
                        </div>

                        <div class="ll-auth-support-card">
                            <strong>Session-aware return</strong>
                            <p>When a browser already has a valid session, the product routes the user back into the correct workspace instead of asking them to guess.</p>
                        </div>
                    </div>

                    <button class="ll-button-ghost ll-auth-advanced-toggle" type="button" onclick="toggleAdvanced()">Advanced setup</button>

                    <div id="advanced-card" class="ll-card ll-hidden">
                        <div class="ll-auth-advanced-card">
                            <div class="ll-panel-heading">
                                <div>
                                    <h3>Bootstrap the first admin</h3>
                                    <p>Keep this collapsed during normal use. It only exists for initial environment setup.</p>
                                </div>
                                <span class="ll-status-chip is-warning">Restricted</span>
                            </div>

                            <div class="ll-form-grid">
                                <div class="ll-field">
                                    <label class="ll-label" for="adminName">Admin full name</label>
                                    <input id="adminName" class="ll-input" type="text" placeholder="Enter admin full name">
                                </div>
                                <div class="ll-field">
                                    <label class="ll-label" for="adminEmail">Admin email</label>
                                    <input id="adminEmail" class="ll-input" type="email" placeholder="admin@example.com">
                                </div>
                                <div class="ll-field">
                                    <label class="ll-label" for="adminPassword">Admin password</label>
                                    <input id="adminPassword" class="ll-input" type="password" placeholder="Create a secure password">
                                </div>
                            </div>

                            <div class="ll-inline-actions">
                                <button class="ll-button" type="button" onclick="createAdmin()">Create first admin</button>
                            </div>
                        </div>
                    </div>
                @elseif ($mode === 'patient')
                    <div class="ll-form-grid">
                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="patientName">Full name</label>
                                <input id="patientName" class="ll-input" type="text" placeholder="Enter full name">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="patientEmail">Email</label>
                                <input id="patientEmail" class="ll-input" type="email" placeholder="name@example.com">
                            </div>
                        </div>

                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="patientPassword">Password</label>
                                <input id="patientPassword" class="ll-input" type="password" placeholder="Create a password">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="patientBloodGroup">Blood group</label>
                                <select id="patientBloodGroup" class="ll-select">
                                    <option value="">Prefer not to say</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                        </div>

                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="patientEmergencyName">Emergency contact name</label>
                                <input id="patientEmergencyName" class="ll-input" type="text" placeholder="Optional emergency contact">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="patientEmergencyPhone">Emergency contact phone</label>
                                <input id="patientEmergencyPhone" class="ll-input" type="text" placeholder="Optional contact number">
                            </div>
                        </div>

                        <div class="ll-inline-actions">
                            <button class="ll-button" type="button" onclick="registerPatient()">Create patient account</button>
                            <a class="ll-button-ghost" href="/ui/login">Already have an account?</a>
                        </div>
                    </div>

                    <div class="ll-auth-support-grid">
                        <div class="ll-auth-support-card">
                            <strong>What this unlocks</strong>
                            <p>Patients can manage appointments, medical records, and blood support requests from one portal after login.</p>
                        </div>
                        <div class="ll-auth-support-card">
                            <strong>What happens next</strong>
                            <p>After registration, return to the shared login page and LifeLink routes the session into the patient portal.</p>
                        </div>
                    </div>
                @elseif ($mode === 'donor')
                    <div class="ll-form-grid">
                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="donorName">Full name</label>
                                <input id="donorName" class="ll-input" type="text" placeholder="Enter full name">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="donorEmail">Email</label>
                                <input id="donorEmail" class="ll-input" type="email" placeholder="name@example.com">
                            </div>
                        </div>

                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="donorPassword">Password</label>
                                <input id="donorPassword" class="ll-input" type="password" placeholder="Create a password">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="donorBloodGroup">Blood group</label>
                                <select id="donorBloodGroup" class="ll-select">
                                    <option value="">Select blood group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                        </div>

                        <div class="ll-field">
                            <label class="ll-label" for="donorNotes">Notes</label>
                            <textarea id="donorNotes" class="ll-textarea" placeholder="Optional donor notes"></textarea>
                        </div>

                        <div class="ll-inline-actions">
                            <button class="ll-button" type="button" onclick="registerDonor()">Create donor account</button>
                            <a class="ll-button-ghost" href="/ui/login">Already have an account?</a>
                        </div>
                    </div>

                    <div class="ll-auth-support-grid">
                        <div class="ll-auth-support-card">
                            <strong>Donor onboarding</strong>
                            <p>This path creates the account and initializes donor profile setup for future availability and notifications.</p>
                        </div>
                        <div class="ll-auth-support-card">
                            <strong>After registration</strong>
                            <p>Return to shared login, then continue into the donor workspace for availability, requests, and donation history.</p>
                        </div>
                    </div>
                @else
                    <div class="ll-form-grid">
                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="applicantName">Full name</label>
                                <input id="applicantName" class="ll-input" type="text" placeholder="Enter full name">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="applicantEmail">Email</label>
                                <input id="applicantEmail" class="ll-input" type="email" placeholder="name@example.com">
                            </div>
                        </div>

                        <div class="ll-form-grid-2">
                            <div class="ll-field">
                                <label class="ll-label" for="applicantPassword">Password</label>
                                <input id="applicantPassword" class="ll-input" type="password" placeholder="Create a password">
                            </div>
                            <div class="ll-field">
                                <label class="ll-label" for="applicantRole">Applied role</label>
                                <select id="applicantRole" class="ll-select">
                                    <option value="Doctor">Doctor</option>
                                    <option value="Nurse">Nurse</option>
                                    <option value="ITWorker">IT Worker</option>
                                </select>
                            </div>
                        </div>

                        <div id="applicantDepartmentField" class="ll-field">
                            <label class="ll-label" for="applicantDepartment">Department</label>
                            <select id="applicantDepartment" class="ll-select">
                                <option value="">Select department</option>
                            </select>
                            <p class="ll-helper">Only doctor applicants choose a preferred department during first submission. Nurse and IT assignments are handled after review.</p>
                        </div>

                        <div class="ll-inline-actions">
                            <button class="ll-button" type="button" onclick="registerApplicant()">Create applicant account</button>
                            <a class="ll-button-ghost" href="/ui/login">Already have an account?</a>
                        </div>
                    </div>

                    <div class="ll-auth-support-grid">
                        <div class="ll-auth-support-card">
                            <strong>Application journey</strong>
                            <p>Create the account, submit the initial role request, then return through shared login to track review status.</p>
                        </div>
                        <div class="ll-auth-support-card">
                            <strong>Department rule</strong>
                            <p>Doctor applicants can suggest a department during registration. Other assignments remain an admin decision after review.</p>
                        </div>
                    </div>
                @endif

                <div id="message" class="ll-auth-message"></div>
            </div>
        </article>
    </div>
@endsection
