@extends('ui.layouts.public')

@section('title', 'Modern Hospital Management')
@section('public_tagline', 'Hospital coordination for admissions, care delivery, patient access, and blood response')

@section('public_nav')
    <a href="#overview">Overview</a>
    <a href="#services">Services</a>
    <a href="#roles">Workspaces</a>
    <a href="#entry">Get Started</a>
@endsection

@section('content')
    <div class="ll-public-stack">
        <section id="overview" class="ll-public-hero">
            <article class="ll-public-hero-card ll-public-card">
                <span class="ll-public-kicker">Healthcare SaaS</span>
                <h1>One connected hospital workspace for care teams, patients, operations, and blood response.</h1>
                <p class="ll-public-lead">LifeLink turns scattered dashboards into one premium, role-aware experience for admissions, appointments, nursing, patient access, donor coordination, and staffing review.</p>

                <div class="ll-public-actions">
                    <a class="ll-public-chip is-primary" href="/ui/login" data-hide-when-session>Open Shared Login</a>
                    <a class="ll-public-chip" href="/ui/register/patient" data-hide-when-session>Register as Patient</a>
                    <a class="ll-public-chip" href="/ui/register/donor" data-hide-when-session>Register as Donor</a>
                    <a class="ll-public-chip is-soft" href="/ui/dashboard" data-session-destination data-show-when-session>Continue to Workspace</a>
                </div>

                <div class="ll-metric-strip">
                    <div class="ll-metric-card">
                        <small>Core Workspaces</small>
                        <strong>7</strong>
                        <span>Admin, IT, doctor, nurse, patient, donor, and applicant flows now sit inside one visual system.</span>
                    </div>
                    <div class="ll-metric-card">
                        <small>Operational Zones</small>
                        <strong>5</strong>
                        <span>Admissions, care delivery, self-service, staffing review, and Blood Bank response stay connected.</span>
                    </div>
                    <div class="ll-metric-card">
                        <small>Primary Goal</small>
                        <strong>1</strong>
                        <span>Keep urgent hospital work calm, readable, and action-focused without losing role safety.</span>
                    </div>
                </div>
            </article>

            <aside class="ll-public-panel">
                <span class="ll-public-kicker">Design Direction</span>
                <h2>Built to feel calm in urgent moments.</h2>
                <p class="ll-public-copy">The target product language is a modern hospital SaaS shell: soft blue-gray background, white surfaces, rounded cards, large page titles, clear status chips, and guided actions instead of raw technical output.</p>

                <div class="ll-collection-grid" style="margin-top: 20px;">
                    <article class="ll-action-card">
                        <strong>Unified app shell</strong>
                        <p>Stable left navigation, role identity, notifications, and consistent page rhythm across every module.</p>
                    </article>
                    <article class="ll-action-card">
                        <strong>Readable hierarchy</strong>
                        <p>KPI cards, page headers, alert banners, and structured panels surface what matters first.</p>
                    </article>
                    <article class="ll-action-card">
                        <strong>Human-facing workflows</strong>
                        <p>Patient, donor, and applicant experiences use guided forms, progress steps, and clearer next actions.</p>
                    </article>
                    <article class="ll-action-card">
                        <strong>Operational confidence</strong>
                        <p>Admins, doctors, nurses, IT, and Blood Bank users get faster status visibility without backend clutter.</p>
                    </article>
                </div>
            </aside>
        </section>

        <section id="services" class="ll-public-stack">
            <div class="ll-public-panel">
                <span class="ll-public-kicker">Platform Modules</span>
                <h2>Modules organized around real hospital workflows instead of raw system views.</h2>
                <p class="ll-public-copy">Each surface is being translated into cards, filters, timelines, queues, and action panels while keeping the same underlying routes, endpoints, and role boundaries intact.</p>
            </div>

            <div class="ll-collection-grid">
                <article class="ll-action-card">
                    <strong>Admissions and bed operations</strong>
                    <p>IT users can coordinate departments, available beds, admissions, transfers, and discharge from one control surface.</p>
                </article>
                <article class="ll-action-card">
                    <strong>Clinical dashboards</strong>
                    <p>Doctors and nurses see patient context, appointments, vitals, and care actions inside a calmer dashboard shell.</p>
                </article>
                <article class="ll-action-card">
                    <strong>Patient self-service portal</strong>
                    <p>Appointments, records, and blood requests stay grouped into a cleaner patient journey with fewer technical distractions.</p>
                </article>
                <article class="ll-action-card">
                    <strong>Blood response workflows</strong>
                    <p>Donor matching, screening, notifications, and fulfillment stay visible across donor, nurse, and Blood Bank operations.</p>
                </article>
            </div>
        </section>

        <section class="ll-public-grid">
            <article class="ll-public-col-8 ll-public-panel">
                <span class="ll-public-kicker">Product Journey</span>
                <h2>One product, multiple guided journeys.</h2>
                <p class="ll-public-copy">The visual direction stays cohesive while each route adapts to a specific hospital responsibility.</p>

                <div class="ll-step-grid" style="margin-top: 22px;">
                    <article class="ll-step-card">
                        <div class="ll-step-card__number">1</div>
                        <h3>Enter cleanly</h3>
                        <p class="ll-public-copy">Users enter through a shared login or one of the dedicated onboarding paths.</p>
                    </article>
                    <article class="ll-step-card">
                        <div class="ll-step-card__number">2</div>
                        <h3>Land in role context</h3>
                        <p class="ll-public-copy">The workspace hub routes the active session to the correct dashboard and navigation.</p>
                    </article>
                    <article class="ll-step-card">
                        <div class="ll-step-card__number">3</div>
                        <h3>Work from cards</h3>
                        <p class="ll-public-copy">Queues, tables, KPIs, alerts, and action cards replace developer-looking panels.</p>
                    </article>
                </div>
            </article>

            <aside class="ll-public-col-4 ll-public-panel">
                <span class="ll-public-kicker">Signals</span>
                <h2>Visual patterns shared everywhere</h2>
                <div class="ll-chip-row" style="margin-top: 16px;">
                    <span class="ll-chip">KPI cards</span>
                    <span class="ll-chip">Status badges</span>
                    <span class="ll-chip">Filter bars</span>
                    <span class="ll-chip">Alert banners</span>
                    <span class="ll-chip">Steppers</span>
                    <span class="ll-chip">Timelines</span>
                    <span class="ll-chip">Action cards</span>
                    <span class="ll-chip">Cleaner tables</span>
                </div>
            </aside>
        </section>

        <section id="roles" class="ll-public-stack">
            <div class="ll-public-panel">
                <span class="ll-public-kicker">Role Workspaces</span>
                <h2>Each role lands in the tools they actually need.</h2>
            </div>

            <div class="ll-role-grid">
                <article class="ll-role-card">
                    <span class="ll-role-card__meta">Admin</span>
                    <h3>Applicant review and account safety</h3>
                    <p class="ll-public-copy">Review staff applicants, protect access, and provision doctor, nurse, or IT roles without technical clutter.</p>
                </article>
                <article class="ll-role-card">
                    <span class="ll-role-card__meta">Doctor + Nurse</span>
                    <h3>Clinical dashboards</h3>
                    <p class="ll-public-copy">Appointments, patient monitoring, vitals, and care actions stay visible inside a consistent shell.</p>
                </article>
                <article class="ll-role-card">
                    <span class="ll-role-card__meta">Patient</span>
                    <h3>Portal for appointments and records</h3>
                    <p class="ll-public-copy">Patients get a cleaner experience for booking, record review, and blood support requests.</p>
                </article>
                <article class="ll-role-card">
                    <span class="ll-role-card__meta">Donor + Blood Bank</span>
                    <h3>Donation and fulfillment flow</h3>
                    <p class="ll-public-copy">Eligibility, notifications, donor screening, and blood fulfillment align across matching workflows.</p>
                </article>
            </div>
        </section>

        <section id="entry" class="ll-public-grid">
            <article class="ll-public-col-8 ll-public-panel" data-hide-when-session>
                <span class="ll-public-kicker">Start Here</span>
                <h2>Choose the right entry path for the person behind the screen.</h2>
                <p class="ll-public-copy">Existing users use a shared sign-in route. New patient, donor, and applicant users get tailored onboarding so the forms stay focused and supportive instead of overloaded.</p>

                <div class="ll-public-actions">
                    <a class="ll-public-chip is-primary" href="/ui/login">Login</a>
                    <a class="ll-public-chip" href="/ui/register/patient">Patient Registration</a>
                    <a class="ll-public-chip" href="/ui/register/donor">Donor Registration</a>
                    <a class="ll-public-chip" href="/ui/register/applicant">Staff Application</a>
                </div>
            </article>

            <article class="ll-public-col-8 ll-public-panel ll-hidden" data-show-when-session>
                <span class="ll-public-kicker">Session Detected</span>
                <h2>Your workspace is ready.</h2>
                <p class="ll-public-copy">LifeLink found an active session in this browser. Continue to the correct dashboard for your role, or sign out before switching accounts.</p>

                <div class="ll-public-actions">
                    <a class="ll-public-chip is-primary" href="/ui/dashboard" data-session-destination>Continue to Workspace</a>
                    <button class="ll-public-chip" type="button" onclick="window.lifeLinkUi.clearSession(); window.location.reload();">Logout</button>
                </div>
            </article>

            <aside class="ll-public-col-4 ll-public-panel">
                <span class="ll-public-kicker">Quick Access</span>
                <h2>Browse sample modules</h2>
                <p class="ll-public-copy">Open the current role workspaces and operational modules from one lightweight entry card.</p>
                <ul class="ll-public-list">
                    <li><a href="/ui/patient-portal">Patient portal</a></li>
                    <li><a href="/ui/doctor-dashboard">Doctor dashboard</a></li>
                    <li><a href="/ui/nurse-dashboard">Nurse workspace</a></li>
                    <li><a href="/ui/it-bed-allocation">IT operations</a></li>
                    <li><a href="/ui/blood-matching">Blood matching center</a></li>
                </ul>
            </aside>
        </section>
    </div>
@endsection
