<?php
session_start();
include "../includes/config.php"; 
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
};
$categoryQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);

$userid = $_SESSION["user_id"];

$savequery = "SELECT *,saved_jobs.id,categories.name AS cat_name from jobs inner join saved_jobs on jobs.id = saved_jobs.job_id inner join categories on jobs.category_id = categories.id WHERE saved_jobs.job_id = jobs.id and saved_jobs.user_id = $userid " ; 
$result = mysqli_query($conn , $savequery) ;

if (isset($_GET["submit-filter"]) && !empty($_GET["filter"])) {
  $filter = $_GET["filter"];
  $filterquery = "select *,jobs.*,categories.name AS cat_name from saved_jobs inner join jobs on saved_jobs.job_id = jobs.id inner join categories on jobs.category_id = categories.id where saved_jobs.user_id = $userid and jobs.category_id = $filter ";  
  $result = mysqli_query($conn, $filterquery);
  if (!$result) {
    echo "Filter Failed" . mysqli_error($conn);
  }
    } elseif(isset($_GET["submit-filter"]) && empty($_GET["filter"])) {
      $showquery = "select *,jobs.*,categories.name AS cat_name from saved_jobs inner join jobs on saved_jobs.job_id = jobs.id inner join categories on jobs.category_id = categories.id where saved_jobs.user_id = $userid";
      $result = mysqli_query($conn, $showquery);
}elseif(isset($_GET["submit-search"])){
$search = $_GET["search"];  
$query = "select *,saved_jobs.*,categories.name AS cat_name from jobs inner join saved_jobs on jobs.id = saved_jobs.job_id inner join categories on jobs.category_id = categories.id  where title like '%$search%' and saved_jobs.user_id = $userid ";
$result = mysqli_query($conn, $query);
if (!$result) {
echo "There Is An Error In Search Query". mysqli_error($conn);
}
}

?>

<style>
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

    <?php include "../includes/header.php";  ?>
    <main class="main">
    <section class="jobs-section" id="jobs-section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row align-items-end mb-4 pb-3" style="border-bottom: 2px dashed #e2e8f0;">
        <div class="col-lg-7">
          <div class="section-intro mb-0">
            <div class="label-tag" style="background: rgba(14, 165, 233, 0.1); padding: 6px 14px; border-radius: 50px;">
              <i class="bi bi-bookmark-heart-fill"></i> Your Bookmarks
            </div>
            <h2 class="mt-2" style="font-size: 2.4rem; color: var(--heading-color);">Keep Track of Your Saved Jobs</h2>
            <p class="text-muted">Here are the positions you have saved. Review details, track status, or apply when you're ready.</p>
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
          <button class="filter-tab active" onclick="window.location.href='./saved-jobs.php#jobs-section'">All Jobs</button>
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
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="job-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; position: relative;">
              
              <span class="job-featured-badge" style="background: linear-gradient(135deg, #10b981, #059669);"><i class="bi bi-bookmark-fill"></i> Saved</span>
              
              <div class="job-card-head">
                <div class="job-logo" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: var(--accent-color);">💼</div>
                <div class="job-head-info">
                  <h5 class="job-title" style="color: var(--heading-color); font-weight: 700;"><?php echo $jobs['title']; ?></h5>
                  <div>
                    <div class="job-company" style="color: var(--default-color);"><i class="bi bi-buildings"></i> <?php echo htmlspecialchars($jobs['company']); ?></div>
                    <span class="badge bg-light text-secondary mt-1 border" style="font-size: 11px; font-weight: 600;"><?php echo $jobs['cat_name']; ?></span>
                  </div>
                </div>
              </div>

              <div class="job-posted text-muted" style="font-size: 12px; font-weight: 500;">
                <i class="bi bi-clock-history text-warning"></i> Created At: <?php echo $jobs['created_at']; ?>
              </div>

              <div class="job-meta-row">
                <div class="job-meta-item"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $jobs['location']; ?> </div>
                <div class="job-meta-item"><i class="bi bi-briefcase"></i> Full-Time</div>
              </div>

              <div class="job-card-footer" style="background: #f8fafc; margin: 18px -24px -26px -24px; padding: 16px 24px; border-radius: 0 0 16px 16px;">
                <div>
                  <div class="job-salary" style="color: var(--heading-color); font-size: 1.1rem;">
                    <?php echo $jobs['salary']; ?> <span style="font-size: 11px; font-weight: 600;">/ yr</span>
                  </div>
                </div>
                
                <div class="d-flex gap-2">
                  <a href="/job-portal/job-details.php?id=<?php echo $jobs['job_id'] ?>" class="btn-view-details py-2 px-3 rounded-3" style="font-size: 12px;">
                    Apply Now <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>

            </div>
          </div>
        <?php } ?>
      </div>
      <!-- /Row -->

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
include "../includes/footer.php"; 
?>

