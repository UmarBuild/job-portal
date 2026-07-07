<?php session_start();
include("../includes/config.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
$message = ""; // DELETE JOB 
if (isset($_POST['delete_job_id'])) {
    $deleteId = $_POST['delete_job_id'];
    $deletejobquery = "DELETE FROM jobs WHERE id='$deleteId'";
    $deletejobqueryresult = mysqli_query($conn, $deletejobquery);
    if ($deletejobqueryresult) { // Delete related applications
        $deleteapplicationquery = "DELETE FROM applications WHERE job_id='$deleteId'";
        mysqli_query($conn, $deleteapplicationquery); // Delete related saved jobs
        $deletesavedjobsquery = "DELETE FROM saved_jobs WHERE job_id='$deleteId'";
        mysqli_query($conn, $deletesavedjobsquery);
        $message = "Job deleted successfully";
    } else {
        $message = "Error while deleting job";
    }
} // CATEGORY FILTER
$categoryFilter = 0;
if (isset($_GET['category'])) {
    $categoryFilter = $_GET['category'];
} // SEARCH
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
} // JOBS QUERY
$jobsquery = "SELECT jobs.*, users.fullname AS employer_name, users.email AS employer_email, categories.name AS category_name FROM jobs LEFT JOIN users ON jobs.user_id = users.id LEFT JOIN categories ON jobs.category_id = categories.id WHERE 1";
if ($categoryFilter > 0) {
    $jobsquery .= " AND jobs.category_id='$categoryFilter'";
}
if ($search != "") {
    $jobsquery .= " AND ( jobs.title LIKE '%$search%' OR jobs.company LIKE '%$search%' OR jobs.location LIKE '%$search%' )";
}
$jobsquery .= " ORDER BY jobs.created_at DESC";
$jobs = mysqli_query($conn, $jobsquery); // CATEGORIES
$categoriesquery = "SELECT * FROM categories ORDER BY name ASC";
$categories = mysqli_query($conn, $categoriesquery); ?>

<style>
    .admin-jobs {
        background-color: var(--background-color);
        padding: 40px 20px;
        font-family: var(--default-font);
        color: var(--default-color);
    }

    .admin-jobs h1 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 28px;
        margin-bottom: 6px;
    }

    .admin-jobs .subtitle {
        margin-bottom: 24px;
        font-size: 15px;
    }

    .alert-msg {
        background: rgba(14, 165, 233, 0.10);
        color: var(--accent-color);
        border: 1px solid rgba(14, 165, 233, 0.25);
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .controls-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
    }

    .filter-select select {
        padding: 10px 16px;
        border-radius: 30px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: var(--surface-color);
        color: var(--default-color);
        font-size: 14px;
        outline: none;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: var(--surface-color);
        border-radius: 30px;
        padding: 6px 6px 6px 18px;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .search-box input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 14px;
        color: var(--default-color);
        width: 220px;
    }

    .search-box button {
        background: var(--accent-color);
        color: var(--contrast-color);
        border: none;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        cursor: pointer;
    }

    .jobs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .job-card {
        background: var(--surface-color);
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .job-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
    }

    .job-card .top-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .job-card h3 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 18px;
        margin: 0;
    }

    .job-card .category-tag {
        background: rgba(14, 165, 233, 0.12);
        color: var(--accent-color);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .job-card .company {
        color: var(--default-color);
        font-size: 14px;
        font-weight: 600;
    }

    .job-card .meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 13px;
        color: var(--default-color);
    }

    .job-card .meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .job-card .employer-line {
        font-size: 13px;
        color: var(--default-color);
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        padding-top: 10px;
        margin-top: 4px;
    }

    .job-card .employer-line strong {
        color: var(--heading-color);
    }

    .job-card .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 10px;
    }

    .job-card .date-posted {
        font-size: 12.5px;
        color: var(--default-color);
    }

    .btn-delete {
        background: rgba(220, 38, 38, 0.10);
        color: #dc2626;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-delete:hover {
        background: #dc2626;
        color: #fff;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: var(--default-color);
        background: var(--surface-color);
        border-radius: 14px;
        grid-column: 1 / -1;
    }

    @media (max-width: 576px) {
        .controls-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box input {
            width: 100%;
        }

        .jobs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include "../includes/header.php" ?>

<section class="admin-jobs">
    <h1>Manage Jobs</h1>
    <p class="subtitle"> Manage all jobs posted by employers </p> <?php if ($message != "") { ?> <div class="alert-msg"> <?php echo $message; ?> </div> <?php } ?> <div class="controls-bar">
        <form class="filter-select" method="GET"> <input type="hidden" name="search" value="<?php echo $search; ?>"> <select name="category" onchange="this.form.submit()">
                <option value="0"> All Categories </option> <?php while ($cat = mysqli_fetch_assoc($categories)) { ?> <option value="<?php echo $cat['id']; ?>" <?php if ($categoryFilter == $cat['id']) {
                                                                                                                                                                    echo "selected";
                                                                                                                                                                } ?>> <?php echo $cat['name']; ?> </option> <?php } ?>
            </select> </form>
        <form class="search-box" method="GET"> <input type="hidden" name="category" value="<?php echo $categoryFilter; ?>"> <input type="text" name="search" placeholder="Search by title, company or location" value="<?php echo $search; ?>"> <button type="submit"> <i class="bi bi-search"></i> Search </button> </form>
    </div>
    <div class="jobs-grid"> <?php if (mysqli_num_rows($jobs) > 0) {
                                while ($job = mysqli_fetch_assoc($jobs)) { ?> <div class="job-card">
                    <div class="top-row">
                        <h3> <?php echo $job['title']; ?> </h3> <span class="category-tag"> <?php echo $job['category_name']; ?> </span>
                    </div>
                    <div class="company"> <?php echo $job['company']; ?> </div>
                    <div class="meta"> <span> <i class="bi bi-geo-alt"></i> <?php echo $job['location']; ?> </span> <span> <i class="bi bi-cash-stack"></i> <?php echo $job['salary']; ?> </span> </div>
                    <div class="employer-line"> Posted by <strong> <?php echo $job['employer_name']; ?> </strong> (<?php echo $job['employer_email']; ?>) </div>
                    <div class="card-footer"> <span class="date-posted"> <?php echo date("d M Y", strtotime($job['created_at'])); ?> </span>
                        <form method="POST" onsubmit="return confirm('Do you want to delete this job?')"> <input type="hidden" name="delete_job_id" value="<?php echo $job['id']; ?>"> <button type="submit" class="btn-delete"> <i class="bi bi-trash"></i> Delete </button> </form>
                    </div>
                </div> <?php }
                            } else { ?> <div class="no-data"> No jobs found </div> <?php } ?> </div>
</section>

<?php include "../includes/footer.php" ?>