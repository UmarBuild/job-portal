<?php
session_start();

include("../includes/config.php");

if (
  !isset($_SESSION['user_id'])
  ||
  $_SESSION['role'] != 'admin'
) {
  header("Location: ../login.php");
  exit();
}


// Total Applicants
$applicantquery = "SELECT COUNT(*) AS total FROM users WHERE role='applicant'";
$applicantqueryresult = mysqli_query($conn, $applicantquery);
$applicantdata = mysqli_fetch_assoc($applicantqueryresult);
$totalApplicants = $applicantdata['total'];


// Total Employers
$employerquery = "SELECT COUNT(*) AS total FROM users WHERE role='employer'";
$employerqueryresult = mysqli_query($conn, $employerquery);
$employerdata = mysqli_fetch_assoc($employerqueryresult);
$totalEmployers = $employerdata['total'];


// Total Jobs
$jobsquery = "SELECT COUNT(*) AS total FROM jobs";
$jobsqueryresult = mysqli_query($conn, $jobsquery);
$jobsdata = mysqli_fetch_assoc($jobsqueryresult);
$totalJobs = $jobsdata['total'];


// Total Categories
$categoriesquery = "SELECT COUNT(*) AS total FROM categories";
$categoriesqueryresult = mysqli_query($conn, $categoriesquery);
$categoriesdata = mysqli_fetch_assoc($categoriesqueryresult);
$totalCategories = $categoriesdata['total'];


// Total Applications
$applicationsquery = "SELECT COUNT(*) AS total FROM applications";
$applicationsqueryresult = mysqli_query($conn, $applicationsquery);
$applicationsdata = mysqli_fetch_assoc($applicationsqueryresult);
$totalApplications = $applicationsdata['total'];


// Pending Applications
$pendingquery = "SELECT COUNT(*) AS total FROM applications WHERE status='pending'";
$pendingqueryresult = mysqli_query($conn, $pendingquery);
$pendingdata = mysqli_fetch_assoc($pendingqueryresult);
$pendingApplications = $pendingdata['total'];


// Recent Jobs
$recentjobsquery = "SELECT jobs.*, users.fullname, categories.name AS category_name
FROM jobs
INNER JOIN users ON jobs.user_id = users.id
INNER JOIN categories ON jobs.category_id = categories.id
ORDER BY jobs.id DESC
LIMIT 5";

$recentjobsqueryresult = mysqli_query($conn, $recentjobsquery);


// Recent Users
$recentusersquery = "SELECT * FROM users ORDER BY id DESC LIMIT 5";
$recentusersqueryresult = mysqli_query($conn, $recentusersquery);

?>


<style>
  .admin-dashboard {
    background-color: var(--background-color);
    padding: 40px 20px;
    font-family: var(--default-font);
    color: var(--default-color);
  }

  .admin-dashboard h1 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 28px;
    margin-bottom: 6px;
  }

  .admin-dashboard .subtitle {
    color: var(--default-color);
    margin-bottom: 30px;
    font-size: 15px;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 40px;
  }

  .stat-card {
    background-color: var(--surface-color);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border-left: 5px solid var(--accent-color);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
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
    grid-template-columns: 1.4fr 1fr;
    gap: 24px;
  }

  .panel {
    background-color: var(--surface-color);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  }

  .panel h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 18px;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
  }

  .data-table th {
    text-align: left;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--default-color);
    padding: 10px 8px;
    border-bottom: 2px solid rgba(0, 0, 0, 0.06);
  }

  .data-table td {
    padding: 12px 8px;
    font-size: 14px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    color: var(--default-color);
  }

  .data-table tr:hover td {
    background-color: var(--background-color);
  }

  .badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
  }

  .badge.applicant {
    background: rgba(14, 165, 233, 0.12);
    color: var(--accent-color);
  }

  .badge.employer {
    background: rgba(30, 41, 59, 0.10);
    color: var(--heading-color);
  }

  .badge.admin {
    background: rgba(220, 38, 38, 0.10);
    color: #dc2626;
  }

  .job-title-cell strong {
    color: var(--heading-color);
    display: block;
  }

  .job-title-cell span {
    font-size: 12.5px;
    color: var(--default-color);
  }

  @media (max-width: 992px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .dashboard-panels {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 576px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    .admin-dashboard {
      padding: 24px 14px;
    }
  }
</style>
<?php include('../includes/header.php'); ?>
<section class="admin-dashboard">

  <h1>Admin Dashboard</h1>

  <p class="subtitle">
    Job portal ka overall summary aur latest activity
  </p>

  <div class="stats-grid">

    <div class="stat-card">
      <div class="icon">
        <i class="bi bi-people-fill"></i>
      </div>

      <div class="number">
        <?php echo $totalApplicants; ?>
      </div>

      <div class="label">
        Total Applicants
      </div>
    </div>

    <div class="stat-card">

      <div class="icon">
        <i class="bi bi-briefcase-fill"></i>
      </div>

      <div class="number">
        <?php echo $totalEmployers; ?>
      </div>

      <div class="label">
        Total Employers
      </div>

    </div>

    <div class="stat-card">

      <div class="icon">
        <i class="bi bi-file-earmark-text-fill"></i>
      </div>

      <div class="number">
        <?php echo $totalJobs; ?>
      </div>

      <div class="label">
        Total Jobs Posted
      </div>

    </div>

    <div class="stat-card">

      <div class="icon">
        <i class="bi bi-send-check-fill"></i>
      </div>

      <div class="number">
        <?php echo $totalApplications; ?>
      </div>

      <div class="label">
        Total Applications
      </div>

    </div>

  </div>


  <div class="dashboard-panels">

    <div class="panel">

      <h2>Recently Posted Jobs</h2>

      <table class="data-table">

        <thead>

          <tr>
            <th>Job</th>
            <th>Category</th>
            <th>Posted By</th>
            <th>Date</th>
          </tr>

        </thead>

        <tbody>

          <?php

          if (mysqli_num_rows($recentjobsqueryresult) > 0) {

            while ($job = mysqli_fetch_assoc($recentjobsqueryresult)) {

          ?>

              <tr>

                <td class="job-title-cell">

                  <strong>
                    <?php echo $job['title']; ?>
                  </strong>

                  <span>
                    <?php echo $job['company']; ?>
                    -
                    <?php echo $job['location']; ?>
                  </span>

                </td>

                <td>
                  <?php echo $job['category_name']; ?>
                </td>

                <td>
                  <?php echo $job['fullname']; ?>
                </td>

                <td>
                  <?php echo date("d M Y", strtotime($job['created_at'])); ?>
                </td>

              </tr>

            <?php

            }
          } else {

            ?>

            <tr>

              <td colspan="4">
                Not Found Any Job
              </td>

            </tr>

          <?php } ?>

        </tbody>

      </table>

    </div>



    <div class="panel">

      <h2>Newly Registered Users</h2>

      <table class="data-table">

        <thead>

          <tr>
            <th>Name</th>
            <th>Role</th>
          </tr>

        </thead>

        <tbody>

          <?php

          if (mysqli_num_rows($recentusersqueryresult) > 0) {

            while ($user = mysqli_fetch_assoc($recentusersqueryresult)) {

          ?>

              <tr>

                <td class="job-title-cell">

                  <strong>
                    <?php echo $user['fullname']; ?>
                  </strong>

                  <span>
                    <?php echo $user['email']; ?>
                  </span>

                </td>

                <td>

                  <span class="badge <?php echo $user['role']; ?>">

                    <?php echo $user['role']; ?>

                  </span>

                </td>

              </tr>

            <?php

            }
          } else {

            ?>

            <tr>

              <td colspan="2">
                Koi user nahi mila
              </td>

            </tr>

          <?php } ?>

        </tbody>

      </table>

    </div>

  </div>



  <div class="dashboard-panels" style="margin-top:24px; grid-template-columns:1fr 1fr;">

    <div class="panel">

      <h2>Categories Overview</h2>

      <div class="number" style="font-size:26px;">

        <?php echo $totalCategories; ?>

        <span style="font-size:14px; color:var(--default-color); font-weight:400;">

          total categories

        </span>

      </div>

    </div>


    <div class="panel">

      <h2>Pending Applications</h2>

      <div class="number" style="font-size:26px;">

        <?php echo $pendingApplications; ?>

        <span style="font-size:14px; color:var(--default-color); font-weight:400;">

          awaiting review

        </span>

      </div>

    </div>

  </div>

</section>
<?php include('../includes/footer.php'); ?>