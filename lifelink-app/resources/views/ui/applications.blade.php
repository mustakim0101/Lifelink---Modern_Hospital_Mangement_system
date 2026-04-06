@extends('ui.layouts.app')

@section('title', 'Applicant Status')
@section('workspace_label', 'Applicant progress workspace')
@section('hero_badge', 'Applicant')
@section('hero_title', 'Track staffing review progress, latest notes, and next steps from one calmer status page.')
@section('hero_description', 'The applicant workspace keeps status, role details, department context, and review history visible without forcing applicants through raw API output.')
@section('meta_title', 'Applicant Status')
@section('meta_copy', 'Application state, review notes, and next actions')

@push('styles')
<style>
    .applicant-grid,.applicant-summary-grid,.applicant-info-grid{display:grid;gap:18px}
    .applicant-summary-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
    .applicant-info-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .applicant-status-pill{display:inline-flex;align-items:center;min-height:34px;padding:0 14px;border-radius:999px;font-size:.82rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
    .applicant-status-pill.pending{background:rgba(212,140,32,.14);color:#a66a11}
    .applicant-status-pill.approved{background:rgba(16,152,107,.14);color:#0d7b57}
    .applicant-status-pill.rejected{background:rgba(217,72,90,.14);color:#b53b4a}
    .applicant-note-card{padding:18px;border-radius:20px;border:1px solid rgba(140,170,201,.18);background:rgba(248,252,255,.88)}
    .applicant-note-card strong{display:block;font-size:1.02rem}
    .applicant-note-card p{margin:8px 0 0;color:var(--ll-text-muted);line-height:1.65}
    .applicant-console{margin-top:16px;min-height:140px;max-height:320px;overflow:auto;border-radius:18px;border:1px solid rgba(140,170,201,.18);background:#0f1c33;color:#d7e3ff;padding:16px;font-size:12px}
    @media (max-width:980px){.applicant-summary-grid,.applicant-info-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#applicant-overview"><strong>Overview</strong><span>Status</span></a>
    <a href="#applicant-timeline"><strong>Timeline</strong><span>Steps</span></a>
    <a href="#applicant-history"><strong>History</strong><span>Records</span></a>
    <a href="#applicant-debug"><strong>Debug</strong><span>Hidden</span></a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>How this page works</strong>
        <p>The page still loads the same applicant endpoints. It now presents results as status cards, notes, and history instead of a prototype console.</p>
    </div>
@endsection

@section('section_nav')
    <a href="#applicant-overview" class="is-active">Overview</a>
    <a href="#applicant-timeline">Timeline</a>
    <a href="#applicant-history">History</a>
    <a href="#applicant-debug">Debug</a>
@endsection

@section('content')
    <div class="applicant-grid">
        <div id="applicantSessionAlert" class="ll-inline-alert is-warning ll-hidden-debug">
            <strong>Applicant session required</strong>
            <p>Sign in first so LifeLink can load the latest application status and review history for this account.</p>
        </div>

        <section id="applicant-overview" class="ll-section">
            <div class="applicant-summary-grid">
                <article class="ll-stat-card is-primary"><small>Latest status</small><strong id="latestStatus">-</strong><span id="statusMessage">Your application status will appear here after loading.</span></article>
                <article class="ll-stat-card is-success"><small>Applied role</small><strong id="latestRole">-</strong><span id="roleSummary">Role context updates from the latest application.</span></article>
                <article class="ll-stat-card is-warning"><small>Preferred department</small><strong id="latestDepartment">-</strong><span id="departmentSummary">Department context stays visible when it was part of the application.</span></article>
            </div>
        </section>

        <section class="applicant-info-grid">
            <article class="ll-panel">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Current application</h2>
                        <p>Use refresh actions to pull the newest decision and review note without leaving the applicant workspace.</p>
                    </div>
                    <span id="waitingBadge" class="applicant-status-pill pending">Pending review</span>
                </div>

                <div class="hub-value-list" style="margin-top: 16px;">
                    <div class="hub-value-item"><small>Applicant email</small><strong id="applicantEmail">No applicant session found.</strong></div>
                    <div class="hub-value-item"><small>Latest review note</small><strong id="latestReviewNote">No review note available yet.</strong></div>
                </div>

                <div class="ll-inline-actions" style="margin-top: 18px;">
                    <button class="ll-button" type="button" onclick="loadLatest()">Refresh latest status</button>
                    <button class="ll-button-ghost" type="button" onclick="loadAll()">Load full history</button>
                </div>
            </article>

            <article id="applicant-timeline" class="ll-panel ll-section">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Review journey</h2>
                        <p>This timeline translates the staffing workflow into a user-facing explanation.</p>
                    </div>
                </div>

                <div class="ll-timeline" style="margin-top: 16px;">
                    <div class="ll-timeline__item">
                        <div class="ll-timeline__dot">1</div>
                        <strong>Account created</strong>
                        <div class="ll-timeline__meta">You registered through the applicant onboarding flow and submitted an initial role request.</div>
                    </div>
                    <div class="ll-timeline__item">
                        <div class="ll-timeline__dot">2</div>
                        <strong>Admin review</strong>
                        <div class="ll-timeline__meta">Admins review role, department context, and notes before approving or rejecting the request.</div>
                    </div>
                    <div class="ll-timeline__item">
                        <div class="ll-timeline__dot">3</div>
                        <strong>Next action</strong>
                        <div id="nextStepCopy" class="ll-timeline__meta">Load your latest status to see whether you should wait, log in again, or prepare for another application.</div>
                    </div>
                </div>
            </article>
        </section>

        <section id="applicant-history" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Application history</h2>
                    <p>Every submission stays visible here with status, role, department, and review note context.</p>
                </div>
            </div>

            <div class="ll-table-wrap" style="margin-top: 18px;">
                <table class="ll-table">
                    <thead>
                        <tr><th>ID</th><th>Status</th><th>Applied Role</th><th>Department</th><th>Applied At</th><th>Review Note</th></tr>
                    </thead>
                    <tbody id="applicationsBody">
                        <tr><td colspan="6">No applications loaded yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="applicant-debug" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Debug response</h2>
                    <p>The raw response is still preserved for troubleshooting, but it stays contained below the main user-facing interface.</p>
                </div>
                <span class="ll-status-chip is-soft">Contained</span>
            </div>
            <pre id="out" class="applicant-console"></pre>
        </section>
    </div>
@endsection

@push('scripts')
<script>
const out=document.getElementById('out');
const API='/api';
function write(data){out.textContent=typeof data==='string'?data:JSON.stringify(data,null,2);}
function userToken(){return localStorage.getItem('USER_TOKEN');}
function applicantStatusClass(status){if(status==='Approved')return'approved';if(status==='Rejected')return'rejected';return'pending';}
function setText(id,value){const node=document.getElementById(id);if(node)node.textContent=value;}
function updateStatus(status){const badge=document.getElementById('waitingBadge');badge.className=`applicant-status-pill ${applicantStatusClass(status)}`;badge.textContent=status||'Pending review';}
function setMessageCopy(status){if(status==='Approved'){setText('statusMessage','Your application was approved. Future logins should route you into the assigned staff workspace.');setText('nextStepCopy','Approval is complete. Use the shared login page again to continue into your newly assigned role workspace.');return;}if(status==='Rejected'){setText('statusMessage','Your application was reviewed and rejected. Wait for further instruction or submit another request when appropriate.');setText('nextStepCopy','The current request was rejected. Review the latest note, then wait for guidance before submitting again.');return;}setText('statusMessage','Your application is pending. Keep checking this page for review notes and final status changes.');setText('nextStepCopy','The request is still pending. No action is required yet other than checking back for an update.');}
async function call(path,method,body=null){const token=userToken();if(!token)return{status:401,data:{message:'USER_TOKEN missing. Login first from /ui/login.'}};const headers={'Accept':'application/json','Content-Type':'application/json','Authorization':`Bearer ${token}`};const res=await fetch(API+path,{method,headers,body:body?JSON.stringify(body):undefined});const text=await res.text();try{return{status:res.status,data:JSON.parse(text)}}catch{return{status:res.status,data:text}}}
function renderLatest(application){const status=application?.status||'Pending';setText('latestStatus',status);setText('latestRole',application?.applied_role||'-');setText('latestDepartment',application?.applied_department||'General');setText('latestReviewNote',application?.review_notes||'No review note available yet.');updateStatus(status);setMessageCopy(status);}
async function loadLatest(){const result=await call('/applications/my/latest','GET');write(result);document.getElementById('applicantSessionAlert').classList.toggle('ll-hidden-debug',result.status!==401);renderLatest(result.data?.latestApplication||null);}
async function loadAll(){const result=await call('/applications/my','GET');write(result);document.getElementById('applicantSessionAlert').classList.toggle('ll-hidden-debug',result.status!==401);const rows=Array.isArray(result.data?.applications)?result.data.applications:[];document.getElementById('applicationsBody').innerHTML=rows.length?rows.map((row)=>`<tr><td>${row.id}</td><td><span class="applicant-status-pill ${applicantStatusClass(row.status)}">${row.status||'-'}</span></td><td>${row.applied_role||'-'}</td><td>${row.applied_department||'-'}</td><td>${row.applied_at?new Date(row.applied_at).toLocaleString():'-'}</td><td>${row.review_notes||'-'}</td></tr>`).join(''):'<tr><td colspan="6">No applications found.</td></tr>';}
function hydrateApplicantIdentity(){setText('applicantEmail',localStorage.getItem('CURRENT_USER_EMAIL')||'No applicant session found.');}
hydrateApplicantIdentity();loadLatest();loadAll();
</script>
@endpush
