# LifeLink UI Redesign Reference

## Purpose

This document is the visual and structural reference for the Blade UI redesign of LifeLink.

Use this file as the source of truth for:
- design tokens
- layout direction
- component styling direction
- dashboard structure direction
- public/auth page direction
- shared UI patterns

Important:
- this is a UI redesign guide only
- backend logic must remain unchanged
- routes must remain unchanged
- API calls must remain unchanged
- IDs, JS hooks, localStorage keys, and panel-switch behavior must remain unchanged unless a very small safe refactor is required

The uploaded React/Tailwind design files are inspiration only.
Do not force exact feature parity from those references into the current Laravel Blade project.
Use them for visual quality, hierarchy, spacing, and component patterns only.

---

## Core redesign goals

The current Blade UI should move:
- from prototype-like inline styling
- to a more professional, centralized design system
- from page-by-page visual inconsistency
- to a reusable shared UI architecture
- from large inline `<style>` blocks inside many Blade files
- to shared CSS files and shared reusable patterns

The target feel is:
- modern healthcare SaaS
- professional and deployment-ready
- clean, trustworthy, calm, and structured
- colorful in a controlled way using semantic states
- more modular and component-driven
- better hierarchy and spacing
- stronger cards, headers, sidebars, filters, badges, and dashboard sections

---

## Must preserve existing features

These rules are mandatory:

- Keep backend logic unchanged
- Keep routes unchanged
- Keep controller behavior unchanged
- Keep API endpoints unchanged
- Keep request/response shapes unchanged unless absolutely necessary
- Keep IDs and JS hooks unchanged
- Keep localStorage keys unchanged
- Keep Blood Bank conditional behavior unchanged
- Keep donor-style one-panel-at-a-time navigation where already implemented
- Keep role-aware routing behavior unchanged
- Keep session-aware landing/auth behavior unchanged
- Keep current workflows functional
- Keep existing forms, tables, actions, and business-critical controls functional
- Do not remove debug/API areas if they are part of current workflow
- Use uploaded React/Tailwind files as visual inspiration only, not exact feature parity

---

## Landing page anatomy finder must stay functional

The landing page currently includes a body/anatomy-based department finder.

This feature must be preserved exactly during redesign and style refactor.

Mandatory rules:
- Keep the anatomy finder feature functional
- Keep hotspot coordinates intact unless intentionally re-mapped with full care
- Keep current region IDs intact
- Keep `finderRegions` data intact unless explicitly extending it safely
- Keep current JS hover/focus/click behavior intact
- Keep session-aware login/dashboard actions on the landing page intact
- Do not remove or simplify the anatomy finder during the style-architecture phase
- Styling may be moved to shared CSS if safe
- Visual polish is allowed later, but functionality must remain preserved

---

## Design principles

### 1. Professional over playful
The UI should feel like a production healthcare system, not a student prototype.

### 2. Calm color hierarchy
Use strong color where it communicates action, state, urgency, or emphasis.
Do not over-saturate every section.

### 3. Information hierarchy first
Primary actions, KPIs, titles, filters, and workflows should be visually obvious.

### 4. Reuse patterns
Repeated UI pieces should look like they belong to one system.

### 5. Better scanning
Dashboards should help quick scanning:
- KPI cards first
- grouped related actions
- clear section boundaries
- stronger status indicators
- better spacing between dense areas

### 6. Keep the backend invisible
The redesign should not interfere with controller logic, DOM hooks, or API integration.

---

## Color tokens

Use these as primary visual tokens for the Blade redesign:

```css
:root {
  --ui-bg: #f8fafb;
  --ui-bg-soft: #f1f5f9;
  --ui-surface: #ffffff;
  --ui-surface-soft: #f8fbfd;
  --ui-surface-muted: #f4f8fb;

  --ui-text: #0f172a;
  --ui-text-soft: #1e293b;
  --ui-text-muted: #64748b;

  --ui-border: #e2e8f0;
  --ui-border-strong: #cbd5e1;

  --ui-primary: #0369a1;
  --ui-primary-strong: #075985;
  --ui-primary-soft: #e0f2fe;

  --ui-secondary: #0d9488;
  --ui-secondary-strong: #0f766e;
  --ui-secondary-soft: #ccfbf1;

  --ui-success: #059669;
  --ui-success-strong: #047857;
  --ui-success-soft: #d1fae5;

  --ui-warning: #d97706;
  --ui-warning-strong: #b45309;
  --ui-warning-soft: #fef3c7;

  --ui-danger: #dc2626;
  --ui-danger-strong: #b91c1c;
  --ui-danger-soft: #fee2e2;

  --ui-info: #3b82f6;
  --ui-info-soft: #dbeafe;

  --ui-radius-sm: 10px;
  --ui-radius-md: 16px;
  --ui-radius-lg: 22px;
  --ui-radius-xl: 28px;

  --ui-shadow-sm: 0 6px 18px rgba(15, 23, 42, 0.06);
  --ui-shadow-md: 0 18px 40px rgba(15, 23, 42, 0.10);
  --ui-shadow-lg: 0 24px 60px rgba(15, 23, 42, 0.14);

  --ui-space-1: 6px;
  --ui-space-2: 10px;
  --ui-space-3: 14px;
  --ui-space-4: 18px;
  --ui-space-5: 24px;
  --ui-space-6: 32px;
}




=============================================================================================
======================this part is only for understanding in words===========================
=============================================================================================


Color intent rules
Primary blue = main CTA, page emphasis, key states
Teal/secondary = supporting actions, health-positive workflows
Success green = confirmed, available, eligible, completed
Warning amber = pending, urgent attention, caution
Danger red = emergency, rejected, destructive, blocked
Neutral gray = secondary text, subdued UI, inactive controls
Typography direction

The UI should feel more product-grade and readable.

Rules:

Strong page titles
Short muted subtitles
Small uppercase helper labels for stats/status headers
Body copy should be shorter and clearer than the current verbose prototype style
Avoid very decorative typography
Favor clean, professional readability

Suggested hierarchy:

Page title: large, strong
Section title: medium, semibold
KPI value: large, bold
Label / helper / meta: small, muted, uppercase when useful
Shell layout rules

The authenticated shell should evolve toward a more application-like layout.

Desired structure
left sidebar for main navigation
top bar for session/user/actions
page header at top of content area
main content area with cards, KPI grids, filters, and sections
Shell rules
Sidebar should feel stable and app-like
Sidebar active state must be obvious
Topbar should feel clean and light
Page content should have breathing room
Content width should feel intentional, not stretched randomly
Cards and sections should align on a grid
Sidebar rules
Use clearer active state
Better hover feedback
Better spacing between nav items
Short labels
Avoid noisy extra copy inside nav items unless needed
Preserve existing nav logic and panel links
Topbar rules
Keep actions compact and polished
Session/user area should look production-ready
Do not crowd the header with too many equal-weight actions


Public/auth page direction

Public pages should feel like modern healthcare SaaS marketing/auth experiences.

Landing page
Strong hero hierarchy
Cleaner CTA grouping
Better section rhythm
More polished cards and support sections
Anatomy finder remains and should look premium, not gimmicky
Auth page
Cleaner auth card
Better form spacing
More polished inputs and buttons
Stronger trust and clarity
Workspace hub
Role-aware destination should feel like a proper app entry point
Better primary destination card
Better session summary
Better shortcut cards
Card patterns

Cards should become one of the core reusable visual building blocks.

General card rules
White or near-white surface
Soft border
Medium radius
Soft shadow
Comfortable padding
Clear header/content/footer separation if needed
Example card CSS

.ui-card {
  background: var(--ui-surface);
  border: 1px solid var(--ui-border);
  border-radius: var(--ui-radius-lg);
  box-shadow: var(--ui-shadow-sm);
}

.ui-card--soft {
  background: linear-gradient(180deg, #ffffff, #f8fbfd);
}

.ui-card--primary {
  background: linear-gradient(180deg, #ffffff, var(--ui-primary-soft));
  border-color: rgba(3, 105, 161, 0.18);
}

.ui-card--success {
  background: linear-gradient(180deg, #ffffff, var(--ui-success-soft));
  border-color: rgba(5, 150, 105, 0.18);
}

.ui-card--warning {
  background: linear-gradient(180deg, #ffffff, var(--ui-warning-soft));
  border-color: rgba(217, 119, 6, 0.18);
}

.ui-card--danger {
  background: linear-gradient(180deg, #ffffff, var(--ui-danger-soft));
  border-color: rgba(220, 38, 38, 0.18);
}

KPI card pattern

KPI cards should be visually stronger than current plain stat boxes.

KPI rules
Use compact labels
Large values
Small muted meta text
Allow semantic color variants
Use grid layout
Keep them easy to scan


Example KPI structure

<section class="ui-kpi-grid">
  <article class="ui-kpi ui-kpi--primary">
    <span class="ui-kpi__label">Active Admissions</span>
    <strong class="ui-kpi__value" id="admissionCount">0</strong>
    <span class="ui-kpi__meta">Current patients</span>
  </article>
</section>

Example KPI CSS

.ui-kpi-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
}

.ui-kpi {
  padding: 18px;
  border-radius: 18px;
  border: 1px solid var(--ui-border);
  background: var(--ui-surface);
  box-shadow: var(--ui-shadow-sm);
}

.ui-kpi--primary {
  background: var(--ui-primary-soft);
  border-color: rgba(3, 105, 161, 0.2);
}

.ui-kpi--success {
  background: var(--ui-success-soft);
  border-color: rgba(5, 150, 105, 0.2);
}

.ui-kpi--warning {
  background: var(--ui-warning-soft);
  border-color: rgba(217, 119, 6, 0.2);
}

.ui-kpi--danger {
  background: var(--ui-danger-soft);
  border-color: rgba(220, 38, 38, 0.2);
}

.ui-kpi__label {
  display: block;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--ui-text-muted);
  letter-spacing: 0.08em;
}

.ui-kpi__value {
  display: block;
  margin-top: 8px;
  font-size: 2rem;
  color: var(--ui-text);
}

.ui-kpi__meta {
  display: block;
  margin-top: 6px;
  font-size: 0.88rem;
  color: var(--ui-text-muted);
}


Status badge rules

Statuses must become visually consistent across the project.

Badge rules
Small, compact, readable
Fully rounded pills
Semantic color variants
Use consistent padding
Use same visual language for state across all dashboards

Example badge CSS

.ui-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 0.78rem;
  font-weight: 700;
}

.ui-badge--success { background: var(--ui-success-soft); color: var(--ui-success); }
.ui-badge--warning { background: var(--ui-warning-soft); color: var(--ui-warning); }
.ui-badge--danger  { background: var(--ui-danger-soft); color: var(--ui-danger); }
.ui-badge--primary { background: var(--ui-primary-soft); color: var(--ui-primary); }
.ui-badge--muted   { background: var(--ui-bg-soft); color: var(--ui-text-muted); }

Status meaning guidance
Available / Eligible / Completed / Approved = success
Pending / Urgent / Maintenance = warning
Rejected / Cancelled / Emergency / Occupied in critical context = danger
Admitted / Booked / Matched / Active info states = primary
Frozen / Declined / inactive secondary states = muted or danger depending on context
Page header pattern

Page headers should be standardized.

Header rules
title + subtitle on the left
optional actions on the right
optional eyebrow/breadcrumb above
should feel more like SaaS app header than large marketing hero


Example page header HTML

<header class="ui-page-header">
  <div>
    <p class="ui-page-header__eyebrow">Patient workspace</p>
    <h1 class="ui-page-header__title">Patient Portal</h1>
    <p class="ui-page-header__subtitle">Manage appointments, records, and blood requests.</p>
  </div>
  <div class="ui-page-header__actions">
    <a class="ui-btn ui-btn--primary" href="#">Primary action</a>
    <a class="ui-btn ui-btn--ghost" href="#">Secondary</a>
  </div>
</header>

Example page header CSS

.ui-page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 18px;
  margin-bottom: 24px;
}

.ui-page-header__eyebrow {
  margin: 0 0 8px;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--ui-text-muted);
}

.ui-page-header__title {
  margin: 0;
  font-size: clamp(1.9rem, 3vw, 2.8rem);
  line-height: 1.08;
  color: var(--ui-text);
}

.ui-page-header__subtitle {
  margin: 8px 0 0;
  max-width: 72ch;
  color: var(--ui-text-muted);
  line-height: 1.65;
}

.ui-page-header__actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

Button styling rules

Buttons should feel more professional and consistent.

Button rules
Keep rounded but not overly bubbly
Primary button = blue
Secondary/support = teal or subtle surface
Destructive = red
Outline/ghost = neutral
Consistent heights across forms and dashboards
Suggested variants
.ui-btn--primary
.ui-btn--secondary
.ui-btn--outline
.ui-btn--ghost
.ui-btn--danger
Form field styling rules

Inputs, selects, and textareas should share one visual system.

Rules
One consistent height for standard inputs/selects
Rounded corners
Strong but clean focus ring
White field background
Muted placeholder text
Labels above fields
Related controls grouped in cards or filter bars
Preserve all existing IDs, names, and JS bindings
Suggested behavior
Primary focus ring = blue
Error/destructive border = red
Disabled state = muted opacity
Filter bar pattern

Filters should be grouped into one reusable pattern.

Rules
Put search and selects into a single filter row or filter card
Keep spacing even
Use a shared background/card surface
Use Reset/Clear action when useful
Do not scatter filters randomly above tables unless necessary
Example CSS

.ui-filterbar {
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  padding: 16px;
  background: var(--ui-surface);
  border: 1px solid var(--ui-border);
  border-radius: 18px;
  box-shadow: var(--ui-shadow-sm);
}

Table styling rules

Tables should feel cleaner and more product-grade.

Rules
Tables should sit inside bordered white cards
Header row should be clearly separated
Row hover should be subtle
Cell spacing should feel comfortable
Use badges for statuses
Use action buttons with clear hierarchy
Preserve existing table IDs and JS logic
Do not convert every table into cards if table scanning is important
Visual direction
soft borders
muted headers
clearer spacing
better scroll container appearance
Dialog / modal rules

Dialogs should feel polished and lightweight.

Rules
Clear title
Small supporting text
Comfortable spacing
Strong confirm/cancel hierarchy
Use danger styling only for destructive actions
Keep modal content readable and not cramped
Tabs rules

When tabs are used:

tabs should look clean and compact
active tab should be obvious
tab content should sit in a strong card/section below
do not use tabs where side navigation or section panels are more appropriate
Empty state rules

No-data states should not look broken.

Rules
show simple message
optional icon
optional CTA
use muted visuals
keep them centered and readable
no raw blank boxes

Suggested content style:

title
short explanation
next action if useful
Dashboard structure rules

Most dashboards should follow this rhythm:

page header
KPI/stat row
primary work area
filters or quick actions
content sections/cards/tables
debug/API section if needed
For dashboards that already use donor-style panel switching
keep one visible section at a time
redesign the visuals only
preserve IDs and panel logic
preserve hash navigation where already implemented
Sidebar and panel navigation rules

For pages with sidebar navigation:

keep labels short
use clearer active states
preserve anchor-based navigation
preserve section IDs
do not break hash-based panel switching
keep hidden/disallowed sections hidden according to current role/department logic
Public landing page direction

For welcome.blade.php:

keep the existing anatomy finder
keep hotspot behavior
improve visual polish carefully
stronger but calmer hero
more premium CTA/buttons/cards
smoother section rhythm
make the finder feel like a premium healthcare discovery tool
Auth page direction

For auth.blade.php:

cleaner auth panel
stronger spacing and field styling
better visual separation of login/register/bootstrap areas
preserve all current logic and API calls
do not break admin bootstrap helpers if they remain needed
Workspace hub direction

For dashboard.blade.php:

make primary role destination more obvious
improve shortcut card presentation
improve session summary
keep role-aware logic intact
Dashboard-by-role direction
Admin
stronger overview cards
cleaner pending/applicant sections
better action hierarchy
Doctor
KPI-first layout
cleaner patient and appointment cards
stronger quick-action area
Nurse
stronger monitoring and patient workflow grouping
preserve Blood Bank conditional behavior
keep one-panel-at-a-time navigation where implemented
IT
stronger operational dashboard hierarchy
preserve Blood Bank IT conditional behavior
preserve standard vs Blood Bank scope logic
Patient
cleaner profile + quick actions + records/appointments layout
more polished cards and sections
Donor
stronger donor status summary
better request/notification cards
better donation history presentation
Applicant
cleaner status/timeline/history presentation
keep separation from staff dashboards
Code quality rules for the redesign
Prefer shared CSS over large repeated inline CSS
Keep page-specific CSS small and justified
Reuse shared classes whenever possible
Preserve DOM ids and JS selectors
Preserve existing Blade section structure where possible
Avoid unnecessary HTML churn if style classes can solve it
Do not replace working vanilla JS with a new frontend framework
Do not move this Blade project to React/Tailwind
Do not change the programming language or backend stack
Suggested shared CSS file structure

Codex may create or update:

public/css/ui-tokens.css
public/css/ui-layout.css
public/css/ui-components.css
public/css/ui-dashboard.css
public/css/ui-utilities.css
public/css/ui-system.css
Suggested responsibility split
ui-tokens.css
colors
shadows
spacing
radii
typography tokens
ui-layout.css

shell
topbar
sidebar
content area
page header wrappers
major responsive layout patterns
ui-components.css
cards
buttons
badges
inputs
selects
textareas
tables
dialogs
helper panels
debug boxes
ui-dashboard.css
KPI grids
section layouts
split layouts
panel-switch visual classes
role dashboard shared visual patterns
ui-utilities.css
spacing helpers
visibility helpers
alignment helpers
responsive utility classes
ui-system.css
import all shared CSS files in correct order
Example shell card

.ui-card {
  background: var(--ui-surface);
  border: 1px solid var(--ui-border);
  border-radius: var(--ui-radius-lg);
  box-shadow: var(--ui-shadow-sm);
}

.ui-card--soft {
  background: linear-gradient(180deg, #ffffff, #f8fbfd);
}

.ui-card--primary {
  background: linear-gradient(180deg, #ffffff, var(--ui-primary-soft));
  border-color: rgba(3, 105, 161, 0.18);
}

Example card section block

.ui-section {
  padding: 18px;
  background: var(--ui-surface);
  border: 1px solid var(--ui-border);
  border-radius: var(--ui-radius-lg);
  box-shadow: var(--ui-shadow-sm);
}

.ui-section__title {
  margin: 0;
  font-size: 1.1rem;
  color: var(--ui-text);
}

.ui-section__copy {
  margin-top: 6px;
  color: var(--ui-text-muted);
  line-height: 1.6;
}

Example button direction
.ui-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 40px;
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 700;
  border: 1px solid transparent;
  text-decoration: none;
}

.ui-btn--primary {
  background: var(--ui-primary);
  color: #fff;
}

.ui-btn--primary:hover {
  background: var(--ui-primary-strong);
}

.ui-btn--outline {
  background: #fff;
  color: var(--ui-text);
  border-color: var(--ui-border);
}

.ui-btn--ghost {
  background: transparent;
  color: var(--ui-text-muted);
}

.ui-btn--danger {
  background: var(--ui-danger);
  color: #fff;
}

Final reminder for Codex

When redesigning:

improve the UI architecture first
then improve the shared shell
then redesign public/auth pages
then redesign dashboards in groups
do not try to do everything in one unsafe pass
preserve backend behavior throughout






=OLD files no need to read
====================================Example token layer============================

:root {
  --ui-bg: #f8fafb;
  --ui-surface: #ffffff;
  --ui-surface-soft: #f8fbfd;
  --ui-text: #0f172a;
  --ui-text-muted: #64748b;
  --ui-border: #e2e8f0;

  --ui-primary: #0369a1;
  --ui-primary-strong: #075985;
  --ui-secondary: #0d9488;
  --ui-success: #059669;
  --ui-warning: #d97706;
  --ui-danger: #dc2626;

  --ui-primary-soft: #e0f2fe;
  --ui-secondary-soft: #ccfbf1;
  --ui-success-soft: #d1fae5;
  --ui-warning-soft: #fef3c7;
  --ui-danger-soft: #fee2e2;

  --ui-radius-sm: 10px;
  --ui-radius-md: 16px;
  --ui-radius-lg: 22px;
  --ui-shadow-sm: 0 6px 18px rgba(15, 23, 42, 0.06);
  --ui-shadow-md: 0 18px 40px rgba(15, 23, 42, 0.10);
}

====================================Example app shell card============================

.ui-card {
  background: var(--ui-surface);
  border: 1px solid var(--ui-border);
  border-radius: var(--ui-radius-lg);
  box-shadow: var(--ui-shadow-sm);
}

.ui-card--soft {
  background: linear-gradient(180deg, #ffffff, #f8fbfd);
}

.ui-card--primary {
  background: linear-gradient(180deg, #ffffff, var(--ui-primary-soft));
  border-color: rgba(3, 105, 161, 0.18);
}

====================================Example KPI card structure============================

<section class="ui-kpi-grid">
  <article class="ui-kpi ui-kpi--primary">
    <span class="ui-kpi__label">Active Admissions</span>
    <strong class="ui-kpi__value" id="admissionCount">0</strong>
    <span class="ui-kpi__meta">Current patients</span>
  </article>
</section>



.ui-kpi-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
}

.ui-kpi {
  padding: 18px;
  border-radius: 18px;
  border: 1px solid var(--ui-border);
  background: var(--ui-surface);
  box-shadow: var(--ui-shadow-sm);
}

.ui-kpi--primary {
  background: var(--ui-primary-soft);
  border-color: rgba(3, 105, 161, 0.2);
}

.ui-kpi__label {
  display: block;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--ui-text-muted);
  letter-spacing: 0.08em;
}

.ui-kpi__value {
  display: block;
  margin-top: 8px;
  font-size: 2rem;
  color: var(--ui-text);
}

.ui-kpi__meta {
  display: block;
  margin-top: 6px;
  font-size: 0.88rem;
  color: var(--ui-text-muted);
}

====================================Example status badge system============================

.ui-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 0.78rem;
  font-weight: 700;
}

.ui-badge--success { background: var(--ui-success-soft); color: var(--ui-success); }
.ui-badge--warning { background: var(--ui-warning-soft); color: var(--ui-warning); }
.ui-badge--danger  { background: var(--ui-danger-soft); color: var(--ui-danger); }
.ui-badge--primary { background: var(--ui-primary-soft); color: var(--ui-primary); }

====================================  Example page header pattern      ============================
<header class="ui-page-header">
  <div>
    <p class="ui-page-header__eyebrow">Patient workspace</p>
    <h1 class="ui-page-header__title">Patient Portal</h1>
    <p class="ui-page-header__subtitle">Manage appointments, records, and blood requests.</p>
  </div>
  <div class="ui-page-header__actions">
    <a class="ui-btn ui-btn--primary" href="#">Primary action</a>
    <a class="ui-btn ui-btn--ghost" href="#">Secondary</a>
  </div>
</header>
====================================  Example filter bar      ============================

.ui-filterbar {
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  padding: 16px;
  background: var(--ui-surface);
  border: 1px solid var(--ui-border);
  border-radius: 18px;
  box-shadow: var(--ui-shadow-sm);
}
==================================== notice   ============================
## Must Preserve Existing Features
- Keep backend logic unchanged
- Keep routes unchanged
- Keep API calls unchanged
- Keep IDs and JS hooks unchanged
- Keep Blood Bank conditional behavior unchanged
- Keep donor-style one-panel-at-a-time navigation where already implemented
- Keep landing page anatomy finder exactly functional
- Use uploaded React/Tailwind files as visual inspiration only, not exact feature parity

====================================        ============================
====================================        ============================
====================================        ============================
====================================        ============================
