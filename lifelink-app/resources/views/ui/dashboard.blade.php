<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Authenticated Dashboard</title>
    <style>
        :root {
            --bg: #edf4f7;
            --surface: rgba(255, 255, 255, 0.84);
            --line: rgba(22, 49, 67, 0.12);
            --text: #153143;
            --muted: #5d7481;
            --primary: #0f766e;
            --secondary: #1d4ed8;
            --danger: #b91c1c;
            --shadow: 0 28px 70px rgba(16, 42, 60, 0.12);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.14), transparent 24rem),
                radial-gradient(circle at right, rgba(15, 118, 110, 0.14), transparent 24rem),
                linear-gradient(180deg, #f8fbfd 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 42px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .mark {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            box-shadow: 0 16px 28px rgba(29, 78, 216, 0.22);
        }

        .brand strong {
            display: block;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.14rem;
        }

        .brand span {
            color: var(--muted);
            font-size: 0.93rem;
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chip,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.72);
            font-weight: 700;
            cursor: pointer;
        }

        .button-primary {
            color: #fff;
            border: 0;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 20px;
            margin-top: 10px;
        }

        .panel,
        .card,
        .action-card {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--surface);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .hero-copy {
            padding: 34px;
            background:
                radial-gradient(circle at top left, rgba(249, 115, 22, 0.18), transparent 20rem),
                linear-gradient(160deg, rgba(255, 255, 255, 0.9), rgba(229, 245, 244, 0.82));
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.1);
            color: #0c615d;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .hero-copy h1 {
            margin: 18px 0 12px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(2rem, 3vw, 3.6rem);
            line-height: 1.05;
            max-width: 12ch;
        }

        .hero-copy p {
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
        }

        .hero-meta {
            display: grid;
            gap: 14px;
            margin-top: 24px;
        }

        .card {
            padding: 18px;
        }

        .card small {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .card strong {
            display: block;
            font-size: 1.1rem;
        }

        .hero-side {
            display: grid;
            gap: 18px;
        }

        .primary-card {
            padding: 24px;
        }

        .primary-card h2 {
            margin: 0 0 12px;
            font-size: 1.3rem;
        }

        .primary-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .main-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 24px;
        }

        .action-card {
            padding: 22px;
        }

        .action-card h3 {
            margin: 0 0 10px;
            font-size: 1.16rem;
        }

        .action-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .action-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .action-links a {
            display: inline-flex;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(22, 49, 67, 0.06);
            font-weight: 700;
        }

        .action-links a.primary {
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .hidden { display: none; }

        .notice {
            margin-top: 18px;
            padding: 18px;
            border-radius: 20px;
            border: 1px solid rgba(185, 28, 28, 0.18);
            color: var(--danger);
            background: rgba(185, 28, 28, 0.08);
        }

        @media (max-width: 960px) {
            .hero, .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 24px, 1180px); }
            .topbar { flex-direction: column; align-items: flex-start; }
            .hero-copy, .primary-card, .action-card, .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <div class="mark">LL</div>
                <div>
                    <strong>LifeLink Dashboard</strong>
                    <span>Authenticated mode with role-aware navigation.</span>
                </div>
            </div>

            <div class="top-actions">
                <a class="chip" href="/">Public Home</a>
                <a class="chip" href="/ui">Prototype Directory</a>
                <button class="button" type="button" onclick="logoutSession()">Logout</button>
            </div>
        </header>

        <section class="hero">
            <article class="panel hero-copy">
                <span class="eyebrow">Authenticated Mode</span>
                <h1 id="welcome-line">Organized access for your role.</h1>
                <p id="welcome-copy">This dashboard reads the current session and highlights the workflow areas that matter most for the signed-in user.</p>

                <div class="hero-meta">
                    <div class="card">
                        <small>Signed in as</small>
                        <strong id="user-email">No active session</strong>
                    </div>
                    <div class="card">
                        <small>Detected roles</small>
                        <strong id="role-list">None</strong>
                    </div>
                </div>
            </article>

            <aside class="hero-side">
                <div class="panel primary-card">
                    <h2 id="primary-title">Primary destination</h2>
                    <p id="primary-copy">Sign in through the login page first to unlock the correct workflow area.</p>
                    <div class="action-links">
                        <a id="primary-link" class="primary" href="/ui/login">Open login page</a>
                    </div>
                </div>

                <div id="admin-tools-card" class="panel primary-card hidden">
                    <h2>Advanced admin and IT tools</h2>
                    <p>Verification panels are separated from the normal user flow. Use the advanced tools page only when you need deeper inspection.</p>
                    <div class="action-links">
                        <a class="primary" href="/ui/dev-tools">Open advanced tools</a>
                    </div>
                </div>
            </aside>
        </section>

        <section id="action-grid" class="main-grid"></section>

        <div id="session-warning" class="notice hidden">
            No valid local session was found. Return to the login page and log in again before using the authenticated mode.
        </div>
    </div>

    <script>
    const BLOOD_BANK_DEPARTMENT = 'Blood Bank';

    const roleConfig = {
        Admin: {
            label: 'Administrator',
            primaryLabel: 'Admin control center',
            primaryHref: '/ui/admin-users',
            primaryCopy: 'Admins can manage user account status, review operational pages, and access advanced verification tools.',
            cards: [
                { title: 'User account control', href: '/ui/admin-users', desc: 'Freeze, unfreeze, and inspect user account state.' },
                { title: 'Application reviews', href: '/ui/application-reviews', desc: 'Approve or reject role and staffing applications.' },
                { title: 'Advanced tools', href: '/ui/dev-tools', desc: 'Open controlled raw verification and session inspection tools.' }
            ]
        },
        ITWorker: {
            label: 'IT Worker',
            primaryLabel: 'Operations dashboard',
            primaryHref: '/ui/it-bed-allocation',
            primaryCopy: 'IT Workers coordinate admissions, bed assignment, and blood matching within their allowed departments.',
            cards: [
                { title: 'Ward setup', href: '/ui/ward-setup', desc: 'Configure care units and bed structures.' },
                { title: 'Bed allocation', href: '/ui/it-bed-allocation', desc: 'Manage admissions and assign available beds.' },
                { title: 'Advanced tools', href: '/ui/dev-tools', desc: 'Open controlled diagnostics for technical verification.' }
            ],
            bloodBankCards: [
                { title: 'Blood matching', href: '/ui/blood-matching', desc: 'Review blood requests, notify donors, and record Blood Bank donation workflows.' }
            ]
        },
        Doctor: {
            label: 'Doctor',
            primaryLabel: 'Doctor dashboard',
            primaryHref: '/ui/doctor-dashboard',
            primaryCopy: 'Doctors can review patients, appointments, and submit bed-related care requests.',
            cards: [
                { title: 'Doctor dashboard', href: '/ui/doctor-dashboard', desc: 'Manage doctor-facing patient, appointment, and request actions.' }
            ]
        },
        Nurse: {
            label: 'Nurse',
            primaryLabel: 'Nurse dashboard',
            primaryHref: '/ui/nurse-dashboard',
            primaryCopy: 'Nurses focus on admissions, monitoring, and patient vital sign workflows.',
            cards: [
                { title: 'Nurse dashboard', href: '/ui/nurse-dashboard', desc: 'Monitor patients, admissions, and vital sign updates.' }
            ]
        },
        Patient: {
            label: 'Patient',
            primaryLabel: 'Patient portal',
            primaryHref: '/ui/patient-portal',
            primaryCopy: 'Patients can review records, appointments, and blood request activity from one place.',
            cards: [
                { title: 'Patient portal', href: '/ui/patient-portal', desc: 'View appointments, medical records, and blood requests.' }
            ]
        },
        Donor: {
            label: 'Donor',
            primaryLabel: 'Donor dashboard',
            primaryHref: '/ui/donor-dashboard',
            primaryCopy: 'Donors can update availability, health data, donation history, and notification response activity.',
            cards: [
                { title: 'Donor dashboard', href: '/ui/donor-dashboard', desc: 'Manage profile, availability, health checks, donations, and notifications.' }
            ]
        },
        Applicant: {
            label: 'Applicant',
            primaryLabel: 'Application flow',
            primaryHref: '/ui/applications',
            primaryCopy: 'Applicants can submit and follow the status of role applications.',
            cards: [
                { title: 'Applications', href: '/ui/applications', desc: 'Submit and track job application progress.' }
            ]
        }
    };

    const fullName = localStorage.getItem('CURRENT_USER_FULL_NAME') || '';
    const userId = localStorage.getItem('CURRENT_USER_ID') || '';
    const email = localStorage.getItem('CURRENT_USER_EMAIL') || '';
    const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
    const token = localStorage.getItem('USER_TOKEN') || '';

    const userEmail = document.getElementById('user-email');
    const roleList = document.getElementById('role-list');
    const welcomeLine = document.getElementById('welcome-line');
    const welcomeCopy = document.getElementById('welcome-copy');
    const primaryTitle = document.getElementById('primary-title');
    const primaryCopy = document.getElementById('primary-copy');
    const primaryLink = document.getElementById('primary-link');
    const actionGrid = document.getElementById('action-grid');
    const warning = document.getElementById('session-warning');
    const adminToolsCard = document.getElementById('admin-tools-card');

    async function api(path) {
        const response = await fetch(`/api${path}`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`
            }
        });

        const text = await response.text();
        let data = {};
        try { data = JSON.parse(text); } catch {}
        return { status: response.status, data };
    }

    async function hasBloodBankItAccess() {
        if (roles.includes('Admin')) {
            return true;
        }

        if (!roles.includes('ITWorker') || !token) {
            return false;
        }

        const result = await api('/ward/it/departments');
        if (result.status >= 300) {
            return false;
        }

        const departments = Array.isArray(result.data?.departments) ? result.data.departments : [];
        return departments.some(department => department?.dept_name === BLOOD_BANK_DEPARTMENT);
    }

    if (!token || !roles.length) {
        warning.classList.remove('hidden');
        userEmail.textContent = 'No active session';
        roleList.textContent = 'None';
    } else {
        initializeDashboard();
    }

    async function initializeDashboard() {
        const summaryParts = [];
        if (fullName) summaryParts.push(fullName);
        if (userId) summaryParts.push(`#${userId}`);
        if (email) summaryParts.push(email);
        userEmail.textContent = summaryParts.join(' | ') || 'Logged-in user';
        roleList.textContent = roles.join(', ');

        const preferredRole = ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient']
            .find(role => roles.includes(role));
        const config = roleConfig[preferredRole] || roleConfig.Patient;
        const currentPath = window.location.pathname;
        const bloodBankItAccess = await hasBloodBankItAccess();

        welcomeLine.textContent = `Welcome back, ${config.label}.`;
        welcomeCopy.textContent = `This dashboard organizes your next steps around the ${config.label.toLowerCase()} workflow and related tools already built in the system.`;
        primaryTitle.textContent = config.primaryLabel;
        primaryCopy.textContent = config.primaryCopy;
        primaryLink.href = config.primaryHref;
        primaryLink.textContent = 'Open main area';

        const visibleCards = [];
        roles.forEach(role => {
            const roleEntry = roleConfig[role];
            if (!roleEntry) {
                return;
            }

            roleEntry.cards.forEach(card => {
                if (!visibleCards.some(existing => existing.href === card.href)) {
                    visibleCards.push(card);
                }
            });

            if (role === 'ITWorker' && bloodBankItAccess) {
                (roleEntry.bloodBankCards || []).forEach(card => {
                    if (!visibleCards.some(existing => existing.href === card.href)) {
                        visibleCards.push(card);
                    }
                });
            }
        });

        actionGrid.innerHTML = visibleCards.map(card => `
            <article class="action-card">
                <h3>${card.title}</h3>
                <p>${card.desc}</p>
                <div class="action-links">
                    <a class="primary" href="${card.href}">Open</a>
                </div>
            </article>
        `).join('');

        if (roles.includes('Admin') || roles.includes('ITWorker')) {
            adminToolsCard.classList.remove('hidden');
        }

        if (currentPath === '/ui/dashboard') {
            setTimeout(() => {
                window.location.href = config.primaryHref;
            }, 1200);
        }
    }

    function logoutSession() {
        [
            'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
            'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
            'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
        ].forEach(key => localStorage.removeItem(key));

        window.location.href = '/ui/login';
    }
    </script>
</body>
</html>
