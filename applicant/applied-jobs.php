<?php
session_start();
include "../includes/config.php";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "applicant") {
    header("Location: ../login.php");
    exit();
};
$user_id = $_SESSION["user_id"];
// $jobs_id = $_GET['id'];   
$JobsAndApplicationquery = "select *,jobs.* from applications inner join jobs on applications.job_id = jobs.id where applications.applicant_id = $user_id; ";
$result = mysqli_query($conn, $JobsAndApplicationquery);

// if(mysqli_num_rows($result) >= 1){ 
// echo "query run properly" ; // QUERY RUN PROPERLY I HAVE CHECKED
// }else{
//     echo "Jobs and application query failed".mysqli_error($conn) ; 
// } 

?>
<head>
  <style>
    body {
      background-color: #f1f5f9;
      font-family: "Roboto", sans-serif;
      color: #475569;
    }

    .page-title-box h2 {
      font-family: "Raleway", sans-serif;
      color: #1e293b;
      font-weight: 700;
    }

    .custom-card {
      background-color: #ffffff;
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .table th {
      font-family: "Raleway", sans-serif;
      color: #1e293b;
      background-color: #f8fafc;
      font-weight: 600;
    }

    .accent-btn {
      background-color: #0ea5e9;
      color: #ffffff;
      border: none;
      transition: all 0.3s ease;
    }

    .accent-btn:hover {
      background-color: #0284c7;
      color: #ffffff;
    }

    .badge-pending {
      background-color: #fef08a;
      color: #854d0e;
    }

    .badge-shortlisted {
      background-color: #dcfce7;
      color: #166534;
    }

    .badge-rejected {
      background-color: #fee2e2;
      color: #991b1b;
    }
  </style>
</head>
<?php include "../includes/header.php"; ?>

<body>


  <main class="main py-5" data-aos="fade-up">
    <div class="container">

      <div class="page-title-box d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2>My Applied Jobs</h2>
          <p class="text-muted mb-0">Track and manage your submitted job applications</p>
        </div>
        <a href="../jobs.php" class="btn accent-btn btn-sm px-3 rounded-pill">
          <i class="bi bi-search me-1"></i> Find More Jobs
        </a>
      </div>

      <div class="card custom-card p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr class="border-bottom text-secondary">
                <th scope="col" class="py-3">Job Title & Company</th>
                <th scope="col" class="py-3">Date Applied</th>
                <th scope="col" class="py-3">Job Type</th>
                <th scope="col" class="py-3">Salary Package</th>
                <th scope="col" class="py-3 text-center">Status</th>
                <th scope="col" class="py-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($jobs = mysqli_fetch_assoc($result)) { ?>
                <tr class="border-bottom" style="transition: transform 0.2s;">
                  <td class="py-3">
                    <div class="d-flex align-items-center">
                      <div class="avatar-icon bg-light rounded p-2 me-3 text-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-briefcase-fill fs-5" style="color: #0ea5e9;"></i>
                      </div>
                      <div>
                        <h6 class="mb-0 fw-bold text-dark"><?php echo $jobs['title'] ?></h6>
                        <small class="text-muted"><i class="bi bi-building me-1"></i>Aptech Tech Solutions</small>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 text-secondary">
                    <i class="bi bi-calendar3 me-1"></i> <?php echo $jobs['created_at'] ?>
                  </td>
                  <td class="py-3">
                    <span class="badge bg-light text-dark border px-2 py-1.5 fw-normal text-capitalize">
                      Full-time
                    </span>
                  </td>
                  <td class="py-3 fw-medium text-dark">
                    <?php echo $jobs['salary'] ?>
                  </td>
                  <td class="py-3 text-center">
               <?php     if($jobs['status'] == "pending"){  ?>   
                      <span class="badge badge-pending px-3 py-2 rounded-pill text-uppercase fw-semibold" style="font-size: 0.75rem;">
                        <?php echo $jobs['status'] ?>
                      </span>
<?php                    }  ?>
               <?php     if($jobs['status'] == "rejected"){  ?>   
                      <span class="badge badge-pending px-3 bg-danger py-2 rounded-pill text-uppercase fw-semibold" style="font-size: 0.75rem;">
                        <?php echo $jobs['status'] ?>
                      </span>
<?php                    }  ?>
               <?php     if($jobs['status'] == "accepted"){  ?>   
                      <span class="badge badge-pending bg-success px-3 py-2 rounded-pill text-uppercase fw-semibold" style="font-size: 0.75rem;">
                        <?php echo $jobs['status'] ?>
                      </span>
<?php                    }  ?>
                  </td>
                  <td class="py-3 text-center">
                    <a href="../job-details.php?id=<?php echo $jobs['id'] ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                      <i class="bi bi-eye-fill me-1"></i> View
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
  <?php
include "../includes/footer.php";;
?>

  <script src="/job-portal/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/job-portal/assets/vendor/php-email-form/validate.js"></script>
  <script src="/job-portal/assets/vendor/aos/aos.js"></script>
  <script src="/job-portal/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/job-portal/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="/job-portal/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="/job-portal/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/job-portal/assets/vendor/swiper/swiper-bundle.min.js"></script>


  <script>
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true
    });
  </script>

</body>

</html>