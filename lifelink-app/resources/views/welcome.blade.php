<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Modern Hospital Management</title>
    <style>
        :root {
            --bg: #eef3f7;
            --surface: rgba(255, 255, 255, 0.9);
            --text: #183244;
            --muted: #5d7280;
            --line: rgba(24, 50, 68, 0.12);
            --primary: #0f766e;
            --primary-strong: #0a4d56;
            --secondary: #1d4ed8;
            --accent: #f97316;
            --shadow: 0 18px 44px rgba(16, 40, 60, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.14), transparent 24rem),
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.12), transparent 24rem),
                linear-gradient(180deg, #f7fbfd 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1160px, calc(100% - 24px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(12px);
            background: rgba(247, 251, 253, 0.9);
            border-bottom: 1px solid rgba(24, 50, 68, 0.08);
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .brand-copy strong {
            display: block;
            font-size: 1.08rem;
        }

        .brand-copy span {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .topnav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .topnav a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 13px;
            border-radius: 999px;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .topnav a:hover {
            background: rgba(15, 118, 110, 0.09);
            color: var(--text);
        }

        .topnav .cta {
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        main {
            padding: 28px 0 40px;
            display: grid;
            gap: 18px;
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 24px;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 22px;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
            gap: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 12px;
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary-strong);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
            font-weight: 800;
        }

        h1 {
            margin: 12px 0 10px;
            font-size: clamp(1.9rem, 3vw, 3.2rem);
            line-height: 1.06;
        }

        .hero p,
        .card p,
        .list li,
        .meta p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.88);
        }

        .button.primary {
            color: #fff;
            border: 0;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .meta {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.92);
            padding: 14px;
            display: grid;
            gap: 8px;
        }

        .meta strong {
            display: block;
            font-size: 1.06rem;
        }

        .grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 14px;
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 1.06rem;
        }

        .list {
            margin: 10px 0 0;
            padding-left: 18px;
            display: grid;
            gap: 6px;
        }

        .auth-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: 1.2fr 0.8fr;
        }

        .stack {
            display: grid;
            gap: 10px;
        }

        .page-hidden { display: none !important; }

        @media (max-width: 980px) {
            .hero,
            .grid,
            .auth-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 16px, 1160px); }
            .topbar-inner { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell topbar-inner">
            <div class="brand">
                <div class="brand-mark">LL</div>
                <div class="brand-copy">
                    <strong>LifeLink</strong>
                    <span>Hospital coordination for care, beds, and blood response.</span>
                </div>
            </div>

            <nav class="topnav">
                <a href="#overview">Overview</a>
                <a href="#modules">Services</a>
                <a href="#entry">Entry</a>
                <a id="auth-nav-link" href="/ui/login">Login / Register</a>
                <a id="session-nav-link" class="cta" href="/ui">Explore UI</a>
            </nav>
        </div>
    </header>

    <div class="shell">
        <main>
            <section class="hero" id="overview">
                <article>
                    <span class="badge">Public Mode</span>
                    <h1>One platform for hospital operations and donor response.</h1>
                    <p>LifeLink connects admissions, bed allocation, patient access, and blood-request workflows in one role-aware system.</p>
                    <div class="hero-actions">
                        <a class="button primary" href="/ui/login">Open Login</a>
                        <a class="button" href="#modules">See Modules</a>
                        <a class="button" href="/ui/dashboard">Workspace Hub</a>
                    </div>
                </article>
                <aside class="meta">
                    <strong>What this page does</strong>
                    <p>Explains the product before login and routes signed-in users back to authenticated workflow pages.</p>
                    <strong>Who it serves</strong>
                    <p>Admin, IT, doctor, nurse, patient, donor, and applicant roles.</p>
                </aside>
            </section>

            <section id="modules" class="grid">
                <article class="card">
                    <h2>Role-aware authentication</h2>
                    <p>Single login entry with route decisions based on active role.</p>
                </article>
                <article class="card">
                    <h2>Admissions and beds</h2>
                    <p>Department scope, care units, admissions, and bed assignment.</p>
                </article>
                <article class="card">
                    <h2>Blood operations</h2>
                    <p>Donor readiness, matching, notifications, and fulfillment flow.</p>
                </article>
            </section>

            <section id="entry" class="auth-grid">
                <div id="logged-out-entry" class="card stack">
                    <h2>Start here</h2>
                    <p>Use login for existing users. Use registration only for new patient, donor, or applicant accounts.</p>
                    <ul class="list">
                        <li>Login routes users to role-specific workflow pages.</li>
                        <li>Applicants stay in application workspace until approval.</li>
                        <li>Donor and patient registration open role onboarding paths.</li>
                    </ul>
                    <div class="hero-actions">
                        <a class="button primary" href="/ui/login">Login</a>
                        <a class="button" href="/ui/register/patient">Patient Register</a>
                        <a class="button" href="/ui/register/donor">Donor Register</a>
                        <a class="button" href="/ui/register/applicant">Applicant Register</a>
                    </div>
                </div>

                <div id="logged-in-entry" class="card stack page-hidden">
                    <h2>Session detected</h2>
                    <p>You are already signed in. Continue to your workspace or log out before switching account.</p>
                    <div class="hero-actions">
                        <a id="logged-dashboard-link" class="button primary" href="/ui/dashboard">Go to dashboard</a>
                        <button id="logout-button" class="button" type="button">Logout</button>
                    </div>
                </div>

                <article class="card">
                    <h2>Quick links</h2>
                    <ul class="list">
                        <li><a href="/ui/applications">Application flow prototype</a></li>
                        <li><a href="/ui/it-bed-allocation">IT operations prototype</a></li>
                        <li><a href="/ui/blood-matching">Blood matching prototype</a></li>
                        <li><a href="/ui/patient-portal">Patient portal prototype</a></li>
                    </ul>
                </article>
            </section>
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
    const roleDestinations = {
        Admin: '/ui/admin-users',
        ITWorker: '/ui/it-bed-allocation',
        Doctor: '/ui/doctor-dashboard',
        Nurse: '/ui/nurse-dashboard',
        Donor: '/ui/donor-dashboard',
        Applicant: '/ui/applications',
        Patient: '/ui/patient-portal'
    };

    function preferredRolePath() {
        const preferredRole = rolePriority.find(role => roles.includes(role));
        return preferredRole ? roleDestinations[preferredRole] : '/ui/dashboard';
    }

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
