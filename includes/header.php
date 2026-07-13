<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - Orbit Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/job-portal/node_modules/@tabler/icons-webfont/dist/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&family=Lato:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link href="/job-portal/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="/job-portal/assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">Orbit</h1><span>.</span>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "admin") { ?>
            <li><a href="/job-portal/index.php">Home</a></li>
            <li><a href="/job-portal/admin/dashboard.php">Dashboard</a></li>
           <div class="dropdown">
  <button class="btn dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Management
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
    <a type="button" class="dropdown-item" href="/job-portal/admin/manage-users.php">Manage Users</a>
    <a type="button" class="dropdown-item" href="/job-portal/admin/manage-jobs.php">Manage Jobs</a>
    <a type="button" class="dropdown-item" href="/job-portal/admin/manage-applications.php">Manage Applications</a>
    <a type="button" class="dropdown-item" href="/job-portal/admin/manage-categories.php">Manage Categories</a>

  </div>
</div>

            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>
          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "employer") { ?>
            <li><a href="/job-portal/index.php">Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/employer/employer-applications.php">Applications</a></li>
            <li><a href="/job-portal/employer/add-manage-jobs.php">Manage Jobs</a></li>
            <li><a href="/job-portal/employer/employerDashboard.php">Dashboard</a></li>
            <li><a href="/job-portal/employer/company-profile.php">Profile</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>

          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "applicant") { ?>
            <li><a href="/job-portal/index.php" >Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/applicant/applied-jobs.php">Applied Jobs</a></li>
            <li><a href="/job-portal/applicant/saved-jobs.php">Saved Jobs</a></li>
            <li><a href="/job-portal/applicant/applicantDashboard.php">Dashboard</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>

          <?php if (!isset($_SESSION["user_id"])) { ?>
            <li><a href="/job-portal/index.php" >Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <div class="header-buttons">
        <?php if (isset($_SESSION['user_id'])) {
          if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "employer") { ?>
            <a class="btn-getstarted" href="/job-portal/employer/employerDashboard.php">Dashboard</a>
          <?php } ?>
          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "applicant") { ?>
            <a class="btn-getstarted" href="/job-portal/applicant/applicantDashboard.php">Dashboard</a>
          <?php } ?>
          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "admin") { ?>
            <a class="btn-getstarted" href="/job-portal/admin/dashboard.php">Dashboard</a>
          <?php } ?>
          <a class="btn-getstarted" href="/job-portal/logout.php">Logout</a>
        <?php } else { ?>
          <a class="btn-getstarted" href="/job-portal/login.php">Login</a>
          <a class="btn-getstarted" href="/job-portal/register.php">Register</a>
        <?php } ?>
      </div>

    </div>
  </header>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>