<?php
require_once './includes/config.php';
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
};

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']);

$result = mysqli_execute_query($conn, "SELECT * FROM users WHERE id = ? AND role = 'employer'", [$id]);
$company = mysqli_fetch_assoc($result);

if (!$company) {
    echo "Company not found";
    exit();
}

$jobs_result = mysqli_execute_query($conn, "SELECT *,jobs.id as job_id,categories.name AS cat_name FROM jobs inner join categories on jobs.category_id = categories.id WHERE user_id = ? AND status = 'open' ORDER BY jobs.id DESC", [$id]);

$total_jobs_result = mysqli_execute_query($conn, "select count(*) as total_jobs from jobs where user_id = ? and status = 'open' and approval_status = 'approved'", [$id]);
$total_jobs = mysqli_fetch_assoc($total_jobs_result);

// Ye check karta hai ke link http:// ya https:// se shuru ho, warna kuch mat dikhao
// javascript:... jaisi malicious values ko render hone se rokta hai
function safeUrl($url) {
    if (empty($url)) {
        return "";
    }
    if (preg_match('/^https?:\/\//i', $url)) {
        return htmlspecialchars($url);
    }
    return "";
}

require_once './includes/header.php';
?>

<div class="container my-5">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">

                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <?php if (!empty($company['company_logo'])) { ?>
                        <img src="uploads/company_logos/<?php echo htmlspecialchars($company['company_logo']); ?>" width="120" height="120" style="object-fit:cover; border-radius:5px;">
                    <?php } else { ?>
                        <img src="assets/img/default_company.png" width="120" height="120" style="object-fit:cover; border-radius:5px;">
                    <?php } ?>
                </div>

                <div class="col-md-10">
                    <h3><?php echo htmlspecialchars($company['company_name'] ?? 'N/A'); ?></h3>
                    <p class="mb-1"><?php echo (int)$total_jobs['total_jobs']; ?> active job(s)</p>
                    <p class="mb-1"><?php echo htmlspecialchars($company['company_industry'] ?? 'N/A'); ?></p>
                    <p class="mb-1"><?php echo htmlspecialchars($company['company_city'] ?? 'N/A'); ?></p>
                </div>

            </div>

            <hr>

            <div class="row g-3">

                <div class="col-md-6">
                    <strong>About Company</strong>
                    <p><?php echo !empty($company['company_about']) ? nl2br(htmlspecialchars($company['company_about'])) : 'N/A'; ?></p>
                </div>

                <div class="col-md-6">
                    <p><strong>Company Size:</strong> <?php echo !empty($company['company_size']) ? htmlspecialchars($company['company_size']) : 'N/A'; ?> </p>
                    <p><strong>Founded Year:</strong> <?php echo !empty($company['company_founded_year']) ? htmlspecialchars($company['company_founded_year']) : 'N/A'; ?></p>

                    <p><strong>Website:</strong>
                        <?php $website = safeUrl($company['company_website'] ?? ''); ?>
                        <?php if (!empty($website)) { ?>
                            <a href="<?php echo $website; ?>" target="_blank" rel="noopener noreferrer"><?php echo $website; ?></a>
                        <?php } else { ?>
                            N/A
                        <?php } ?>
                    </p>

                    <p><strong>Email:</strong> <?php echo !empty($company['company_email']) ? htmlspecialchars($company['company_email']) : 'N/A'; ?>  </p>
                    <p><strong>Phone:</strong> <?php echo !empty($company['company_phone']) ? htmlspecialchars($company['company_phone']) : 'N/A'; ?>  </p>
                    <p><strong>Address:</strong> <?php echo !empty($company['company_address']) ? nl2br(htmlspecialchars($company['company_address'])) : 'N/A'; ?> </p>

                    <p><strong>LinkedIn:</strong>
                        <?php $linkedin = safeUrl($company['company_linkedin'] ?? ''); ?>
                        <?php if (!empty($linkedin)) { ?>
                            <a href="<?php echo $linkedin; ?>" target="_blank" rel="noopener noreferrer"><?php echo $linkedin; ?></a>
                        <?php } else { ?>
                            N/A
                        <?php } ?>
                    </p>
                </div>

            </div>

        </div>
    </div>

    <h4 class="mb-3">Active Jobs</h4>

    <div class="row g-3">

        <?php if (mysqli_num_rows($jobs_result) > 0) { ?>

            <?php while ($job = mysqli_fetch_assoc($jobs_result)) { ?>

                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <h5><?php echo htmlspecialchars($job['title']); ?></h5>
                            <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($job['cat_name']); ?></p>
                            <p class="mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            <p class="mb-1"><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                            <p class="mb-3"> <strong>Posted On:</strong> <?php echo date("d M Y", strtotime($job['created_at'])); ?> </p>

                            <a href="job-details.php?id=<?php echo (int)$job['job_id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="col-12">
                <p>No active jobs posted by this company.</p>
            </div>

        <?php } ?>

    </div>

</div>

<?php require_once './includes/footer.php'; ?>