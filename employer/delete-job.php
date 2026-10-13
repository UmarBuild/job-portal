<?php

session_start();
include "../includes/config.php";

if (!isset($_SESSION["user_id"])) {
  header("Location: ../login.php");
  exit();
};
if ($_SESSION["role"] != "employer" && $_SESSION["role"] != "admin") {
  header("Location: ../login.php");
  exit();
};

$id = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

if ($_SESSION["role"] == "admin") {
  // Admin kisi bhi job ko delete kar sakta hai
  $sql = "DELETE from jobs where id = ?";
  $result = mysqli_execute_query($conn, $sql, [$id]);
} else {
  // Employer sirf apni khud ki job delete kar sakta hai
  $sql = "DELETE from jobs where id = ? and user_id = ?";
  $result = mysqli_execute_query($conn, $sql, [$id, $userId]);
}

if ($result && mysqli_affected_rows($conn) >= 1) { ?>
<body>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    Swal.fire({
  title: "Job Deleted",
  icon: "success",
  draggable: true
    }).then(function(){
window.location.href = "add-manage-jobs.php?tab=manage-jobs";
    });
</script>
</body>
<?php
} else {
  header("Location: add-manage-jobs.php?tab=manage-jobs");
  exit();
}
?>