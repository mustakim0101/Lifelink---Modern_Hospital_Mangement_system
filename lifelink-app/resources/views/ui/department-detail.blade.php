@extends('ui.layouts.app')

@section('title', 'Department Detail')
@section('public_page', '1')

@section('top_actions')
    <a href="/ui/departments">Department Directory</a>
@endsection

@section('sidebar_nav')
    <a class="is-active" href="#department-hero" data-panel="department-hero">
        <strong>Overview</strong>
    </a>
    <a href="#department-about" data-panel="department-about">
        <strong>Services</strong>
    </a>
    <a href="#department-doctors" data-panel="department-doctors">
        <strong>Doctors</strong>
    </a>
    <a href="#department-reviews" data-panel="department-reviews">
        <strong>Reviews</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@push('styles')
<style>
    .dept-detail { display: grid; gap: 14px; }
    .dept-panel-view { display: none; }
    .dept-panel { border: 1px solid var(--ui-border); border-radius: 18px; background: rgba(255, 255, 255, 0.95); box-shadow: var(--ui-shadow-sm); padding: 15px; }
    .dept-hero {
        display: grid;
        gap: 12px;
        background:
            radial-gradient(circle at top left, rgba(59, 130, 246, 0.16), transparent 22rem),
            radial-gradient(circle at top right, rgba(13, 148, 136, 0.15), transparent 20rem),
            rgba(255, 255, 255, 0.96);
    }
    .dept-kicker {
        display: inline-flex;
        border-radius: 999px;
        background: rgba(2, 132, 199, 0.13);
        color: var(--ui-primary-strong);
        border: 1px solid rgba(2, 132, 199, 0.22);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 4px 10px;
    }
    .dept-title { margin: 0; font-size: 1.36rem; }
    .dept-title-row {
        display: grid;
        grid-template-columns: 56px minmax(0, 1fr);
        gap: 12px;
        align-items: center;
    }
    .dept-anatomy-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        border: 1px solid rgba(3, 105, 161, 0.2);
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(224, 242, 254, 0.78));
        display: grid;
        place-items: center;
        overflow: hidden;
    }
    .dept-anatomy-icon img {
        width: 34px;
        height: 34px;
        object-fit: contain;
        display: block;
    }
    .dept-copy { margin: 0; color: var(--ui-text-muted); line-height: 1.6; }
    .dept-chip-list { display: flex; flex-wrap: wrap; gap: 7px; }
    .dept-chip {
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--ui-primary) 30%, #cbd5e1);
        background: rgba(224, 242, 254, 0.7);
        color: var(--ui-primary-strong);
        font-size: 0.8rem;
        font-weight: 700;
        padding: 5px 10px;
    }
    .dept-split { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dept-subtitle { margin: 0 0 9px; font-size: 1.02rem; }
    .dept-list { margin: 0; padding-left: 18px; color: var(--ui-text-muted); display: grid; gap: 7px; line-height: 1.5; }
    .dept-doctor-toolbar,
    .dept-review-toolbar { display: grid; gap: 9px; grid-template-columns: repeat(4, minmax(0, 1fr)); align-items: end; }
    .dept-label { display: block; color: var(--ui-text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 5px; }
    .dept-input,
    .dept-select,
    .dept-textarea { width: 100%; border-radius: 11px; border: 1px solid var(--ui-border-strong); background: #fff; color: var(--ui-text); font: inherit; padding: 9px 10px; }
    .dept-input:focus,
    .dept-select:focus,
    .dept-textarea:focus { outline: none; border-color: var(--ui-primary); box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.16); }
    .dept-textarea { min-height: 96px; resize: vertical; }
    .dept-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; border: 0; border-radius: 11px; padding: 9px 14px; font: inherit; font-weight: 700; cursor: pointer; }
    .dept-btn-primary { background: var(--ui-primary); color: #fff; }
    .dept-btn-primary:hover { background: var(--ui-primary-strong); }
    .dept-btn-soft { background: rgba(2, 132, 199, 0.12); color: var(--ui-primary-strong); }
    .dept-doctor-grid { margin-top: 12px; display: grid; gap: 11px; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); }
    .dept-doctor-card { border: 1px solid var(--ui-border); border-radius: 15px; background: rgba(255, 255, 255, 0.96); box-shadow: var(--ui-shadow-sm); padding: 13px; display: grid; gap: 10px; }
    .dept-doctor-head { display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; }
    .dept-doctor-head h4 { margin: 0; font-size: 1rem; }
    .dept-doctor-head p { margin: 4px 0 0; color: var(--ui-text-muted); font-size: 0.88rem; }
    .dept-pill { border-radius: 999px; background: rgba(13, 148, 136, 0.12); color: #0f766e; border: 1px solid rgba(13, 148, 136, 0.2); font-size: 0.75rem; font-weight: 700; padding: 4px 8px; white-space: nowrap; }
    .dept-doctor-metrics { display: grid; gap: 7px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dept-doctor-metrics div { border: 1px solid var(--ui-border); border-radius: 11px; padding: 8px; background: rgba(248, 250, 252, 0.9); }
    .dept-doctor-metrics small { color: var(--ui-text-muted); display: block; font-size: 0.73rem; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 700; }
    .dept-doctor-metrics strong { font-size: 0.9rem; }
    .dept-note { margin: 0; color: var(--ui-text-muted); font-size: 0.85rem; line-height: 1.5; }
    .dept-doctor-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .dept-table-wrap { margin-top: 10px; border: 1px solid var(--ui-border); border-radius: 13px; overflow: auto; background: #fff; }
    .dept-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .dept-table th,
    .dept-table td { text-align: left; padding: 9px 10px; border-bottom: 1px solid var(--ui-border); white-space: nowrap; }
    .dept-table th { font-size: 0.74rem; color: var(--ui-text-muted); text-transform: uppercase; letter-spacing: 0.04em; background: rgba(248, 250, 252, 0.95); position: sticky; top: 0; }
    .dept-status { display: inline-flex; align-items: center; justify-content: center; border-radius: 999px; font-size: 0.75rem; font-weight: 700; min-height: 24px; padding: 0 8px; }
    .dept-status.available { color: #166534; background: rgba(34, 197, 94, 0.16); }
    .dept-status.unavailable { color: #9f1239; background: rgba(244, 63, 94, 0.15); }
    .dept-reviews-list { margin-top: 10px; display: grid; gap: 9px; }
    .dept-review-card { border: 1px solid var(--ui-border); border-radius: 11px; background: rgba(255, 255, 255, 0.95); padding: 10px; }
    .dept-review-card strong { font-size: 0.9rem; }
    .dept-review-card p { margin: 7px 0 0; color: var(--ui-text-muted); line-height: 1.5; }
    .dept-review-meta { margin-top: 7px; color: var(--ui-text-muted); font-size: 0.78rem; }
    .dept-toast-stack { position: fixed; right: 14px; bottom: 14px; display: grid; gap: 8px; z-index: 40; }
    .dept-toast { border-radius: 10px; color: #fff; padding: 9px 11px; font-size: 0.82rem; box-shadow: 0 12px 26px rgba(15, 23, 42, 0.25); }
    .dept-toast.ok { background: #166534; }
    .dept-toast.error { background: #b91c1c; }
    .dept-empty { border: 1px dashed var(--ui-border-strong); border-radius: 12px; background: rgba(248, 250, 252, 0.9); padding: 15px; color: var(--ui-text-muted); text-align: center; }
    @media (max-width: 1000px) {
        .dept-doctor-toolbar,
        .dept-review-toolbar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 760px) {
        .dept-split { grid-template-columns: 1fr; }
        .dept-doctor-metrics { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    <div class="dept-detail">
        <section id="department-hero" class="dept-panel dept-hero dept-panel-view" data-display="grid">
            <a id="departmentBackLink" href="/ui/departments" class="dept-btn dept-btn-soft" style="width: fit-content; text-decoration: none;">Back to directory</a>
            <span class="dept-kicker">Department Profile</span>
            <div class="dept-title-row">
                <span id="departmentAnatomyIcon" class="dept-anatomy-icon"><img src="/assets/anatomy/human-heart-svgrepo-com.svg" alt="Department anatomy icon"></span>
                <h2 id="departmentName" class="dept-title">Loading department...</h2>
            </div>
            <p id="departmentBannerDescription" class="dept-copy"></p>
            <div id="departmentCoverageChips" class="dept-chip-list"></div>
        </section>

        <section id="department-about" class="dept-panel dept-split dept-panel-view" data-display="grid">
            <article>
                <h3 class="dept-subtitle">About this department</h3>
                <p id="departmentOverview" class="dept-copy"></p>
                <h4 class="dept-subtitle" style="margin-top: 12px;">Covered specialties</h4>
                <ul id="departmentCoverageList" class="dept-list"></ul>
            </article>
            <article>
                <h3 class="dept-subtitle">Services</h3>
                <ul id="departmentServicesList" class="dept-list"></ul>
            </article>
        </section>

        <section id="department-doctors" class="dept-panel dept-panel-view" data-display="block">
            <h3 class="dept-subtitle">Doctors in this department</h3>
            <p class="dept-copy">Doctor cards auto-load for this department. Pick an appointment date to see practical day-specific availability before booking.</p>
            <div class="dept-doctor-toolbar">
                <div>
                    <label class="dept-label" for="bookingDate">Appointment date</label>
                    <input id="bookingDate" class="dept-input" type="date">
                </div>
                <div style="display: flex; align-items: end;">
                    <button class="dept-btn dept-btn-primary" onclick="reloadDepartmentDetail()">Refresh doctor data</button>
                </div>
                <div style="grid-column: span 2;">
                    <p class="dept-note">Guests can browse doctors. Booking requires a logged-in Patient account.</p>
                </div>
            </div>
            <div id="departmentDoctorsGrid" class="dept-doctor-grid"></div>
        </section>

        <section id="department-reviews" class="dept-panel dept-panel-view" data-display="block">
            <h3 class="dept-subtitle">Doctor Reviews</h3>
            <div class="dept-review-toolbar">
                <div>
                    <label class="dept-label" for="reviewDoctorSelect">Doctor</label>
                    <select id="reviewDoctorSelect" class="dept-select"></select>
                </div>
                <div>
                    <label class="dept-label">&nbsp;</label>
                    <button class="dept-btn dept-btn-soft" onclick="loadSelectedDoctorReviews()">Refresh reviews</button>
                </div>
                <div style="grid-column: span 2;">
                    <p id="reviewSummaryText" class="dept-note"></p>
                </div>
            </div>

            <div id="reviewFormWrapper" style="margin-top: 10px;"></div>
            <div id="departmentReviewsList" class="dept-reviews-list"></div>
        </section>

    </div>

    <div id="departmentToastStack" class="dept-toast-stack"></div>
@endsection

@push('scripts')
<script>
const detailState = {
    slug: '',
    department: null,
    doctors: [],
    selectedDoctorId: null,
    availabilityPayload: null,
    from: '',
};
const detailPanelIds = ['department-hero', 'department-about', 'department-doctors', 'department-reviews'];
let detailPanelControl = null;

const departmentName = document.getElementById('departmentName');
const departmentAnatomyIcon = document.getElementById('departmentAnatomyIcon');
const departmentBannerDescription = document.getElementById('departmentBannerDescription');
const departmentCoverageChips = document.getElementById('departmentCoverageChips');
const departmentOverview = document.getElementById('departmentOverview');
const departmentCoverageList = document.getElementById('departmentCoverageList');
const departmentServicesList = document.getElementById('departmentServicesList');
const departmentDoctorsGrid = document.getElementById('departmentDoctorsGrid');
const reviewDoctorSelect = document.getElementById('reviewDoctorSelect');
const departmentReviewsList = document.getElementById('departmentReviewsList');
const reviewSummaryText = document.getElementById('reviewSummaryText');
const reviewFormWrapper = document.getElementById('reviewFormWrapper');
const bookingDate = document.getElementById('bookingDate');

function detailHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function showDepartmentToast(message, type = 'ok') {
    const stack = document.getElementById('departmentToastStack');
    const node = document.createElement('div');
    node.className = `dept-toast ${type === 'error' ? 'error' : 'ok'}`;
    node.textContent = message;
    stack.appendChild(node);
    setTimeout(() => node.remove(), 2800);
}

function getToken() {
    return localStorage.getItem('USER_TOKEN') || '';
}

function getRoles() {
    try {
        const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
        return Array.isArray(roles) ? roles : [];
    } catch (error) {
        return [];
    }
}

function hasPatientRole() {
    return getRoles().includes('Patient');
}

function bookingDateValue() {
    return bookingDate.value || '';
}

function departmentSlugFromPath() {
    const segments = window.location.pathname.split('/').filter(Boolean);
    return decodeURIComponent(segments[segments.length - 1] || '');
}

function weekdayCapacityLabel(summary) {
    const items = summary?.daily_capacity_by_weekday || [];
    if (!items.length) return 'No active weekday capacity configured yet.';

    return items.map((item) => `${item.weekday}: ${item.daily_capacity}`).join(' | ');
}

function consultationWindowLabel(windowValue) {
    return windowValue?.label || 'No consultation window';
}

function formatRating(averageRating, reviewCount) {
    if (!reviewCount || averageRating === null || averageRating === undefined) {
        return 'No ratings yet';
    }

    return `${Number(averageRating).toFixed(1)} / 5 (${reviewCount} reviews)`;
}

function bookButtonLabel() {
    if (!getToken()) return 'Login to Book';
    if (!hasPatientRole()) return 'Patient Role Required';
    return 'Book Appointment';
}

function renderDepartmentMeta() {
    const department = detailState.department;
    if (!department) return;
    const anatomy = window.lifeLinkAnatomy?.resolveDepartmentAsset(department) || {
        src: '/assets/anatomy/human-heart-svgrepo-com.svg',
        alt: 'Department icon',
    };

    departmentName.textContent = department.banner_title || department.name;
    if (departmentAnatomyIcon) {
        departmentAnatomyIcon.innerHTML = `<img src="${detailHtml(anatomy.src)}" alt="${detailHtml(anatomy.alt)}">`;
    }
    departmentBannerDescription.textContent = department.banner_description || department.short_description || 'Department profile information is being prepared.';
    departmentOverview.textContent = department.short_description || 'This department provides focused care based on mapped organ coverage and services.';

    const coverage = Array.isArray(department.organ_coverage) ? department.organ_coverage : [];
    const services = Array.isArray(department.services) ? department.services : [];

    departmentCoverageChips.innerHTML = coverage.length
        ? coverage.map((item) => `<span class="dept-chip">${detailHtml(item)}</span>`).join('')
        : '<span class="dept-chip">Coverage pending</span>';

    departmentCoverageList.innerHTML = coverage.length
        ? coverage.map((item) => `<li>${detailHtml(item)}</li>`).join('')
        : '<li>Coverage data not configured yet.</li>';

    departmentServicesList.innerHTML = services.length
        ? services.map((item) => `<li>${detailHtml(item)}</li>`).join('')
        : '<li>Service list not configured yet.</li>';
}

function renderDoctorSelects() {
    const doctors = detailState.doctors;
    if (!doctors.length) {
        reviewDoctorSelect.innerHTML = '<option value="">No doctors</option>';
        return;
    }

    if (!detailState.selectedDoctorId || !doctors.some((doctor) => Number(doctor.doctor_user_id) === Number(detailState.selectedDoctorId))) {
        detailState.selectedDoctorId = Number(doctors[0].doctor_user_id);
    }

    reviewDoctorSelect.innerHTML = doctors.map((doctor) =>
        `<option value="${doctor.doctor_user_id}">${detailHtml(doctor.full_name || 'Doctor')} (${detailHtml(doctor.specialization || 'General')})</option>`
    ).join('');

    reviewDoctorSelect.value = String(detailState.selectedDoctorId);
}

function doctorAvailabilityPreviewSnippet(doctor) {
    const preview = Array.isArray(doctor.availability_preview) ? doctor.availability_preview : [];
    if (!preview.length) {
        return 'No upcoming availability data.';
    }

    return preview.slice(0, 3).map((row) => `${row.date}: ${row.status_label} (${row.remaining_count}/${row.daily_capacity})`).join(' | ');
}

function renderDoctors() {
    const doctors = detailState.doctors;
    if (!doctors.length) {
        departmentDoctorsGrid.innerHTML = '<div class="dept-empty">No active doctors are assigned to this department yet.</div>';
        return;
    }

    departmentDoctorsGrid.innerHTML = doctors.map((doctor) => `
        <article class="dept-doctor-card">
            <div class="dept-doctor-head">
                <div>
                    <h4>${detailHtml(doctor.full_name || 'Doctor')}</h4>
                    <p>${detailHtml(doctor.specialization || 'General specialist')}</p>
                </div>
                <span class="dept-pill">${detailHtml(consultationWindowLabel(doctor.weekly_schedule_summary?.consultation_window))}</span>
            </div>

            <div class="dept-doctor-metrics">
                <div>
                    <small>Experience</small>
                    <strong>${doctor.years_experience !== null ? `${doctor.years_experience} years` : 'Not shared'}</strong>
                </div>
                <div>
                    <small>Consultation Fee</small>
                    <strong>${doctor.consultation_fee !== null ? `BDT ${Number(doctor.consultation_fee).toFixed(2)}` : 'Not shared'}</strong>
                </div>
                <div>
                    <small>Reviews</small>
                    <strong>${detailHtml(formatRating(doctor.average_rating, doctor.review_count))}</strong>
                </div>
                <div>
                    <small>Patients Seen</small>
                    <strong>${Number(doctor.patients_seen_count || 0)}</strong>
                </div>
            </div>

            <p class="dept-note">${detailHtml(doctor.bio || 'Bio not added yet.')}</p>
            <p class="dept-note"><strong>Weekly capacity:</strong> ${detailHtml(weekdayCapacityLabel(doctor.weekly_schedule_summary))}</p>
            <p class="dept-note"><strong>7-day preview:</strong> ${detailHtml(doctorAvailabilityPreviewSnippet(doctor))}</p>
            <p id="selectedAvailability-${doctor.doctor_user_id}" class="dept-note"><strong>Selected date:</strong> Loading...</p>

            <div class="dept-doctor-actions">
                <button class="dept-btn dept-btn-primary" onclick="bookDepartmentAppointment(${doctor.doctor_user_id})">${detailHtml(bookButtonLabel())}</button>
                <button class="dept-btn dept-btn-soft" onclick="focusDoctorReviews(${doctor.doctor_user_id})">View Reviews</button>
            </div>
        </article>
    `).join('');
}

function renderReviewForm() {
    if (!detailState.department || !detailState.selectedDoctorId) {
        reviewFormWrapper.innerHTML = '';
        return;
    }

    if (!getToken()) {
        reviewFormWrapper.innerHTML = `
            <div class="dept-empty">
                Login as a Patient to submit a doctor review.
                <div style="margin-top: 10px;">
                    <a class="dept-btn dept-btn-primary" style="text-decoration: none;" href="/ui/login">Go to Login</a>
                </div>
            </div>
        `;
        return;
    }

    if (!hasPatientRole()) {
        reviewFormWrapper.innerHTML = '<div class="dept-empty">Only users with Patient role can submit reviews.</div>';
        return;
    }

    reviewFormWrapper.innerHTML = `
        <div class="dept-panel" style="padding: 12px; margin-top: 10px;">
            <h4 class="dept-subtitle" style="margin-bottom: 8px;">Submit Review</h4>
            <div class="dept-review-toolbar">
                <div>
                    <label class="dept-label" for="reviewRating">Rating</label>
                    <select id="reviewRating" class="dept-select">
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                </div>
                <div>
                    <label class="dept-label" for="reviewAppointmentId">Appointment ID (optional)</label>
                    <input id="reviewAppointmentId" class="dept-input" type="number" min="1" placeholder="e.g. 123">
                </div>
                <div style="grid-column: span 2;">
                    <label class="dept-label" for="reviewText">Review text (optional)</label>
                    <textarea id="reviewText" class="dept-textarea" maxlength="1000" placeholder="Share your care experience for this doctor"></textarea>
                </div>
            </div>
            <div style="margin-top: 9px;">
                <button class="dept-btn dept-btn-primary" onclick="submitDoctorReview()">Submit Review</button>
            </div>
        </div>
    `;
}

function renderReviews(payload) {
    const reviews = Array.isArray(payload?.reviews) ? payload.reviews : [];
    reviewSummaryText.textContent = `Average rating: ${payload?.average_rating !== null && payload?.average_rating !== undefined ? Number(payload.average_rating).toFixed(1) : 'N/A'} | Total reviews: ${Number(payload?.review_count || 0)}`;

    if (!reviews.length) {
        departmentReviewsList.innerHTML = '<div class="dept-empty">No visible reviews for this doctor yet.</div>';
        return;
    }

    departmentReviewsList.innerHTML = reviews.map((review) => `
        <article class="dept-review-card">
            <strong>${detailHtml(review.patient_display_name || 'Patient')} | ${Number(review.rating || 0)} / 5</strong>
            <p>${detailHtml(review.review_text || 'No written comment provided.')}</p>
            <div class="dept-review-meta">
                ${detailHtml(review.department || '-')}${review.created_at ? ` | ${new Date(review.created_at).toLocaleString()}` : ''}
            </div>
        </article>
    `).join('');
}

function updateDoctorSelectedDateSummary(payload) {
    const doctors = Array.isArray(payload?.doctors) ? payload.doctors : [];
    const selectedDate = bookingDate.value;

    detailState.doctors.forEach((doctor) => {
        const node = document.getElementById(`selectedAvailability-${doctor.doctor_user_id}`);
        if (!node) return;

        const matchedDoctor = doctors.find((item) => Number(item.doctor_user_id) === Number(doctor.doctor_user_id));
        const selectedRow = matchedDoctor?.availability?.find((row) => row.date === selectedDate) || matchedDoctor?.availability?.[0];

        if (!selectedRow) {
            node.innerHTML = '<strong>Selected date:</strong> No availability data.';
            return;
        }

        node.innerHTML = `<strong>Selected date:</strong> ${detailHtml(selectedRow.date)} (${detailHtml(selectedRow.weekday)}) | ${detailHtml(selectedRow.status_label)} | Remaining ${Number(selectedRow.remaining_count || 0)} / ${Number(selectedRow.daily_capacity || 0)}`;
    });
}

function renderPageNotFound() {
    departmentName.textContent = 'Department not found';
    if (departmentAnatomyIcon) {
        departmentAnatomyIcon.innerHTML = '<img src="/assets/anatomy/human-heart-svgrepo-com.svg" alt="Department icon">';
    }
    departmentBannerDescription.textContent = 'The requested department page is unavailable.';
    departmentCoverageChips.innerHTML = '';
    departmentOverview.textContent = 'Please go back to the department directory and choose another department.';
    departmentCoverageList.innerHTML = '<li>Unavailable</li>';
    departmentServicesList.innerHTML = '<li>Unavailable</li>';
    departmentDoctorsGrid.innerHTML = '<div class="dept-empty">Department detail was not found.</div>';
    departmentReviewsList.innerHTML = '<div class="dept-empty">No reviews available.</div>';
    reviewSummaryText.textContent = 'No review summary available.';
    reviewFormWrapper.innerHTML = '';
}

async function loadSelectedDoctorReviews() {
    const doctorId = Number(reviewDoctorSelect.value || 0);
    detailState.selectedDoctorId = doctorId || null;
    renderReviewForm();

    if (!doctorId) {
        reviewSummaryText.textContent = 'Select a doctor to view reviews.';
        departmentReviewsList.innerHTML = '<div class="dept-empty">No doctor selected.</div>';
        return;
    }

    const response = await fetch(`/api/public/doctors/${doctorId}/reviews?perPage=8`, {
        headers: { Accept: 'application/json' },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        reviewSummaryText.textContent = payload.message || 'Could not load reviews.';
        departmentReviewsList.innerHTML = '<div class="dept-empty">Unable to load reviews now.</div>';
        return;
    }

    renderReviews(payload);
}

async function loadAvailability() {
    if (!detailState.department) return;

    const params = new URLSearchParams({
        startDate: bookingDate.value,
        days: '1',
    });

    const response = await fetch(`/api/public/departments/${encodeURIComponent(detailState.department.slug)}/availability?${params.toString()}`, {
        headers: { Accept: 'application/json' },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        detailState.availabilityPayload = null;
        updateDoctorSelectedDateSummary({ doctors: [] });
        return;
    }

    detailState.availabilityPayload = payload;
    updateDoctorSelectedDateSummary(payload);
}

async function bookDepartmentAppointment(doctorUserId) {
    const token = getToken();
    if (!token) {
        showDepartmentToast('Please login first to submit an appointment request.', 'error');
        window.location.href = '/ui/login';
        return;
    }

    if (!hasPatientRole()) {
        showDepartmentToast('Only Patient role can book appointments from this page.', 'error');
        return;
    }

    const appointmentDate = bookingDateValue();
    if (!appointmentDate) {
        showDepartmentToast('Select an appointment date first.', 'error');
        return;
    }

    const payload = {
        departmentId: Number(detailState.department.id),
        doctorUserId: Number(doctorUserId),
        appointmentDate,
    };

    const response = await fetch('/api/patient/appointments', {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
    });
    const result = await response.json().catch(() => ({}));

    if (!response.ok) {
        showDepartmentToast(result.message || 'Could not submit appointment request.', 'error');
        return;
    }

    const remaining = result?.capacity?.remaining_count;
    showDepartmentToast(
        remaining !== undefined
            ? `Appointment request submitted. Remaining capacity: ${remaining}`
            : 'Appointment request submitted.'
    );
    await loadAvailability();
}

function focusDoctorReviews(doctorUserId) {
    detailState.selectedDoctorId = Number(doctorUserId);
    reviewDoctorSelect.value = String(doctorUserId);
    detailPanelControl?.setActivePanel?.('department-reviews', true);
    loadSelectedDoctorReviews();
}

async function submitDoctorReview() {
    const token = getToken();
    if (!token || !hasPatientRole()) {
        showDepartmentToast('Login with Patient role to submit a review.', 'error');
        return;
    }

    const doctorId = Number(reviewDoctorSelect.value || 0);
    if (!doctorId) {
        showDepartmentToast('Select a doctor first.', 'error');
        return;
    }

    const rating = Number(document.getElementById('reviewRating')?.value || 0);
    const reviewText = document.getElementById('reviewText')?.value || '';
    const appointmentIdRaw = document.getElementById('reviewAppointmentId')?.value || '';

    const payload = {
        rating,
        reviewText,
        departmentId: Number(detailState.department.id),
    };
    if (appointmentIdRaw.trim() !== '') {
        payload.appointmentId = Number(appointmentIdRaw);
    }

    const response = await fetch(`/api/patient/doctors/${doctorId}/reviews`, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
    });
    const result = await response.json().catch(() => ({}));

    if (!response.ok) {
        showDepartmentToast(result.message || 'Review submission failed.', 'error');
        return;
    }

    document.getElementById('reviewText').value = '';
    document.getElementById('reviewAppointmentId').value = '';
    document.getElementById('reviewRating').value = '5';

    showDepartmentToast('Review submitted successfully.');
    await Promise.all([loadSelectedDoctorReviews(), reloadDepartmentDetail(false)]);
}

async function reloadDepartmentDetail(loadAfter = true) {
    if (!detailState.slug) return;

    const response = await fetch(`/api/public/departments/${encodeURIComponent(detailState.slug)}`, {
        headers: { Accept: 'application/json' },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        renderPageNotFound();
        return;
    }

    detailState.department = payload.department || null;
    detailState.doctors = Array.isArray(payload.doctors) ? payload.doctors : [];

    renderDepartmentMeta();
    renderDoctorSelects();
    renderDoctors();
    renderReviewForm();

    if (loadAfter) {
        await Promise.all([loadAvailability(), loadSelectedDoctorReviews()]);
    }
}

function initDateDefaults() {
    const now = new Date();
    const today = now.toISOString().slice(0, 10);
    const tomorrow = new Date(now.getTime() + (24 * 60 * 60 * 1000)).toISOString().slice(0, 10);

    bookingDate.value = tomorrow;
    bookingDate.min = today;
}

function bindEvents() {
    reviewDoctorSelect.addEventListener('change', () => {
        detailState.selectedDoctorId = Number(reviewDoctorSelect.value || 0);
        loadSelectedDoctorReviews();
    });

    bookingDate.addEventListener('change', () => {
        loadAvailability();
    });
}

async function bootDepartmentDetail() {
    const params = new URLSearchParams(window.location.search);
    detailState.from = params.get('from') || 'directory';
    detailState.slug = departmentSlugFromPath();
    initDateDefaults();
    bindEvents();

    detailPanelControl = window.lifeLinkShell?.initPanelNavigation?.({
        panelIds: detailPanelIds,
        defaultPanel: 'department-hero',
        navSelector: '.app-shell__nav a[data-panel]',
    }) || null;

    const backLink = document.getElementById('departmentBackLink');
    if (backLink) {
        const fromWelcome = detailState.from === 'welcome';
        backLink.href = fromWelcome ? '/' : '/ui/departments';
        backLink.textContent = fromWelcome ? 'Back to welcome' : 'Back to directory';
    }

    window.lifeLinkShell?.updateIdentityContext({
        name: localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Guest user',
        userId: localStorage.getItem('CURRENT_USER_ID') || '-',
        email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
        role: window.lifeLinkShell?.getPreferredRole(getRoles()),
        hideDepartment: true,
    });

    if (!detailState.slug) {
        renderPageNotFound();
        return;
    }

    await reloadDepartmentDetail(true);
}

bootDepartmentDetail();
</script>
@endpush
