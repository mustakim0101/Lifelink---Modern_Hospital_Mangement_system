<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | @yield('title', 'Workspace')</title>
    <link rel="stylesheet" href="/css/ui-system.css">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <header class="app-shell__topbar">
            <a class="app-shell__brand" href="/">
                <div class="app-shell__mark">LL</div>
                <div>
                    <strong>LifeLink Workspace</strong>
                    @if(trim($__env->yieldContent('workspace_label', 'Role-aware authenticated mode')) !== '')
                        <span>@yield('workspace_label', 'Role-aware authenticated mode')</span>
                    @endif
                </div>
            </a>

            <div class="app-shell__actions">
                <a class="app-shell__chip" href="/">Public Home</a>
                @if(trim($__env->yieldContent('show_prototype_directory')) === '1')
                    <a class="app-shell__chip" href="/ui">Prototype Directory</a>
                @endif
                @yield('top_actions')
                <button class="app-shell__button" type="button" onclick="window.lifeLinkShell.logout()">Logout</button>
            </div>
        </header>

        <section class="app-shell__hero">
            <div class="app-shell__hero-copy">
                @if(trim($__env->yieldContent('hero_badge', 'Authenticated Mode')) !== '')
                    <span class="app-shell__eyebrow">@yield('hero_badge', 'Authenticated Mode')</span>
                @endif
                <h1>@yield('hero_title', 'Workspace')</h1>
                @if(trim($__env->yieldContent('hero_description', 'This area is part of the authenticated product flow.')) !== '')
                    <p>@yield('hero_description', 'This area is part of the authenticated product flow.')</p>
                @endif
                @hasSection('hero_extra')
                    @yield('hero_extra')
                @endif
            </div>

            <div class="app-shell__hero-meta">
                <div class="app-shell__meta-card">
                    <small>Signed in as</small>
                    <strong id="shell-user-name">No active session</strong>
                    <span id="shell-user-meta">No role detected</span>
                </div>
                @if(trim($__env->yieldContent('hide_meta_card')) !== '1')
                    <div class="app-shell__meta-card">
                        <small>Current area</small>
                        <strong>@yield('meta_title', 'Workspace')</strong>
                        <span>@yield('meta_copy', 'Primary task area')</span>
                    </div>
                @endif
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
                @hasSection('section_nav')
                    <div class="ll-section-nav" role="navigation" aria-label="Section navigation">
                        @yield('section_nav')
                    </div>
                @endif
                <div class="app-shell__content-body">
                    @yield('content')
                </div>
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
                'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES',
                'LAST_USED_EMAIL'
            ].forEach(key => localStorage.removeItem(key));
            window.location.href = '/ui/login';
        },
        getPreferredRole(roles) {
            return this.rolePriority.find(role => roles.includes(role)) || null;
        }
    };

    (function hydrateShell() {
        const fullName = localStorage.getItem('CURRENT_USER_FULL_NAME') || '';
        const userId = localStorage.getItem('CURRENT_USER_ID') || '';
        const email = localStorage.getItem('CURRENT_USER_EMAIL') || 'No active session';
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        const userName = document.getElementById('shell-user-name');
        const userMeta = document.getElementById('shell-user-meta');
        const preferredRole = window.lifeLinkShell.getPreferredRole(roles);
        const identity = fullName || email;
        const metaParts = [];

        if (userId) metaParts.push(`ID #${userId}`);
        if (email) metaParts.push(email);
        metaParts.push(preferredRole ? `${preferredRole} workflow` : 'No role detected');

        if (userName) userName.textContent = identity;
        if (userMeta) userMeta.textContent = metaParts.join(' | ');
    })();
    </script>
    @stack('scripts')
</body>
</html>
