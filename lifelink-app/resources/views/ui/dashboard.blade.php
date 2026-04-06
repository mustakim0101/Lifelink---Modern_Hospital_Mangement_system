@extends('ui.layouts.app')

@section('title', 'Workspace Hub')
@section('workspace_label', 'Role-aware routing workspace')
@section('hero_badge', 'Workspace Hub')
@section('hero_title', 'Return every signed-in user to the right place with a cleaner role-aware hub.')
@section('hero_description', 'This hub keeps identity, primary destination, and module shortcuts visible before routing the current browser session into the correct workspace.')
@section('meta_title', 'Workspace Hub')
@section('meta_copy', 'Role routing, session summary, and operational shortcuts')

@section('hero_extra')
    <div class="ll-inline-actions">
        <a id="heroPrimaryLink" class="ll-button" href="/ui/login">Open main area</a>
        <button class="ll-button-ghost" type="button" onclick="logoutSession()">Logout</button>
    </div>
@endsection

@push('styles')
<style>
    .hub-grid,.hub-card-grid,.hub-chip-row{display:grid;gap:18px}
    .hub-card-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
    .hub-chip-row{grid-template-columns:repeat(auto-fit,minmax(160px,max-content));gap:10px;justify-content:start}
    .hub-chip{display:inline-flex;align-items:center;min-height:34px;padding:0 12px;border-radius:999px;background:var(--ll-surface-muted);border:1px solid var(--ll-border);color:var(--ll-text-muted);font-size:.82rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase}
    .hub-route-card,.hub-session-card,.hub-role-card{height:100%}
    .hub-role-card p,.hub-route-card p,.hub-session-card p{margin:8px 0 0;color:var(--ll-text-muted);line-height:1.65}
    .hub-route-grid{display:grid;gap:14px}
    .hub-value-list{display:grid;gap:12px;margin-top:14px}
    .hub-value-item{padding:14px 16px;border-radius:18px;border:1px solid rgba(27,117,208,.1);background:rgba(248,252,255,.88)}
    .hub-value-item small{display:block;margin-bottom:6px;color:var(--ll-text-muted);font-size:.74rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
    .hub-value-item strong{font-size:1rem}
    .hub-shortcut{display:grid;gap:12px;padding:18px;border-radius:22px;border:1px solid rgba(140,170,201,.18);background:rgba(255,255,255,.9);box-shadow:var(--ll-shadow-sm)}
    .hub-shortcut strong{font-size:1.04rem}
    .hub-shortcut p{margin:0;color:var(--ll-text-muted);line-height:1.6}
    .hub-alert-title{display:block;margin-bottom:6px}
    @media (max-width:1080px){.hub-card-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#hub-overview"><strong>Overview</strong><span>Today</span></a>
    <a href="#hub-next"><strong>Primary Route</strong><span>Next</span></a>
    <a href="#hub-shortcuts"><strong>Shortcuts</strong><span>Modules</span></a>
    <a href="#hub-directory"><strong>Role Guide</strong><span>Reference</span></a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Hub behavior</strong>
        <p>The page still reads the current browser session and routes to the correct role workspace. This refactor only improves presentation and hierarchy.</p>
    </div>
    <div class="app-shell__sidebar-card">
        <strong>Recommended flow</strong>
        <p>Confirm identity, review your primary destination, then continue into the matching operational module.</p>
    </div>
@endsection

@section('section_nav')
    <a href="#hub-overview" class="is-active">Overview</a>
    <a href="#hub-next">Primary Route</a>
    <a href="#hub-shortcuts">Shortcuts</a>
    <a href="#hub-directory">Role Guide</a>
@endsection

@section('content')
    <div class="hub-grid">
        <div id="hubSessionAlert" class="ll-inline-alert is-warning ll-hidden-debug">
            <strong class="hub-alert-title">No active workspace session</strong>
            <p>Sign in first to unlock role-aware routing, shortcuts, and identity-aware navigation.</p>
        </div>

        <div id="hubRedirectAlert" class="ll-inline-alert is-success ll-hidden-debug">
            <strong class="hub-alert-title">Auto-routing is ready</strong>
            <p id="redirectCopy">The hub will continue into the primary workspace shortly.</p>
        </div>

        <section id="hub-overview" class="ll-section">
            <div class="ll-kpi-grid">
                <article class="ll-stat-card is-primary"><small>Detected roles</small><strong id="roleCount">0</strong><span id="roleSummary">Sign in to unlock role-aware routing.</span></article>
                <article class="ll-stat-card is-success"><small>Primary destination</small><strong id="primaryAcronym">--</strong><span id="primarySummary">No route selected yet.</span></article>
                <article class="ll-stat-card is-warning"><small>Session token</small><strong id="tokenState">Missing</strong><span id="tokenSummary">Shared login still controls all active sessions.</span></article>
                <article class="ll-stat-card is-neutral"><small>Advanced tools</small><strong id="advancedState">Locked</strong><span id="advancedSummary">Admin and IT users unlock operational tooling.</span></article>
            </div>
        </section>

        <div class="hub-card-grid">
            <article id="hub-next" class="ll-panel hub-route-card ll-section">
                <div class="ll-panel-heading">
                    <div>
                        <h2 id="primaryTitle">Sign in required</h2>
                        <p id="primaryCopy">Log in first to unlock role-aware routing and the correct operational dashboard.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Primary route</span>
                </div>

                <div class="hub-chip-row" id="roleChips" style="margin-top: 16px;"></div>

                <div class="hub-value-list">
                    <div class="hub-value-item">
                        <small>Continue to</small>
                        <strong id="primaryLinkLabel">Login page</strong>
                    </div>
                    <div class="hub-value-item">
                        <small>Auto-redirect</small>
                        <strong id="redirectCountdown">Waiting for session</strong>
                    </div>
                </div>

                <div class="ll-inline-actions" style="margin-top: 18px;">
                    <a id="primaryLink" class="ll-button" href="/ui/login">Open login page</a>
                </div>
            </article>

            <article class="ll-panel hub-session-card">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Session summary</h2>
                        <p>Identity details remain readable here without exposing raw token values or technical storage state.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Identity</span>
                </div>

                <div class="hub-value-list">
                    <div class="hub-value-item">
                        <small>User</small>
                        <strong id="identityName">No active session</strong>
                    </div>
                    <div class="hub-value-item">
                        <small>Email</small>
                        <strong id="identityEmail">No email stored</strong>
                    </div>
                    <div class="hub-value-item">
                        <small>User ID</small>
                        <strong id="identityId">Not available</strong>
                    </div>
                </div>
            </article>

            <article class="ll-panel hub-role-card">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Role guide</h2>
                        <p>Each workspace keeps a shared shell while adapting cards, filters, and actions to a specific hospital responsibility.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Modules</span>
                </div>

                <div class="hub-value-list">
                    <div class="hub-value-item"><small>Admin</small><strong>Review applicants, protect access, provision staff</strong></div>
                    <div class="hub-value-item"><small>Clinical</small><strong>Appointments, monitoring, records, and care actions</strong></div>
                    <div class="hub-value-item"><small>Patient and donor</small><strong>Guided self-service, requests, availability, and history</strong></div>
                </div>
            </article>
        </div>

        <section id="hub-shortcuts" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Role shortcuts</h2>
                    <p>These cards mirror the current role configuration and stay aligned to the same routes and features already present in the app.</p>
                </div>
                <span class="ll-status-chip is-soft">Shortcut grid</span>
            </div>

            <div id="actionGrid" class="ll-collection-grid" style="margin-top: 18px;"></div>
        </section>

        <section id="hub-directory" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Browse workspace modules</h2>
                    <p>Use the hub as a visual directory when testing flows across the Laravel Blade prototypes.</p>
                </div>
            </div>

            <div class="ll-collection-grid" style="margin-top: 18px;">
                <article class="hub-shortcut"><strong>Admin control</strong><p>Account state, application reviews, and staff setup.</p><a class="ll-button-ghost" href="/ui/admin-users">Open admin</a></article>
                <article class="hub-shortcut"><strong>IT operations</strong><p>Admissions, care units, beds, and department scope.</p><a class="ll-button-ghost" href="/ui/it-bed-allocation">Open IT</a></article>
                <article class="hub-shortcut"><strong>Clinical dashboards</strong><p>Doctor and nurse workspaces for appointments, monitoring, and bedside support.</p><a class="ll-button-ghost" href="/ui/doctor-dashboard">Doctor view</a></article>
                <article class="hub-shortcut"><strong>Patient portal</strong><p>Appointments, records, and blood requests in one patient-friendly flow.</p><a class="ll-button-ghost" href="/ui/patient-portal">Open patient</a></article>
                <article class="hub-shortcut"><strong>Donor and blood response</strong><p>Donor availability, matching, screening, and fulfillment flows.</p><a class="ll-button-ghost" href="/ui/donor-dashboard">Open donor</a></article>
                <article class="hub-shortcut"><strong>Applicant tracking</strong><p>Registration, review notes, and approval status visibility.</p><a class="ll-button-ghost" href="/ui/applications">Open applicant</a></article>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
const BLOOD_BANK_DEPARTMENT='Blood Bank';
const roleConfig={
    Admin:{label:'Administrator',primaryLabel:'Admin control center',primaryHref:'/ui/admin-users',primaryCopy:'Review applicants, control account access, and finish staff provisioning.',cards:[{title:'Admin control',href:'/ui/admin-users',desc:'Account control and profile provisioning.'},{title:'Application reviews',href:'/ui/application-reviews',desc:'Approve or reject staff-role applications.'},{title:'Advanced tools',href:'/ui/dev-tools',desc:'Controlled diagnostics and verification tooling.'}]},
    ITWorker:{label:'IT worker',primaryLabel:'IT operations workspace',primaryHref:'/ui/it-bed-allocation',primaryCopy:'Coordinate department scope, admissions, and bed allocation flows.',cards:[{title:'IT bed allocation',href:'/ui/it-bed-allocation',desc:'Admission and bed assignment workflow.'},{title:'Ward setup',href:'/ui/ward-setup',desc:'Care unit and bed structure setup.'}],bloodBankCards:[{title:'Blood matching center',href:'/ui/blood-matching',desc:'Request matching and donor-response operations.'}]},
    Doctor:{label:'Doctor',primaryLabel:'Doctor dashboard',primaryHref:'/ui/doctor-dashboard',primaryCopy:'Open doctor-facing clinical actions, appointments, and patient workflows.',cards:[{title:'Doctor dashboard',href:'/ui/doctor-dashboard',desc:'Clinical tasks, appointments, and bed requests.'}]},
    Nurse:{label:'Nurse',primaryLabel:'Nurse dashboard',primaryHref:'/ui/nurse-dashboard',primaryCopy:'Open monitoring, vitals, and bedside care actions.',cards:[{title:'Nurse dashboard',href:'/ui/nurse-dashboard',desc:'Monitoring, vitals, and Blood Bank screening tools.'}]},
    Patient:{label:'Patient',primaryLabel:'Patient portal',primaryHref:'/ui/patient-portal',primaryCopy:'Manage appointments, records, and blood requests from one portal.',cards:[{title:'Patient portal',href:'/ui/patient-portal',desc:'Appointments, records, and blood support requests.'}]},
    Donor:{label:'Donor',primaryLabel:'Donor dashboard',primaryHref:'/ui/donor-dashboard',primaryCopy:'Manage donor availability, notifications, and donation history.',cards:[{title:'Donor dashboard',href:'/ui/donor-dashboard',desc:'Availability, request response, and donation history.'}]},
    Applicant:{label:'Applicant',primaryLabel:'Applicant workspace',primaryHref:'/ui/applications',primaryCopy:'Track application status and next steps until review completes.',cards:[{title:'Applicant workspace',href:'/ui/applications',desc:'Submission history, review notes, and status tracking.'}]}
};
const rolePriority=['Admin','ITWorker','Doctor','Nurse','Donor','Applicant','Patient'];
const fullName=localStorage.getItem('CURRENT_USER_FULL_NAME')||'';
const userId=localStorage.getItem('CURRENT_USER_ID')||'';
const email=localStorage.getItem('CURRENT_USER_EMAIL')||'';
const roles=JSON.parse(localStorage.getItem('CURRENT_USER_ROLES')||'[]');
const token=localStorage.getItem('USER_TOKEN')||'';

function setText(id,value){const node=document.getElementById(id);if(node)node.textContent=value;}
function escapeHtml(value){return String(value??'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;');}
function preferredRole(){return rolePriority.find((role)=>roles.includes(role))||null;}
function logoutSession(){['ADMIN_TOKEN','ADMIN_USER_ID','ADMIN_EMAIL','USER_TOKEN','PATIENT_ID','PATIENT_EMAIL','CURRENT_USER_ID','CURRENT_USER_FULL_NAME','CURRENT_USER_EMAIL','CURRENT_USER_ROLES'].forEach((key)=>localStorage.removeItem(key));window.location.href='/ui/login';}
async function api(path){const response=await fetch(`/api${path}`,{headers:{Accept:'application/json',Authorization:`Bearer ${token}`}});const text=await response.text();let data={};try{data=JSON.parse(text)}catch{}return{status:response.status,data};}
async function hasBloodBankItAccess(){if(roles.includes('Admin'))return true;if(!roles.includes('ITWorker')||!token)return false;const result=await api('/ward/it/departments');if(result.status>=300)return false;const departments=Array.isArray(result.data?.departments)?result.data.departments:[];return departments.some((department)=>department?.dept_name===BLOOD_BANK_DEPARTMENT);}

function renderRoleChips(){const root=document.getElementById('roleChips');root.innerHTML=roles.length?roles.map((role)=>`<span class="hub-chip">${escapeHtml(role==='ITWorker'?'IT Worker':role)}</span>`).join(''):'<span class="hub-chip">No active roles</span>';}

function renderShortcuts(visibleCards){const root=document.getElementById('actionGrid');root.innerHTML=visibleCards.length?visibleCards.map((card)=>`<article class="hub-shortcut"><strong>${escapeHtml(card.title)}</strong><p>${escapeHtml(card.desc)}</p><a class="ll-button-ghost" href="${card.href}">Open</a></article>`).join(''):'<div class="ll-empty"><strong>No shortcuts available</strong><p>Sign in to reveal the correct modules for this session.</p></div>';}

async function bootHub(){
    const hasSession=Boolean(token&&roles.length);
    document.getElementById('hubSessionAlert').classList.toggle('ll-hidden-debug',hasSession);
    setText('identityName',fullName||email||'No active session');
    setText('identityEmail',email||'No email stored');
    setText('identityId',userId?`#${userId}`:'Not available');
    setText('roleCount',String(roles.length));
    setText('roleSummary',roles.length?roles.join(', '):'Sign in to unlock role-aware routing.');
    setText('tokenState',hasSession?'Ready':'Missing');
    setText('tokenSummary',hasSession?'A valid shared session is available for workspace routing.':'Shared login still controls all active sessions.');
    renderRoleChips();
    if(!hasSession){renderShortcuts([]);return;}

    const preferred=preferredRole();
    const config=roleConfig[preferred]||roleConfig.Patient;
    const bloodAccess=await hasBloodBankItAccess();
    const cards=[];
    roles.forEach((role)=>{const entry=roleConfig[role];if(!entry)return;entry.cards.forEach((card)=>{if(!cards.some((existing)=>existing.href===card.href))cards.push(card);});if(role==='ITWorker'&&bloodAccess){(entry.bloodBankCards||[]).forEach((card)=>{if(!cards.some((existing)=>existing.href===card.href))cards.push(card);});}});

    setText('primaryTitle',config.primaryLabel);
    setText('primaryCopy',config.primaryCopy);
    setText('primaryAcronym',config.label.slice(0,2).toUpperCase());
    setText('primarySummary',config.primaryCopy);
    setText('primaryLinkLabel',config.label);
    setText('advancedState',(roles.includes('Admin')||roles.includes('ITWorker'))?'Enabled':'Focused');
    setText('advancedSummary',(roles.includes('Admin')||roles.includes('ITWorker'))?'Operational support and verification tools are available.':'Advanced tooling stays hidden for non-admin workflows.');
    document.getElementById('primaryLink').href=config.primaryHref;
    document.getElementById('primaryLink').textContent='Continue to workspace';
    document.getElementById('heroPrimaryLink').href=config.primaryHref;
    document.getElementById('heroPrimaryLink').textContent='Continue to workspace';
    renderShortcuts(cards);

    let remaining=3;
    document.getElementById('hubRedirectAlert').classList.remove('ll-hidden-debug');
    setText('redirectCountdown',`${remaining}s until automatic routing`);
    setText('redirectCopy',`The hub will continue into ${config.label.toLowerCase()} workspace automatically unless you choose another module first.`);
    const interval=setInterval(()=>{remaining-=1;setText('redirectCountdown',remaining>0?`${remaining}s until automatic routing`:'Routing now');if(remaining<=0){clearInterval(interval);window.location.href=config.primaryHref;}},1000);
}

bootHub();
</script>
@endpush
