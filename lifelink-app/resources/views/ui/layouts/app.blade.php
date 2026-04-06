<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | @yield('title', 'Workspace')</title>
    <link rel="stylesheet" href="{{ asset('css/ui-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui-foundation.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')">
    <div class="app-shell">
        <div class="app-shell__sidebar-backdrop" data-shell-close></div>

        <aside class="app-shell__sidebar">
            <a class="app-shell__brand" href="/">
                <div class="app-shell__mark" aria-hidden="true">
                    <span class="app-shell__mark-heart"></span>
                </div>
                <div class="app-shell__brand-copy">
                    <strong>LifeLink</strong>
                    <span>@yield('workspace_label', 'Role-aware healthcare workspace')</span>
                </div>
            </a>

            <div class="app-shell__sidebar-card">
                <strong>Connected Care Workspace</strong>
                <p>Shared shell for hospital operations, bedside care, patient self-service, and Blood Bank coordination.</p>
            </div>

            <div class="app-shell__nav-caption">Role Navigation</div>
            <nav class="app-shell__nav" aria-label="Primary">
                @yield('sidebar_nav')
            </nav>

            <div class="app-shell__sidebar-meta">
                <div class="app-shell__nav-caption">Operational Notes</div>
                @yield('sidebar')
                <div class="app-shell__sidebar-card">
                    <strong>Workspace Safety</strong>
                    <p>Production screens prioritize patient context, status clarity, and guided actions over raw system output.</p>
                </div>
            </div>
        </aside>

        <main class="app-shell__main">
            <header class="app-shell__topbar">
                <div class="app-shell__topbar-left">
                    <button class="app-shell__nav-toggle" type="button" aria-label="Open navigation" data-shell-toggle>
                        <span aria-hidden="true"></span>
                    </button>

                    <div class="app-shell__topbar-copy">
                        <span class="app-shell__topbar-label">@yield('topbar_badge', 'Clinical Workspace')</span>
                        <strong id="shell-workspace-title">@yield('meta_title', 'Healthcare Workspace')</strong>
                        @if(trim($__env->yieldContent('meta_copy', '')) !== '')
                            <p>@yield('meta_copy')</p>
                        @endif
                    </div>
                </div>

                <div class="app-shell__topbar-actions">
                    @yield('top_actions')
                    <button class="app-shell__icon-button" type="button" aria-label="Notifications">
                        <span class="app-shell__icon-bell" aria-hidden="true"></span>
                        <span class="app-shell__icon-dot" aria-hidden="true"></span>
                    </button>

                    <div class="app-shell__profile">
                        <div class="app-shell__profile-avatar" id="shell-profile-avatar">LL</div>
                        <div class="app-shell__profile-copy">
                            <strong id="shell-user-name">No active session</strong>
                            <span id="shell-user-meta">No role detected</span>
                        </div>
                    </div>

                    <button class="app-shell__logout" type="button" onclick="window.lifeLinkShell.logout()">Logout</button>
                </div>
            </header>

                <div class="app-shell__content">
                    <section class="app-shell__page-header">
                        <div class="app-shell__page-copy">
                            <div class="app-shell__breadcrumbs" aria-label="Breadcrumb">
                                @hasSection('breadcrumbs')
                                @yield('breadcrumbs')
                            @else
                                <a href="/ui/dashboard">Workspace</a>
                                <span aria-hidden="true">/</span>
                                <span>@yield('title', 'Current Page')</span>
                            @endif
                        </div>

                        @if(trim($__env->yieldContent('hero_badge', '')) !== '')
                            <span class="app-shell__role-pill">@yield('hero_badge')</span>
                        @endif

                            <h1 class="app-shell__page-title">@yield('hero_title', 'Workspace')</h1>

                            @if(trim($__env->yieldContent('hero_description', '')) !== '')
                                <p class="app-shell__page-description">@yield('hero_description')</p>
                            @endif
                        </div>

                        @hasSection('hero_extra')
                            <div class="app-shell__page-extra">
                                @yield('hero_extra')
                            </div>
                        @endif
                    </section>

                @hasSection('section_nav')
                    <div class="ll-section-nav" role="navigation" aria-label="Section navigation">
                        @yield('section_nav')
                    </div>
                @endif

                <div class="app-shell__content-body">
                    @yield('content')
                </div>
            </div>
        </main>
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
                'USER_TOKEN', 'DONOR_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
                'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES',
                'LAST_USED_EMAIL'
            ].forEach((key) => localStorage.removeItem(key));
            window.location.href = '/ui/login';
        },
        getPreferredRole(roles) {
            return this.rolePriority.find((role) => roles.includes(role)) || null;
        }
    };

    (function hydrateShell() {
        const fullName = localStorage.getItem('CURRENT_USER_FULL_NAME') || '';
        const userId = localStorage.getItem('CURRENT_USER_ID') || '';
        const email = localStorage.getItem('CURRENT_USER_EMAIL') || 'No active session';
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        const preferredRole = window.lifeLinkShell.getPreferredRole(roles);
        const identity = fullName || email;
        const metaParts = [];
        const userName = document.getElementById('shell-user-name');
        const userMeta = document.getElementById('shell-user-meta');
        const avatar = document.getElementById('shell-profile-avatar');
        const workspaceTitle = document.getElementById('shell-workspace-title');

        if (userId) metaParts.push(`ID #${userId}`);
        if (preferredRole) metaParts.push(preferredRole);
        if (email) metaParts.push(email);

        if (userName) userName.textContent = identity;
        if (userMeta) userMeta.textContent = metaParts.join(' | ') || 'No role detected';
        if (avatar) {
            const source = (fullName || email || 'LL').trim();
            const parts = source.split(/\s+/).filter(Boolean);
            const initials = parts.length > 1
                ? `${parts[0][0] || ''}${parts[1][0] || ''}`
                : `${source[0] || 'L'}${source[1] || 'L'}`;
            avatar.textContent = initials.toUpperCase();
        }
        if (workspaceTitle && preferredRole && workspaceTitle.textContent === 'Healthcare Workspace') {
            workspaceTitle.textContent = `${preferredRole} Workspace`;
        }
    })();

    (function hydrateShellNavigation() {
        const body = document.body;
        const toggle = document.querySelector('[data-shell-toggle]');
        const closeTargets = Array.from(document.querySelectorAll('[data-shell-close]'));

        function closeNav() {
            body.classList.remove('app-shell-nav-open');
        }

        if (toggle) {
            toggle.addEventListener('click', () => {
                body.classList.toggle('app-shell-nav-open');
            });
        }

        closeTargets.forEach((node) => {
            node.addEventListener('click', closeNav);
        });

        document.querySelectorAll('.app-shell__nav a[href]').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 980) {
                    closeNav();
                }
            });
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
