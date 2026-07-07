<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
    };
    
    include "../includes/header.php";
?>


<?php include "../includes/footer.php"; ?>   

