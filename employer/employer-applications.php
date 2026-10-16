<?php
session_start();

function calculateSkillMatch($applicantSkills, $jobSkills)
{
  if (empty($applicantSkills) || empty($jobSkills)) {
    return null;
  }

  $applicantArr = array_map('trim', array_map('strtolower', explode(',', $applicantSkills)));
  $jobArr = array_map('trim', array_map('strtolower', explode(',', $jobSkills)));
  $jobArr = array_filter($jobArr);

  if (count($jobArr) == 0) {
    return null;
  }


  $matched = array_intersect($jobArr, $applicantArr);
  return round((count($matched) / count($jobArr)) * 100);
}

include "../includes/config.php";
require_once "../includes/notify.php";
require_once "../includes/csrf.php";
require_once "../mail/mail.php";

if (!isset($_SESSION["user_id"])) {
  header("Location: ../login.php");
  exit();
};
if ($_SESSION["role"] != "employer" && $_SESSION["role"] != "admin") {
  header("Location: ../login.php");
  exit();
};

$userid = $_SESSION["user_id"];

// ---- Security helpers ----
function getOwnedApplication($conn, $appId, $userid, $role)
{
  // Employer sirf apni jobs ki applications dekh sakta hai, admin sab
  $sql = "select applications.id, applications.status, applications.applicant_id, users.email, users.fullname, jobs.title
          from applications
          inner join users on applications.applicant_id = users.id
          inner join jobs on applications.job_id = jobs.id
          where applications.id = ?";
  $params = [$appId];
  if ($role != "admin") {
    $sql .= " and jobs.user_id = ?";
    $params[] = $userid;
  }
  $res = mysqli_execute_query($conn, $sql, $params);
  return $res ? mysqli_fetch_assoc($res) : null;
}

function notifyApplicant($conn, $info, $notifMsg, $mailSubject, $mailBody)
{
  sendNotification($conn, $info['applicant_id'], 'application_status', $notifMsg, BASE_PATH . "/applicant/applied-jobs.php");
  sendMail($info['email'], $mailSubject, $mailBody);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die("Invalid request (CSRF). Page refresh karke dobara try karein.");
  }

  $role = $_SESSION["role"];
  $id = (int)($_POST["id"] ?? $_POST["application_id"] ?? 0);
  $info = getOwnedApplication($conn, $id, $userid, $role);

  if (!$info) {
    die("Application not found or you don't have permission to access it.");
  }

  $safeName = htmlspecialchars($info['fullname']);
  $safeTitle = htmlspecialchars($info['title']);
  $current = $info['status'];

  if (isset($_POST["visibility"])) {
    mysqli_execute_query($conn, "update applications set visibility = 0 where id = ?", [$id]);
  } elseif (isset($_POST["status-accept"])) {
    if ($current == 'pending') {
      mysqli_execute_query($conn, "update applications set status = 'accepted' where id = ?", [$id]);
      notifyApplicant(
        $conn,
        $info,
        "Great news! Your application for \"{$info['title']}\" was accepted.",
        "Application Accepted",
        "<p>Hi {$safeName}, congratulations! Your application for <b>{$safeTitle}</b> was accepted. The employer will contact you soon regarding next steps.</p>"
      );
    }
  } elseif (isset($_POST["status-reject"])) {
    if ($current == 'pending' || $current == 'accepted') {
      mysqli_execute_query($conn, "update applications set status = 'rejected' where id = ?", [$id]);
      notifyApplicant(
        $conn,
        $info,
        "Your application for \"{$info['title']}\" was not selected this time.",
        "Application Update",
        "<p>Hi {$safeName}, unfortunately your application for <b>{$safeTitle}</b> was not selected this time.</p>"
      );
    }
  } elseif (isset($_POST["status-hire"])) {
    // Sirf accepted applicant (jiska interview ho chuka ho) hire ho sakta hai
    if ($current == 'accepted') {
      mysqli_execute_query($conn, "update applications set status = 'hired' where id = ?", [$id]);
      mysqli_execute_query($conn, "update interviews set status = 'completed' where application_id = ?", [$id]);
      notifyApplicant(
        $conn,
        $info,
        "🎉 Congratulations! You've been hired for \"{$info['title']}\"",
        "You're Hired! 🎉",
        "<p>Hi {$safeName}, congratulations! After the interview, you have been selected for <b>{$safeTitle}</b>. The employer will reach out with next steps.</p>"
      );
    }
  } elseif (isset($_POST["status-not-selected"])) {
    if ($current == 'accepted') {
      mysqli_execute_query($conn, "update applications set status = 'rejected' where id = ?", [$id]);
      mysqli_execute_query($conn, "update interviews set status = 'completed' where application_id = ?", [$id]);
      notifyApplicant(
        $conn,
        $info,
        "Update on your application for \"{$info['title']}\"",
        "Application Update",
        "<p>Hi {$safeName}, thank you for interviewing for <b>{$safeTitle}</b>. After careful consideration, we've decided to move forward with another candidate. We appreciate your time and wish you the best.</p>"
      );
    }
  }

  header("Location: ./employer-applications.php");
  exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
  $page = 1;
}
$perPage = 6;
$offset = ($page - 1) * $perPage;

$countQuery = "select count(*) as total
               from applications
               inner join jobs on applications.job_id = jobs.id
               where jobs.user_id = ? and applications.visibility = 1";
$countResult = mysqli_execute_query($conn, $countQuery, [$userid]);
$totalRow = mysqli_fetch_assoc($countResult);
$totalApplications = (int)$totalRow['total'];
$totalPages = ceil($totalApplications / $perPage);

$query = "select applications.*, jobs.title, jobs.location, jobs.job_type, jobs.required_skills, jobs.status as job_status,users.profile_image,users.fullname, users.skills, users.experience, users.cv_file,interviews.scheduled_at as interview_at, interviews.interview_type, interviews.location_or_link as interview_location, interviews.status as interview_status,interviews.reschedule_reason from applications inner join jobs on applications.job_id = jobs.id inner join users on applications.applicant_id = users.id left join interviews on interviews.application_id = applications.id where jobs.user_id = ? and applications.visibility = 1 order by applications.applied_at desc limit ? offset ?";

$result = mysqli_execute_query($conn, $query, [$userid, $perPage, $offset]);

$rows = [];
if ($result && mysqli_num_rows($result) >= 1) {
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

include "../includes/header.php";
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

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: var(--background-color);
      font-family: var(--default-font);
      color: var(--default-color);
    }

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

    .section-header p strong {
      color: var(--heading-color);
    }

    .results-count {
      max-width: 1200px;
      margin: 0 auto 20px;
      font-size: 13.5px;
      color: var(--default-color);
    }

    .results-count strong {
      color: var(--heading-color);
    }

    .applications-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
    }

    .app-card {
      background: var(--surface-color);
      border-radius: 16px;
      padding: 22px;
      box-shadow: 0 4px 20px rgba(30, 41, 59, 0.06);
      border: 1px solid rgba(30, 41, 59, 0.06);
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .app-card-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
    }

    .applicant-cell {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }

    .applicant-avatar {
      width: 42px;
      height: 42px;
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

    .applicant-name {
      font-weight: 700;
      color: var(--heading-color);
      font-size: 14.5px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .status-pill {
      display: inline-block;
      padding: 5px 13px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      letter-spacing: 0.02em;
      text-transform: capitalize;
      flex-shrink: 0;
    }

    .status-pending {
      color: #b45309;
      background-color: rgba(234, 179, 8, 0.14);
    }

    .status-accepted {
      color: var(--contrast-color);
      background-color: var(--accent-color);
    }

    .status-rejected {
      color: #dc2626;
      background-color: rgba(220, 38, 38, 0.12);
    }

    .app-job-info {
      border-top: 1px dashed rgba(30, 41, 59, 0.1);
      border-bottom: 1px dashed rgba(30, 41, 59, 0.1);
      padding: 12px 0;
    }

    .job-title {
      font-weight: 700;
      color: var(--heading-color);
      font-size: 14.5px;
      margin-bottom: 6px;
      display: block;
    }

    .job-meta {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .job-meta small {
      font-size: 12px;
      color: var(--default-color);
    }

    .job-status-badge {
      display: inline-block;
      padding: 2px 9px;
      border-radius: 20px;
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .job-status-badge.job-open {
      color: #15803d;
      background-color: rgba(34, 197, 94, 0.14);
    }

    .job-status-badge.job-closed {
      color: #b91c1c;
      background-color: rgba(220, 38, 38, 0.12);
    }

    .app-detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
    }

    .app-detail-row .lbl {
      color: var(--default-color);
    }

    .app-detail-row .val {
      color: var(--heading-color);
      font-weight: 600;
    }

    .skills-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .skill-tag {
      background-color: color-mix(in srgb, var(--accent-color) 12%, var(--surface-color));
      color: var(--accent-color);
      font-size: 12px;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 20px;
    }

    .cv-row {
      display: flex;
      gap: 16px;
    }

    .cv-link,
    .cv-download {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      color: var(--accent-color);
      font-weight: 600;
      font-size: 13px;
      text-decoration: none;
    }

    .cv-link:hover,
    .cv-download:hover {
      text-decoration: underline;
    }

    .app-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 4px;
    }

    .app-actions form {
      display: flex;
      gap: 8px;
      flex: 1;
    }

    .app-actions button {
      flex: 1;
      border: 1px solid transparent;
      padding: 9px 14px;
      border-radius: 8px;
      font-size: 12.5px;
      font-weight: 600;
      font-family: var(--default-font);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-accept {
      background-color: var(--accent-color);
      color: var(--contrast-color);
    }

    .btn-accept:hover {
      background-color: color-mix(in srgb, var(--accent-color) 85%, black);
    }

    .btn-reject {
      background-color: transparent;
      color: var(--heading-color);
      border-color: color-mix(in srgb, var(--heading-color) 25%, transparent);
    }

    .btn-reject:hover {
      background-color: color-mix(in srgb, var(--heading-color) 8%, transparent);
    }

    .btn-hide {
      background-color: transparent;
      color: #dc2626;
      border-color: rgba(220, 38, 38, 0.3);
    }

    .btn-hide:hover {
      background-color: rgba(220, 38, 38, 0.08);
    }

    .no-data {
      grid-column: 1 / -1;
      text-align: center;
      padding: 60px 20px;
      color: var(--default-color);
      font-size: 14.5px;
      background: var(--surface-color);
      border-radius: 16px;
    }

    .load-more-wrap {
      text-align: center;
      margin-top: 40px;
    }

    .btn-load-more {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 2px solid var(--accent-color);
      color: var(--accent-color);
      background: transparent;
      padding: 12px 30px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 700;
      font-family: var(--nav-font);
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s;
    }

    .btn-load-more:hover {
      background: var(--accent-color);
      color: #fff;
    }

    @media (max-width: 576px) {
      .applications-section {
        padding: 40px 14px;
      }

      .applications-grid {
        grid-template-columns: 1fr;
      }
    }

    .skill-match-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 999px;
      align-self: flex-start;
    }

    .skill-match-badge.high {
      background: rgba(34, 197, 94, 0.14);
      color: #166534;
    }

    .skill-match-badge.medium {
      background: rgba(234, 179, 8, 0.16);
      color: #854d0e;
    }

    .skill-match-badge.low {
      background: rgba(220, 38, 38, 0.12);
      color: #991b1b;
    }

    .interview-block {
      display: flex;
      flex-direction: column;
      gap: 8px;
      width: 100%;
    }

    .interview-info-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12.5px;
      color: #15803d;
      background: rgba(34, 197, 94, 0.1);
      border: 1px solid rgba(34, 197, 94, 0.25);
      padding: 8px 12px;
      border-radius: 8px;
      line-height: 1.4;
    }

    .btn-schedule-interview {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: linear-gradient(135deg, #0ea5e9, #0284c7);
      color: #fff;
      padding: 9px 16px;
      border-radius: 9px;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      width: 100%;
      box-sizing: border-box;
      transition: all 0.3s ease;
    }

    .btn-schedule-interview:hover {
      color: white;
      background: linear-gradient(135deg, #0284c7, #0ea5e9);
      transition: all 0.3s ease;
    }

    .btn-reschedule {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12.5px;
      color: #0ea5e9;
      text-decoration: none;
      font-weight: 600;
      align-self: flex-start;
    }

    .btn-reschedule:hover {
      text-decoration: underline;
    }
     .snapshot-avatar, .snapshot-avatar-fallback {
      width: 50px; height: 50px; border-radius: 50%; flex-shrink: 0;
      object-fit: cover;
      background: var(--accent-color-soft); color: var(--accent-color-dark);
      display: flex; align-items: center; justify-content: center;
      font-family: var(--heading-font); font-weight: 700; font-size: 26px;
    }
  </style>
</head>

<body>

  <section class="applications-section" id="applications-section">
    <div class="section-header">
      <span class="badge">Employer Dashboard</span>
      <h2>Applications</h2>
      <p>
        Here you can see all the <strong>applicants</strong> who have applied for your job postings —
        including jobs you've since closed. Review each candidate, then <strong>Accept</strong> or
        <strong>Reject</strong> their application, or download their CV.
      </p>
    </div>

    <?php if ($totalApplications > 0) { ?>
      <div class="results-count">
        Page <strong><?php echo $page; ?></strong> of <strong><?php echo $totalPages; ?></strong>
        (<?php echo $totalApplications; ?> total applications)
      </div>
    <?php } ?>

    <div class="applications-grid">
      <?php if (!empty($rows)) { ?>
        <?php foreach ($rows as $row) { ?>
          <div class="app-card">

            <div class="app-card-top">
              <div class="applicant-cell">
                <div class="applicant-avatar">
               <?php if (!empty($row['profile_image'])) { ?>
 <img class="snapshot-avatar" src="../uploads/profile/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="Profile photo">
<?php }else{ echo strtoupper(substr($row['fullname'], 0, 2));} ?>
                </div>
                <span class="applicant-name"><?php echo htmlspecialchars($row['fullname'] ?? ""); ?></span>
              </div>
              <?php if ($row['status'] == 'pending') { ?>
                <span class="status-pill status-pending">Pending</span>
              <?php } elseif ($row['status'] == 'accepted') { ?>
                <span class="status-pill status-accepted">Accepted</span>
              <?php } elseif ($row['status'] == 'rejected') { ?>
                <span class="status-pill status-rejected">Rejected</span>
              <?php } elseif ($row['status'] == 'hired') { ?>
                <span class="status-pill status-hired">Hired</span>
              <?php } ?>
            </div>

            <div class="app-job-info">
              <span class="job-title"><?php echo htmlspecialchars($row['title'] ?? ""); ?></span>
              <div class="job-meta">
                <small><?php echo htmlspecialchars($row['job_type']); ?> · <?php echo htmlspecialchars($row['location'] ?? ""); ?></small>
                <span class="job-status-badge <?php echo $row['job_status'] == 'open' ? 'job-open' : 'job-closed'; ?>">
                  <?php echo htmlspecialchars($row['job_status'] ?? ""); ?>
                </span>
              </div>
            </div>
            <?php $matchPercent = calculateSkillMatch($row['skills'], $row['required_skills']); ?>
            <?php if ($matchPercent !== null) { ?>
              <div class="skill-match-badge <?php echo $matchPercent >= 70 ? 'high' : ($matchPercent >= 40 ? 'medium' : 'low'); ?>">
                <i class="bi bi-graph-up-arrow"></i> <?php echo $matchPercent; ?>% Skill Match
              </div>
            <?php } ?>

            <div class="app-detail-row">
              <span class="lbl">Experience</span>
              <span class="val"><?php echo htmlspecialchars($row['experience'] ?? "NotSpecified"); ?></span>
            </div>

            <div class="skills-tags">
              <span class="skill-tag"><?php echo htmlspecialchars($row['skills'] ?? "") ?></span>
            </div>

            <div class="cv-row">
              <a href="../uploads/cv/<?php echo htmlspecialchars($row['cv_file'] ?? ""); ?>" target="_blank" class="cv-link">
                <i class="bi bi-eye"></i> View CV
              </a>
              <a href="../uploads/cv/<?php echo htmlspecialchars($row['cv_file'] ?? ""); ?>" download class="cv-download">
                <i class="bi bi-download"></i> Download
              </a>
            </div>

            <div class="app-actions">
              <?php if ($row['status'] == 'pending') { ?>
                <form method="POST">
                  <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                  <button type="submit" class="btn-accept" name="status-accept">Accept</button>
                  <button type="submit" class="btn-reject" name="status-reject">Reject</button>
                </form>
              <?php } elseif ($row['status'] == 'accepted') { ?>
                <div class="interview-block">
                  <?php if ($row['interview_at']) { ?>

                    <?php if ($row['interview_status'] == 'confirmed') { ?>
                      <div class="interview-info-badge" style="color:#15803d; background:rgba(34,197,94,.1); border-color:rgba(34,197,94,.25);">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo date("d M, h:i A", strtotime($row['interview_at'])); ?> — Confirmed</span>
                      </div>
                    <?php } elseif ($row['interview_status'] == 'reschedule_requested') { ?>
                      <div class="interview-info-badge" style="color:#b45309; background:rgba(234,179,8,.1); border-color:rgba(234,179,8,.3);">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Candidate requested reschedule<?php echo $row['reschedule_reason'] ? ': ' . htmlspecialchars($row['reschedule_reason']) : ''; ?></span>
                      </div>
                    <?php } else { ?>
                      <div class="interview-info-badge">
                        <i class="bi bi-calendar-check"></i>
                        <span><?php echo date("d M, h:i A", strtotime($row['interview_at'])); ?> — Awaiting confirmation</span>
                      </div>
                    <?php } ?>

                    <div style="display:flex; gap:8px;">
                      <form method="POST" style="flex:1;">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit" name="status-hire" class="btn-accept" style="width:100%;">
                          <i class="bi bi-check-circle"></i> Mark Hired
                        </button>
                      </form>
                      <form method="POST" style="flex:1;">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit" name="status-not-selected" class="btn-reject" style="width:100%;">
                          Not Selected
                        </button>
                      </form>
                    </div>
                    <a href="schedule-interview.php?application_id=<?php echo (int)$row['id']; ?>" class="btn-reschedule">
                      <i class="bi bi-arrow-repeat"></i> Reschedule
                    </a>

                  <?php } else { ?>
                    <a href="schedule-interview.php?application_id=<?php echo (int)$row['id']; ?>" class="btn-schedule-interview">
                      <i class="bi bi-calendar-plus"></i> Schedule Interview
                    </a>
                  <?php } ?>
                </div>
              <?php } elseif ($row['status'] == 'hired') { ?>
                <span class="status-pill status-hired" style="width:100%; text-align:center;">
                  <i class="bi bi-trophy-fill"></i> Hired
                </span>
              <?php } ?>
              <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <input type="hidden" name="application_id" value="<?php echo (int)$row['id']; ?>">
                <button type="submit" class="btn-hide" name="visibility">Hide</button>
              </form>
            </div>

          </div>
        <?php } ?>
      <?php } else { ?>
        <div class="no-data">No applications have been received yet.</div>
      <?php } ?>
    </div>

    <?php if ($totalPages > 1) { ?>
      <div class="pagination-wrap">
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
          <a href="?page=<?php echo $i; ?>#applications-section"
            class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
          </a>
        <?php } ?>
      </div>
    <?php } ?>

  </section>

</body>

</html>

<?php
include "../includes/footer.php";
?>