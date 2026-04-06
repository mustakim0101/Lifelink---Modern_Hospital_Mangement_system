@extends('ui.layouts.app')

@section('title', 'Doctor Dashboard')
@section('workspace_label', 'Clinical doctor workspace')
@section('hero_badge', 'Doctor')
@section('hero_title', 'Review patients, manage appointments, and create bed requests from one clinical dashboard.')
@section('hero_description', 'The doctor workspace keeps patient context, appointment flow, and admission support visible without making doctors work through raw developer tools.')
@section('meta_title', 'Doctor Dashboard')
@section('meta_copy', 'Clinical overview, appointments, and bed requests')

@push('styles')
<style>
    .doctor-grid,.doctor-card-grid{display:grid;gap:18px}
    .doctor-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .doctor-list{display:grid;gap:14px}
    .doctor-patient-card{padding:18px;border-radius:22px;border:1px solid rgba(140,170,201,.18);background:rgba(255,255,255,.9);box-shadow:var(--ll-shadow-sm)}
    .doctor-patient-card strong{font-size:1.04rem}
    .doctor-patient-card p{margin:8px 0 0;color:var(--ll-text-muted);line-height:1.6}
    .doctor-meta-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px}
    .doctor-meta-pill{display:inline-flex;align-items:center;min-height:32px;padding:0 12px;border-radius:999px;background:rgba(27,117,208,.08);border:1px solid rgba(27,117,208,.08);color:var(--ll-primary-strong);font-size:.8rem;font-weight:800}
    .doctor-console{margin-top:16px;min-height:140px;max-height:320px;overflow:auto;border-radius:18px;border:1px solid rgba(140,170,201,.18);background:#0f1c33;color:#d7e3ff;padding:16px;font-size:12px}
    @media (max-width:980px){.doctor-card-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#doctor-overview"><strong>Overview</strong><span>Today</span></a>
    <a href="#doctor-requests"><strong>Bed Request</strong><span>Admissions</span></a>
    <a href="#doctor-patients"><strong>Patients</strong><span>Context</span></a>
    <a href="#doctor-appointments"><strong>Appointments</strong><span>Flow</span></a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Doctor workflow</strong>
        <p>Load your session, review current patients, create a bed request when admission support is needed, then manage appointment changes from the same screen.</p>
    </div>
@endsection

@section('section_nav')
    <a href="#doctor-overview" class="is-active">Overview</a>
    <a href="#doctor-requests">Bed Request</a>
    <a href="#doctor-patients">Patients</a>
    <a href="#doctor-appointments">Appointments</a>
@endsection

@section('content')
    <div class="doctor-grid">
        <div id="doctorSessionAlert" class="ll-inline-alert is-warning ll-hidden-debug">
            <strong>Doctor session required</strong>
            <p>Use the current shared session or paste a doctor-capable token before loading profile, patients, or appointment data.</p>
        </div>

        <section id="doctor-overview" class="ll-section">
            <div class="ll-kpi-grid">
                <article class="ll-stat-card is-primary"><small>Profile</small><strong id="doctorName">--</strong><span id="doctorProfileSummary">Load profile to see department and clinical scope.</span></article>
                <article class="ll-stat-card is-success"><small>Patients loaded</small><strong id="patientCount">0</strong><span>Doctor patient cards update from the same doctor endpoints already in use.</span></article>
                <article class="ll-stat-card is-warning"><small>Appointments</small><strong id="appointmentCount">0</strong><span id="appointmentSummary">Upcoming appointment counts refresh from the selected filter.</span></article>
                <article class="ll-stat-card is-neutral"><small>Bed requests</small><strong id="bedRequestCount">0</strong><span id="bedRequestSummary">Recent admission support requests appear here after refresh.</span></article>
            </div>
        </section>

        <div class="doctor-card-grid">
            <article id="doctor-requests" class="ll-panel ll-section">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Create bed request</h2>
                        <p>Use this guided form when a patient needs admission or bed allocation support.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Admission support</span>
                </div>

                <div class="ll-form-grid" style="margin-top: 18px;">
                    <div class="ll-field">
                        <label class="ll-label" for="doctorTokenInput">Doctor session token</label>
                        <input id="doctorTokenInput" class="ll-input" placeholder="Use USER_TOKEN or paste a doctor token">
                    </div>

                    <div class="ll-inline-actions">
                        <button class="ll-button-ghost" type="button" onclick="useUserToken()">Use USER_TOKEN</button>
                        <button class="ll-button-ghost" type="button" onclick="doctorProfile()">Refresh profile</button>
                        <button class="ll-button-ghost" type="button" onclick="doctorBedRequests()">Refresh requests</button>
                    </div>

                    <div class="ll-form-grid-2">
                        <div class="ll-field">
                            <label class="ll-label" for="patientUserId">Patient user ID</label>
                            <input id="patientUserId" class="ll-input" type="number" placeholder="Patient user ID">
                        </div>
                        <div class="ll-field">
                            <label class="ll-label" for="careLevelRequested">Care level</label>
                            <select id="careLevelRequested" class="ll-select">
                                <option value="Ward">Ward</option>
                                <option value="ICU">ICU</option>
                                <option value="NICU">NICU</option>
                                <option value="CCU">CCU</option>
                            </select>
                        </div>
                    </div>

                    <div class="ll-field">
                        <label class="ll-label" for="diagnosis">Diagnosis</label>
                        <input id="diagnosis" class="ll-input" placeholder="Primary diagnosis">
                    </div>

                    <div class="ll-field">
                        <label class="ll-label" for="requestNotes">Request notes</label>
                        <textarea id="requestNotes" class="ll-textarea" placeholder="Optional admission note"></textarea>
                    </div>

                    <div class="ll-inline-actions">
                        <button class="ll-button" type="button" onclick="createBedRequest()">Create bed request</button>
                    </div>
                </div>
            </article>

            <article class="ll-panel">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Doctor session</h2>
                        <p>The shared login session is still supported. This card keeps profile context and quick refresh actions close by.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Session</span>
                </div>

                <div class="hub-value-list" style="margin-top: 18px;">
                    <div class="hub-value-item"><small>Department</small><strong id="doctorDepartment">Waiting for profile</strong></div>
                    <div class="hub-value-item"><small>Specialization</small><strong id="doctorSpecialization">Not loaded</strong></div>
                    <div class="hub-value-item"><small>Session state</small><strong id="doctorSessionState">Checking</strong></div>
                </div>

                <div class="ll-inline-actions" style="margin-top: 18px;">
                    <button class="ll-button-ghost" type="button" onclick="doctorPatients()">Load patients</button>
                    <button class="ll-button-ghost" type="button" onclick="doctorAppointments()">Load appointments</button>
                </div>
            </article>
        </div>

        <section id="doctor-patients" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Patient context</h2>
                    <p>These patient cards come from the existing doctor patient endpoint and keep clinical context readable.</p>
                </div>
            </div>
            <div id="patientCards" class="ll-collection-grid" style="margin-top: 18px;"></div>
        </section>

        <section id="doctor-appointments" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Appointments</h2>
                    <p>Filter upcoming appointments and cancel a specific booking without leaving the doctor workspace.</p>
                </div>
            </div>

            <div class="ll-filter-bar" style="margin-top: 18px;">
                <div>
                    <label class="ll-label" for="appointmentStatusFilter">Appointment status</label>
                    <select id="appointmentStatusFilter" class="ll-select">
                        <option value="">All appointment statuses</option>
                        <option value="Booked">Booked</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Completed">Completed</option>
                        <option value="NoShow">NoShow</option>
                    </select>
                </div>
                <div>
                    <label class="ll-label" for="cancelAppointmentId">Cancel appointment ID</label>
                    <input id="cancelAppointmentId" class="ll-input" type="number" placeholder="Appointment ID">
                </div>
                <div class="is-wide">
                    <label class="ll-label" for="cancelReason">Cancel reason</label>
                    <input id="cancelReason" class="ll-input" placeholder="Optional reason shown to the backend as before">
                </div>
                <div class="is-actions">
                    <div class="ll-inline-actions">
                        <button class="ll-button" type="button" onclick="doctorAppointments()">Refresh appointments</button>
                        <button class="ll-button-ghost" type="button" onclick="cancelAppointment()">Cancel selected appointment</button>
                    </div>
                </div>
            </div>

            <div class="ll-table-wrap" style="margin-top: 18px;">
                <table class="ll-table">
                    <thead>
                        <tr><th>ID</th><th>Patient</th><th>Department</th><th>Datetime</th><th>Status</th></tr>
                    </thead>
                    <tbody id="appointmentsBody">
                        <tr><td colspan="5">No appointments loaded yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ll-panel">
            <div class="ll-panel-heading">
                <div>
                    <h2>Bed request history</h2>
                    <p>Recent requests stay visible so admission support actions are easy to confirm after submission.</p>
                </div>
            </div>

            <div class="ll-table-wrap" style="margin-top: 18px;">
                <table class="ll-table">
                    <thead>
                        <tr><th>ID</th><th>Patient</th><th>Care level</th><th>Diagnosis</th><th>Status</th></tr>
                    </thead>
                    <tbody id="bedRequestsBody">
                        <tr><td colspan="5">No bed requests loaded yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ll-panel">
            <div class="ll-panel-heading">
                <div>
                    <h2>Debug response</h2>
                    <p>The raw response still exists for troubleshooting, but it stays contained below the operational UI.</p>
                </div>
                <span class="ll-status-chip is-soft">Contained</span>
            </div>
            <pre id="out" class="doctor-console"></pre>
        </section>
    </div>
@endsection

@push('scripts')
<script>
const API='/api';
const out=document.getElementById('out');
function write(data){out.textContent=typeof data==='string'?data:JSON.stringify(data,null,2);}
function useUserToken(){document.getElementById('doctorTokenInput').value=localStorage.getItem('USER_TOKEN')||'';syncSessionState();}
function setText(id,value){const node=document.getElementById(id);if(node)node.textContent=value;}
function syncSessionState(){const hasToken=!!document.getElementById('doctorTokenInput').value.trim();document.getElementById('doctorSessionAlert').classList.toggle('ll-hidden-debug',hasToken);setText('doctorSessionState',hasToken?'Ready':'Token required');}
async function call(path,method='GET',body=null){const token=document.getElementById('doctorTokenInput').value.trim();if(!token)return{status:401,data:{message:'Doctor token missing'}};const headers={'Accept':'application/json','Content-Type':'application/json','Authorization':`Bearer ${token}`};const res=await fetch(API+path,{method,headers,body:body?JSON.stringify(body):undefined});const text=await res.text();let data=text;try{data=JSON.parse(text)}catch{}return{status:res.status,data};}
function badge(status){if(['Completed','Approved','Assigned'].includes(status))return'<span class="ll-status-chip is-success">'+status+'</span>';if(['Cancelled','Rejected'].includes(status))return'<span class="ll-status-chip is-danger">'+status+'</span>';return'<span class="ll-status-chip is-warning">'+(status||'Pending')+'</span>';}
function escapeHtml(value){return String(value??'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;');}
async function doctorProfile(){const result=await call('/doctor/profile');write(result);syncSessionState();if(result.status<300){const doctor=result.data?.doctor||{};setText('doctorName',doctor.full_name||doctor.name||'Profile loaded');setText('doctorDepartment',doctor.department||'Not assigned');setText('doctorSpecialization',doctor.specialization||'General');setText('doctorProfileSummary','Department, role, and specialization are loaded from the existing doctor profile endpoint.');}}
async function doctorPatients(){const result=await call('/doctor/patients');write(result);syncSessionState();const rows=Array.isArray(result.data?.patients)?result.data.patients:[];setText('patientCount',String(rows.length));document.getElementById('patientCards').innerHTML=rows.length?rows.map((patient)=>`<article class="doctor-patient-card"><strong>${escapeHtml(patient.patient_name||patient.full_name||'Unknown patient')}</strong><p>${escapeHtml(patient.diagnosis||'No diagnosis supplied')}</p><div class="doctor-meta-row"><span class="doctor-meta-pill">Patient #${patient.patient_user_id||patient.id||'-'}</span><span class="doctor-meta-pill">${escapeHtml(patient.department||'Department not set')}</span><span class="doctor-meta-pill">${escapeHtml(patient.status||'Active')}</span></div></article>`).join(''):'<div class="ll-empty"><strong>No patients loaded</strong><p>Your doctor patient list is empty for the current session.</p></div>';}
async function doctorAppointments(){const status=document.getElementById('appointmentStatusFilter').value.trim();const qs=status?`?status=${encodeURIComponent(status)}`:'';const result=await call(`/doctor/appointments${qs}`,'GET');write(result);syncSessionState();const rows=Array.isArray(result.data?.appointments)?result.data.appointments:[];setText('appointmentCount',String(rows.length));setText('appointmentSummary',rows.length?`${rows.length} appointment${rows.length===1?'':'s'} loaded for the current filter.`:'No appointments were returned for the current filter.');document.getElementById('appointmentsBody').innerHTML=rows.length?rows.map((row)=>`<tr><td>${row.id}</td><td>${escapeHtml(row.patient_name||'-')}</td><td>${escapeHtml(row.department||'-')}</td><td>${row.appointment_datetime?new Date(row.appointment_datetime).toLocaleString():'-'}</td><td>${badge(row.status||'Booked')}</td></tr>`).join(''):'<tr><td colspan="5">No appointments found.</td></tr>';}
async function cancelAppointment(){const id=Number(document.getElementById('cancelAppointmentId').value);const reason=document.getElementById('cancelReason').value.trim();if(!id){write({status:422,data:{message:'appointment id required'}});return;}const body=reason?{cancelReason:reason}:{};write(await call(`/doctor/appointments/${id}/cancel`,'POST',body));await doctorAppointments();}
async function createBedRequest(){const body={patientUserId:Number(document.getElementById('patientUserId').value),diagnosis:document.getElementById('diagnosis').value.trim(),careLevelRequested:document.getElementById('careLevelRequested').value,notes:document.getElementById('requestNotes').value.trim()||null};write(await call('/doctor/bed-requests','POST',body));await doctorBedRequests();}
async function doctorBedRequests(){const result=await call('/doctor/bed-requests','GET');write(result);syncSessionState();const rows=Array.isArray(result.data?.bed_requests)?result.data.bed_requests:Array.isArray(result.data?.requests)?result.data.requests:[];setText('bedRequestCount',String(rows.length));setText('bedRequestSummary',rows.length?`${rows.length} request${rows.length===1?'':'s'} loaded from the doctor bed request endpoint.`:'No bed requests returned yet.');document.getElementById('bedRequestsBody').innerHTML=rows.length?rows.map((row)=>`<tr><td>${row.id}</td><td>${escapeHtml(row.patient_name||row.patient_user_id||'-')}</td><td>${escapeHtml(row.care_level_requested||row.careLevelRequested||'-')}</td><td>${escapeHtml(row.diagnosis||'-')}</td><td>${badge(row.status||'Pending')}</td></tr>`).join(''):'<tr><td colspan="5">No bed requests found.</td></tr>';}
useUserToken();syncSessionState();doctorProfile();doctorPatients();doctorAppointments();doctorBedRequests();
</script>
@endpush
