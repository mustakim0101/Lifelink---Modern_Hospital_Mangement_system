<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLink UI</title>
    <style>
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f4f7fb; color: #1f2937; }
        .wrap { max-width: 960px; margin: 40px auto; padding: 20px; }
        .card { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 24px; }
        h1 { margin-top: 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 20px; }
        a.box { display: block; text-decoration: none; color: #111827; background: #eef4ff; border: 1px solid #c8d8ff; padding: 16px; border-radius: 10px; }
        a.box:hover { background: #e4efff; }
        .small { color: #6b7280; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>LifeLink UI Starter</h1>
        <p class="small">Frontend pages for backend features completed up to Phase 5 Issue 15.</p>

        <div class="grid">
            <a class="box" href="/ui/auth">
                <strong>Auth Page</strong><br>
                Register, login, create admin, token preview.
            </a>
            <a class="box" href="/ui/applications">
                <strong>Applications Page</strong><br>
                Submit job applications and track status.
            </a>
            <a class="box" href="/ui/admin-users">
                <strong>Admin Account Control</strong><br>
                Freeze/unfreeze user and check status.
            </a>
            <a class="box" href="/ui/application-reviews">
                <strong>Application Reviews</strong><br>
                Admin/IT approve or reject job applications.
            </a>
            <a class="box" href="/ui/ward-setup">
                <strong>Ward Setup</strong><br>
                Create care units/beds and view bed summary.
            </a>
            <a class="box" href="/ui/it-bed-allocation">
                <strong>IT Bed Allocation</strong><br>
                Create admissions and assign available beds.
            </a>
            <a class="box" href="/ui/doctor-dashboard">
                <strong>Doctor Dashboard</strong><br>
                Manage doctor patients, appointments, and bed requests.
            </a>
            <a class="box" href="/ui/nurse-dashboard">
                <strong>Nurse Dashboard</strong><br>
                Monitor department patients, beds, and vital signs.
            </a>
            <a class="box" href="/ui/patient-portal">
                <strong>Patient Portal</strong><br>
                View records, manage appointments, and request blood.
            </a>
        </div>
    </div>
</div>
</body>
</html>
