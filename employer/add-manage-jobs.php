<?php
session_start();
include "../../job-portal/includes/config.php";
if (!isset($_SESSION["user_id"])) {
  header("Location: ../login.php");
  exit();
  };
  if ($_SESSION["role"] != "employer" && $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
      exit();
    };

$userid = $_SESSION['user_id'];
$username = $_SESSION['user_name'];
$userrole = $_SESSION['role'];
//   global $conn ; if you want  to remove these red erors just uncomment the global variable 
$dataquery = "select * from users where id = '$userid' ";
$dataqueryresult = mysqli_query($conn, $dataquery);

if($dataqueryresult) {
  $user = mysqli_fetch_assoc($dataqueryresult);
} 
else {
  echo "data query failed" . mysqli_error($conn);
  };

$categoryQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);
if (isset($_POST['post_job'])) {
  $status = mysqli_real_escape_string($conn,$_POST['status']);
  $jobTitle = mysqli_real_escape_string($conn,$_POST['job_title']);
  $companyName = mysqli_real_escape_string($conn,$_POST['company_name']);
  $location = $_POST['location'];
  $category_id = mysqli_real_escape_string($conn,$_POST['category_id']);
  $salary = mysqli_real_escape_string($conn,$_POST['salary']);
  $job_type = mysqli_real_escape_string($conn,$_POST['job_type']);
  $description = mysqli_real_escape_string($conn,$_POST['description']);

  $sql = "insert into jobs (user_id,title,company,location,salary,job_type,description,category_id,status) VALUES ($userid,'$jobTitle','$companyName','$location','$salary','$job_type','$description',$category_id,'$status') ";

  $result = mysqli_query($conn,$sql);


  if ($result) {
    echo "Job posted successfully";
    header("Location: ./add-manage-jobs.php?tab=manage-jobs");;
    exit();
    } else {
    echo "Job posting failed" . mysqli_error($conn);
  };
};

$jobquery = " select * from jobs where user_id = '$userid' ";
$result = mysqli_query($conn, $jobquery);

$jobquerycount = " select count(*) from jobs where user_id = '$userid' ";
$jobquerycountresult = mysqli_query($conn, $jobquerycount);
$currentjobs = mysqli_fetch_assoc($jobquerycountresult);

$applicationquery = "select count(*) from applications inner join jobs on applications.job_id = jobs.id where jobs.user_id = $userid ";
$applicationresult = mysqli_query($conn,$applicationquery);
$applications = mysqli_fetch_assoc($applicationresult);

$acceptapplicationquery = "select count(*) from applications inner join jobs on applications.job_id = jobs.id where applications.status = 'accepted' and jobs.user_id = $userid ";
$acceptapplicationresult = mysqli_query($conn,$acceptapplicationquery);
$acceptapplication = mysqli_fetch_assoc($acceptapplicationresult);

$rejectapplicationquery = "select count(*) from applications inner join jobs on applications.job_id = jobs.id where applications.status = 'rejected' and jobs.user_id = $userid ";
$rejectapplicationresult = mysqli_query($conn,$rejectapplicationquery);
$rejectapplication = mysqli_fetch_assoc($rejectapplicationresult);
?>

<title>OrbitJobs — Job Seeker Portal</title>
<style>
  /* *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }  */
  
  :root {
    --bg: #f1f5f9;
    --default: #475569;
    --heading: #1e293b;
    --accent: #0ea5e9;
    --accent-dark: #0284c7;
    --accent-soft: rgba(14, 165, 233, .10);
    --surface: #ffffff;
    --contrast: #ffffff;
    --border: #e2e8f0;
    --sidebar-w: 260px;
    --shadow-sm: 0 2px 12px rgba(14, 165, 233, .07);
    --shadow-md: 0 8px 32px rgba(30, 41, 59, .10);
    --shadow-lg: 0 20px 60px rgba(30, 41, 59, .14);
    --r: 14px;
    --r-sm: 10px;
  }

  /* body {
  font-family: 'Lato', sans-serif;
  background: var(--bg);
  color: var(--default);
  min-height: 100vh;
  display: flex;
} */
  .side-body {
    position: relative;
    margin-top: 30px;
  }

  .sidebar {
    width: var(--sidebar-w);
    min-height: 100vh;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: absolute;
    /*  fixed */
    margin-top: 30px;
    top: 0;
    left: 0;
    z-index: 50;
    box-shadow: 4px 0 24px rgba(14, 165, 233, .06);
  }

  /* Logo */
  .sidebar-logo {
    padding: 1.6rem 1.5rem 1.4rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .logo-mark {
    width: 36px;
    height: 36px;
    background: var(--accent);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  /* .logo-mark svg { width: 20px; height: 20px; color: #fff; }  */

  .logo-text {
    font-family: 'Raleway', sans-serif;
    font-weight: 800;
    font-size: 1.15rem;
    color: var(--heading);
  }

  .logo-text span {
    color: var(--accent);
  }

  /* User Profile Card */
  .user-card {
    margin: 1.2rem 1rem;
    padding: 1rem;
    background: var(--accent-soft);
    border-radius: var(--r-sm);
    border: 1px solid rgba(14, 165, 233, .15);
  }

  .user-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Raleway', sans-serif;
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
    margin-bottom: 10px;
    border: 3px solid rgba(14, 165, 233, .25);
  }

  .user-name {
    font-family: 'Raleway', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--heading);
    margin-bottom: 2px;
  }

  .user-role {
    font-size: 0.78rem;
    color: var(--accent);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .user-role svg {
    width: 12px;
    height: 12px;
  }

  .user-id {
    margin-top: 8px;
    padding: 5px 10px;
    background: var(--surface);
    border-radius: 50px;
    font-size: 0.72rem;
    color: var(--default);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .user-id svg {
    width: 11px;
    height: 11px;
    color: var(--accent);
  }

  /* Nav Menu */
  .sidebar-nav {
    padding: 0.75rem 1rem;
    flex: 1;
  }

  .nav-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #94a3b8;
    padding: 0.5rem 0.5rem 0.6rem;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 11px 14px;
    border-radius: var(--r-sm);
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--default);
    cursor: pointer;
    transition: all .22s ease;
    margin-bottom: 3px;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    position: relative;
  }

  .nav-item svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    transition: transform .22s;
  }

  .nav-item:hover {
    background: var(--accent-soft);
    color: var(--accent);
  }

  .nav-item:hover svg {
    transform: translateX(2px);
  }

  .nav-item.active {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 6px 20px rgba(14, 165, 233, .30);
  }

  .nav-item .badge {
    margin-left: auto;
    background: rgba(255, 255, 255, .25);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 50px;
    min-width: 22px;
    text-align: center;
  }

  .nav-item:not(.active) .badge {
    background: var(--accent-soft);
    color: var(--accent);
  }

  /* Sidebar Footer */
  .sidebar-footer {
    padding: 1rem;
    border-top: 1px solid var(--border);
  }

  .sidebar-footer-text {
    font-size: 0.75rem;
    color: #94a3b8;
    text-align: center;
  }

  /* ═══════════════════════════════
   MAIN CONTENT
═══════════════════════════════ */
  .content-area {
    margin-left: var(--sidebar-w);
    flex: 1;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .page {
    display: none;
    flex-direction: column;
    min-height: 100vh;
  }

  .page.active {
    display: flex;
  }

  /* ═══════════════════════════════
   PAGE BODY
═══════════════════════════════ */
  .page-body {
    padding: 2.5rem 3rem;
    flex: 1;
  }

  /* ═══════════════════════════════
   ADD JOB LAYOUT
═══════════════════════════════ */
  .add-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 2rem;
    align-items: start;
  }

  /* ─── FORM CARD ─── */
  .form-card {
    background: var(--surface);
    border-radius: var(--r);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-md);
    overflow: hidden;
  }

  .form-card-head {
    padding: 1.75rem 2rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, #fafcff 0%, #f0f9ff 100%);
  }

  .fch-icon {
    width: 48px;
    height: 48px;
    background: var(--accent-soft);
    border-radius: var(--r-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(14, 165, 233, .2);
  }

  .fch-icon svg {
    width: 24px;
    height: 24px;
    color: var(--accent);
  }

  .fch-icon h3 {
    font-family: 'Raleway', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 2px;
  }

  .fch-icon p {
    font-size: 0.82rem;
    color: var(--default);
  }

  .fch-text h3 {
    font-family: 'Raleway', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 2px;
  }

  .fch-text p {
    font-size: 0.82rem;
    color: var(--default);
  }

  .form-body {
    padding: 2rem;
  }

  /* Form Groups */
  .form-group {
    margin-bottom: 1.3rem;
  }

  .form-group label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 7px;
    letter-spacing: .2px;
  }

  .form-group label .req {
    color: var(--accent);
    margin-left: 2px;
  }

  .inp-wrap {
    position: relative;
  }

  .inp-wrap>svg:first-child {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: #94a3b8;
    pointer-events: none;
    transition: color .2s;
    z-index: 1;
  }

  .inp-wrap.textarea-wrap>svg:first-child {
    top: 15px;
    transform: none;
  }

  .inp-wrap input,
  .inp-wrap textarea {
    width: 100%;
    height: 46px;
    padding: 0 14px 0 40px;
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    font-family: 'Lato', sans-serif;
    font-size: 0.9rem;
    color: var(--heading);
    background: #f8fafc;
    outline: none;
    transition: all .2s ease;
  }

  .inp-wrap textarea {
    height: auto;
    min-height: 120px;
    padding: 13px 14px 13px 40px;
    resize: vertical;
    line-height: 1.65;
  }

  .inp-wrap input::placeholder,
  .inp-wrap textarea::placeholder {
    color: #94a3b8;
  }

  .inp-wrap input:focus,
  .inp-wrap textarea:focus {
    border-color: var(--accent);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, .11);
  }

  .inp-wrap:focus-within>svg:first-child {
    color: var(--accent);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .btn-post {
    width: 100%;
    height: 50px;
    margin-top: 0.25rem;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: var(--r-sm);
    font-family: 'Lato', sans-serif;
    font-size: 0.97rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all .25s ease;
    letter-spacing: .3px;
  }

  .btn-post svg {
    width: 19px;
    height: 19px;
    transition: transform .25s;
  }

  .btn-post:hover {
    background: var(--accent-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(14, 165, 233, .38);
  }

  .btn-post:hover svg {
    transform: translateX(4px);
  }

  .btn-post:active {
    transform: translateY(0);
  }

  /* ─── RIGHT SIDEBAR ─── */
  .right-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  .side-widget {
    background: var(--surface);
    border-radius: var(--r);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .widget-head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fafbfc;
  }

  .widget-head svg {
    width: 16px;
    height: 16px;
    color: var(--accent);
  }

  .widget-head h4 {
    font-family: 'Raleway', sans-serif;
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--heading);
  }

  .widget-body {
    padding: 1.25rem;
  }

  /* Stats Widget */
  .stats-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1px;
    background: var(--border);
    border-radius: var(--r-sm);
    overflow: hidden;
  }

  .stat-cell {
    background: var(--surface);
    padding: 1rem;
    text-align: center;
  }

  .stat-cell .n {
    display: block;
    font-family: 'Raleway', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--accent);
    margin-bottom: 2px;
  }

  .stat-cell .l {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--default);
    text-transform: uppercase;
    letter-spacing: .5px;
  }

  /* Tips Widget */
  .tips-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 9px;
  }

  .tips-list li {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    font-size: 0.83rem;
    color: var(--default);
    line-height: 1.5;
  }

  .tips-list li::before {
    content: '';
    width: 6px;
    height: 6px;
    background: var(--accent);
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
  }

  /* Profile Widget */
  .profile-mini {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1rem;
  }

  .pm-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Raleway', sans-serif;
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
    flex-shrink: 0;
  }

  .pm-name {
    font-family: 'Raleway', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--heading);
  }

  .pm-role {
    font-size: 0.78rem;
    color: var(--accent);
    font-weight: 600;
  }

  .profile-detail-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: var(--default);
    padding: 5px 0;
  }

  .profile-detail-row svg {
    width: 14px;
    height: 14px;
    color: var(--accent);
    flex-shrink: 0;
  }

  /* Manage job */
  /* ===========================
   HERO SECTION
=========================== */

  .hero {
    padding: 3rem;
    background: linear-gradient(135deg,
        #f8fcff 0%,
        #eef8ff 50%,
        #ffffff 100%);
    border-bottom: 1px solid var(--border);
    position: relative;
    overflow: hidden;
  }

  .hero::before {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: rgba(14, 165, 233, .06);
    top: -180px;
    right: -120px;
  }

  .hero-inner {
    display: grid;
    grid-template-columns: 1.4fr .8fr;
    gap: 2rem;
    align-items: center;
    position: relative;
    z-index: 2;
  }

  /* ===========================
   GREETING
=========================== */

  .hero-greeting {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    background: rgba(14, 165, 233, .08);
    border: 1px solid rgba(14, 165, 233, .15);
    border-radius: 50px;
    color: var(--accent);
    font-size: .85rem;
    font-weight: 700;
    margin-bottom: 1.2rem;
  }

  .hero-greeting svg {
    width: 18px;
    height: 18px;
    stroke-width: 2.3;
  }

  /* ===========================
   HEADING
=========================== */

  .hero h1 {
    font-family: 'Raleway', sans-serif;
    font-size: 3rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--heading);
    margin-bottom: 1rem;
  }

  .hero h1 .accent {
    color: var(--accent);
  }

  .hero-sub {
    max-width: 650px;
    color: var(--default);
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 2rem;
  }

  /* ===========================
   STATS
=========================== */

  .hero-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .hero-stat {
    min-width: 120px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
  }

  .hero-stat .num {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--accent);
    font-family: 'Raleway', sans-serif;
  }

  .hero-stat .lbl {
    display: block;
    margin-top: 4px;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #64748b;
  }

  /* ===========================
   RIGHT CARDS
=========================== */

  .hero-cards {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .hero-info-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
    transition: .3s ease;
  }

  .hero-info-card:hover {
    transform: translateY(-4px);
    border-color: rgba(14, 165, 233, .20);
    box-shadow: 0 18px 40px rgba(14, 165, 233, .12);
  }

  /* ===========================
   ICON BOX
=========================== */

  .hi-icon {
    width: 58px;
    height: 58px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 16px;

    background: linear-gradient(135deg,
        rgba(14, 165, 233, .14),
        rgba(14, 165, 233, .05));

    border: 1px solid rgba(14, 165, 233, .15);
  }

  .hi-icon svg {
    width: 25px;
    height: 25px;
    color: var(--accent);
    stroke-width: 2.3;
    transition: .3s ease;
  }

  .hero-info-card:hover .hi-icon svg {
    transform: scale(1.15);
  }

  /* ===========================
   TEXT
=========================== */

  .hi-label {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #94a3b8;
    margin-bottom: 3px;
  }

  .hi-val {
    font-size: 1rem;
    font-weight: 700;
    color: var(--heading);
  }

  /* ===========================
   SECTION HEADER
=========================== */

  .section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }

  .section-head h2 {
    font-size: 1.8rem;
    font-family: 'Raleway', sans-serif;
    font-weight: 800;
    color: var(--heading);
  }

  .section-head h2 span {
    color: var(--accent);
  }

  /* ===========================
   COUNT PILL
=========================== */

  .count-pill {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    background: rgba(14, 165, 233, .08);
    border: 1px solid rgba(14, 165, 233, .15);
    border-radius: 50px;
    color: var(--accent);
    font-weight: 700;
  }

  .count-pill svg {
    width: 18px;
    height: 18px;
    stroke-width: 2.3;
  }

  /* ===========================
   RESPONSIVE
=========================== */

  @media(max-width:900px) {

    .hero-inner {
      grid-template-columns: 1fr;
    }

    .hero h1 {
      font-size: 2.3rem;
    }

    .hero-cards {
      margin-top: 1rem;
    }
  }

  @media(max-width:700px) {

    .hero {
      padding: 2rem 1.2rem;
    }

    .hero h1 {
      font-size: 1.9rem;
    }

    .hero-stats {
      flex-direction: column;
    }

    .hero-stat {
      width: 100%;
    }
  }

  /* Toast */
  .toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: #059652;
    color: #fff;
    padding: 0.9rem 1.4rem;
    border-radius: var(--r-sm);
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 0.88rem;
    box-shadow: 0 8px 24px rgba(5, 150, 82, .28);
    z-index: 999;
    opacity: 0;
    transform: translateY(16px);
    pointer-events: none;
    transition: all .3s cubic-bezier(.25, .46, .45, .94);
  }

  .toast.show {
    opacity: 1;
    transform: translateY(0);
  }

  .toast svg {
    width: 18px;
    height: 18px;
  }

  /* ═══════════════════════════════
   RESPONSIVE
═══════════════════════════════ */
  @media (max-width: 1100px) {
    .add-layout {
      grid-template-columns: 1fr;
    }

    .right-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
    }
  }

  @media (max-width: 860px) {
    .hero-inner {
      grid-template-columns: 1fr;
    }

    .hero-cards {
      flex-direction: row;
      flex-wrap: wrap;
    }
  }

  @media (max-width: 768px) {
    :root {
      --sidebar-w: 220px;
    }

    .hero {
      padding: 2.5rem 1.5rem;
    }

    .page-body {
      padding: 2rem 1.5rem;
    }

    .form-row {
      grid-template-columns: 1fr;
    }

    .right-col {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 700px) {
    .sidebar {
      width: 100%;
      min-height: auto;
      position: relative;
    }

    .content-area {
      margin-left: 0;
    }

    body {
      flex-direction: column;
    }

    .hero {
      padding: 2rem 1.25rem;
    }

    .hero h1 {
      font-size: 1.7rem;
    }

    .page-body {
      padding: 1.5rem 1.25rem;
    }

    .jobs-grid {
      grid-template-columns: 1fr;
    }
  }

  /* // svg  */
</style>
</head>
<?php
    include "../../job-portal/includes/header.php";
?>
  <!-- ══ LEFT SIDEBAR ══ -->
  <div class="side-body">
    <aside class="sidebar">
      <div class="sidebar-logo">
        <div class="logo-mark">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="3" />
            <path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" />
          </svg>
        </div>
        <span class="logo-text">Orbit<span>Jobs</span></span>
      </div>

      <!-- User Profile -->
      <div class="user-card">
        <div class="user-avatar">AS</div>
        <div class="user-name">Ahmed Saleem</div>
        <div class="user-role">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <circle cx="12" cy="12" r="4" />
          </svg>
          Job Seeker
        </div>
        <div class="user-id">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="5" width="20" height="14" rx="2" />
            <path d="M2 10h20" />
          </svg>
          ID: JS-20294
        </div>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>

        <button id="showjobBtn" class="nav-item active" onclick="window.location.href='add-manage-jobs.php?tab=add-job';">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
          Add Jobs
        </button>

        <button id="showmanageBtn" class="nav-item" onclick="window.location.href='add-manage-jobs.php?tab=manage-jobs';">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
            <rect x="9" y="3" width="6" height="4" rx="1" />
            <line x1="9" y1="12" x2="15" y2="12" />
            <line x1="9" y1="16" x2="13" y2="16" />
          </svg>
          Manage Jobs
          <span class="badge" id="sidebarBadge">0</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-footer-text">OrbitJobs Portal v1.0</div>
      </div>
    </aside>

  </div>

  <!-- ══ CONTENT AREA ══ -->
  <div class="content-area">


    <!-- ════ ADD JOB PAGE ════ -->
    <div id="add-job" class="page active">

      <!-- Form -->
      <div class="page-body">
        <div class="add-layout">

          <!-- FORM -->
          <div class="form-card">
            <div class="form-card-head">
              <div class="fch-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                  <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                </svg>
              </div>
              <div class="fch-text">
                <h3>Job Listing Details</h3>
                <p>Fields marked <span style="color:var(--accent)">*</span> are required</p>
              </div>
            </div>

            <div class="form-body">
              <form id="jobForm" method="POST">

                <!-- Job Title -->
                <div class="form-group">
                  <label for="jobTitle">Job Title <span class="req">*</span></label>
                  <div class="inp-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 2a5 5 0 015 5v2H7V7a5 5 0 015-5z" />
                      <rect x="3" y="9" width="18" height="13" rx="2" />
                    </svg>
                    <input type="text" id="jobTitle" name="job_title" placeholder="e.g. Senior Frontend Developer" required autocomplete="off">
                  </div>
                </div>

                <!-- Company + Location -->
                <div class="form-row">
                  <div class="form-group">
                    <label for="companyName">Company Name <span class="req">*</span></label>
                    <div class="inp-wrap">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="15" rx="2" />
                        <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                      </svg>
                      <input type="text" id="companyName" name="company_name" placeholder="e.g. Acme Corp" required autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="location">Location <span class="req">*</span></label>
                    <div class="inp-wrap">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z" />
                        <circle cx="12" cy="10" r="3" />
                      </svg>
                      <input type="text" id="location" name="location" placeholder="e.g. Karachi, Pakistan" required autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="d-flex gap-3 mb-2">
                  <!-- Categry -->
                   <div>
                     <label for="select-category">Select Category</label>
                                <select name="category_id" class="form-select" style="max-width: 260px; padding: 8px 14px; font-size: 14px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.15); background-color: var(--surface-color); color: var(--default-color); outline: none;">
                      
                       <option selected disabled>Select Category</option>
                       <?php
                       while ($categories = mysqli_fetch_assoc($categoryResult)) { ?>
                         <option value="<?php echo $categories["id"] ?>"><?php echo $categories["name"] ?></option>
                       <?php
                       }
                       ?>
                     </select>
                   </div>
                  <!-- Status -->
                   <div>
                     <label for="select-status">Status</label>
                    <select name="status" class="form-select" style="max-width: 260px; font-size: 14px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.15); background-color: var(--surface-color); color: var(--default-color); outline: none;">
                      <option value="open" selected>Open </option>
                      <option value="closed">Closed</option>
                    </select>
                   </div>

                </div>

              <!-- Salary -->
              <div class="form-group">
                <label for="salary">Salary <span class="req">*</span></label>
                <div class="inp-wrap">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                  </svg>
                  <input type="text" id="salary" name="salary" placeholder="e.g. PKR 80,000 – 120,000 / month" required autocomplete="off">
                </div>
              </div>
<!-- Job Time / Type -->
 <div class="col-md-6">
    <label class="form-label">Job Type</label>
    <select name="job_type" class="form-select" required>
        <option value="" disabled>Select Job Type</option>
        <option value="Full Time">Full Time</option>
        <option value="Part Time">Part Time</option>
        <option value="Internship">Internship</option>
        <option value="Contract">Contract</option>
        <option value="Remote">Remote</option>
        <option value="Hybrid">Hybrid</option>
    </select>
</div>
              <!-- Description -->
              <div class="form-group">
                <label for="description">Job Description <span class="req">*</span></label>
                <div class="inp-wrap textarea-wrap">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                  </svg>
                  <textarea id="description" name="description" placeholder="Describe role, responsibilities, requirements…" required rows="5"></textarea>
                </div>
              </div>

              <button type="submit" class="btn-post" name="post_job">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M22 2L11 13" />
                  <path d="M22 2L15 22 11 13 2 9l20-7z" />
                </svg>
                Post Job
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </button>

              </form>
            </div>
          </div>

          <!-- RIGHT COL -->
          <div class="right-col">

            <!-- Stats -->
            <div class="side-widget">
              <div class="widget-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 20V10M12 20V4M6 20v-6" />
                </svg>
                <h4>Your Activity</h4>
              </div>
              <div class="widget-body">
                <div class="stats-2col">
                  <div class="stat-cell"><span class="n" id="wTotal">0</span><span class="l">Posted</span></div>
                  <div class="stat-cell"><span class="n" id="wActive">0</span><span class="l">Active</span></div>
                </div>
              </div>
            </div>

            <!-- Profile -->
            <div class="side-widget">
              <div class="widget-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
                <h4>Your Profile</h4>
              </div>
              <div class="widget-body">
                <div class="profile-mini">
                  <div class="pm-avatar">AS</div>
                  <div>
                    <div class="pm-name">Ahmed Saleem</div>
                    <div class="pm-role">Job Seeker</div>
                  </div>
                </div>
                <div class="profile-detail-row">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2" />
                    <path d="M2 10h20" />
                  </svg>
                  ID: JS-20294
                </div>
                <div class="profile-detail-row">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z" />
                    <circle cx="12" cy="10" r="3" />
                  </svg>
                  Karachi, Pakistan
                </div>
                <div class="profile-detail-row">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                  </svg>
                  ahmed@email.com
                </div>
              </div>
            </div>

            <!-- Tips -->
            <div class="side-widget">
              <div class="widget-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="12" />
                  <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <h4>Tips for a Great Listing</h4>
              </div>
              <div class="widget-body">
                <ul class="tips-list">
                  <li>Use a clear, specific job title to attract the right candidates</li>
                  <li>Always mention whether the role is remote, hybrid, or on-site</li>
                  <li>Stating salary increases applications by up to 40%</li>
                  <li>List required skills and preferred qualifications separately</li>
                  <li>Keep the description concise — under 300 words works best</li>
                </ul>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- ════ MANAGE JOBS PAGE ════ -->
    <div id="manage-jobs" class="page w-90 mx-3 overflow-hidden">

      <section class="hero">
        <div class="hero-inner">
          <div>
            <div class="hero-greeting">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                <rect x="9" y="3" width="6" height="4" rx="1" />
              </svg>
              Your Job Listings
            </div>
            <h1>Manage Your<br><span class="accent">Posted Jobs</span></h1>
            <p class="hero-sub">View, edit, or remove the job listings you have published. All your postings in one place.</p>
            <div class="hero-stats">
              <div class="hero-stat"><span class="num" id="mHeroTotal"><?php echo $currentjobs['count(*)'] ?></span><span class="lbl">Total Jobs</span></div>
              <div class="hero-stat"><span class="num" id="mHeroActive"><?php echo $applications['count(*)'] ?></span><span class="lbl">Total Applications</span></div>
              <div class="hero-stat"><span class="num"><?php echo $acceptapplication['count(*)'] ?></span><span class="lbl">Accepted</span></div>
              <div class="hero-stat"><span class="num"><?php echo $rejectapplication['count(*)'] ?></span><span class="lbl">Rejected</span></div>
            </div>
          </div>
          <div class="hero-cards">
            <div class="hero-info-card">
              <div class="hi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg></div>
              <div class="hi-text">
                <div class="hi-label">Name</div>
                <div class="hi-val"><?php echo $user['fullname'] ?></div>
              </div>
            </div>
            <div class="hero-info-card">
              <div class="hi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2a5 5 0 015 5v2H7V7a5 5 0 015-5z" />
                  <rect x="3" y="9" width="18" height="13" rx="2" />
                </svg></div>
              <div class="hi-text">
                <div class="hi-label">Role</div>
                <div class="hi-val"><?php echo $user['role'] ?></div>
              </div>
            </div>
            <div class="hero-info-card">
              <div class="hi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="5" width="20" height="14" rx="2" />
                  <path d="M2 10h20" />
                </svg></div>
              <div class="hi-text">
                <div class="hi-label">ID</div>
                <div class="hi-val"><?php echo $user['id'] ?></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="page-body container-fluid px-3">
        <div class="section-head d-flex justify-content-between align-items-center mb-3">
          <h2>All <span>Listings</span></h2>
          <div class="count-pill" id="countPill">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
              <rect x="9" y="3" width="6" height="4" rx="1" />
            </svg>
            <?php echo mysqli_num_rows($result) ?> listings
          </div>
        </div>

        <div class="table-responsive shadow-sm rounded border bg-white w-100">
          <table class="table table-striped table-hover align-middle mb-0" style="min-width: 600px;">
            <thead class="table-light">
              <tr>
                <th scope="col" style="padding: 12px 16px; width:10%; ">User_Id</th>
                <th scope="col" style="width: 20%;">Title</th>
                <th scope="col" style="width: 25%;">Company</th>
                <th scope="col" style="width: 15%;">Salary</th>
                <th scope="col" class="text-center" style="width: 30% ; ">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while ($jobs = mysqli_fetch_assoc($result)) {
              ?>
                <tr>
                  <td style="padding: 12px 16px;"> <?php echo $jobs['user_id'] ?> </td>
                  <td class="fw-semibold text-secondary"> <?php echo $jobs['title'] ?> </td>
                  <td> <?php echo $jobs['company'] ?> </td>
                  <td class="text-success fw-medium"> <?php echo $jobs['salary'] ?> </td>
                  <td style="text-align: center; vertical-align: middle;">
                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">

                      <a href="./edit-job.php?id=<?php echo $jobs['id']; ?>"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; color: #3b82f6; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; text-decoration: none; transition: all 0.2s ease;"
                        onmouseover="this.style.backgroundColor='#3b82f6'; this.style.color='#ffffff';"
                        onmouseout="this.style.backgroundColor='#eff6ff'; this.style.color='#3b82f6';">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                          <path d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Edit
                      </a>

                      <a href="./delete-job.php?id=<?php echo $jobs['id']; ?>"
                        onclick="return confirm('Are you sure you want to delete this job?')"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; color: #ef4444; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; text-decoration: none; transition: all 0.2s ease;"
                        onmouseover="this.style.backgroundColor='#ef4444'; this.style.color='#ffffff';"
                        onmouseout="this.style.backgroundColor='#fef2f2'; this.style.color='#ef4444';">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                          <polyline points="3 6 5 6 21 6" />
                          <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                          <line x1="10" y1="11" x2="10" y2="17" />
                          <line x1="14" y1="11" x2="14" y2="17" />
                        </svg>
                        Delete
                      </a>

                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  </div>

  </div>

  <!-- Toast -->
  <div class="toast" id="toast">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <polyline points="20 6 9 17 4 12" />
    </svg>
    Job posted successfully!
  </div>

  <script>
    const jobPage = document.getElementById("add-job");
    const managejobPage = document.getElementById("manage-jobs");
    const showjobBtn = document.getElementById("showjobBtn");
    const showmanageBtn = document.getElementById("showmanageBtn");
    //  function showjobPage(){
    //         jobPage.style.display = "block";
    //         managejobPage.style.display = "none";

    //         document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
    //         event.currentTarget.classList.add('active');
    //     }

    //     function showmanageJob(){
    //         managejobPage.style.display = "block";
    //         jobPage.style.display = "none";

    //         document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
    //         event.currentTarget.classList.add('active');
    //     }
    let tab = "<?php echo $_GET['tab'] ?? 'add-job';  ?>";
    if (tab == 'add-job') {
      jobPage.style.display = "block";
      managejobPage.style.display = "none";
      document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
      showjobBtn.classList.add('active');
    }
    if (tab == 'manage-jobs') {
      managejobPage.style.display = "block";
      jobPage.style.display = "none";
      document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
      showmanageBtn.classList.add('active');
    }
    console.log(tab);
  </script>

</body>

</html>

<?php
include "../includes/footer.php";
?>