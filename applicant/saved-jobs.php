<?php
session_start();
include "../includes/config.php";
require_once "../includes/csrf.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
  header("Location: ../login.php");
  exit();
};

$userid = $_SESSION["user_id"];

if (isset($_POST["remove_saved"])) {
  $token = $_POST['csrf_token'] ?? '';
  if (!verifyCsrfToken($token)) {
    die("Invalid CSRF Token.");
  }

  $savedId = (int)$_POST["saved_id"];
  $deleteQuery = "delete from saved_jobs where id = ? and user_id = ?";
  mysqli_execute_query($conn, $deleteQuery, [$savedId, $userid]);
  header("Location: saved-jobs.php");
  exit();
}

$showCount = isset($_GET["show"]) ? (int)$_GET["show"] : 6;
if ($showCount < 6) {
  $showCount = 6;
}

$categoryQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);

$commonColumns = "jobs.id as job_id, jobs.title, jobs.company, jobs.location, jobs.salary, jobs.job_type,
                   jobs.status as job_status, jobs.created_at, categories.name as cat_name, saved_jobs.id as saved_id";

if (isset($_GET["submit-filter"]) && !empty($_GET["filter"])) {
  $filter = (int)$_GET["filter"];

  $filterquery = "select $commonColumns
                      from saved_jobs
                      inner join jobs on saved_jobs.job_id = jobs.id
                      inner join categories on jobs.category_id = categories.id
                      where saved_jobs.user_id = ? and jobs.category_id = ?
                      order by saved_jobs.id desc limit ?";
  $result = mysqli_execute_query($conn, $filterquery, [$userid, $filter, $showCount]);
  if (!$result) {
    echo "Filter Failed" . mysqli_error($conn);
  }

  $countFilterQuery = "select count(*) as total from saved_jobs inner join jobs on saved_jobs.job_id = jobs.id where saved_jobs.user_id = ? and jobs.category_id = ?";
  $countFilterResult = mysqli_execute_query($conn, $countFilterQuery, [$userid, $filter]);
  $totalSaved = (int)mysqli_fetch_assoc($countFilterResult)['total'];
} elseif (isset($_GET["submit-filter"]) && empty($_GET["filter"])) {
  $showquery = "select $commonColumns
                  from saved_jobs
                  inner join jobs on saved_jobs.job_id = jobs.id
                  inner join categories on jobs.category_id = categories.id
                  where saved_jobs.user_id = ?
                  order by saved_jobs.id desc limit ?";
  $result = mysqli_execute_query($conn, $showquery, [$userid, $showCount]);

  $countQuery = "select count(*) as total from saved_jobs where user_id = ?";
  $countResult = mysqli_execute_query($conn, $countQuery, [$userid]);
  $totalSaved = (int)mysqli_fetch_assoc($countResult)['total'];
} elseif (isset($_GET["submit-search"]) && !empty($_GET["search"])) {
  $search = "%" . trim($_GET["search"]) . "%";

  $query = "select $commonColumns
              from saved_jobs
              inner join jobs on saved_jobs.job_id = jobs.id
              inner join categories on jobs.category_id = categories.id
              where jobs.title like ? and saved_jobs.user_id = ?
              order by saved_jobs.id desc limit ?";
  $result = mysqli_execute_query($conn, $query, [$search, $userid, $showCount]);
  if (!$result) {
    echo "There Is An Error In Search Query" . mysqli_error($conn);
  }

  $countSearchQuery = "select count(*) as total from saved_jobs inner join jobs on saved_jobs.job_id = jobs.id where jobs.title like ? and saved_jobs.user_id = ?";
  $countSearchResult = mysqli_execute_query($conn, $countSearchQuery, [$search, $userid]);
  $totalSaved = (int)mysqli_fetch_assoc($countSearchResult)['total'];
} else {
  $savequery = "select $commonColumns
                  from saved_jobs
                  inner join jobs on saved_jobs.job_id = jobs.id
                  inner join categories on jobs.category_id = categories.id
                  where saved_jobs.user_id = ?
                  order by saved_jobs.id desc limit ?";
  $result = mysqli_execute_query($conn, $savequery, [$userid, $showCount]);

  $countQuery = "select count(*) as total from saved_jobs where user_id = ?";
  $countResult = mysqli_execute_query($conn, $countQuery, [$userid]);
  $totalSaved = (int)mysqli_fetch_assoc($countResult)['total'];
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

  /* ===== Category Filter Form ===== */
  .category-filter-form {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--surface-color);
    padding: 8px 14px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
  }

  .category-filter-form label {
    color: var(--accent-color);
    display: flex;
    align-items: center;
    margin: 0;
  }

  .category-filter-form select {
    border: none;
    outline: none;
    background: transparent;
    font-family: var(--default-font);
    font-size: 14px;
    color: var(--heading-color);
    padding: 6px 4px;
    cursor: pointer;
    min-width: 150px;
  }

  .category-filter-form .btn-filter {
    background: var(--accent-color);
    color: #fff;
    border: none;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s ease;
  }

  .category-filter-form .btn-filter:hover {
    background: #0284c7;
  }

  /* ===== Search Form ===== */
  .job-search-form {
    display: flex;
    background: var(--surface-color);
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
  }

  .job-search-form input[type="search"] {
    border: none;
    outline: none;
    padding: 10px 16px;
    font-size: 14px;
    font-family: var(--default-font);
    color: var(--heading-color);
    width: 220px;
  }

  .job-search-form input[type="search"]::placeholder {
    color: #94a3b8;
  }

  .job-search-form .btn-do-search {
    background: var(--heading-color);
    color: #fff;
    border: none;
    padding: 0 20px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .job-search-form .btn-do-search:hover {
    background: var(--accent-color);
  }

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

  .job-card:hover {
    box-shadow: 0 16px 48px rgba(14, 165, 233, 0.12);
    border-color: rgba(14, 165, 233, 0.3);
    transform: translateY(-4px);
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
  }

  .card-actions {
    display: flex;
    align-items: center;
    text-align: center;
    gap: 8px;
  }

  .card-actions form {
    display: flex;
    align-items: center;
    margin: 0;
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
    text-decoration: none;
  }

  .btn-job-closed {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #f1f5f9;
    color: #94a3b8;
    padding: 9px 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    font-family: var(--nav-font);
    border: none;
    cursor: not-allowed;
  }

  .btn-remove-saved {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 40px;
    height: 40px;
    background: transparent;
    border: 1.5px solid #e2e8f0;
    color: #94a3b8;
    border-radius: 9px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    padding: 0;
  }

  .btn-remove-saved:hover {
    background: rgba(220, 38, 38, 0.08);
    border-color: rgba(220, 38, 38, 0.4);
    color: #dc2626;
  }

  .job-featured-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 50px;
    font-family: var(--nav-font);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #fff;
  }

  .job-featured-badge.saved-badge {
    background: linear-gradient(135deg, #10b981, #059669);
  }

  .job-featured-badge.closed-badge {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    right: auto;
    left: 16px;
  }

  .job-posted {
    font-size: 11.5px;
    color: #94a3b8;
    font-family: var(--default-font);
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 14px;
  }

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
    text-decoration: none;
  }

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

  .no-jobs-msg {
    text-align: center;
    padding: 60px 20px;
    color: var(--default-color);
    background: var(--surface-color);
    border-radius: 16px;
  }

  @media (max-width: 767px) {
    .filter-tabs {
      overflow-x: auto;
    }

    .job-search-form input[type="search"] {
      width: 100%;
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
          <form method="GET" action="#jobs-section" class="category-filter-form">
            <label><i class="bi bi-sliders fs-4"></i></label>
            <select name="filter">
              <option value="" <?php echo (!isset($_GET['filter']) || $_GET['filter'] === '') ? 'selected' : ''; ?>>All Category</option>
              <?php
              mysqli_data_seek($categoryResult, 0);
              while ($categories = mysqli_fetch_assoc($categoryResult)) { ?>
                <option value="<?php echo (int)$categories["id"]; ?>" <?php echo (isset($_GET['filter']) && (int)$_GET['filter'] === (int)$categories["id"]) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($categories["name"]); ?>
                </option>
              <?php } ?>
            </select>
            <button type="submit" name="submit-filter" value="1" class="btn-filter">Filter Jobs</button>
          </form>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="filter-tabs" data-aos="fade-up" data-aos-delay="150">
          <button class="filter-tab <?php echo (!isset($_GET['filter']) && !isset($_GET['search'])) ? 'active' : ''; ?>" onclick="window.location.href='./saved-jobs.php#jobs-section'">All Jobs</button>
        </div>
        <form action="#jobs-section" method="GET" class="job-search-form">
          <input type="search" name="search" placeholder="Search saved jobs…" value="<?php echo !empty($_GET["search"]) ? htmlspecialchars($_GET["search"]) : ''; ?>">
          <button type="submit" name="submit-search" class="btn-do-search">Search</button>
        </form>
      </div>

      <div class="results-count mt-4" data-aos="fade-up" data-aos-delay="160">
        Showing <strong><?php echo min($showCount, $totalSaved); ?></strong> of <strong><?php echo $totalSaved; ?></strong> saved jobs
      </div>

      <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0) { ?>
          <?php while ($jobs = mysqli_fetch_assoc($result)) { ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="job-card">

                <?php if ($jobs['job_status'] === 'open') { ?>
                  <span class="job-featured-badge saved-badge"><i class="bi bi-bookmark-fill"></i> Saved</span>
                <?php } else { ?>
                  <span class="job-featured-badge saved-badge"><i class="bi bi-bookmark-fill"></i> Saved</span>
                  <span class="job-featured-badge closed-badge">Closed</span>
                <?php } ?>

                <div class="job-card-head">
                  <div class="job-logo" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: var(--accent-color);">💼</div>
                  <div class="job-head-info">
                    <h5 class="job-title"><?php echo htmlspecialchars($jobs['title']); ?></h5>
                    <div>
                      <div class="job-company"><i class="bi bi-buildings"></i> <?php echo htmlspecialchars($jobs['company']); ?></div>
                      <span class="badge bg-light text-secondary mt-1 border" style="font-size: 11px; font-weight: 600;"><?php echo htmlspecialchars($jobs['cat_name']); ?></span>
                    </div>
                  </div>
                </div>

                <div class="job-posted">
                  <i class="bi bi-clock-history text-warning"></i> Posted <?php echo date('d M Y', strtotime($jobs['created_at'])); ?>
                </div>

                <div class="job-meta-row">
                  <div class="job-meta-item"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo htmlspecialchars($jobs['location']); ?></div>
                  <div class="job-meta-item"><i class="bi bi-briefcase"></i> <?php echo htmlspecialchars($jobs['job_type']); ?></div>
                </div>

                <div class="job-card-footer">
                  <div>
                    <div class="job-salary"><?php echo htmlspecialchars($jobs['salary']); ?> <span>/ yr</span></div>
                  </div>

                  <div class="card-actions">
                    <?php if ($jobs['job_status'] === 'open') { ?>
                      <a href="/job-portal/job-details.php?id=<?php echo (int)$jobs['job_id']; ?>" class="btn-view-details">
                        View Job <i class="bi bi-arrow-right"></i>
                      </a>
                    <?php } else { ?>
                      <span class="btn-job-closed">Job Closed</span>
                    <?php } ?>
                    <form method="POST" onsubmit="return confirm('Remove this job from your saved list?');">
                      <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                      <input type="hidden" name="saved_id" value="<?php echo (int)$jobs['saved_id']; ?>">
                      <button type="submit" name="remove_saved" class="btn-remove-saved" title="Remove from saved">
                        <i class="bi bi-trash3"></i> 
                      </button>
                    </form>
                  </div>
                </div>

              </div>
            </div>
          <?php } ?>
        <?php } else { ?>
          <div class="col-12">
            <div class="no-jobs-msg">You haven't saved any jobs yet. <a href="../jobs.php">Browse jobs →</a></div>
          </div>
        <?php } ?>
      </div>

      <?php if ($showCount < $totalSaved) { ?>
        <div class="load-more-wrap" data-aos="fade-up" data-aos-delay="100">
          <a href="?show=<?php echo $showCount + 6; ?><?php echo isset($filter) ? '&filter=' . $filter . '&submit-filter=1' : ''; ?><?php echo isset($search) && isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) . '&submit-search=1' : ''; ?>#jobs-section" class="btn-load-more">
            <i class="bi bi-plus-circle"></i> Load More Jobs
          </a>
        </div>
      <?php } ?>

    </div>
  </section>
</main>

<?php
include "../includes/footer.php";
?>