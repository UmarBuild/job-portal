 <?php  

session_start() ; 
include "../../job-portal/includes/config.php" ; 

if (!isset($_SESSION["user_id"])) {
  header("Location: ../login.php");
  exit();
  };
  if ($_SESSION["role"] != "employer" && $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
      exit();
    };

 $id = $_GET['id'] ;  
$userId =  $_SESSION['user_id'] ; 
 $sql = "DELETE from jobs where id = $id and user_id = $userId " ; 
 if($result = mysqli_query($conn , $sql)){ ?> 
<body> 
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    Swal.fire({
  title: "Drag me!",
  icon: "success",
  draggable: true
    }).then(function(){
window.location.href = "userDashboard.php?tab=manage-jobs" ; 
    }) ; 
</script> 
</body> 
 <?php 
 } 
 ?>  
  
   
   