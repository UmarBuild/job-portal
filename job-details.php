<?php          
session_start() ; 
include "includes/config.php"; 
$userid = $_SESSION['user_id'] ; 
  if (!isset($_SESSION["user_id"])) {   
header("Location: login.php");
  };
$id = $_GET['id'] ; 
$showquery = "select *,users.company_logo from jobs inner join users on jobs.user_id = users.id where jobs.id=$id" ; 
$result = mysqli_query($conn , $showquery) ; 
$jobs = mysqli_fetch_assoc($result) ;  
$namequery = " select users.fullname from jobs inner join users on jobs.user_id = users.id where jobs.id = $id; " ;  
$nameResult = mysqli_query($conn , $namequery) ; 
$name = mysqli_fetch_assoc($nameResult) ;   

$applicationsquery = "select * from applications where job_id = $id and applicant_id = $userid " ; 
$applications = mysqli_query($conn , $applicationsquery) ;               
if(isset($_POST['apply-job'])){ 
       if(mysqli_num_rows($applications) >= 1 ){ ?>
 <script>
  alert("already enrolled in this job") ; 
 window.location.href = "./applicant/applied-jobs.php?id=<?php echo $id ?>" ;
 </script>       
    <?php   }else{
$insertapplication =   "insert into applications (job_id,applicant_id) VALUES ($id,$userid)" ; 
       $applied_jobs = mysqli_query($conn , $insertapplication) ; 
       if($applied_jobs){
         echo "Job applied successfully" ;  
         }     else{
           echo "Job application failed".mysqli_error($conn) ;
           }  
       header("Location: ./applicant/applied-jobs.php?id=$id") ;               
       }
       }             
       
     
       
?>  
        
      
        
        <head>
   <style>  

.jd-hero {
background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0c2340 100%);
position: relative;
padding: 80px 0 70px;
overflow: hidden;
}

.jd-hero::before {
content: '';
position: absolute;
inset: 0;
background:
 radial-gradient(ellipse 70% 70% at 80% 40%, rgba(14,165,233,0.12) 0%, transparent 65%),
 radial-gradient(ellipse 40% 50% at 10% 80%, rgba(14,165,233,0.07) 0%, transparent 60%);
pointer-events: none;
}

.jd-hero::after {
content: '';
position: absolute;
inset: 0;
background-image:
 linear-gradient(rgba(14,165,233,0.06) 1px, transparent 1px),
 linear-gradient(90deg, rgba(14,165,233,0.06) 1px, transparent 1px);
background-size: 44px 44px;
pointer-events: none;
mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 100%);
}

.jd-hero .container { position: relative; z-index: 2; }

.jd-back-link {
display: inline-flex;
align-items: center;
gap: 7px;
color: #64748b;
font-size: 13.5px;
font-family: var(--nav-font);
font-weight: 600;
text-decoration: none;
margin-bottom: 32px;
transition: color 0.25s;
letter-spacing: 0.3px;
}

.jd-back-link:hover { color: #38bdf8; text-decoration: none; }
.jd-back-link i { font-size: 16px; }

.jd-hero-inner {
display: flex;
align-items: flex-start;
gap: 28px;
flex-wrap: wrap;
}

.jd-company-logo {
width: 80px;
height: 80px;
border-radius: 18px;
display: flex;
align-items: center;
justify-content: center;
font-size: 36px;
flex-shrink: 0;
box-shadow: 0 8px 28px rgba(0,0,0,0.3);
border: 2px solid rgba(255,255,255,0.08);
}

.jd-hero-content { flex: 1; min-width: 0; }

.jd-featured-badge {
display: inline-flex;
align-items: center;
gap: 5px;
background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.2));
border: 1px solid rgba(245,158,11,0.4);
color: #fbbf24;
font-size: 11px;
font-weight: 700;
padding: 4px 12px;
border-radius: 50px;
font-family: var(--nav-font);
text-transform: uppercase;
letter-spacing: 0.8px;
margin-bottom: 14px;
}

.jd-hero h1 {
font-family: var(--heading-font);
font-size: clamp(1.8rem, 4vw, 2.8rem);
font-weight: 800;
color: #f8fafc;
margin: 0 0 12px;
line-height: 1.2;
letter-spacing: -0.5px;
}

.jd-company-row {
display: flex;
align-items: center;
gap: 20px;
flex-wrap: wrap;
margin-bottom: 20px;
}

.jd-company-name {
display: flex;
align-items: center;
gap: 6px;
color: #94a3b8;
font-size: 15px;
font-family: var(--default-font);
}

.jd-company-name i { color: #0ea5e9; }

.jd-meta-chips {
display: flex;
gap: 10px;
flex-wrap: wrap;
}

.jd-chip {
display: inline-flex;
align-items: center;
gap: 5px;
font-size: 12.5px;
font-family: var(--nav-font);
font-weight: 600;
padding: 5px 13px;
border-radius: 50px;
}

.jd-chip.remote  { background: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
.jd-chip.full    { background: rgba(14,165,233,0.15);  color: #38bdf8; border: 1px solid rgba(14,165,233,0.3); }
.jd-chip.senior  { background: rgba(168,85,247,0.15);  color: #c084fc; border: 1px solid rgba(168,85,247,0.3); }
.jd-chip.part    { background: rgba(245,158,11,0.15);  color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
.jd-chip.contract{ background: rgba(168,85,247,0.15);  color: #c084fc; border: 1px solid rgba(168,85,247,0.3); }

.jd-hero-footer {
display: flex;
align-items: center;
gap: 24px;
flex-wrap: wrap;
margin-top: 8px;
}

.jd-posted {
display: flex;
align-items: center;
gap: 5px;
color: #64748b;
font-size: 13px;
font-family: var(--default-font);
}

.jd-posted i { color: #0ea5e9; font-size: 13px; }

.jd-deadline {
display: flex;
align-items: center;
gap: 5px;
color: #f87171;
font-size: 13px;
font-family: var(--default-font);
font-weight: 600;
}

.jd-deadline i { font-size: 13px; }

/* ---- MAIN CONTENT ---- */
.jd-section {
padding: 60px 0 80px;
background: var(--background-color);
}

/* Left: Detail Card */
.jd-detail-card {
background: var(--surface-color);
border-radius: 20px;
border: 1px solid #e2e8f0;
overflow: hidden;
box-shadow: 0 4px 24px rgba(0,0,0,0.05);
}

.jd-card-header {
padding: 28px 32px 24px;
border-bottom: 1px solid #f1f5f9;
background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}

.jd-section-title {
display: flex;
align-items: center;
gap: 10px;
font-family: var(--heading-font);
font-size: 18px;
font-weight: 800;
color: var(--heading-color);
margin: 0;
}

.jd-section-title .title-icon {
width: 36px;
height: 36px;
background: linear-gradient(135deg, #0ea5e9, #0284c7);
border-radius: 9px;
display: flex;
align-items: center;
justify-content: center;
color: #fff;
font-size: 16px;
flex-shrink: 0;
}

.jd-card-body { padding: 28px 32px; }

/* Description */
.jd-description {
font-family: var(--default-font);
color: var(--default-color);
font-size: 15px;
line-height: 1.85;
}

.jd-description p { margin-bottom: 18px; }
.jd-description p:last-child { margin-bottom: 0; }

/* Responsibilities & Requirements */
.jd-list-section { margin-top: 32px; }

.jd-list-heading {
font-family: var(--heading-font);
font-size: 16px;
font-weight: 700;
color: var(--heading-color);
margin-bottom: 16px;
display: flex;
align-items: center;
gap: 8px;
}

.jd-list-heading i { color: #0ea5e9; font-size: 17px; }

.jd-list {
list-style: none;
padding: 0;
margin: 0;
display: flex;
flex-direction: column;
gap: 12px;
}

.jd-list li {
display: flex;
align-items: flex-start;
gap: 12px;
font-family: var(--default-font);
font-size: 14.5px;
color: var(--default-color);
line-height: 1.7;
}

.jd-list li .li-dot {
width: 22px;
height: 22px;
background: rgba(14,165,233,0.1);
border: 1.5px solid rgba(14,165,233,0.25);
border-radius: 50%;
display: flex;
align-items: center;
justify-content: center;
flex-shrink: 0;
margin-top: 2px;
}

.jd-list li .li-dot i {
color: #0ea5e9;
font-size: 11px;
}

/* Skills Tags */
.jd-skills-wrap {
display: flex;
flex-wrap: wrap;
gap: 8px;
margin-top: 6px;
}

.jd-skill-tag {
background: #f1f5f9;
color: #475569;
border: 1px solid #e2e8f0;
border-radius: 8px;
padding: 6px 14px;
font-size: 13px;
font-family: var(--nav-font);
font-weight: 600;
transition: all 0.2s;
}

.jd-skill-tag:hover {
background: rgba(14,165,233,0.08);
border-color: rgba(14,165,233,0.3);
color: #0284c7;
}

/* Divider */
.jd-divider {
height: 1px;
background: #f1f5f9;
margin: 28px 0;
}

/* ---- RIGHT SIDEBAR ---- */
.jd-sidebar { display: flex; flex-direction: column; gap: 22px; }

/* Apply Card */
.jd-apply-card {
background: linear-gradient(135deg, #0f172a, #1e293b);
border-radius: 18px;
padding: 28px 26px;
border: 1px solid rgba(14,165,233,0.2);
position: relative;
overflow: hidden;
}

.jd-apply-card::before {
content: '';
position: absolute;
top: -40px;
right: -40px;
width: 140px;
height: 140px;
background: radial-gradient(circle, rgba(14,165,233,0.15), transparent 70%);
pointer-events: none;
}

.jd-apply-salary {
font-family: var(--heading-font);
font-size: 2rem;
font-weight: 800;
color: #f8fafc;
line-height: 1;
margin-bottom: 6px;
}

.jd-apply-salary-sub {
color: #64748b;
font-size: 13px;
font-family: var(--default-font);
margin-bottom: 22px;
}

.btn-apply-now {
display: flex;
align-items: center;
justify-content: center;
gap: 9px;
width: 100%;
background: linear-gradient(135deg, #0ea5e9, #0284c7);
color: #fff;
border: none;
padding: 15px 20px;
border-radius: 12px;
font-size: 15px;
font-weight: 700;
font-family: var(--nav-font);
text-decoration: none;
cursor: pointer;
transition: all 0.3s;
box-shadow: 0 8px 24px rgba(14,165,233,0.35);
letter-spacing: 0.3px;
margin-bottom: 12px;
}

.btn-apply-now:hover {
background: linear-gradient(135deg, #0284c7, #0369a1);
color: #fff;
text-decoration: none;
transform: translateY(-2px);
box-shadow: 0 12px 32px rgba(14,165,233,0.45);
}

.btn-apply-now i { font-size: 18px; }

.btn-save-job {
display: flex;
align-items: center;
justify-content: center;
gap: 8px;
width: 100%;
background: transparent;
color: #94a3b8;
border: 1.5px solid rgba(255,255,255,0.1);
padding: 12px 20px;
border-radius: 12px;
font-size: 14px;
font-weight: 600;
font-family: var(--nav-font);
text-decoration: none;
cursor: pointer;
transition: all 0.3s;
}

.btn-save-job:hover {
border-color: rgba(14,165,233,0.4);
color: #38bdf8;
text-decoration: none;
}

.jd-apply-note {
display: flex;
align-items: center;
gap: 6px;
color: #64748b;
font-size: 12px;
font-family: var(--default-font);
margin-top: 16px;
justify-content: center;
}

.jd-apply-note i { color: #22c55e; font-size: 13px; }

/* Info Card */
.jd-info-card {
background: var(--surface-color);
border-radius: 18px;
padding: 24px 24px;
border: 1px solid #e2e8f0;
box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.jd-info-card-title {
font-family: var(--heading-font);
font-size: 15px;
font-weight: 700;
color: var(--heading-color);
margin-bottom: 18px;
padding-bottom: 14px;
border-bottom: 1px solid #f1f5f9;
}

.jd-info-list {
display: flex;
flex-direction: column;
gap: 16px;
}

.jd-info-item {
display: flex;
align-items: center;
gap: 14px;
}

.jd-info-icon {
width: 40px;
height: 40px;
border-radius: 10px;
background: rgba(14,165,233,0.08);
border: 1px solid rgba(14,165,233,0.15);
display: flex;
align-items: center;
justify-content: center;
color: #0ea5e9;
font-size: 16px;
flex-shrink: 0;
}

.jd-info-text .lbl {
font-size: 11.5px;
color: #94a3b8;
font-family: var(--nav-font);
text-transform: uppercase;
letter-spacing: 0.7px;
margin-bottom: 2px;
}

.jd-info-text .val {
font-size: 14px;
font-weight: 600;
color: var(--heading-color);
font-family: var(--default-font);
}

/* Company Card */
.jd-company-card {
background: var(--surface-color);
border-radius: 18px;
padding: 24px;
border: 1px solid #e2e8f0;
box-shadow: 0 2px 12px rgba(0,0,0,0.04);
text-align: center;
}

.jd-co-logo {
width: 64px;
height: 64px;
border-radius: 16px;
display: flex;
align-items: center;
justify-content: center;
font-size: 28px;
margin: 0 auto 14px;
box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.jd-co-name {
font-family: var(--heading-font);
font-size: 16px;
font-weight: 700;
color: var(--heading-color);
margin-bottom: 4px;
}

.jd-co-industry {
font-size: 13px;
color: #94a3b8;
font-family: var(--default-font);
margin-bottom: 16px;
}

.jd-co-stats {
display: flex;
justify-content: center;
gap: 24px;
padding: 16px 0;
border-top: 1px solid #f1f5f9;
border-bottom: 1px solid #f1f5f9;
margin-bottom: 16px;
}

.jd-co-stat .num {
font-family: var(--heading-font);
font-size: 17px;
font-weight: 800;
color: var(--heading-color);
line-height: 1;
margin-bottom: 3px;
}

.jd-co-stat .lbl {
font-size: 11px;
color: #94a3b8;
font-family: var(--nav-font);
text-transform: uppercase;
letter-spacing: 0.6px;
}

.btn-view-company {
display: flex;
align-items: center;
justify-content: center;
gap: 7px;
width: 100%;
background: #f1f5f9;
color: var(--heading-color);
border: 1px solid #e2e8f0;
padding: 11px 16px;
border-radius: 10px;
font-size: 13.5px;
font-weight: 600;
font-family: var(--nav-font);
text-decoration: none;
transition: all 0.25s;
}

.btn-view-company:hover {
background: rgba(14,165,233,0.08);
border-color: rgba(14,165,233,0.3);
color: #0284c7;
text-decoration: none;
}

/* Similar Jobs Card */
.jd-similar-card {
background: var(--surface-color);
border-radius: 18px;
padding: 24px;
border: 1px solid #e2e8f0;
box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.jd-similar-title {
font-family: var(--heading-font);
font-size: 15px;
font-weight: 700;
color: var(--heading-color);
margin-bottom: 16px;
padding-bottom: 14px;
border-bottom: 1px solid #f1f5f9;
}

.similar-job-item {
display: flex;
align-items: center;
gap: 12px;
padding: 12px 0;
border-bottom: 1px solid #f8fafc;
text-decoration: none;
transition: all 0.2s;
}

.similar-job-item:last-child { border-bottom: none; padding-bottom: 0; }
.similar-job-item:first-child { padding-top: 0; }

.similar-job-item:hover { text-decoration: none; }
.similar-job-item:hover .sj-title { color: #0ea5e9; }

.sj-logo {
width: 40px;
height: 40px;
border-radius: 10px;
display: flex;
align-items: center;
justify-content: center;
font-size: 18px;
flex-shrink: 0;
}

.sj-info { flex: 1; min-width: 0; }

.sj-title {
font-size: 13.5px;
font-weight: 700;
color: var(--heading-color);
font-family: var(--heading-font);
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
transition: color 0.2s;
margin-bottom: 2px;
}

.sj-company {
font-size: 12px;
color: #94a3b8;
font-family: var(--default-font);
}

.sj-salary {
font-size: 13px;
font-weight: 700;
color: #0ea5e9;
font-family: var(--heading-font);
white-space: nowrap;
}

/* Breadcrumb area fix */
.page-title { background: var(--background-color); }

/* Responsive */
@media (max-width: 991px) {
.jd-hero { padding: 60px 0 50px; }
.jd-card-body { padding: 22px 20px; }
.jd-card-header { padding: 22px 20px 18px; }
}

@media (max-width: 575px) {
.jd-company-logo { width: 60px; height: 60px; font-size: 26px; border-radius: 14px; }
.jd-hero h1 { font-size: 1.6rem; }
}
</style>  
   </head>
   
   <?php
   include "includes/header.php"; 
    ?>

<main class="main"> 
  
  <div class="page-title"> 
    <div class="breadcrumbs">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#"><i class="bi bi-house"></i> Home</a></li>
          <li class="breadcrumb-item"><a href="jobs.php">Jobs</a></li>
          <li class="breadcrumb-item active current">Job Details</li>
        </ol>
      </nav>
    </div>
    <div class="title-wrapper">
      <h1>Job Details</h1>
      <p>Review the full job description and apply directly from this page.</p>
    </div>
  </div> 
   
   
  <section class="jd-hero">
    <div class="container">

      <a href="jobs.php" class="jd-back-link">
        <i class="bi bi-arrow-left"></i> Back to All Jobs
      </a>

      <div class="jd-hero-inner">
        <div class="jd-company-logo"  style="background: #f1f5f9;">
           <?php if (!empty($jobs['company_logo'])) { ?>
                        <img src="uploads/company_logos/<?php echo $jobs['company_logo']; ?>" width="70" height="70" style="object-fit:cover; border-radius:5px;">
                    <?php } else { ?>
                        <img src="assets/img/default_company.png" width="70" height="70" style="object-fit:cover; border-radius:5px;">
                    <?php } ?>
        </div>

        <div class="jd-hero-content">
          <div class="jd-featured-badge"><i class="bi bi-star-fill"></i> Posted By :  <?php  echo $name['fullname'] ;   ?></div> 
          <h1><?php echo $jobs['title']; ?></h1>

          <div class="jd-company-row">
            <div class="jd-company-name">
              <i class="bi bi-buildings"></i> <?php echo $jobs['company']; ?> 
            </div>
            <div>
              <a href="/job-portal/company.php?id=<?php echo $jobs['user_id']; ?>" class="btn-apply-now">View Company</a>
            </div>
          </div>

          <div class="jd-meta-chips">
            <span class="jd-chip full"><i class="bi bi-briefcase"></i> <?php echo $jobs['job_type']; ?></span>
          </div>

          <div class="jd-hero-footer" style="margin-top:18px;">
            <div class="jd-posted"><i class="bi bi-clock"></i> <?php echo $jobs['created_at']; ?>  </div>
            <div class="jd-deadline"><i class="bi bi-calendar-x"></i> Deadline: July 30, 2026</div>
          </div>
        </div>
      </div>

    </div>
  </section>  
    
   
  <section class="jd-section">
    <div class="container">
      <div class="row g-4 align-items-start">
 
        
        <div class="col-lg-8" data-aos="fade-right" data-aos-delay="100">
          <div class="jd-detail-card">
 
            <div class="jd-card-header">
              <h3 class="jd-section-title">
                <span class="title-icon"><i class="bi bi-file-text"></i></span>
                Job Overview
              </h3>
            </div>

            <div class="jd-card-body"> 
              <div class="jd-description"> 
          <p> <?php echo $jobs['description']; ?>  </p>           
              </div>
                                                
              <div class="jd-divider"></div>                               
                                                                        
            </div>                                                     
          </div> 
        </div> 
        
        <div class="col-lg-4" data-aos="fade-left" data-aos-delay="150"> 
          <div class="jd-sidebar">
 
            <div class="jd-apply-card">
              <div class="jd-apply-salary">  <?php echo $jobs['salary']; ?>  </div>
              <div class="jd-apply-salary-sub">Per year · Full-Time · Remote</div>  
              
              <?php  if( $jobs['user_id'] != $_SESSION['user_id'] && $_SESSION["role"] != "employer") { ?> 

              <form method="POST">
               <?php  if(mysqli_num_rows($applications) >= 1 ){ ?>
               <button name="apply-job" class="btn-apply-now">
                   <i class="bi bi-send-fill">Applied </i> <i class="fs-3 fa-regular fa-circle-check" style="color: rgb(60, 255, 0);"></i>
               </button>
               <?php   } else{ ?>
      <button name="apply-job" class="btn-apply-now">
          <i class="bi bi-send-fill">Apply Now </i> 
      </button>
      <?php } ?>
    </form>  
                   <?php
                     }elseif( $jobs['user_id'] == $_SESSION['user_id'] && $_SESSION["role"] == "employer") {                     ?> 
                      <button disabled class="btn-apply-now">
                <i class="bi bi-send-fill"></i>  This Is Your Job Posting  
</button>  <?php  } ?>
              <?php
               if( $jobs['user_id'] != $_SESSION['user_id'] && $_SESSION["role"] != "employer") { 
$job_id = $_GET["id"];
$userid = $_SESSION["user_id"];
$checkQuery = "SELECT * FROM saved_jobs WHERE user_id='$userid' AND job_id='$job_id'";
$checkResult = mysqli_query($conn, $checkQuery);
if (mysqli_num_rows($checkResult) > 0) { ?>
    <a class="btn-save-job" href="./applicant/save-jobs.php?id=<?php echo $jobs['id']; ?>">
                  <i class="bi bi-bookmark"></i> Already Saved
                  <!-- <i class="fs-4 fa-solid fa-circle-check" style="color: rgb(60, 255, 0);"></i> -->
                  <i class="fs-3 fa-regular fa-circle-check" style="color: rgb(60, 255, 0);"></i>

                </a>

    <?php 
    }else{ ?>
  <a class="btn-save-job" href="./applicant/save-jobs.php?id=<?php echo $jobs['id']; ?>">
                  <i class="bi bi-bookmark"></i> Save this Job 
                </a>

                <?php   }}elseif($jobs['user_id'] == $_SESSION['user_id'] && $_SESSION["role"] == "employer"){ ?>
   <a class="btn-save-job" href="./employer/add-manage-jobs.php?tab=manage-jobs">
                  <i class="bi bi-bookmark"></i> Manage Job
                </a>
 <?php }   ?>  
 <?php if($_SESSION["role"] == "admin"){ ?>
 <a class="btn-save-job mt-3" href="./employer/add-manage-jobs.php?tab=manage-jobs">
                  <i class="bi bi-bookmark"></i> Manage Job
                </a>
 <?php } ?>
              <div class="jd-apply-note">
                <i class="bi bi-shield-check"></i>
                Your application is 100% secure
              </div>
            </div>
  
               
                         
          </div> 
        </div> 
              
      </div> 
    </div> 
  </section>          
</main>
  
 <?php     
    include './includes/footer.php'; 
 ?>     
 