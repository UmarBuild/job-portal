<?php
session_start();
include "includes/config.php";

$categoryQuery = "SELECT * FROM categories";

$categoryResult = mysqli_query($conn, $categoryQuery);

if (isset($_GET["submit-filter"]) && !empty($_GET["filter"])) {
  $filter = $_GET["filter"];
  $filterquery = "SELECT categories.name AS cat_name,jobs.* from categories inner join jobs on jobs.category_id = categories.id where category_id = $filter and jobs.status = 'open' ";
  $result = mysqli_query($conn, $filterquery);
  if (!$result) {
    echo "Filter Failed" . mysqli_error($conn);
  }
    } else {
      $showquery = "SELECT categories.name AS cat_name, jobs.* FROM categories 
                  INNER JOIN jobs ON jobs.category_id = categories.id 
                  where jobs.status = 'open' ORDER BY jobs.id DESC";
      $result = mysqli_query($conn, $showquery);
}
if(isset($_GET["submit-search"])){
$search = $_GET["search"];  
$query = "select *,categories.name AS cat_name from jobs inner join categories on jobs.category_id = categories.id where title like '%$search%' and jobs.status = 'open' ";
$result = mysqli_query($conn, $query);
if (!$result) {
echo "There Is An Error In Search Query". mysqli_error($conn);
}
}
?>



<head>
<style>
  /* ===== JOBS PAGE STYLES ===== */

  /* Hero Section */
  .jobs-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0c2340 100%);
    position: relative;
    padding: 100px 0 80px;
    overflow: hidden;
  }

  .jobs-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse 80% 60% at 70% 50%, rgba(14, 165, 233, 0.13) 0%, transparent 70%),
      radial-gradient(ellipse 40% 40% at 20% 80%, rgba(14, 165, 233, 0.08) 0%, transparent 60%);
    pointer-events: none;
  }

  .jobs-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
      linear-gradient(rgba(14, 165, 233, 0.07) 1px, transparent 1px),
      linear-gradient(90deg, rgba(14, 165, 233, 0.07) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
  }

  .jobs-hero .container {
    position: relative;
    z-index: 2;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(14, 165, 233, 0.15);
    border: 1px solid rgba(14, 165, 233, 0.35);
    color: #38bdf8;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 7px 18px;
    border-radius: 50px;
    margin-bottom: 28px;
    font-family: var(--nav-font);
  }

  .hero-badge i {
    font-size: 14px;
  }

  .jobs-hero h1 {
    font-family: var(--heading-font);
    font-size: clamp(2.4rem, 5vw, 3.8rem);
    font-weight: 800;
    color: #f8fafc;
    line-height: 1.15;
    margin-bottom: 22px;
    letter-spacing: -1px;
  }

  .jobs-hero h1 .accent {
    color: #0ea5e9;
    position: relative;
  }

  .jobs-hero h1 .accent::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #0ea5e9, #38bdf8);
    border-radius: 2px;
    opacity: 0.5;
  }

  .jobs-hero p {
    color: #94a3b8;
    font-size: 1.1rem;
    line-height: 1.8;
    max-width: 560px;
    margin-bottom: 36px;
    font-family: var(--default-font);
  }

  .hero-search-bar {
    display: flex;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    max-width: 620px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.08);
  }

  .hero-search-bar input {
    flex: 1;
    border: none;
    outline: none;
    padding: 16px 22px;
    font-size: 15px;
    font-family: var(--default-font);
    color: #1e293b;
    background: transparent;
  }

  .hero-search-bar input::placeholder {
    color: #94a3b8;
  }

  .hero-search-bar .search-divider {
    width: 1px;
    background: #e2e8f0;
    margin: 10px 0;
  }

  .hero-search-bar select {
    border: none;
    outline: none;
    padding: 16px 16px;
    font-size: 15px;
    font-family: var(--default-font);
    color: #475569;
    background: transparent;
    cursor: pointer;
    min-width: 130px;
  }

  .hero-search-bar .btn-search {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    border: none;
    padding: 16px 26px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    font-family: var(--nav-font);
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .hero-search-bar .btn-search:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
  }

  .hero-stats {
    display: flex;
    gap: 36px;
    margin-top: 42px;
    flex-wrap: wrap;
  }

  .hero-stat {
    text-align: left;
  }

  .hero-stat .num {
    font-family: var(--heading-font);
    font-size: 1.9rem;
    font-weight: 800;
    color: #f1f5f9;
    line-height: 1;
    margin-bottom: 4px;
  }

  .hero-stat .num span {
    color: #0ea5e9;
  }

  .hero-stat .lbl {
    font-size: 12.5px;
    color: #64748b;
    font-family: var(--nav-font);
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }

  .hero-stat-divider {
    width: 1px;
    background: rgba(255, 255, 255, 0.1);
    align-self: stretch;
    margin: 4px 0;
  }

  /* Hero Right Visual */
  .hero-visual {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 380px;
  }

  .floating-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 18px 22px;
    position: absolute;
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.3);
    transition: transform 0.4s ease;
  }

  .floating-card:hover {
    transform: translateY(-4px);
  }

  .floating-card.fc-main {
    width: 300px;
    top: 20px;
    left: 10px;
    animation: floatA 5s ease-in-out infinite;
  }

  .floating-card.fc-small {
    width: 200px;
    bottom: 40px;
    right: 10px;
    animation: floatB 6s ease-in-out infinite;
  }

  .floating-card.fc-tag {
    width: 155px;
    top: 50%;
    right: -10px;
    transform: translateY(-50%);
    animation: floatC 4.5s ease-in-out infinite;
    text-align: center;
  }

  @keyframes floatA {

    0%,
    100% {
      transform: translateY(0)
    }

    50% {
      transform: translateY(-10px)
    }
  }

  @keyframes floatB {

    0%,
    100% {
      transform: translateY(0)
    }

    50% {
      transform: translateY(-8px)
    }
  }

  @keyframes floatC {

    0%,
    100% {
      transform: translateY(-50%)
    }

    50% {
      transform: translateY(calc(-50% - 8px))
    }
  }

  .fc-company {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
  }

  .fc-logo {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }

  .fc-company-info .fc-title {
    color: #f1f5f9;
    font-size: 14px;
    font-weight: 700;
    font-family: var(--heading-font);
  }

  .fc-company-info .fc-company-name {
    color: #64748b;
    font-size: 12px;
    font-family: var(--default-font);
  }

  .fc-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 10px;
  }

  .fc-badge {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 50px;
    font-family: var(--nav-font);
    font-weight: 600;
  }

  .fc-badge.remote {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.25);
  }

  .fc-badge.full {
    background: rgba(14, 165, 233, 0.15);
    color: #38bdf8;
    border: 1px solid rgba(14, 165, 233, 0.25);
  }

  .fc-badge.senior {
    background: rgba(168, 85, 247, 0.15);
    color: #c084fc;
    border: 1px solid rgba(168, 85, 247, 0.25);
  }

  .fc-salary {
    color: #0ea5e9;
    font-size: 15px;
    font-weight: 700;
    font-family: var(--heading-font);
    margin-top: 8px;
  }

  .fc-small .fc-s-title {
    color: #94a3b8;
    font-size: 11px;
    font-family: var(--nav-font);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
  }

  .fc-small .fc-s-value {
    color: #f1f5f9;
    font-size: 22px;
    font-weight: 800;
    font-family: var(--heading-font);
  }

  .fc-small .fc-s-sub {
    color: #64748b;
    font-size: 12px;
    font-family: var(--default-font);
    margin-top: 2px;
  }

  .fc-bar {
    height: 6px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 3px;
    margin-top: 10px;
    overflow: hidden;
  }

  .fc-bar-fill {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, #0ea5e9, #38bdf8);
    width: 72%;
  }

  .fc-tag .fc-t-icon {
    font-size: 28px;
    margin-bottom: 6px;
  }

  .fc-tag .fc-t-label {
    color: #94a3b8;
    font-size: 11px;
    font-family: var(--nav-font);
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }

  .fc-tag .fc-t-value {
    color: #f1f5f9;
    font-size: 16px;
    font-weight: 700;
    font-family: var(--heading-font);
    margin-top: 2px;
  }

  /* ===== JOBS SECTION ===== */
  .jobs-section {
    padding: 80px 0;
    background: var(--background-color);
  }

  .section-intro {
    margin-bottom: 50px;
  }

  .section-intro .label-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #0ea5e9;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    font-family: var(--nav-font);
    margin-bottom: 12px;
  }

  .section-intro h2 {
    font-family: var(--heading-font);
    font-size: 2.1rem;
    font-weight: 800;
    color: var(--heading-color);
    margin-bottom: 12px;
    letter-spacing: -0.5px;
  }

  .section-intro p {
    color: var(--default-color);
    font-size: 1rem;
    font-family: var(--default-font);
    max-width: 500px;
  }

  /* Filter Tabs */
  .filter-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 36px;
    background: var(--surface-color);
    padding: 8px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    width: fit-content;
    max-width: 100%;
  }

  .filter-tab {
    border: none;
    background: transparent;
    padding: 9px 20px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    font-family: var(--nav-font);
    color: var(--default-color);
    cursor: pointer;
    transition: all 0.25s;
    white-space: nowrap;
  }

  .filter-tab:hover {
    background: #f1f5f9;
    color: var(--heading-color);
  }

  .filter-tab.active {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    box-shadow: 0 4px 14px rgba(14, 165, 233, 0.3);
  }

  /* Job Card */
  .job-card {
    background: var(--surface-color);
    border-radius: 16px;
    padding: 26px 24px;
    border: 1px solid #e2e8f0;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .job-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #0ea5e9, #38bdf8);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s ease;
    border-radius: 3px 3px 0 0;
  }

  .job-card:hover {
    box-shadow: 0 16px 48px rgba(14, 165, 233, 0.12);
    border-color: rgba(14, 165, 233, 0.3);
    transform: translateY(-4px);
  }

  .job-card:hover::before {
    transform: scaleX(1);
  }

  .job-card-head {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 18px;
  }

  .job-logo {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .job-head-info {
    flex: 1;
    min-width: 0;
  }

  .job-title {
    font-family: var(--heading-font);
    font-size: 16px;
    font-weight: 700;
    color: var(--heading-color);
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .job-company {
    font-size: 13px;
    color: var(--default-color);
    font-family: var(--default-font);
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .job-company i {
    color: #0ea5e9;
    font-size: 12px;
  }

  .job-save-btn {
    background: none;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #94a3b8;
    font-size: 15px;
    transition: all 0.25s;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .job-save-btn:hover {
    border-color: #0ea5e9;
    color: #0ea5e9;
    background: rgba(14, 165, 233, 0.06);
  }

  .job-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 16px;
  }

  .job-tag {
    font-size: 11.5px;
    padding: 4px 11px;
    border-radius: 50px;
    font-family: var(--nav-font);
    font-weight: 600;
    letter-spacing: 0.3px;
  }

  .job-tag.type-full {
    background: rgba(14, 165, 233, 0.1);
    color: #0284c7;
    border: 1px solid rgba(14, 165, 233, 0.2);
  }

  .job-tag.type-part {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
  }

  .job-tag.type-remote {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border: 1px solid rgba(34, 197, 94, 0.2);
  }

  .job-tag.type-contract {
    background: rgba(168, 85, 247, 0.1);
    color: #9333ea;
    border: 1px solid rgba(168, 85, 247, 0.2);
  }

  .job-tag.type-intern {
    background: rgba(236, 72, 153, 0.1);
    color: #db2777;
    border: 1px solid rgba(236, 72, 153, 0.2);
  }

  .job-tag.level {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
  }

  .job-meta-row {
    display: flex;
    gap: 16px;
    margin-bottom: 18px;
    flex-wrap: wrap;
  }

  .job-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    color: var(--default-color);
    font-family: var(--default-font);
  }

  .job-meta-item i {
    color: #0ea5e9;
    font-size: 13px;
  }

  .job-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
  }

  .job-salary {
    font-family: var(--heading-font);
    font-size: 17px;
    font-weight: 800;
    color: var(--heading-color);
  }

  .job-salary span {
    font-size: 12px;
    font-weight: 500;
    color: var(--default-color);
    font-family: var(--default-font);
  }

  .btn-view-details {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    padding: 9px 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    font-family: var(--nav-font);
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
  }

  .btn-view-details:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #fff;
    transform: translateX(2px);
    box-shadow: 0 6px 20px rgba(14, 165, 233, 0.35);
    text-decoration: none;
  }

  .btn-view-details i {
    font-size: 14px;
    transition: transform 0.25s;
  }

  .btn-view-details:hover i {
    transform: translateX(3px);
  }

  /* Featured badge */
  .job-featured-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 50px;
    font-family: var(--nav-font);
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }

  /* Posted time */
  .job-posted {
    font-size: 11.5px;
    color: #94a3b8;
    font-family: var(--default-font);
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 14px;
  }

  .job-posted i {
    font-size: 12px;
  }

  /* Load More */
  .load-more-wrap {
    text-align: center;
    margin-top: 50px;
  }

  .btn-load-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid #0ea5e9;
    color: #0ea5e9;
    background: transparent;
    padding: 13px 32px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: var(--nav-font);
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
  }

  .btn-load-more:hover {
    background: #0ea5e9;
    color: #fff;
    box-shadow: 0 8px 24px rgba(14, 165, 233, 0.3);
    text-decoration: none;
  }

  /* Results count */
  .results-count {
    font-family: var(--default-font);
    font-size: 14px;
    color: var(--default-color);
    margin-bottom: 24px;
  }

  .results-count strong {
    color: var(--heading-color);
    font-family: var(--heading-font);
  }

  /* Responsive */
  @media (max-width: 991px) {
    .jobs-hero {
      padding: 70px 0 60px;
    }

    .hero-visual {
      min-height: 260px;
      margin-top: 40px;
    }

    .floating-card.fc-tag {
      display: none;
    }
  }

  @media (max-width: 767px) {
    .hero-stats {
      gap: 20px;
    }

    .hero-stat-divider {
      display: none;
    }

    .hero-visual {
      display: none;
    }

    .hero-search-bar {
      flex-wrap: wrap;
    }

    .hero-search-bar select {
      display: none;
    }

    .filter-tabs {
      overflow-x: auto;
    }
  }
</style>
</head>
<?php include 'includes/header.php'; ?>
<main class="main">

  <!-- Page Title / Breadcrumb -->
  <div class="page-title">
    <div class="breadcrumbs">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#"><i class="bi bi-house"></i> Home</a></li>
          <li class="breadcrumb-item active current">Jobs</li>
        </ol>
      </nav>
    </div>
    <div class="title-wrapper">
      <h1>Job Listings</h1>
      <p>Explore hundreds of opportunities and find the role that fits your ambitions.</p>
    </div>
  </div>


  <section class="jobs-hero">
    <div class="container">
      <div class="row align-items-center gy-4">

        <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
          <div class="hero-badge">
            <i class="bi bi-lightning-charge-fill"></i>
            <span>Hiring Now — 1,200+ Live Roles</span>
          </div>

          <h1>
            Find Your <span class="accent">Dream Job</span><br>
            Build Your Future
          </h1>

          <p>
            We connect ambitious professionals with forward-thinking companies across tech, design, marketing, finance, and beyond. Your next career leap starts right here — search, apply, and get hired faster.
          </p>

          <div class="hero-search-bar">
            <input type="text" placeholder="Job title, skill, or keyword…">
            <div class="search-divider"></div>
            <select>
              <option>All Locations</option>
              <option>Remote</option>
              <option>Karachi</option>
              <option>Lahore</option>
              <option>Islamabad</option>
            </select>
            <button class="btn-search">
              <i class="bi bi-search"></i> Search
            </button>
          </div>

          <div class="hero-stats">
            <div class="hero-stat">
              <div class="num">12<span>k+</span></div>
              <div class="lbl">Active Jobs</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
              <div class="num">3<span>k+</span></div>
              <div class="lbl">Companies</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
              <div class="num">98<span>%</span></div>
              <div class="lbl">Success Rate</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
              <div class="num">50<span>+</span></div>
              <div class="lbl">Industries</div>
            </div>
          </div>
        </div>

        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
          <div class="hero-visual">
            <!-- Floating Card Main -->
            <div class="floating-card fc-main">
              <div class="fc-company">
                <div class="fc-logo" style="background:rgba(14,165,233,0.15);">💼</div>
                <div class="fc-company-info">
                  <div class="fc-title">Senior UX Designer</div>
                  <div class="fc-company-name">TechVenture Labs</div>
                </div>
              </div>
              <div class="fc-badges">
                <span class="fc-badge remote">Remote</span>
                <span class="fc-badge full">Full-Time</span>
                <span class="fc-badge senior">Senior</span>
              </div>
              <div class="fc-salary">$95k – $130k / year</div>
            </div>

            <!-- Floating Card Small -->
            <div class="floating-card fc-small">
              <div class="fc-s-title">Applications Today</div>
              <div class="fc-s-value">284</div>
              <div class="fc-s-sub">↑ 12% from yesterday</div>
              <div class="fc-bar">
                <div class="fc-bar-fill"></div>
              </div>
            </div>

            <!-- Floating Tag -->
            <div class="floating-card fc-tag">
              <div class="fc-t-icon">🚀</div>
              <div class="fc-t-label">New Today</div>
              <div class="fc-t-value">47 Jobs</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>


  <section class="jobs-section" id="jobs-section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row align-items-end mb-3">
        <div class="col-lg-7">
          <div class="section-intro mb-0">
            <div class="label-tag"><i class="bi bi-briefcase"></i> Open Positions</div>
            <h2>Latest Job Opportunities</h2>
            <p>Browse our curated list of openings across top companies. New roles added daily.</p>
          </div>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <form method="GET" action="#jobs-section">
            <label><i class="bi bi-sliders fs-4"></i></label>
            <select name="filter">
              <option selected disabled>Filter Category</option>
              <option value="">All Category</option>
              <?php
              while ($categories = mysqli_fetch_assoc($categoryResult)) { ?>
                <option value="<?php echo $categories["id"] ?>"><?php echo $categories["name"] ?></option>
              <?php
              }
              ?>
            </select>
            <button type="submit" name="submit-filter">Filter Jobs</button>
          </form>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="d-flex justify-content-between align-items-center">
        <div class="filter-tabs" data-aos="fade-up" data-aos-delay="150">
          <button class="filter-tab active" onclick="window.location.href='./jobs.php#jobs-section'">All Jobs</button>
          <!-- <button class="filter-tab">Remote</button>
          <button class="filter-tab">Full-Time</button>
          <button class="filter-tab">Part-Time</button>
          <button class="filter-tab">Contract</button>
          <button class="filter-tab">Internship</button> -->
        </div>
        <div>

        </div>
        <form action="#jobs-section" method="GET" class="d-flex gap-2">
          <input type="search" class="" name="search" id="" value="<?php if(!empty($_GET["search"])){echo $_GET["search"];} ?>">
          <button type="submit" name="submit-search" class="btn btn-outline-dark">Search</button>
        </form>
      </div>
      <div class="results-count" data-aos="fade-up" data-aos-delay="160">
        Showing <strong>12</strong> of <strong>1,248</strong> jobs
      </div>

      <!-- Job Cards Grid -->
      <div class="row g-4">
        <?php while ($jobs = mysqli_fetch_assoc($result)) { ?>
          <!-- Job Card 1 -->
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="job-card">
              <span class="job-featured-badge"><i class="bi bi-star-fill"></i> Featured</span>
              <div class="job-card-head">
                <div class="job-logo" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">🖥️</div>
                <div class="job-head-info">
                  <h5 class="job-title"><?php echo $jobs['title'] ?></h5>
                  <div class="">
                    <div class="job-company"><i class="bi bi-buildings"></i><?php echo $jobs['company'] ?></div>
<span><?php echo $jobs['cat_name'] ?></span>
                  </div>
                </div>

              </div>
              <div class="job-posted"><i class="bi bi-clock"></i> <?php echo $jobs['created_at'];  ?> </div>
              <!-- <div class="job-tags">
              <span class="job-tag type-full">Full-Time</span>
              <span class="job-tag type-remote">Remote</span>
              <span class="job-tag level">Mid-Level</span>
            </div> -->
              <div class="job-meta-row">
                <div class="job-meta-item"><i class="bi bi-geo-alt"></i> <?php echo $jobs['location'] ?> </div>
                <div class="job-meta-item"><i class="bi bi-people"></i> 11-50 emp.</div>
              </div>
              <div class="job-card-footer">
                <div>
                  <div class="job-salary"> <?php echo $jobs['salary'] ?> <span>/ year</span></div>
                </div>
                <a href="/job-portal/job-details.php?id=<?php echo $jobs['id'] ?>" class="btn-view-details">
                  View Details <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        <?php } ?>
      </div><!-- /Row -->

      <!-- Load More -->
      <div class="load-more-wrap" data-aos="fade-up" data-aos-delay="100">
        <a href="#" class="btn-load-more">
          <i class="bi bi-plus-circle"></i> Load More Jobs
        </a>
        <p style="margin-top:14px;font-size:13px;color:#94a3b8;font-family:var(--default-font);">
          Showing 12 of 1,248 results
        </p>
      </div>

    </div>
  </section>
  
  </main>
<?php
include "./includes/footer.php";
?>

