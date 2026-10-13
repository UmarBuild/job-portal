<?php 
session_start();
include("../includes/config.php");
require_once("../includes/csrf.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Verify CSRF Token on POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        die("Invalid CSRF Token.");
    }
}

$message = ""; 

// DELETE JOB
if (isset($_POST['delete_job_id'])) {
    $deleteId = (int)$_POST['delete_job_id'];
    
    $deletejobquery = "DELETE FROM jobs WHERE id = ?";
    $deletejobqueryresult = mysqli_execute_query($conn, $deletejobquery, [$deleteId]);
    
    if ($deletejobqueryresult) { 
        mysqli_execute_query($conn, "DELETE FROM applications WHERE job_id = ?", [$deleteId]); 
        mysqli_execute_query($conn, "DELETE FROM saved_jobs WHERE job_id = ?", [$deleteId]);
        
        header("Location: manage-jobs.php?status=all");
        exit();
    } else {
        $message = "Error while deleting job";
    }
}

require_once "../includes/notify.php";
require_once "../mail/mail.php";

// APPROVE JOB
if (isset($_POST['approve_job_id'])) {
    $jid = (int)$_POST['approve_job_id'];
    mysqli_execute_query($conn, "UPDATE jobs SET approval_status = 'approved' WHERE id = ?", [$jid]);

    $infoQuery = "SELECT jobs.title, users.id as employer_id, users.email, users.fullname
                  FROM jobs INNER JOIN users ON jobs.user_id = users.id WHERE jobs.id = ?";
    $infoResult = mysqli_execute_query($conn, $infoQuery, [$jid]);
    
    if ($info = mysqli_fetch_assoc($infoResult)) {
        sendNotification(
            $conn,
            $info['employer_id'],
            'job_approved',
            "Your job \"{$info['title']}\" has been approved and is now live",
            BASE_PATH . "/employer/add-manage-jobs.php"
        );

        sendMail(
            $info['email'],
            "Job Approved ✅",
            "<p>Hi {$info['fullname']}, your job posting <b>{$info['title']}</b> has been approved and is now live on the site.</p>"
        );
    }

    $message = "Job approved successfully";
}

// REJECT JOB
if (isset($_POST['reject_job_id'])) {
    $jid = (int)$_POST['reject_job_id'];
    mysqli_execute_query($conn, "UPDATE jobs SET approval_status = 'rejected' WHERE id = ?", [$jid]);

    $infoQuery = "SELECT jobs.title, users.id as employer_id, users.email, users.fullname
                  FROM jobs INNER JOIN users ON jobs.user_id = users.id WHERE jobs.id = ?";
    $infoResult = mysqli_execute_query($conn, $infoQuery, [$jid]);
    
    if ($info = mysqli_fetch_assoc($infoResult)) {
        sendNotification(
            $conn,
            $info['employer_id'],
            'job_rejected',
            "Your job \"{$info['title']}\" was not approved",
            BASE_PATH . "/employer/add-manage-jobs.php"
        );

        sendMail(
            $info['email'],
            "Job Not Approved",
            "<p>Hi {$info['fullname']}, unfortunately your job posting <b>{$info['title']}</b> did not meet our posting guidelines and was not approved.</p>"
        );
    }

    $message = "Job rejected";
}

// FILTERS & SEARCH
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$approvalFilter = isset($_GET['status']) ? $_GET['status'] : 'pending';

// PREPARED QUERY BUILDER
$whereClause = " WHERE 1=1";
$params = [];

if ($approvalFilter != 'all') {
    $whereClause .= " AND jobs.approval_status = ?";
    $params[] = $approvalFilter;
}

if ($categoryFilter > 0) {
    $whereClause .= " AND jobs.category_id = ?";
    $params[] = $categoryFilter;
}

if ($search != "") {
    $whereClause .= " AND (jobs.title LIKE ? OR jobs.company LIKE ? OR jobs.location LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// PAGINATION CALCULATIONS
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$perPage = 10;
$offset = ($page - 1) * $perPage;

// COUNT QUERY
$countQuery = "SELECT COUNT(*) as total FROM jobs LEFT JOIN users ON jobs.user_id = users.id LEFT JOIN categories ON jobs.category_id = categories.id" . $whereClause;
$countResult = mysqli_execute_query($conn, $countQuery, $params);
$totalJobs = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalJobs / $perPage);

$jobsquery = "SELECT jobs.*, users.fullname AS employer_name, users.email AS employer_email, categories.name AS category_name 
              FROM jobs 
              LEFT JOIN users ON jobs.user_id = users.id 
              LEFT JOIN categories ON jobs.category_id = categories.id" 
              . $whereClause . " ORDER BY jobs.created_at DESC LIMIT ? OFFSET ?";

$queryParams = array_merge($params, [$perPage, $offset]);
$jobs = mysqli_execute_query($conn, $jobsquery, $queryParams);

// CATEGORIES FETCH
$categoriesquery = "SELECT * FROM categories ORDER BY name ASC";
$categories = mysqli_query($conn, $categoriesquery); 
?>

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

    .approval-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .approval-tabs .btn {
        border-radius: 30px;
        font-size: 13px;
        padding: 7px 18px;
        font-weight: 600;
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
        position: relative;
    }

    .job-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
    }

    .job-card .top-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-right: 80px;
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
        align-self: flex-start;
    }

    .approval-badge {
        position: absolute;
        top: 18px;
        right: 18px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .approval-badge.pending { background: #fef9c3; color: #854d0e; }
    .approval-badge.approved { background: #dcfce7; color: #166534; }
    .approval-badge.rejected { background: #fee2e2; color: #991b1b; }

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
        flex-wrap: wrap;
        gap: 8px;
    }

    .job-card .date-posted {
        font-size: 12.5px;
        color: var(--default-color);
    }

    .footer-actions {
        display: flex;
        gap: 6px;
        margin-left: auto;
    }

    .btn-approve {
        background: rgba(22, 163, 74, 0.10);
        color: #16a34a;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-approve:hover {
        background: #16a34a;
        color: #fff;
    }

    .btn-reject {
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

    .btn-reject:hover {
        background: #dc2626;
        color: #fff;
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

    .pagination-wrap {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 6px;
        border-radius: 8px;
        background: var(--surface-color);
        border: 1px solid rgba(0,0,0,0.08);
        color: var(--default-color);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .page-link:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
        text-decoration: none;
    }

    .page-link.active {
        background: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
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

        .card-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .footer-actions {
            margin-left: 0;
            justify-content: stretch;
        }

        .footer-actions form {
            flex: 1;
        }

        .footer-actions button {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php include "../includes/header.php"; ?>

<section class="admin-jobs">
    <h1>Manage Jobs</h1>
    <p class="subtitle">Manage all jobs posted by employers</p>

    <?php if ($message != "") { ?>
        <div class="alert-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <div class="approval-tabs">
        <a href="?status=pending" class="btn <?php echo $approvalFilter == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">
            <i class="bi bi-hourglass-split"></i> Pending Review
        </a>
        <a href="?status=approved" class="btn <?php echo $approvalFilter == 'approved' ? 'btn-success' : 'btn-outline-success'; ?>">
            <i class="bi bi-check-circle"></i> Approved
        </a>
        <a href="?status=rejected" class="btn <?php echo $approvalFilter == 'rejected' ? 'btn-danger' : 'btn-outline-danger'; ?>">
            <i class="bi bi-x-circle"></i> Rejected
        </a>
        <a href="?status=all" class="btn <?php echo $approvalFilter == 'all' ? 'btn-dark' : 'btn-outline-dark'; ?>">
            All Jobs
        </a>
    </div>

    <div class="controls-bar">
        <form class="filter-select" method="GET">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($approvalFilter); ?>">
            <select name="category" onchange="this.form.submit()">
                <option value="0">All Categories</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?php echo (int)$cat['id']; ?>" <?php if ($categoryFilter == $cat['id']) { echo "selected"; } ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </form>
        <form class="search-box" method="GET">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($approvalFilter); ?>">
            <input type="text" name="search" placeholder="Search by title, company or location" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
    </div>

    <div class="jobs-grid">
        <?php if (mysqli_num_rows($jobs) > 0) {
            while ($job = mysqli_fetch_assoc($jobs)) { ?>
                <div class="job-card">
                    <span class="approval-badge <?php echo htmlspecialchars($job['approval_status']); ?>">
                        <?php echo htmlspecialchars($job['approval_status']); ?>
                    </span>

                    <div class="top-row">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    </div>
                    <span class="category-tag"><?php echo htmlspecialchars($job['category_name'] ?? 'Uncategorized'); ?></span>

                    <div class="company"><?php echo htmlspecialchars($job['company']); ?></div>
                    <div class="meta">
                        <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                        <span><i class="bi bi-cash-stack"></i> <?php echo htmlspecialchars($job['salary']); ?></span>
                    </div>

                    <div class="employer-line">
                        Posted by <strong><?php echo htmlspecialchars($job['employer_name'] ?? 'Unknown'); ?></strong>
                        (<?php echo htmlspecialchars($job['employer_email'] ?? 'N/A'); ?>)
                    </div>

                    <div class="card-footer">
                        <span class="date-posted"><?php echo date("d M Y", strtotime($job['created_at'])); ?></span>

                        <div class="footer-actions">
                            <?php if ($job['approval_status'] == 'pending') { ?>
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                    <input type="hidden" name="approve_job_id" value="<?php echo (int)$job['id']; ?>">
                                    <button type="submit" class="btn-approve">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                    <input type="hidden" name="reject_job_id" value="<?php echo (int)$job['id']; ?>">
                                    <button type="submit" class="btn-reject">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                </form>
                            <?php } ?>
                            <form method="POST" onsubmit="return confirm('Do you want to delete this job?')">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                <input type="hidden" name="delete_job_id" value="<?php echo (int)$job['id']; ?>">
                                <button type="submit" class="btn-delete">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }
        } else { ?>
            <div class="no-data">No jobs found</div>
        <?php } ?>
    </div>

    <?php if ($totalPages > 1) { ?>
        <div class="pagination-wrap">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>&category=<?php echo $categoryFilter; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($approvalFilter); ?>"
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</section>

<?php include "../includes/footer.php"; ?>