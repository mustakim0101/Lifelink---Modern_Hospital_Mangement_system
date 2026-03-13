<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Modern Hospital Management</title>
    <style>
        :root {
            --bg: #eef3f7;
            --surface: rgba(255, 255, 255, 0.78);
            --surface-strong: #ffffff;
            --text: #183244;
            --muted: #5d7280;
            --line: rgba(24, 50, 68, 0.12);
            --primary: #0f766e;
            --primary-strong: #0a4d56;
            --secondary: #1d4ed8;
            --accent: #f97316;
            --success: #15803d;
            --shadow: 0 28px 70px rgba(16, 40, 60, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.16), transparent 24rem),
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.16), transparent 26rem),
                linear-gradient(180deg, #f7fbfd 0%, var(--bg) 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(18px);
            background: rgba(247, 251, 253, 0.88);
            border-bottom: 1px solid rgba(24, 50, 68, 0.08);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            box-shadow: 0 18px 32px rgba(29, 78, 216, 0.28);
        }

        .brand-copy h1 {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.2rem;
            letter-spacing: 0.03em;
        }

        .brand-copy p {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .topnav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        .topnav a {
            padding: 10px 14px;
            border-radius: 999px;
            color: var(--muted);
            font-weight: 600;
            transition: 0.2s ease;
        }

        .topnav a:hover {
            color: var(--text);
            background: rgba(15, 118, 110, 0.08);
        }

        .topnav .cta {
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            box-shadow: 0 16px 28px rgba(15, 118, 110, 0.22);
        }

        .hero {
            padding: 64px 0 40px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(340px, 0.85fr);
            gap: 28px;
            align-items: stretch;
        }

        .panel,
        .feature-card,
        .mini-card,
        .department-card,
        .cta-card,
        .footer-card {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--surface);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .hero-copy {
            padding: 42px;
            position: relative;
            overflow: hidden;
        }

        .hero-copy::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -80px;
            top: -80px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.24), transparent 68%);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary-strong);
            font-size: 0.84rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-copy h2 {
            margin: 20px 0 18px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(2.5rem, 4vw, 4.8rem);
            line-height: 1.02;
            max-width: 10ch;
        }

        .hero-copy p {
            margin: 0;
            max-width: 58ch;
            color: var(--muted);
            font-size: 1.06rem;
            line-height: 1.8;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 20px;
            border-radius: 999px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .button-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            box-shadow: 0 18px 30px rgba(29, 78, 216, 0.24);
        }

        .button-secondary {
            color: var(--text);
            border: 1px solid rgba(24, 50, 68, 0.14);
            background: rgba(255, 255, 255, 0.82);
        }

        .hero-note {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 30px;
        }

        .mini-card {
            padding: 18px;
        }

        .mini-card strong {
            display: block;
            font-size: 1.35rem;
            margin-bottom: 6px;
        }

        .mini-card span {
            color: var(--muted);
            font-size: 0.94rem;
            line-height: 1.5;
        }

        .hero-visual {
            padding: 30px;
            display: grid;
            gap: 18px;
            background:
                linear-gradient(165deg, rgba(255, 255, 255, 0.92), rgba(230, 244, 245, 0.82)),
                radial-gradient(circle at bottom right, rgba(29, 78, 216, 0.16), transparent 38%);
        }

        .visual-stage {
            position: relative;
            min-height: 360px;
            border-radius: 24px;
            overflow: hidden;
            background:
                radial-gradient(circle at 20% 25%, rgba(29, 78, 216, 0.18), transparent 18rem),
                radial-gradient(circle at 80% 70%, rgba(15, 118, 110, 0.22), transparent 20rem),
                linear-gradient(180deg, #fefefe 0%, #e6f3f1 100%);
        }

        .glow,
        .orb,
        .ring {
            position: absolute;
            border-radius: 50%;
        }

        .glow {
            inset: 18% auto auto 14%;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.32), transparent 70%);
            filter: blur(8px);
        }

        .orb {
            top: 22%;
            left: 19%;
            width: 170px;
            height: 170px;
            background: linear-gradient(145deg, rgba(29, 78, 216, 0.92), rgba(15, 118, 110, 0.88));
            box-shadow: 0 34px 60px rgba(15, 118, 110, 0.2);
            animation: drift 8s ease-in-out infinite;
        }

        .ring {
            top: 20%;
            left: 17%;
            width: 210px;
            height: 210px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-left-color: rgba(29, 78, 216, 0.34);
            border-right-color: rgba(15, 118, 110, 0.34);
            animation: spin 16s linear infinite;
        }

        .pulse-card {
            position: absolute;
            right: 22px;
            width: min(250px, calc(100% - 44px));
            padding: 18px 18px 16px;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.78);
            box-shadow: 0 20px 44px rgba(21, 55, 70, 0.12);
        }

        .pulse-card.top {
            top: 28px;
        }

        .pulse-card.bottom {
            bottom: 24px;
        }

        .pulse-card small,
        .insight small {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.74rem;
            font-weight: 700;
        }

        .pulse-card strong,
        .insight strong {
            display: block;
            font-size: 1.34rem;
            margin-bottom: 8px;
        }

        .pulse-card p,
        .insight p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: 0.94rem;
        }

        .visual-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .insight {
            padding: 18px;
            border-radius: 22px;
            border: 1px solid rgba(24, 50, 68, 0.1);
            background: rgba(255, 255, 255, 0.72);
        }

        section {
            padding: 22px 0 34px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            margin-bottom: 22px;
        }

        .section-head h3 {
            margin: 0 0 10px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(1.9rem, 3vw, 3rem);
        }

        .section-head p {
            margin: 0;
            color: var(--muted);
            max-width: 58ch;
            line-height: 1.75;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .feature-card,
        .department-card,
        .cta-card,
        .footer-card {
            padding: 22px;
        }

        .feature-card .icon,
        .department-card .icon {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            margin-bottom: 18px;
            color: #fff;
            font-size: 1.3rem;
            background: linear-gradient(145deg, var(--secondary), var(--primary));
        }

        .feature-card h4,
        .department-card h4,
        .cta-card h4 {
            margin: 0 0 10px;
            font-size: 1.16rem;
        }

        .feature-card p,
        .department-card p,
        .cta-card p,
        .footer-card p,
        .footer-links a {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .dept-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .department-card .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary-strong);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .cta-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(260px, 0.8fr);
            gap: 18px;
        }

        .auth-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .cta-card {
            min-height: 100%;
            background:
                linear-gradient(160deg, rgba(15, 118, 110, 0.92), rgba(29, 78, 216, 0.9)),
                #0f766e;
            color: #fff;
        }

        .cta-card p {
            color: rgba(255, 255, 255, 0.82);
        }

        .cta-list {
            margin: 18px 0 22px;
            display: grid;
            gap: 10px;
        }

        .cta-list span {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
        }

        .cta-list span::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #fde68a;
            box-shadow: 0 0 0 6px rgba(253, 230, 138, 0.16);
        }

        .button-light {
            color: var(--text);
            background: #ffffff;
        }

        .footer {
            padding: 24px 0 54px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 18px;
        }

        .footer-links {
            display: grid;
            gap: 12px;
        }

        .footer-links a:hover {
            color: var(--text);
        }

        .footer-card strong {
            display: block;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .route-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .route-links a {
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.9);
        }

        .auth-card-light {
            background: rgba(255, 255, 255, 0.82);
        }

        .auth-card-light .button-secondary {
            background: rgba(22, 49, 67, 0.08);
        }

        .auth-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .auth-actions .button {
            padding: 12px 16px;
        }

        .page-hidden {
            display: none !important;
        }

        @keyframes drift {
            0%, 100% { transform: translate3d(0, 0, 0); }
            50% { transform: translate3d(0, -14px, 0); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 1040px) {
            .hero-grid,
            .cta-grid,
            .footer-grid,
            .feature-grid,
            .dept-grid,
            .auth-options {
                grid-template-columns: 1fr;
            }

            .hero-note,
            .visual-grid {
                grid-template-columns: 1fr;
            }

            .section-head {
                align-items: start;
                flex-direction: column;
            }
        }

        @media (max-width: 720px) {
            .topbar-inner {
                align-items: start;
                flex-direction: column;
            }

            .topnav {
                justify-content: flex-start;
            }

            .hero {
                padding-top: 36px;
            }

            .hero-copy,
            .hero-visual,
            .feature-card,
            .department-card,
            .cta-card,
            .footer-card {
                padding: 20px;
            }

            .hero-copy h2 {
                max-width: none;
            }

            .visual-stage {
                min-height: 310px;
            }

            .pulse-card {
                position: static;
                width: 100%;
                margin-top: 14px;
            }

            .orb {
                left: 50%;
                transform: translateX(-50%);
            }

            .ring {
                left: calc(50% - 105px);
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
                    <h1>LifeLink</h1>
                    <p>Modern hospital coordination for care, beds, and blood response.</p>
                </div>
            </div>

            <nav class="topnav">
                <a href="#overview">Overview</a>
                <a href="#modules">Services</a>
                <a href="#departments">Departments</a>
                <a href="#donate">Donate Blood</a>
                <a id="auth-nav-link" href="/ui/auth#login">Login / Register</a>
                <a id="session-nav-link" class="cta" href="/ui">Explore UI</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="shell hero-grid">
                <article class="panel hero-copy">
                    <span class="eyebrow">Public Mode</span>
                    <h2>Care coordination built around people, urgency, and trust.</h2>
                    <p>
                        LifeLink brings admissions, bed allocation, patient access, donor coordination,
                        and hospital role workflows into one structured platform. It is designed to help
                        teams move from request to response with less confusion and clearer visibility.
                    </p>

                    <div class="hero-actions">
                        <a class="button button-primary" href="/ui/auth">Start With Login</a>
                        <a class="button button-secondary" href="#modules">View Core Modules</a>
                        <a class="button button-secondary" href="#donate">See Donor Flow</a>
                    </div>

                    <div class="hero-note">
                        <div class="mini-card">
                            <strong>7 Roles</strong>
                            <span>Different user experiences for admin, IT, doctor, nurse, patient, applicant, and donor.</span>
                        </div>
                        <div class="mini-card">
                            <strong>Bed to Blood</strong>
                            <span>Supports both physical care coordination and donor response workflows in one system.</span>
                        </div>
                        <div class="mini-card">
                            <strong>SQL-Driven</strong>
                            <span>Built on Laravel, MSSQL, and Docker with role-aware backend flows already implemented.</span>
                        </div>
                    </div>
                </article>

                <aside class="panel hero-visual">
                    <div class="visual-stage">
                        <div class="glow"></div>
                        <div class="ring"></div>
                        <div class="orb"></div>

                        <div class="pulse-card top">
                            <small>Hospital View</small>
                            <strong>Admissions, departments, and critical requests stay connected.</strong>
                            <p>The platform is organized around what the hospital needs to act on next, not just around isolated forms.</p>
                        </div>

                        <div class="pulse-card bottom">
                            <small>Donor Response</small>
                            <strong>Urgent blood needs can move from request to matching to donor action.</strong>
                            <p>Available and compatible donors can be identified quickly through the blood workflow already designed in the backend.</p>
                        </div>
                    </div>

                    <div class="visual-grid">
                        <div class="insight">
                            <small>System Focus</small>
                            <strong>Role-aware by design</strong>
                            <p>Each person should enter the same platform but arrive at a different experience based on their role.</p>
                        </div>
                        <div class="insight">
                            <small>Public Goal</small>
                            <strong>Clarity before login</strong>
                            <p>The landing experience should explain what LifeLink does before asking users to sign in.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section id="overview">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <h3>One hospital platform, several coordinated journeys.</h3>
                        <p>
                            LifeLink is not just an admin panel. It is a hospital workflow system that supports
                            staffing, admissions, patient care, donor tracking, and blood request response in one place.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="modules">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <h3>Core services inside the platform.</h3>
                        <p>
                            These are the major system areas already represented in the backend and prototype UI pages.
                        </p>
                    </div>
                </div>

                <div class="feature-grid">
                    <article class="feature-card">
                        <div class="icon">A</div>
                        <h4>Authentication and Role Access</h4>
                        <p>Handles secure login, registration, account status, and role-based access to protected areas.</p>
                    </article>

                    <article class="feature-card">
                        <div class="icon">J</div>
                        <h4>Job Application Review</h4>
                        <p>Applicants submit requests, while Admin and IT reviewers approve or reject staff-role transitions.</p>
                    </article>

                    <article class="feature-card">
                        <div class="icon">B</div>
                        <h4>Bed and Ward Coordination</h4>
                        <p>Departments, care units, admissions, and beds are connected so staff can allocate and release space properly.</p>
                    </article>

                    <article class="feature-card">
                        <div class="icon">D</div>
                        <h4>Blood and Donor Operations</h4>
                        <p>Tracks donor availability, health, donation activity, blood requests, matching, and notification response.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="departments">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <h3>Departments snapshot.</h3>
                        <p>
                            The system is designed around real clinical areas so beds, staff scope, and requests remain tied to the right department.
                        </p>
                    </div>
                </div>

                <div class="dept-grid">
                    <article class="department-card">
                        <div class="icon">C</div>
                        <h4>Cardiology</h4>
                        <p>Supports patients who need heart-related consultation, monitoring, and admission management.</p>
                        <div class="meta">
                            <span class="pill">Ward / ICU scope</span>
                            <span class="pill">Doctor-led flow</span>
                        </div>
                    </article>

                    <article class="department-card">
                        <div class="icon">N</div>
                        <h4>Neurology</h4>
                        <p>Useful for patient admission, monitoring, and assignment across specialized care levels and beds.</p>
                        <div class="meta">
                            <span class="pill">Care unit driven</span>
                            <span class="pill">Nurse follow-up</span>
                        </div>
                    </article>

                    <article class="department-card">
                        <div class="icon">P</div>
                        <h4>Pediatrics</h4>
                        <p>Fits the same coordinated hospital flow while keeping department-specific admissions and care visibility.</p>
                        <div class="meta">
                            <span class="pill">Patient records</span>
                            <span class="pill">Appointments</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="donate">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <h3>Blood donation call-to-action.</h3>
                        <p>
                            Donor support is one of the strongest workflow areas already designed in the backend. The public experience should make that visible and trustworthy.
                        </p>
                    </div>
                </div>

                <div id="logged-out-entry" class="auth-options">
                    <article class="cta-card">
                        <h4>Login for every existing user.</h4>
                        <p>
                            A single login path is used for all roles. After sign-in, the system reads the account role
                            and routes the user into the correct dashboard area.
                        </p>

                        <div class="cta-list">
                            <span>One login path for all accounts</span>
                            <span>Role decides the dashboard after sign-in</span>
                            <span>Clean entry before advanced workflows</span>
                        </div>

                        <div class="auth-actions">
                            <a class="button button-light" href="/ui/auth#login">Login</a>
                            <a class="button button-secondary" href="/ui/dashboard">Dashboard preview</a>
                        </div>
                    </article>

                    <article class="feature-card auth-card-light">
                        <div class="icon">P</div>
                        <h4>Register as patient</h4>
                        <p>Create a standard user account for appointments, records, and patient-side blood request access.</p>
                        <div class="auth-actions">
                            <a class="button button-secondary" href="/ui/auth#patient">Patient registration</a>
                        </div>
                    </article>

                    <article class="feature-card auth-card-light">
                        <div class="icon">D</div>
                        <h4>Register as blood donor</h4>
                        <p>Create the account and initialize the donor profile so the person can later log in and continue donor actions.</p>
                        <div class="auth-actions">
                            <a class="button button-secondary" href="/ui/auth#donor">Donor registration</a>
                        </div>
                    </article>

                    <article class="feature-card auth-card-light">
                        <div class="icon">J</div>
                        <h4>Register as job applicant</h4>
                        <p>Create the account and start the application journey so the user can later log in and track approval status.</p>
                        <div class="auth-actions">
                            <a class="button button-secondary" href="/ui/auth#applicant">Applicant registration</a>
                        </div>
                    </article>
                </div>

                <div id="logged-in-entry" class="cta-grid page-hidden">
                    <article class="cta-card">
                        <h4>You already have an active session.</h4>
                        <p>
                            The public landing page switches to a session-aware state when a user is already logged in.
                            Instead of showing fresh registration prompts, it now guides the person back into the authenticated flow.
                        </p>

                        <div class="cta-list">
                            <span>Continue into the authenticated dashboard</span>
                            <span>Move into role-specific workflow pages</span>
                            <span>Log out before starting another account</span>
                        </div>

                        <div class="auth-actions">
                            <a class="button button-light" href="/ui/dashboard">Go to dashboard</a>
                            <button id="logout-button" class="button button-secondary" type="button">Logout</button>
                        </div>
                    </article>

                    <article class="feature-card auth-card-light">
                        <div class="icon">+</div>
                        <h4>Why this matters</h4>
                        <p>
                            Logged-in users should not see a public-only register prompt as their main next step. The session-aware state keeps the landing page consistent with authenticated mode.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="shell footer-grid">
                <div class="footer-card">
                    <strong>LifeLink Public Entry</strong>
                    <p>
                        This landing page is the public-facing introduction for visitors who have not logged in yet.
                        It should explain the product before sending users into the role-specific parts of the system.
                    </p>

                    <div class="route-links">
                        <a href="/ui/auth">Auth Page</a>
                        <a href="/ui">UI Directory</a>
                        <a href="#modules">System Overview</a>
                    </div>
                </div>

                <div class="footer-card">
                    <strong>Project Links</strong>
                    <div class="footer-links">
                        <a href="/ui/applications">Application flow prototype</a>
                        <a href="/ui/it-bed-allocation">Bed allocation prototype</a>
                        <a href="/ui/blood-matching">Blood matching prototype</a>
                        <a href="/ui/patient-portal">Patient portal prototype</a>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script>
    const token = localStorage.getItem('USER_TOKEN') || '';
    const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');

    const authNavLink = document.getElementById('auth-nav-link');
    const sessionNavLink = document.getElementById('session-nav-link');
    const loggedOutEntry = document.getElementById('logged-out-entry');
    const loggedInEntry = document.getElementById('logged-in-entry');
    const logoutButton = document.getElementById('logout-button');

    if (token && roles.length) {
        if (authNavLink) {
            authNavLink.textContent = 'Go to Dashboard';
            authNavLink.href = '/ui/dashboard';
        }

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
