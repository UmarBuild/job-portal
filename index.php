<?php
session_start();
include "includes/config.php"; 

$jobsCountQuery = "select count(*) as total from jobs where status = 'open'";
$jobsCountResult = mysqli_query($conn, $jobsCountQuery);
$jobsCountRow = mysqli_fetch_assoc($jobsCountResult);
$totalJobs = $jobsCountRow['total'];

$catCountQuery = "select count(*) as total from categories";
$catCountResult = mysqli_query($conn, $catCountQuery);
$catCountRow = mysqli_fetch_assoc($catCountResult);
$totalCategories = $catCountRow['total'];

$empCountQuery = "select count(*) as total from users where role = 'employer'";
$empCountResult = mysqli_query($conn, $empCountQuery);
$empCountRow = mysqli_fetch_assoc($empCountResult);
$totalEmployers = $empCountRow['total'];

$appCountQuery = "select count(*) as total from users where role = 'applicant'";
$appCountResult = mysqli_query($conn, $appCountQuery);
$appCountRow = mysqli_fetch_assoc($appCountResult);
$totalApplicants = $appCountRow['total'];

$categoriesQuery = "select id, name from categories limit 6";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

$latestJobsQuery = "select jobs.id, jobs.title, jobs.company, jobs.location, jobs.salary, jobs.created_at,
                    categories.name as category_name
                    from jobs
                    left join categories on jobs.category_id = categories.id
                    where jobs.status = 'open'
                    order by jobs.created_at desc
                    limit 6";
$latestJobsResult = mysqli_query($conn, $latestJobsQuery);
?>

<style>
.home-hero {
    background: linear-gradient(135deg, var(--accent-color) 0%, #0284c7 100%);
    padding: 80px 20px;
    text-align: center;
    color: var(--contrast-color);
}
 
.home-hero h1 {
    font-family: var(--heading-font);
    font-size: 40px;
    font-weight: 800;
    margin-bottom: 14px;
    color: var(--contrast-color);
}
 
.home-hero p {
    font-size: 17px;
    max-width: 640px;
    margin: 0 auto 30px;
    opacity: 0.95;
}
 
.hero-btn-wrap {
    display: flex;
    justify-content: center;
}
 
.btn-browse-jobs {
    display: inline-block;
    background: var(--surface-color);
    color: var(--accent-color) !important;
    text-decoration: none;
    padding: 14px 36px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    transition: all 0.25s ease;
}
 
.btn-browse-jobs:hover {
    background: var(--heading-color);
    color: var(--contrast-color) !important;
}
 
/* ---------------- STATS ---------------- */
.stats-section {
    background-color: var(--background-color);
    padding: 50px 20px;
}
 
.stats-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
 
.stat-box {
    background: var(--surface-color);
    border-radius: 14px;
    padding: 26px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
 
.stat-box .num {
    font-family: var(--heading-font);
    font-size: 32px;
    font-weight: 800;
    color: var(--accent-color);
}
 
.stat-box .label {
    margin-top: 6px;
    color: var(--default-color);
    font-size: 14px;
}
 
/* ---------------- CATEGORIES ---------------- */
.categories-section {
    background-color: var(--background-color);
    padding: 20px 20px 60px;
}
 
.section-heading {
    max-width: 1100px;
    margin: 0 auto 30px;
    text-align: center;
}
 
.section-heading h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 30px;
    margin-bottom: 8px;
}
 
.section-heading p {
    color: var(--default-color);
    font-size: 15px;
}
 
.categories-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 18px;
}
 
.category-card {
    background: var(--surface-color);
    border-radius: 14px;
    padding: 22px 14px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
 
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
 
.category-card .icon-circle {
    width: 48px;
    height: 48px;
    background: rgba(14,165,233,0.12);
    color: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin: 0 auto 12px;
}
 
.category-card h3 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 14.5px;
    margin: 0;
}
 
/* ---------------- LATEST JOBS ---------------- */
.jobs-section {
    background-color: var(--background-color);
    padding: 20px 20px 70px;
}
 
.jobs-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
 
.job-card {
    background: var(--surface-color);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
 
.job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
 
.job-card .top-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}
 
.job-card h3 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 18px;
    margin: 0;
}
 
.job-card .category-tag {
    background: rgba(14,165,233,0.12);
    color: var(--accent-color);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
 
.job-card .company {
    color: var(--default-color);
    font-weight: 600;
    font-size: 14px;
}
 
.job-card .meta {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    font-size: 13px;
    color: var(--default-color);
}
 
.job-card .card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px solid rgba(0,0,0,0.06);
}
 
.job-card .date-posted {
    font-size: 12.5px;
    color: var(--default-color);
}
 
.btn-apply {
    background: var(--accent-color);
    color: var(--contrast-color) !important;
    text-decoration: none;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    transition: background 0.25s ease;
}
 
.btn-apply:hover {
    background: var(--heading-color);
    color: var(--contrast-color) !important;
}
 
.no-jobs {
    text-align: center;
    color: var(--default-color);
    padding: 40px;
    background: var(--surface-color);
    border-radius: 14px;
    grid-column: 1 / -1;
}
 
.view-all-wrap {
    text-align: center;
    margin-top: 30px;
}
 
.btn-view-all {
    display: inline-block;
    background: var(--surface-color);
    color: var(--accent-color) !important;
    border: 2px solid var(--accent-color);
    text-decoration: none;
    padding: 12px 30px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 14.5px;
    transition: all 0.25s ease;
}
 
.btn-view-all:hover {
    background: var(--accent-color);
    color: var(--contrast-color) !important;
}
 
@media (max-width: 992px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .categories-grid { grid-template-columns: repeat(3, 1fr); }
    .jobs-grid { grid-template-columns: repeat(2, 1fr); }
}
 
@media (max-width: 576px) {
    .home-hero h1 { font-size: 28px; }
    .stats-grid { grid-template-columns: 1fr; }
    .categories-grid { grid-template-columns: repeat(2, 1fr); }
    .jobs-grid { grid-template-columns: 1fr; }
}
</style>

<?php
include "./includes/header.php";
?>

<section class="home-hero">
    <h1>Find Your Next Job Today</h1>
    <p>Browse thousands of job listings or find the right talent for your company - all in one place</p>

    <div class="hero-btn-wrap">
        <a href="jobs.php" class="btn-browse-jobs">Browse Jobs</a>
    </div>
</section>

<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-box">
            <div class="num"><?php echo $totalJobs; ?>+</div>
            <div class="label">Active Jobs</div>
        </div>
        <div class="stat-box">
            <div class="num"><?php echo $totalEmployers; ?>+</div>
            <div class="label">Companies</div>
        </div>
        <div class="stat-box">
            <div class="num"><?php echo $totalApplicants; ?>+</div>
            <div class="label">Job Seekers</div>
        </div>
        <div class="stat-box">
            <div class="num"><?php echo $totalCategories; ?>+</div>
            <div class="label">Categories</div>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="section-heading">
        <h2>Popular Categories</h2>
        <p>A quick look at the fields we cover</p>
    </div>

    <div class="categories-grid">
        <?php
        if ($categoriesResult && mysqli_num_rows($categoriesResult) > 0) {
            while ($category = mysqli_fetch_assoc($categoriesResult)) {
        ?>
            <div class="category-card">
                <div class="icon-circle"><i class="bi bi-briefcase-fill"></i></div>
                <h3><?php echo $category['name']; ?></h3>
            </div>
        <?php
            }
        } else {
        ?>
            <div class="no-jobs">No categories available yet.</div>
        <?php
        }
        ?>
    </div>
</section>

<section class="jobs-section">
    <div class="section-heading">
        <h2>Latest Job Openings</h2>
        <p>Check out the newest jobs posted on the portal</p>
    </div>

    <div class="jobs-grid">
        <?php
        if ($latestJobsResult && mysqli_num_rows($latestJobsResult) > 0) {
            while ($job = mysqli_fetch_assoc($latestJobsResult)) {
        ?>
            <div class="job-card">
                <div class="top-row">
                    <h3><?php echo $job['title']; ?></h3>
                    <span class="category-tag"><?php echo $job['category_name'] ? $job['category_name'] : 'N/A'; ?></span>
                </div>
                <div class="company"><?php echo $job['company']; ?></div>
                <div class="meta">
                    <span><i class="bi bi-geo-alt"></i> <?php echo $job['location']; ?></span>
                    <span><i class="bi bi-cash-stack"></i> <?php echo $job['salary']; ?></span>
                </div>
                <div class="card-footer">
                    <span class="date-posted"><?php echo date('d M Y', strtotime($job['created_at'])); ?></span>
                    <a class="btn-apply" href="jobs.php">View Job</a>
                </div>
            </div>
        <?php
            }
        } else {
        ?>
            <div class="no-jobs">No jobs available yet.</div>
        <?php
        }
        ?>
    </div>

    <div class="view-all-wrap">
        <a href="jobs.php" class="btn-view-all">View All Jobs</a>
    </div>
</section>

<?php
include "./includes/footer.php";
?>