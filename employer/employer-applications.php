<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION["user_id"])) {
  header("Location: ../login.php");
  exit();
  };
  if ($_SESSION["role"] != "employer" && $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
      exit();
    };
  include "../includes/header.php";

$userid = $_SESSION["user_id"];
$query = "select applications.*,jobs.title,jobs.company,jobs.salary,users.fullname,users.skills,users.experience,users.cv_file from applications inner join jobs on  applications.job_id = jobs.id inner join users on applications.applicant_id = users.id where jobs.user_id = $userid and visibility = 1 and jobs.status = 'open'"; ;  
$result = mysqli_query($conn, $query);  
if(mysqli_num_rows($result) >= 1){
  $rows = mysqli_fetch_all($result,MYSQLI_ASSOC);
  }
  if(isset($_POST["visibility"])){
    $app_id = $_POST["application_id"]; ;
    $hideapplicationquery = "update applications set visibility = 0 where id = $app_id";
    $hideapplicationqueryresult = mysqli_query($conn, $hideapplicationquery);
    if($hideapplicationqueryresult){
header("Location: ./employer-applications.php");
    }
  };

if(isset($_POST["status-reject"])){
  $id = $_POST["id"];
  $query = "update applications set status = 'rejected' where id = $id";
  $result = mysqli_query($conn, $query);
  if(mysqli_affected_rows($conn) >= 1){
 header("Location: ./employer-applications.php");
 exit();
 }else{
   echo "Job rejecting failed".mysqli_error($conn);
   }
   }

   if(isset($_POST["status-accept"])){
     $id = $_POST["id"];
     $query = "update applications set status = 'accepted' where id = $id";
     $result = mysqli_query($conn, $query);
     if(mysqli_affected_rows($conn) >= 1){
       header("Location: ./employer-applications.php");
       exit();
  }else{
    echo "Job accepting failed".mysqli_error($conn);
  }
}









?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Applications</title>
<style>
  :root {
    --default-font: "Roboto", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    --heading-font: "Raleway", sans-serif;
    --nav-font: "Lato", sans-serif;
  }

  :root {
    --background-color: #f1f5f9;
    --default-color: #475569;
    --heading-color: #1e293b;
    --accent-color: #0ea5e9;
    --surface-color: #ffffff;
    --contrast-color: #ffffff;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background-color: var(--background-color);
    font-family: var(--default-font);
    color: var(--default-color);
  }

  /* ===== Applications Section ===== */
  .applications-section {
    padding: 70px 20px;
  }

  .section-header {
    max-width: 1200px;
    margin: 0 auto 40px;
    text-align: center;
  }

  .section-header span.badge {
    display: inline-block;
    background-color: color-mix(in srgb, var(--accent-color) 12%, var(--surface-color));
    color: var(--accent-color);
    font-family: var(--nav-font);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 20px;
    margin-bottom: 14px;
  }

  .section-header h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 34px;
    font-weight: 800;
    margin-bottom: 12px;
  }

  .section-header p {
    max-width: 640px;
    margin: 0 auto;
    font-size: 15px;
    line-height: 1.7;
    color: var(--default-color);
  }

  .section-header p strong { color: var(--heading-color); }

  /* ===== Card Wrapper ===== */
  .applications-card {
    max-width: 1200px;
    margin: 0 auto;
    background: var(--surface-color);
    border-radius: 16px;
    box-shadow: 0 6px 30px rgba(30, 41, 59, 0.08);
    border: 1px solid rgba(30, 41, 59, 0.06);
    overflow: hidden;
  }

  .applications-table-wrapper { overflow-x: auto; }

  table.applications-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
  }

  .applications-table thead th {
    text-align: left;
    font-family: var(--heading-font);
    font-size: 12px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--heading-color);
    background-color: color-mix(in srgb, var(--accent-color) 8%, var(--surface-color));
    padding: 18px 20px;
    border-bottom: 2px solid color-mix(in srgb, var(--accent-color) 25%, transparent);
    white-space: nowrap;
  }

  .applications-table tbody td {
    padding: 18px 20px;
    font-size: 14px;
    color: var(--default-color);
    border-bottom: 1px solid color-mix(in srgb, var(--heading-color) 8%, transparent);
    vertical-align: middle;
  }

  .applications-table tbody tr:last-child td { border-bottom: none; }
  .applications-table tbody tr:hover { background-color: color-mix(in srgb, var(--accent-color) 4%, transparent); }

  .applicant-cell { display: flex; align-items: center; gap: 12px; }
  .applicant-avatar {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 50%;
    background-color: var(--accent-color);
    color: var(--contrast-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--heading-font);
    font-weight: 700;
    font-size: 14px;
  }
  .applicant-name { font-weight: 600; color: var(--heading-color); }

  .job-title { font-weight: 600; color: var(--heading-color); }
  .job-title small {
    display: block;
    font-weight: 400;
    color: var(--default-color);
    font-size: 12px;
    margin-top: 2px;
  }

  .skills-tags { display: flex; flex-wrap: wrap; gap: 6px; }
  .skill-tag {
    background-color: color-mix(in srgb, var(--accent-color) 12%, var(--surface-color));
    color: var(--accent-color);
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
  }

  .cv-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--accent-color);
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
  }
  .cv-link:hover { text-decoration: underline; }

  .status-pill {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.02em;
  }
  .status-pending {
    color: var(--accent-color);
    background-color: color-mix(in srgb, var(--accent-color) 14%, var(--surface-color));
  }
  .status-accepted {
    color: var(--contrast-color);
    background-color: var(--accent-color);
  }
  .status-rejected {
    color: var(--heading-color);
    background-color: color-mix(in srgb, var(--heading-color) 10%, var(--surface-color));
  }

  .actions { display: flex; flex-wrap: wrap; gap: 8px; }
  .actions button {
    border: 1px solid transparent;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    font-family: var(--default-font);
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .btn-accept { background-color: var(--accent-color); color: var(--contrast-color); }
  .btn-accept:hover { background-color: color-mix(in srgb, var(--accent-color) 85%, black); }

  .btn-reject {
    background-color: transparent;
    color: var(--heading-color);
    border-color: color-mix(in srgb, var(--heading-color) 25%, transparent);
  }
  .btn-reject:hover { background-color: color-mix(in srgb, var(--heading-color) 8%, transparent); }

  .btn-view, .btn-download {
    background-color: transparent;
    color: var(--accent-color);
    border-color: color-mix(in srgb, var(--accent-color) 40%, transparent);
  }
  .btn-view:hover, .btn-download:hover {
    background-color: color-mix(in srgb, var(--accent-color) 10%, transparent);
  }

  /* ===== Responsive: Tablet ===== */
  @media (max-width: 992px) {
    .section-header h2 { font-size: 28px; }
  }

  /* ===== Responsive: Mobile — table becomes stacked cards ===== */
  @media (max-width: 768px) {
    .applications-section { padding: 50px 14px; }

    .applications-card { border-radius: 14px; }

    .applications-table-wrapper { overflow-x: visible; }

    table.applications-table { min-width: 0; }

    .applications-table thead { display: none; }

    .applications-table, .applications-table tbody, .applications-table tr, .applications-table td {
      display: block;
      width: 100%;
    }

    .applications-table tr {
      padding: 18px 18px 14px;
      border-bottom: 8px solid var(--background-color);
    }

    .applications-table tr:last-child { border-bottom: none; }

    .applications-table td {
      border: none;
      padding: 10px 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .applications-table td::before {
      content: attr(data-label);
      font-family: var(--heading-font);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--heading-color);
      opacity: 0.55;
      flex-shrink: 0;
      width: 90px;
    }

    .applicant-cell { justify-content: flex-end; }
    .skills-tags { justify-content: flex-end; }
    .actions { justify-content: flex-end; }
    .actions button { flex: 1 1 auto; text-align: center; }
  }

  @media (max-width: 420px) {
    .section-header h2 { font-size: 24px; }
    .applications-table td { flex-direction: column; align-items: flex-start; gap: 6px; }
    .applications-table td::before { width: auto; }
    .applicant-cell, .skills-tags, .actions { justify-content: flex-start; width: 100%; }
    .actions { flex-direction: column; }
  }
</style>
</head>
<body>

<section class="applications-section">
  <div class="section-header">
    <span class="badge">Employer Dashboard</span>
    <h2>Applications</h2>
    <p>
      Here you can see all the <strong>applicants</strong> who have applied for your job postings.
      Review each candidate's skills, experience, and CV, then <strong>Accept</strong> or
      <strong>Reject</strong> their application, view their full profile, or download their CV —
      all from one place.
    </p>
  </div>

  <div class="applications-card">
    <div class="applications-table-wrapper">
      <table class="applications-table">
        <thead>
          <tr>
            <th>Job Title</th>
            <th>Applicant</th>
            <th>Skills</th>
            <th>Experience</th>
            <th>CV</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($rows)){
            foreach($rows as $row) {
              ?>
          <tr>
            <td data-label="Job Title">
              <span class="job-title"><?php echo $row['title'] ?><small>Full-time · Karachi</small></span>
            </td>
            <td data-label="Applicant">
              <div class="applicant-cell">
                <div class="applicant-avatar">AR</div>
                <span class="applicant-name"><?php echo $row['fullname'] ?></span>
              </div>
            </td>
            <td data-label="Skills">
              <div class="skills-tags">
                <span class="skill-tag"><?php echo $row['skills'] ?></span>
              </div>
            </td>
            <td data-label="Experience"><?php echo $row['experience'] ?></td>
            <td data-label="CV"><a href="../uploads/cv/<?php echo $row['cv_file'] ?>" class="cv-link">View CV</a></td>
            <td data-label="Status">
              <?php
              if($row['status'] == "accepted"){ ?>
<span class="status-pill status-pending"><?php echo $row['status'] ?></span>
<?php } ?>
<?php  
              if($row['status'] == "rejected"){ ?>
<span class="status-pill bg-danger text-white status-pending"><?php echo $row['status'] ?></span>
<?php } ?>
              
            </td>
            <td data-label="Actions">
              <div class="actions d-flex">
                <form method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                <button type="submit" class="btn-accept" name="status-accept">Accept</button>
                <button type="submit" class="btn-reject" name="status-reject">Reject</button>
              </form>
                <button class="btn-view">View Profile</button>
                <button class="btn-download">Download</button>
                <form method="POST">
                  <input type="hidden" name="application_id" id="" value="<?php echo $row["id"] ?>">
                  <button type="submit" class="bg-danger text-white" name="visibility">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          <?php }} ?>

        </tbody>
      </table>
    </div>
  </div>
</section>

</body>
</html>

<?php
include "../includes/footer.php";
?>