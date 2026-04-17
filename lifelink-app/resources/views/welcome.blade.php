<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink | Modern Hospital Management</title>
    <link rel="stylesheet" href="/css/ui-system.css">
    <style>
        .welcome-carousel {
            border: 1px solid var(--ui-border);
            border-radius: 26px;
            overflow: hidden;
            position: relative;
            min-height: 420px;
            box-shadow: var(--ui-shadow-lg);
            background: #0f172a;
        }

        .welcome-carousel-track {
            position: relative;
            min-height: 420px;
        }

        .welcome-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s ease;
        }

        .welcome-slide.is-active {
            opacity: 1;
            pointer-events: auto;
        }

        .welcome-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            min-height: 420px;
            filter: saturate(1.02);
        }

        .welcome-slide::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.18), rgba(15, 23, 42, 0.58));
        }

        .welcome-caption {
            position: absolute;
            left: 26px;
            right: 26px;
            bottom: 24px;
            z-index: 2;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.46);
            backdrop-filter: blur(4px);
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.45s ease, transform 0.45s ease;
        }

        .welcome-caption.is-active {
            opacity: 1;
            transform: translateY(0);
        }

        .welcome-actions-card,
        .welcome-services {
            border: 1px solid var(--ui-border);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: var(--ui-shadow-md);
            padding: 18px;
        }

        .welcome-actions-card h2,
        .welcome-services h2 {
            margin: 0;
        }

        .welcome-actions-card p {
            margin: 8px 0 14px;
            color: var(--ui-text-muted);
        }

        .welcome-actions-card .hero-actions {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .welcome-actions-card .button {
            min-height: 46px;
            width: 100%;
            text-align: center;
            white-space: nowrap;
            font-size: 0.9rem;
        }

        .welcome-services-grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .finder-panel .hero-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .finder-panel .hero-actions .button {
            width: 100%;
            min-height: 42px;
            text-align: center;
        }

        .welcome-service {
            border: 1px solid var(--ui-border);
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.92);
            padding: 12px;
        }

        .welcome-service h3 {
            margin: 0;
            font-size: 1rem;
        }

        .welcome-service p {
            margin: 6px 0 0;
            color: var(--ui-text-muted);
            line-height: 1.55;
        }

        @media (max-width: 980px) {
            .welcome-actions-card .hero-actions,
            .welcome-services-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .welcome-carousel,
            .welcome-carousel-track,
            .welcome-slide img {
                min-height: 320px;
            }

            .welcome-caption {
                left: 14px;
                right: 14px;
                bottom: 12px;
            }

            .welcome-actions-card .hero-actions,
            .welcome-services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="welcome-page">
    <header class="topbar topbar--public">
        <div class="shell topbar-inner">
            <div class="brand">
                <div class="brand-mark">LL</div>
                <div class="brand-copy">
                    <strong>LifeLink</strong>
                </div>
            </div>

            <nav class="topnav">
                <a href="/ui/departments">Departments</a>
                <a href="#find-department">Department Finder</a>
                <a href="/about">About</a>
                <a class="cta" href="/ui/login">Login</a>
            </nav>
        </div>
    </header>

    <div class="shell">
        <main class="welcome-main">
            <section class="welcome-carousel" id="overview" aria-label="LifeLink highlights">
                <div class="welcome-carousel-track" id="welcomeCarouselTrack">
                    <article class="welcome-slide is-active" data-caption="Connected care begins with faster, clearer public access.">
                        <img src="/assets/welcome_pg/welcome1.jpg" alt="Modern hospital care team.">
                    </article>
                    <article class="welcome-slide" data-caption="Department discovery, booking, and role dashboards stay in one flow.">
                        <img src="/assets/welcome_pg/welcome2.jpg" alt="Hospital operations and patient care.">
                    </article>
                    <article class="welcome-slide" data-caption="Blood Bank coordination stays linked across patient, nurse, and IT workflows.">
                        <img src="/assets/welcome_pg/welcome3.jpg" alt="Healthcare technology and support systems.">
                    </article>
                </div>
                <div id="welcomeCaption" class="welcome-caption is-active">Connected care begins with faster, clearer public access.</div>
            </section>

            <section class="welcome-actions-card" id="entry">
                <h2>Start your LifeLink access</h2>
                <p>Choose your path to create an account or continue into your workspace.</p>
                <div class="hero-actions">
                    <a class="button primary" href="/ui/register/patient">Register as Patient</a>
                    <a class="button" href="/ui/register/donor">Register as Donor</a>
                    <a class="button" href="/ui/register/applicant">Join Our Team</a>
                    <a class="button primary" href="/ui/login">Login</a>
                </div>
            </section>

            <section class="finder-section" id="find-department" aria-labelledby="finder-title">
                <div class="finder-header">
                    <span class="badge">Department Finder</span>
                    <h2 id="finder-title">Find the right department</h2>
                    <p>Use the anatomy guide to surface the most relevant department path before you continue into LifeLink.</p>
                </div>

                <div class="finder-shell">
                    <article class="anatomy-card">
                        <div class="anatomy-stage">
                            <div class="anatomy-figure">
                                <img src="/assets/anatomy/51152.jpg" alt="Human anatomy illustration with interactive body regions for department guidance.">
                                <button class="finder-hotspot" type="button" data-region="eyes" style="--top: 19.4%; --left: 22.9%; --width: 9.8%; --height: 4.9%;" aria-label="Eyes" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Eyes</span>
                                </button>
                                <button class="finder-hotspot is-active" type="button" data-region="brain" style="--top: 15.9%; --left: 22.9%; --width: 10.6%; --height: 6.4%;" aria-label="Brain" aria-pressed="true" aria-controls="finder-panel">
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
                                <button class="finder-hotspot" type="button" data-region="left-kidney" style="--top: 45.8%; --left: 21.9%; --width: 3.6%; --height: 4.3%;" aria-label="Left kidney" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Left kidney</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="right-kidney" style="--top: 45.1%; --left: 25.7%; --width: 4.1%; --height: 4.8%;" aria-label="Right kidney" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Right kidney</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="intestines" style="--top: 50.9%; --left: 23.1%; --width: 11.1%; --height: 6.8%;" aria-label="Intestines" aria-pressed="false" aria-controls="finder-panel">
                                    <span class="sr-only">Intestines</span>
                                </button>
                                <button class="finder-hotspot" type="button" data-region="bladder" style="--top: 56.6%; --left: 23.1%; --width: 5.3%; --height: 3.7%;" aria-label="Bladder" aria-pressed="false" aria-controls="finder-panel">
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
                            <p id="finder-region-note">Click an organ or support path to lock your recommended department, then continue into LifeLink.</p>
                        </div>

                        <div class="hero-actions">
                            <a id="finder-department-link" class="button primary" href="/ui/departments?from=welcome">View Department</a>
                            <a class="button" href="/ui/departments?from=welcome">Browse All Departments</a>
                            <a class="button" href="/ui/login">Continue to Login</a>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="welcome-services" id="services">
                <h2>What you can do on this platform</h2>
                <div class="welcome-services-grid">
                    <article class="welcome-service">
                        <h3>Public Department Discovery</h3>
                        <p>Browse departments, doctor profiles, ratings, and date-based availability before login.</p>
                    </article>
                    <article class="welcome-service">
                        <h3>Patient Care Flow</h3>
                        <p>Book appointments, view medical records, and submit blood requests from one patient portal.</p>
                    </article>
                    <article class="welcome-service">
                        <h3>Clinical Operations</h3>
                        <p>Doctor, nurse, and IT dashboards coordinate admissions, monitoring, and care transitions.</p>
                    </article>
                    <article class="welcome-service">
                        <h3>Blood Bank Coordination</h3>
                        <p>Donor response, nurse screening, and IT approval and fulfillment are linked end to end.</p>
                    </article>
                    <article class="welcome-service">
                        <h3>Admin Governance</h3>
                        <p>Manage applicant reviews, account status controls, and staff setup from admin workspace.</p>
                    </article>
                    <article class="welcome-service">
                        <h3>Role-aware Routing</h3>
                        <p>Users land on their role overview panel with consistent UI and focused task access.</p>
                    </article>
                </div>
            </section>

            <section class="welcome-impact" aria-label="LifeLink highlights">
                <article class="welcome-impact__item">
                    <strong id="welcomeMetricPatients">--</strong>
                    <span>Patients Served</span>
                </article>
                <article class="welcome-impact__item">
                    <strong id="welcomeMetricStaff">--</strong>
                    <span>Medical Staff</span>
                </article>
                <article class="welcome-impact__item">
                    <strong id="welcomeMetricDonors">--</strong>
                    <span>Active Donors</span>
                </article>
            </section>

            <footer class="welcome-footer">
                <strong>LifeLink</strong>
                <p>&copy; 2026 LifeLink Hospital Management System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <script>
    const welcomeSlides = Array.from(document.querySelectorAll('.welcome-slide'));
    const welcomeCaption = document.getElementById('welcomeCaption');
    let currentWelcomeSlide = 0;
    let welcomeTimer = null;

    function setWelcomeSlide(index) {
        if (!welcomeSlides.length || !welcomeCaption) return;
        const safeIndex = ((index % welcomeSlides.length) + welcomeSlides.length) % welcomeSlides.length;
        currentWelcomeSlide = safeIndex;

        welcomeSlides.forEach((slide, slideIndex) => {
            const active = slideIndex === safeIndex;
            slide.classList.toggle('is-active', active);
        });

        welcomeCaption.classList.remove('is-active');
        window.setTimeout(() => {
            welcomeCaption.textContent = welcomeSlides[safeIndex].dataset.caption || '';
            welcomeCaption.classList.add('is-active');
        }, 180);
    }

    function startWelcomeCarousel() {
        if (welcomeTimer || welcomeSlides.length <= 1) return;
        welcomeTimer = window.setInterval(() => {
            setWelcomeSlide(currentWelcomeSlide + 1);
        }, 4200);
    }

    const finderHotspots = Array.from(document.querySelectorAll('.finder-hotspot'));
    const finderSupportButtons = Array.from(document.querySelectorAll('.finder-support-button'));
    const finderRegionTitle = document.getElementById('finder-region-title');
    const finderRegionDescription = document.getElementById('finder-region-description');
    const finderRegionTags = document.getElementById('finder-region-tags');
    const finderRegionNote = document.getElementById('finder-region-note');
    const finderDepartmentLink = document.getElementById('finder-department-link');
    const welcomeMetricPatients = document.getElementById('welcomeMetricPatients');
    const welcomeMetricStaff = document.getElementById('welcomeMetricStaff');
    const welcomeMetricDonors = document.getElementById('welcomeMetricDonors');
    const finderRegions = {
        eyes: {
            name: 'Eyes',
            departments: ['Neurology & Neurosurgery'],
            departmentSlug: 'neurology-neurosurgery',
            description: 'Visual changes, eye-related nerve symptoms, and neurological concerns affecting sight can begin with a neurology-led review.',
            note: 'This route helps surface vision symptoms that may connect to broader nerve or brain-related evaluation.'
        },
        brain: {
            name: 'Brain',
            departments: ['Neurology & Neurosurgery'],
            departmentSlug: 'neurology-neurosurgery',
            description: 'Neurology is the best fit for symptoms centered around the brain, nerves, memory, balance, or severe headaches.',
            note: 'A strong first route for neurological review, imaging decisions, and specialist follow-up inside the hospital workflow.'
        },
        'left-lung': {
            name: 'Left Lung',
            departments: ['Pulmonology', 'General Medicine'],
            departmentSlug: 'pulmonology',
            description: 'Breathing discomfort, coughing, congestion, or chest symptoms affecting the left lung can begin with pulmonology-guided triage.',
            note: 'A focused entry point for respiratory review, baseline assessment, and referral into the next care path.'
        },
        'right-lung': {
            name: 'Right Lung',
            departments: ['Pulmonology', 'General Medicine'],
            departmentSlug: 'pulmonology',
            description: 'Breathing discomfort, coughing, congestion, or chest symptoms affecting the right lung can begin with pulmonology-guided triage.',
            note: 'This keeps respiratory symptoms moving through a clear pulmonary workflow before any specialty escalation.'
        },
        heart: {
            name: 'Heart',
            departments: ['Cardiology & Vascular Medicine'],
            departmentSlug: 'cardiology-vascular-medicine',
            description: 'Cardiology is the right match for chest pressure, palpitations, circulation concerns, or ongoing heart-health monitoring.',
            note: 'This path supports focused cardiac assessment, monitoring, and escalation into specialist-led treatment.'
        },
        liver: {
            name: 'Liver',
            departments: ['Gastroenterology & Hepatology', 'General Medicine'],
            departmentSlug: 'gastroenterology-hepatology',
            description: 'Gastroenterology and hepatology are a practical first stop for liver-area discomfort, fatigue, metabolism concerns, or abnormal clinical findings.',
            note: 'This route supports focused digestive-liver assessment before any additional specialist escalation.'
        },
        stomach: {
            name: 'Stomach',
            departments: ['Gastroenterology & Hepatology', 'General Medicine'],
            departmentSlug: 'gastroenterology-hepatology',
            description: 'Nausea, upper abdominal discomfort, indigestion, or appetite changes can begin with a gastroenterology workup.',
            note: 'This route supports symptom review, initial treatment, and referral when stomach-related issues need deeper investigation.'
        },
        'left-kidney': {
            name: 'Left Kidney',
            departments: ['Nephrology & Urology', 'General Medicine'],
            departmentSlug: 'nephrology-urology',
            description: 'Left-sided flank pain, swelling, hydration issues, or suspected kidney-related symptoms can be triaged through nephrology and urology care.',
            note: 'A balanced entry point for tests, acute symptom review, and internal medicine coordination for kidney-related concerns.'
        },
        'right-kidney': {
            name: 'Right Kidney',
            departments: ['Nephrology & Urology', 'General Medicine'],
            departmentSlug: 'nephrology-urology',
            description: 'Right-sided flank pain, swelling, hydration issues, or suspected kidney-related symptoms can be triaged through nephrology and urology care.',
            note: 'A balanced entry point for tests, acute symptom review, and internal medicine coordination for kidney-related concerns.'
        },
        intestines: {
            name: 'Intestines',
            departments: ['Gastroenterology & Hepatology', 'General Medicine'],
            departmentSlug: 'gastroenterology-hepatology',
            description: 'Bowel discomfort, cramping, digestive irregularity, or lower abdominal symptoms are well suited to gastroenterology triage.',
            note: 'This path helps turn broad digestive concerns into a clear next step with examination and follow-up planning.'
        },
        bladder: {
            name: 'Bladder',
            departments: ['Nephrology & Urology', 'General Medicine'],
            departmentSlug: 'nephrology-urology',
            description: 'Urinary discomfort, lower pelvic pressure, or fluid-balance concerns can start with nephrology and urology support.',
            note: 'Helpful for early assessment, testing coordination, and routing into the right treatment pathway.'
        },
        'hands-arms': {
            name: 'Hands and Arms',
            departments: ['Orthopedics & Musculoskeletal Care'],
            departmentSlug: 'orthopedics-musculoskeletal-care',
            description: 'Hand injuries, arm pain, joint strain, or limited upper-limb movement align well with orthopedic evaluation.',
            note: 'This route is useful for musculoskeletal issues affecting the shoulders, arms, wrists, or hands.'
        },
        legs: {
            name: 'Legs',
            departments: ['Orthopedics & Musculoskeletal Care'],
            departmentSlug: 'orthopedics-musculoskeletal-care',
            description: 'Leg pain, sports injuries, gait problems, fractures, or weight-bearing discomfort fit the orthopedic pathway.',
            note: 'A strong first stop for lower-limb injuries, stability concerns, and rehab-oriented treatment planning.'
        },
        'bones-joints': {
            name: 'Bones and Joints',
            departments: ['Orthopedics & Musculoskeletal Care'],
            departmentSlug: 'orthopedics-musculoskeletal-care',
            description: 'Bone pain, joint instability, posture issues, or broader skeletal discomfort align best with orthopedics.',
            note: 'Use this when symptoms feel structural or joint-based rather than isolated to the arms or legs alone.'
        },
        'child-care': {
            name: 'Child Care',
            departments: ['Pediatrics'],
            departmentSlug: 'pediatrics',
            description: 'Pediatrics stays available for child wellness, fever monitoring, growth concerns, and age-specific clinical attention.',
            note: 'This gives families a clear path into child-focused care without treating pediatrics like a body-organ overlay.'
        },
        'blood-donation': {
            name: 'Blood and Donation',
            departments: ['Blood Bank'],
            departmentSlug: 'blood-bank',
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
        finderRegionTags.innerHTML = region.departments.map((department) => `<span>${department}</span>`).join('');

        if (finderDepartmentLink) {
            const primaryDepartment = region.departments?.[0] || 'Department';
            const slug = region.departmentSlug || '';
            finderDepartmentLink.href = slug ? `/ui/departments/${slug}?from=welcome` : '/ui/departments?from=welcome';
            finderDepartmentLink.textContent = `View ${primaryDepartment}`;
        }

        finderHotspots.forEach((button) => {
            const isActive = button.dataset.region === regionKey;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });

        finderSupportButtons.forEach((button) => {
            const isActive = button.dataset.region === regionKey;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
        });
    }

    finderHotspots.forEach((button) => {
        button.addEventListener('click', () => {
            renderFinderRegion(button.dataset.region);
        });
    });

    finderSupportButtons.forEach((button) => {
        button.addEventListener('click', () => {
            renderFinderRegion(button.dataset.region);
        });
    });

    function formatMetricCount(value) {
        const numberValue = Number(value || 0);
        return Number.isFinite(numberValue) ? new Intl.NumberFormat().format(numberValue) : '0';
    }

    async function loadWelcomeMetrics() {
        try {
            const response = await fetch('/api/public/welcome/metrics', {
                headers: { Accept: 'application/json' },
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) return;

            if (welcomeMetricPatients) welcomeMetricPatients.textContent = formatMetricCount(payload?.metrics?.patients);
            if (welcomeMetricStaff) welcomeMetricStaff.textContent = formatMetricCount(payload?.metrics?.medical_staff);
            if (welcomeMetricDonors) welcomeMetricDonors.textContent = formatMetricCount(payload?.metrics?.active_donors);
        } catch (error) {
            // Graceful fallback for metric endpoint failures.
        }
    }

    setWelcomeSlide(0);
    startWelcomeCarousel();
    renderFinderRegion('brain');
    loadWelcomeMetrics();

    </script>
</body>
</html>
