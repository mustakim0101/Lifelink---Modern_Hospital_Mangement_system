<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | @yield('title', 'Workspace')</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap');

        :root {
            --shell-bg-a: #edf6f8;
            --shell-bg-b: #fef7ed;
            --shell-ink: #132d41;
            --shell-muted: #5d7281;
            --shell-line: rgba(19, 45, 65, 0.12);
            --shell-card: rgba(255, 255, 255, 0.84);
            --shell-card-strong: #ffffff;
            --shell-primary: #0f766e;
            --shell-primary-strong: #0c5d58;
            --shell-secondary: #1d4ed8;
            --shell-accent: #ea580c;
            --shell-danger: #b91c1c;
            --shell-shadow: 0 24px 60px rgba(15, 34, 48, 0.14);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--shell-ink);
            font-family: "Manrope", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.14), transparent 24rem),
                radial-gradient(circle at right, rgba(15, 118, 110, 0.14), transparent 28rem),
                linear-gradient(150deg, var(--shell-bg-a), var(--shell-bg-b));
        }

        a { color: inherit; text-decoration: none; }
        h1, h2, h3, h4 { margin: 0; font-family: "Sora", "Trebuchet MS", sans-serif; letter-spacing: -0.01em; }

        .app-shell {
            width: min(1400px, calc(100% - 24px));
            margin: 0 auto;
            padding: 16px 0 28px;
        }

        .app-shell__topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 10px 0 16px;
        }

        .app-shell__brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .app-shell__mark {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, var(--shell-secondary), var(--shell-primary));
            box-shadow: 0 18px 28px rgba(29, 78, 216, 0.22);
        }

        .app-shell__brand strong {
            display: block;
            font-size: 1.05rem;
        }

        .app-shell__brand span {
            color: var(--shell-muted);
            font-size: 0.93rem;
        }

        .app-shell__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .app-shell__chip,
        .app-shell__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--shell-line);
            background: rgba(255, 255, 255, 0.74);
            color: var(--shell-ink);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .app-shell__button--primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, var(--shell-secondary), var(--shell-primary));
        }

        .app-shell__hero {
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 28px;
            background:
                radial-gradient(circle at top left, rgba(234, 88, 12, 0.14), transparent 18rem),
                linear-gradient(150deg, rgba(255, 255, 255, 0.94), rgba(234, 246, 245, 0.82));
            box-shadow: var(--shell-shadow);
            padding: 24px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
        }

        .app-shell__hero-copy {
            max-width: 70ch;
        }

        .app-shell__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.1);
            color: var(--shell-primary-strong);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .app-shell__hero h1 {
            margin-top: 16px;
            font-size: clamp(2rem, 3vw, 3.2rem);
            line-height: 1.04;
        }

        .app-shell__hero p {
            margin: 12px 0 0;
            color: var(--shell-muted);
            line-height: 1.8;
        }

        .app-shell__hero-meta {
            min-width: 260px;
            display: grid;
            gap: 12px;
        }

        .app-shell__meta-card {
            border: 1px solid var(--shell-line);
            border-radius: 20px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.72);
        }

        .app-shell__meta-card small {
            display: block;
            margin-bottom: 6px;
            color: var(--shell-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .app-shell__meta-card strong {
            display: block;
            font-size: 1.06rem;
        }

        .app-shell__meta-card span {
            color: var(--shell-muted);
            font-size: 0.92rem;
        }

        .app-shell__body {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 14px;
            margin-top: 14px;
        }

        .app-shell__sidebar,
        .app-shell__content {
            border: 1px solid var(--shell-line);
            border-radius: 22px;
            background: var(--shell-card);
            box-shadow: var(--shell-shadow);
        }

        .app-shell__sidebar {
            padding: 14px;
            align-self: start;
            position: sticky;
            top: 14px;
        }

        .app-shell__content {
            padding: 14px;
        }

        .app-shell__nav {
            display: grid;
            gap: 10px;
            margin-bottom: 14px;
        }

        .app-shell__nav a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.78);
            color: var(--shell-muted);
            font-weight: 700;
            transition: 0.18s ease;
        }

        .app-shell__nav a:hover,
        .app-shell__nav a.is-active {
            border-color: rgba(15, 118, 110, 0.18);
            background: rgba(15, 118, 110, 0.1);
            color: var(--shell-primary-strong);
        }

        .app-shell__nav a span {
            font-size: 0.86rem;
            font-weight: 600;
        }

        .app-shell__sidebar-card {
            border: 1px solid var(--shell-line);
            border-radius: 18px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.76);
        }

        .app-shell__sidebar-card + .app-shell__sidebar-card {
            margin-top: 12px;
        }

        .app-shell__sidebar-card strong {
            display: block;
            margin-bottom: 6px;
        }

        .app-shell__sidebar-card p {
            margin: 0;
            color: var(--shell-muted);
            line-height: 1.7;
            font-size: 0.93rem;
        }

        @media (max-width: 1080px) {
            .app-shell__body {
                grid-template-columns: 1fr;
            }

            .app-shell__sidebar {
                position: static;
            }
        }

        @media (max-width: 780px) {
            .app-shell {
                width: min(100% - 20px, 1400px);
            }

            .app-shell__topbar,
            .app-shell__hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .app-shell__hero-meta {
                width: 100%;
                min-width: 0;
            }
        }

        @media (prefers-reduced-motion: no-preference) {
            .app-shell__hero,
            .app-shell__sidebar,
            .app-shell__content {
                animation: shellFade 0.38s ease;
            }
        }

        @keyframes shellFade {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <header class="app-shell__topbar">
            <div class="app-shell__brand">
                <div class="app-shell__mark">LL</div>
                <div>
                    <strong>LifeLink Workspace</strong>
                    <span>@yield('workspace_label', 'Role-aware authenticated mode')</span>
                </div>
            </div>

            <div class="app-shell__actions">
                <a class="app-shell__chip" href="/">Public Home</a>
                <a class="app-shell__chip" href="/ui">Prototype Directory</a>
                @yield('top_actions')
                <button class="app-shell__button" type="button" onclick="window.lifeLinkShell.logout()">Logout</button>
            </div>
        </header>

        <section class="app-shell__hero">
            <div class="app-shell__hero-copy">
                <span class="app-shell__eyebrow">@yield('hero_badge', 'Authenticated Mode')</span>
                <h1>@yield('hero_title', 'Workspace')</h1>
                <p>@yield('hero_description', 'This area is part of the authenticated product flow.')</p>
            </div>

            <div class="app-shell__hero-meta">
                <div class="app-shell__meta-card">
                    <small>Signed in as</small>
                    <strong id="shell-user-email">No active session</strong>
                    <span id="shell-user-role">No role detected</span>
                </div>
                <div class="app-shell__meta-card">
                    <small>Current area</small>
                    <strong>@yield('meta_title', 'Workspace')</strong>
                    <span>@yield('meta_copy', 'Primary task area')</span>
                </div>
            </div>
        </section>

        <section class="app-shell__body">
            <aside class="app-shell__sidebar">
                <nav class="app-shell__nav">
                    @yield('sidebar_nav')
                </nav>
                @yield('sidebar')
            </aside>

            <main class="app-shell__content">
                @yield('content')
            </main>
        </section>
    </div>

    <script>
    window.lifeLinkShell = {
        rolePriority: ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient'],
        roleDestinations: {
            Admin: '/ui/admin-users',
            ITWorker: '/ui/it-bed-allocation',
            Doctor: '/ui/doctor-dashboard',
            Nurse: '/ui/nurse-dashboard',
            Patient: '/ui/patient-portal',
            Donor: '/ui/donor-dashboard',
            Applicant: '/ui/applications',
        },
        logout() {
            [
                'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
                'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
                'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES',
                'LAST_USED_EMAIL'
            ].forEach(key => localStorage.removeItem(key));
            window.location.href = '/ui/login';
        },
        getPreferredRole(roles) {
            return this.rolePriority.find(role => roles.includes(role)) || null;
        }
    };

    (function hydrateShell() {
        const email = localStorage.getItem('CURRENT_USER_EMAIL') || 'No active session';
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        const userEmail = document.getElementById('shell-user-email');
        const userRole = document.getElementById('shell-user-role');
        const preferredRole = window.lifeLinkShell.getPreferredRole(roles);

        if (userEmail) userEmail.textContent = email;
        if (userRole) userRole.textContent = preferredRole ? `${preferredRole} workflow` : 'No role detected';
    })();
    </script>
    @stack('scripts')
</body>
</html>
