@extends('ui.layouts.app')

@section('title', 'Ward Management')
@section('workspace_label', 'Ward setup workspace')
@section('hero_badge', 'IT / Admin')
@section('hero_title', 'Create care units, add beds, and review ward structure from one clean management page.')
@section('hero_description', 'Ward setup keeps care unit creation, bed creation, and reference reads together in a safer operational interface without turning the screen into a raw API form.')
@section('meta_title', 'Ward Management')
@section('meta_copy', 'Care units, beds, and setup history')

@push('styles')
<style>
    .ward-grid,.ward-card-grid{display:grid;gap:18px}
    .ward-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .ward-console{margin-top:16px;min-height:140px;max-height:320px;overflow:auto;border-radius:18px;border:1px solid rgba(140,170,201,.18);background:#0f1c33;color:#d7e3ff;padding:16px;font-size:12px}
    @media (max-width:980px){.ward-card-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('sidebar_nav')
    <a class="is-active" href="#ward-overview"><strong>Overview</strong><span>Setup</span></a>
    <a href="#ward-units"><strong>Care Units</strong><span>Create</span></a>
    <a href="#ward-beds"><strong>Beds</strong><span>Create</span></a>
    <a href="#ward-reference"><strong>Reference</strong><span>Read</span></a>
@endsection

@section('sidebar')
    <div class="app-shell__sidebar-card">
        <strong>Preserved behavior</strong>
        <p>This page still uses the same ward creation and read endpoints. The refactor only improves layout, hierarchy, and readability.</p>
    </div>
@endsection

@section('section_nav')
    <a href="#ward-overview" class="is-active">Overview</a>
    <a href="#ward-units">Care Units</a>
    <a href="#ward-beds">Beds</a>
    <a href="#ward-reference">Reference</a>
@endsection

@section('content')
    <div class="ward-grid">
        <div id="wardSessionAlert" class="ll-inline-alert is-warning ll-hidden-debug">
            <strong>Privileged session recommended</strong>
            <p>Ward creation actions require an Admin or IT session. Use the stored token helpers before saving changes.</p>
        </div>

        <section id="ward-overview" class="ll-section">
            <div class="ll-kpi-grid">
                <article class="ll-stat-card is-primary"><small>Stored admin token</small><strong id="adminTokenState">No</strong><span>Session helpers still read the same browser storage keys.</span></article>
                <article class="ll-stat-card is-success"><small>Latest care unit</small><strong id="lastCareUnitId">-</strong><span>Newly created care units are remembered locally for faster bed setup.</span></article>
                <article class="ll-stat-card is-warning"><small>Latest bed</small><strong id="lastBedId">-</strong><span>Recent bed identifiers stay visible after the create call completes.</span></article>
                <article class="ll-stat-card is-neutral"><small>Reference reads</small><strong id="readState">Ready</strong><span>Departments, care units, beds, and summary reads remain available below.</span></article>
            </div>
        </section>

        <div class="ward-card-grid">
            <article id="ward-units" class="ll-panel ll-section">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Create care unit</h2>
                        <p>Capture department, unit type, optional name, and floor without leaving the ward management surface.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Care units</span>
                </div>

                <div class="ll-form-grid" style="margin-top: 18px;">
                    <div class="ll-field">
                        <label class="ll-label" for="tokenInput">Session token</label>
                        <input id="tokenInput" class="ll-input" placeholder="Use ADMIN_TOKEN or USER_TOKEN">
                    </div>

                    <div class="ll-inline-actions">
                        <button class="ll-button-ghost" type="button" onclick="useStoredAdminToken()">Use ADMIN_TOKEN</button>
                        <button class="ll-button-ghost" type="button" onclick="useStoredUserToken()">Use USER_TOKEN</button>
                    </div>

                    <div class="ll-form-grid-2">
                        <div class="ll-field">
                            <label class="ll-label" for="departmentId">Department ID</label>
                            <input id="departmentId" class="ll-input" type="number" placeholder="Department ID">
                        </div>
                        <div class="ll-field">
                            <label class="ll-label" for="unitType">Unit type</label>
                            <select id="unitType" class="ll-select">
                                <option value="Ward">Ward</option>
                                <option value="ICU">ICU</option>
                                <option value="NICU">NICU</option>
                                <option value="CCU">CCU</option>
                            </select>
                        </div>
                    </div>

                    <div class="ll-form-grid-2">
                        <div class="ll-field">
                            <label class="ll-label" for="unitName">Unit name</label>
                            <input id="unitName" class="ll-input" placeholder="Optional unit name">
                        </div>
                        <div class="ll-field">
                            <label class="ll-label" for="floor">Floor</label>
                            <input id="floor" class="ll-input" type="number" placeholder="Optional floor">
                        </div>
                    </div>

                    <div class="ll-inline-actions">
                        <button class="ll-button" type="button" onclick="createCareUnit()">Create care unit</button>
                        <button class="ll-button-ghost" type="button" onclick="listCareUnits()">List care units</button>
                    </div>
                </div>
            </article>

            <article id="ward-beds" class="ll-panel ll-section">
                <div class="ll-panel-heading">
                    <div>
                        <h2>Create bed</h2>
                        <p>Add a new bed after a care unit is available, while keeping the latest setup identifiers visible.</p>
                    </div>
                    <span class="ll-status-chip is-soft">Beds</span>
                </div>

                <div class="ll-form-grid" style="margin-top: 18px;">
                    <div class="ll-form-grid-2">
                        <div class="ll-field">
                            <label class="ll-label" for="careUnitId">Care unit ID</label>
                            <input id="careUnitId" class="ll-input" type="number" placeholder="Care unit ID">
                        </div>
                        <div class="ll-field">
                            <label class="ll-label" for="bedCode">Bed code</label>
                            <input id="bedCode" class="ll-input" placeholder="Example: ICU-01">
                        </div>
                    </div>

                    <div class="ll-field">
                        <label class="ll-label" for="bedStatus">Bed status</label>
                        <select id="bedStatus" class="ll-select">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Reserved">Reserved</option>
                        </select>
                    </div>

                    <div class="ll-inline-actions">
                        <button class="ll-button" type="button" onclick="createBed()">Create bed</button>
                        <button class="ll-button-ghost" type="button" onclick="listBeds()">List beds</button>
                    </div>
                </div>
            </article>
        </div>

        <section id="ward-reference" class="ll-panel ll-section">
            <div class="ll-panel-heading">
                <div>
                    <h2>Reference reads</h2>
                    <p>Keep read actions close by for quick verification of departments, care units, beds, and bed summary.</p>
                </div>
                <span class="ll-status-chip is-soft">Read APIs</span>
            </div>

            <div class="ll-inline-actions" style="margin-top: 18px;">
                <button class="ll-button-ghost" type="button" onclick="listDepartments()">Departments</button>
                <button class="ll-button-ghost" type="button" onclick="listCareUnits()">Care units</button>
                <button class="ll-button-ghost" type="button" onclick="listBeds()">Beds</button>
                <button class="ll-button" type="button" onclick="loadSummary()">Bed summary</button>
            </div>
        </section>

        <section class="ll-panel">
            <div class="ll-panel-heading">
                <div>
                    <h2>API response</h2>
                    <p>The raw response is still preserved here for troubleshooting and verification.</p>
                </div>
            </div>
            <pre id="out" class="ward-console"></pre>
        </section>
    </div>
@endsection

@push('scripts')
<script>
const API='/api';
const out=document.getElementById('out');
function write(data){out.textContent=typeof data==='string'?data:JSON.stringify(data,null,2);}
function refreshCtx(){const adminToken=!!localStorage.getItem('ADMIN_TOKEN');document.getElementById('wardSessionAlert').classList.toggle('ll-hidden-debug',adminToken);document.getElementById('adminTokenState').textContent=adminToken?'Yes':'No';document.getElementById('lastCareUnitId').textContent=localStorage.getItem('LAST_CARE_UNIT_ID')||'-';document.getElementById('lastBedId').textContent=localStorage.getItem('LAST_BED_ID')||'-';}
function useStoredAdminToken(){document.getElementById('tokenInput').value=localStorage.getItem('ADMIN_TOKEN')||'';refreshCtx();}
function useStoredUserToken(){document.getElementById('tokenInput').value=localStorage.getItem('USER_TOKEN')||'';refreshCtx();}
async function call(path,method='GET',body=null){const token=document.getElementById('tokenInput').value.trim();const headers={'Accept':'application/json','Content-Type':'application/json'};if(token)headers.Authorization=`Bearer ${token}`;const res=await fetch(API+path,{method,headers,body:body?JSON.stringify(body):undefined});const text=await res.text();let data=text;try{data=JSON.parse(text)}catch{}return{status:res.status,data};}
async function listDepartments(){write(await call('/ward/departments'));}
async function listCareUnits(){write(await call('/ward/care-units'));}
async function listBeds(){write(await call('/ward/beds'));}
async function loadSummary(){write(await call('/ward/beds/summary'));}
async function createCareUnit(){const payload={departmentId:Number(document.getElementById('departmentId').value),unitType:document.getElementById('unitType').value,unitName:document.getElementById('unitName').value.trim()||null,floor:document.getElementById('floor').value?Number(document.getElementById('floor').value):null};const result=await call('/ward/care-units','POST',payload);const id=result.data?.care_unit?.id;if(id){localStorage.setItem('LAST_CARE_UNIT_ID',String(id));document.getElementById('careUnitId').value=String(id);}refreshCtx();write(result);}
async function createBed(){const payload={careUnitId:Number(document.getElementById('careUnitId').value),bedCode:document.getElementById('bedCode').value.trim(),status:document.getElementById('bedStatus').value};const result=await call('/ward/beds','POST',payload);const id=result.data?.bed?.id;if(id)localStorage.setItem('LAST_BED_ID',String(id));refreshCtx();write(result);}
useStoredAdminToken();refreshCtx();
</script>
@endpush
