<?php
session_start();
include "../includes/config.php";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
};
include "../includes/header.php";


$userid = $_SESSION["user_id"];
$selectquery = "select * from users where id = '$userid' ";
$result = mysqli_query($conn, $selectquery);
if (mysqli_num_rows($result) > 0) {
    $users = mysqli_fetch_assoc($result);
}

$isProfileComplete = !empty($users['bio']) && !empty($users['skills']) && !empty($users['cv_file']);

$editMode = isset($_GET['edit']) || !$isProfileComplete;

if (isset($_POST["create_profile"])) {
    $fullname = $_POST["fullname"];
    $old_profile_image = $_POST["old_profile_image"];
    $skills = $_POST["skills"];
    $bio = $_POST["bio"];
    $old_cv_file = $_POST["old_cv_file"];
    $experience =  $_POST["experience"];

    $profile_image = "";
    if (!empty($_FILES["profile_image"]["name"])) {
        $profile_image = time() . $_FILES["profile_image"]["name"];
        $update_profile_image = $profile_image;
    } else {
        $update_profile_image = $old_profile_image;
    }

    $cv_file = "";
    if (!empty($_FILES["cv_file"]["name"])) {
        $cv_file = time() . $_FILES["cv_file"]["name"];
        $update_cv_file = $cv_file;
    } else {
        $update_cv_file = $old_cv_file;
    }

    $updatequery = "update users set fullname = '$fullname' , profile_image = '$update_profile_image',skills = '$skills',bio = '$bio',cv_file = '$update_cv_file',experience='$experience' where id = '$userid'";
    $update = mysqli_query($conn, $updatequery);

    if ($update) {
        if (!empty($profile_image)) {
            move_uploaded_file($_FILES["profile_image"]["tmp_name"], "../uploads/profile/" . $profile_image);

            if (!empty($old_profile_image) && file_exists("../uploads/profile/" . $old_profile_image)) {
                unlink("../uploads/profile/" . $old_profile_image);
            }
        }

        if (!empty($cv_file)) {
            move_uploaded_file($_FILES["cv_file"]["tmp_name"], "../uploads/cv/" . $cv_file);

            if (!empty($old_cv_file) && file_exists("../uploads/cv/" . $old_cv_file)) {
                unlink("../uploads/cv/" . $old_cv_file);
            }
        }

        header("Location: applicantDashboard.php");
        exit();
    }
};
?>


<!DOCTYPE html>
<html lang="ur">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Candidate Profile — Build Your Profile</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@600;700;800&family=Lato:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --default-font: "Roboto", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      --heading-font: "Raleway", sans-serif;
      --nav-font: "Lato", sans-serif;

      --background-color: #f1f5f9;
      --default-color: #475569;
      --heading-color: #1e293b;
      --accent-color: #0ea5e9;
      --surface-color: #ffffff;
      --contrast-color: #ffffff;

      --nav-color: #475569;
      --nav-hover-color: #0ea5e9;
      --nav-mobile-background-color: #ffffff;
      --nav-dropdown-background-color: #ffffff;
      --nav-dropdown-color: #475569;
      --nav-dropdown-hover-color: #0ea5e9;

      --accent-color-soft: #e0f2fe;
      --accent-color-dark: #0284c7;
      --border-color: #e2e8f0;
      --success-color: #16a34a;
      --error-color: #dc2626;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: var(--default-font);
      background-color: var(--background-color);
      color: var(--default-color);
      line-height: 1.6;
      min-height: 100vh;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background:
        radial-gradient(circle at 8% 8%, var(--accent-color-soft) 0%, transparent 32%),
        radial-gradient(circle at 95% 15%, var(--accent-color-soft) 0%, transparent 28%);
      opacity: .8;
      pointer-events: none;
      z-index: 0;
    }

    .page-shell {
      position: relative;
      z-index: 1;
      max-width: 1180px;
      margin: 0 auto;
      padding: clamp(20px, 4vw, 56px) clamp(16px, 4vw, 32px) 80px;
    }

    .masthead {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: clamp(28px, 5vw, 48px);
      flex-wrap: wrap;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-mark {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--accent-color), var(--accent-color-dark));
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--contrast-color);
      font-family: var(--heading-font);
      font-weight: 800;
      font-size: 18px;
      flex-shrink: 0;
      box-shadow: 0 8px 20px -8px rgba(14, 165, 233, .6);
    }

    .brand-text {
      font-family: var(--nav-font);
      font-weight: 700;
      color: var(--heading-color);
      font-size: 15px;
      letter-spacing: .2px;
    }

    .brand-text span {
      display: block;
      font-weight: 400;
      font-size: 12px;
      color: var(--default-color);
    }

    .step-pill {
      font-family: var(--nav-font);
      font-size: 13px;
      font-weight: 700;
      color: var(--accent-color-dark);
      background: var(--accent-color-soft);
      border: 1px solid #bae6fd;
      padding: 7px 14px;
      border-radius: 999px;
      white-space: nowrap;
    }

    .hero-copy {
      margin-bottom: clamp(28px, 5vw, 44px);
      max-width: 680px;
    }

    .hero-eyebrow {
      font-family: var(--nav-font);
      text-transform: uppercase;
      letter-spacing: 1.6px;
      font-size: 12px;
      font-weight: 700;
      color: var(--accent-color-dark);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .hero-eyebrow::before {
      content: "";
      width: 22px;
      height: 2px;
      background: var(--accent-color);
      display: inline-block;
    }

    .hero-copy h1 {
      font-family: var(--heading-font);
      color: var(--heading-color);
      font-size: clamp(26px, 4vw, 38px);
      font-weight: 800;
      letter-spacing: -.5px;
      margin-bottom: 12px;
    }

    .hero-copy p {
      font-size: 15.5px;
      color: var(--default-color);
      max-width: 58ch;
    }

    .builder-grid {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: clamp(20px, 3vw, 32px);
      align-items: start;
    }

    @media (max-width: 900px) {
      .builder-grid {
        grid-template-columns: 1fr;
      }
    }

    .preview-col {
      position: sticky;
      top: 20px;
    }

    @media (max-width: 900px) {
      .preview-col {
        position: static;
        order: -1;
      }
    }

    .preview-label {
      font-family: var(--nav-font);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #94a3b8;
      margin-bottom: 10px;
      padding-left: 4px;
    }

    .preview-card {
      background: var(--surface-color);
      border-radius: 20px;
      padding: 28px 24px 24px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 20px 40px -24px rgba(15, 23, 42, .18);
      border: 1px solid var(--border-color);
      position: relative;
      overflow: hidden;
    }

    .preview-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 84px;
      background: linear-gradient(120deg, var(--accent-color), var(--accent-color-dark));
    }

    .preview-avatar-wrap {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: center;
      margin-bottom: 14px;
    }

    .preview-avatar,
    .preview-avatar-fallback {
      width: 92px;
      height: 92px;
      border-radius: 50%;
      background: var(--accent-color-soft);
      border: 4px solid var(--surface-color);
      box-shadow: 0 6px 16px -6px rgba(15, 23, 42, .3);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--accent-color-dark);
      font-family: var(--heading-font);
      font-weight: 700;
      font-size: 30px;
      object-fit: cover;
    }

    .preview-body {
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .preview-name {
      font-family: var(--heading-font);
      color: var(--heading-color);
      font-size: 19px;
      font-weight: 700;
      margin-bottom: 3px;
    }

    .preview-role {
      font-family: var(--nav-font);
      color: var(--accent-color-dark);
      font-size: 13.5px;
      font-weight: 700;
      margin-bottom: 14px;
      text-transform: capitalize;
    }

    .preview-badges {
      display: flex;
      justify-content: center;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .preview-badge {
      font-size: 11px;
      font-weight: 700;
      font-family: var(--nav-font);
      color: var(--default-color);
      background: var(--background-color);
      border: 1px solid var(--border-color);
      padding: 4px 10px;
      border-radius: 999px;
    }

    .preview-divider {
      height: 1px;
      background: var(--border-color);
      margin: 16px 0;
    }

    .preview-bio {
      font-size: 13px;
      color: var(--default-color);
      text-align: left;
      line-height: 1.65;
    }

    .preview-section-label {
      font-family: var(--nav-font);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #94a3b8;
      text-align: left;
      margin: 16px 0 8px;
    }

    .preview-skills {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      justify-content: flex-start;
    }

    .preview-skill-chip {
      font-size: 11.5px;
      font-weight: 600;
      color: var(--accent-color-dark);
      background: var(--accent-color-soft);
      padding: 4px 10px;
      border-radius: 8px;
    }

    .preview-file-row {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12.5px;
      color: var(--default-color);
      text-align: left;
      margin-top: 6px;
    }

    .preview-file-row svg {
      flex-shrink: 0;
      color: var(--accent-color);
    }

    .preview-file-row a {
      color: var(--accent-color-dark);
      text-decoration: none;
      font-weight: 600;
    }

    /* ---------- Profile summary (read/view mode) ---------- */
    .summary-card {
      background: var(--surface-color);
      border-radius: 20px;
      padding: clamp(22px, 4vw, 40px);
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 20px 40px -24px rgba(15, 23, 42, .14);
      border: 1px solid var(--border-color);
    }

    .summary-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 22px;
      flex-wrap: wrap;
    }

    .summary-head h2 {
      font-family: var(--heading-font);
      color: var(--heading-color);
      font-size: 20px;
      font-weight: 800;
    }

    .summary-head p {
      font-size: 13px;
      color: #94a3b8;
      margin-top: 4px;
    }

    .btn-edit-profile {
      font-family: var(--nav-font);
      font-weight: 700;
      font-size: 13.5px;
      color: var(--contrast-color);
      background: linear-gradient(135deg, var(--accent-color), var(--accent-color-dark));
      border: none;
      padding: 11px 22px;
      border-radius: 10px;
      cursor: pointer;
      text-decoration: none;
      color: white;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 8px 18px -8px rgba(14, 165, 233, .55);
    }

    .summary-block {
      margin-bottom: 22px;
    }

    .summary-block:last-child {
      margin-bottom: 0;
    }

    .summary-label {
      font-family: var(--nav-font);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #94a3b8;
      margin-bottom: 8px;
    }

    .summary-text {
      font-size: 14.5px;
      color: var(--default-color);
      line-height: 1.7;
    }

    /* ---------- Form card ---------- */
    .form-card {
      background: var(--surface-color);
      border-radius: 20px;
      padding: clamp(22px, 4vw, 40px);
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 20px 40px -24px rgba(15, 23, 42, .14);
      border: 1px solid var(--border-color);
    }

    .form-section+.form-section {
      margin-top: 30px;
      padding-top: 30px;
      border-top: 1px dashed var(--border-color);
    }

    .section-heading {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .section-num {
      width: 26px;
      height: 26px;
      border-radius: 8px;
      background: var(--accent-color-soft);
      color: var(--accent-color-dark);
      font-family: var(--nav-font);
      font-weight: 800;
      font-size: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .section-heading h2 {
      font-family: var(--heading-font);
      color: var(--heading-color);
      font-size: 17px;
      font-weight: 700;
    }

    .section-heading p {
      font-size: 12.5px;
      color: #94a3b8;
      margin-top: 1px;
    }

    .field {
      margin-bottom: 18px;
    }

    .field:last-child {
      margin-bottom: 0;
    }

    .field label {
      display: flex;
      align-items: center;
      gap: 6px;
      font-family: var(--nav-font);
      font-weight: 700;
      font-size: 13.5px;
      color: var(--heading-color);
      margin-bottom: 7px;
    }

    .field label .req {
      color: var(--accent-color);
      font-weight: 700;
    }

    .field label .optional-tag {
      font-family: var(--default-font);
      font-weight: 400;
      font-size: 11.5px;
      color: #94a3b8;
    }

    .field .hint {
      font-size: 12px;
      color: #94a3b8;
      margin-top: 6px;
    }

    .input,
    .textarea {
      width: 100%;
      font-family: var(--default-font);
      font-size: 14.5px;
      color: var(--heading-color);
      background: var(--background-color);
      border: 1.5px solid var(--border-color);
      border-radius: 10px;
      padding: 12px 14px;
      outline: none;
      transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .input::placeholder,
    .textarea::placeholder {
      color: #94a3b8;
    }

    .input:focus,
    .textarea:focus {
      border-color: var(--accent-color);
      background: var(--surface-color);
      box-shadow: 0 0 0 4px var(--accent-color-soft);
    }

    .textarea {
      resize: vertical;
      min-height: 110px;
      line-height: 1.6;
    }

    .row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 560px) {
      .row-2 {
        grid-template-columns: 1fr;
      }
    }

    .avatar-uploader {
      display: flex;
      align-items: center;
      gap: 18px;
      flex-wrap: wrap;
    }

    .avatar-drop {
      width: 84px;
      height: 84px;
      border-radius: 50%;
      border: 2px dashed #cbd5e1;
      background: var(--background-color);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #94a3b8;
      flex-shrink: 0;
      cursor: pointer;
      overflow: hidden;
      transition: border-color .15s ease, background .15s ease;
    }

    .avatar-drop img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .avatar-drop:hover {
      border-color: var(--accent-color);
      background: var(--accent-color-soft);
    }

    .avatar-actions {
      flex: 1;
      min-width: 180px;
    }

    .btn-choose {
      display: inline-block;
      font-family: var(--nav-font);
      font-weight: 700;
      font-size: 13px;
      color: var(--accent-color-dark);
      background: var(--accent-color-soft);
      border: 1px solid #bae6fd;
      padding: 9px 16px;
      border-radius: 9px;
      cursor: pointer;
      transition: background .15s ease;
    }

    .btn-choose:hover {
      background: #bae6fd;
    }

    .avatar-filename {
      font-size: 12px;
      color: #94a3b8;
      margin-top: 8px;
    }

    .skills-box {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      width: 100%;
      background: var(--background-color);
      border: 1.5px solid var(--border-color);
      border-radius: 10px;
      padding: 10px 12px;
    }

    .skills-box:focus-within {
      border-color: var(--accent-color);
      background: var(--surface-color);
      box-shadow: 0 0 0 4px var(--accent-color-soft);
    }

    .skills-box input {
      border: none;
      outline: none;
      background: transparent;
      font-family: var(--default-font);
      font-size: 14px;
      color: var(--heading-color);
      flex: 1;
      min-width: 120px;
      padding: 5px 2px;
    }

    .skills-box input::placeholder {
      color: #94a3b8;
    }

    .file-drop {
      display: block;
      border: 2px dashed #cbd5e1;
      border-radius: 14px;
      background: var(--background-color);
      padding: 26px 20px;
      text-align: center;
      cursor: pointer;
      transition: border-color .15s ease, background .15s ease;
    }

    .file-drop:hover {
      border-color: var(--accent-color);
      background: var(--accent-color-soft);
    }

    .file-drop-icon {
      width: 44px;
      height: 44px;
      margin: 0 auto 10px;
      border-radius: 50%;
      background: var(--surface-color);
      box-shadow: 0 4px 10px -4px rgba(15, 23, 42, .15);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--accent-color);
    }

    .file-drop-title {
      font-family: var(--nav-font);
      font-weight: 700;
      font-size: 14px;
      color: var(--heading-color);
    }

    .file-drop-sub {
      font-size: 12px;
      color: #94a3b8;
      margin-top: 4px;
    }

    .exp-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 10px;
    }

    .exp-card {
      position: relative;
    }

    .exp-card input {
      position: absolute;
      opacity: 0;
      inset: 0;
      cursor: pointer;
      margin: 0;
    }

    .exp-card span {
      display: block;
      text-align: center;
      font-family: var(--nav-font);
      font-weight: 700;
      font-size: 13px;
      color: var(--default-color);
      background: var(--background-color);
      border: 1.5px solid var(--border-color);
      border-radius: 10px;
      padding: 12px 8px;
      transition: all .15s ease;
    }

    .exp-card input:checked+span {
      border-color: var(--accent-color);
      background: var(--accent-color-soft);
      color: var(--accent-color-dark);
      box-shadow: 0 0 0 4px var(--accent-color-soft);
    }

    .exp-card:hover span {
      border-color: var(--accent-color);
    }

    .submit-row {
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .submit-note {
      font-size: 12px;
      color: #94a3b8;
      max-width: 340px;
    }

    .btn-submit {
      font-family: var(--nav-font);
      font-weight: 700;
      font-size: 15px;
      color: var(--contrast-color);
      background: linear-gradient(135deg, var(--accent-color), var(--accent-color-dark));
      border: none;
      padding: 14px 30px;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 0 10px 24px -10px rgba(14, 165, 233, .6);
      transition: transform .15s ease, box-shadow .15s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 28px -10px rgba(14, 165, 233, .7);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
      * {
        transition: none !important;
      }
    }
  </style>
</head>

<body>

  <div class="page-shell">

    <div class="masthead">
      <div class="brand">
        <div class="brand-mark">JP</div>
        <div class="brand-text">JobPortal<span>Candidate Profile</span></div>
      </div>
      <div class="step-pill"><?php echo $editMode ? "Profile setup" : "Profile complete"; ?></div>
    </div>

    <?php if ($editMode) { ?>

      <div class="hero-copy">
        <div class="hero-eyebrow">Get discovered by recruiters</div>
        <h1>Build a profile that gets you shortlisted</h1>
        <p>Employers scan hundreds of profiles a day — a sharp photo, a clear headline and the right skills are what make yours worth a second look. Fill in the details below to complete your profile.</p>
      </div>

      <form class="builder-grid" id="profileForm" method="POST" enctype="multipart/form-data">

        <div class="preview-col">
          <div class="preview-label">Preview</div>
          <div class="preview-card">
            <div class="preview-avatar-wrap">
              <?php if (!empty($users["profile_image"])) { ?>
                <img class="preview-avatar" src="../uploads/profile/<?php echo $users["profile_image"] ?>" alt="Current Photo">
              <?php } ?>
            </div>
            <div class="preview-body">
              <div class="preview-name"><?php echo $users["fullname"] ?></div>
              <div class="preview-role"><?php echo $users["role"] ?></div>

              <div class="preview-badges">
                <span class="preview-badge"><?php echo $users["experience"] ?></span>
              </div>

              <div class="preview-divider"></div>

              <div class="preview-section-label">About</div>
              <div class="preview-bio"><?php echo !empty($users["bio"]) ? $users["bio"] : "No bio added yet." ?></div>

              <div class="preview-section-label">Skills</div>
              <div class="preview-skills">
                <span class="preview-skill-chip"><?php echo !empty($users["skills"]) ? $users["skills"] : "No skills added yet." ?></span>
              </div>

              <div class="preview-section-label">Resume</div>
              <div class="preview-file-row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                  <path d="M14 2v6h6" />
                </svg>
                <span><?php echo !empty($users["cv_file"]) ? $users["cv_file"] : "No CV uploaded yet." ?></span>
              </div>
            </div>
          </div>
        </div>

        <div class="form-card">

          <div class="form-section">
            <div class="section-heading">
              <div class="section-num">1</div>
              <div>
                <h2>Basic information</h2>
                <p>How employers will identify you</p>
              </div>
            </div>

            <div class="field">
              <label for="fullname">Full name <span class="req">*</span></label>
              <input class="input" type="text" id="fullname" name="fullname" placeholder="e.g. Ayesha Khan" required autocomplete="name" value="<?php echo $users['fullname'] ?? '' ?>">
            </div>

            <div class="field">
              <label for="profile_image">Profile photo <span class="optional-tag">optional but recommended</span></label>
              <div class="avatar-uploader">
                <label class="avatar-drop" for="profile_image">
                  <?php if (!empty($users['profile_image'])) { ?>
                    <img src="../uploads/profile/<?php echo $users['profile_image'] ?>" alt="Current Photo">
                  <?php } else { ?>
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 5v14M5 12h14" />
                    </svg>
                  <?php } ?>
                </label>
                <div class="avatar-actions">
                  <label class="btn-choose" for="profile_image">Upload photo</label>
                  <div class="avatar-filename">PNG or JPG, up to 5MB. Square photos work best.</div>
                  <input type="file" id="profile_image" name="profile_image" style="display:none;">
                  <input type="hidden" name="old_profile_image" value="<?php echo $users['profile_image'] ?? '' ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-heading">
              <div class="section-num">2</div>
              <div>
                <h2>About you</h2>
                <p>Tell employers your story in a few lines</p>
              </div>
            </div>

            <div class="field">
              <label for="bio">Professional bio <span class="req">*</span></label>
              <textarea class="textarea" id="bio" name="bio" maxlength="400" placeholder="Briefly describe your background, what you're great at, and what kind of role you're looking for…" required><?php echo $users['bio'] ?? '' ?></textarea>
              <p class="hint">Maximum 400 characters.</p>
            </div>

            <div class="field">
              <label>Years of experience <span class="req">*</span></label>
              <div class="exp-grid">
                <label class="exp-card"><input type="radio" name="experience" value="Fresher" <?php echo ($users['experience'] ?? '') == 'Fresher' ? 'checked' : '' ?> required><span>Fresher</span></label>
                <label class="exp-card"><input type="radio" name="experience" value="1 years" <?php echo ($users['experience'] ?? '') == '1 years' ? 'checked' : '' ?>><span>0–1 yrs</span></label>
                <label class="exp-card"><input type="radio" name="experience" value="2 years" <?php echo ($users['experience'] ?? '') == '2 years' ? 'checked' : '' ?>><span>1–2 yrs</span></label>
                <label class="exp-card"><input type="radio" name="experience" value="3 years" <?php echo ($users['experience'] ?? '') == '3 years' ? 'checked' : '' ?>><span>2–3 yrs</span></label>
                <label class="exp-card"><input type="radio" name="experience" value="3+ years" <?php echo ($users['experience'] ?? '') == '3+ years' ? 'checked' : '' ?>><span>3+ yrs</span></label>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-heading">
              <div class="section-num">3</div>
              <div>
                <h2>Skills</h2>
                <p>Add the skills recruiters search for</p>
              </div>
            </div>

            <div class="field">
              <label for="skillsInput">Your skills <span class="req">*</span></label>
              <div class="skills-box">
                <input name="skills" type="text" id="skillsInput" placeholder="e.g. React, Laravel, PHP" value="<?php echo $users['skills'] ?? '' ?>" required>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-heading">
              <div class="section-num">4</div>
              <div>
                <h2>Resume / CV</h2>
                <p>PDF or Word, so employers can review it offline</p>
              </div>
            </div>

            <div class="field">
              <label for="cv_file">Upload your CV <span class="req">*</span></label>
              <label class="file-drop" for="cv_file">
                <div class="file-drop-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                    <path d="M17 8l-5-5-5 5" />
                    <path d="M12 3v12" />
                  </svg>
                </div>
                <div class="file-drop-title">Click to upload or drag your file here</div>
                <?php if (!empty($users['cv_file'])) { ?>
                  <div class="file-drop-sub">Current file: <?php echo $users['cv_file'] ?></div>
                <?php } ?>
                <input type="file" id="cv_file" name="cv_file" required style="display:none;">
                <input type="hidden" name="old_cv_file" value="<?php echo $users['cv_file'] ?? '' ?>">
              </label>
            </div>
          </div>

          <div class="submit-row">
            <p class="submit-note">By submitting, you agree that recruiters on JobPortal can view this profile and contact you about matching roles.</p>
            <button type="submit" class="btn-submit" name="create_profile">
              Save profile
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M5 12h14M13 6l6 6-6 6" />
              </svg>
            </button>
          </div>

        </div>
      </form>

    <?php } else { ?>

      <div class="hero-copy">
        <div class="hero-eyebrow">Your profile is live</div>
        <h1>Welcome back, <?php echo $users['fullname'] ?></h1>
        <p>Recruiters can now see your profile and apply your details when reviewing applications. Keep it up to date for the best results.</p>
      </div>

      <div class="builder-grid">

        <div class="preview-col">
          <div class="preview-label">Preview</div>
          <div class="preview-card">
            <div class="preview-avatar-wrap">
              <?php if (!empty($users["profile_image"])) { ?>
                <img class="preview-avatar" src="../uploads/profile/<?php echo $users["profile_image"] ?>" alt="Current Photo">
              <?php } else { ?>
                <div class="preview-avatar-fallback"><?php echo strtoupper(substr($users["fullname"], 0, 1)) ?></div>
              <?php } ?>
            </div>
            <div class="preview-body">
              <div class="preview-name"><?php echo $users["fullname"] ?></div>
              <div class="preview-role"><?php echo $users["role"] ?></div>

              <div class="preview-badges">
                <span class="preview-badge"><?php echo $users["experience"] ?></span>
              </div>
            </div>
          </div>
        </div>

        <div class="summary-card">
          <div class="summary-head">
            <div>
              <h2>Profile Overview</h2>
              <p>This is what employers see when they view your application</p>
            </div>
            <a href="applicantDashboard.php?edit=1" class="btn-edit-profile text-white">
              Edit Profile
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 20h9" />
                <path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
              </svg>
            </a>
          </div>

          <div class="summary-block">
            <div class="summary-label">About</div>
            <div class="summary-text"><?php echo $users['bio'] ?></div>
          </div>

          <div class="summary-block">
            <div class="summary-label">Skills</div>
            <div class="summary-text"><?php echo $users['skills'] ?></div>
          </div>

          <div class="summary-block">
            <div class="summary-label">Experience</div>
            <div class="summary-text"><?php echo $users['experience'] ?></div>
          </div>

          <div class="summary-block">
            <div class="summary-label">Resume</div>
            <div class="summary-text">
              <a href="../uploads/cv/<?php echo $users['cv_file'] ?>" target="_blank" style="color: var(--accent-color-dark); font-weight:600; text-decoration:none;">
                <?php echo $users['cv_file'] ?> — View / Download
              </a>
            </div>
          </div>
        </div>

      </div>

    <?php } ?>

  </div>

</body>

</html>
<?php
include "../includes/footer.php";
?>