<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Workspace Hub</title>
    <style>
        :root {
            --hub-bg: #edf4f7;
            --hub-surface: rgba(255, 255, 255, 0.9);
            --hub-line: rgba(20, 45, 63, 0.12);
            --hub-text: #143145;
            --hub-muted: #607686;
            --hub-primary: #0f766e;
            --hub-secondary: #1d4ed8;
            --hub-danger: #b91c1c;
            --hub-shadow: 0 18px 42px rgba(16, 42, 60, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--hub-text);
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.14), transparent 24rem),
                radial-gradient(circle at right, rgba(15, 118, 110, 0.14), transparent 24rem),
                linear-gradient(180deg, #f8fbfd 0%, var(--hub-bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .hub-shell {
            width: min(1280px, calc(100% - 24px));
            margin: 0 auto;
            padding: 16px 0 30px;
        }

        .hub-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .hub-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hub-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, var(--hub-secondary), var(--hub-primary));
            box-shadow: 0 12px 22px rgba(29, 78, 216, 0.24);
        }

        .hub-brand strong {
            display: block;
            font-size: 1.04rem;
        }

        .hub-brand span {
            color: var(--hub-muted);
            font-size: 0.9rem;
        }

        .hub-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .hub-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 14px;
            border: 1px solid var(--hub-line);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.78);
            font-weight: 700;
            cursor: pointer;
        }

        .hub-layout {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            gap: 14px;
        }

        .hub-rail,
        .hub-main {
            border: 1px solid var(--hub-line);
            border-radius: 20px;
            background: var(--hub-surface);
            box-shadow: var(--hub-shadow);
        }

        .hub-rail {
            padding: 14px;
            align-self: start;
            position: sticky;
            top: 12px;
            display: grid;
            gap: 12px;
        }

        .hub-main {
            padding: 14px;
            display: grid;
            gap: 12px;
        }

        .hub-block {
            border: 1px solid var(--hub-line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.86);
            padding: 14px;
        }

        .hub-label {
            display: block;
            margin-bottom: 6px;
            color: var(--hub-muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .hub-title {
            margin: 0;
            font-size: 1.24rem;
        }

        .hub-copy {
            margin: 6px 0 0;
            color: var(--hub-muted);
            line-height: 1.6;
            font-size: 0.93rem;
        }

        .hub-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 14px;
            border: 0;
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, var(--hub-secondary), var(--hub-primary));
            font-weight: 700;
        }

        .hub-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .hub-stat {
            border: 1px solid var(--hub-line);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
        }

        .hub-stat strong {
            display: block;
            font-size: 0.95rem;
        }

        .hub-stat span {
            color: var(--hub-muted);
            font-size: 0.82rem;
        }

        .hub-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .hub-card {
            border: 1px solid var(--hub-line);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.9);
            padding: 14px;
        }

        .hub-card h3 {
            margin: 0 0 8px;
            font-size: 1rem;
        }

        .hub-card p {
            margin: 0;
            color: var(--hub-muted);
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .hub-card .hub-actions {
            margin-top: 10px;
        }

        .hub-notice {
            padding: 12px;
            border-radius: 14px;
            border: 1px solid rgba(185, 28, 28, 0.2);
            color: var(--hub-danger);
            background: rgba(185, 28, 28, 0.08);
        }

        .hidden { display: none; }

        @media (max-width: 980px) {
            .hub-layout,
            .hub-grid {
                grid-template-columns: 1fr;
            }

            .hub-rail {
                position: static;
            }
        }

        @media (max-width: 720px) {
            .hub-shell { width: min(100% - 16px, 1280px); }
            .hub-topbar { flex-direction: column; align-items: flex-start; }
            .hub-stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="hub-shell">
        <header class="hub-topbar">
            <div class="hub-brand">
                <div class="hub-mark">LL</div>
                <div>
                    <strong>LifeLink Workspace Hub</strong>
                    <span>Role-aware routing and operational shortcuts.</span>
                </div>
            </div>
            <div class="hub-actions">
                <a class="hub-chip" href="/">Public Home</a>
                <button class="hub-chip" type="button" onclick="logoutSession()">Logout</button>
            </div>
        </header>

        <div class="hub-layout">
            <aside class="hub-rail">
                <article class="hub-block">
                    <span class="hub-label">Signed In</span>
                    <strong id="user-email">No active session</strong>
                    <p class="hub-copy">Identity and session summary.</p>
                </article>
                <article class="hub-block">
                    <span class="hub-label">Detected Roles</span>
                    <strong id="role-list">None</strong>
                    <p class="hub-copy">Primary route and tools adjust to your active roles.</p>
                </article>
                <article id="admin-tools-card" class="hub-block hidden">
                    <span class="hub-label">Admin / IT</span>
                    <strong>Advanced tools</strong>
                    <p class="hub-copy">Open diagnostics only when normal workflow pages are not enough.</p>
                    <div class="hub-actions">
                        <a class="hub-primary" href="/ui/dev-tools">Open advanced tools</a>
                    </div>
                </article>
            </aside>

            <main class="hub-main">
                <section class="hub-block">
                    <span class="hub-label">Workspace Hub</span>
                    <h1 id="welcome-line" class="hub-title">Organized access for your role.</h1>
                    <p id="welcome-copy" class="hub-copy">This hub highlights your next operational page and related tools.</p>
                </section>

                <section class="hub-block">
                    <span class="hub-label">Primary Destination</span>
                    <h2 id="primary-title" class="hub-title">Sign in required</h2>
                    <p id="primary-copy" class="hub-copy">Log in first to unlock role-aware routing.</p>
                    <div class="hub-actions" style="margin-top: 10px;">
                        <a id="primary-link" class="hub-primary" href="/ui/login">Open login page</a>
                    </div>
                </section>

                <section class="hub-block">
                    <span class="hub-label">Session Snapshot</span>
                    <div class="hub-stats">
                        <div class="hub-stat">
                            <strong id="session-token-state">No token</strong>
                            <span>Token state</span>
                        </div>
                        <div class="hub-stat">
                            <strong id="session-role-count">0</strong>
                            <span>Role count</span>
                        </div>
                    </div>
                </section>

                <section class="hub-block">
                    <span class="hub-label">Role Shortcuts</span>
                    <div id="action-grid" class="hub-grid"></div>
                </section>

                <div id="session-warning" class="hub-notice hidden">
                    No valid local session found. Log in again to restore role-aware routing.
                </div>
            </main>
        </div>
    </div>

    <script>
    const BLOOD_BANK_DEPARTMENT = 'Blood Bank';

    const roleConfig = {
        Admin: {
            label: 'Administrator',
            primaryLabel: 'Admin control center',
            primaryHref: '/ui/admin-users',
            primaryCopy: 'Manage account state, application decisions, and staff setup from one page.',
            cards: [
                { title: 'Admin control', href: '/ui/admin-users', desc: 'Account control and profile provisioning.' },
                { title: 'Application reviews', href: '/ui/application-reviews', desc: 'Approve or reject role applications.' },
                { title: 'Advanced tools', href: '/ui/dev-tools', desc: 'Controlled diagnostics and raw endpoint checks.' }
            ]
        },
        ITWorker: {
            label: 'IT Worker',
            primaryLabel: 'IT operations dashboard',
            primaryHref: '/ui/it-bed-allocation',
            primaryCopy: 'Manage department-scoped admissions, beds, and operational allocation flows.',
            cards: [
                { title: 'IT bed allocation', href: '/ui/it-bed-allocation', desc: 'Admission and bed assignment workflow.' },
                { title: 'Ward setup', href: '/ui/ward-setup', desc: 'Care unit and bed structure setup.' },
                { title: 'Advanced tools', href: '/ui/dev-tools', desc: 'Technical diagnostics for operations support.' }
            ],
            bloodBankCards: [
                { title: 'Blood matching center', href: '/ui/blood-matching', desc: 'Request matching and donor-response operations.' }
            ]
        },
        Doctor: {
            label: 'Doctor',
            primaryLabel: 'Doctor dashboard',
            primaryHref: '/ui/doctor-dashboard',
            primaryCopy: 'Open clinical doctor actions for patients, appointments, and bed requests.',
            cards: [
                { title: 'Doctor dashboard', href: '/ui/doctor-dashboard', desc: 'Doctor-facing clinical tools.' }
            ]
        },
        Nurse: {
            label: 'Nurse',
            primaryLabel: 'Nurse dashboard',
            primaryHref: '/ui/nurse-dashboard',
            primaryCopy: 'Open nurse monitoring workflow for department patients and vitals.',
            cards: [
                { title: 'Nurse dashboard', href: '/ui/nurse-dashboard', desc: 'Department patient monitoring and vital logs.' }
            ]
        },
        Patient: {
            label: 'Patient',
            primaryLabel: 'Patient portal',
            primaryHref: '/ui/patient-portal',
            primaryCopy: 'Use one patient workspace for appointments, records, and blood requests.',
            cards: [
                { title: 'Patient portal', href: '/ui/patient-portal', desc: 'Patient appointments, records, and blood requests.' }
            ]
        },
        Donor: {
            label: 'Donor',
            primaryLabel: 'Donor dashboard',
            primaryHref: '/ui/donor-dashboard',
            primaryCopy: 'Manage donor availability, request response, and donation history.',
            cards: [
                { title: 'Donor dashboard', href: '/ui/donor-dashboard', desc: 'Donor profile, availability, notifications, and history.' }
            ]
        },
        Applicant: {
            label: 'Applicant',
            primaryLabel: 'Application workspace',
            primaryHref: '/ui/applications',
            primaryCopy: 'Track application state and next steps until role approval.',
            cards: [
                { title: 'Applications', href: '/ui/applications', desc: 'Submit and track staff-role applications.' }
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
    const sessionTokenState = document.getElementById('session-token-state');
    const sessionRoleCount = document.getElementById('session-role-count');

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
        sessionTokenState.textContent = 'No token';
        sessionRoleCount.textContent = '0';
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
        sessionTokenState.textContent = 'Ready';
        sessionRoleCount.textContent = String(roles.length);

        const preferredRole = ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient']
            .find(role => roles.includes(role));
        const config = roleConfig[preferredRole] || roleConfig.Patient;
        const currentPath = window.location.pathname;
        const bloodBankItAccess = await hasBloodBankItAccess();

        welcomeLine.textContent = `Welcome back, ${config.label}.`;
        welcomeCopy.textContent = `This hub keeps your role workflow clear and routes you to operational pages quickly.`;
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
            <article class="hub-card">
                <h3>${card.title}</h3>
                <p>${card.desc}</p>
                <div class="hub-actions">
                    <a class="hub-primary" href="${card.href}">Open</a>
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
