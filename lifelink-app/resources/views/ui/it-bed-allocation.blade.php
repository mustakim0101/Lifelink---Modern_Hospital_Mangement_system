@extends('ui.layouts.app')

@section('title', 'IT Worker Dashboard')
@section('workspace_label', '')
@section('hero_badge', '')
@section('hero_title', 'IT Dashboard')
@section('hero_description', '')
@section('hide_meta_card', '1')
@section('meta_title', 'IT Workflow')
@section('meta_copy', 'Department assignment, ward setup, admissions, and beds')

@section('sidebar_nav')
    <a class="is-active" href="#it-overview" data-panel="it-overview" data-mode="all">
        <strong>Overview</strong>
    </a>
    <a href="#it-directory" data-panel="it-directory" data-mode="regular">
        <strong>Doctor + Patient Lookup</strong>
    </a>
    <a href="#it-appointments" data-panel="it-appointments" data-mode="regular">
        <strong>Appointment Queue</strong>
    </a>
    <a href="#it-admission" data-panel="it-admission" data-mode="regular">
        <strong>Admission + Bed Flow</strong>
    </a>
    <a href="#it-reference" data-panel="it-reference" data-mode="regular">
        <strong>Reference Tables</strong>
    </a>
    <a href="#it-blood-bank" data-panel="it-blood-bank" data-mode="blood">
        <strong>Blood Bank Operations</strong>
    </a>
    <a href="#it-bb-request-board" data-panel="it-blood-bank" data-anchor="request-board" data-hash="it-bb-request-board" data-mode="blood">
        <strong>Request Board</strong>
    </a>
    <a href="#it-bb-approval-fulfillment" data-panel="it-blood-bank" data-anchor="approval-fulfillment" data-hash="it-bb-approval-fulfillment" data-mode="blood">
        <strong>Approval + Fulfillment</strong>
    </a>
    <a href="#it-bb-match-timeline" data-panel="it-blood-bank" data-anchor="match-timeline" data-hash="it-bb-match-timeline" data-mode="blood">
        <strong>Match Timeline</strong>
    </a>
    <a href="#it-bb-donor-suggestions" data-panel="it-blood-bank" data-anchor="donor-suggestions" data-hash="it-bb-donor-suggestions" data-mode="blood">
        <strong>Donor Suggestions</strong>
    </a>
    <a href="#it-bb-donor-search" data-panel="it-blood-bank" data-anchor="donor-search" data-hash="it-bb-donor-search" data-mode="blood">
        <strong>Donor Search</strong>
    </a>
    <a href="#it-bb-donation-logging" data-panel="it-blood-bank" data-anchor="donation-logging" data-hash="it-bb-donation-logging" data-mode="blood">
        <strong>Donation Logging</strong>
    </a>
    <a href="#it-debug" data-panel="it-debug" data-mode="all">
        <strong>Activity Log</strong>
    </a>
@endsection

@section('sidebar')
@endsection

@section('content')
    <div class="it-grid">
        <input id="tokenInput" type="hidden">
        <div id="it-overview" class="it-split ll-section it-panel-switch" data-display="grid">
            <div class="it-panel it-col-12">
                <h3>Current totals</h3>
                <p id="scopeModeSummary" class="it-note">Load your departments to open the correct IT workspace.</p>
                <div class="it-summary">
                    <div class="it-stat"><small>Scoped depts</small><strong id="scopeCount">0</strong></div>
                    <div class="it-stat"><small>Care units</small><strong id="careUnitCount">0</strong></div>
                    <div class="it-stat"><small>Beds shown</small><strong id="bedCount">0</strong></div>
                    <div class="it-stat"><small>Admissions shown</small><strong id="admissionCount">0</strong></div>
                </div>
            </div>
        </div>

        <div id="it-blood-bank" class="it-panel it-panel-switch ll-section" data-display="block">
            <h3>Blood Bank operations</h3>
            <p class="it-note">Blood Bank IT mode keeps the real request, donor, approval, fulfillment, and donation tools inline in this workspace.</p>
            <div class="it-summary">
                <div class="it-stat"><small>Blood Bank access</small><strong id="bloodBankScopeStatus">Locked</strong></div>
                <div class="it-stat"><small>Blood Bank departments</small><strong id="bloodBankScopeCount">0</strong></div>
            </div>
            <div class="bbops-grid u-mt-4">
                <div class="bbops-summary">
                    <div class="it-stat"><small>Requests shown</small><strong id="bbRequestCount">0</strong></div>
                    <div class="it-stat"><small>Accepted matches</small><strong id="bbAcceptedCount">0</strong></div>
                    <div class="it-stat"><small>Donors shown</small><strong id="bbDonorCount">0</strong></div>
                    <div class="it-stat"><small>Selected request</small><strong id="bbSelectedRequestLabel">None</strong></div>
                </div>

                <section id="request-board" class="bbops-section">
                    <div class="bbops-card">
                        <h3>Request Board</h3>
                        <p class="it-note">Select a blood request here. The approval, donor search, and donation tools below will align to the selected request.</p>
                        <div class="bbops-filter-grid">
                            <div>
                                <label class="it-label" for="bbStatusFilter">Request status</label>
                                <select id="bbStatusFilter" class="it-select">
                                    <option value="">All</option>
                                    <option>Pending</option>
                                    <option>Matched</option>
                                    <option>Approved</option>
                                    <option>Fulfilled</option>
                                    <option>Rejected</option>
                                    <option>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="it-label" for="bbBloodGroupFilter">Blood group</label>
                                <select id="bbBloodGroupFilter" class="it-select">
                                    <option value="">All</option>
                                    <option>A+</option>
                                    <option>A-</option>
                                    <option>B+</option>
                                    <option>B-</option>
                                    <option>AB+</option>
                                    <option>AB-</option>
                                    <option>O+</option>
                                    <option>O-</option>
                                </select>
                            </div>
                            <div>
                                <label class="it-label" for="bbDepartmentFilter">Patient department</label>
                                <select id="bbDepartmentFilter" class="it-select">
                                    <option value="">All departments</option>
                                </select>
                            </div>
                            <div>
                                <label class="it-label" for="bbFulfillmentBankId">Fulfillment bank</label>
                                <select id="bbFulfillmentBankId" class="it-select">
                                    <option value="">Keep request bank / none</option>
                                </select>
                            </div>
                            <div>
                                <label class="it-label" for="bbRequestLimit">Request limit</label>
                                <input id="bbRequestLimit" class="it-input" type="number" min="1" max="150" value="40">
                            </div>
                        </div>
                        <div class="bbops-actions">
                            <button class="it-button primary" type="button" onclick="loadBloodBankRequests()">Refresh request board</button>
                            <button class="it-button soft" type="button" onclick="refreshBloodBankWorkspace()">Refresh full Blood Bank workspace</button>
                        </div>
                        <div class="it-table-wrap u-mt-3">
                            <table class="it-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Need</th>
                                        <th>Units</th>
                                        <th>Status</th>
                                        <th>Accepted</th>
                                    </tr>
                                </thead>
                                <tbody id="bbRequestsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section id="approval-fulfillment" class="bbops-section">
                    <div class="bbops-toolbar">
                        <div class="bbops-card">
                            <h3>Approval + Fulfillment</h3>
                            <p id="bbSelectedRequestHint" class="it-note">Pick a request from the Request Board to unlock request-specific actions.</p>
                            <div id="bbSelectedRequestMeta" class="bbops-meta">No request selected.</div>
                            <div class="bbops-action-grid">
                                <div>
                                    <label class="it-label" for="bbSelectedMatchId">Accepted match ID</label>
                                    <input id="bbSelectedMatchId" class="it-input" type="number" placeholder="Choose from match timeline">
                                </div>
                                <div>
                                    <label class="it-label" for="bbLinkedRequestId">Linked request ID</label>
                                    <input id="bbLinkedRequestId" class="it-input" type="number" placeholder="Auto-filled from selected request">
                                </div>
                            </div>
                            <label class="it-label" for="bbNotifyMessage">Notification message</label>
                            <textarea id="bbNotifyMessage" class="it-textarea" placeholder="Please come to the blood bank within the next 3 days."></textarea>
                            <label class="it-label" for="bbWorkflowNote">Workflow note</label>
                            <textarea id="bbWorkflowNote" class="it-textarea" placeholder="Optional approval or fulfillment note"></textarea>
                            <label class="it-label" for="bbConsumeInventory">Fulfillment mode</label>
                            <div class="bbops-meta">
                                <label><input id="bbConsumeInventory" type="checkbox"> Deduct inventory during fulfillment when stored blood is used.</label>
                            </div>
                            <div class="bbops-actions">
                                <button class="it-button primary" type="button" onclick="notifyBloodBankDonors()">Auto notify donors</button>
                                <button class="it-button soft" type="button" onclick="approveBloodBankMatch()">Approve accepted donor</button>
                                <button class="it-button accent" type="button" onclick="fulfillBloodBankRequest()">Fulfill request</button>
                            </div>
                        </div>

                        <div id="match-timeline" class="bbops-card">
                            <h3>Match timeline</h3>
                            <p class="it-note">Accepted donors from this table can feed both approval and donation logging.</p>
                            <div class="it-table-wrap">
                                <table class="it-table">
                                    <thead>
                                        <tr>
                                            <th>Match ID</th>
                                            <th>Donor</th>
                                            <th>Group</th>
                                            <th>Status</th>
                                            <th>Notified</th>
                                            <th>Responded</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bbMatchesBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="donor-suggestions" class="bbops-section">
                    <div class="bbops-card">
                        <h3>Compatible donor suggestions</h3>
                        <p class="it-note">Tick suggested donors before using Auto notify. This stays request-aware and uses the current selected request.</p>
                        <div id="bbSuggestionsGrid" class="bbops-card-grid"></div>
                    </div>
                </section>

                <section id="donor-search" class="bbops-section">
                    <div class="bbops-toolbar">
                        <div class="bbops-card">
                            <h3>Donor Search</h3>
                            <p class="it-note">Search across Blood Bank donors for request-linked matching or casual walk-in donation handling.</p>
                            <div class="bbops-filter-grid">
                                <div>
                                    <label class="it-label" for="bbDonorSearchQuery">Donor search</label>
                                    <input id="bbDonorSearchQuery" class="it-input" placeholder="Donor name, email, or donor id">
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonorSearchRequestId">Request filter</label>
                                    <input id="bbDonorSearchRequestId" class="it-input" type="number" min="1" placeholder="Optional request id">
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonorSearchBloodGroup">Blood group</label>
                                    <select id="bbDonorSearchBloodGroup" class="it-select">
                                        <option value="">All</option>
                                        <option>A+</option>
                                        <option>A-</option>
                                        <option>B+</option>
                                        <option>B-</option>
                                        <option>AB+</option>
                                        <option>AB-</option>
                                        <option>O+</option>
                                        <option>O-</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonorSearchEligible">Eligibility</label>
                                    <select id="bbDonorSearchEligible" class="it-select">
                                        <option value="">All</option>
                                        <option value="true">Eligible</option>
                                        <option value="false">Not eligible</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bbops-actions">
                                <button class="it-button primary" type="button" onclick="loadBloodBankStaffDonors()">Refresh donor search</button>
                            </div>
                            <div id="bbStaffDonorGrid" class="bbops-card-grid"></div>
                            <div id="bbStaffDonorPagination" class="ui-list-pagination"></div>
                        </div>

                        <div id="donation-logging" class="bbops-card">
                            <h3>Donation Logging</h3>
                            <p class="it-note">Record the actual donation after nurse screening. This supports both request-linked donations and non-request walk-ins.</p>
                            <div class="bbops-action-grid">
                                <div>
                                    <label class="it-label" for="bbDonationDonorId">Donor ID</label>
                                    <input id="bbDonationDonorId" class="it-input" type="number" placeholder="Auto-filled from donor selection">
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonationHealthCheckId">Health check ID</label>
                                    <select id="bbDonationHealthCheckId" class="it-select">
                                        <option value="">Select latest nurse screening</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonationBankId">Blood bank ID</label>
                                    <select id="bbDonationBankId" class="it-select">
                                        <option value="">Choose blood bank</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonationDateTime">Donation datetime</label>
                                    <input id="bbDonationDateTime" class="it-input" type="datetime-local">
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonationBloodGroup">Blood group</label>
                                    <select id="bbDonationBloodGroup" class="it-select">
                                        <option>A+</option>
                                        <option>A-</option>
                                        <option>B+</option>
                                        <option>B-</option>
                                        <option>AB+</option>
                                        <option>AB-</option>
                                        <option>O+</option>
                                        <option>O-</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="it-label" for="bbComponentType">Component type</label>
                                    <select id="bbComponentType" class="it-select">
                                        <option selected>WholeBlood</option>
                                        <option>Plasma</option>
                                        <option>Platelets</option>
                                        <option>RBC</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="it-label" for="bbUnitsDonated">Units donated</label>
                                    <input id="bbUnitsDonated" class="it-input" type="number" min="1" max="5" value="1">
                                </div>
                                <div>
                                    <label class="it-label" for="bbDonationNotes">Donation note</label>
                                    <input id="bbDonationNotes" class="it-input" placeholder="Optional staff note">
                                </div>
                            </div>
                            <div class="bbops-actions">
                                <button class="it-button accent" type="button" onclick="logBloodBankDonation()">Record donation</button>
                                <button class="it-button soft" type="button" onclick="loadBloodBankDonationHealthChecks()">Refresh health checks</button>
                                <a class="it-button soft" href="/ui/blood-bank-schema">Open Blood Bank schema</a>
                            </div>
                            <div class="it-table-wrap">
                                <table class="it-table">
                                    <thead>
                                        <tr>
                                            <th>Health Check ID</th>
                                            <th>Time</th>
                                            <th>Weight</th>
                                            <th>Temp</th>
                                            <th>Hb</th>
                                            <th>Checked by</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bbDonationHealthChecksBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        <div id="regularItLocked" class="it-panel ll-section it-panel-switch" data-display="block">
            <h3>Regular IT operations</h3>
            <p id="regularItLockedMessage" class="it-note">Load your departments to open the correct IT workspace.</p>
        </div>

        <div id="standardItWorkArea">
        <div id="it-directory" class="it-split ll-section it-panel-switch" data-display="grid">
            <div class="it-panel it-col-6">
                <h3>Doctor lookup</h3>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="doctorSearchDepartmentId">Doctor department</label>
                        <select id="doctorSearchDepartmentId" class="it-select">
                            <option value="">All accessible departments</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="doctorSearchQuery">Doctor search</label>
                        <input id="doctorSearchQuery" class="it-input" placeholder="Doctor name or email">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="loadDoctors()">Load doctors</button>
                </div>
                <div id="doctorCards" class="it-card-grid ui-list-window"></div>
                <div id="doctorPagination" class="ui-list-pagination"></div>
            </div>

            <div class="it-panel it-col-6">
                <h3>Patient directory</h3>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="patientSearchDepartmentId">Current department filter</label>
                        <select id="patientSearchDepartmentId" class="it-select">
                            <option value="">All patients</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="patientSearchQuery">Patient search</label>
                        <input id="patientSearchQuery" class="it-input" placeholder="Patient name or email">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="loadPatients()">Load patients</button>
                </div>
                <div id="patientCards" class="it-card-grid ui-list-window"></div>
                <div id="patientPagination" class="ui-list-pagination"></div>
            </div>
        </div>

        <div id="it-appointments" class="it-panel ll-section it-panel-switch" data-display="block">
            <h3>Appointment approval management</h3>
            <p class="it-note">Review patient requests submitted as PendingApproval. Filter by doctor, department, date, and status, then approve, reject, or cancel as needed.</p>
            <div class="it-controls">
                <div>
                    <label class="it-label" for="itAppointmentDepartmentId">Department</label>
                    <select id="itAppointmentDepartmentId" class="it-select">
                        <option value="">All accessible departments</option>
                    </select>
                </div>
                <div>
                    <label class="it-label" for="itAppointmentDoctorUserId">Doctor ID</label>
                    <input id="itAppointmentDoctorUserId" class="it-input" type="number" placeholder="Optional doctor user ID">
                </div>
                <div>
                    <label class="it-label" for="itAppointmentDate">Appointment date</label>
                    <input id="itAppointmentDate" class="it-input" type="date">
                </div>
                <div>
                    <label class="it-label" for="itAppointmentStatus">Status</label>
                    <select id="itAppointmentStatus" class="it-select">
                        <option value="">All statuses</option>
                        <option value="PendingApproval">PendingApproval</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Booked">Booked</option>
                        <option value="Completed">Completed</option>
                        <option value="NoShow">NoShow</option>
                    </select>
                </div>
            </div>
            <div class="it-actions">
                <button class="it-button primary" type="button" onclick="loadAppointmentQueue()">Refresh appointment queue</button>
            </div>
            <div class="it-summary">
                <div class="it-stat"><small>Queue rows</small><strong id="itAppointmentQueueCount">0</strong></div>
                <div class="it-stat"><small>Pending</small><strong id="itAppointmentPendingCount">0</strong></div>
                <div class="it-stat"><small>Approved</small><strong id="itAppointmentApprovedCount">0</strong></div>
            </div>
            <div class="it-table-wrap">
                <table class="it-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Department</th>
                            <th>Doctor</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Capacity</th>
                            <th>Used</th>
                            <th>Remaining</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="itAppointmentQueueBody"></tbody>
                </table>
            </div>
            <div id="itAppointmentQueuePagination" class="ui-list-pagination"></div>
        </div>

        <div id="it-admission" class="it-split ll-section it-panel-switch" data-display="grid">
            <div class="it-panel it-col-6">
                <h3>Ward setup</h3>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="wardDepartmentId">Department</label>
                        <select id="wardDepartmentId" class="it-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="unitType">Unit type</label>
                        <select id="unitType" class="it-select">
                            <option value="Ward">Ward</option>
                            <option value="ICU">ICU</option>
                            <option value="NICU">NICU</option>
                            <option value="CCU">CCU</option>
                        </select>
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="unitName">Unit name</label>
                        <input id="unitName" class="it-input" placeholder="Optional unit name">
                    </div>
                    <div>
                        <label class="it-label" for="floor">Floor</label>
                        <input id="floor" class="it-input" type="number" placeholder="Optional floor">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button primary" type="button" onclick="createCareUnit()">Create care unit</button>
                    <button class="it-button soft" type="button" onclick="listCareUnits()">List care units</button>
                </div>

                <div class="it-controls u-mt-4">
                    <div>
                        <label class="it-label" for="careUnitId">Care unit ID</label>
                        <input id="careUnitId" class="it-input" type="number" placeholder="Care unit ID">
                    </div>
                    <div>
                        <label class="it-label" for="bedCode">Bed code</label>
                        <input id="bedCode" class="it-input" placeholder="e.g. ICU-01">
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="bedStatus">Bed status</label>
                        <select id="bedStatus" class="it-select">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Reserved">Reserved</option>
                        </select>
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button accent" type="button" onclick="createBed()">Create bed</button>
                    <button class="it-button soft" type="button" onclick="listBeds()">List beds</button>
                </div>
            </div>

            <div class="it-panel it-col-6">
                <h3>Admission intake and allocation</h3>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="patientUserId">Patient user ID</label>
                        <input id="patientUserId" class="it-input" type="number" placeholder="Patient user ID">
                    </div>
                    <div>
                        <label class="it-label" for="admissionDepartmentId">Admission department</label>
                        <select id="admissionDepartmentId" class="it-select">
                            <option value="">Select department</option>
                        </select>
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="admittedByDoctorId">Admitted by doctor ID</label>
                        <input id="admittedByDoctorId" class="it-input" type="number" placeholder="Optional doctor ID from search">
                    </div>
                </div>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="careLevel">Care level requested</label>
                        <select id="careLevel" class="it-select">
                            <option value="Ward">Ward</option>
                            <option value="ICU">ICU</option>
                            <option value="NICU">NICU</option>
                            <option value="CCU">CCU</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="diagnosis">Diagnosis</label>
                        <input id="diagnosis" class="it-input" placeholder="Diagnosis">
                    </div>
                </div>
                <label class="it-label" for="admissionNotes">Admission notes</label>
                <textarea id="admissionNotes" class="it-textarea" placeholder="Optional notes"></textarea>
                <div class="it-actions">
                    <button class="it-button warm" type="button" onclick="createAdmission()">Create admission</button>
                </div>

                <div class="it-controls u-mt-4">
                    <div>
                        <label class="it-label" for="filterDepartmentId">Filter department</label>
                        <select id="filterDepartmentId" class="it-select">
                            <option value="">All accessible departments</option>
                        </select>
                    </div>
                    <div>
                        <label class="it-label" for="filterStatus">Admission status</label>
                        <select id="filterStatus" class="it-select">
                            <option value="">All statuses</option>
                            <option value="Admitted">Admitted</option>
                            <option value="Discharged">Discharged</option>
                            <option value="Transferred">Transferred</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button soft" type="button" onclick="listAdmissions()">Load admissions</button>
                    <button class="it-button soft" type="button" onclick="availableBeds()">Load available beds</button>
                    <button class="it-button soft" type="button" onclick="listDepartments()">Load departments</button>
                </div>
            </div>
            <div class="it-panel it-col-6">
                <h3>Bed assignment actions</h3>
                <div class="it-controls">
                    <div>
                        <label class="it-label" for="assignAdmissionId">Admission ID</label>
                        <input id="assignAdmissionId" class="it-input" type="number" placeholder="Admission ID">
                    </div>
                    <div>
                        <label class="it-label" for="assignBedId">Bed ID</label>
                        <input id="assignBedId" class="it-input" type="number" placeholder="Bed ID">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button accent" type="button" onclick="assignBed()">Assign bed</button>
                </div>

                <div class="it-controls u-mt-4">
                    <div>
                        <label class="it-label" for="dischargeAdmissionId">Discharge admission ID</label>
                        <input id="dischargeAdmissionId" class="it-input" type="number" placeholder="Admission to discharge">
                    </div>
                    <div>
                        <label class="it-label" for="releaseReason">Release reason</label>
                        <input id="releaseReason" class="it-input" placeholder="Default: Discharge">
                    </div>
                </div>
                <div class="it-actions">
                    <button class="it-button danger" type="button" onclick="dischargeAdmission()">Discharge and release bed</button>
                </div>
            </div>
            <div class="it-panel it-col-6">
                <h3>Available beds</h3>
                <div id="bedCards" class="it-card-grid"></div>
            </div>
        </div>

        <div id="it-reference" class="it-panel ll-section it-panel-switch" data-display="block">
            <h3>Reference Tables</h3>
            <div class="it-table-grid">
                <div>
                    <div class="it-table-wrap">
                        <table class="it-table">
                            <thead>
                                <tr><th>ID</th><th>Department</th><th>Type</th><th>Name</th><th>Floor</th></tr>
                            </thead>
                            <tbody id="careUnitsBody"></tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="it-table-wrap">
                        <table class="it-table">
                            <thead>
                                <tr><th>ID</th><th>Code</th><th>Status</th><th>Unit</th><th>Department</th></tr>
                            </thead>
                            <tbody id="bedsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div id="it-debug" class="it-panel ll-section it-panel-switch" data-display="block">
            <details class="ll-debug">
                <summary>Operational activity log</summary>
                <pre id="out" class="it-console"></pre>
            </details>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const API = '/api';
const out = document.getElementById('out');
const itPanelIds = ['it-overview', 'it-directory', 'it-appointments', 'it-admission', 'it-reference', 'it-blood-bank', 'it-debug'];
const itBloodBankSectionHashMap = {
    'it-bb-request-board': 'request-board',
    'it-bb-approval-fulfillment': 'approval-fulfillment',
    'it-bb-match-timeline': 'match-timeline',
    'it-bb-donor-suggestions': 'donor-suggestions',
    'it-bb-donor-search': 'donor-search',
    'it-bb-donation-logging': 'donation-logging',
    'request-board': 'request-board',
    'approval-fulfillment': 'approval-fulfillment',
    'match-timeline': 'match-timeline',
    'donor-suggestions': 'donor-suggestions',
    'donor-search': 'donor-search',
    'donation-logging': 'donation-logging',
};
const itBloodBankSectionHashById = {
    'request-board': 'it-bb-request-board',
    'approval-fulfillment': 'it-bb-approval-fulfillment',
    'match-timeline': 'it-bb-match-timeline',
    'donor-suggestions': 'it-bb-donor-suggestions',
    'donor-search': 'it-bb-donor-search',
    'donation-logging': 'it-bb-donation-logging',
};
const itNavLinks = Array.from(document.querySelectorAll('.app-shell__nav a[data-panel]'));

const state = {
    activePanel: 'it-overview',
    activeBloodBankSection: '',
    scopeLoaded: false,
    departments: [],
    scopeDepartments: [],
    doctors: [],
    patients: [],
    pagination: {
        directoryPageSize: 4,
        doctorsPage: 1,
        patientsPage: 1,
        appointmentQueuePageSize: 10,
        appointmentQueuePage: 1,
        bloodDonorsPageSize: 6,
        bloodDonorsPage: 1,
    },
    admissions: [],
    beds: [],
    careUnits: [],
    appointmentQueue: [],
    appointmentCapacity: {},
    bloodBank: {
        requests: [],
        matches: [],
        suggestions: [],
        staffDonors: [],
        selectedRequestId: null,
        selectedDonorIds: new Set(),
        workspaceLoaded: false,
        banksLoaded: false,
    },
};

const BLOOD_BANK_DEPARTMENT = 'Blood Bank';

function normalizeDepartmentName(value) {
    if (!value) return '';
    if (typeof value === 'string') return value.trim().toLowerCase();
    if (typeof value === 'object') {
        return String(
            value.dept_name
            || value.department
            || value.department_name
            || value.name
            || ''
        ).trim().toLowerCase();
    }
    return String(value).trim().toLowerCase();
}

function isBloodBankDepartment(department) {
    return normalizeDepartmentName(department?.dept_name || department) === BLOOD_BANK_DEPARTMENT.toLowerCase();
}

function allowedPanels() {
    if (!state.scopeLoaded) {
        return ['it-overview', 'it-debug'];
    }

    const blood = hasBloodBankScope();
    const regular = hasNonBloodBankScope();

    if (blood && !regular) {
        return ['it-overview', 'it-blood-bank', 'it-debug'];
    }

    if (blood && regular) {
        return ['it-overview', 'it-directory', 'it-appointments', 'it-admission', 'it-reference', 'it-blood-bank', 'it-debug'];
    }

    return ['it-overview', 'it-directory', 'it-appointments', 'it-admission', 'it-reference', 'it-debug'];
}

function updateSidebarByScope() {
    const allowed = allowedPanels();

    itNavLinks.forEach((link) => {
        const mode = link.dataset.mode || 'all';
        const panel = link.dataset.panel || '';
        const visible = mode === 'all'
            || (mode === 'regular' && allowed.includes(panel))
            || (mode === 'blood' && allowed.includes(panel));
        link.style.display = visible ? '' : 'none';
    });
}

function setVisibility(elementId, visible, displayValue = 'block') {
    const element = document.getElementById(elementId);
    if (!element) return;
    element.hidden = !visible;
    element.style.display = visible ? displayValue : 'none';
}

function setActivePanel(panelId, sectionId = '') {
    const allowed = allowedPanels();
    if (!allowed.includes(panelId)) {
        panelId = allowed[0];
    }

    state.activePanel = panelId;
    state.activeBloodBankSection = panelId === 'it-blood-bank' ? sectionId : '';

    itPanelIds.forEach((id) => {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.style.display = id === panelId ? (panel.dataset.display || 'block') : 'none';
    });

    itNavLinks.forEach((link) => {
        const targetId = link.dataset.panel || '';
        const anchorId = link.dataset.anchor || '';
        const isPanelActive = targetId === panelId;
        const isAnchorActive = isPanelActive && !!anchorId && anchorId === state.activeBloodBankSection;
        const isGenericPanelActive = isPanelActive && !anchorId;
        link.classList.toggle('is-active', isAnchorActive || isGenericPanelActive);
    });

    renderDepartmentMode();

    if (panelId === 'it-blood-bank' && allowed.includes('it-blood-bank')) {
        maybeLoadBloodBankWorkspace();
    }
}

function setupSidebarPanelNav() {
    itNavLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const panelId = link.dataset.panel || '';
            const sectionId = link.dataset.anchor || '';
            const hashId = link.dataset.hash || sectionId || panelId;
            if (!itPanelIds.includes(panelId)) return;
            setActivePanel(panelId, sectionId);
            history.replaceState(null, '', `#${hashId}`);
        });
    });

    updateSidebarByScope();
    const initialHash = (window.location.hash || '').replace('#', '');
    const resolvedBloodSection = itBloodBankSectionHashMap[initialHash] || '';
    const initialIsBloodBankSection = !!resolvedBloodSection;
    const initialPanel = initialIsBloodBankSection
        ? 'it-blood-bank'
        : (itPanelIds.includes(initialHash) ? initialHash : allowedPanels()[0]);
    setActivePanel(initialPanel, resolvedBloodSection);
    if (initialIsBloodBankSection) {
        history.replaceState(null, '', `#${itBloodBankSectionHashById[resolvedBloodSection] || initialHash}`);
    }
}

function write(data) {
    out.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
}

function useUserToken() {
    document.getElementById('tokenInput').value = localStorage.getItem('USER_TOKEN') || '';
}

function selectedToken() {
    return document.getElementById('tokenInput').value.trim();
}

function refreshCtx() {
    const ctx = document.getElementById('ctx');
    if (!ctx) return;

    ctx.textContent = JSON.stringify({
        ADMIN_TOKEN_PRESENT: !!localStorage.getItem('ADMIN_TOKEN'),
        USER_TOKEN_PRESENT: !!localStorage.getItem('USER_TOKEN'),
        LAST_ADMISSION_ID: localStorage.getItem('LAST_ADMISSION_ID'),
        LAST_ASSIGNED_BED_ID: localStorage.getItem('LAST_ASSIGNED_BED_ID'),
        LAST_CARE_UNIT_ID: localStorage.getItem('LAST_CARE_UNIT_ID'),
        LAST_BED_ID: localStorage.getItem('LAST_BED_ID'),
        SCOPE_DEPARTMENTS: state.scopeDepartments.map((department) => ({ id: department.id, dept_name: department.dept_name })),
    }, null, 2);
}

function buildApiUrl(path, query = null) {
    if (!query) {
        return API + path;
    }

    const queryString = new URLSearchParams(query).toString();
    return queryString ? `${API}${path}?${queryString}` : API + path;
}

async function call(path, method = 'GET', body = null, query = null) {
    const token = selectedToken();
    if (!token) return { status: 401, data: { message: 'Token missing in Auth Context.' } };

    const headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
    };
    const res = await fetch(buildApiUrl(path, query), { method, headers, body: body ? JSON.stringify(body) : undefined });
    const text = await res.text();
    let data = text;
    try { data = JSON.parse(text); } catch {}
    return { status: res.status, data };
}

async function publicDepartments() {
    const res = await fetch('/api/public/departments', { headers: { Accept: 'application/json' } });
    const text = await res.text();
    let data = {};
    try { data = JSON.parse(text); } catch {}
    return Array.isArray(data?.departments) ? data.departments : [];
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function setDepartmentOptions(selectId, departments, includeBlankLabel) {
    const select = document.getElementById(selectId);
    select.innerHTML = `<option value="">${includeBlankLabel}</option>${departments.map((department) => `
        <option value="${department.id}">${escapeHtml(department.dept_name)}</option>
    `).join('')}`;
}

function statusClass(status) {
    const normalized = String(status || '').toLowerCase();
    if (normalized === 'admitted') return 'admitted';
    if (normalized === 'discharged') return 'discharged';
    if (normalized === 'cancelled') return 'cancelled';
    if (normalized === 'transferred') return 'transferred';
    return 'default';
}

function appointmentCapacityKey(appointment) {
    return `${Number(appointment?.doctor_user_id || 0)}|${String(appointment?.appointment_date || '')}`;
}

function queueDerivedUsedCount(appointment) {
    const key = appointmentCapacityKey(appointment);
    return state.appointmentQueue.filter((row) => {
        if (appointmentCapacityKey(row) !== key) return false;
        return ['PendingApproval', 'Approved', 'Booked'].includes(String(row.status || ''));
    }).length;
}

function appointmentStatusBadge(status) {
    const value = String(status || '-');
    if (['Approved', 'Booked', 'Completed'].includes(value)) {
        return `<span class="it-status admitted">${escapeHtml(value)}</span>`;
    }
    if (['PendingApproval'].includes(value)) {
        return `<span class="it-status default">${escapeHtml(value)}</span>`;
    }
    if (['Rejected', 'Cancelled', 'NoShow'].includes(value)) {
        return `<span class="it-status cancelled">${escapeHtml(value)}</span>`;
    }
    return `<span class="it-status transferred">${escapeHtml(value)}</span>`;
}

function renderAppointmentQueue() {
    const body = document.getElementById('itAppointmentQueueBody');
    if (!body) return;

    if (!state.appointmentQueue.length) {
        body.innerHTML = '<tr><td colspan="10">No appointments found for this queue filter.</td></tr>';
        document.getElementById('itAppointmentQueueCount').textContent = '0';
        document.getElementById('itAppointmentPendingCount').textContent = '0';
        document.getElementById('itAppointmentApprovedCount').textContent = '0';
        renderPaginationControls('itAppointmentQueuePagination', 1, 1, 0, 'prevAppointmentQueuePage()', 'nextAppointmentQueuePage()', state.pagination.appointmentQueuePageSize);
        return;
    }

    const pendingCount = state.appointmentQueue.filter((row) => String(row.status) === 'PendingApproval').length;
    const approvedCount = state.appointmentQueue.filter((row) => ['Approved', 'Booked'].includes(String(row.status))).length;
    document.getElementById('itAppointmentQueueCount').textContent = String(state.appointmentQueue.length);
    document.getElementById('itAppointmentPendingCount').textContent = String(pendingCount);
    document.getElementById('itAppointmentApprovedCount').textContent = String(approvedCount);

    const pageData = paginateRows(state.appointmentQueue, state.pagination.appointmentQueuePage, state.pagination.appointmentQueuePageSize);
    state.pagination.appointmentQueuePage = pageData.safePage;

    body.innerHTML = pageData.pagedRows.map((row) => {
        const key = appointmentCapacityKey(row);
        const knownCapacity = state.appointmentCapacity[key] || null;
        const usedCount = knownCapacity?.used_count ?? queueDerivedUsedCount(row);
        const dailyCapacity = knownCapacity?.daily_capacity ?? '-';
        const remainingCount = knownCapacity?.remaining_count ?? '-';
        const actions = String(row.status) === 'PendingApproval'
            ? `
                <button class="it-button soft" type="button" onclick="approveAppointmentQueueItem(${Number(row.id)})">Approve</button>
                <button class="it-button warm" type="button" onclick="rejectAppointmentQueueItem(${Number(row.id)})">Reject</button>
                <button class="it-button danger" type="button" onclick="cancelAppointmentQueueItem(${Number(row.id)})">Cancel</button>
            `
            : `
                <button class="it-button danger" type="button" onclick="cancelAppointmentQueueItem(${Number(row.id)})">Cancel</button>
            `;

        return `
            <tr>
                <td>#${Number(row.id)}</td>
                <td>${escapeHtml(row.appointment_date || '-')}</td>
                <td>${escapeHtml(row.department || '-')}</td>
                <td>${escapeHtml(row.doctor_name || '-')}</td>
                <td>${escapeHtml(row.patient_name || '-')}</td>
                <td>${appointmentStatusBadge(row.status)}</td>
                <td>${dailyCapacity}</td>
                <td>${usedCount}</td>
                <td>${remainingCount}</td>
                <td><div class="it-actions">${actions}</div></td>
            </tr>
        `;
    }).join('');

    renderPaginationControls(
        'itAppointmentQueuePagination',
        pageData.safePage,
        pageData.totalPages,
        pageData.totalRows,
        'prevAppointmentQueuePage()',
        'nextAppointmentQueuePage()',
        state.pagination.appointmentQueuePageSize
    );
}

function syncCounters() {
    document.getElementById('scopeCount').textContent = String(state.scopeDepartments.length);
    document.getElementById('careUnitCount').textContent = String(state.careUnits.length);
    document.getElementById('bedCount').textContent = String(state.beds.length);
    document.getElementById('admissionCount').textContent = String(state.admissions.length);
}

function hasBloodBankScope() {
    return state.scopeDepartments.some((department) => isBloodBankDepartment(department));
}

function hasNonBloodBankScope() {
    return state.scopeDepartments.some((department) => !isBloodBankDepartment(department));
}

function renderDepartmentMode() {
    const bloodBankAccess = hasBloodBankScope();
    const regularAccess = hasNonBloodBankScope();
    const selectedPanel = state.activePanel || 'it-overview';
    const allowed = allowedPanels();
    const modeSummary = document.getElementById('scopeModeSummary');
    const regularLockedMessage = document.getElementById('regularItLockedMessage');

    if (!allowed.includes(selectedPanel)) {
        setActivePanel(allowed[0]);
        return;
    }

    const bloodBankVisible = bloodBankAccess && selectedPanel === 'it-blood-bank';
    const regularWorkspaceVisible = regularAccess && selectedPanel !== 'it-blood-bank';
    setVisibility('it-blood-bank', bloodBankVisible, 'block');
    setVisibility('standardItWorkArea', regularWorkspaceVisible, 'block');
    setVisibility('regularItLocked', !regularAccess && selectedPanel !== 'it-blood-bank', 'block');
    document.getElementById('bloodBankScopeStatus').textContent = bloodBankAccess ? 'Enabled' : 'Locked';
    document.getElementById('bloodBankScopeCount').textContent = String(state.scopeDepartments.filter((department) => isBloodBankDepartment(department)).length);
    regularLockedMessage.textContent = !state.scopeLoaded
        ? 'Load your departments to open the correct IT workspace.'
        : bloodBankAccess
            ? 'This account is scoped only to Blood Bank, so ward and bed operations stay hidden.'
            : 'No non-Blood-Bank department scope was detected for this account.';
    modeSummary.textContent = !state.scopeLoaded
        ? 'Load your departments to open the correct IT workspace.'
        : bloodBankAccess && regularAccess
            ? 'Mixed IT scope is active: regular operations and Blood Bank operations are both available.'
        : bloodBankAccess
                ? 'Blood Bank IT mode is active for this account.'
                : 'Regular IT mode is active for this account.';
    updateSidebarByScope();
}

function bloodBankSelectedRequest() {
    return state.bloodBank.requests.find((request) => Number(request.id) === Number(state.bloodBank.selectedRequestId)) || null;
}

function bloodBankBadge(status) {
    const normalized = String(status || '').toLowerCase();
    if (['accepted', 'completed', 'fulfilled', 'eligible'].includes(normalized)) {
        return `<span class="it-status admitted">${escapeHtml(status)}</span>`;
    }
    if (['pending', 'matched', 'approved', 'notified', 'suggested'].includes(normalized)) {
        return `<span class="it-status default">${escapeHtml(status)}</span>`;
    }
    if (['declined', 'rejected', 'cancelled', 'not eligible'].includes(normalized)) {
        return `<span class="it-status cancelled">${escapeHtml(status)}</span>`;
    }
    return `<span class="it-status transferred">${escapeHtml(status || '-')}</span>`;
}

function syncBloodBankStats() {
    document.getElementById('bbRequestCount').textContent = String(state.bloodBank.requests.length);
    document.getElementById('bbAcceptedCount').textContent = String(
        state.bloodBank.matches.filter((row) => ['Accepted', 'Completed'].includes(String(row.status || ''))).length
    );
    document.getElementById('bbDonorCount').textContent = String(state.bloodBank.staffDonors.length);
    const selected = bloodBankSelectedRequest();
    document.getElementById('bbSelectedRequestLabel').textContent = selected ? `#${selected.id}` : 'None';
}

function renderBloodBankSelectedRequest() {
    const request = bloodBankSelectedRequest();
    const hint = document.getElementById('bbSelectedRequestHint');
    const meta = document.getElementById('bbSelectedRequestMeta');

    if (!request) {
        hint.textContent = 'Pick a request from the Request Board to unlock request-specific actions.';
        meta.textContent = 'No request selected.';
        document.getElementById('bbLinkedRequestId').value = '';
        return;
    }

    hint.textContent = `Request #${request.id} is active. Notify donors, approve an accepted match, then fulfill when blood is actually available.`;
    meta.innerHTML = `
        <strong>#${request.id}</strong> | ${escapeHtml(request.blood_group_needed || '-')} ${escapeHtml(request.component_type || '-')} | Units ${request.units_required ?? '-'}<br>
        Patient: ${escapeHtml(request.patient_name || '-')} (${escapeHtml(request.patient_email || '-')})<br>
        Department: ${escapeHtml(request.department_name || '-')} | Bank: ${escapeHtml(request.bank_name || 'Not set')}<br>
        Inventory visible: <strong>${request.available_units ?? 0}</strong> | Accepted donors: <strong>${request.accepted_count ?? 0}</strong>
    `;
    document.getElementById('bbLinkedRequestId').value = String(request.id);
    document.getElementById('bbDonorSearchRequestId').value = String(request.id);
    if (request.blood_group_needed) {
        document.getElementById('bbDonationBloodGroup').value = String(request.blood_group_needed);
    }
}

function renderBloodBankRequests() {
    const body = document.getElementById('bbRequestsBody');
    if (!state.bloodBank.requests.length) {
        body.innerHTML = '<tr><td colspan="6">No blood requests found.</td></tr>';
        syncBloodBankStats();
        renderBloodBankSelectedRequest();
        return;
    }

    body.innerHTML = state.bloodBank.requests.map((request) => `
        <tr class="${Number(state.bloodBank.selectedRequestId) === Number(request.id) ? 'is-selected' : ''}" onclick="selectBloodBankRequest(${Number(request.id)})">
            <td>#${Number(request.id)}</td>
            <td>${escapeHtml(request.patient_name || '-')}</td>
            <td>${escapeHtml(request.blood_group_needed || '-')} / ${escapeHtml(request.component_type || '-')}</td>
            <td>${request.units_required ?? '-'}</td>
            <td>${bloodBankBadge(request.status)}</td>
            <td>${request.accepted_count ?? 0}</td>
        </tr>
    `).join('');
    syncBloodBankStats();
    renderBloodBankSelectedRequest();
}

function toggleBloodBankSuggestedDonor(donorId, checked) {
    if (checked) {
        state.bloodBank.selectedDonorIds.add(Number(donorId));
    } else {
        state.bloodBank.selectedDonorIds.delete(Number(donorId));
    }
    renderBloodBankSuggestions(state.bloodBank.suggestions);
}

function renderBloodBankSuggestions(rows = []) {
    state.bloodBank.suggestions = rows;
    const grid = document.getElementById('bbSuggestionsGrid');
    if (!rows.length) {
        grid.innerHTML = '<div class="bbops-empty">No compatible donors found for the selected request.</div>';
        return;
    }

    grid.innerHTML = rows.map((row) => `
        <article class="bbops-card ${state.bloodBank.selectedDonorIds.has(Number(row.donor_id)) ? 'is-selected' : ''}">
            <div class="bbops-card__head">
                <label class="it-note" style="margin:0;">
                    <input type="checkbox" ${state.bloodBank.selectedDonorIds.has(Number(row.donor_id)) ? 'checked' : ''} onchange="toggleBloodBankSuggestedDonor(${Number(row.donor_id)}, this.checked)">
                    <strong>${escapeHtml(row.donor_name || `Donor #${row.donor_id}`)}</strong>
                </label>
                ${bloodBankBadge(row.compatibility_label || 'Compatible')}
            </div>
            <p class="it-note">${escapeHtml(row.donor_email || '-')}</p>
            <div class="bbops-meta">
                <span>Donor #${Number(row.donor_id)}</span>
                <span>${escapeHtml(row.donor_blood_group || '-')}</span>
                <span>${row.is_eligible ? 'Eligible' : 'Not eligible'}</span>
            </div>
            <div class="bbops-meta">
                <span>Latest check: ${row.last_check_datetime ? new Date(row.last_check_datetime).toLocaleString() : 'No check yet'}</span>
                <span>Week bags: ${row.max_bags_possible ?? 0}</span>
            </div>
        </article>
    `).join('');
}

function useBloodBankMatch(matchId, donorId = null, group = '') {
    document.getElementById('bbSelectedMatchId').value = String(matchId);
    if (donorId) {
        document.getElementById('bbDonationDonorId').value = String(donorId);
        loadBloodBankDonationHealthChecks();
    }
    if (group) {
        document.getElementById('bbDonationBloodGroup').value = group;
    }
}

function renderBloodBankMatches() {
    const body = document.getElementById('bbMatchesBody');
    if (!state.bloodBank.matches.length) {
        body.innerHTML = '<tr><td colspan="7">No match records yet.</td></tr>';
        syncBloodBankStats();
        return;
    }

    body.innerHTML = state.bloodBank.matches.map((row) => `
        <tr>
            <td>#${Number(row.id)}</td>
            <td>${escapeHtml(row.donor_name || '-')}</td>
            <td>${escapeHtml(row.donor_blood_group || '-')}</td>
            <td>${bloodBankBadge(row.status)}</td>
            <td>${row.notified_at ? new Date(row.notified_at).toLocaleString() : '-'}</td>
            <td>${row.responded_at ? new Date(row.responded_at).toLocaleString() : '-'}</td>
            <td>${['Accepted', 'Completed'].includes(String(row.status || ''))
                ? `<button class="it-button soft" type="button" onclick="useBloodBankMatch(${Number(row.id)}, ${Number(row.donor_id)}, '${escapeHtml(row.donor_blood_group || '')}')">Use</button>`
                : '<span class="it-note">Wait</span>'}
            </td>
        </tr>
    `).join('');
    syncBloodBankStats();
}

function renderBloodBankStaffDonors() {
    const grid = document.getElementById('bbStaffDonorGrid');
    if (!state.bloodBank.staffDonors.length) {
        grid.innerHTML = '<div class="bbops-empty">No donors found for this search.</div>';
        renderPaginationControls('bbStaffDonorPagination', 1, 1, 0, 'prevBloodDonorsPage()', 'nextBloodDonorsPage()', state.pagination.bloodDonorsPageSize);
        syncBloodBankStats();
        return;
    }

    const pageData = paginateRows(
        state.bloodBank.staffDonors,
        state.pagination.bloodDonorsPage,
        state.pagination.bloodDonorsPageSize
    );
    state.pagination.bloodDonorsPage = pageData.safePage;

    grid.innerHTML = pageData.pagedRows.map((row) => `
        <article class="bbops-card">
            <div class="bbops-card__head">
                <strong>${escapeHtml(row.donor_name || `Donor #${row.donor_id}`)}</strong>
                ${bloodBankBadge(row.is_eligible ? 'Eligible' : 'Not eligible')}
            </div>
            <p class="it-note">${escapeHtml(row.donor_email || '-')}</p>
            <div class="bbops-meta">
                <span>Donor #${Number(row.donor_id)}</span>
                <span>${escapeHtml(row.blood_group || '-')}</span>
                <span>Latest check #${row.latest_health_check_id ?? '-'}</span>
            </div>
            <div class="bbops-meta">
                <span>Matched request: ${row.matched_request_id ? `#${row.matched_request_id}` : 'None'}</span>
                <span>${escapeHtml(row.matched_request_status || 'No match')}</span>
            </div>
            <div class="bbops-actions">
                <button class="it-button primary" type="button" onclick="selectBloodBankDonationDonor(${Number(row.donor_id)}, '${escapeHtml(row.blood_group || '')}', ${row.latest_health_check_id ?? 'null'}, ${row.matched_request_id ?? 'null'})">Use donor</button>
            </div>
        </article>
    `).join('');
    renderPaginationControls(
        'bbStaffDonorPagination',
        pageData.safePage,
        pageData.totalPages,
        pageData.totalRows,
        'prevBloodDonorsPage()',
        'nextBloodDonorsPage()',
        state.pagination.bloodDonorsPageSize
    );
    syncBloodBankStats();
}

function renderBloodBankDonationHealthChecks(rows = [], preferredId = null) {
    const body = document.getElementById('bbDonationHealthChecksBody');
    const select = document.getElementById('bbDonationHealthCheckId');

    body.innerHTML = rows.length
        ? rows.map((row) => `
            <tr>
                <td>${row.id}</td>
                <td>${row.check_datetime ? new Date(row.check_datetime).toLocaleString() : '-'}</td>
                <td>${row.weight_kg ?? '-'}</td>
                <td>${row.temperature_c ?? '-'}</td>
                <td>${row.hemoglobin ?? '-'}</td>
                <td>${escapeHtml(row.checked_by_name || '-')}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="6">No donor health checks available.</td></tr>';

    select.innerHTML = ['<option value="">Select latest nurse screening</option>']
        .concat(rows.map((row) => `<option value="${row.id}">#${row.id} - ${row.check_datetime ? new Date(row.check_datetime).toLocaleString() : 'No date'}</option>`))
        .join('');

    if (preferredId) {
        select.value = String(preferredId);
    } else if (rows.length) {
        select.value = String(rows[0].id);
    }
}

async function loadBloodBankBanks() {
    const result = await call('/blood/schema/banks');
    write(result);
    if (result.status >= 300) {
        return;
    }

    const rows = Array.isArray(result.data?.banks) ? result.data.banks : [];
    document.getElementById('bbFulfillmentBankId').innerHTML = ['<option value="">Keep request bank / none</option>']
        .concat(rows.map((row) => `<option value="${row.id}">${escapeHtml(row.bank_name)}</option>`))
        .join('');
    document.getElementById('bbDonationBankId').innerHTML = ['<option value="">Choose blood bank</option>']
        .concat(rows.map((row) => `<option value="${row.id}">${escapeHtml(row.bank_name)}</option>`))
        .join('');
    state.bloodBank.banksLoaded = true;
}

function bloodBankRequestQuery() {
    const query = {};
    const status = document.getElementById('bbStatusFilter').value.trim();
    const bloodGroup = document.getElementById('bbBloodGroupFilter').value.trim();
    const departmentId = document.getElementById('bbDepartmentFilter').value.trim();
    const limit = document.getElementById('bbRequestLimit').value.trim();

    if (status) query.status = status;
    if (bloodGroup) query.bloodGroup = bloodGroup;
    if (departmentId) query.departmentId = Number(departmentId);
    if (limit) query.limit = Number(limit);
    return query;
}

async function loadBloodBankRequests() {
    const result = await call('/blood/matching/requests', 'GET', null, bloodBankRequestQuery());
    write(result);
    if (result.status >= 300) {
        state.bloodBank.requests = [];
        renderBloodBankRequests();
        return;
    }

    state.bloodBank.requests = Array.isArray(result.data?.requests) ? result.data.requests : [];
    renderBloodBankRequests();

    if (!state.bloodBank.selectedRequestId && state.bloodBank.requests.length) {
        await selectBloodBankRequest(state.bloodBank.requests[0].id);
    } else if (state.bloodBank.selectedRequestId && !bloodBankSelectedRequest()) {
        state.bloodBank.selectedRequestId = null;
        state.bloodBank.matches = [];
        renderBloodBankMatches();
        renderBloodBankSuggestions([]);
        renderBloodBankSelectedRequest();
    }
}

async function loadBloodBankSuggestions() {
    if (!state.bloodBank.selectedRequestId) {
        renderBloodBankSuggestions([]);
        return;
    }

    const result = await call(`/blood/matching/requests/${state.bloodBank.selectedRequestId}/suggestions`, 'GET', null, { limit: 20 });
    write(result);
    if (result.status >= 300) {
        renderBloodBankSuggestions([]);
        return;
    }

    renderBloodBankSuggestions(Array.isArray(result.data?.suggestions) ? result.data.suggestions : []);
}

async function loadBloodBankMatches() {
    if (!state.bloodBank.selectedRequestId) {
        state.bloodBank.matches = [];
        renderBloodBankMatches();
        return;
    }

    const result = await call(`/blood/matching/requests/${state.bloodBank.selectedRequestId}/matches`);
    write(result);
    if (result.status >= 300) {
        state.bloodBank.matches = [];
        renderBloodBankMatches();
        return;
    }

    state.bloodBank.matches = Array.isArray(result.data?.matches) ? result.data.matches : [];
    renderBloodBankMatches();
}

function bloodBankDonorQuery() {
    const query = { limit: 120 };
    const search = document.getElementById('bbDonorSearchQuery').value.trim();
    const requestId = document.getElementById('bbDonorSearchRequestId').value.trim();
    const bloodGroup = document.getElementById('bbDonorSearchBloodGroup').value.trim();
    const eligible = document.getElementById('bbDonorSearchEligible').value.trim();

    if (search) query.q = search;
    if (requestId) query.requestId = Number(requestId);
    if (bloodGroup) query.bloodGroup = bloodGroup;
    if (eligible) query.eligible = eligible === 'true';
    return query;
}

async function loadBloodBankStaffDonors() {
    const result = await call('/blood/matching/donors', 'GET', null, bloodBankDonorQuery());
    write(result);
    if (result.status >= 300) {
        state.bloodBank.staffDonors = [];
        state.pagination.bloodDonorsPage = 1;
        renderBloodBankStaffDonors();
        return;
    }

    state.bloodBank.staffDonors = Array.isArray(result.data?.donors) ? result.data.donors : [];
    state.pagination.bloodDonorsPage = 1;
    renderBloodBankStaffDonors();
}

async function selectBloodBankRequest(requestId) {
    const nextRequestId = Number(requestId);
    if (Number(state.bloodBank.selectedRequestId) !== nextRequestId) {
        state.bloodBank.selectedDonorIds.clear();
        document.getElementById('bbSelectedMatchId').value = '';
    }

    state.bloodBank.selectedRequestId = nextRequestId;
    renderBloodBankRequests();
    await Promise.all([
        loadBloodBankSuggestions(),
        loadBloodBankMatches(),
        loadBloodBankStaffDonors(),
    ]);
}

async function notifyBloodBankDonors() {
    if (!state.bloodBank.selectedRequestId) {
        write({ status: 422, data: { message: 'Select a blood request first.' } });
        return;
    }

    const payload = {
        donorIds: Array.from(state.bloodBank.selectedDonorIds),
        message: document.getElementById('bbNotifyMessage').value.trim() || null,
        suggestedLimit: 6,
    };

    const result = await call(`/blood/matching/requests/${state.bloodBank.selectedRequestId}/notify`, 'POST', payload);
    write(result);
    if (result.status < 300) {
        state.bloodBank.selectedDonorIds.clear();
        await Promise.all([
            loadBloodBankRequests(),
            loadBloodBankSuggestions(),
            loadBloodBankMatches(),
        ]);
    }
}

async function approveBloodBankMatch() {
    if (!state.bloodBank.selectedRequestId) {
        write({ status: 422, data: { message: 'Select a blood request first.' } });
        return;
    }

    const matchId = Number(document.getElementById('bbSelectedMatchId').value || 0);
    if (!matchId) {
        write({ status: 422, data: { message: 'Choose an accepted donor match first.' } });
        return;
    }

    const payload = {
        matchId,
        bloodBankId: document.getElementById('bbFulfillmentBankId').value ? Number(document.getElementById('bbFulfillmentBankId').value) : null,
        note: document.getElementById('bbWorkflowNote').value.trim() || null,
    };

    const result = await call(`/blood/matching/requests/${state.bloodBank.selectedRequestId}/approve`, 'POST', payload);
    write(result);
    if (result.status < 300) {
        await Promise.all([
            loadBloodBankRequests(),
            loadBloodBankMatches(),
        ]);
    }
}

async function fulfillBloodBankRequest() {
    if (!state.bloodBank.selectedRequestId) {
        write({ status: 422, data: { message: 'Select a blood request first.' } });
        return;
    }

    const payload = {
        matchId: document.getElementById('bbSelectedMatchId').value ? Number(document.getElementById('bbSelectedMatchId').value) : null,
        bloodBankId: document.getElementById('bbFulfillmentBankId').value ? Number(document.getElementById('bbFulfillmentBankId').value) : null,
        consumeInventory: !!document.getElementById('bbConsumeInventory').checked,
        note: document.getElementById('bbWorkflowNote').value.trim() || null,
    };

    const result = await call(`/blood/matching/requests/${state.bloodBank.selectedRequestId}/fulfill`, 'POST', payload);
    write(result);
    if (result.status < 300) {
        await Promise.all([
            loadBloodBankRequests(),
            loadBloodBankMatches(),
        ]);
    }
}

async function selectBloodBankDonationDonor(donorId, group = '', healthCheckId = null, requestId = null) {
    document.getElementById('bbDonationDonorId').value = String(donorId);
    if (group) {
        document.getElementById('bbDonationBloodGroup').value = group;
    }
    if (requestId && !document.getElementById('bbLinkedRequestId').value) {
        document.getElementById('bbLinkedRequestId').value = String(requestId);
    }
    await loadBloodBankDonationHealthChecks(healthCheckId);
}

async function loadBloodBankDonationHealthChecks(preferredId = null) {
    const donorId = Number(document.getElementById('bbDonationDonorId').value || 0);
    if (!donorId) {
        renderBloodBankDonationHealthChecks([]);
        return;
    }

    const result = await call(`/blood/matching/donors/${donorId}/health-checks`, 'GET', null, { limit: 12 });
    write(result);
    if (result.status >= 300) {
        renderBloodBankDonationHealthChecks([]);
        return;
    }

    renderBloodBankDonationHealthChecks(Array.isArray(result.data?.health_checks) ? result.data.health_checks : [], preferredId);
}

async function logBloodBankDonation() {
    const donorId = Number(document.getElementById('bbDonationDonorId').value || 0);
    if (!donorId) {
        write({ status: 422, data: { message: 'Choose a donor first.' } });
        return;
    }

    const payload = {
        donorId,
        donorHealthCheckId: Number(document.getElementById('bbDonationHealthCheckId').value || 0),
        bloodBankId: Number(document.getElementById('bbDonationBankId').value || 0),
        donationDateTime: document.getElementById('bbDonationDateTime').value || null,
        bloodGroup: document.getElementById('bbDonationBloodGroup').value || null,
        componentType: document.getElementById('bbComponentType').value || null,
        unitsDonated: Number(document.getElementById('bbUnitsDonated').value || 1),
        linkedRequestId: document.getElementById('bbLinkedRequestId').value ? Number(document.getElementById('bbLinkedRequestId').value) : null,
        notes: document.getElementById('bbDonationNotes').value.trim() || null,
    };

    const result = await call('/blood/matching/donations', 'POST', payload);
    write(result);
    if (result.status < 300) {
        await Promise.all([
            loadBloodBankRequests(),
            loadBloodBankStaffDonors(),
        ]);
    }
}

async function refreshBloodBankWorkspace() {
    if (!hasBloodBankScope() || !selectedToken()) {
        return;
    }

    if (!state.bloodBank.banksLoaded) {
        await loadBloodBankBanks();
    }

    await loadBloodBankRequests();
    if (state.bloodBank.selectedRequestId) {
        await Promise.all([
            loadBloodBankSuggestions(),
            loadBloodBankMatches(),
        ]);
    }
    await loadBloodBankStaffDonors();
    state.bloodBank.workspaceLoaded = true;
}

async function maybeLoadBloodBankWorkspace() {
    if (!hasBloodBankScope() || !selectedToken()) {
        return;
    }

    if (!state.bloodBank.workspaceLoaded) {
        await refreshBloodBankWorkspace();
    }
}

function isArtificialDirectoryEntry(entry) {
    const name = String(entry?.full_name || '').trim().toLowerCase();
    const email = String(entry?.email || '').trim().toLowerCase();

    if (!name && !email) {
        return false;
    }

    return email.endsWith('_ui@demo.com')
        || name.endsWith(' ui');
}

function paginateRows(rows, page, pageSize) {
    const safeRows = Array.isArray(rows) ? rows : [];
    const totalPages = Math.max(1, Math.ceil(safeRows.length / pageSize));
    const safePage = Math.min(Math.max(1, Number(page) || 1), totalPages);
    const startIndex = (safePage - 1) * pageSize;
    const pagedRows = safeRows.slice(startIndex, startIndex + pageSize);

    return {
        pagedRows,
        totalPages,
        safePage,
        totalRows: safeRows.length,
    };
}

function renderPaginationControls(rootId, page, totalPages, totalRows, onPrev, onNext, pageSize = state.pagination.directoryPageSize) {
    const root = document.getElementById(rootId);
    if (!root) return;

    if (totalRows <= pageSize) {
        root.innerHTML = '';
        return;
    }

    root.innerHTML = `
        <div class="ui-list-pagination__meta">Page ${page} of ${totalPages} (${totalRows} total)</div>
        <div class="ui-list-pagination__controls">
            <button class="it-button soft" type="button" ${page <= 1 ? 'disabled' : ''} onclick="${onPrev}">Previous</button>
            <button class="it-button soft" type="button" ${page >= totalPages ? 'disabled' : ''} onclick="${onNext}">Next</button>
        </div>
    `;
}

function prevDoctorsPage() {
    state.pagination.doctorsPage = Math.max(1, state.pagination.doctorsPage - 1);
    renderDoctors();
}

function nextDoctorsPage() {
    state.pagination.doctorsPage += 1;
    renderDoctors();
}

function prevPatientsPage() {
    state.pagination.patientsPage = Math.max(1, state.pagination.patientsPage - 1);
    renderPatientsDirectory();
}

function nextPatientsPage() {
    state.pagination.patientsPage += 1;
    renderPatientsDirectory();
}

function prevAppointmentQueuePage() {
    state.pagination.appointmentQueuePage = Math.max(1, state.pagination.appointmentQueuePage - 1);
    renderAppointmentQueue();
}

function nextAppointmentQueuePage() {
    state.pagination.appointmentQueuePage += 1;
    renderAppointmentQueue();
}

function prevBloodDonorsPage() {
    state.pagination.bloodDonorsPage = Math.max(1, state.pagination.bloodDonorsPage - 1);
    renderBloodBankStaffDonors();
}

function nextBloodDonorsPage() {
    state.pagination.bloodDonorsPage += 1;
    renderBloodBankStaffDonors();
}

function renderDoctors() {
    const root = document.getElementById('doctorCards');
    if (!state.doctors.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No doctors loaded yet.</p></div>';
        renderPaginationControls('doctorPagination', 1, 1, 0, 'prevDoctorsPage()', 'nextDoctorsPage()');
        return;
    }

    const pageData = paginateRows(state.doctors, state.pagination.doctorsPage, state.pagination.directoryPageSize);
    state.pagination.doctorsPage = pageData.safePage;

    root.innerHTML = pageData.pagedRows.map((doctor) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(doctor.full_name || 'Unnamed doctor')}</h3>
                    <p class="it-note">${escapeHtml(doctor.email || '')}</p>
                </div>
                <span class="it-status default">${escapeHtml(doctor.department || 'Unknown department')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Doctor ID</small><strong>#${doctor.doctor_id}</strong></span>
            </div>
            <div class="it-actions">
                <button class="it-button accent" type="button" onclick="pickDoctor(${doctor.doctor_id}, ${doctor.department_id})">Use doctor</button>
            </div>
        </article>
    `).join('');

    renderPaginationControls(
        'doctorPagination',
        pageData.safePage,
        pageData.totalPages,
        pageData.totalRows,
        'prevDoctorsPage()',
        'nextDoctorsPage()'
    );
}

function renderPatientsDirectory() {
    const root = document.getElementById('patientCards');
    if (!state.patients.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No patients loaded yet.</p></div>';
        renderPaginationControls('patientPagination', 1, 1, 0, 'prevPatientsPage()', 'nextPatientsPage()');
        return;
    }

    const pageData = paginateRows(state.patients, state.pagination.patientsPage, state.pagination.directoryPageSize);
    state.pagination.patientsPage = pageData.safePage;

    root.innerHTML = pageData.pagedRows.map((patient) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(patient.full_name || 'Unnamed patient')}</h3>
                    <p class="it-note">${escapeHtml(patient.email || '')}</p>
                </div>
                <span class="it-status default">${escapeHtml(patient.blood_group || 'Blood group not set')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Patient ID</small><strong>#${patient.patient_user_id}</strong></span>
            </div>
            <div class="it-actions">
                <button class="it-button accent" type="button" onclick="pickPatient(${patient.patient_user_id})">Use patient</button>
            </div>
        </article>
    `).join('');

    renderPaginationControls(
        'patientPagination',
        pageData.safePage,
        pageData.totalPages,
        pageData.totalRows,
        'prevPatientsPage()',
        'nextPatientsPage()'
    );
}

function renderAdmissions() {
    const root = document.getElementById('admissionCards');
    if (!root) return;

    if (!state.admissions.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No admissions loaded yet.</p></div>';
        return;
    }

    root.innerHTML = state.admissions.map((admission) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(admission.patient_name || 'Unknown patient')}</h3>
                    <p class="it-note">${escapeHtml(admission.department || 'Unknown department')}</p>
                </div>
                <span class="it-status ${statusClass(admission.status)}">${escapeHtml(admission.status || 'Unknown')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Admission</small><strong>#${admission.id}</strong></span>
                <span class="it-chip"><small>Care level</small><strong>${escapeHtml(admission.care_level_assigned || admission.care_level_requested || '-')}</strong></span>
                <span class="it-chip"><small>Bed</small><strong>${escapeHtml(admission.active_bed_assignment?.bed_code || 'Not assigned')}</strong></span>
            </div>
            <p class="it-note u-mt-3">${escapeHtml(admission.diagnosis || 'No diagnosis')}</p>
            <div class="it-actions">
                <button class="it-button soft" type="button" onclick="pickAdmission(${admission.id})">Use admission ID</button>
                <button class="it-button danger" type="button" onclick="prefillDischarge(${admission.id})">Prepare discharge</button>
            </div>
        </article>
    `).join('');
}

function renderBeds() {
    const root = document.getElementById('bedCards');
    if (!root) return;

    if (!state.beds.length) {
        root.innerHTML = '<div class="it-card"><p class="it-note">No available beds loaded yet.</p></div>';
        return;
    }

    root.innerHTML = state.beds.map((bed) => `
        <article class="it-card">
            <div class="it-card__head">
                <div>
                    <h3>${escapeHtml(bed.bed_code || 'Unnamed bed')}</h3>
                    <p class="it-note">${escapeHtml(bed.department || 'Unknown department')}</p>
                </div>
                <span class="it-status default">${escapeHtml(bed.status || 'Unknown')}</span>
            </div>
            <div class="it-card__meta u-mt-3">
                <span class="it-chip"><small>Bed ID</small><strong>#${bed.id}</strong></span>
                <span class="it-chip"><small>Unit</small><strong>${escapeHtml(bed.unit_type || '-')}</strong></span>
                <span class="it-chip"><small>Floor</small><strong>${escapeHtml(bed.floor ?? '-')}</strong></span>
            </div>
            <div class="it-actions">
                <button class="it-button accent" type="button" onclick="pickBed(${bed.id})">Use bed ID</button>
            </div>
        </article>
    `).join('');
}

function renderCareUnitsTable() {
    const body = document.getElementById('careUnitsBody');
    body.innerHTML = state.careUnits.length
        ? state.careUnits.map((unit) => `
            <tr>
                <td>${unit.id ?? '-'}</td>
                <td>${escapeHtml(unit.department?.dept_name || unit.department || '-')}</td>
                <td>${escapeHtml(unit.unit_type || '-')}</td>
                <td>${escapeHtml(unit.unit_name || '-')}</td>
                <td>${escapeHtml(unit.floor ?? '-')}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No care units loaded.</td></tr>';
}

function renderBedsTable() {
    const body = document.getElementById('bedsBody');
    body.innerHTML = state.beds.length
        ? state.beds.map((bed) => `
            <tr>
                <td>${bed.id ?? '-'}</td>
                <td>${escapeHtml(bed.bed_code || '-')}</td>
                <td>${escapeHtml(bed.status || '-')}</td>
                <td>${escapeHtml(bed.unit_name || bed.unit_type || '-')}</td>
                <td>${escapeHtml(bed.department || '-')}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="5">No beds loaded.</td></tr>';
}

function pickAdmission(admissionId) {
    document.getElementById('assignAdmissionId').value = String(admissionId);
}

function prefillDischarge(admissionId) {
    document.getElementById('dischargeAdmissionId').value = String(admissionId);
}

function pickBed(bedId) {
    document.getElementById('assignBedId').value = String(bedId);
}

function pickDoctor(doctorId, departmentId) {
    document.getElementById('admittedByDoctorId').value = String(doctorId);
    if (!document.getElementById('admissionDepartmentId').value && departmentId) {
        document.getElementById('admissionDepartmentId').value = String(departmentId);
    }
}

function pickPatient(patientId) {
    document.getElementById('patientUserId').value = String(patientId);
}

async function loadDepartmentSelectors() {
    state.departments = await publicDepartments();
    setDepartmentOptions('wardDepartmentId', state.departments, 'Select department');
    setDepartmentOptions('admissionDepartmentId', state.departments, 'Select department');
    setDepartmentOptions('filterDepartmentId', state.departments, 'All accessible departments');
    setDepartmentOptions('itAppointmentDepartmentId', state.departments, 'All accessible departments');
    setDepartmentOptions('doctorSearchDepartmentId', state.departments, 'All accessible departments');
    setDepartmentOptions('patientSearchDepartmentId', state.departments, 'All patients');
    setDepartmentOptions('bbDepartmentFilter', state.departments, 'All departments');
}

async function listDepartments() {
    const result = await call('/ward/departments');
    write(result);
}

async function loadDepartmentsScope() {
    const result = await call('/ward/it/departments');
    write(result);
    state.scopeLoaded = true;
    if (result.status < 300) {
        state.scopeDepartments = Array.isArray(result.data?.departments) ? result.data.departments : [];
        syncCounters();
        refreshCtx();
    } else {
        state.scopeDepartments = [];
    }
    renderDepartmentMode();

    if (!allowedPanels().includes(state.activePanel) || state.activePanel === 'it-overview') {
        const initialHash = (window.location.hash || '').replace('#', '');
        const resolvedBloodSection = itBloodBankSectionHashMap[initialHash] || '';
        const hashIsBloodBankSection = !!resolvedBloodSection;
        const requestedPanel = hashIsBloodBankSection ? 'it-blood-bank' : initialHash;
        const nextPanel = allowedPanels().includes(requestedPanel) ? requestedPanel : (allowedPanels()[1] || allowedPanels()[0]);
        const nextSection = nextPanel === 'it-blood-bank' && hashIsBloodBankSection ? resolvedBloodSection : '';
        const nextHash = nextSection ? (itBloodBankSectionHashById[nextSection] || nextSection) : nextPanel;
        setActivePanel(nextPanel, nextSection);
        history.replaceState(null, '', `#${nextHash}`);
    }

    if (result.status < 300 && hasNonBloodBankScope()) {
        await Promise.all([loadDoctors(), loadPatients(), loadAppointmentQueue()]);
    }

    if (result.status < 300 && hasBloodBankScope()) {
        await maybeLoadBloodBankWorkspace();
    }
}

async function loadDoctors() {
    const departmentId = document.getElementById('doctorSearchDepartmentId').value.trim();
    const q = document.getElementById('doctorSearchQuery').value.trim();
    const params = new URLSearchParams();
    if (departmentId) params.set('departmentId', departmentId);
    if (q) params.set('q', q);
    const result = await call(`/ward/it/doctors${params.toString() ? `?${params.toString()}` : ''}`);
    write(result);
    if (result.status < 300) {
        const rows = Array.isArray(result.data?.doctors) ? result.data.doctors : [];
        state.doctors = rows.filter((doctor) => !isArtificialDirectoryEntry(doctor));
        state.pagination.doctorsPage = 1;
        renderDoctors();
    }
}

async function loadPatients() {
    const departmentId = document.getElementById('patientSearchDepartmentId').value.trim();
    const q = document.getElementById('patientSearchQuery').value.trim();
    const params = new URLSearchParams();
    if (departmentId) params.set('departmentId', departmentId);
    if (q) params.set('q', q);
    const result = await call(`/ward/it/patients${params.toString() ? `?${params.toString()}` : ''}`);
    write(result);
    if (result.status < 300) {
        const rows = Array.isArray(result.data?.patients) ? result.data.patients : [];
        state.patients = rows.filter((patient) => !isArtificialDirectoryEntry(patient));
        state.pagination.patientsPage = 1;
        renderPatientsDirectory();
    }
}

function appointmentQueueQuery() {
    const query = {};
    const doctorUserId = document.getElementById('itAppointmentDoctorUserId').value.trim();
    const departmentId = document.getElementById('itAppointmentDepartmentId').value.trim();
    const appointmentDate = document.getElementById('itAppointmentDate').value.trim();
    const status = document.getElementById('itAppointmentStatus').value.trim();

    if (doctorUserId) query.doctorUserId = Number(doctorUserId);
    if (departmentId) query.departmentId = Number(departmentId);
    if (appointmentDate) query.appointmentDate = appointmentDate;
    if (status) query.status = status;
    return query;
}

async function loadAppointmentQueue() {
    const result = await call('/appointments/it/queue', 'GET', null, appointmentQueueQuery());
    write(result);
    if (result.status < 300) {
        state.appointmentQueue = Array.isArray(result.data?.appointments) ? result.data.appointments : [];
        state.pagination.appointmentQueuePage = 1;
        renderAppointmentQueue();
    } else {
        state.appointmentQueue = [];
        renderAppointmentQueue();
    }
}

function rememberQueueCapacity(appointment, capacityPayload) {
    if (!appointment || !capacityPayload) return;
    const key = appointmentCapacityKey(appointment);
    state.appointmentCapacity[key] = {
        daily_capacity: capacityPayload.daily_capacity ?? '-',
        used_count: capacityPayload.used_count ?? '-',
        remaining_count: capacityPayload.remaining_count ?? '-',
    };
}

async function approveAppointmentQueueItem(appointmentId) {
    const note = prompt('Approval note (optional):', 'Approved by IT');
    const payload = note ? { approvalNote: note } : {};
    const result = await call(`/appointments/it/${appointmentId}/approve`, 'POST', payload);
    write(result);
    if (result.status < 300) {
        rememberQueueCapacity(result.data?.appointment, result.data?.capacity);
        await loadAppointmentQueue();
    }
}

async function rejectAppointmentQueueItem(appointmentId) {
    const reason = prompt('Rejection reason (required):', 'Capacity or clinical queue constraint');
    if (!reason || !reason.trim()) {
        write({ status: 422, data: { message: 'Rejection reason is required.' } });
        return;
    }

    const result = await call(`/appointments/it/${appointmentId}/reject`, 'POST', { rejectionReason: reason.trim() });
    write(result);
    if (result.status < 300) {
        await loadAppointmentQueue();
    }
}

async function cancelAppointmentQueueItem(appointmentId) {
    const reason = prompt('Cancel reason (optional):', 'Cancelled by IT/admin');
    const payload = reason && reason.trim() ? { cancelReason: reason.trim() } : {};
    const result = await call(`/appointments/it/${appointmentId}/cancel`, 'POST', payload);
    write(result);
    if (result.status < 300) {
        await loadAppointmentQueue();
    }
}

async function createCareUnit() {
    const payload = {
        departmentId: Number(document.getElementById('wardDepartmentId').value),
        unitType: document.getElementById('unitType').value,
        unitName: document.getElementById('unitName').value.trim() || null,
        floor: document.getElementById('floor').value ? Number(document.getElementById('floor').value) : null,
    };
    const result = await call('/ward/care-units', 'POST', payload);
    write(result);
    const id = result.data?.care_unit?.id;
    if (id) {
        localStorage.setItem('LAST_CARE_UNIT_ID', String(id));
        document.getElementById('careUnitId').value = String(id);
        await listCareUnits();
    }
    refreshCtx();
}

async function listCareUnits() {
    const result = await call('/ward/care-units');
    write(result);
    if (result.status < 300) {
        state.careUnits = Array.isArray(result.data?.care_units) ? result.data.care_units : [];
        renderCareUnitsTable();
        syncCounters();
    }
}

async function createBed() {
    const payload = {
        careUnitId: Number(document.getElementById('careUnitId').value),
        bedCode: document.getElementById('bedCode').value.trim(),
        status: document.getElementById('bedStatus').value,
    };
    const result = await call('/ward/beds', 'POST', payload);
    write(result);
    const id = result.data?.bed?.id;
    if (id) {
        localStorage.setItem('LAST_BED_ID', String(id));
        await listBeds();
    }
    refreshCtx();
}

async function listBeds() {
    const result = await call('/ward/beds');
    write(result);
    if (result.status < 300) {
        state.beds = Array.isArray(result.data?.beds) ? result.data.beds : [];
        renderBedsTable();
        renderBeds();
        syncCounters();
    }
}

async function createAdmission() {
    const body = {
        patientUserId: Number(document.getElementById('patientUserId').value),
        departmentId: Number(document.getElementById('admissionDepartmentId').value),
        admittedByDoctorId: document.getElementById('admittedByDoctorId').value ? Number(document.getElementById('admittedByDoctorId').value) : null,
        diagnosis: document.getElementById('diagnosis').value.trim(),
        careLevelRequested: document.getElementById('careLevel').value,
        notes: document.getElementById('admissionNotes').value.trim() || null,
    };
    const result = await call('/ward/it/admissions', 'POST', body);
    write(result);
    const id = result.data?.admission?.id;
    if (id) {
        localStorage.setItem('LAST_ADMISSION_ID', String(id));
        document.getElementById('assignAdmissionId').value = String(id);
        await listAdmissions();
    }
    refreshCtx();
}

async function listAdmissions() {
    const departmentId = document.getElementById('filterDepartmentId').value.trim();
    const status = document.getElementById('filterStatus').value.trim();
    const params = new URLSearchParams();
    if (departmentId) params.set('departmentId', departmentId);
    if (status) params.set('status', status);
    const result = await call(`/ward/it/admissions${params.toString() ? `?${params.toString()}` : ''}`);
    write(result);
    if (result.status < 300) {
        state.admissions = Array.isArray(result.data?.admissions) ? result.data.admissions : [];
        renderAdmissions();
        syncCounters();
    }
}

async function availableBeds() {
    const departmentId = document.getElementById('filterDepartmentId').value.trim();
    const unitType = document.getElementById('careLevel').value;
    if (!departmentId) {
        write({ status: 422, data: { message: 'Set a filter department before loading available beds.' } });
        return;
    }
    const result = await call(`/ward/it/available-beds?departmentId=${encodeURIComponent(departmentId)}&unitType=${encodeURIComponent(unitType)}`);
    write(result);
    if (result.status < 300) {
        state.beds = Array.isArray(result.data?.beds) ? result.data.beds : [];
        renderBeds();
        renderBedsTable();
        syncCounters();
    }
}

async function assignBed() {
    const body = {
        admissionId: Number(document.getElementById('assignAdmissionId').value),
        bedId: Number(document.getElementById('assignBedId').value),
    };
    const result = await call('/ward/it/assign-bed', 'POST', body);
    write(result);
    const bedId = result.data?.admission?.active_bed_assignment?.bed_id;
    if (bedId) localStorage.setItem('LAST_ASSIGNED_BED_ID', String(bedId));
    const admissionId = result.data?.admission?.id;
    if (admissionId) document.getElementById('dischargeAdmissionId').value = String(admissionId);
    refreshCtx();
    await listAdmissions();
    if (document.getElementById('filterDepartmentId').value.trim()) await availableBeds();
}

async function dischargeAdmission() {
    const admissionId = Number(document.getElementById('dischargeAdmissionId').value);
    const releaseReason = document.getElementById('releaseReason').value.trim();
    if (!admissionId) {
        write({ status: 422, data: { message: 'Discharge admission id is required.' } });
        return;
    }
    const body = releaseReason ? { releaseReason } : {};
    const result = await call(`/ward/it/admissions/${admissionId}/discharge`, 'POST', body);
    write(result);
    refreshCtx();
    await listAdmissions();
    if (document.getElementById('filterDepartmentId').value.trim()) await availableBeds();
}

function initializeEmptyTables() {
    renderDoctors();
    renderPatientsDirectory();
    renderAppointmentQueue();
    renderAdmissions();
    renderBeds();
    renderCareUnitsTable();
    renderBedsTable();
    renderBloodBankRequests();
    renderBloodBankMatches();
    renderBloodBankSuggestions([]);
    renderBloodBankStaffDonors();
    renderBloodBankDonationHealthChecks([]);
    syncCounters();
    refreshCtx();
    renderDepartmentMode();
}

async function bootItDashboard() {
    setupSidebarPanelNav();
    useUserToken();
    initializeEmptyTables();
    await loadDepartmentSelectors();

    if (selectedToken()) {
        await loadDepartmentsScope();
    } else {
        write('Login first or use USER_TOKEN so the IT dashboard can auto-load your department scope.');
    }
}

bootItDashboard();
</script>
@endpush
