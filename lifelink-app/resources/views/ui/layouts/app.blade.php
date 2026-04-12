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
    <div class="app-shell" data-role-theme="@yield('role_theme', 'default')">
        <header class="app-shell__topbar">
            <div class="app-shell__topbar-main">
                <a class="app-shell__brand" href="/">
                    <div class="app-shell__mark">LL</div>
                    <div class="app-shell__brand-copy">
                        <strong>LifeLink Workspace</strong>
                    </div>
                </a>
                <div class="app-shell__session-pill app-shell__session-pill--topbar">
                    <small id="shell-role-label" class="app-shell__session-label">Welcome back,</small>
                    <strong id="shell-user-name">No active session</strong>
                    <span id="shell-user-meta">ID: - | Email: -</span>
                </div>
            </div>

            <nav class="topnav" aria-label="Workspace top navigation">
                <a href="/">Home</a>
                @if(trim($__env->yieldContent('show_prototype_directory')) === '1')
                    <a href="/ui">Prototype Directory</a>
                @endif
                @yield('top_actions')
                <a class="cta" href="#" onclick="window.lifeLinkShell.logout(); return false;">Logout</a>
            </nav>
        </header>

        <section class="app-shell__body">
            <aside class="app-shell__sidebar" id="shell-sidebar">
                <nav class="app-shell__nav" aria-label="Workspace sections">
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
                <div class="app-shell__content-body ui-card ui-card--shell">
                    @yield('content')
                </div>
            </main>
        </section>
    </div>

    <script>
    window.lifeLinkShell = {
        debugStorageKey: 'LL_DASHBOARD_DEBUG',
        rolePriority: ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient'],
        roleLabels: {
            Admin: 'Admin',
            ITWorker: 'IT Worker',
            Doctor: 'Doctor',
            Nurse: 'Nurse',
            Donor: 'Donor',
            Applicant: 'Applicant',
            Patient: 'Patient',
        },
        roleNeedsDepartment: new Set(['ITWorker', 'Doctor', 'Nurse']),
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
        },
        isDebugEnabled() {
            if (new URLSearchParams(window.location.search).get('debug') === '1') return true;
            return localStorage.getItem(this.debugStorageKey) === '1';
        },
        initPanelNavigation(config) {
            const panelIds = Array.isArray(config?.panelIds) ? config.panelIds : [];
            if (!panelIds.length) return { setActivePanel: () => {} };
            const navSelector = config.navSelector || '.app-shell__nav a[data-panel]';
            const navLinks = Array.from(document.querySelectorAll(navSelector));
            const defaultPanel = config.defaultPanel || panelIds[0];
            const onPanelChange = typeof config.onPanelChange === 'function' ? config.onPanelChange : null;

            const setActivePanel = (panelId, updateHistory = true) => {
                const nextPanel = panelIds.includes(panelId) ? panelId : defaultPanel;
                panelIds.forEach((id) => {
                    const panel = document.getElementById(id);
                    if (!panel) return;
                    panel.style.display = id === nextPanel ? (panel.dataset.display || 'block') : 'none';
                });
                navLinks.forEach((link) => {
                    const target = link.dataset.panel || (link.getAttribute('href') || '').replace('#', '');
                    link.classList.toggle('is-active', target === nextPanel);
                });
                if (updateHistory) history.replaceState(null, '', `#${nextPanel}`);
                if (onPanelChange) onPanelChange(nextPanel);
            };

            navLinks.forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const panelId = link.dataset.panel || (link.getAttribute('href') || '').replace('#', '');
                    if (!panelIds.includes(panelId)) return;
                    setActivePanel(panelId, true);
                });
            });

            const initialHash = (window.location.hash || '').replace('#', '');
            setActivePanel(panelIds.includes(initialHash) ? initialHash : defaultPanel, false);
            return { setActivePanel };
        },
        updateIdentityContext(context = {}) {
            const roleLabel = document.getElementById('shell-role-label');
            const userName = document.getElementById('shell-user-name');
            const userMeta = document.getElementById('shell-user-meta');
            if (!roleLabel || !userMeta) return;

            const userId = context.userId || localStorage.getItem('CURRENT_USER_ID') || '';
            const email = context.email || localStorage.getItem('CURRENT_USER_EMAIL') || '-';
            const role = context.role || this.getPreferredRole(JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]'));
            const roleText = this.roleLabels[role] || role || 'No role detected';
            const resolvedName = context.name || localStorage.getItem('CURRENT_USER_FULL_NAME') || email;
            roleLabel.textContent = 'Welcome back,';
            if (userName) userName.textContent = resolvedName;

            const metaParts = [];
            metaParts.push(`ID: ${userId || '-'}`);
            metaParts.push(`Email: ${email || '-'}`);
            metaParts.push(`Role: ${roleText}`);
            if (!context.hideDepartment && this.roleNeedsDepartment.has(role) && context.department) {
                metaParts.push(`Department: ${context.department}`);
            }
            userMeta.textContent = metaParts.join(' | ');
        }
    };

    (function hydrateShell() {
        const fullName = localStorage.getItem('CURRENT_USER_FULL_NAME') || '';
        const userId = localStorage.getItem('CURRENT_USER_ID') || '';
        const email = localStorage.getItem('CURRENT_USER_EMAIL') || 'No active session';
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        const preferredRole = window.lifeLinkShell.getPreferredRole(roles);
        const identity = fullName || email;
        window.lifeLinkShell.updateIdentityContext({
            name: identity,
            userId: userId || '-',
            email,
            role: preferredRole || null,
            department: null,
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>

