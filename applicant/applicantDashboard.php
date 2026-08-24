<?php
session_start();
include "../includes/config.php";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
};

$userid = $_SESSION["user_id"];

$userquery = "select * from users where id = ?";
$result = mysqli_execute_query($conn, $userquery, [$userid]);
$users = mysqli_fetch_assoc($result);

$isProfileComplete = !empty($users['bio']) && !empty($users['skills']) && !empty($users['cv_file']);

// ---- Profile completion percentage (5 fields check) ----
$profileFields = ['bio', 'skills', 'experience', 'cv_file', 'profile_image'];
$filledCount = 0;
foreach ($profileFields as $field) {
    if (!empty($users[$field])) {
        $filledCount++;
    }
}
$profileCompletionPercent = round(($filledCount / count($profileFields)) * 100);

$statsQuery = "select status, count(*) as total from applications where applicant_id = ? group by status";
$statsResult = mysqli_execute_query($conn, $statsQuery, [$userid]);
$statusCounts = ['pending' => 0, 'accepted' => 0, 'rejected' => 0,'hired' => 0];
$totalApplications = 0;
while ($row = mysqli_fetch_assoc($statsResult)) {
    $statusCounts[$row['status']] = (int)$row['total'];
    $totalApplications += (int)$row['total'];
}

$savedQuery = "select count(*) as total from saved_jobs where user_id = ?";
$savedResult = mysqli_execute_query($conn, $savedQuery, [$userid]);
$savedCount = mysqli_fetch_assoc($savedResult)['total'];

$recentQuery = "select applications.status, applications.applied_at, jobs.id as job_id, jobs.title, jobs.company, jobs.location
                from applications
                inner join jobs on applications.job_id = jobs.id
                where applications.applicant_id = ?
                order by applications.applied_at desc
                limit 5";
$recentResult = mysqli_execute_query($conn, $recentQuery, [$userid]);

$recentSavedQuery = "select saved_jobs.id as saved_id, jobs.id as job_id, jobs.title, jobs.company, jobs.location
                      from saved_jobs
                      inner join jobs on saved_jobs.job_id = jobs.id
                      where saved_jobs.user_id = ?
                      order by saved_jobs.id desc
                      limit 5";
$recentSavedResult = mysqli_execute_query($conn, $recentSavedQuery, [$userid]);

include "../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Lato:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --default-font: "Roboto", system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
      --heading-font: "Raleway", sans-serif;
      --accent-color: #0ea5e9;
      --accent-color-dark: #0284c7;
      --accent-color-soft: rgba(14,165,233,.12);
      --heading-color: #0f172a;
      --surface-color: #ffffff;
      --border-color: #e2e8f0;
    }

    body {
      background: #f4f7fb;
      font-family: var(--default-font);
      color: var(--heading-color);
    }

    .dash-shell { max-width: 1100px; margin: 40px auto 80px; padding: 0 20px; }

    .dash-hero {
      background: linear-gradient(135deg, var(--accent-color), var(--accent-color-dark));
      border-radius: 20px; padding: 32px 36px; color: #fff;
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 16px; margin-bottom: 28px;
    }
    .dash-hero h1 { font-family: var(--heading-font); font-size: 26px; margin: 0 0 6px; }
    .dash-hero p { margin: 0; opacity: .9; font-size: 14.5px; }

    .btn-white {
      background: #fff; color: var(--accent-color-dark); font-weight: 700;
      padding: 10px 20px; border-radius: 10px; text-decoration: none;
      font-size: 14px; white-space: nowrap;
    }

    .nudge {
      background: #fff7ed; border: 1px solid #fdba74; color: #9a3412;
      padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 10px; font-size: 14px;
    }
    .nudge a {
      background: #ea580c; color: #fff; padding: 8px 16px; border-radius: 8px;
      text-decoration: none; font-weight: 600; font-size: 13.5px; white-space: nowrap;
    }

    /* ---- Profile Snapshot ---- */
    .profile-snapshot {
      background: var(--surface-color); border: 1px solid var(--border-color);
      border-radius: 16px; padding: 24px; margin-bottom: 24px;
      display: flex; gap: 20px; align-items: center; flex-wrap: wrap;
    }
    .snapshot-avatar, .snapshot-avatar-fallback {
      width: 72px; height: 72px; border-radius: 50%; flex-shrink: 0;
      object-fit: cover;
      background: var(--accent-color-soft); color: var(--accent-color-dark);
      display: flex; align-items: center; justify-content: center;
      font-family: var(--heading-font); font-weight: 700; font-size: 26px;
    }
    .snapshot-info { flex: 1; min-width: 200px; }
    .snapshot-name { font-family: var(--heading-font); font-size: 18px; font-weight: 800; margin-bottom: 4px; }
    .snapshot-bio { font-size: 13.5px; color: #64748b; margin-bottom: 10px; line-height: 1.5; max-width: 480px; }
    .snapshot-skills { display: flex; flex-wrap: wrap; gap: 6px; }
    .snapshot-skill-tag {
      background: var(--accent-color-soft); color: var(--accent-color-dark);
      font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px;
    }

    .completion-wrap { min-width: 160px; text-align: center; }
    .completion-ring-label { font-size: 12px; color: #64748b; margin-bottom: 6px; }
    .completion-bar-track {
      width: 100%; height: 8px; background: #f1f5f9; border-radius: 20px; overflow: hidden;
    }
    .completion-bar-fill {
      height: 100%; background: linear-gradient(90deg, var(--accent-color), var(--accent-color-dark));
      border-radius: 20px;
    }
    .completion-percent { font-family: var(--heading-font); font-weight: 800; font-size: 20px; margin-top: 6px; color: var(--heading-color); }

    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
    .stat-card {
      background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 14px;
      padding: 20px; text-align: center; position: relative;
    }
    .stat-card .stat-icon {
      width: 36px; height: 36px; border-radius: 10px; background: var(--accent-color-soft);
      color: var(--accent-color-dark); display: flex; align-items: center; justify-content: center;
      margin: 0 auto 10px; font-size: 16px;
    }
    .stat-card .num { font-size: 28px; font-weight: 800; font-family: var(--heading-font); color: var(--heading-color); }
    .stat-card .label { font-size: 13px; color: #64748b; margin-top: 4px; }
    .stat-card.pending .stat-icon { background: rgba(234,179,8,.14); color: #b45309; }
    .stat-card.pending .num { color: #b45309; }
    .stat-card.accepted .stat-icon { background: rgba(34,197,94,.14); color: #15803d; }
    .stat-card.accepted .num { color: #15803d; }
    .stat-card.rejected .stat-icon { background: rgba(220,38,38,.12); color: #dc2626; }
    .stat-card.rejected .num { color: #dc2626; }

    .dashboard-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

    .section-card { background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 24px; }
    .section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .section-head h2 { font-family: var(--heading-font); font-size: 18px; margin: 0; }
    .section-head a { font-size: 13.5px; color: var(--accent-color-dark); text-decoration: none; font-weight: 600; }

    .app-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 10px; }
    .app-row:last-child { border-bottom: none; }
    .app-title { font-weight: 700; font-size: 14.5px; color: var(--heading-color); text-decoration: none; }
    .app-title:hover { color: var(--accent-color-dark); }
    .app-meta { font-size: 13px; color: #64748b; margin-top: 2px; }

    .status-pill { padding: 5px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 700; }
    .status-pending { color: #b45309; background: rgba(234,179,8,.14); }
    .status-accepted { color: #15803d; background: rgba(34,197,94,.14); }
    .status-rejected { color: #dc2626; background: rgba(220,38,38,.12); }

    .saved-pill {
      color: var(--accent-color-dark); background: var(--accent-color-soft);
      padding: 5px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 700;
      text-decoration: none;
    }

    .empty-note { text-align: center; color: #94a3b8; font-size: 14px; padding: 20px 0; }

    .quick-links { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .quick-link {
      background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 14px;
      padding: 20px; text-decoration: none; color: var(--heading-color); font-weight: 700;
      font-size: 14.5px; display: flex; align-items: center; gap: 12px;
      transition: border-color .15s ease, transform .15s ease;
    }
    .quick-link:hover { border-color: var(--accent-color); transform: translateY(-2px); }
    .quick-link .icon {
      width: 38px; height: 38px; border-radius: 10px; background: var(--accent-color-soft);
      color: var(--accent-color-dark); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    @media (max-width: 767px) {
      .stats-grid, .quick-links { grid-template-columns: 1fr 1fr; }
      .dashboard-panels { grid-template-columns: 1fr; }
      .profile-snapshot { flex-direction: column; text-align: center; }
      .snapshot-skills { justify-content: center; }
    }

    @media (max-width: 480px) {
      .stats-grid, .quick-links { grid-template-columns: 1fr; }
      .dash-hero { flex-direction: column; text-align: center; }
      .dash-hero .btn-white { width: 100%; text-align: center; }
    }
  </style>
</head>

<body>

  <div class="dash-shell">

    <div class="dash-hero">
      <div>
        <h1>Welcome back, <?php echo htmlspecialchars($users['fullname'] ?? ''); ?></h1>
        <p>Here's a quick look at your job search activity.</p>
      </div>
      <a class="btn-white" href="applicant-profile.php">View / Edit Profile</a>
    </div>

    <?php if (!$isProfileComplete) { ?>
      <div class="nudge">
        <span>⚠️ Your profile is incomplete — employers may skip incomplete profiles when reviewing applicants.</span>
        <a href="applicant-profile.php?edit=1">Complete Profile</a>
      </div>
    <?php } ?>

    <div class="profile-snapshot">
      <?php if (!empty($users['profile_image'])) { ?>
        <img class="snapshot-avatar" src="../uploads/profile/<?php echo htmlspecialchars($users['profile_image']); ?>" alt="Profile photo">
      <?php } else { ?>
        <div class="snapshot-avatar-fallback"><?php echo strtoupper(substr($users['fullname'] ?? 'U', 0, 1)); ?></div>
      <?php } ?>

      <div class="snapshot-info">
        <div class="snapshot-name"><?php echo htmlspecialchars($users['fullname'] ?? ''); ?></div>
        <div class="snapshot-bio">
          <?php echo !empty($users['bio']) ? htmlspecialchars($users['bio']) : 'No bio added yet — tell employers about yourself.'; ?>
        </div>
        <div class="snapshot-skills">
          <?php if (!empty($users['skills'])) { ?>
            <span class="snapshot-skill-tag"><?php echo htmlspecialchars($users['skills']); ?></span>
          <?php } else { ?>
            <span class="snapshot-skill-tag">No skills added yet</span>
          <?php } ?>
        </div>
      </div>

      <div class="completion-wrap">
        <div class="completion-ring-label">Profile Completion</div>
        <div class="completion-bar-track">
          <div class="completion-bar-fill" style="width: <?php echo $profileCompletionPercent; ?>%;"></div>
        </div>
        <div class="completion-percent"><?php echo $profileCompletionPercent; ?>%</div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-send-fill"></i></div>
        <div class="num"><?php echo $totalApplications; ?></div>
        <div class="label">Total Applications</div>
      </div>
      <div class="stat-card pending">
        <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="num"><?php echo $statusCounts['pending']; ?></div>
        <div class="label">Pending</div>
      </div>
      <div class="stat-card accepted">
        <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
        <div class="num"><?php echo $statusCounts['accepted']; ?></div>
        <div class="label">Accepted</div>
      </div>
      <div class="stat-card hired">
        <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
        <div class="num"><?php echo $statusCounts['hired']; ?></div>
        <div class="label">Hired</div>
      </div>
      <div class="stat-card rejected">
        <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
        <div class="num"><?php echo $statusCounts['rejected']; ?></div>
        <div class="label">Rejected</div>
      </div>
    </div>

    <div class="dashboard-panels">

      <div class="section-card">
        <div class="section-head">
          <h2>Recent Applications</h2>
          <a href="applied-jobs.php">View all →</a>
        </div>

        <?php if (mysqli_num_rows($recentResult) > 0) { ?>
          <?php while ($app = mysqli_fetch_assoc($recentResult)) { ?>
            <div class="app-row">
              <div>
                <a class="app-title" href="../job-details.php?id=<?php echo (int)$app['job_id']; ?>"><?php echo htmlspecialchars($app['title']); ?></a>
                <div class="app-meta"><?php echo htmlspecialchars($app['company']); ?> &middot; <?php echo htmlspecialchars($app['location']); ?></div>
              </div>
              <span class="status-pill status-<?php echo htmlspecialchars($app['status']); ?>">
                <?php echo ucfirst(htmlspecialchars($app['status'])); ?>
              </span>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="empty-note">You haven't applied to any jobs yet. <a href="../jobs.php">Browse jobs →</a></div>
        <?php } ?>
      </div>

      <div class="section-card">
        <div class="section-head">
          <h2>Recently Saved Jobs</h2>
          <a href="saved-jobs.php">View all →</a>
        </div>

        <?php if (mysqli_num_rows($recentSavedResult) > 0) { ?>
          <?php while ($saved = mysqli_fetch_assoc($recentSavedResult)) { ?>
            <div class="app-row">
              <div>
                <a class="app-title" href="../job-details.php?id=<?php echo (int)$saved['job_id']; ?>"><?php echo htmlspecialchars($saved['title']); ?></a>
                <div class="app-meta"><?php echo htmlspecialchars($saved['company']); ?> &middot; <?php echo htmlspecialchars($saved['location']); ?></div>
              </div>
              <a class="saved-pill" href="../job-details.php?id=<?php echo (int)$saved['job_id']; ?>">View</a>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="empty-note">No saved jobs yet. <a href="../jobs.php">Browse jobs →</a></div>
        <?php } ?>
      </div>

    </div>

    <div class="quick-links">
      <a class="quick-link" href="../jobs.php">
        <span class="icon"><i class="bi bi-search"></i></span>
        Browse Jobs
      </a>
      <a class="quick-link" href="applied-jobs.php">
        <span class="icon"><i class="bi bi-send"></i></span>
        Applied Jobs
      </a>
      <a class="quick-link" href="saved-jobs.php">
        <span class="icon"><i class="bi bi-bookmark"></i></span>
        Saved Jobs (<?php echo (int)$savedCount; ?>)
      </a>
      <a class="quick-link" href="applicant-profile.php">
        <span class="icon"><i class="bi bi-person"></i></span>
        Edit Profile
      </a>
    </div>

  </div>

</body>

</html>

<?php
include "../includes/footer.php";