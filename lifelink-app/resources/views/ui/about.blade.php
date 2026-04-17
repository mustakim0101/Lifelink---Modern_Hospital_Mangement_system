@extends('ui.layouts.app')

@section('title', 'About LifeLink')
@section('public_page', '1')
@section('hide_sidebar', '1')

@push('styles')
<style>
    .about-shell {
        display: grid;
        gap: 14px;
    }

    .about-card {
        border: 1px solid var(--ui-border);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: var(--ui-shadow-sm);
        padding: 16px;
    }

    .about-title {
        margin: 0;
        font-size: clamp(1.8rem, 2.6vw, 2.4rem);
    }

    .about-copy {
        margin: 8px 0 0;
        color: var(--ui-text-muted);
        line-height: 1.65;
    }

    .about-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .about-member h3 {
        margin: 0;
        font-size: 1.05rem;
    }

    .about-member p {
        margin: 8px 0 0;
        color: var(--ui-text-muted);
        line-height: 1.6;
    }

    .about-tech {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 6px;
        color: var(--ui-text-soft);
    }

    @media (max-width: 900px) {
        .about-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <section class="about-shell">
        <article class="about-card">
            <h1 class="about-title">About LifeLink</h1>
            <p class="about-copy">
                LifeLink is a role-aware hospital management platform that keeps public discovery, patient care, clinical operations, admissions, and blood-bank coordination connected inside one system.
            </p>
        </article>

        <article class="about-card">
            <h2>Project Contributors</h2>
            <div class="about-grid">
                <section class="about-member">
                    <h3>Musa</h3>
                    <p>Core platform setup, authentication, RBAC, and admin-oriented workflow foundations.</p>
                </section>
                <section class="about-member">
                    <h3>Ahbab</h3>
                    <p>Ward, bed, and clinical operation modules including doctor/IT workflow integration.</p>
                </section>
                <section class="about-member">
                    <h3>Shadman</h3>
                    <p>Nurse, patient, donor, and blood bank feature tracks plus workflow expansion support.</p>
                </section>
            </div>
        </article>

        <article class="about-card">
            <h2>Technologies Used</h2>
            <ul class="about-tech">
                <li>Laravel (Blade + routing + middleware)</li>
                <li>PHP API controllers with JWT-based auth</li>
                <li>Microsoft SQL Server for core data storage</li>
                <li>Docker (app, web, and MSSQL services)</li>
                <li>Vanilla JavaScript + modular CSS design system</li>
            </ul>
        </article>
    </section>
@endsection
