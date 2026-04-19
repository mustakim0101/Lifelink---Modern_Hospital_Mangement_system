<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink UI Directory</title>
    <style>
        :root {
            --ink: #163247;
            --muted: #607585;
            --line: rgba(22, 50, 71, 0.12);
            --surface: #ffffff;
            --bg: #edf4f7;
            --primary: #0f766e;
            --secondary: #1d4ed8;
            --shadow: 0 14px 28px rgba(15, 34, 48, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.14), transparent 24rem),
                linear-gradient(180deg, #f7fbfd 0%, var(--bg) 100%);
        }

        .wrap {
            width: min(1120px, calc(100% - 24px));
            margin: 18px auto 30px;
            display: grid;
            gap: 12px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 16px;
        }

        h1, h2 {
            margin: 0;
        }

        .muted {
            margin: 6px 0 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.93rem;
        }

        .chips {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 13px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.9);
            font-weight: 700;
        }

        .chip.primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .box {
            display: block;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px;
            transition: transform 0.16s ease;
        }

        .box:hover {
            transform: translateY(-2px);
        }

        .box strong {
            display: block;
            margin-bottom: 5px;
        }

        .box span {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        @media (max-width: 980px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 680px) {
            .wrap { width: min(100% - 16px, 1120px); }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <section class="card">
        <h1>LifeLink UI Directory</h1>
        <p class="muted">Jump to prototype pages by role and workflow. Keep this as a navigation hub, not an operational page.</p>
        <div class="chips">
            <a class="chip primary" href="/ui/dashboard">Workspace Hub</a>
            <a class="chip" href="/ui/login">Login</a>
            <a class="chip" href="/">Public Landing</a>
        </div>
    </section>

    <section class="card">
        <h2>Auth and onboarding</h2>
        <div class="grid" style="margin-top: 10px;">
            <a class="box" href="/ui/login"><strong>Login Page</strong><span>Single entry for all roles and registration links.</span></a>
            <a class="box" href="/ui/applications"><strong>Applications</strong><span>Applicant-side submission and status workflow.</span></a>
            <a class="box" href="/ui/application-reviews"><strong>Application Reviews</strong><span>Admin card-based review queue.</span></a>
        </div>
    </section>

    <section class="card">
        <h2>Admin and operations</h2>
        <div class="grid" style="margin-top: 10px;">
            <a class="box" href="/ui/admin-users"><strong>Admin Control</strong><span>Account state and staff profile setup.</span></a>
            <a class="box" href="/ui/it-bed-allocation"><strong>IT Bed Allocation</strong><span>Admissions, bed assignment, and department scope.</span></a>
            <a class="box" href="/ui/ward-setup"><strong>Ward Setup</strong><span>Care units and bed structure setup.</span></a>
            <a id="bloodBankSchemaLink" class="box" href="/ui/blood-bank-schema"><strong>Blood Bank Schema</strong><span>Blood-bank setup and schema inspection.</span></a>
            <a id="bloodMatchingLink" class="box" href="/ui/blood-matching"><strong>Blood Matching Center</strong><span>Request matching and donor-response operations.</span></a>
            <a class="box" href="/ui/dev-tools"><strong>Advanced Tools</strong><span>Controlled debug and API inspection panel.</span></a>
        </div>
    </section>

    <section class="card">
        <h2>Role dashboards</h2>
        <div class="grid" style="margin-top: 10px;">
            <a class="box" href="/ui/departments"><strong>Department Directory</strong><span>Patient-facing department pages with doctor availability and booking actions.</span></a>
            <a class="box" href="/ui/doctor-dashboard"><strong>Doctor Dashboard</strong><span>Clinical profile, appointments, and bed requests.</span></a>
            <a class="box" href="/ui/nurse-dashboard"><strong>Nurse Dashboard</strong><span>Patient monitoring and vital-sign logging.</span></a>
            <a class="box" href="/ui/patient-portal"><strong>Patient Portal</strong><span>Appointments, records, and blood requests.</span></a>
            <a class="box" href="/ui/donor-dashboard"><strong>Donor Dashboard</strong><span>Availability, request response, and donation history.</span></a>
        </div>
    </section>
</div>
<script>
const BLOOD_BANK_DEPARTMENT = 'Blood Bank';
const token = localStorage.getItem('USER_TOKEN') || '';
const roles = JSON.parse(localStorage.getItem('CURRENT_USER_ROLES') || '[]');
const bloodMatchingLink = document.getElementById('bloodMatchingLink');
const bloodBankSchemaLink = document.getElementById('bloodBankSchemaLink');

async function api(path) {
    const response = await fetch(`/api${path}`, {
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`
        }
    });

    const text = await response.text();
    let data = {};
    try { data = JSON.parse(text); } catch {}
    return { status: response.status, data };
}

async function hasBloodBankItAccess() {
    if (roles.includes('Admin')) {
        return true;
    }

    if (!roles.includes('ITWorker') || !token) {
        return false;
    }

    const result = await api('/ward/it/departments');
    if (result.status >= 300) {
        return false;
    }

    const departments = Array.isArray(result.data?.departments) ? result.data.departments : [];
    return departments.some(department => department?.dept_name === BLOOD_BANK_DEPARTMENT);
}

(async function applyPrototypeVisibility() {
    const canSeeBloodTools = await hasBloodBankItAccess();

    if (!roles.includes('Admin') && !canSeeBloodTools) {
        if (bloodMatchingLink) bloodMatchingLink.style.display = 'none';
        if (bloodBankSchemaLink) bloodBankSchemaLink.style.display = 'none';
    }
})();
</script>
</body>
</html>
