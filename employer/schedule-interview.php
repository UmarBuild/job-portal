<?php
session_start();
include "../includes/config.php";
require_once "../includes/notify.php";
require_once "../mail/mail.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "employer") {
  header("Location: ../login.php");
  exit();
}

$employerId = $_SESSION["user_id"];
$applicationId = isset($_GET['application_id']) ? (int)$_GET['application_id'] : (isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0);

$verifyQuery = "select applications.id, applications.status, users.fullname, users.email, jobs.title
                 from applications
                 inner join jobs on applications.job_id = jobs.id
                 inner join users on applications.applicant_id = users.id
                 where applications.id = ? and jobs.user_id = ?";
$verifyResult = mysqli_execute_query($conn, $verifyQuery, [$applicationId, $employerId]);
$application = mysqli_fetch_assoc($verifyResult);

if (!$application) {
  die("Application not found or you don't have permission to access it.");
}

if ($application['status'] != 'accepted') {
  die("You can only schedule interviews for accepted applicants.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $type = $_POST['interview_type'];
  $datetime = $_POST['scheduled_at'];
  $location = trim($_POST['location_or_link']);
  $notes = trim($_POST['notes']);

  $upsertQuery = "insert into interviews (application_id, scheduled_by, interview_type, scheduled_at, location_or_link, notes, status)
                   values (?, ?, ?, ?, ?, ?, 'scheduled')
                   on duplicate key update
                     interview_type = values(interview_type),
                     scheduled_at = values(scheduled_at),
                     location_or_link = values(location_or_link),
                     notes = values(notes),
                     status = 'scheduled'";
  $ok = mysqli_execute_query($conn, $upsertQuery, [$applicationId, $employerId, $type, $datetime, $location, $notes]);

  if ($ok) {
    $applicantIdQuery = "select applicant_id from applications where id = ?";
    $applicantIdResult = mysqli_execute_query($conn, $applicantIdQuery, [$applicationId]);
    $applicantId = mysqli_fetch_assoc($applicantIdResult)['applicant_id'];

    $formattedDate = date("d M Y, h:i A", strtotime($datetime));

    sendNotification(
      $conn,
      $applicantId,
      'interview_scheduled',
      "Interview scheduled for \"{$application['title']}\" on {$formattedDate}",
      "/job-portal/applicant/applied-jobs.php"
    );
      sendMail(
      $application['email'],
      "Interview Scheduled ",
      "<p>Hi {$application['fullname']}, your interview for <b>{$application['title']}</b> has been scheduled.</p>
       <p><b>When:</b> {$formattedDate}<br>
       <b>Type:</b> " . ucfirst($type) . "<br>
       <b>Where:</b> " . htmlspecialchars($location) . "</p>" .
       ($notes ? "<p><b>Notes:</b> " . htmlspecialchars($notes) . "</p>" : "")
    );
  }

  header("Location: employer-applications.php");
  exit();
}
$existingQuery = "select * from interviews where application_id = ?";
$existingResult = mysqli_execute_query($conn, $existingQuery, [$applicationId]);
$existing = mysqli_fetch_assoc($existingResult);

include "../includes/header.php";
?>
<div style="max-width:600px; margin:50px auto; padding:0 20px;">
  <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:30px;">
    <h3 style="font-family:'Raleway',sans-serif; margin-bottom:6px;">
      <?php echo $existing ? "Reschedule Interview" : "Schedule Interview"; ?>
    </h3>
    <p style="color:#64748b; font-size:14px; margin-bottom:24px;">
      With <?php echo htmlspecialchars($application['fullname']); ?> for <?php echo htmlspecialchars($application['title']); ?>
    </p>

    <form method="POST">
      <input type="hidden" name="application_id" value="<?php echo $applicationId; ?>">

      <label style="font-weight:600; font-size:13.5px;">Interview Type</label>
      <select name="interview_type" required style="width:100%; padding:10px; margin:8px 0 16px; border:1px solid #e2e8f0; border-radius:8px;">
        <option value="video" <?php echo ($existing && $existing['interview_type'] == 'video') ? 'selected' : ''; ?>>Video Call</option>
        <option value="phone" <?php echo ($existing && $existing['interview_type'] == 'phone') ? 'selected' : ''; ?>>Phone Call</option>
        <option value="in-person" <?php echo ($existing && $existing['interview_type'] == 'in-person') ? 'selected' : ''; ?>>In-Person</option>
      </select>

      <label style="font-weight:600; font-size:13.5px;">Date & Time</label>
      <input type="datetime-local" name="scheduled_at" required
        value="<?php echo $existing ? date('Y-m-d\TH:i', strtotime($existing['scheduled_at'])) : ''; ?>"
        style="width:100%; padding:10px; margin:8px 0 16px; border:1px solid #e2e8f0; border-radius:8px;">

      <label style="font-weight:600; font-size:13.5px;">Location / Meeting Link</label>
      <input type="text" name="location_or_link" placeholder="Zoom link or office address"
        value="<?php echo $existing ? htmlspecialchars($existing['location_or_link']) : ''; ?>"
        style="width:100%; padding:10px; margin:8px 0 16px; border:1px solid #e2e8f0; border-radius:8px;">

      <label style="font-weight:600; font-size:13.5px;">Notes (optional)</label>
      <textarea name="notes" rows="3" style="width:100%; padding:10px; margin:8px 0 20px; border:1px solid #e2e8f0; border-radius:8px;"><?php echo $existing ? htmlspecialchars($existing['notes']) : ''; ?></textarea>

      <div style="display:flex; gap:10px;">
        <button type="submit" style="background:#0ea5e9; color:#fff; border:none; padding:11px 24px; border-radius:8px; font-weight:600; cursor:pointer;">
          <?php echo $existing ? "Update Interview" : "Confirm Schedule"; ?>
        </button>
        <a href="employer-applications.php" style="padding:11px 24px; border-radius:8px; color:#64748b; text-decoration:none; border:1px solid #e2e8f0;">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include "../includes/footer.php"; ?>