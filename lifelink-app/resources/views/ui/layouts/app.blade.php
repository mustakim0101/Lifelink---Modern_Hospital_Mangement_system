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
    @php($isPublicPage = trim($__env->yieldContent('public_page')) === '1')
    @php($hideSidebar = trim($__env->yieldContent('hide_sidebar')) === '1')
    <div class="app-shell {{ $hideSidebar ? 'app-shell--no-sidebar' : '' }} {{ $isPublicPage ? 'app-shell--public-page' : '' }}" data-role-theme="@yield('role_theme', 'default')">
        @if($isPublicPage)
            <header class="topbar topbar--public">
                <div class="shell topbar-inner">
                    <div class="brand">
                        <div class="brand-mark">LL</div>
                        <div class="brand-copy">
                            <strong>LifeLink</strong>
                        </div>
                    </div>
                    <nav class="topnav" aria-label="Public top navigation">
                        <a href="/">Home</a>
                        <a href="/ui/departments">Departments</a>
                        <a href="/about">About</a>
                        @yield('top_actions')
                        <a id="publicNavWorkspace" class="cta" href="/ui/dashboard" hidden>Go to Dashboard</a>
                        <a id="publicNavLogout" href="#" onclick="window.lifeLinkShell.logout(); return false;" hidden>Logout</a>
                        <a id="publicNavLogin" class="cta" href="/ui/login">Login</a>
                    </nav>
                </div>
            </header>
        @else
            <header class="app-shell__topbar">
                <div class="app-shell__topbar-main">
                    <a class="app-shell__brand" href="/">
                        <div class="app-shell__mark">LL</div>
                        <div class="app-shell__brand-copy">
                            <strong>LifeLink</strong>
                            <span>Workspace</span>
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
        @endif

        <section class="app-shell__body">
            @unless($hideSidebar)
                <aside class="app-shell__sidebar" id="shell-sidebar">
                    <nav class="app-shell__nav" aria-label="Workspace sections">
                        @yield('sidebar_nav')
                    </nav>
                    @yield('sidebar')
                </aside>
            @endunless

            <main class="app-shell__content {{ ($isPublicPage && $hideSidebar) ? 'app-shell__content--public-full' : '' }}">
                @hasSection('section_nav')
                    <div class="ll-section-nav" role="navigation" aria-label="Section navigation">
                        @yield('section_nav')
                    </div>
                @endif
                <div class="app-shell__content-body {{ $isPublicPage ? 'app-shell__content-body--public' : 'ui-card ui-card--shell' }}">
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
        getSessionRoles() {
            try {
                const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
                return Array.isArray(roles) ? roles : [];
            } catch (error) {
                return [];
            }
        },
        hasActiveSession() {
            const userToken = localStorage.getItem('USER_TOKEN');
            const adminToken = localStorage.getItem('ADMIN_TOKEN');
            const roles = this.getSessionRoles();
            return Boolean((userToken || adminToken) && roles.length);
        },
        getWorkspaceDestination() {
            const role = this.getPreferredRole(this.getSessionRoles());
            return this.roleDestinations[role] || '/ui/dashboard';
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
        },
        profileStorageKey(role, userId) {
            return `LL_PROFILE_${role || 'User'}_${userId || 'guest'}`;
        },
        readProfileDraft(role, userId) {
            const key = this.profileStorageKey(role, userId);
            try {
                const parsed = JSON.parse(localStorage.getItem(key) || '{}');
                return parsed && typeof parsed === 'object' ? parsed : {};
            } catch (error) {
                return {};
            }
        },
        writeProfileDraft(role, userId, payload) {
            const key = this.profileStorageKey(role, userId);
            localStorage.setItem(key, JSON.stringify({
                ...payload,
                updatedAt: new Date().toISOString(),
            }));
        },
        mountProfileEditor(config = {}) {
            const container = document.getElementById(config.containerId || '');
            if (!container) return;
            const esc = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');

            const role = config.role || this.getPreferredRole(JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]')) || 'User';
            const userId = config.userId || localStorage.getItem('CURRENT_USER_ID') || 'guest';
            const email = localStorage.getItem('CURRENT_USER_EMAIL') || '';
            const fullName = localStorage.getItem('CURRENT_USER_FULL_NAME') || '';
            const draft = this.readProfileDraft(role, userId);
            const initial = {
                fullName: draft.fullName || fullName || '',
                email,
                phone: draft.phone || '',
                dateOfBirth: draft.dateOfBirth || '',
                address: draft.address || '',
                emergencyContact: draft.emergencyContact || '',
                notes: draft.notes || '',
                updatedAt: draft.updatedAt || '',
            };

            container.innerHTML = `
                <section class="ll-profile-card">
                    <div class="ll-profile-card__head">
                        <h2>Personal profile</h2>
                        <p>Review identity details and keep editable contact information up to date.</p>
                    </div>
                    <div class="ll-profile-grid">
                        <label class="ll-profile-field">
                            <span>Full name</span>
                            <input id="${config.containerId}-fullName" type="text" value="${esc(initial.fullName)}">
                        </label>
                        <label class="ll-profile-field">
                            <span>Email</span>
                            <input type="email" value="${esc(initial.email)}" disabled>
                        </label>
                        <label class="ll-profile-field">
                            <span>Phone</span>
                            <input id="${config.containerId}-phone" type="text" value="${esc(initial.phone)}" placeholder="Add phone number">
                        </label>
                        <label class="ll-profile-field">
                            <span>Date of birth</span>
                            <input id="${config.containerId}-dob" type="date" value="${esc(initial.dateOfBirth)}">
                        </label>
                        <label class="ll-profile-field ll-profile-field--wide">
                            <span>Address</span>
                            <input id="${config.containerId}-address" type="text" value="${esc(initial.address)}" placeholder="Add current address">
                        </label>
                        <label class="ll-profile-field ll-profile-field--wide">
                            <span>Emergency contact</span>
                            <input id="${config.containerId}-emergency" type="text" value="${esc(initial.emergencyContact)}" placeholder="Name and phone">
                        </label>
                        <label class="ll-profile-field ll-profile-field--wide">
                            <span>Notes</span>
                            <textarea id="${config.containerId}-notes" rows="3" placeholder="Optional personal notes">${esc(initial.notes)}</textarea>
                        </label>
                    </div>
                    <div class="ll-profile-actions">
                        <button type="button" class="button primary" id="${config.containerId}-save">Save profile details</button>
                        <span class="ll-profile-meta" id="${config.containerId}-meta">${initial.updatedAt ? `Last updated: ${new Date(initial.updatedAt).toLocaleString()}` : 'No profile updates saved yet.'}</span>
                    </div>
                </section>
            `;

            const saveButton = document.getElementById(`${config.containerId}-save`);
            saveButton?.addEventListener('click', () => {
                const next = {
                    fullName: document.getElementById(`${config.containerId}-fullName`)?.value?.trim() || '',
                    phone: document.getElementById(`${config.containerId}-phone`)?.value?.trim() || '',
                    dateOfBirth: document.getElementById(`${config.containerId}-dob`)?.value || '',
                    address: document.getElementById(`${config.containerId}-address`)?.value?.trim() || '',
                    emergencyContact: document.getElementById(`${config.containerId}-emergency`)?.value?.trim() || '',
                    notes: document.getElementById(`${config.containerId}-notes`)?.value?.trim() || '',
                };
                this.writeProfileDraft(role, userId, next);
                if (next.fullName) {
                    localStorage.setItem('CURRENT_USER_FULL_NAME', next.fullName);
                }
                const meta = document.getElementById(`${config.containerId}-meta`);
                if (meta) {
                    meta.textContent = `Last updated: ${new Date().toLocaleString()}`;
                }
                this.updateIdentityContext({
                    name: localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'User',
                    userId,
                    email,
                    role,
                    department: config.department || null,
                    hideDepartment: !!config.hideDepartment,
                });
            });
        }
    };

    window.lifeLinkAnatomy = {
        fallback: {
            src: '/assets/anatomy/human-heart-svgrepo-com.svg',
            alt: 'General medical anatomy icon',
        },
        rules: [
            { icon: '/assets/anatomy/blood-bag-color-icon.svg', alt: 'Blood Bank icon', keywords: ['blood bank', 'donation', 'transfusion', 'blood request'] },
            { icon: '/assets/anatomy/human-heart-svgrepo-com.svg', alt: 'Cardiology icon', keywords: ['cardio', 'vascular', 'circulatory'] },
            { icon: '/assets/anatomy/lungs-svgrepo-com.svg', alt: 'Lungs anatomy icon', keywords: ['pulmo', 'respirat', 'lung'] },
            { icon: '/assets/anatomy/skull-svgrepo-com.svg', alt: 'Neurology anatomy icon', keywords: ['neuro', 'brain', 'skull', 'neurosurgery'] },
            { icon: '/assets/anatomy/kidneys-svgrepo-com.svg', alt: 'Kidneys anatomy icon', keywords: ['nephro', 'uro', 'renal', 'kidney'] },
            { icon: '/assets/anatomy/colon-svgrepo-com.svg', alt: 'Digestive anatomy icon', keywords: ['gastro', 'hepato', 'digest', 'liver', 'stomach', 'intestin', 'colon'] },
            { icon: '/assets/anatomy/spine-svgrepo-com.svg', alt: 'Spine anatomy icon', keywords: ['spine', 'vertebra'] },
            { icon: '/assets/anatomy/knee-svgrepo-com.svg', alt: 'Knee anatomy icon', keywords: ['knee'] },
            { icon: '/assets/anatomy/bone-svgrepo-com.svg', alt: 'Bone anatomy icon', keywords: ['ortho', 'musculo', 'skelet', 'joint', 'bone', 'fracture'] },
            { icon: '/assets/anatomy/eye-svgrepo-com.svg', alt: 'Eye anatomy icon', keywords: ['ophthal', 'eye', 'vision', 'optic'] },
            { icon: '/assets/anatomy/tongue-svgrepo-com.svg', alt: 'Oral and ENT anatomy icon', keywords: ['ent', 'otolaryng', 'tongue', 'oral', 'mouth', 'throat'] },
        ],
        normalize(value) {
            return String(value || '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, ' ')
                .trim();
        },
        resolveDepartmentAsset(department = {}) {
            const tokens = [
                department.name,
                department.slug,
                department.icon_key,
                department.short_description,
                ...(Array.isArray(department.organ_coverage_summary) ? department.organ_coverage_summary : []),
                ...(Array.isArray(department.organ_coverage) ? department.organ_coverage : []),
                ...(Array.isArray(department.services) ? department.services : []),
            ]
                .filter(Boolean)
                .map((entry) => this.normalize(entry))
                .join(' ');

            if (!tokens) return this.fallback;

            const match = this.rules.find((rule) => rule.keywords.some((word) => tokens.includes(this.normalize(word))));
            if (!match) return this.fallback;
            return { src: match.icon, alt: match.alt };
        },
    };

    (function hydrateShell() {
        const isPublicPage = @json($isPublicPage);
        if (isPublicPage) {
            const loginLink = document.getElementById('publicNavLogin');
            const workspaceLink = document.getElementById('publicNavWorkspace');
            const logoutLink = document.getElementById('publicNavLogout');
            if (window.lifeLinkShell.hasActiveSession()) {
                if (loginLink) loginLink.hidden = true;
                if (workspaceLink) {
                    workspaceLink.hidden = false;
                    workspaceLink.href = window.lifeLinkShell.getWorkspaceDestination();
                }
                if (logoutLink) logoutLink.hidden = false;
            } else {
                if (loginLink) loginLink.hidden = false;
                if (workspaceLink) workspaceLink.hidden = true;
                if (logoutLink) logoutLink.hidden = true;
            }
            return;
        }

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
