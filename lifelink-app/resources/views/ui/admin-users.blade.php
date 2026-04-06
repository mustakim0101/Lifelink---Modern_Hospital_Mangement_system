@extends('ui.layouts.app')

@section('title', 'Admin Control Center')
@section('workspace_label', 'Admin operations workspace')
@section('hero_badge', 'Admin')
@section('hero_title', 'Run approvals, account safeguards, and staff provisioning from one calm control center.')
@section('hero_description', 'Review applicants, protect account access, and complete doctor, nurse, or IT setup without exposing technical backend details to the people doing operational work.')
@section('meta_title', 'Admin Control Center')

@section('hero_extra')
    <div class="ll-inline-actions">
        <button class="ll-button" type="button" onclick="loadPendingApplications()">Refresh applicant queue</button>
        <a class="ll-button-ghost" href="/ui/application-reviews">Open full review workspace</a>
    </div>
@endsection

@push('styles')
<style>
.admin-dashboard,.admin-queue-grid,.admin-role-grid,.admin-chip-row{display:grid;gap:var(--ll-space-4)}.admin-queue-grid,.admin-role-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.admin-role-grid--triple{grid-template-columns:repeat(3,minmax(0,1fr))}.admin-chip-row{grid-template-columns:repeat(auto-fit,minmax(140px,max-content));gap:10px;justify-content:start}.admin-chip{display:inline-flex;align-items:center;min-height:34px;padding:0 12px;border-radius:999px;background:var(--ll-surface-muted);border:1px solid var(--ll-border);color:var(--ll-text-muted);font-size:.86rem;font-weight:700}.admin-app-card,.admin-role-card,.admin-account-card{height:100%}.admin-account-state,.admin-provisioning-state{padding:16px 18px;border-radius:18px}.admin-account-state{background:linear-gradient(180deg,#f8fbff 0%,#fff 100%);border:1px dashed var(--ll-border-strong)}.admin-provisioning-state{border:1px solid rgba(19,114,170,.14);background:linear-gradient(180deg,#f3f9fe 0%,#fff 100%)}.admin-account-state strong,.admin-provisioning-state strong{display:block;font-size:1rem}.admin-account-state p,.admin-provisioning-state p{margin:8px 0 0;color:var(--ll-text-muted);line-height:1.65}.admin-note{min-height:110px}.admin-panel-stack{display:grid;gap:14px}.admin-app-card .ll-panel-heading,.admin-account-card .ll-panel-heading,.admin-role-card .ll-panel-heading{align-items:center}@media (max-width:1240px){.admin-role-grid--triple{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:860px){.admin-queue-grid,.admin-role-grid,.admin-role-grid--triple{grid-template-columns:1fr}}
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#admin-overview"><strong>Overview</strong><span>Today</span></a>
    <a href="#admin-queue"><strong>Applicant Queue</strong><span>Review</span></a>
    <a href="#admin-provisioning"><strong>Staff Provisioning</strong><span>Setup</span></a>
    <a href="#admin-accounts"><strong>Account Safety</strong><span>Access</span></a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Recommended flow</strong>
        <p>Review pending applicants first, approve or reject with notes, then use the approved account to prefill doctor, nurse, or IT setup so the staff member lands in a ready workspace.</p>
    </div>
    <div class="app-shell__sidebar-card">
        <strong>Safety reminder</strong>
        <p>Operational users should see tasks, patient context, and clear statuses. Technical diagnostics stay hidden here unless support work truly requires them.</p>
    </div>
@endsection

@section('section_nav')
    <a href="#admin-overview" class="is-active">Overview</a>
    <a href="#admin-queue">Applicant Queue</a>
    <a href="#admin-provisioning">Provisioning</a>
    <a href="#admin-accounts">Account Safety</a>
@endsection

@section('content')
    <div class="admin-dashboard">
        <div id="sessionAlert" class="ll-inline-alert is-warning ll-hidden-debug">
            <strong>Admin session required</strong>
            <p>Sign in as an administrator to review applicants and complete provisioning tasks.</p>
        </div>
        <div id="actionAlert" class="ll-inline-alert is-success ll-hidden-debug">
            <strong id="actionAlertTitle">Action completed</strong>
            <p id="actionAlertBody">The latest admin activity summary will appear here.</p>
        </div>
        <section id="admin-overview" class="ll-section">
            <div class="ll-kpi-grid">
                <article class="ll-stat-card is-primary"><small>Pending applicants</small><strong id="pendingCount">0</strong><span id="pendingSummary">Awaiting first queue refresh.</span></article>
                <article class="ll-stat-card is-success"><small>Departments loaded</small><strong id="departmentCount">0</strong><span>Provisioning menus update automatically from the public department list.</span></article>
                <article class="ll-stat-card is-warning"><small>Admin session</small><strong id="tokenState">Check</strong><span id="sessionStateCopy">We are verifying whether this workspace has the access it needs.</span></article>
                <article class="ll-stat-card is-neutral"><small>Last queue refresh</small><strong id="lastQueueRefresh">Waiting</strong><span>Use the refresh action whenever new applications arrive.</span></article>
            </div>
        </section>
        <section class="admin-role-grid ll-section">
            <article id="admin-accounts" class="ll-panel admin-account-card">
                <div class="ll-panel-heading">
                    <div><h2>Account safety</h2><p>Freeze, unfreeze, or inspect an account without leaving the admin workspace.</p></div>
                    <span class="ll-status-chip is-soft">Protected action</span>
                </div>
                <div class="admin-panel-stack">
                    <div class="ll-field">
                        <label class="ll-label" for="userId">Target account</label>
                        <input id="userId" class="ll-input" placeholder="Approved user ID or selected applicant">
                        <p class="ll-helper">Prefer using "Use in setup" from an applicant card so the right account is pulled in automatically.</p>
                    </div>
                    <div class="ll-inline-actions">
                        <button class="ll-button" type="button" onclick="statusUser()">Check status</button>
                        <button class="ll-button-accent" type="button" onclick="unfreezeUser()">Restore access</button>
                        <button class="ll-button-ghost" type="button" onclick="freezeUser()">Freeze access</button>
                    </div>
                    <div class="admin-account-state">
                        <strong id="accountStateTitle">No account reviewed yet</strong>
                        <p id="accountStateBody">Status updates, freeze confirmations, and access notes will appear here instead of raw backend output.</p>
                    </div>
                </div>
            </article>
            <article class="ll-panel">
                <div class="ll-panel-heading">
                    <div><h2>Admin focus</h2><p>Use this compact queue for quick triage, then switch to the larger review page if you need a dedicated approval workflow.</p></div>
                    <a class="ll-button-ghost" href="/ui/application-reviews">Full reviews</a>
                </div>
                <div class="admin-panel-stack">
                    <div class="admin-chip-row"><span class="admin-chip">Approve with notes</span><span class="admin-chip">Reject cleanly</span><span class="admin-chip">Prefill role setup</span></div>
                    <div class="admin-provisioning-state">
                        <strong id="prefillStateTitle">Provisioning is ready</strong>
                        <p id="prefillStateBody">Select an applicant card to push the correct account into doctor, nurse, or IT setup without retyping.</p>
                    </div>
                    <div class="ll-inline-actions">
                        <button class="ll-button" type="button" onclick="loadPendingApplications()">Refresh queue</button>
                        <button class="ll-button-ghost" type="button" onclick="loadDepartments()">Reload departments</button>
                    </div>
                </div>
            </article>
        </section>
        <section id="admin-queue" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div><h2>Pending applicant queue</h2><p>Each card keeps role, department, notes, and decision actions together so approvals stay clear and fast.</p></div>
                <span class="ll-status-chip is-soft">Live queue</span>
            </div>
            <div id="pendingCards" class="admin-queue-grid">
                <div class="ll-empty"><strong>No applicants loaded yet</strong><p>Refresh the queue to pull the current pending applications into this workspace.</p></div>
            </div>
        </section>
        <section id="admin-provisioning" class="ll-section">
            <div class="admin-role-grid admin-role-grid--triple">
                <article class="ll-panel admin-role-card">
                    <div class="ll-panel-heading"><div><h2>Doctor setup</h2><p>Save the doctor profile with the approved account and department assignment.</p></div><span class="ll-status-chip is-primary">Doctor</span></div>
                    <div class="ll-form-grid">
                        <div class="ll-field"><label class="ll-label" for="doctorUserId">Approved doctor account</label><input id="doctorUserId" class="ll-input" type="number" placeholder="Selected from applicant queue"></div>
                        <div class="ll-field"><label class="ll-label" for="doctorDepartmentId">Department</label><select id="doctorDepartmentId" class="ll-select"><option value="">Select department</option></select></div>
                        <p class="ll-helper">Doctor setup keeps the same underlying save action. This UI only guides the flow more clearly.</p>
                        <div class="ll-inline-actions"><button class="ll-button" type="button" onclick="upsertDoctorProfile()">Save doctor setup</button></div>
                    </div>
                </article>
                <article class="ll-panel admin-role-card">
                    <div class="ll-panel-heading"><div><h2>Nurse setup</h2><p>Create or update the nurse profile and lock department scope before shift work begins.</p></div><span class="ll-status-chip is-success">Nurse</span></div>
                    <div class="ll-form-grid">
                        <div class="ll-field"><label class="ll-label" for="nurseUserId">Approved nurse account</label><input id="nurseUserId" class="ll-input" type="number" placeholder="Selected from applicant queue"></div>
                        <div class="ll-field"><label class="ll-label" for="nurseDepartmentId">Department</label><select id="nurseDepartmentId" class="ll-select"><option value="">Select department</option></select></div>
                        <div class="ll-field"><label class="ll-label" for="wardAssignmentNote">Ward assignment note</label><textarea id="wardAssignmentNote" class="ll-textarea admin-note" placeholder="Optional shift, ward, or floor note"></textarea></div>
                        <div class="ll-inline-actions"><button class="ll-button-accent" type="button" onclick="upsertNurseProfile()">Save nurse setup</button></div>
                    </div>
                </article>
                <article class="ll-panel admin-role-card">
                    <div class="ll-panel-heading"><div><h2>IT setup</h2><p>Assign department scope for admissions, ward operations, and bed allocation tools.</p></div><span class="ll-status-chip is-warning">IT</span></div>
                    <div class="ll-form-grid">
                        <div class="ll-field"><label class="ll-label" for="itUserId">Approved IT account</label><input id="itUserId" class="ll-input" type="number" placeholder="Selected from applicant queue"></div>
                        <div class="ll-field"><label class="ll-label" for="itDepartmentId">Department</label><select id="itDepartmentId" class="ll-select"><option value="">Select department</option></select></div>
                        <p class="ll-helper">Use this after approval so the IT workspace opens with the right department boundaries.</p>
                        <div class="ll-inline-actions"><button class="ll-button" type="button" onclick="assignItDepartment()">Save IT setup</button></div>
                    </div>
                </article>
            </div>
        </section>
        <div class="ll-hidden-debug" aria-hidden="true"><pre id="ctx"></pre><pre id="out"></pre></div>
    </div>
@endsection

@push('scripts')
<script>
const out=document.getElementById('out');const ctx=document.getElementById('ctx');const API='/api';const state={pendingApplications:[],departments:[]};
function pickMessage(data){if(!data)return'';if(typeof data==='string')return data;if(typeof data?.message==='string')return data.message;if(typeof data?.error==='string')return data.error;if(typeof data?.status==='string')return data.status;if(typeof data?.details==='string')return data.details;return''}
function deriveTone(result){const status=Number(result?.status||0);if(!status||status<300)return'success';if(status===401||status===403)return'warning';return'danger'}
function deriveTitle(result){const message=pickMessage(result?.data);if(message&&result?.status>=400)return'Action needs attention';if(message)return'Action completed';return result?.status>=400?'Request failed':'Action completed'}
function deriveBody(result){const message=pickMessage(result?.data);if(message)return message;return result?.status>=400?'The request could not be completed. Review the selected account or role setup and try again.':'The admin action completed successfully.'}
function setActionAlert(tone,title,body){const root=document.getElementById('actionAlert');root.classList.remove('ll-hidden-debug','is-success','is-warning','is-danger');root.classList.add(tone==='danger'?'is-danger':tone==='warning'?'is-warning':'is-success');document.getElementById('actionAlertTitle').textContent=title;document.getElementById('actionAlertBody').textContent=body}
function write(result,config={}){out.textContent=typeof result==='string'?result:JSON.stringify(result,null,2);if(config.skipAlert)return;setActionAlert(config.tone||deriveTone(result),config.title||deriveTitle(result),config.body||deriveBody(result))}
function setSessionAlert(show){document.getElementById('sessionAlert').classList.toggle('ll-hidden-debug',!show)}
function updateAccountState(title,body){document.getElementById('accountStateTitle').textContent=title;document.getElementById('accountStateBody').textContent=body}
function updatePrefillState(title,body){document.getElementById('prefillStateTitle').textContent=title;document.getElementById('prefillStateBody').textContent=body}
function formatRole(role){return({ITWorker:'IT Worker'})[role]||role||'Unassigned role'}
function formatDate(value){if(!value)return'Recently updated';const date=new Date(value);if(Number.isNaN(date.getTime()))return'Recently updated';return date.toLocaleString()}
function refreshContext(){const tokenPresent=!!localStorage.getItem('ADMIN_TOKEN');const data={ADMIN_USER_ID:localStorage.getItem('ADMIN_USER_ID'),ADMIN_EMAIL:localStorage.getItem('ADMIN_EMAIL'),ADMIN_TOKEN_PRESENT:tokenPresent,CURRENT_USER_EMAIL:localStorage.getItem('CURRENT_USER_EMAIL'),CURRENT_USER_ROLES:JSON.parse(localStorage.getItem('CURRENT_USER_ROLES')||'[]'),PATIENT_ID:localStorage.getItem('PATIENT_ID'),PATIENT_EMAIL:localStorage.getItem('PATIENT_EMAIL')};document.getElementById('tokenState').textContent=tokenPresent?'Ready':'Missing';document.getElementById('sessionStateCopy').textContent=tokenPresent?'Admin access is available for queue review, account control, and provisioning.':'Sign in as an administrator before running protected actions in this workspace.';setSessionAlert(!tokenPresent);ctx.textContent=JSON.stringify(data,null,2)}
function adminToken(){return localStorage.getItem('ADMIN_TOKEN')}
function targetId(){return document.getElementById('userId').value.trim()}
function loadPatientId(){document.getElementById('userId').value=localStorage.getItem('PATIENT_ID')||localStorage.getItem('CURRENT_USER_ID')||''}
async function call(path,method,body=null){const token=adminToken();if(!token)return{status:401,data:{message:'Admin sign-in is required before this action can run.'}};const headers={Accept:'application/json','Content-Type':'application/json',Authorization:`Bearer ${token}`};const res=await fetch(API+path,{method,headers,body:body?JSON.stringify(body):undefined});const text=await res.text();try{return{status:res.status,data:JSON.parse(text)}}catch{return{status:res.status,data:text}}}
function escapeHtml(value){if(value===null||value===undefined)return'';return String(value).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;')}
async function loadDepartments(){try{const response=await fetch('/api/public/departments',{headers:{Accept:'application/json'}});const text=await response.text();let data={};try{data=JSON.parse(text)}catch{}state.departments=Array.isArray(data?.departments)?data.departments:[];document.getElementById('departmentCount').textContent=String(state.departments.length);const options=`<option value=\"\">Select department</option>${state.departments.map((department)=>`<option value=\"${department.id}\">${escapeHtml(department.dept_name)}</option>`).join('')}`;document.getElementById('doctorDepartmentId').innerHTML=options;document.getElementById('nurseDepartmentId').innerHTML=options;document.getElementById('itDepartmentId').innerHTML=options;if(!state.departments.length){write({status:404,data:{message:'No departments were returned, so provisioning selections are currently empty.'}},{tone:'warning',title:'Departments unavailable'})}}catch(error){state.departments=[];document.getElementById('departmentCount').textContent='0';write({status:500,data:{message:'Departments could not be loaded right now.'}},{tone:'danger',title:'Departments unavailable'})}}
function renderPendingCards(){const root=document.getElementById('pendingCards');document.getElementById('pendingCount').textContent=String(state.pendingApplications.length);document.getElementById('pendingSummary').textContent=state.pendingApplications.length?`${state.pendingApplications.length} applicant${state.pendingApplications.length===1?'':'s'} waiting for review.`:'No pending applicants are waiting right now.';if(!state.pendingApplications.length){root.innerHTML=`<div class=\"ll-empty\"><strong>No pending applicants</strong><p>The queue is clear for now. Refresh again later if more staff applications arrive.</p></div>`;return}root.innerHTML=state.pendingApplications.map((application)=>{const role=formatRole(application.applied_role);const applicantName=escapeHtml(application.user?.full_name||'Unnamed applicant');const applicantEmail=escapeHtml(application.user?.email||'No email on file');const departmentName=escapeHtml(application.applied_department||'Department not provided');const status=escapeHtml(application.status||'Pending');const createdAt=escapeHtml(formatDate(application.created_at));const userId=application.user?.id?`User #${escapeHtml(application.user.id)}`:'User not linked yet';return `<article class=\"ll-card admin-app-card\"><div class=\"ll-panel-heading\"><div><h3>${applicantName}</h3><p>${applicantEmail}</p></div><span class=\"ll-status-chip is-warning\">${status}</span></div><div class=\"admin-chip-row\"><span class=\"admin-chip\">${role}</span><span class=\"admin-chip\">${departmentName}</span><span class=\"admin-chip\">Application #${application.id}</span><span class=\"admin-chip\">${userId}</span></div><div class=\"ll-form-grid\" style=\"margin-top:16px;\"><div class=\"ll-field\"><label class=\"ll-label\" for=\"reviewNote-${application.id}\">Review note</label><textarea id=\"reviewNote-${application.id}\" class=\"ll-textarea admin-note\" placeholder=\"Add context for the approval or rejection\">${escapeHtml(application.review_notes||'')}</textarea></div><div class=\"ll-helper\">Submitted ${createdAt}</div><div class=\"ll-inline-actions\"><button class=\"ll-button-accent\" type=\"button\" onclick=\"approveApplication(${application.id})\">Approve</button><button class=\"ll-button-ghost\" type=\"button\" onclick=\"prefillSetup(${application.id})\">Use in setup</button><button class=\"ll-button\" type=\"button\" onclick=\"rejectApplication(${application.id})\">Reject</button></div></div></article>`}).join('')}
async function loadPendingApplications(){const root=document.getElementById('pendingCards');root.innerHTML=`<div class=\"ll-empty\"><strong class=\"ll-loading\">Loading applicants</strong><p>Pulling the latest pending applications into the admin queue.</p></div>`;const result=await call('/admin/applications?status=Pending','GET');if(result.status<300){state.pendingApplications=Array.isArray(result.data?.applications)?result.data.applications:[];document.getElementById('lastQueueRefresh').textContent=new Date().toLocaleTimeString([],{hour:'numeric',minute:'2-digit'});renderPendingCards();write(result,{tone:'success',title:'Applicant queue updated',body:state.pendingApplications.length?`${state.pendingApplications.length} pending applicant${state.pendingApplications.length===1?'':'s'} loaded into the review queue.`:'The queue is empty right now.'});return}state.pendingApplications=[];renderPendingCards();write(result,{tone:'danger',title:'Applicant queue unavailable'})}
function applicationNote(applicationId){const field=document.getElementById(`reviewNote-${applicationId}`);return field?field.value.trim():''}
function prefillSetupFromApplication(application){if(!application)return;const role=application.applied_role;const userId=application.user?.id||'';const departmentId=application.applied_department_id||'';const applicantName=application.user?.full_name||'Selected applicant';document.getElementById('userId').value=String(userId||'');if(role==='Nurse'){document.getElementById('nurseUserId').value=String(userId||'');document.getElementById('nurseDepartmentId').value=departmentId?String(departmentId):''}if(role==='Doctor'){document.getElementById('doctorUserId').value=String(userId||'');document.getElementById('doctorDepartmentId').value=departmentId?String(departmentId):''}if(role==='ITWorker'){document.getElementById('itUserId').value=String(userId||'');document.getElementById('itDepartmentId').value=departmentId?String(departmentId):''}updatePrefillState(`${applicantName} is ready for setup`,`${formatRole(role)} provisioning was prefilled with the linked account and department so you can save without retyping.`)}
async function approveApplication(applicationId){const review_notes=applicationNote(applicationId);const body=review_notes?{review_notes}:{};const result=await call(`/admin/applications/${applicationId}/approve`,'POST',body);write(result,{tone:result.status<300?'success':deriveTone(result),title:result.status<300?'Applicant approved':'Approval could not be completed'});if(result.status<300&&result.data?.application){prefillSetupFromApplication(result.data.application)}await loadPendingApplications()}
async function rejectApplication(applicationId){const review_notes=applicationNote(applicationId);const body=review_notes?{review_notes}:{};const result=await call(`/admin/applications/${applicationId}/reject`,'POST',body);write(result,{tone:result.status<300?'warning':deriveTone(result),title:result.status<300?'Applicant rejected':'Rejection could not be completed'});await loadPendingApplications()}
function prefillSetup(applicationId){const application=state.pendingApplications.find((item)=>Number(item.id)===Number(applicationId));if(!application)return;prefillSetupFromApplication(application);setActionAlert('success','Setup prefilled','The selected applicant was moved into the relevant provisioning form and account safety lookup.')}
function ensureProvisioningValues(userIdField,departmentField,title){const userId=Number(document.getElementById(userIdField).value);const departmentId=Number(document.getElementById(departmentField).value);if(!userId||!departmentId){write({status:422,data:{message:`Choose both an approved account and a department before saving ${title.toLowerCase()}.`}},{tone:'warning',title:`${title} needs more information`});return null}return{userId,departmentId}}
async function upsertDoctorProfile(){const values=ensureProvisioningValues('doctorUserId','doctorDepartmentId','Doctor setup');if(!values)return;const result=await call('/admin/doctors/profile','POST',values);write(result,{tone:result.status<300?'success':deriveTone(result),title:result.status<300?'Doctor setup saved':'Doctor setup failed'})}
async function upsertNurseProfile(){const values=ensureProvisioningValues('nurseUserId','nurseDepartmentId','Nurse setup');if(!values)return;const payload={...values,wardAssignmentNote:document.getElementById('wardAssignmentNote').value.trim()||null};const result=await call('/admin/nurses/profile','POST',payload);write(result,{tone:result.status<300?'success':deriveTone(result),title:result.status<300?'Nurse setup saved':'Nurse setup failed'})}
async function assignItDepartment(){const values=ensureProvisioningValues('itUserId','itDepartmentId','IT setup');if(!values)return;const result=await call('/ward/it/department-admins','POST',values);write(result,{tone:result.status<300?'success':deriveTone(result),title:result.status<300?'IT setup saved':'IT setup failed'})}
function requireTargetId(){const id=targetId();if(id)return id;write({status:422,data:{message:'Select an account before running an access action.'}},{tone:'warning',title:'Account selection required'});return null}
async function freezeUser(){const id=requireTargetId();if(!id)return;const result=await call(`/admin/users/${id}/freeze`,'POST');write(result,{tone:result.status<300?'warning':deriveTone(result),title:result.status<300?'Account frozen':'Freeze action failed'});if(result.status<300){updateAccountState('Access frozen',`Account #${id} was frozen successfully. Restore access when the user is cleared to continue.`)}}
async function unfreezeUser(){const id=requireTargetId();if(!id)return;const result=await call(`/admin/users/${id}/unfreeze`,'POST');write(result,{tone:result.status<300?'success':deriveTone(result),title:result.status<300?'Access restored':'Restore action failed'});if(result.status<300){updateAccountState('Access restored',`Account #${id} is active again and ready to return to the workspace.`)}}
async function statusUser(){const id=requireTargetId();if(!id)return;const result=await call(`/admin/users/${id}/status`,'GET');write(result,{tone:result.status<300?'success':deriveTone(result),title:result.status<300?'Account status loaded':'Status check failed'});if(result.status<300){const status=result.data?.status||result.data?.user?.status||'Status loaded';updateAccountState(`Account #${id}: ${status}`,'The latest account status was retrieved successfully. Use freeze or restore actions from this same panel when needed.')}}
refreshContext();loadPatientId();loadDepartments();loadPendingApplications();
</script>
@endpush
