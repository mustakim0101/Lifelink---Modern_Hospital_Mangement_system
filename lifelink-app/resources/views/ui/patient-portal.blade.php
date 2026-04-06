@extends('ui.layouts.app')

@section('title', 'Patient Portal')
@section('workspace_label', 'Patient care workspace')
@section('hero_badge', 'Patient')
@section('hero_title', 'Appointments, records, and blood support in one calmer patient care flow.')
@section('hero_description', 'The patient module now follows a cleaner, reference-driven layout while keeping the same live booking, records, and blood request functionality underneath.')
@section('meta_title', 'Patient Portal')
@section('meta_copy', 'Patient-first view for scheduling, medical history, and blood request tracking.')

@push('styles')
<style>
    :root {
        --portal-ink: #143047;
        --portal-muted: #64748b;
        --portal-line: rgba(20, 48, 71, 0.12);
        --portal-surface: rgba(255, 255, 255, 0.94);
        --portal-primary: #0f766e;
        --portal-primary-strong: #115e59;
        --portal-sky: #0284c7;
        --portal-accent: #ea580c;
        --portal-ok: #15803d;
        --portal-warn: #b45309;
        --portal-danger: #b91c1c;
        --portal-shadow: 0 22px 50px rgba(20, 48, 71, 0.12);
    }

    html { scroll-behavior: smooth; }
    .portal-ui { display: grid; gap: 24px; color: var(--portal-ink); font-family: "Sora", "Segoe UI", "Trebuchet MS", sans-serif; }
    .portal-hidden { display: none !important; }
    .portal-stack, .portal-chip-row, .portal-btn-row, .portal-filter-row { display: flex; flex-wrap: wrap; gap: 10px; }
    .portal-stack { display: grid; }
    .portal-grid { display: grid; gap: 18px; }
    .portal-grid.two, .portal-hero, .portal-form-grid, .portal-profile-grid, .portal-card-meta { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .portal-card, .portal-alert, .portal-hero { border: 1px solid var(--portal-line); background: var(--portal-surface); box-shadow: var(--portal-shadow); }
    .portal-card { border-radius: 24px; padding: 24px; }
    .portal-card.soft { background: linear-gradient(135deg, rgba(240, 253, 250, 0.96), rgba(255, 255, 255, 0.95)); }
    .portal-card.warm { background: linear-gradient(135deg, rgba(255, 247, 237, 0.96), rgba(255, 255, 255, 0.95)); }
    .portal-alert { border-radius: 18px; padding: 18px 20px; }
    .portal-alert strong { display: block; margin-bottom: 6px; font-size: 15px; }
    .portal-alert p, .portal-copy, .portal-note, .portal-empty p { margin: 0; color: var(--portal-muted); line-height: 1.6; }
    .portal-alert.success { background: linear-gradient(135deg, rgba(240, 253, 244, 0.96), rgba(255, 255, 255, 0.94)); }
    .portal-alert.warning { background: linear-gradient(135deg, rgba(255, 247, 237, 0.96), rgba(255, 255, 255, 0.94)); }
    .portal-alert.danger { background: linear-gradient(135deg, rgba(254, 242, 242, 0.96), rgba(255, 255, 255, 0.94)); }
    .portal-hero { position: relative; overflow: hidden; display: grid; gap: 24px; border-radius: 32px; padding: 30px; background: radial-gradient(circle at top right, rgba(15, 118, 110, 0.16), transparent 32%), radial-gradient(circle at bottom left, rgba(2, 132, 199, 0.12), transparent 30%), linear-gradient(135deg, rgba(248, 252, 255, 0.98), rgba(255, 255, 255, 0.96)); }
    .portal-hero::before { content: ""; position: absolute; width: 220px; height: 220px; right: -70px; top: -100px; border-radius: 999px; background: rgba(15, 118, 110, 0.08); }
    .portal-kicker { display: inline-flex; margin-bottom: 8px; color: var(--portal-primary-strong); font-size: 11px; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase; }
    .portal-hero h2, .portal-section h2, .portal-card h3 { margin: 0; letter-spacing: -0.03em; line-height: 1.1; }
    .portal-hero h2 { font-size: clamp(2rem, 3vw, 3rem); max-width: 11ch; }
    .portal-section h2 { font-size: clamp(1.7rem, 2vw, 2.2rem); }
    .portal-hero p, .portal-section p, .portal-card p { margin: 10px 0 0; color: var(--portal-muted); line-height: 1.6; }
    .portal-stat-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .portal-stat, .portal-summary-card, .portal-mini-card, .portal-detail-card, .portal-notice, .portal-empty { border: 1px solid var(--portal-line); border-radius: 18px; background: rgba(255, 255, 255, 0.86); }
    .portal-stat { padding: 18px; }
    .portal-stat small, .portal-summary-card small, .portal-detail-card small { display: block; color: var(--portal-muted); font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
    .portal-stat strong { display: block; margin-top: 10px; font-size: 30px; letter-spacing: -0.05em; }
    .portal-stat span, .portal-summary-card span, .portal-mini-card span, .portal-detail-card span, .portal-donor span { display: block; margin-top: 6px; color: var(--portal-muted); font-size: 13px; line-height: 1.5; }
    .portal-summary-card, .portal-mini-card, .portal-detail-card, .portal-notice { padding: 16px; }
    .portal-summary-card strong, .portal-mini-card strong, .portal-detail-card strong, .portal-donor strong { display: block; margin-top: 8px; font-size: 15px; }
    .portal-profile-grid, .portal-form-grid, .portal-card-meta { display: grid; gap: 12px; margin-top: 18px; }
    .portal-card-meta { margin-top: 16px; }
    .portal-section { display: grid; gap: 16px; scroll-margin-top: 88px; }
    .portal-section-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
    .portal-section-head .portal-copy { max-width: 64ch; }
    .portal-chip, .portal-filter, .portal-badge { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 0 14px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .portal-chip { background: rgba(20, 48, 71, 0.06); color: var(--portal-ink); }
    .portal-filter { border: 0; background: rgba(20, 48, 71, 0.07); color: var(--portal-ink); cursor: pointer; font: inherit; }
    .portal-filter.active { background: rgba(15, 118, 110, 0.14); color: var(--portal-primary-strong); }
    .portal-badge.success { color: var(--portal-ok); background: rgba(21, 128, 61, 0.12); }
    .portal-badge.pending { color: var(--portal-warn); background: rgba(180, 83, 9, 0.12); }
    .portal-badge.danger { color: var(--portal-danger); background: rgba(185, 28, 28, 0.12); }
    .portal-badge.info { color: var(--portal-sky); background: rgba(2, 132, 199, 0.12); }
    .portal-field { display: grid; gap: 8px; }
    .portal-field.full { grid-column: 1 / -1; }
    .portal-label { color: var(--portal-muted); font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
    .portal-input, .portal-select, .portal-textarea { width: 100%; border-radius: 16px; border: 1px solid rgba(20, 48, 71, 0.14); background: rgba(255, 255, 255, 0.96); color: var(--portal-ink); font: inherit; padding: 14px 16px; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .portal-input:focus, .portal-select:focus, .portal-textarea:focus { border-color: rgba(15, 118, 110, 0.3); box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.1); }
    .portal-textarea { min-height: 120px; resize: vertical; }
    .portal-btn { border: 0; border-radius: 14px; min-height: 46px; padding: 0 18px; font: inherit; font-size: 14px; font-weight: 700; cursor: pointer; }
    .portal-btn[disabled] { opacity: 0.6; pointer-events: none; }
    .portal-btn-main { background: var(--portal-primary); color: #fff; }
    .portal-btn-main:hover { background: var(--portal-primary-strong); }
    .portal-btn-soft { background: rgba(20, 48, 71, 0.08); color: var(--portal-ink); }
    .portal-btn-accent { background: var(--portal-accent); color: #fff; }
    .portal-step-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin: 18px 0; }
    .portal-step { border: 1px solid rgba(20, 48, 71, 0.08); border-radius: 18px; background: rgba(255, 255, 255, 0.82); padding: 14px; }
    .portal-step strong { display: flex; align-items: center; gap: 10px; font-size: 14px; }
    .portal-step em { display: block; margin-top: 8px; color: var(--portal-muted); font-size: 12px; font-style: normal; line-height: 1.5; }
    .portal-step-count { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 999px; background: rgba(20, 48, 71, 0.08); font-size: 12px; }
    .portal-step.active, .portal-step.done { border-color: rgba(15, 118, 110, 0.22); }
    .portal-step.active .portal-step-count { background: rgba(15, 118, 110, 0.14); color: var(--portal-primary-strong); }
    .portal-step.done .portal-step-count { background: var(--portal-primary); color: #fff; }
    .portal-record-grid, .portal-list { display: grid; gap: 14px; }
    .portal-record-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .portal-item { border: 1px solid var(--portal-line); border-radius: 18px; background: rgba(255, 255, 255, 0.88); padding: 18px; }
    .portal-item-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .portal-item-title { margin: 0; font-size: 18px; line-height: 1.25; letter-spacing: -0.02em; }
    .portal-item-subtitle { margin-top: 8px; color: var(--portal-muted); font-size: 14px; line-height: 1.5; }
    .portal-note, .portal-donor-list { margin-top: 16px; }
    .portal-note { border: 1px solid var(--portal-line); border-radius: 16px; background: rgba(248, 250, 252, 0.92); padding: 14px; }
    .portal-empty { padding: 22px; }
    .portal-empty strong { display: block; margin-bottom: 8px; font-size: 17px; }
    .portal-notice ul { margin: 0; padding-left: 18px; color: var(--portal-muted); line-height: 1.7; }
    .portal-donor-list { display: grid; gap: 10px; }
    .portal-donor { border-radius: 14px; background: rgba(15, 118, 110, 0.08); padding: 12px 14px; }
    .portal-pre { margin: 0; }
    .portal-toast-stack { position: fixed; right: 14px; bottom: 14px; display: grid; gap: 10px; z-index: 40; }
    .portal-toast { border-radius: 14px; padding: 14px 16px; color: #fff; font-size: 13px; box-shadow: 0 20px 40px rgba(20, 48, 71, 0.24); }
    .portal-toast.ok { background: linear-gradient(135deg, var(--portal-primary), var(--portal-sky)); }
    .portal-toast.error { background: linear-gradient(135deg, var(--portal-danger), var(--portal-accent)); }
    .portal-clock { font-size: 1.85rem; letter-spacing: -0.04em; }
    .ll-hidden-debug { display: none !important; }
    @media (max-width: 1280px) { .portal-hero, .portal-grid.two, .portal-record-grid { grid-template-columns: 1fr; } }
    @media (max-width: 900px) {
        .portal-form-grid, .portal-profile-grid, .portal-card-meta, .portal-step-grid, .portal-stat-grid { grid-template-columns: 1fr; }
        .portal-section-head, .portal-item-head { flex-direction: column; }
    }
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#portal-overview">
        <strong>Overview</strong>
    </a>
    <a href="#portal-appointments">
        <strong>Appointments</strong>
    </a>
    <a href="#portal-records">
        <strong>Medical Records</strong>
    </a>
    <a href="#portal-blood">
        <strong>Blood Support</strong>
    </a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Portal actions</strong>
        <p>Refresh the patient session and reload live appointments, records, and blood request activity without changing any backend workflow.</p>
        <div class="portal-btn-row">
            <button id="btnStored" class="portal-btn portal-btn-soft" type="button" onclick="restorePatientSession()">Refresh session</button>
            <button id="btnRefresh" class="portal-btn portal-btn-main" type="button" onclick="refreshAll()">Refresh all</button>
        </div>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Patient workflow</strong>
        <p>The patient module is now grouped into overview, appointments, records, and blood support sections while keeping the same endpoints, validations, and payloads.</p>
    </div>

    <div class="app-shell__sidebar-card">
        <strong>Session clock</strong>
        <p>The patient workspace stays inside the shared shell, and the same patient tools still refresh live from the existing APIs.</p>
        <strong id="clockNow" class="portal-clock">--:--</strong>
    </div>
@endsection

@section('section_nav')
    <a href="#portal-overview">Overview</a>
    <a href="#portal-appointments">Appointments</a>
    <a href="#portal-records">Records</a>
    <a href="#portal-blood">Blood Support</a>
@endsection

@section('content')
    <div class="portal-ui">
        <div id="portalSessionAlert" class="portal-alert warning portal-hidden">
            <strong>Patient session required</strong>
            <p>Sign in again to load your appointments, medical records, and blood support activity.</p>
        </div>

        <div id="portalActionAlert" class="portal-alert success portal-hidden">
            <strong id="portalActionTitle">Portal ready</strong>
            <p id="portalActionBody">Recent patient updates will appear here.</p>
        </div>

        <section id="portal-overview" class="portal-hero ll-section">
            <div>
                <span class="portal-kicker">Patient portal</span>
                <h2>Welcome back to your care dashboard.</h2>
                <p>This refreshed patient view keeps the same underlying functionality, but organizes it around the way patients actually book visits, review records, and request blood support.</p>
                <div id="heroIdentityChips" class="portal-chip-row" style="margin-top:18px;">
                    <span class="portal-chip">Loading patient profile</span>
                </div>
                <div class="portal-btn-row" style="margin-top:20px;">
                    <a class="portal-btn portal-btn-main" href="#portal-appointments">Book appointment</a>
                    <a class="portal-btn portal-btn-soft" href="#portal-records">Open records</a>
                    <a class="portal-btn portal-btn-accent" href="#portal-blood">Request blood support</a>
                </div>
            </div>
            <div class="portal-stack" style="gap:14px;">
                <div class="portal-stat-grid">
                    <div class="portal-stat"><small>Medical records</small><strong id="stRecords">0</strong><span>Available in your care history</span></div>
                    <div class="portal-stat"><small>Upcoming visits</small><strong id="stUpcoming">0</strong><span>Booked appointments ahead</span></div>
                    <div class="portal-stat"><small>Blood requests</small><strong id="stRequests">0</strong><span>Support requests on file</span></div>
                    <div class="portal-stat"><small>Account roles</small><strong id="stRoleCount">0</strong><span>Roles on this session</span></div>
                </div>
                <div class="portal-mini-card">
                    <span class="portal-kicker">Care focus</span>
                    <strong id="heroFocusTitle">Loading your care summary</strong>
                    <span id="heroFocusBody">Appointments, records, and blood request highlights will appear here after refresh.</span>
                </div>
                <div id="overviewHighlights" class="portal-grid">
                    <div class="portal-mini-card">
                        <strong>Preparing your patient overview</strong>
                        <span>We are gathering your latest portal activity.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="portal-section">
            <div class="portal-grid two">
                <article class="portal-card">
                    <div class="portal-section-head">
                        <div class="portal-copy">
                            <span class="portal-kicker">Profile summary</span>
                            <h3>Your patient information</h3>
                            <p>Contact, emergency, role, and care summary details tied to the same patient account and session you already use.</p>
                        </div>
                        <span id="patientStatusTag" class="portal-badge success">Active patient</span>
                    </div>
                    <div id="patientSummary" class="portal-profile-grid">
                        <div class="portal-summary-card"><small>Loading</small><strong>Gathering patient details</strong><span>Your summary cards will appear here.</span></div>
                    </div>
                </article>

                <article class="portal-card">
                    <div class="portal-section-head">
                        <div class="portal-copy">
                            <span class="portal-kicker">Quick snapshot</span>
                            <h3>What needs attention</h3>
                            <p>Important patient activity is grouped here so the next useful action is easier to spot.</p>
                        </div>
                    </div>
                    <div id="overviewSnapshotCards" class="portal-list">
                        <div class="portal-empty">
                            <strong>Waiting for live portal data</strong>
                            <p>Refresh the portal to load your next appointment, recent record updates, and blood request status.</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section id="portal-appointments" class="portal-section ll-section">
            <div class="portal-section-head">
                <div class="portal-copy">
                    <span class="portal-kicker">Appointment booking</span>
                    <h2>Book and manage your visits</h2>
                    <p>The appointment flow still posts to the same patient API, but now guides patients with clearer steps and cleaner status cards.</p>
                </div>
                <div id="appointmentFilters" class="portal-filter-row">
                    <button class="portal-filter active" type="button" data-status="" onclick="setAppointmentStatus('')">All</button>
                    <button class="portal-filter" type="button" data-status="Booked" onclick="setAppointmentStatus('Booked')">Booked</button>
                    <button class="portal-filter" type="button" data-status="Cancelled" onclick="setAppointmentStatus('Cancelled')">Cancelled</button>
                    <button class="portal-filter" type="button" data-status="Completed" onclick="setAppointmentStatus('Completed')">Completed</button>
                    <button class="portal-filter" type="button" data-status="NoShow" onclick="setAppointmentStatus('NoShow')">No Show</button>
                </div>
            </div>

            <div class="portal-grid two">
                <article class="portal-card soft">
                    <div class="portal-section-head">
                        <div class="portal-copy">
                            <span class="portal-kicker">New visit</span>
                            <h3>Request an appointment</h3>
                            <p>Department and date-time stay required. Doctor preference remains optional.</p>
                        </div>
                        <span class="portal-chip">Same booking payload preserved</span>
                    </div>

                    <div class="portal-step-grid">
                        <div id="bookingStep1" class="portal-step active"><strong><span class="portal-step-count">1</span> Choose department</strong><em>Select the care area you need first.</em></div>
                        <div id="bookingStep2" class="portal-step"><strong><span class="portal-step-count">2</span> Select doctor</strong><em>Choose a doctor or leave the request unassigned.</em></div>
                        <div id="bookingStep3" class="portal-step"><strong><span class="portal-step-count">3</span> Confirm schedule</strong><em>Pick the date and time for your visit.</em></div>
                    </div>

                    <div class="portal-form-grid">
                        <div class="portal-field">
                            <label class="portal-label" for="appointmentDepartmentId">Department</label>
                            <select id="appointmentDepartmentId" class="portal-select"></select>
                        </div>
                        <div class="portal-field">
                            <label class="portal-label" for="appointmentDoctorUserId">Preferred doctor</label>
                            <select id="appointmentDoctorUserId" class="portal-select"></select>
                        </div>
                        <div class="portal-field full">
                            <label class="portal-label" for="appointmentDateTime">Appointment date and time</label>
                            <input id="appointmentDateTime" class="portal-input" type="datetime-local">
                        </div>
                    </div>

                    <div id="doctorMeta" class="portal-note">Choose a department to see active doctors.</div>
                    <div id="bookingSummary" class="portal-note">Select a department and date-time to prepare your appointment request.</div>

                    <div class="portal-btn-row">
                        <button id="btnBook" class="portal-btn portal-btn-main" type="button" onclick="bookAppointment()">Book appointment</button>
                        <button class="portal-btn portal-btn-soft" type="button" onclick="loadAppointments()">Refresh appointments</button>
                    </div>
                </article>

                <article class="portal-card">
                    <div class="portal-section-head">
                        <div class="portal-copy">
                            <span class="portal-kicker">Visit timeline</span>
                            <h3>My appointments</h3>
                            <p id="appointmentsMeta">Your live appointment cards will appear here after refresh.</p>
                        </div>
                    </div>
                    <div id="appointmentsList" class="portal-list">
                        <div class="portal-empty">
                            <strong>No appointments loaded yet</strong>
                            <p>Refresh the portal or book a new visit to see appointment cards here.</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section id="portal-records" class="portal-section ll-section">
            <div class="portal-section-head">
                <div class="portal-copy">
                    <span class="portal-kicker">Medical history</span>
                    <h2>Review your records more clearly</h2>
                    <p>The same records endpoint is now presented as a patient-friendly care library with summaries and more readable cards.</p>
                </div>
                <input id="recordSearch" class="portal-input" placeholder="Search by diagnosis, plan, clinician, or notes" style="max-width:360px;">
            </div>

            <div id="recordsSummary" class="portal-chip-row">
                <span class="portal-chip">Loading record summary</span>
            </div>
            <div id="recordsList" class="portal-record-grid">
                <div class="portal-empty">
                    <strong>No medical records loaded yet</strong>
                    <p>Refresh the patient portal to load your care history.</p>
                </div>
            </div>
        </section>

        <section id="portal-blood" class="portal-section ll-section">
            <div class="portal-section-head">
                <div class="portal-copy">
                    <span class="portal-kicker">Blood support</span>
                    <h2>Submit and track blood requests</h2>
                    <p>The same blood request workflow now uses a cleaner form and clearer request cards while preserving the existing submission payload.</p>
                </div>
                <div id="bloodFilters" class="portal-filter-row">
                    <button class="portal-filter active" type="button" data-status="" onclick="setBloodStatus('')">All</button>
                    <button class="portal-filter" type="button" data-status="Pending" onclick="setBloodStatus('Pending')">Pending</button>
                    <button class="portal-filter" type="button" data-status="Matched" onclick="setBloodStatus('Matched')">Matched</button>
                    <button class="portal-filter" type="button" data-status="Approved" onclick="setBloodStatus('Approved')">Approved</button>
                    <button class="portal-filter" type="button" data-status="Fulfilled" onclick="setBloodStatus('Fulfilled')">Fulfilled</button>
                    <button class="portal-filter" type="button" data-status="Rejected" onclick="setBloodStatus('Rejected')">Rejected</button>
                    <button class="portal-filter" type="button" data-status="Cancelled" onclick="setBloodStatus('Cancelled')">Cancelled</button>
                </div>
            </div>

            <div class="portal-grid two">
                <article class="portal-card warm">
                    <div class="portal-section-head">
                        <div class="portal-copy">
                            <span class="portal-kicker">New request</span>
                            <h3>Request blood support</h3>
                            <p>Useful context stays available to the backend, but the request flow is now easier to scan and complete.</p>
                        </div>
                    </div>

                    <div id="bloodPatientSummary" class="portal-chip-row">
                        <span class="portal-chip">Loading patient blood profile</span>
                    </div>

                    <div class="portal-notice" style="margin-top:18px;">
                        <strong style="display:block; margin-bottom:10px;">What patients should know</strong>
                        <ul>
                            <li>Blood requests still follow the same approval and matching process already used by the backend.</li>
                            <li>If no department is chosen, the current workflow still falls back to the latest linked department when available.</li>
                            <li>Urgency, component, units, and notes are preserved exactly in the same request flow.</li>
                        </ul>
                    </div>

                    <form id="bloodRequestForm" onsubmit="submitBloodRequest(event)">
                        <div class="portal-form-grid">
                            <div class="portal-field">
                                <label class="portal-label" for="bloodGroupNeeded">Required blood group</label>
                                <select id="bloodGroupNeeded" class="portal-select">
                                    <option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option>
                                    <option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="portal-field">
                                <label class="portal-label" for="bloodComponentType">Blood component</label>
                                <select id="bloodComponentType" class="portal-select">
                                    <option value="WholeBlood">Whole Blood</option><option value="Plasma">Plasma</option><option value="Platelets">Platelets</option><option value="RBC">Red Blood Cells (RBC)</option>
                                </select>
                            </div>
                            <div class="portal-field">
                                <label class="portal-label" for="bloodUnits">Units required</label>
                                <input id="bloodUnits" class="portal-input" type="number" min="1" max="20" value="1">
                            </div>
                            <div class="portal-field">
                                <label class="portal-label" for="bloodUrgency">Urgency level</label>
                                <select id="bloodUrgency" class="portal-select">
                                    <option value="Normal">Normal</option><option value="Urgent" selected>Urgent</option><option value="Emergency">Emergency</option>
                                </select>
                            </div>
                            <div class="portal-field full">
                                <label class="portal-label" for="bloodDepartmentId">Department (optional)</label>
                                <select id="bloodDepartmentId" class="portal-select"></select>
                            </div>
                            <div class="portal-field full">
                                <label class="portal-label" for="bloodNotes">Medical note</label>
                                <textarea id="bloodNotes" class="portal-textarea" placeholder="Share treatment context, surgery timing, or any useful note for the blood bank and care team."></textarea>
                            </div>
                        </div>

                        <div class="portal-btn-row">
                            <button id="btnBlood" class="portal-btn portal-btn-accent" type="submit">Submit blood request</button>
                            <button class="portal-btn portal-btn-soft" type="button" onclick="loadBloodRequests()">Refresh requests</button>
                        </div>
                    </form>
                </article>

                <article class="portal-card">
                    <div class="portal-section-head">
                        <div class="portal-copy">
                            <span class="portal-kicker">Request history</span>
                            <h3>Blood request status</h3>
                            <p id="bloodRequestsMeta">Your recent blood request activity will appear here after refresh.</p>
                        </div>
                    </div>
                    <div id="bloodRequestsList" class="portal-list">
                        <div class="portal-empty">
                            <strong>No blood requests loaded yet</strong>
                            <p>When requests are submitted or updated, their status cards will appear here.</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>

    <div id="toastStack" class="portal-toast-stack"></div>
    <div class="ll-hidden-debug" aria-hidden="true">
        <input id="tokenInput" class="portal-input" placeholder="Hidden patient session input">
        <pre id="out" class="portal-pre"></pre>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const state = {
    appointmentStatus: '',
    bloodStatus: '',
    departments: [],
    doctors: [],
    patient: null,
    stats: {},
    records: [],
    appointments: [],
    bloodRequests: [],
    recordSearch: ''
};

function byId(id) { return document.getElementById(id); }
function portalMessage(data) {
    if (!data) return '';
    if (typeof data === 'string') return data;
    if (typeof data?.message === 'string') return data.message;
    if (typeof data?.error === 'string') return data.error;
    if (typeof data?.status === 'string') return data.status;
    return '';
}
function setPortalAlert(tone, title, body) {
    const root = byId('portalActionAlert');
    root.classList.remove('portal-hidden', 'success', 'warning', 'danger');
    root.classList.add(tone === 'danger' ? 'danger' : tone === 'warning' ? 'warning' : 'success');
    byId('portalActionTitle').textContent = title;
    byId('portalActionBody').textContent = body;
}
function refreshPortalSessionState() {
    const hasSession = !!byId('tokenInput').value.trim();
    byId('portalSessionAlert').classList.toggle('portal-hidden', hasSession);
}
function write(data, config = {}) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
    if (config.skipAlert) return;
    const status = Number(data?.status || 0);
    const tone = config.tone || (!status || status < 300 ? 'success' : status === 401 || status === 403 ? 'warning' : 'danger');
    const title = config.title || (tone === 'danger' ? 'Portal action needs attention' : tone === 'warning' ? 'Portal session check' : 'Portal updated');
    const body = config.body || portalMessage(data?.data || data) || 'The patient portal completed the latest action.';
    setPortalAlert(tone, title, body);
}
function html(value) {
    if (value === null || value === undefined) return '';
    return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
}
function shortText(value, limit = 140) {
    const text = String(value || '').trim();
    if (!text) return 'No additional details shared.';
    return text.length <= limit ? text : `${text.slice(0, Math.max(0, limit - 3)).trimEnd()}...`;
}
function formatDateTime(value, mode = 'full') {
    if (!value) return 'Not available';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return 'Not available';
    const options = mode === 'compact'
        ? { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' }
        : mode === 'date'
            ? { dateStyle: 'long' }
            : { dateStyle: 'full', timeStyle: 'short' };
    return new Intl.DateTimeFormat([], options).format(date);
}

function showToast(message, type = 'ok') {
    const toast = document.createElement('div');
    toast.className = `portal-toast ${type === 'error' ? 'error' : 'ok'}`;
    toast.textContent = message;
    byId('toastStack').appendChild(toast);
    setTimeout(() => toast.remove(), 2600);
}

function setClock() {
    byId('clockNow').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function setButtonBusy(id, busy) {
    const b = byId(id);
    if (!b) return;
    b.disabled = busy;
    b.dataset.label = b.dataset.label || b.textContent;
    b.textContent = busy ? 'Working...' : b.dataset.label;
}

function useStoredUserToken() { byId('tokenInput').value = localStorage.getItem('USER_TOKEN') || ''; refreshPortalSessionState(); }
function restorePatientSession() {
    useStoredUserToken();
    if (!byId('tokenInput').value.trim()) {
        setPortalAlert('warning', 'No stored patient session', 'Sign in again to restore the patient portal on this device.');
        showToast('No stored session found', 'error');
        return;
    }
    setPortalAlert('success', 'Patient session refreshed', 'Using the latest stored patient session for this device.');
    showToast('Session refreshed');
    refreshAll();
}

async function call(path, method = 'GET', body = null, query = null) {
    const token = byId('tokenInput').value.trim();
    if (!token) return { status: 401, data: { message: 'Patient session missing. Sign in again before continuing.' } };

    const q = query ? new URLSearchParams(query).toString() : '';
    const endpoint = `${API}${path}${q ? `?${q}` : ''}`;
    const res = await fetch(endpoint, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: body ? JSON.stringify(body) : undefined
    });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

function statusBadge(status) {
    const value = status || '-';
    let type = 'info';
    if (['Fulfilled', 'Completed', 'Approved'].includes(value)) type = 'success';
    if (['Pending', 'Matched', 'Urgent', 'Emergency'].includes(value)) type = 'pending';
    if (['Cancelled', 'Rejected', 'NoShow'].includes(value)) type = 'danger';
    return `<span class="portal-badge ${type}">${html(value)}</span>`;
}

function setFilterActive(containerId, status) {
    byId(containerId).querySelectorAll('button[data-status]').forEach((b) => {
        b.classList.toggle('active', (b.getAttribute('data-status') || '') === status);
    });
}

function populateDoctorOptions() {
    const departmentId = Number(byId('appointmentDepartmentId').value || 0);
    const doctors = state.doctors.filter((d) => Number(d.department_id) === departmentId);
    const options = ['<option value="">No doctor preference (hospital can assign)</option>']
        .concat(doctors.map((d) => `<option value="${d.user_id}">${html(d.full_name || 'Doctor')} (${html(d.specialization || 'General')})</option>`))
        .join('');
    const select = byId('appointmentDoctorUserId');
    const prev = select.value;
    if (!departmentId) {
        select.innerHTML = '<option value="">Choose a department first</option>';
        select.disabled = true;
        byId('doctorMeta').textContent = 'Pick a department first, then choose a doctor or keep the visit unassigned.';
        updateBookingProgress();
        return;
    }
    select.innerHTML = options;
    select.disabled = false;
    if (prev && doctors.some((d) => String(d.user_id) === prev)) select.value = prev;
    byId('doctorMeta').textContent = doctors.length
        ? `${doctors.length} active doctor(s) in the selected department.`
        : 'No active doctors are listed for this department right now. The appointment can still stay unassigned.';
    updateBookingProgress();
}

function populateDepartmentOptions() {
    const ap = byId('appointmentDepartmentId');
    const bl = byId('bloodDepartmentId');
    const currentAp = ap.value;
    const currentBl = bl.value;
    if (!state.departments.length) {
        ap.innerHTML = '<option value="">No departments</option>';
        bl.innerHTML = '<option value="">Auto</option>';
        byId('appointmentDoctorUserId').innerHTML = '<option value="">Choose a department first</option>';
        return;
    }
    const options = state.departments.map((d) => `<option value="${d.id}">${html(d.dept_name)}</option>`).join('');
    ap.innerHTML = `<option value="">Choose a department</option>${options}`;
    bl.innerHTML = `<option value="">Use latest linked department</option>${options}`;
    ap.value = state.departments.some((d) => String(d.id) === currentAp) ? currentAp : '';
    bl.value = state.departments.some((d) => String(d.id) === currentBl) ? currentBl : '';
    populateDoctorOptions();
}

function setDefaultAppointmentTime() {
    const next = new Date(Date.now() + (24 * 60 * 60 * 1000));
    next.setHours(10, 0, 0, 0);
    const min = new Date(Date.now() + (5 * 60 * 1000));
    byId('appointmentDateTime').min = min.toISOString().slice(0, 16);
    byId('appointmentDateTime').value = next.toISOString().slice(0, 16);
}
function updateBookingProgress() {
    const hasDepartment = !!byId('appointmentDepartmentId').value;
    const hasDateTime = !!byId('appointmentDateTime').value;
    [
        { id: 'bookingStep1', done: hasDepartment, active: !hasDepartment },
        { id: 'bookingStep2', done: hasDepartment, active: hasDepartment && !hasDateTime },
        { id: 'bookingStep3', done: hasDepartment && hasDateTime, active: hasDepartment && hasDateTime }
    ].forEach((step) => {
        const node = byId(step.id);
        node.classList.toggle('done', step.done);
        node.classList.toggle('active', step.active);
    });
    if (!hasDepartment) {
        byId('bookingSummary').textContent = 'Select a department and date-time to prepare your appointment request.';
        return;
    }
    if (!hasDateTime) {
        byId('bookingSummary').textContent = `Department selected: ${byId('appointmentDepartmentId').selectedOptions[0]?.textContent || '-'}. Next, choose the date and time.`;
        return;
    }
    const doctor = byId('appointmentDoctorUserId').selectedOptions[0]?.textContent || 'No doctor preference';
    byId('bookingSummary').textContent = `Ready to submit: ${byId('appointmentDepartmentId').selectedOptions[0]?.textContent || '-'} with ${doctor} on ${formatDateTime(byId('appointmentDateTime').value)}.`;
}

async function loadBookingOptions() {
    const r = await call('/patient/booking-options', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load booking options', 'error'); return; }
    state.departments = r.data?.departments || [];
    state.doctors = r.data?.doctors || [];
    populateDepartmentOptions();
    updateBookingProgress();
}

function hydratePatientLinkedFields() {
    const patient = state.patient || {};
    const bloodGroup = byId('bloodGroupNeeded');
    if (patient.blood_group && !bloodGroup.dataset.userModified) bloodGroup.value = patient.blood_group;
    byId('heroIdentityChips').innerHTML = [
        patient.full_name || 'Patient profile',
        patient.email || 'Email unavailable',
        patient.blood_group ? `${patient.blood_group} blood group` : 'Blood group not set',
        patient.emergency_contact_name ? `Emergency: ${patient.emergency_contact_name}` : 'Emergency contact not set'
    ].map((item) => `<span class="portal-chip">${html(item)}</span>`).join('');
    byId('bloodPatientSummary').innerHTML = [
        patient.full_name ? `Patient: ${patient.full_name}` : 'Patient profile loading',
        patient.blood_group ? `Blood group: ${patient.blood_group}` : 'Blood group not set',
        patient.emergency_contact_phone ? `Emergency phone: ${patient.emergency_contact_phone}` : 'Emergency phone not set'
    ].map((item) => `<span class="portal-chip">${html(item)}</span>`).join('');
}

function renderOverview() {
    const patient = state.patient || {};
    const stats = state.stats || {};
    const roles = Array.isArray(patient.roles) ? patient.roles : [];
    byId('stRecords').textContent = stats.medical_records ?? state.records.length;
    byId('stUpcoming').textContent = stats.upcoming_appointments ?? state.appointments.filter((row) => row.status === 'Booked').length;
    byId('stRequests').textContent = stats.blood_requests ?? state.bloodRequests.length;
    byId('stRoleCount').textContent = roles.length;
    byId('patientStatusTag').className = `portal-badge ${patient.is_active === false ? 'danger' : 'success'}`;
    byId('patientStatusTag').textContent = patient.is_active === false ? 'Inactive profile' : 'Active patient';
    byId('patientSummary').innerHTML = [
        ['Patient name', patient.full_name || 'Not available', patient.email || 'Email unavailable'],
        ['Blood group', patient.blood_group || 'Not set', 'Linked to patient profile'],
        ['Emergency contact', patient.emergency_contact_name || 'Not set', patient.emergency_contact_phone || 'Phone unavailable'],
        ['Account roles', roles.length ? roles.join(', ') : 'No roles found', 'From the active sign-in session'],
        ['Upcoming appointments', String(stats.upcoming_appointments ?? 0), 'Booked visits still ahead'],
        ['Blood requests', String(stats.blood_requests ?? 0), 'Requests submitted from your patient account']
    ].map((item) => `<div class="portal-summary-card"><small>${html(item[0])}</small><strong>${html(item[1])}</strong><span>${html(item[2])}</span></div>`).join('');

    const nextAppointment = state.appointments.filter((row) => row.status === 'Booked' && row.appointment_datetime)
        .sort((a, b) => new Date(a.appointment_datetime) - new Date(b.appointment_datetime))[0] || null;
    const latestRecord = state.records[0] || null;
    const activeBlood = state.bloodRequests.find((row) => ['Pending', 'Matched', 'Approved'].includes(row.status)) || state.bloodRequests[0] || null;
    if (nextAppointment) {
        byId('heroFocusTitle').textContent = 'Your next visit is already on the schedule';
        byId('heroFocusBody').textContent = `${nextAppointment.department || 'Department pending'} on ${formatDateTime(nextAppointment.appointment_datetime)}.`;
    } else if (activeBlood) {
        byId('heroFocusTitle').textContent = 'Blood support is being tracked here';
        byId('heroFocusBody').textContent = `${activeBlood.blood_group_needed || 'Blood group'} ${activeBlood.component_type || 'support'} request is currently ${activeBlood.status || 'in progress'}.`;
    } else {
        byId('heroFocusTitle').textContent = 'Everything is organized in one patient view';
        byId('heroFocusBody').textContent = 'Use the sections below to book visits, review records, and submit blood requests with the same live functionality.';
    }
    byId('overviewHighlights').innerHTML = [
        nextAppointment
            ? ['Next appointment', `${nextAppointment.department || 'Department'} with ${nextAppointment.doctor_name || 'hospital assignment'} on ${formatDateTime(nextAppointment.appointment_datetime, 'compact')}.`]
            : ['Appointments', 'No upcoming booked visit is currently showing. You can request one below.'],
        latestRecord
            ? ['Latest record', `${latestRecord.diagnosis || 'Medical record'} updated ${formatDateTime(latestRecord.record_datetime, 'compact')}.`]
            : ['Medical records', 'Your record timeline will appear here once records are available.'],
        activeBlood
            ? ['Blood request', `${activeBlood.units_required || 0} unit(s) of ${activeBlood.component_type || 'blood'} currently ${activeBlood.status || 'pending'}.`]
            : ['Blood support', 'No active blood request is currently listed for your patient account.']
    ].map((item) => `<div class="portal-mini-card"><strong>${html(item[0])}</strong><span>${html(item[1])}</span></div>`).join('');
    byId('overviewSnapshotCards').innerHTML = [
        nextAppointment
            ? `<div class="portal-item"><div class="portal-item-head"><div><h4 class="portal-item-title">${html(nextAppointment.department || 'Hospital appointment')}</h4><div class="portal-item-subtitle">${html(nextAppointment.doctor_name || 'Doctor assignment will be confirmed by the hospital.')}</div></div>${statusBadge(nextAppointment.status)}</div><div class="portal-card-meta"><div class="portal-detail-card"><small>Schedule</small><strong>${html(formatDateTime(nextAppointment.appointment_datetime))}</strong></div><div class="portal-detail-card"><small>Reference</small><strong>#${html(nextAppointment.id)}</strong></div></div></div>`
            : `<div class="portal-empty"><strong>No upcoming appointment yet</strong><p>Use the booking form below to request your next visit.</p></div>`,
        latestRecord
            ? `<div class="portal-item"><div class="portal-item-head"><div><h4 class="portal-item-title">${html(latestRecord.diagnosis || 'Medical record')}</h4><div class="portal-item-subtitle">${html(shortText(latestRecord.treatment_plan || latestRecord.notes || 'No treatment plan summary available.'))}</div></div><span class="portal-chip">${html(formatDateTime(latestRecord.record_datetime, 'compact'))}</span></div></div>`
            : `<div class="portal-empty"><strong>Your care history will appear here</strong><p>When medical records are available, the most recent update will be highlighted here.</p></div>`,
        activeBlood
            ? `<div class="portal-item"><div class="portal-item-head"><div><h4 class="portal-item-title">${html(`${activeBlood.blood_group_needed || '-'} ${activeBlood.component_type || 'blood request'}`)}</h4><div class="portal-item-subtitle">${html(activeBlood.department || 'Department auto-assigned by system')}</div></div>${statusBadge(activeBlood.status)}</div><div class="portal-card-meta"><div class="portal-detail-card"><small>Units</small><strong>${html(String(activeBlood.units_required || 0))}</strong></div><div class="portal-detail-card"><small>Urgency</small><strong>${html(activeBlood.urgency || 'Normal')}</strong></div></div></div>`
            : `<div class="portal-empty"><strong>No active blood support request</strong><p>If you need blood support, the request form below will keep the same backend workflow and status tracking.</p></div>`
    ].join('');
    hydratePatientLinkedFields();
}

async function loadPortal() {
    const r = await call('/patient/portal', 'GET');
    write(r, { skipAlert: true });
    if (r.status >= 300) {
        if (r.status === 401 || r.status === 403) {
            refreshPortalSessionState();
            setPortalAlert('warning', 'Patient sign-in required', portalMessage(r.data) || 'Sign in again to load your patient dashboard.');
        } else {
            showToast(r.data?.message || 'Could not load portal snapshot', 'error');
        }
        return;
    }
    state.patient = r.data?.patient || {};
    state.stats = r.data?.stats || {};
    renderOverview();
}

function renderMedicalRecords() {
    const search = state.recordSearch.trim().toLowerCase();
    const rows = search ? state.records.filter((row) => `${row.diagnosis || ''} ${row.treatment_plan || ''} ${row.notes || ''} ${row.created_by || ''}`.toLowerCase().includes(search)) : state.records;
    const clinicians = new Set(state.records.map((row) => row.created_by).filter(Boolean)).size;
    const latest = state.records[0];
    byId('recordsSummary').innerHTML = [
        `${state.records.length} total record${state.records.length === 1 ? '' : 's'}`,
        latest ? `Latest update: ${formatDateTime(latest.record_datetime, 'date')}` : 'Latest update: not available',
        `${clinicians} clinician${clinicians === 1 ? '' : 's'} involved`
    ].map((item) => `<span class="portal-chip">${html(item)}</span>`).join('');
    byId('recordsList').innerHTML = rows.length ? rows.map((row) => `
        <div class="portal-item">
            <div class="portal-item-head">
                <div>
                    <h4 class="portal-item-title">${html(row.diagnosis || 'Medical record')}</h4>
                    <div class="portal-item-subtitle">${html(shortText(row.treatment_plan || 'No treatment plan summary provided.'))}</div>
                </div>
                <span class="portal-chip">${html(formatDateTime(row.record_datetime, 'compact'))}</span>
            </div>
            <div class="portal-card-meta">
                <div class="portal-detail-card"><small>Clinician</small><strong>${html(row.created_by || 'Not listed')}</strong></div>
                <div class="portal-detail-card"><small>Reference</small><strong>${html(row.admission_id ? `Admission #${row.admission_id}` : `Record #${row.id}`)}</strong></div>
            </div>
            <div class="portal-note">${html(shortText(row.notes || 'No additional clinical notes were provided for this record.', 220))}</div>
        </div>
    `).join('') : '<div class="portal-empty"><strong>No medical records in this view</strong><p>Try another search term or refresh the patient portal.</p></div>';
}

async function loadMedicalRecords() {
    const r = await call('/patient/medical-records', 'GET', null, { limit: 40 });
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load medical records', 'error'); return; }
    state.records = r.data?.medical_records || [];
    renderMedicalRecords();
    renderOverview();
}

function renderAppointments() {
    const rows = state.appointmentStatus ? state.appointments.filter((row) => row.status === state.appointmentStatus) : state.appointments;
    byId('appointmentsMeta').textContent = rows.length
        ? `${rows.length} appointment${rows.length === 1 ? '' : 's'} ${state.appointmentStatus ? `with status ${state.appointmentStatus}` : 'available in your timeline'}.`
        : state.appointmentStatus ? `No appointments found with status ${state.appointmentStatus}.` : 'No appointments found for your patient account yet.';
    byId('appointmentsList').innerHTML = rows.length ? rows.map((row) => `
        <div class="portal-item">
            <div class="portal-item-head">
                <div>
                    <h4 class="portal-item-title">${html(row.department || 'Department pending')}</h4>
                    <div class="portal-item-subtitle">${html(row.doctor_name || 'Doctor will be assigned by the hospital if no preference is chosen.')}</div>
                </div>
                ${statusBadge(row.status)}
            </div>
            <div class="portal-card-meta">
                <div class="portal-detail-card"><small>Date and time</small><strong>${html(formatDateTime(row.appointment_datetime))}</strong></div>
                <div class="portal-detail-card"><small>Reference</small><strong>Appointment #${html(row.id)}</strong></div>
            </div>
            ${row.cancel_reason ? `<div class="portal-note"><strong style="display:block; margin-bottom:6px; color: var(--portal-ink);">Cancellation note</strong>${html(row.cancel_reason)}</div>` : ''}
            ${row.status === 'Booked' ? `<div class="portal-btn-row"><button class="portal-btn portal-btn-soft" type="button" onclick="cancelAppointment(${row.id})">Cancel appointment</button></div>` : ''}
        </div>
    `).join('') : '<div class="portal-empty"><strong>No appointments in this view</strong><p>Try another status filter or book a new appointment.</p></div>';
}

async function loadAppointments() {
    const r = await call('/patient/appointments', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load appointments', 'error'); return; }
    state.appointments = r.data?.appointments || [];
    renderAppointments();
    renderOverview();
}

function renderBloodRequests() {
    const rows = state.bloodStatus ? state.bloodRequests.filter((row) => row.status === state.bloodStatus) : state.bloodRequests;
    byId('bloodRequestsMeta').textContent = rows.length
        ? `${rows.length} request${rows.length === 1 ? '' : 's'} ${state.bloodStatus ? `currently filtered to ${state.bloodStatus}` : 'linked to your patient account'}.`
        : state.bloodStatus ? `No blood requests found with status ${state.bloodStatus}.` : 'No blood requests have been submitted from this patient profile yet.';
    byId('bloodRequestsList').innerHTML = rows.length ? rows.map((row) => `
        <div class="portal-item">
            <div class="portal-item-head">
                <div>
                    <h4 class="portal-item-title">${html(`${row.blood_group_needed || '-'} ${row.component_type || 'blood request'}`)}</h4>
                    <div class="portal-item-subtitle">${html(row.department || 'Department assigned automatically by the system')}</div>
                </div>
                ${statusBadge(row.status)}
            </div>
            <div class="portal-card-meta">
                <div class="portal-detail-card"><small>Units</small><strong>${html(String(row.units_required || 0))}</strong></div>
                <div class="portal-detail-card"><small>Urgency</small><strong>${html(row.urgency || 'Normal')}</strong></div>
                <div class="portal-detail-card"><small>Requested</small><strong>${html(formatDateTime(row.request_date, 'compact'))}</strong></div>
                <div class="portal-detail-card"><small>Reference</small><strong>${html(row.admission_id ? `Admission #${row.admission_id}` : `Request #${row.id}`)}</strong></div>
            </div>
            ${row.notes ? `<div class="portal-note"><strong style="display:block; margin-bottom:6px; color: var(--portal-ink);">Submitted note</strong>${html(row.notes)}</div>` : ''}
            ${(row.accepted_donors || []).length ? `<div class="portal-donor-list">${row.accepted_donors.map((donor) => `<div class="portal-donor"><strong>${html(donor.donor_name || `Donor #${donor.donor_id}`)}</strong><span>${html(`${donor.donor_blood_group || 'Unknown group'} match accepted${donor.responded_at ? ` on ${formatDateTime(donor.responded_at, 'compact')}` : ''}.`)}</span></div>`).join('')}</div>` : ''}
        </div>
    `).join('') : '<div class="portal-empty"><strong>No blood requests in this view</strong><p>Switch filters or submit a new request to build your blood support timeline.</p></div>';
}

async function loadBloodRequests() {
    const r = await call('/patient/blood-requests', 'GET');
    if (r.status >= 300) { write(r); showToast(r.data?.message || 'Could not load blood requests', 'error'); return; }
    state.bloodRequests = r.data?.blood_requests || [];
    renderBloodRequests();
    renderOverview();
}

async function bookAppointment() {
    const payload = {
        departmentId: Number(byId('appointmentDepartmentId').value || 0),
        doctorUserId: byId('appointmentDoctorUserId').value ? Number(byId('appointmentDoctorUserId').value) : null,
        appointmentDateTime: byId('appointmentDateTime').value
    };
    if (!payload.departmentId || !payload.appointmentDateTime) {
        setPortalAlert('warning', 'Appointment details missing', 'Choose a department and appointment date-time before submitting the booking.');
        showToast('Choose a department and schedule first', 'error');
        return;
    }
    setButtonBusy('btnBook', true);
    const r = await call('/patient/appointments', 'POST', payload);
    setButtonBusy('btnBook', false);
    write(r, { skipAlert: true });
    if (r.status >= 300) {
        setPortalAlert('danger', 'Appointment booking needs attention', portalMessage(r.data) || 'The appointment could not be booked with the selected details.');
        showToast(r.data?.message || 'Appointment booking failed', 'error');
        return;
    }
    setPortalAlert('success', 'Appointment booked', `Your appointment request was saved for ${formatDateTime(payload.appointmentDateTime)}.`);
    showToast('Appointment booked');
    await loadAppointments();
    await loadPortal();
}

async function cancelAppointment(id) {
    const reason = prompt('Cancel reason (optional):', 'Cancelled by patient');
    const payload = reason ? { cancelReason: reason } : {};
    const r = await call(`/patient/appointments/${id}/cancel`, 'POST', payload);
    write(r, { skipAlert: true });
    if (r.status >= 300) {
        setPortalAlert('danger', 'Appointment cancellation needs attention', portalMessage(r.data) || 'The appointment could not be cancelled right now.');
        showToast(r.data?.message || 'Could not cancel appointment', 'error');
        return;
    }
    setPortalAlert('success', 'Appointment cancelled', 'Your appointment timeline has been updated.');
    showToast('Appointment cancelled');
    await loadAppointments();
    await loadPortal();
}

async function submitBloodRequest(event) {
    if (event) event.preventDefault();
    const departmentId = byId('bloodDepartmentId').value.trim();
    const payload = {
        bloodGroup: byId('bloodGroupNeeded').value,
        unitsRequested: Number(byId('bloodUnits').value || 0),
        componentType: byId('bloodComponentType').value,
        urgency: byId('bloodUrgency').value,
        departmentId: departmentId ? Number(departmentId) : null,
        notes: byId('bloodNotes').value.trim() || null
    };
    if (!payload.bloodGroup || !payload.unitsRequested) {
        setPortalAlert('warning', 'Blood request details missing', 'Select the blood group and number of units before submitting the request.');
        showToast('Complete the blood request details', 'error');
        return;
    }
    setButtonBusy('btnBlood', true);
    const r = await call('/patient/blood-requests', 'POST', payload);
    setButtonBusy('btnBlood', false);
    write(r, { skipAlert: true });
    if (r.status >= 300) {
        setPortalAlert('danger', 'Blood request needs attention', portalMessage(r.data) || 'The blood request could not be submitted right now.');
        showToast(r.data?.message || 'Blood request failed', 'error');
        return;
    }
    setPortalAlert('success', 'Blood request submitted', 'Your request has been saved and your blood support timeline is refreshing now.');
    showToast('Blood request submitted');
    byId('bloodNotes').value = '';
    byId('bloodUnits').value = '1';
    await loadBloodRequests();
    await loadPortal();
}

function setAppointmentStatus(status) { state.appointmentStatus = status; setFilterActive('appointmentFilters', status); renderAppointments(); }
function setBloodStatus(status) { state.bloodStatus = status; setFilterActive('bloodFilters', status); renderBloodRequests(); }

async function refreshAll() {
    if (!byId('tokenInput').value.trim()) {
        refreshPortalSessionState();
        setPortalAlert('warning', 'Patient sign-in required', 'Sign in again before loading appointments, records, and blood support activity.');
        return;
    }
    setButtonBusy('btnRefresh', true);
    try {
        await loadBookingOptions();
        await Promise.all([loadPortal(), loadMedicalRecords(), loadAppointments(), loadBloodRequests()]);
        setPortalAlert('success', 'Patient portal refreshed', 'Appointments, records, and blood support activity have been updated from the live patient endpoints.');
        showToast('Portal refreshed');
    } finally {
        setButtonBusy('btnRefresh', false);
    }
}

function boot() {
    setClock();
    setInterval(setClock, 1000);
    useStoredUserToken();
    refreshPortalSessionState();
    setDefaultAppointmentTime();
    setFilterActive('appointmentFilters', '');
    setFilterActive('bloodFilters', '');
    byId('appointmentDepartmentId').addEventListener('change', populateDoctorOptions);
    byId('appointmentDoctorUserId').addEventListener('change', updateBookingProgress);
    byId('appointmentDateTime').addEventListener('input', updateBookingProgress);
    byId('recordSearch').addEventListener('input', (event) => { state.recordSearch = event.target.value; renderMedicalRecords(); });
    byId('bloodGroupNeeded').addEventListener('change', () => { byId('bloodGroupNeeded').dataset.userModified = '1'; });
    refreshAll();
}

boot();
</script>
@endpush
