<?php
session_start();
include "../includes/config.php";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
};
$job_id = $_GET["id"];
$userid = $_SESSION["user_id"];
$checkQuery = "SELECT * FROM saved_jobs WHERE user_id='$userid' AND job_id='$job_id'";
$checkResult = mysqli_query($conn, $checkQuery);
if (mysqli_num_rows($checkResult) > 0) { ?>
    <script> 
    alert("Job Already Saved"); 
        window.location.href = "../job-details.php?id=<?php echo $job_id ?>";
        </script>
    <?php 
    }else{
    $insertsavequery = "insert into saved_jobs (job_id,user_id) VALUES ($job_id,$userid)" ;
    $insertsavequeryresult = mysqli_query($conn , $insertsavequery) ; ?>
    <script>
     alert("Job Saved Successfully");
    window.location.href = "saved-jobs.php";
    </script>
<?php  
    };
?> 