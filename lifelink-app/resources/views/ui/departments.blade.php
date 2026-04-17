@extends('ui.layouts.app')

@section('title', 'Department Directory')
@section('public_page', '1')
@section('hide_sidebar', '1')

@section('top_actions')
    <a class="is-active" href="/ui/departments">Directory</a>
@endsection

@push('styles')
<style>
    .dept-directory {
        display: grid;
        gap: 14px;
    }

    .dept-toolbar,
    .dept-card {
        border: 1px solid var(--ui-border);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: var(--ui-shadow-sm);
        padding: 14px;
    }

    .dept-toolbar {
        display: grid;
        gap: 10px;
    }

    .dept-toolbar__title {
        margin: 0;
        font-size: 1.22rem;
    }

    .dept-toolbar__copy {
        margin: 0;
        color: var(--ui-text-muted);
    }

    .dept-search {
        width: 100%;
        border: 1px solid var(--ui-border-strong);
        border-radius: 12px;
        padding: 10px 12px;
        font: inherit;
        color: var(--ui-text);
        background: #fff;
    }

    .dept-search:focus {
        outline: none;
        border-color: var(--ui-primary);
        box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.16);
    }

    .dept-label {
        display: block;
        color: var(--ui-text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .dept-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
    }

    .dept-card h3 {
        margin: 0;
        font-size: 1.02rem;
    }

    .dept-card-head {
        display: grid;
        grid-template-columns: 40px minmax(0, 1fr);
        align-items: center;
        gap: 10px;
    }

    .dept-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        border: 1px solid rgba(3, 105, 161, 0.2);
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(224, 242, 254, 0.78));
        display: grid;
        place-items: center;
        overflow: hidden;
    }

    .dept-card-icon img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        display: block;
    }

    .dept-card p {
        margin: 8px 0 0;
        color: var(--ui-text-muted);
        line-height: 1.55;
        min-height: 46px;
    }

    .dept-meta {
        margin-top: 12px;
        display: grid;
        gap: 6px;
    }

    .dept-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 0.92rem;
    }

    .dept-meta-row strong {
        color: var(--ui-text-soft);
    }

    .dept-chips {
        margin-top: 11px;
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .dept-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--ui-primary) 32%, #cbd5e1);
        background: rgba(224, 242, 254, 0.7);
        color: var(--ui-primary-strong);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 9px;
    }

    .dept-actions {
        margin-top: 14px;
        display: flex;
        justify-content: flex-end;
    }

    .dept-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: var(--ui-primary);
        color: #fff;
        font-weight: 700;
        text-decoration: none;
        min-height: 38px;
        padding: 8px 13px;
    }

    .dept-link:hover {
        background: var(--ui-primary-strong);
    }

    .dept-empty {
        border: 1px dashed var(--ui-border-strong);
        border-radius: 15px;
        background: rgba(248, 250, 252, 0.9);
        padding: 20px;
        text-align: center;
        color: var(--ui-text-muted);
    }

    @media (max-width: 780px) {
        .dept-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <section id="dept-directory-panel" class="dept-directory">
        <article class="dept-toolbar">
            <h2 class="dept-toolbar__title">Department Directory</h2>
            <p class="dept-toolbar__copy">
                Browse department-focused care paths. Select a department to see doctor cards, weekly schedule, and date-based availability.
            </p>
            <label for="departmentSearch" class="dept-label">Search departments</label>
            <input id="departmentSearch" class="dept-search" placeholder="Type department, organ, or service keyword">
        </article>

        <div id="departmentGrid" class="dept-grid"></div>
    </section>
@endsection

@push('scripts')
<script>
const catalogState = {
    departments: [],
    search: '',
    from: '',
};

const departmentGrid = document.getElementById('departmentGrid');
const departmentSearch = document.getElementById('departmentSearch');

function catalogHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function formatRatingSummary(summary) {
    if (!summary || !summary.review_count) {
        return 'No reviews yet';
    }

    const avg = Number(summary.average_rating || 0);
    return `${avg.toFixed(1)} / 5 (${summary.review_count} reviews)`;
}

function filteredDepartments() {
    const term = catalogState.search.trim().toLowerCase();
    if (!term) return catalogState.departments;

    return catalogState.departments.filter((department) => {
        const searchable = [
            department.name,
            department.short_description,
            ...(department.organ_coverage_summary || []),
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        return searchable.includes(term);
    });
}

function renderDepartmentGrid() {
    const rows = filteredDepartments();
    if (!rows.length) {
        departmentGrid.innerHTML = '<div class="dept-empty">No departments matched your search.</div>';
        return;
    }

    departmentGrid.innerHTML = rows.map((department) => {
        const anatomy = window.lifeLinkAnatomy?.resolveDepartmentAsset(department) || {
            src: '/assets/anatomy/human-heart-svgrepo-com.svg',
            alt: 'Department icon',
        };
        const fromQuery = catalogState.from ? `?from=${encodeURIComponent(catalogState.from)}` : '';
        const chips = (department.organ_coverage_summary || [])
            .slice(0, 6)
            .map((item) => `<span class="dept-chip">${catalogHtml(item)}</span>`)
            .join('');

        return `
            <article class="dept-card">
                <div class="dept-card-head">
                    <span class="dept-card-icon"><img src="${catalogHtml(anatomy.src)}" alt="${catalogHtml(anatomy.alt)}"></span>
                    <h3>${catalogHtml(department.name)}</h3>
                </div>
                <p>${catalogHtml(department.short_description || 'Department profile information is available on the detail page.')}</p>
                <div class="dept-meta">
                    <div class="dept-meta-row">
                        <span>Doctors</span>
                        <strong>${Number(department.doctor_count || 0)}</strong>
                    </div>
                    <div class="dept-meta-row">
                        <span>Patient rating</span>
                        <strong>${catalogHtml(formatRatingSummary(department.average_rating_summary))}</strong>
                    </div>
                </div>
                <div class="dept-chips">${chips || '<span class="dept-chip">Coverage pending</span>'}</div>
                <div class="dept-actions">
                    <a class="dept-link" href="/ui/departments/${encodeURIComponent(department.slug)}${fromQuery}">Open Department</a>
                </div>
            </article>
        `;
    }).join('');
}

async function loadDepartmentCatalog() {
    const response = await fetch('/api/public/departments/catalog', {
        headers: { Accept: 'application/json' },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        departmentGrid.innerHTML = `<div class="dept-empty">${catalogHtml(payload.message || 'Could not load departments right now.')}</div>`;
        return;
    }

    catalogState.departments = Array.isArray(payload.departments) ? payload.departments : [];
    renderDepartmentGrid();
}

function bootDepartmentDirectory() {
    const params = new URLSearchParams(window.location.search);
    catalogState.from = params.get('from') || 'directory';

    window.lifeLinkShell?.updateIdentityContext({
        name: localStorage.getItem('CURRENT_USER_FULL_NAME') || localStorage.getItem('CURRENT_USER_EMAIL') || 'Guest user',
        userId: localStorage.getItem('CURRENT_USER_ID') || '-',
        email: localStorage.getItem('CURRENT_USER_EMAIL') || '-',
        role: window.lifeLinkShell?.getPreferredRole(JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]')),
        hideDepartment: true,
    });

    departmentSearch.addEventListener('input', (event) => {
        catalogState.search = event.target.value || '';
        renderDepartmentGrid();
    });

    loadDepartmentCatalog();
}

bootDepartmentDirectory();
</script>
@endpush
