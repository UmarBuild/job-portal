<?php
session_start();
include "../includes/config.php";
require_once "../includes/notify.php";
require_once "../mail/mail.php";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == "employer") {
  header("Location: ../login.php");
  exit();
};
$user_id = $_SESSION["user_id"];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['interview_action'])) {
  $appId = (int)$_POST['application_id'];

  $ownQuery = "select jobs.title, jobs.user_id as employer_id, employer.email as employer_email, employer.fullname as employer_name from applications inner join jobs on applications.job_id = jobs.id inner join users as employer on jobs.user_id = employer.id where applications.id = ? and applications.applicant_id = ?";
  $ownResult = mysqli_execute_query($conn, $ownQuery, [$appId, $user_id]);
  $ownApp = mysqli_fetch_assoc($ownResult);

  if ($ownApp) {
    if ($_POST['interview_action'] == 'confirm') {
      mysqli_execute_query($conn, "update interviews set status = 'confirmed' where application_id = ?", [$appId]);

      sendNotification($conn, $ownApp['employer_id'], 'interview_confirmed',
          "Candidate confirmed the interview for \"{$ownApp['title']}\"",
          BASE_PATH . "/employer/employer-applications.php");

      sendMail($ownApp['employer_email'], "Interview Confirmed",
          "<p>Hi {$ownApp['employer_name']}, the candidate has confirmed the interview for <b>{$ownApp['title']}</b>.</p>");

    } elseif ($_POST['interview_action'] == 'reschedule') {
      $reason = trim($_POST['reschedule_reason'] ?? '');
      mysqli_execute_query($conn, "update interviews set status = 'reschedule_requested', reschedule_reason = ? where application_id = ?", [$reason, $appId]);

      sendNotification($conn, $ownApp['employer_id'], 'interview_reschedule_requested',
          "Candidate requested a reschedule for \"{$ownApp['title']}\"",
          BASE_PATH . "/employer/employer-applications.php");

      sendMail($ownApp['employer_email'], "Interview Reschedule Requested",
          "<p>Hi {$ownApp['employer_name']}, the candidate has requested to reschedule the interview for <b>{$ownApp['title']}</b>.</p>" .
          ($reason ? "<p><b>Reason given:</b> " . htmlspecialchars($reason) . "</p>" : ""));
    }
  }

  header("Location: applied-jobs.php");
  exit();
}

$JobsAndApplicationquery = "select applications.id as application_id, applications.applied_at, applications.status as app_status,
                                    jobs.id as job_id, jobs.title, jobs.company, jobs.location, jobs.salary, jobs.job_type,
                                    interviews.scheduled_at as interview_at, interviews.interview_type,
                                    interviews.location_or_link as interview_location, interviews.notes as interview_notes,
                                    interviews.status as interview_status, interviews.reschedule_reason
                             from applications
                             inner join jobs on applications.job_id = jobs.id
                             left join interviews on interviews.application_id = applications.id
                             where applications.applicant_id = ?
                             order by applications.applied_at desc";
$result = mysqli_execute_query($conn, $JobsAndApplicationquery, [$user_id]);
?>

<style>
  .applied-jobs-section {
    background: var(--background-color);
    padding: 50px 0 80px;
    min-height: 70vh;
  }

  .page-title-box h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-weight: 800;
    font-size: 1.9rem;
    margin-bottom: 4px;
  }

  .page-title-box p {
    color: var(--default-color);
    font-size: 14.5px;
    margin: 0;
  }

  .btn-find-jobs {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(14, 165, 233, 0.25);
    white-space: nowrap;
  }

  .btn-find-jobs:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #fff;
  }

  /* ===== Application Card ===== */
  .application-card {
    background: var(--surface-color);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px;
    margin-bottom: 18px;
    transition: box-shadow 0.25s ease, border-color 0.25s ease;
  }

  .application-card:hover {
    box-shadow: 0 10px 30px rgba(14, 165, 233, 0.08);
    border-color: rgba(14, 165, 233, 0.25);
  }

  .application-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .application-job-info {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    min-width: 0;
  }

  .job-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: var(--accent-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .job-title-block h3 {
    font-family: var(--heading-font);
    font-size: 16.5px;
    font-weight: 700;
    color: var(--heading-color);
    margin: 0 0 3px;
  }

  .job-title-block .company-line {
    font-size: 13px;
    color: var(--default-color);
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .job-title-block .company-line i {
    color: var(--accent-color);
    font-size: 12px;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
  }

  .status-badge.pending { background: #fef9c3; color: #854d0e; }
  .status-badge.rejected { background: #fee2e2; color: #991b1b; }
  .status-badge.accepted { background: #dcfce7; color: #166534; }
  .status-badge.hired {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #fff;
  }

  /* ===== Meta row (applied date, type, salary) ===== */
  .application-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    padding: 14px 0;
    margin-bottom: 4px;
    border-top: 1px dashed #e2e8f0;
    border-bottom: 1px dashed #e2e8f0;
  }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--default-color);
  }

  .meta-item i {
    color: var(--accent-color);
    font-size: 13px;
  }

  .meta-item strong {
    color: var(--heading-color);
    font-weight: 600;
  }

  /* ===== Interview block ===== */
  .interview-panel {
    margin-top: 16px;
    padding: 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
  }

  .interview-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
  }

  .interview-datetime {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: var(--heading-color);
  }

  .interview-datetime i {
    color: var(--accent-color);
  }

  .interview-type-tag {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    background: #e2e8f0;
    padding: 3px 10px;
    border-radius: 6px;
    text-transform: capitalize;
  }

  .interview-status-line {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 999px;
  }

  .interview-status-line.confirmed { background: #dcfce7; color: #166534; }
  .interview-status-line.pending-response { background: #fef9c3; color: #854d0e; }

  .interview-action-row {
    display: flex;
    gap: 10px;
    margin-top: 12px;
    flex-wrap: wrap;
  }

  .btn-confirm-interview {
    flex: 1;
    min-width: 120px;
    background: #16a34a;
    color: #fff;
    border: none;
    padding: 9px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
  }

  .btn-confirm-interview:hover { background: #15803d; color: #fff; }

  .btn-request-reschedule {
    flex: 1;
    min-width: 120px;
    background: #fff;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 9px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
  }

  .btn-request-reschedule:hover { background: #f1f5f9; }

  .reschedule-panel {
    display: none;
    margin-top: 12px;
    padding: 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
  }

  .reschedule-panel input[type="text"] {
    width: 100%;
    box-sizing: border-box;
    padding: 9px 12px;
    font-size: 13px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 10px;
  }

  .btn-send-reschedule-req {
    width: 100%;
    background: #f59e0b;
    color: #fff;
    border: none;
    padding: 9px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
  }

  .btn-send-reschedule-req:hover { background: #d97706; }

  .awaiting-note {
    font-size: 12.5px;
    color: #94a3b8;
    font-style: italic;
  }

  /* ===== Bottom row: view button ===== */
  .application-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
  }

  .btn-view-job {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--accent-color);
    border: 1.5px solid rgba(14, 165, 233, 0.3);
    padding: 8px 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
  }

  .btn-view-job:hover {
    background: var(--accent-color);
    color: #fff;
    border-color: var(--accent-color);
  }

  .no-applications {
    text-align: center;
    padding: 70px 20px;
    background: var(--surface-color);
    border-radius: 16px;
    color: var(--default-color);
  }

  .no-applications i {
    font-size: 40px;
    color: #cbd5e1;
    margin-bottom: 14px;
    display: block;
  }

  /* ===== Mobile ===== */
  @media (max-width: 575px) {
    .page-title-box {
      text-align: center;
      width: 100%;
    }

    .btn-find-jobs {
      width: 100%;
      justify-content: center;
    }

    .application-top {
      flex-direction: column;
    }

    .status-badge {
      align-self: flex-start;
    }

    .application-meta {
      gap: 12px;
    }

    .interview-action-row {
      flex-direction: column;
    }

    .application-footer {
      justify-content: stretch;
    }

    .btn-view-job {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<?php include "../includes/header.php"; ?>

<?php if (isset($_SESSION["status"])) { ?>
  <div class="alert alert-success alert-dismissible fade show container mt-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?php echo $_SESSION["status"]; unset($_SESSION["status"]); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php } ?>

<?php if (isset($_SESSION["error"])) { ?>
  <div class="alert alert-danger alert-dismissible fade show container mt-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php } ?>

<main class="main">
  <section class="applied-jobs-section">
    <div class="container" data-aos="fade-up">

      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 page-title-box">
        <div>
          <h2>My Applied Jobs</h2>
          <p>Track and manage your submitted job applications</p>
        </div>
        <a href="../jobs.php" class="btn-find-jobs">
          <i class="bi bi-search"></i> Find More Jobs
        </a>
      </div>

      <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while ($jobs = mysqli_fetch_assoc($result)) { ?>
          <div class="application-card" data-aos="fade-up" data-aos-delay="50">

            <div class="application-top">
              <div class="application-job-info">
                <div class="job-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div class="job-title-block">
                  <h3><?php echo htmlspecialchars($jobs['title']); ?></h3>
                  <div class="company-line">
                    <i class="bi bi-buildings"></i> <?php echo htmlspecialchars($jobs['company']); ?>
                  </div>
                </div>
              </div>

              <span class="status-badge <?php echo htmlspecialchars($jobs['app_status']); ?>">
                <?php if ($jobs['app_status'] == 'hired') { ?><i class="bi bi-trophy-fill"></i><?php } ?>
                <?php echo htmlspecialchars($jobs['app_status']); ?>
              </span>
            </div>

            <div class="application-meta">
              <div class="meta-item">
                <i class="bi bi-calendar3"></i> Applied <strong><?php echo date("d M Y", strtotime($jobs['applied_at'])); ?></strong>
              </div>
              <div class="meta-item">
                <i class="bi bi-briefcase"></i> <strong><?php echo htmlspecialchars($jobs['job_type']); ?></strong>
              </div>
              <div class="meta-item">
                <i class="bi bi-cash-stack"></i> <strong><?php echo htmlspecialchars($jobs['salary']); ?></strong>
              </div>
            </div>

            <?php if ($jobs['app_status'] == "accepted" && !empty($jobs['interview_at'])) { ?>
              <div class="interview-panel">
                <div class="interview-panel-head">
                  <div class="interview-datetime">
                    <i class="bi bi-calendar-check"></i>
                    <?php echo date("d M Y, h:i A", strtotime($jobs['interview_at'])); ?>
                    <span class="interview-type-tag"><?php echo htmlspecialchars($jobs['interview_type']); ?></span>
                  </div>

                  <?php if ($jobs['interview_status'] == 'confirmed') { ?>
                    <span class="interview-status-line confirmed"><i class="bi bi-check-circle-fill"></i> Confirmed</span>
                  <?php } elseif ($jobs['interview_status'] == 'reschedule_requested') { ?>
                    <span class="interview-status-line pending-response"><i class="bi bi-clock-history"></i> Awaiting employer</span>
                  <?php } ?>
                </div>

                <?php if (!empty($jobs['interview_location'])) { ?>
                  <div class="meta-item mb-2">
                    <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($jobs['interview_location']); ?>
                  </div>
                <?php } ?>

                <?php if ($jobs['interview_status'] == 'scheduled') { ?>
                  <div class="interview-action-row">
                    <form method="POST" class="d-flex flex-fill">
                      <input type="hidden" name="application_id" value="<?php echo (int)$jobs['application_id']; ?>">
                      <input type="hidden" name="interview_action" value="confirm">
                      <button type="submit" class="btn-confirm-interview">
                        <i class="bi bi-check-lg"></i> Confirm Interview
                      </button>
                    </form>
                    <button type="button" class="btn-request-reschedule"
                            onclick="document.getElementById('reschedule-<?php echo $jobs['application_id']; ?>').style.display='block'">
                      Request Reschedule
                    </button>
                  </div>

                  <div class="reschedule-panel" id="reschedule-<?php echo $jobs['application_id']; ?>">
                    <form method="POST">
                      <input type="hidden" name="application_id" value="<?php echo (int)$jobs['application_id']; ?>">
                      <input type="hidden" name="interview_action" value="reschedule">
                      <input type="text" name="reschedule_reason" placeholder="Reason (optional) — e.g. schedule conflict">
                      <button type="submit" class="btn-send-reschedule-req">Send Reschedule Request</button>
                    </form>
                  </div>
                <?php } elseif ($jobs['interview_status'] == 'reschedule_requested' && !empty($jobs['reschedule_reason'])) { ?>
                  <div class="awaiting-note">Your note: "<?php echo htmlspecialchars($jobs['reschedule_reason']); ?>"</div>
                <?php } ?>
              </div>
            <?php } elseif ($jobs['app_status'] == "accepted") { ?>
              <div class="awaiting-note mt-2"><i class="bi bi-hourglass-split"></i> Interview not scheduled yet — the employer will reach out soon.</div>
            <?php } ?>

            <div class="application-footer">
              <a href="../job-details.php?id=<?php echo (int)$jobs['job_id']; ?>" class="btn-view-job">
                View Job <i class="bi bi-arrow-right"></i>
              </a>
            </div>

          </div>
        <?php } ?>
      <?php } else { ?>
        <div class="no-applications">
          <i class="bi bi-inbox"></i>
          <h5 class="fw-bold mb-2">No applications yet</h5>
          <p class="mb-3">You haven't applied to any jobs. Start exploring opportunities now.</p>
          <a href="../jobs.php" class="btn-find-jobs">Browse Jobs</a>
        </div>
      <?php } ?>

    </div>
  </section>
</main>

<?php include "../includes/footer.php"; ?>