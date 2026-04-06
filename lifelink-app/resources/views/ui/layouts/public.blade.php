<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | @yield('title', 'Healthcare Experience')</title>
    <link rel="stylesheet" href="{{ asset('css/ui-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui-foundation.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')">
    <div class="ll-public-shell">
        <header class="ll-public-topbar">
            <div class="ll-public-wrap ll-public-bar">
                <a class="ll-public-brand" href="/">
                    <span class="ll-public-brand-mark" aria-hidden="true">LL</span>
                    <span class="ll-public-brand-copy">
                        <strong>LifeLink</strong>
                        <span>@yield('public_tagline', 'Connected hospital operations, patient journeys, and blood response workflows')</span>
                    </span>
                </a>

                <nav class="ll-public-nav" aria-label="Public navigation">
                    @yield('public_nav')
                    <a href="/ui/login" data-guest-only>Login</a>
                    <a class="is-primary ll-hidden" href="/ui/dashboard" data-session-link>Continue session</a>
                    <button class="is-soft ll-hidden" type="button" data-session-logout>Logout</button>
                </nav>
            </div>
        </header>

        <main class="ll-public-main">
            <div class="ll-public-wrap">
                @yield('content')
            </div>
        </main>

        <footer class="ll-public-footer">
            <div class="ll-public-wrap">
                <div class="ll-public-footer-card">
                    <div>
                        <strong>LifeLink Healthcare Platform</strong>
                        <p>Role-aware workflows for hospital operations, clinical care, patient access, donor coordination, and staffing review.</p>
                    </div>

                    <div class="ll-public-actions">
                        <a class="ll-public-chip" href="/">Home</a>
                        <a class="ll-public-chip" href="/ui/login">Login</a>
                        <a class="ll-public-chip is-primary" href="/ui/dashboard">Workspace Hub</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
    window.lifeLinkUi = {
        rolePriority: ['Admin', 'ITWorker', 'Doctor', 'Nurse', 'Donor', 'Applicant', 'Patient'],
        roleDestinations: {
            Admin: '/ui/admin-users',
            ITWorker: '/ui/it-bed-allocation',
            Doctor: '/ui/doctor-dashboard',
            Nurse: '/ui/nurse-dashboard',
            Donor: '/ui/donor-dashboard',
            Applicant: '/ui/applications',
            Patient: '/ui/patient-portal',
        },
        clearSession() {
            [
                'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
                'USER_TOKEN', 'DONOR_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
                'CURRENT_USER_ID', 'CURRENT_USER_FULL_NAME', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES',
                'LAST_USED_EMAIL'
            ].forEach((key) => localStorage.removeItem(key));
        },
        hasSession() {
            const token = localStorage.getItem('USER_TOKEN') || '';
            const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
            return Boolean(token && roles.length);
        },
        preferredDestination() {
            const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
            const preferredRole = this.rolePriority.find((role) => roles.includes(role));
            return preferredRole ? this.roleDestinations[preferredRole] : '/ui/dashboard';
        },
        syncPublicChrome() {
            const hasSession = this.hasSession();
            const destination = this.preferredDestination();

            document.querySelectorAll('[data-guest-only]').forEach((element) => {
                element.classList.toggle('ll-hidden', hasSession);
            });

            document.querySelectorAll('[data-session-link]').forEach((element) => {
                element.classList.toggle('ll-hidden', !hasSession);
                if (hasSession) {
                    element.setAttribute('href', destination);
                }
            });

            document.querySelectorAll('[data-session-logout]').forEach((element) => {
                element.classList.toggle('ll-hidden', !hasSession);
                element.onclick = () => {
                    this.clearSession();
                    window.location.href = '/ui/login';
                };
            });

            document.querySelectorAll('[data-session-destination]').forEach((element) => {
                if (hasSession) {
                    element.setAttribute('href', destination);
                }
            });

            document.querySelectorAll('[data-hide-when-session]').forEach((element) => {
                element.classList.toggle('ll-hidden', hasSession);
            });

            document.querySelectorAll('[data-show-when-session]').forEach((element) => {
                element.classList.toggle('ll-hidden', !hasSession);
            });
        }
    };

    window.lifeLinkUi.syncPublicChrome();
    </script>
    @stack('scripts')
</body>
</html>
