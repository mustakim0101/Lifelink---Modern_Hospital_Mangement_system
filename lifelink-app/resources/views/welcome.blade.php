<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Modern Hospital Management</title>
    <link rel="stylesheet" href="/css/ui-system.css">
</head>
<body class="welcome-page">
    <header class="topbar topbar--public">
        <div class="shell topbar-inner">
            <div class="brand">
                <div class="brand-mark">LL</div>
                <div class="brand-copy">
                    <strong>LifeLink</strong>
                    <span>Modern hospital operations, patient access, and blood response.</span>
                </div>
            </div>

            <nav class="topnav">
                <a href="#overview">Overview</a>
                <a href="#find-department">Department Finder</a>
                <a href="#modules">Platform</a>
                <a href="#entry">Entry</a>
                <a id="auth-nav-link" href="/ui/login">Login / Register</a>
                <a id="session-nav-link" class="cta" href="/ui">Workspace</a>
            </nav>
        </div>
    </header>

    <div class="shell">
        <main class="welcome-main">
            <section class="welcome-hero" id="overview">
                <article class="welcome-hero__copy">
                    <span class="badge">Healthcare SaaS Platform</span>
                    <h1>Modern Hospital Management <span class="welcome-hero__title-accent">Made Simple</span></h1>
                    <p>Streamline operations, improve patient care, and keep Blood Bank response connected through one role-aware LifeLink workspace.</p>
                    <div class="hero-actions">
                        <a class="button primary" href="/ui/register/patient">Register as Patient</a>
                        <a class="button" href="/ui/register/donor">Register as Donor</a>
                        <a class="button" href="/ui/register/applicant">Join Our Team</a>
                    </div>
                </article>
                <aside class="welcome-hero__aside">
                    <article class="welcome-highlight">
                        <span class="welcome-highlight__label">Unified Hospital System</span>
                        <strong>From public entry to role dashboards, every workflow stays in one trusted platform.</strong>
                        <p>LifeLink keeps admissions, clinical coordination, donor support, and operations aligned.</p>
                    </article>
                    <div class="welcome-metrics">
                        <article class="welcome-metric">
                            <strong>7</strong>
                            <span>Role workspaces</span>
                        </article>
                        <article class="welcome-metric">
                            <strong>24/7</strong>
                            <span>Operational readiness</span>
                        </article>
                        <article class="welcome-metric">
                            <strong>1</strong>
                            <span>Connected platform</span>
                        </article>
                    </div>
                </aside>
            </section>

            <section id="modules" class="welcome-solutions">
                <div class="welcome-solutions__head">
                    <h2>Comprehensive Healthcare Solutions</h2>
                </div>
                <div class="welcome-module-grid">
                    <article class="card welcome-module-card">
                        <span class="hub-label">Admin</span>
                        <h3>Admin Control Center</h3>
                        <p>Complete oversight of operations, staffing, and application reviews.</p>
                    </article>
                    <article class="card welcome-module-card">
                        <span class="hub-label">Patient</span>
                        <h3>Patient Care</h3>
                        <p>Appointments, records, and requests are managed in one secure flow.</p>
                    </article>
                    <article class="card welcome-module-card">
                        <span class="hub-label">IT</span>
                        <h3>IT Operations</h3>
                        <p>Admissions, bed allocation, and ward operations stay coordinated.</p>
                    </article>
                    <article class="card welcome-module-card">
                        <span class="hub-label">Doctor</span>
                        <h3>Doctor Dashboard</h3>
                        <p>Daily appointments, active patients, and requests in one workspace.</p>
                    </article>
                    <article class="card welcome-module-card">
                        <span class="hub-label">Nurse</span>
                        <h3>Nurse Workspace</h3>
                        <p>Patient monitoring, vitals tracking, and Blood Bank screening tasks.</p>
                    </article>
                    <article class="card welcome-module-card">
                        <span class="hub-label">Blood Bank</span>
                        <h3>Blood Operations</h3>
                        <p>Donor matching, approval, fulfillment, and donation logging.</p>
                    </article>
                </div>
            </section>

            <section class="finder-section" id="find-department" aria-labelledby="finder-title">
                <div class="finder-header">
                    <span class="badge">Department Finder</span>
                    <h2 id="finder-title">Find the right department</h2>
                    <p>Use the anatomy guide to surface the most relevant department path before you continue into LifeLink.</p>
                </div>

                <div class="finder-shell">
                    <article class="anatomy-card">
                        <div class="anatomy-stage">
                            <div class="anatomy-figure">
                                <img src="/assets/anatomy/51152.jpg" alt="Human anatomy illustration with interactive body regions for department guidance.">
                                <button class="finder-hotspot" type="button" data-region="eyes" style="--top: 19.4%; --left: 22.9%; --width: 9.8%; --height: 4.9%;" aria-label="Eyes" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Eyes</span>
                                </button>
                                <button class="finder-hotspot is-active" type="button" data-region="brain" style="--top: 15.9%; --left: 22.9%; --width: 10.6%; --height: 6.4%;" aria-label="Brain" aria-pressed="true" aria-controls="finder-panel">
                                    <span class="sr-only">Brain</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="left-lung" style="--top: 31.2%; --left: 19.2%; --width: 9.4%; --height: 11.8%;" aria-label="Left lung" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Left lung</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="right-lung" style="--top: 31.2%; --left: 27.1%; --width: 9.4%; --height: 11.8%;" aria-label="Right lung" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Right lung</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="heart" style="--top: 34.8%; --left: 23.2%; --width: 6.3%; --height: 7.2%;" aria-label="Heart" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Heart</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="liver" style="--top: 42.6%; --left: 19.2%; --width: 12.6%; --height: 8.2%;" aria-label="Liver" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Liver</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="stomach" style="--top: 39.6%; --left: 26.2%; --width: 7.9%; --height: 7.2%;" aria-label="Stomach" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Stomach</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="left-kidney" style="--top: 45.8%; --left: 21.9%; --width: 3.6%; --height: 4.3%;" aria-label="Left kidney" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Left kidney</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="right-kidney" style="--top: 45.1%; --left: 25.7%; --width: 4.1%; --height: 4.8%;" aria-label="Right kidney" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Right kidney</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="intestines" style="--top: 50.9%; --left: 23.1%; --width: 11.1%; --height: 6.8%;" aria-label="Intestines" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Intestines</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="bladder" style="--top: 56.6%; --left: 23.1%; --width: 5.3%; --height: 3.7%;" aria-label="Bladder" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Bladder</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="hands-arms" style="--top: 45.2%; --left: 35.8%; --width: 7.2%; --height: 22.8%;" aria-label="Hands and arms" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Hands and arms</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="legs" style="--top: 80.8%; --left: 23.1%; --width: 19.6%; --height: 28.5%;" aria-label="Legs" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Legs</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="bones-joints" style="--top: 45.1%; --left: 9.6%; --width: 6.9%; --height: 21.6%;" aria-label="Bones and joints" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Bones and joints</span>
                                </button>
                            </div>
                        </div>

                        <div class="finder-support">
                            <span class="finder-support-copy">Need a non-organ path?</span>
                            <button class="finder-support-button" type="button" data-region="child-care" aria-pressed="false" aria-controls="finder-panel">
                                Child care
                            </button>
                            <button class="finder-support-button" type="button" data-region="blood-donation" aria-pressed="false" aria-controls="finder-panel">
                                Blood and donation
                            </button>
                        </div>
                    </article>

                    <aside id="finder-panel" class="finder-panel" aria-live="polite">
                        <div class="finder-panel-top">
                            <span class="finder-kicker">Recommended match</span>
                            <h3 id="finder-region-title">Brain</h3>
                            <p id="finder-region-description">Neurology is the best fit for symptoms centered around the brain, nerves, memory, balance, or severe headaches.</p>
                        </div>

                        <div class="finder-departments">
                            <strong>Related departments</strong>
                            <div id="finder-region-tags" class="finder-tags"></div>
                        </div>

                        <div class="finder-note">
                            <p id="finder-region-note">Hover or tap the anatomy to preview the most relevant department path, then continue into LifeLink.</p>
                        </div>

                        <div class="hero-actions">
                            <a class="button primary" href="/ui/login">Continue to Login</a>
                            <a class="button" href="#entry">See account entry</a>
                        </div>
                    </aside>
                </div>
            </section>

            <section id="entry" class="welcome-entry-grid">
                <div id="logged-out-entry" class="card stack welcome-entry-card">
                    <span class="hub-label">Account Entry</span>
                    <h2>Choose your entry path.</h2>
                    <p>Sign in to continue, or create the right account for patient, donor, or applicant access.</p>
                    <div class="hero-actions">
                        <a class="button primary" href="/ui/login">Login</a>
                        <a class="button" href="/ui/register/patient">Patient Register</a>
                        <a class="button" href="/ui/register/donor">Donor Register</a>
                        <a class="button" href="/ui/register/applicant">Join Our Team</a>
                    </div>
                </div>

                <div id="logged-in-entry" class="card stack welcome-entry-card page-hidden">
                    <span class="hub-label">Active Session</span>
                    <h2>Continue into your workspace.</h2>
                    <p>A session is already available in this browser. Move forward or sign out before switching accounts.</p>
                    <div class="hero-actions">
                        <a id="logged-dashboard-link" class="button primary" href="/ui/dashboard">Go to dashboard</a>
                        <button id="logout-button" class="button" type="button">Logout</button>
                    </div>
                </div>

                <div class="card stack welcome-entry-card welcome-entry-card--support">
                    <span class="hub-label">Session Access</span>
                    <h2>Already have an account?</h2>
                    <p>Use login for direct workspace access, or use department finder first if you need care guidance.</p>
                    <div class="hero-actions">
                        <a class="button primary" href="/ui/login">Open Login</a>
                        <a class="button" href="#find-department">Open anatomy guide</a>
                    </div>
                </div>
            </section>

            <section class="welcome-impact" aria-label="LifeLink highlights">
                <article class="welcome-impact__item">
                    <strong>10K+</strong>
                    <span>Patients Served</span>
                </article>
                <article class="welcome-impact__item">
                    <strong>500+</strong>
                    <span>Medical Staff</span>
                </article>
                <article class="welcome-impact__item">
                    <strong>1,000+</strong>
                    <span>Active Donors</span>
                </article>
                <article class="welcome-impact__item welcome-impact__item--danger">
                    <strong>99.9%</strong>
                    <span>Uptime</span>
                </article>
            </section>

            <footer class="welcome-footer">
                <strong>LifeLink</strong>
                <p>&copy; 2026 LifeLink Hospital Management System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('USER_TOKEN') || '';
    const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');

    const authNavLink = document.getElementById('auth-nav-link');
    const sessionNavLink = document.getElementById('session-nav-link');
    const loggedDashboardLink = document.getElementById('logged-dashboard-link');
    const loggedOutEntry = document.getElementById('logged-out-entry');
    const loggedInEntry = document.getElementById('logged-in-entry');
    const logoutButton = document.getElementById('logout-button');
    const rolePriority = ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient'];
    const finderHotspots = Array.from(document.querySelectorAll('.finder-hotspot'));
    const finderSupportButtons = Array.from(document.querySelectorAll('.finder-support-button'));
    const finderRegionTitle = document.getElementById('finder-region-title');
    const finderRegionDescription = document.getElementById('finder-region-description');
    const finderRegionTags = document.getElementById('finder-region-tags');
    const finderRegionNote = document.getElementById('finder-region-note');
    const roleDestinations = {
        Admin: '/ui/admin-users',
        ITWorker: '/ui/it-bed-allocation',
        Doctor: '/ui/doctor-dashboard',
        Nurse: '/ui/nurse-dashboard',
        Donor: '/ui/donor-dashboard',
        Applicant: '/ui/applications',
        Patient: '/ui/patient-portal'
    };
    const finderRegions = {
        eyes: {
            name: 'Eyes',
            departments: ['Neurology'],
            description: 'Visual changes, eye-related nerve symptoms, and neurological concerns affecting sight can begin with a neurology-led review.',
            note: 'This route helps surface vision symptoms that may connect to broader nerve or brain-related evaluation.'
        },
        brain: {
            name: 'Brain',
            departments: ['Neurology'],
            description: 'Neurology is the best fit for symptoms centered around the brain, nerves, memory, balance, or severe headaches.',
            note: 'A strong first route for neurological review, imaging decisions, and specialist follow-up inside the hospital workflow.'
        },
        'left-lung': {
            name: 'Left Lung',
            departments: ['General Medicine'],
            description: 'Breathing discomfort, coughing, congestion, or chest symptoms affecting the left lung can begin with general medicine triage.',
            note: 'A useful entry point for respiratory review, baseline assessment, and referral into the next care path.'
        },
        'right-lung': {
            name: 'Right Lung',
            departments: ['General Medicine'],
            description: 'Breathing discomfort, coughing, congestion, or chest symptoms affecting the right lung can begin with general medicine triage.',
            note: 'This keeps respiratory symptoms moving through a clear internal medicine workflow before any specialty escalation.'
        },
        heart: {
            name: 'Heart',
            departments: ['Cardiology'],
            description: 'Cardiology is the right match for chest pressure, palpitations, circulation concerns, or ongoing heart-health monitoring.',
            note: 'This path supports focused cardiac assessment, monitoring, and escalation into specialist-led treatment.'
        },
        liver: {
            name: 'Liver',
            departments: ['General Medicine'],
            description: 'General medicine is a practical first stop for liver-area discomfort, fatigue, metabolism concerns, or abnormal clinical findings.',
            note: 'It helps patients start with broad assessment before being routed onward for more specialized digestive care if needed.'
        },
        stomach: {
            name: 'Stomach',
            departments: ['General Medicine'],
            description: 'Nausea, upper abdominal discomfort, indigestion, or appetite changes can begin with a general medicine workup.',
            note: 'This route supports symptom review, initial treatment, and referral when stomach-related issues need deeper investigation.'
        },
        'left-kidney': {
            name: 'Left Kidney',
            departments: ['General Medicine'],
            description: 'Left-sided flank pain, swelling, hydration issues, or suspected kidney-related symptoms can be triaged through general medicine.',
            note: 'A balanced entry point for tests, acute symptom review, and internal medicine coordination for kidney-related concerns.'
        },
        'right-kidney': {
            name: 'Right Kidney',
            departments: ['General Medicine'],
            description: 'Right-sided flank pain, swelling, hydration issues, or suspected kidney-related symptoms can be triaged through general medicine.',
            note: 'A balanced entry point for tests, acute symptom review, and internal medicine coordination for kidney-related concerns.'
        },
        intestines: {
            name: 'Intestines',
            departments: ['General Medicine'],
            description: 'Bowel discomfort, cramping, digestive irregularity, or lower abdominal symptoms are well suited to general medicine triage.',
            note: 'This path helps turn broad digestive concerns into a clear next step with examination and follow-up planning.'
        },
        bladder: {
            name: 'Bladder',
            departments: ['General Medicine'],
            description: 'Urinary discomfort, lower pelvic pressure, or fluid-balance concerns can start with general medicine support.',
            note: 'Helpful for early assessment, testing coordination, and routing into the right treatment pathway.'
        },
        'hands-arms': {
            name: 'Hands and Arms',
            departments: ['Orthopedics'],
            description: 'Hand injuries, arm pain, joint strain, or limited upper-limb movement align well with orthopedic evaluation.',
            note: 'This route is useful for musculoskeletal issues affecting the shoulders, arms, wrists, or hands.'
        },
        legs: {
            name: 'Legs',
            departments: ['Orthopedics'],
            description: 'Leg pain, sports injuries, gait problems, fractures, or weight-bearing discomfort fit the orthopedic pathway.',
            note: 'A strong first stop for lower-limb injuries, stability concerns, and rehab-oriented treatment planning.'
        },
        'bones-joints': {
            name: 'Bones and Joints',
            departments: ['Orthopedics'],
            description: 'Bone pain, joint instability, posture issues, or broader skeletal discomfort align best with orthopedics.',
            note: 'Use this when symptoms feel structural or joint-based rather than isolated to the arms or legs alone.'
        },
        'child-care': {
            name: 'Child Care',
            departments: ['Pediatrics'],
            description: 'Pediatrics stays available for child wellness, fever monitoring, growth concerns, and age-specific clinical attention.',
            note: 'This gives families a clear path into child-focused care without treating pediatrics like a body-organ overlay.'
        },
        'blood-donation': {
            name: 'Blood and Donation',
            departments: ['Blood Bank'],
            description: 'Blood requests, donor coordination, and transfusion support should flow through the blood bank pathway.',
            note: 'Best for donation readiness, blood availability checks, and urgent blood-support coordination in LifeLink.'
        }
    };

    function renderFinderRegion(regionKey) {
        const region = finderRegions[regionKey];
        if (!region || !finderRegionTitle || !finderRegionDescription || !finderRegionTags || !finderRegionNote) return;

        finderRegionTitle.textContent = region.name;
        finderRegionDescription.textContent = region.description;
        finderRegionNote.textContent = region.note;
        finderRegionTags.innerHTML = region.departments.map(department => `<span>${department}</span>`).join('');

        finderHotspots.forEach(button => {
            const isActive = button.dataset.region === regionKey;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });

        finderSupportButtons.forEach(button => {
            const isActive = button.dataset.region === regionKey;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });
    }

    function preferredRolePath() {
        const preferredRole = rolePriority.find(role => roles.includes(role));
        return preferredRole ? roleDestinations[preferredRole] : '/ui/dashboard';
    }

    finderHotspots.forEach(button => {
        const activate = () => renderFinderRegion(button.dataset.region);
        button.addEventListener('mouseenter', activate);
        button.addEventListener('focus', activate);
        button.addEventListener('click', activate);
    });

    finderSupportButtons.forEach(button => {
        const activate = () => renderFinderRegion(button.dataset.region);
        button.addEventListener('focus', activate);
        button.addEventListener('click', activate);
    });

    renderFinderRegion('brain');

    if (token && roles.length) {
        const dashboardPath = preferredRolePath();
        if (authNavLink) {
            authNavLink.textContent = 'Go to Dashboard';
            authNavLink.href = dashboardPath;
        }
        if (loggedDashboardLink) loggedDashboardLink.href = dashboardPath;

        if (sessionNavLink) {
            sessionNavLink.textContent = 'Logout';
            sessionNavLink.href = '#';
        }

        if (loggedOutEntry) loggedOutEntry.classList.add('page-hidden');
        if (loggedInEntry) loggedInEntry.classList.remove('page-hidden');
    }

    function clearSession() {
        [
            'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
            'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
            'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
        ].forEach(key => localStorage.removeItem(key));
    }

    if (logoutButton) {
        logoutButton.addEventListener('click', () => {
            clearSession();
            window.location.reload();
        });
    }

    if (sessionNavLink) {
        sessionNavLink.addEventListener('click', event => {
            if (!(token && roles.length)) return;
            event.preventDefault();
            clearSession();
            window.location.reload();
        });
    }
    </script>
</body>
</html>
