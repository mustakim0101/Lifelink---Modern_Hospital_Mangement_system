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

        .finder-section {
            border: 1px solid var(--line);
            border-radius: 24px;
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.1), transparent 18rem),
                radial-gradient(circle at bottom right, rgba(15, 118, 110, 0.12), transparent 20rem),
                rgba(255, 255, 255, 0.82);
            box-shadow: var(--shadow);
            padding: 22px;
            display: grid;
            gap: 18px;
        }

        .finder-header {
            display: grid;
            gap: 8px;
            max-width: 680px;
        }

        .finder-header h2,
        .finder-panel h3 {
            margin: 0;
        }

        .finder-shell {
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
            align-items: stretch;
        }

        .anatomy-card,
        .finder-panel {
            border: 1px solid var(--line);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.9);
            overflow: hidden;
        }

        .anatomy-card {
            padding: 18px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(236, 246, 250, 0.96)),
                rgba(255, 255, 255, 0.92);
        }

        .anatomy-stage {
            position: relative;
            min-height: 560px;
            border-radius: 18px;
            border: 1px solid rgba(24, 50, 68, 0.08);
            background:
                radial-gradient(circle at top, rgba(15, 118, 110, 0.12), transparent 16rem),
                linear-gradient(180deg, rgba(248, 251, 253, 0.98), rgba(228, 238, 244, 0.88));
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .anatomy-figure {
            position: relative;
            width: min(100%, 540px);
            aspect-ratio: 4598 / 5329;
        }

        .anatomy-stage img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 18px 34px rgba(24, 50, 68, 0.14));
        }

        .finder-hotspot {
            position: absolute;
            top: var(--top);
            left: var(--left);
            width: var(--width);
            height: var(--height);
            transform: translate(-50%, -50%);
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: transparent;
            cursor: pointer;
            transition: transform 0.18s ease;
            z-index: 1;
        }

        .finder-hotspot[data-region="bones-joints"] {
            z-index: 0;
        }

        .finder-hotspot[data-region="hands-arms"],
        .finder-hotspot[data-region="legs"] {
            z-index: 0;
        }

        .finder-hotspot[data-region="eyes"]::before {
            background:
                radial-gradient(circle at center, rgba(29, 78, 216, 0.24), rgba(15, 118, 110, 0.16) 58%, transparent 78%);
        }

        .finder-hotspot::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1.5px solid transparent;
            background:
                radial-gradient(circle at center, rgba(29, 78, 216, 0.18), rgba(15, 118, 110, 0.12) 55%, transparent 75%);
            opacity: 0;
            box-shadow: 0 16px 34px rgba(15, 118, 110, 0.12);
            transition: opacity 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .finder-hotspot:hover,
        .finder-hotspot.is-active {
            transform: translate(-50%, -50%) scale(1.03);
        }

        .finder-hotspot:hover::before,
        .finder-hotspot:focus-visible::before,
        .finder-hotspot.is-active::before {
            opacity: 1;
            border-color: rgba(15, 118, 110, 0.35);
            box-shadow: 0 18px 36px rgba(15, 118, 110, 0.18);
        }

        .finder-hotspot[data-region="eyes"]:hover::before,
        .finder-hotspot[data-region="eyes"]:focus-visible::before,
        .finder-hotspot[data-region="eyes"].is-active::before {
            border-color: rgba(29, 78, 216, 0.42);
            box-shadow: 0 18px 38px rgba(29, 78, 216, 0.22);
        }

        .finder-hotspot:focus-visible {
            outline: 2px solid rgba(29, 78, 216, 0.45);
            outline-offset: 3px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .finder-panel {
            padding: 18px;
            display: grid;
            gap: 14px;
            align-content: start;
        }

        .finder-panel-top {
            display: grid;
            gap: 8px;
        }

        .finder-kicker {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 12px;
            background: rgba(29, 78, 216, 0.08);
            color: var(--secondary);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .finder-panel p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .finder-departments {
            display: grid;
            gap: 8px;
        }

        .finder-departments strong {
            font-size: 0.95rem;
        }

        .finder-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .finder-tags span {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.08);
            color: var(--primary-strong);
            border: 1px solid rgba(15, 118, 110, 0.12);
            font-weight: 700;
            font-size: 0.88rem;
        }

        .finder-note {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(24, 50, 68, 0.04);
            border: 1px solid rgba(24, 50, 68, 0.08);
        }

        .finder-support {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
        }

        .finder-support-copy {
            color: var(--muted);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .finder-support-button {
            min-height: 38px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(29, 78, 216, 0.16);
            background: rgba(29, 78, 216, 0.08);
            color: var(--secondary);
            font-weight: 800;
            cursor: pointer;
            transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .finder-support-button:hover,
        .finder-support-button:focus-visible,
        .finder-support-button.is-active {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: #fff;
            border-color: transparent;
            transform: translateY(-1px);
            outline: none;
        }

        .stack {
            display: grid;
            gap: 10px;
        }

        .page-hidden { display: none !important; }

        @media (max-width: 980px) {
            .hero,
            .grid,
            .auth-grid,
            .finder-shell {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 16px, 1160px); }
            .topbar-inner { flex-direction: column; align-items: flex-start; }
            .finder-section,
            .anatomy-card,
            .finder-panel { padding: 16px; }
            .anatomy-stage { min-height: 480px; }
            .finder-hotspot {
                min-width: 38px;
                min-height: 38px;
            }
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

            <section class="finder-section" id="find-department" aria-labelledby="finder-title">
                <div class="finder-header">
                    <span class="badge">Guided Discovery</span>
                    <h2 id="finder-title">Find the right department</h2>
                    <p>Use the body map to quickly understand which LifeLink-supported department is best suited for a symptom area, urgent care path, or support need.</p>
                </div>

                <div class="finder-shell">
                    <article class="anatomy-card">
                        <div class="anatomy-stage">
                            <div class="anatomy-figure">
                                <img src="/assets/anatomy/51152.jpg" alt="Human anatomy illustration with interactive body regions for department guidance.">
                                <button class="finder-hotspot" type="button" data-region="eyes" style="--top: 19.4%; --left: 22.9%; --width: 9.8%; --height: 4.9%;" aria-label="Eyes" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Eyes</span>
                                </button>
                                <button class="finder-hotspot is-active" type="button" data-region="brain" style="--top: 16.5%; --left: 22.9%; --width: 12.2%; --height: 7.6%;" aria-label="Brain" aria-pressed="true" aria-controls="finder-panel">
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
                                <button class="finder-hotspot" type="button" data-region="left-kidney" style="--top: 45.1%; --left: 20.7%; --width: 4.1%; --height: 4.8%;" aria-label="Left kidney" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Left kidney</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="right-kidney" style="--top: 45.1%; --left: 25.7%; --width: 4.1%; --height: 4.8%;" aria-label="Right kidney" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Right kidney</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="intestines" style="--top: 50.9%; --left: 23.1%; --width: 11.1%; --height: 6.8%;" aria-label="Intestines" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Intestines</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="bladder" style="--top: 55.9%; --left: 23.1%; --width: 4.8%; --height: 3.2%;" aria-label="Bladder" aria-pressed="false" aria-controls="finder-panel">
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
                            <p id="finder-region-note">Hover or tap the anatomy itself to preview the most relevant department path, then continue into LifeLink for secure workflow access.</p>
                        </div>

                        <div class="hero-actions">
                            <a class="button primary" href="/ui/login">Continue to Login</a>
                        </div>
                    </aside>
                </div>
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
