<?php
session_start();
include "../includes/config.php";
require_once "../includes/csrf.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
}

// CSRF Token Check (Accepts both POST and GET tokens)
$token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';

if (!verifyCsrfToken($token)) {
    die("Invalid CSRF Token.");
}

$job_id = isset($_REQUEST["id"]) ? (int)$_REQUEST["id"] : 0;
$userid = (int)$_SESSION["user_id"];

if ($job_id <= 0) {
    header("Location: ../jobs.php");
    exit();
}

$checkQuery = "SELECT * FROM saved_jobs WHERE user_id = ? AND job_id = ?";
$checkResult = mysqli_execute_query($conn, $checkQuery, [$userid, $job_id]);

if (mysqli_num_rows($checkResult) > 0) { ?>
    <script>
        alert("Job Already Saved");
        window.location.href = "../job-details.php?id=<?php echo $job_id; ?>";
    </script>
<?php
} else {
    $insertsavequery = "INSERT INTO saved_jobs (job_id, user_id) VALUES (?, ?)";
    $insertsavequeryresult = mysqli_execute_query($conn, $insertsavequery, [$job_id, $userid]); ?>
    <script>
        alert("Job Saved Successfully");
        window.location.href = "saved-jobs.php";
    </script>
<?php
}
?>