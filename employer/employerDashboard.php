<?php
session_start();
include "../includes/config.php";  

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["role"] != "employer") {
    header("Location: ../login.php");
    exit();
}

$userid = $_SESSION['user_id'];

// ---------------- EMPLOYER INFO QUERY ----------------
// Logged in employer ki basic info (users table)
$employerQuery = "select fullname, email from users where id = $userid";
$employerResult = mysqli_query($conn, $employerQuery);
$employer = mysqli_fetch_assoc($employerResult);

// ---------------- STATS QUERIES ----------------

// Total jobs posted by this employer (jobs table)
$totalJobsQuery = "select count(*) as total from jobs where user_id = $userid";
$totalJobsResult = mysqli_query($conn, $totalJobsQuery);
$totalJobsRow = mysqli_fetch_assoc($totalJobsResult);
$totalJobs = $totalJobsRow['total'];

// Total open jobs posted by this employer (jobs table)
$openJobsQuery = "select count(*) as total from jobs where user_id = $userid and status = 'open'";
$openJobsResult = mysqli_query($conn, $openJobsQuery);
$openJobsRow = mysqli_fetch_assoc($openJobsResult);
$openJobs = $openJobsRow['total'];

// Total closed jobs posted by this employer (jobs table)
$closedJobsQuery = "select count(*) as total from jobs where user_id = $userid and status = 'closed'";
$closedJobsResult = mysqli_query($conn, $closedJobsQuery);
$closedJobsRow = mysqli_fetch_assoc($closedJobsResult);
$closedJobs = $closedJobsRow['total'];

// Total applications received on this employer's jobs (applications + jobs table)
$totalApplicationsQuery = "select count(*) as total
                            from applications
                            join jobs on applications.job_id = jobs.id
                            where jobs.user_id = $userid";
$totalApplicationsResult = mysqli_query($conn, $totalApplicationsQuery);
$totalApplicationsRow = mysqli_fetch_assoc($totalApplicationsResult);
$totalApplications = $totalApplicationsRow['total'];

// ---------------- RECENT JOBS QUERY ----------------
// Employer ki latest 5 jobs, sath me har job ki applications ki ginti bhi
$recentJobsQuery = "select jobs.id, jobs.title, jobs.company, jobs.location, jobs.status, jobs.created_at,
                    (select count(*) from applications where applications.job_id = jobs.id) as applicants_count
                    from jobs
                    where jobs.user_id = $userid
                    order by jobs.created_at desc
                    limit 5";
$recentJobsResult = mysqli_query($conn, $recentJobsQuery);

// ---------------- RECENT APPLICATIONS QUERY ----------------
// Employer ki jobs par aayi latest 5 applications, applicant ka naam aur job ka title sath
$recentApplicationsQuery = "select applications.id, applications.status, applications.applied_at,
                            jobs.title as job_title,
                            users.fullname as applicant_name, users.email as applicant_email
                            from applications
                            join jobs on applications.job_id = jobs.id
                            join users on applications.applicant_id = users.id
                            where jobs.user_id = $userid
                            order by applications.applied_at desc
                            limit 5";
$recentApplicationsResult = mysqli_query($conn, $recentApplicationsQuery);
?>

<style>
.employer-dashboard {
    background-color: var(--background-color);
    padding: 40px 20px;
    font-family: var(--default-font);
    color: var(--default-color);
}

.employer-dashboard .welcome-bar {
    margin-bottom: 30px;
}

.employer-dashboard .welcome-bar h1 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 28px;
    margin-bottom: 6px;
}

.employer-dashboard .welcome-bar p {
    font-size: 15px;
    color: var(--default-color);
}

.quick-actions {
    display: flex;
    gap: 12px;
    margin-top: 18px;
}

.btn-accent {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--accent-color);
    color: var(--contrast-color) !important;
    text-decoration: none;
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    transition: opacity 0.2s ease;
}

.btn-accent:hover { opacity: 0.88; }

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--surface-color);
    color: var(--accent-color) !important;
    text-decoration: none;
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    border: 1.5px solid var(--accent-color);
    transition: all 0.2s ease;
}

.btn-outline:hover {
    background: var(--accent-color);
    color: var(--contrast-color) !important;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background-color: var(--surface-color);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border-left: 5px solid var(--accent-color);
}

.stat-card .icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background-color: var(--accent-color);
    color: var(--contrast-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 14px;
}

.stat-card .number {
    font-family: var(--heading-font);
    font-size: 30px;
    font-weight: 700;
    color: var(--heading-color);
    line-height: 1;
}

.stat-card .label {
    margin-top: 6px;
    font-size: 14px;
    color: var(--default-color);
}

.dashboard-panels {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 24px;
}

.panel {
    background-color: var(--surface-color);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
}

.panel-head h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 18px;
}

.panel-head a {
    font-size: 13px;
    color: var(--accent-color);
    text-decoration: none;
    font-weight: 600;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    text-align: left;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--default-color);
    padding: 10px 8px;
    border-bottom: 2px solid rgba(0,0,0,0.06);
}

.data-table td {
    padding: 12px 8px;
    font-size: 14px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    vertical-align: middle;
}

.data-table tr:hover td {
    background-color: var(--background-color);
}

.job-title-cell strong {
    color: var(--heading-color);
    display: block;
    font-size: 14.5px;
}

.job-title-cell span {
    font-size: 12.5px;
    color: var(--default-color);
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.open { background: rgba(34,197,94,0.14); color: #15803d; }
.status-badge.closed { background: rgba(220,38,38,0.12); color: #dc2626; }
.status-badge.pending { background: rgba(234,179,8,0.14); color: #b45309; }
.status-badge.accepted { background: rgba(34,197,94,0.14); color: #15803d; }
.status-badge.rejected { background: rgba(220,38,38,0.12); color: #dc2626; }

.applicants-pill {
    background: rgba(14,165,233,0.12);
    color: var(--accent-color);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.no-data {
    text-align: center;
    padding: 30px;
    color: var(--default-color);
    font-size: 14px;
}

@media (max-width: 992px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .dashboard-panels { grid-template-columns: 1fr; }
}

@media (max-width: 576px) {
    .stats-grid { grid-template-columns: 1fr; }
    .employer-dashboard { padding: 24px 14px; }
    .quick-actions { flex-direction: column; }
}
</style>

<?php include "../includes/header.php";   ?>

<section class="employer-dashboard">

    <div class="welcome-bar">
        <h1>Welcome back, <?php echo $employer['fullname']; ?></h1>
        <p>Here is an overview of your job postings and applications</p>

        <div class="quick-actions">
            <a href="add-manage-jobs.php?tab=add-job" class="btn-accent"><i class="bi bi-plus-circle"></i> Post a New Job</a>
            <a href="add-manage-jobs.php?tab=manage-jobs" class="btn-outline"><i class="bi bi-briefcase"></i> Manage Jobs</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="number"><?php echo $totalJobs; ?></div>
            <div class="label">Total Jobs Posted</div>
        </div>

        <div class="stat-card">
            <div class="icon"><i class="bi bi-unlock-fill"></i></div>
            <div class="number"><?php echo $openJobs; ?></div>
            <div class="label">Open Jobs</div>
        </div>

        <div class="stat-card">
            <div class="icon"><i class="bi bi-lock-fill"></i></div>
            <div class="number"><?php echo $closedJobs; ?></div>
            <div class="label">Closed Jobs</div>
        </div>

        <div class="stat-card">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div class="number"><?php echo $totalApplications; ?></div>
            <div class="label">Total Applications</div>
        </div>
    </div>

    <div class="dashboard-panels">
        <div class="panel">
            <div class="panel-head">
                <h2>Your Recent Jobs</h2>
                <a href="add-manage-jobs.php">View All</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Status</th>
                        <th>Applicants</th>
                        <th>Posted On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($recentJobsResult && mysqli_num_rows($recentJobsResult) > 0) {
                        while ($job = mysqli_fetch_assoc($recentJobsResult)) {
                    ?>
                        <tr>
                            <td class="job-title-cell">
                                <strong><?php echo $job['title']; ?></strong>
                                <span><?php echo $job['company'] . ' - ' . $job['location']; ?></span>
                            </td>
                            <td><span class="status-badge <?php echo $job['status']; ?>"><?php echo $job['status']; ?></span></td>
                            <td><span class="applicants-pill"><?php echo $job['applicants_count']; ?></span></td>
                            <td><?php echo date('d M Y', strtotime($job['created_at'])); ?></td>
                        </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr><td colspan="4" class="no-data">You haven't posted any jobs yet.</td></tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Recent Applications</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Job</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($recentApplicationsResult && mysqli_num_rows($recentApplicationsResult) > 0) {
                        while ($app = mysqli_fetch_assoc($recentApplicationsResult)) {
                    ?>
                        <tr>
                            <td class="job-title-cell">
                                <strong><?php echo $app['applicant_name']; ?></strong>
                                <span><?php echo $app['applicant_email']; ?></span>
                            </td>
                            <td><?php echo $app['job_title']; ?></td>
                            <td><span class="status-badge <?php echo $app['status']; ?>"><?php echo $app['status']; ?></span></td>
                        </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr><td colspan="3" class="no-data">No applications received yet.</td></tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</section>

<?php include "../includes/footer.php"; ?>


