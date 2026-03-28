@extends('ui.layouts.app')

@section('title', 'Blood Matching Center')
@section('workspace_label', 'Blood Bank operations workspace')
@section('hero_badge', 'Blood Bank IT / Admin')
@section('hero_title', 'Coordinate blood requests, donor responses, donations, and fulfillment.')
@section('hero_description', 'This page is the Blood Bank operations center for admins and IT workers assigned to Blood Bank work.')
@section('meta_title', 'Blood Matching Center')
@section('meta_copy', 'Requests, donors, donations, and fulfillment')

@push('styles')
<style>
    :root{--ink:#11283b;--muted:#5a6f7f;--line:rgba(17,40,59,.12);--card:rgba(255,255,255,.94);--primary:#0369a1;--primary-strong:#075985;--accent:#c2410c;--ok:#166534;--danger:#b91c1c;--warn:#9a3412;--shadow:0 18px 36px rgba(15,23,42,.14)}
    .grid,.row,.controls,.stats,.actions,.cards{display:grid;gap:12px}.grid{gap:14px}.row{grid-template-columns:repeat(2,minmax(0,1fr))}.controls{grid-template-columns:repeat(2,minmax(0,1fr))}.stats{grid-template-columns:repeat(4,minmax(0,1fr))}.actions{grid-template-columns:repeat(3,max-content);justify-content:start}.cards{grid-template-columns:repeat(auto-fill,minmax(260px,1fr))}
    .card{border:1px solid var(--line);border-radius:18px;background:var(--card);box-shadow:var(--shadow);padding:14px}.card h3{margin:0}.hint{margin:6px 0 0;color:var(--muted);font-size:.93rem;line-height:1.7}
    .label{display:block;margin-bottom:6px;color:var(--muted);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.input,.select,.textarea{width:100%;border-radius:12px;border:1px solid rgba(17,40,59,.18);background:rgba(255,255,255,.96);color:var(--ink);font:inherit;padding:10px 11px;outline:none}.textarea{min-height:84px;resize:vertical}
    .btn{border:0;border-radius:11px;padding:10px 13px;font:inherit;font-size:13px;font-weight:700;cursor:pointer}.btn[disabled]{opacity:.62;pointer-events:none}.btn-main{background:var(--primary);color:#fff}.btn-main:hover{background:var(--primary-strong)}.btn-soft{background:rgba(17,40,59,.08);color:var(--ink)}.btn-accent{background:var(--accent);color:#fff}
    .stat{border:1px solid var(--line);border-radius:14px;background:rgba(255,255,255,.88);padding:12px}.stat small{display:block;margin-bottom:6px;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;font-weight:800}.stat strong{display:block;font-size:1.4rem}
    .badge{display:inline-flex;align-items:center;border-radius:999px;padding:5px 10px;font-size:.72rem;font-weight:800}.badge.ok{color:var(--ok);background:rgba(22,101,52,.14)}.badge.pending{color:var(--primary-strong);background:rgba(3,105,161,.14)}.badge.warn{color:var(--warn);background:rgba(154,52,18,.14)}.badge.danger{color:var(--danger);background:rgba(185,28,28,.12)}
    .wrap{margin-top:10px;border:1px solid var(--line);border-radius:12px;overflow:auto;background:rgba(255,255,255,.96)}.table{width:100%;border-collapse:collapse;font-size:12px}.table th,.table td{text-align:left;white-space:nowrap;padding:9px;border-bottom:1px solid rgba(17,40,59,.08)}.table th{position:sticky;top:0;background:rgba(246,250,255,.98);color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.05em}.table tr.active,.table tr:hover{background:rgba(14,165,233,.08)}
    .meta{color:var(--muted);font-size:.92rem;line-height:1.7}.pre{margin:0;min-height:120px;max-height:320px;overflow:auto;border-radius:12px;border:1px solid var(--line);background:#101c33;color:#d7e3ff;padding:11px;font-size:12px}
    .steps{margin:10px 0 0;padding-left:18px;color:var(--muted);font-size:.93rem;line-height:1.8}
    .auth-ok{color:var(--ok);font-weight:800}.auth-warn{color:var(--warn);font-weight:800}.auth-danger{color:var(--danger);font-weight:800}
    .toast-stack{position:fixed;right:12px;bottom:12px;display:grid;gap:8px;z-index:40}.toast{border-radius:10px;padding:9px 12px;color:#fff;font-size:12px;box-shadow:0 10px 22px rgba(15,23,42,.3)}.toast.ok{background:#166534}.toast.error{background:#b91c1c}
    @media (max-width:1100px){.row,.controls,.actions,.stats{grid-template-columns:1fr}}
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="/ui/blood-matching"><strong>Blood Matching</strong><span>Current area</span></a>
    <a href="/ui/nurse-dashboard"><strong>Nurse Dashboard</strong><span>Blood Bank screening side</span></a>
    <a href="/ui/donor-dashboard"><strong>Donor Dashboard</strong><span>Donor response side</span></a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Blood Bank scope</strong>
        <p>This board is intended for admins and IT workers assigned to the <code>Blood Bank</code> department.</p>
    </div>
@endsection

@section('content')
    <div class="grid">
        <div class="stats">
            <div class="stat"><small>Requests</small><strong id="stRequests">0</strong></div>
            <div class="stat"><small>Matched</small><strong id="stMatched">0</strong></div>
            <div class="stat"><small>Accepted</small><strong id="stAccepted">0</strong></div>
            <div class="stat"><small>Donor Search</small><strong id="stDonors">0</strong></div>
        </div>

        <div class="row">
            <div class="card">
                <h3>How to use this page</h3>
                <p class="hint">This is the real Blood Bank operations page. Test it in this order so the workflow stays understandable.</p>
                <ol class="steps">
                    <li>Refresh the board and pick a request from the request board.</li>
                    <li>Review compatible donor suggestions and notify selected donors.</li>
                    <li>After donor acceptance and nurse screening, load the donor into donation logging.</li>
                    <li>Record the real donation, with or without a linked request.</li>
                    <li>Approve and fulfill the request, optionally consuming inventory if stored blood was used.</li>
                </ol>
            </div>

            <div class="card">
                <h3>Auth status</h3>
                <p id="authStatus" class="hint">Checking stored token state.</p>
                <div class="actions" style="margin-top:12px">
                    <button id="btnUseUserToken" class="btn btn-soft" type="button" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    <button id="btnUseAdminToken" class="btn btn-soft" type="button" onclick="useStoredAdminToken()">Use ADMIN_TOKEN</button>
                    <button id="btnRefreshAll" class="btn btn-main" type="button" onclick="refreshAll()">Refresh Board</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <h3>Request board</h3>
                <p class="hint">This is the entry point. Pick one blood request and the rest of the page will align to that request.</p>
                <div class="controls" style="margin-top:12px">
                    <div><label class="label" for="tokenInput">Bearer token</label><input id="tokenInput" class="input" placeholder="Paste Admin or Blood Bank IT token"></div>
                    <div><label class="label" for="statusFilter">Status</label><select id="statusFilter" class="select"><option value="">All</option><option>Pending</option><option>Matched</option><option>Approved</option><option>Fulfilled</option><option>Rejected</option><option>Cancelled</option></select></div>
                    <div><label class="label" for="bloodGroupFilter">Blood group</label><select id="bloodGroupFilter" class="select"><option value="">All</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
                    <div><label class="label" for="departmentFilter">Patient department</label><select id="departmentFilter" class="select"><option value="">All departments</option></select></div>
                    <div><label class="label" for="fulfillmentBankId">Blood bank</label><select id="fulfillmentBankId" class="select"><option value="">Keep request bank / none</option></select></div>
                    <div><label class="label" for="requestLimit">Request limit</label><input id="requestLimit" class="input" type="number" min="1" max="150" value="40"></div>
                </div>
                <div class="wrap"><table class="table"><thead><tr><th>ID</th><th>Patient</th><th>Need</th><th>Units</th><th>Status</th><th>Accepted</th></tr></thead><tbody id="requestsBody"></tbody></table></div>
            </div>

            <div class="card">
                <h3>Selected request actions</h3>
                <p class="hint">Use this after a request is selected. Notify donors first, then approve an accepted donor, then fulfill the request when blood is actually available.</p>
                <p id="selectedHint" class="hint">Pick a request to start donor matching.</p>
                <div id="selectedStatusBadge" class="badge warn">No request</div>
                <div id="selectedMeta" class="meta" style="margin-top:10px">No request selected.</div>
                <div class="controls" style="margin-top:12px">
                    <div><label class="label" for="selectedMatchId">Accepted match</label><input id="selectedMatchId" class="input" type="number" placeholder="Pick from match timeline below"></div>
                    <div><label class="label" for="linkedRequestId">Linked request ID</label><input id="linkedRequestId" class="input" type="number" placeholder="Auto-filled from selected request"></div>
                </div>
                <label class="label" for="notifyMessage" style="margin-top:10px">Notification message</label>
                <textarea id="notifyMessage" class="textarea" placeholder="Use text scheduling here, for example: Please come within next 3 days"></textarea>
                <label class="label" for="workflowNote" style="margin-top:10px">Workflow note</label>
                <textarea id="workflowNote" class="textarea" placeholder="Optional approval or fulfillment note"></textarea>
                <label class="hint" style="display:block;margin-top:10px"><input id="consumeInventory" type="checkbox"> Deduct inventory during fulfillment when stored blood is used</label>
                <div class="actions" style="margin-top:12px">
                    <button id="btnNotifyAuto" class="btn btn-main" type="button" onclick="notifyAuto()">Auto Notify</button>
                    <button id="btnApproveMatch" class="btn btn-soft" type="button" onclick="approveSelectedMatch()">Approve Donor</button>
                    <button id="btnFulfillRequest" class="btn btn-accent" type="button" onclick="fulfillSelectedRequest()">Fulfill Request</button>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Compatible donor suggestions</h3>
            <p class="hint">These are request-aware suggestions. Tick donors here before using Auto Notify.</p>
            <div id="suggestionsGrid" class="cards" style="margin-top:12px"></div>
        </div>

        <div class="card">
            <h3>Match timeline</h3>
            <p class="hint">This is the donor response timeline for the selected request. Use an accepted match to feed the donation and approval flow.</p>
            <div class="wrap"><table class="table"><thead><tr><th>Match ID</th><th>Donor</th><th>Group</th><th>Status</th><th>Notified</th><th>Responded</th><th>Action</th></tr></thead><tbody id="matchesBody"></tbody></table></div>
        </div>

        <div class="row">
            <div class="card">
                <h3>Staff donor search</h3>
                <p class="hint">This is the broader staff lookup area. Use it when you want donors outside the current suggestions list or for casual donation handling.</p>
                <div class="controls" style="margin-top:12px">
                    <div><label class="label" for="donorSearchQuery">Donor search</label><input id="donorSearchQuery" class="input" placeholder="Donor name, email, id"></div>
                    <div><label class="label" for="donorSearchRequestId">Request filter</label><input id="donorSearchRequestId" class="input" type="number" min="1" placeholder="Optional request id"></div>
                    <div><label class="label" for="donorSearchBloodGroup">Blood group</label><select id="donorSearchBloodGroup" class="select"><option value="">All</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
                    <div><label class="label" for="donorSearchEligible">Eligibility</label><select id="donorSearchEligible" class="select"><option value="">All</option><option value="true">Eligible</option><option value="false">Not eligible</option></select></div>
                </div>
                <div class="actions" style="margin-top:12px"><button id="btnLoadDonors" class="btn btn-main" type="button" onclick="loadStaffDonors()">Load Donors</button></div>
                <div id="staffDonorGrid" class="cards" style="margin-top:12px"></div>
            </div>

            <div class="card">
                <h3>Staff donation logging</h3>
                <p class="hint">This logs the real physical donation after a Blood Bank nurse health check. It supports both request-linked donations and casual walk-in donations.</p>
                <div class="controls" style="margin-top:12px">
                    <div><label class="label" for="donationDonorId">Donor ID</label><input id="donationDonorId" class="input" type="number" placeholder="Auto-filled from donor selection"></div>
                    <div><label class="label" for="donationHealthCheckId">Health check ID</label><select id="donationHealthCheckId" class="select"><option value="">Select latest nurse screening</option></select></div>
                    <div><label class="label" for="donationBankId">Blood bank</label><select id="donationBankId" class="select"><option value="">Choose blood bank</option></select></div>
                    <div><label class="label" for="donationDateTime">Donation datetime</label><input id="donationDateTime" class="input" type="datetime-local"></div>
                    <div><label class="label" for="donationBloodGroup">Blood group</label><select id="donationBloodGroup" class="select"><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
                    <div><label class="label" for="componentType">Component</label><select id="componentType" class="select"><option selected>WholeBlood</option><option>Plasma</option><option>Platelets</option><option>RBC</option></select></div>
                    <div><label class="label" for="unitsDonated">Units donated</label><input id="unitsDonated" class="input" type="number" min="1" max="5" value="1"></div>
                    <div><label class="label" for="donationNotes">Donation note</label><input id="donationNotes" class="input" placeholder="Optional staff note"></div>
                </div>
                <div class="actions" style="margin-top:12px">
                    <button id="btnLogDonation" class="btn btn-accent" type="button" onclick="logDonation()">Record Donation</button>
                    <button class="btn btn-soft" type="button" onclick="loadDonationHealthChecks()">Refresh Health Checks</button>
                </div>
                <div class="wrap"><table class="table"><thead><tr><th>Health Check ID</th><th>Time</th><th>Weight</th><th>Temp</th><th>Hb</th><th>Checked by</th></tr></thead><tbody id="donationHealthChecksBody"></tbody></table></div>
            </div>
        </div>

        <div class="card"><h3>API response</h3><pre id="out" class="pre"></pre></div>
    </div>
    <div id="toastStack" class="toast-stack"></div>
@endsection

@push('scripts')
<script>
const API='/api',out=document.getElementById('out');
const state={requests:[],matches:[],selectedRequestId:null,selectedDonorIds:new Set(),staffDonors:[]};
function byId(id){return document.getElementById(id)} function write(v){out.textContent=typeof v==='string'?v:JSON.stringify(v,null,2)}
function html(v){if(v===null||v===undefined)return'';return String(v).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;')}
function toast(message,type='ok'){const el=document.createElement('div');el.className=`toast ${type==='error'?'error':'ok'}`;el.textContent=message;byId('toastStack').appendChild(el);setTimeout(()=>el.remove(),2600)}
function setBusy(id,busy){const btn=byId(id);if(!btn)return;btn.disabled=busy;btn.dataset.label=btn.dataset.label||btn.textContent;btn.textContent=busy?'Working...':btn.dataset.label}
function setAuthStatus(message,kind='warn'){const el=byId('authStatus');if(!el)return;el.className=`hint ${kind==='ok'?'auth-ok':kind==='danger'?'auth-danger':'auth-warn'}`;el.textContent=message}
function useStoredUserToken(){byId('tokenInput').value=localStorage.getItem('USER_TOKEN')||'';syncAuthStatus()} function useStoredAdminToken(){byId('tokenInput').value=localStorage.getItem('ADMIN_TOKEN')||'';syncAuthStatus()}
function bootToken(){const user=localStorage.getItem('USER_TOKEN')||'',admin=localStorage.getItem('ADMIN_TOKEN')||'';byId('tokenInput').value=user||admin}
function hasToken(){return !!byId('tokenInput').value.trim()}
function syncAuthStatus(){const token=byId('tokenInput').value.trim();if(token){setAuthStatus('Stored token detected. You can refresh the board now.','ok')}else{setAuthStatus('No stored token detected. Login from /ui/auth or press Use USER_TOKEN / Use ADMIN_TOKEN.','warn')}}
async function call(path,method='GET',body=null,query=null){const token=byId('tokenInput').value.trim();if(!token){setAuthStatus('No token is loaded in this page. Login again or inject USER_TOKEN / ADMIN_TOKEN.','danger');return{status:401,data:{message:'Token missing. Use USER_TOKEN or ADMIN_TOKEN.'}}}const qs=query?new URLSearchParams(query).toString():'';const endpoint=`${API}${path}${qs?`?${qs}`:''}`;const res=await fetch(endpoint,{method,headers:{Accept:'application/json','Content-Type':'application/json',Authorization:`Bearer ${token}`},body:body?JSON.stringify(body):undefined});const text=await res.text();let data=text;try{data=JSON.parse(text)}catch{}if(res.status===401){setAuthStatus('Stored token was rejected by the API. Login again from /ui/auth, then return here.','danger')}return{status:res.status,data}}
function badge(status){if(['Accepted','Completed','Fulfilled','Eligible'].includes(status))return`<span class="badge ok">${html(status)}</span>`;if(['Pending','Matched','Approved','Notified','Suggested'].includes(status))return`<span class="badge pending">${html(status)}</span>`;if(['Declined','Rejected','Cancelled','Not Eligible'].includes(status))return`<span class="badge danger">${html(status)}</span>`;return`<span class="badge warn">${html(status||'-')}</span>`}
function selectedRequest(){return state.requests.find(r=>Number(r.id)===Number(state.selectedRequestId))||null}
function updateStats(){byId('stRequests').textContent=String(state.requests.length);byId('stMatched').textContent=String(state.requests.filter(r=>r.status==='Matched').length);byId('stAccepted').textContent=String(state.matches.filter(r=>r.status==='Accepted').length);byId('stDonors').textContent=String(state.staffDonors.length)}
async function loadDepartments(){const res=await fetch('/api/public/departments',{headers:{Accept:'application/json'}});const text=await res.text();let data={};try{data=JSON.parse(text)}catch{};const rows=Array.isArray(data?.departments)?data.departments:[];byId('departmentFilter').innerHTML=['<option value="">All departments</option>'].concat(rows.map(r=>`<option value="${r.id}">${html(r.dept_name)}</option>`)).join('')}
async function loadBanks(){const r=await call('/blood/schema/banks');if(r.status>=300){write(r);toast(r.data?.message||'Could not load banks','error');return}const rows=Array.isArray(r.data?.banks)?r.data.banks:[];const opts=['<option value="">Keep request bank / none</option>'].concat(rows.map(row=>`<option value="${row.id}">${html(row.bank_name)}</option>`)).join('');byId('fulfillmentBankId').innerHTML=opts;byId('donationBankId').innerHTML=['<option value="">Choose blood bank</option>'].concat(rows.map(row=>`<option value="${row.id}">${html(row.bank_name)}</option>`)).join('')}
function requestQuery(){const q={},status=byId('statusFilter').value,bg=byId('bloodGroupFilter').value,dept=byId('departmentFilter').value,limit=byId('requestLimit').value;if(status)q.status=status;if(bg)q.bloodGroup=bg;if(dept)q.departmentId=Number(dept);if(limit)q.limit=Number(limit);return q}
function renderRequests(rows){state.requests=rows;updateStats();byId('requestsBody').innerHTML=rows.length?rows.map(r=>`<tr class="${Number(state.selectedRequestId)===Number(r.id)?'active':''}" onclick="selectRequest(${Number(r.id)})"><td>#${r.id}</td><td>${html(r.patient_name||'-')}</td><td>${html(r.blood_group_needed)} / ${html(r.component_type)}</td><td>${r.units_required}</td><td>${badge(r.status)}</td><td>${r.accepted_count}</td></tr>`).join(''):'<tr><td colspan="6">No blood requests found.</td></tr>'}
function renderSelectedRequest(){const r=selectedRequest();if(!r){byId('selectedHint').textContent='Pick a request to start donor matching.';byId('selectedStatusBadge').className='badge warn';byId('selectedStatusBadge').textContent='No request';byId('selectedMeta').textContent='No request selected.';return}byId('selectedHint').textContent=`Request #${r.id} is active. Use donor suggestions, nurse screening, and donation logging to move it forward.`;byId('selectedStatusBadge').outerHTML=badge(r.status).replace('<span','<div id="selectedStatusBadge"').replace('</span>','</div>');byId('selectedMeta').innerHTML=`<strong>#${r.id}</strong> | ${html(r.blood_group_needed)} ${html(r.component_type)} | Units ${r.units_required}<br>Patient: ${html(r.patient_name||'-')} (${html(r.patient_email||'-')})<br>Department: ${html(r.department_name||'-')} | Bank: ${html(r.bank_name||'Not set')}<br>Visible inventory: <strong>${r.available_units}</strong> | Accepted donors: <strong>${r.accepted_count}</strong>`;byId('linkedRequestId').value=String(r.id);if(r.blood_group_needed)byId('donationBloodGroup').value=r.blood_group_needed;byId('donorSearchRequestId').value=String(r.id)}
async function loadRequests(){const r=await call('/blood/matching/requests','GET',null,requestQuery());write(r);if(r.status>=300){toast(r.data?.message||'Could not load requests','error');return}renderRequests(Array.isArray(r.data?.requests)?r.data.requests:[]);if(!state.selectedRequestId&&state.requests.length)await selectRequest(state.requests[0].id);else renderSelectedRequest()}
function toggleDonor(id,checked){if(checked)state.selectedDonorIds.add(id);else state.selectedDonorIds.delete(id)}
function renderSuggestions(rows){byId('suggestionsGrid').innerHTML=rows.length?rows.map(r=>`<article class="card"><div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"><label class="hint" style="margin:0"><input type="checkbox" ${state.selectedDonorIds.has(r.donor_id)?'checked':''} onchange="toggleDonor(${r.donor_id},this.checked)"> <strong>${html(r.donor_name||`Donor #${r.donor_id}`)}</strong></label>${badge(r.compatibility_label)}</div><p class="hint">${html(r.donor_email||'-')}</p><div class="meta">Donor ID: <strong>#${r.donor_id}</strong><br>Group: <strong>${html(r.donor_blood_group)}</strong><br>Eligible: <strong>${r.is_eligible?'Yes':'No'}</strong><br>Latest check: ${r.last_check_datetime?new Date(r.last_check_datetime).toLocaleString():'No check yet'}</div></article>`).join(''):'<div class="card"><p class="hint">No compatible donors found.</p></div>'}
async function loadSuggestions(){if(!state.selectedRequestId){renderSuggestions([]);return}const r=await call(`/blood/matching/requests/${state.selectedRequestId}/suggestions`,'GET',null,{limit:20});write(r);if(r.status>=300){toast(r.data?.message||'Could not load donor suggestions','error');return}renderSuggestions(Array.isArray(r.data?.suggestions)?r.data.suggestions:[])}
function pickMatch(matchId,donorId=null,group=''){byId('selectedMatchId').value=String(matchId);if(donorId){byId('donationDonorId').value=String(donorId);loadDonationHealthChecks()}if(group)byId('donationBloodGroup').value=group}
function renderMatches(rows){state.matches=rows;updateStats();byId('matchesBody').innerHTML=rows.length?rows.map(r=>`<tr><td>#${r.id}</td><td>${html(r.donor_name||'-')}</td><td>${html(r.donor_blood_group||'-')}</td><td>${badge(r.status)}</td><td>${r.notified_at?new Date(r.notified_at).toLocaleString():'-'}</td><td>${r.responded_at?new Date(r.responded_at).toLocaleString():'-'}</td><td>${['Accepted','Completed'].includes(r.status)?`<button class="btn btn-soft" type="button" onclick="pickMatch(${r.id},${r.donor_id},'${r.donor_blood_group||''}')">Use</button>`:'<span class="hint">Wait</span>'}</td></tr>`).join(''):'<tr><td colspan="7">No match records yet.</td></tr>'}
async function loadMatches(){if(!state.selectedRequestId){renderMatches([]);return}const r=await call(`/blood/matching/requests/${state.selectedRequestId}/matches`);write(r);if(r.status>=300){toast(r.data?.message||'Could not load matches','error');return}renderMatches(Array.isArray(r.data?.matches)?r.data.matches:[])}
async function selectRequest(id){const nextId=Number(id);if(Number(state.selectedRequestId)!==nextId){state.selectedDonorIds.clear();byId('selectedMatchId').value=''}state.selectedRequestId=nextId;renderRequests(state.requests);renderSelectedRequest();await Promise.all([loadSuggestions(),loadMatches(),loadStaffDonors()])}
async function notifyAuto(){if(!state.selectedRequestId)return toast('Select a request first.','error');setBusy('btnNotifyAuto',true);const r=await call(`/blood/matching/requests/${state.selectedRequestId}/notify`,'POST',{donorIds:Array.from(state.selectedDonorIds),message:byId('notifyMessage').value.trim()||null,suggestedLimit:6});setBusy('btnNotifyAuto',false);write(r);if(r.status>=300)return toast(r.data?.message||'Could not notify donors','error');state.selectedDonorIds.clear();toast(`Notifications sent: ${r.data?.sent_count??0}`);await Promise.all([loadRequests(),loadSuggestions(),loadMatches()])}
async function approveSelectedMatch(){if(!state.selectedRequestId)return toast('Select a request first.','error');const matchId=Number(byId('selectedMatchId').value||0);if(!matchId)return toast('Choose an accepted donor match first.','error');setBusy('btnApproveMatch',true);const r=await call(`/blood/matching/requests/${state.selectedRequestId}/approve`,'POST',{matchId,bloodBankId:byId('fulfillmentBankId').value?Number(byId('fulfillmentBankId').value):null,note:byId('workflowNote').value.trim()||null});setBusy('btnApproveMatch',false);write(r);if(r.status>=300)return toast(r.data?.message||'Could not approve donor','error');toast('Accepted donor approved.');await Promise.all([loadRequests(),loadMatches()])}
async function fulfillSelectedRequest(){if(!state.selectedRequestId)return toast('Select a request first.','error');setBusy('btnFulfillRequest',true);const r=await call(`/blood/matching/requests/${state.selectedRequestId}/fulfill`,'POST',{matchId:byId('selectedMatchId').value?Number(byId('selectedMatchId').value):null,bloodBankId:byId('fulfillmentBankId').value?Number(byId('fulfillmentBankId').value):null,consumeInventory:!!byId('consumeInventory').checked,note:byId('workflowNote').value.trim()||null});setBusy('btnFulfillRequest',false);write(r);if(r.status>=300)return toast(r.data?.message||'Could not fulfill request','error');toast('Blood request fulfilled.');await Promise.all([loadRequests(),loadMatches()])}
function donorQuery(){const q={},search=byId('donorSearchQuery').value.trim(),requestId=byId('donorSearchRequestId').value.trim(),group=byId('donorSearchBloodGroup').value,eligible=byId('donorSearchEligible').value;if(search)q.q=search;if(requestId)q.requestId=Number(requestId);if(group)q.bloodGroup=group;if(eligible)q.eligible=eligible==='true';q.limit=20;return q}
function renderStaffDonors(rows){state.staffDonors=rows;updateStats();byId('staffDonorGrid').innerHTML=rows.length?rows.map(r=>`<article class="card"><div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"><strong>${html(r.donor_name||`Donor #${r.donor_id}`)}</strong>${badge(r.is_eligible?'Eligible':'Not Eligible')}</div><p class="hint">${html(r.donor_email||'-')}</p><div class="meta">Donor ID: <strong>#${r.donor_id}</strong><br>Blood group: <strong>${html(r.blood_group||'-')}</strong><br>Latest health check ID: <strong>${r.latest_health_check_id??'-'}</strong><br>Matched request: ${r.matched_request_id?`#${r.matched_request_id} (${html(r.matched_request_status||'-')})`:'None'}</div><div class="actions" style="margin-top:12px"><button class="btn btn-main" type="button" onclick="selectDonationDonor(${r.donor_id},'${r.blood_group||''}',${r.latest_health_check_id??'null'},${r.matched_request_id??'null'})">Use Donor</button></div></article>`).join(''):'<div class="card"><p class="hint">No donors found.</p></div>'}
async function loadStaffDonors(){setBusy('btnLoadDonors',true);const r=await call('/blood/matching/donors','GET',null,donorQuery());setBusy('btnLoadDonors',false);write(r);if(r.status>=300)return toast(r.data?.message||'Could not load staff donors','error');renderStaffDonors(Array.isArray(r.data?.donors)?r.data.donors:[])}
async function selectDonationDonor(donorId,group='',healthCheckId=null,requestId=null){byId('donationDonorId').value=String(donorId);if(group)byId('donationBloodGroup').value=group;if(requestId&&!byId('linkedRequestId').value)byId('linkedRequestId').value=String(requestId);await loadDonationHealthChecks(healthCheckId)}
function renderDonationHealthChecks(rows,preferredId=null){byId('donationHealthChecksBody').innerHTML=rows.length?rows.map(r=>`<tr><td>${r.id}</td><td>${r.check_datetime?new Date(r.check_datetime).toLocaleString():'-'}</td><td>${r.weight_kg??'-'}</td><td>${r.temperature_c??'-'}</td><td>${r.hemoglobin??'-'}</td><td>${html(r.checked_by_name||'-')}</td></tr>`).join(''):'<tr><td colspan="6">No donor health checks available.</td></tr>';byId('donationHealthCheckId').innerHTML=['<option value="">Select latest nurse screening</option>'].concat(rows.map(r=>`<option value="${r.id}">#${r.id} - ${r.check_datetime?new Date(r.check_datetime).toLocaleString():'No date'}</option>`)).join('');if(preferredId)byId('donationHealthCheckId').value=String(preferredId);else if(rows.length)byId('donationHealthCheckId').value=String(rows[0].id)}
async function loadDonationHealthChecks(preferredId=null){const donorId=Number(byId('donationDonorId').value||0);if(!donorId)return renderDonationHealthChecks([]);const r=await call(`/blood/matching/donors/${donorId}/health-checks`,'GET',null,{limit:12});write(r);if(r.status>=300)return toast(r.data?.message||'Could not load donor health checks','error');renderDonationHealthChecks(Array.isArray(r.data?.health_checks)?r.data.health_checks:[],preferredId)}
async function logDonation(){const donorId=Number(byId('donationDonorId').value||0);if(!donorId)return toast('Choose a donor first.','error');setBusy('btnLogDonation',true);const r=await call('/blood/matching/donations','POST',{donorId,bloodBankId:Number(byId('donationBankId').value||0),donationDateTime:byId('donationDateTime').value||null,bloodGroup:byId('donationBloodGroup').value||null,componentType:byId('componentType').value||null,unitsDonated:Number(byId('unitsDonated').value||1),linkedRequestId:byId('linkedRequestId').value?Number(byId('linkedRequestId').value):null,donorHealthCheckId:Number(byId('donationHealthCheckId').value||0),notes:byId('donationNotes').value.trim()||null});setBusy('btnLogDonation',false);write(r);if(r.status>=300)return toast(r.data?.message||'Could not log donation','error');toast('Donation recorded and inventory updated.');await Promise.all([loadRequests(),loadStaffDonors()])}
async function refreshAll(opts={silentIfMissingToken:false}){if(!hasToken()){bootToken();syncAuthStatus()}if(!hasToken()){if(!opts.silentIfMissingToken){write({status:401,data:{message:'Token missing. Use USER_TOKEN or ADMIN_TOKEN.'}});toast('Token missing. Use USER_TOKEN or ADMIN_TOKEN.','error')}return}setBusy('btnRefreshAll',true);try{await loadDepartments();await loadBanks();await loadRequests();if(state.selectedRequestId)await Promise.all([loadSuggestions(),loadMatches()]);await loadStaffDonors();setAuthStatus('Board loaded with the current stored token.','ok');toast('Blood matching board refreshed')}finally{setBusy('btnRefreshAll',false)}}
function boot(){bootToken();syncAuthStatus();loadDepartments();if(hasToken())refreshAll({silentIfMissingToken:true});else write('Login from /ui/auth or use stored USER_TOKEN / ADMIN_TOKEN to load the Blood Matching Center.')}
boot();
</script>
@endpush
