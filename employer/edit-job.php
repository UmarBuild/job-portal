<?php
    session_start();
    include "../../job-portal/includes/config.php";
if (!isset($_SESSION["user_id"])) {
  header("Location: ../login.php");
  exit();
};
if ($_SESSION["role"] != "employer" && $_SESSION["role"] != "admin") {
  header("Location: ../login.php");
  exit();
};

$id = (int)$_GET['id'];
$userid = $_SESSION['user_id'];

$sql = "select * from jobs where id = ? AND user_id = ?";
$result = mysqli_execute_query($conn, $sql, [$id, $userid]);
$job = mysqli_fetch_assoc($result);

if (!$job) {
  die("Job not found or you don't have permission to edit it.");
}

$catSql = "select id, name from categories";
$catResult = mysqli_query($conn, $catSql);

if (isset($_POST['post_job'])) {
  $status = $_POST['status'];
  $categoryId = (int)$_POST['category_id'];
  $jobTitle = trim($_POST['job_title']);
  $companyName = trim($_POST['company_name']);
  $location = trim($_POST['location']);
  $salary = trim($_POST['salary']);
  $job_type = trim($_POST['job_type']);
  $description = trim($_POST['description']);

  $sql = "update jobs set title = ?, company = ?, location = ?, salary = ?, job_type = ?, description = ?, status = ?, category_id = ? where id = ? and user_id = ?";
  $result = mysqli_execute_query($conn, $sql, [
    $jobTitle, $companyName, $location, $salary, $job_type, $description, $status, $categoryId, $id, $userid
  ]);

  if ($result) {
    header("Location: add-manage-jobs.php?tab=manage-jobs");
    exit();
  } else {
    echo "Job updating failed" . mysqli_error($conn);
  }
}

include "../../job-portal/includes/header.php";
?>           
      <head>         
        <style>
            /* ─── FORM CARD ─── */  
            :root {
  --bg:          #f1f5f9;
  --default:     #475569;
  --heading:     #1e293b;
  --accent:      #0ea5e9;
  --accent-dark: #0284c7;
  --accent-soft: rgba(14,165,233,.10);
  --surface:     #ffffff;
  --contrast:    #ffffff;
  --border:      #e2e8f0;
  --sidebar-w:   260px;
  --shadow-sm:   0 2px 12px rgba(14,165,233,.07);
  --shadow-md:   0 8px 32px rgba(30,41,59,.10);
  --shadow-lg:   0 20px 60px rgba(30,41,59,.14);
  --r:           14px;
  --r-sm:        10px;
}
.form-card { 
  background: var(--surface);
  border-radius: var(--r);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-md);
  overflow: hidden;
}

.form-card-head {
  padding: 1.75rem 2rem 1.5rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 14px;
  background: linear-gradient(135deg, #fafcff 0%, #f0f9ff 100%);
}

.fch-icon {
  width: 48px; height: 48px;
  background: var(--accent-soft);
  border-radius: var(--r-sm);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  border: 1px solid rgba(14,165,233,.2);
}

.fch-icon svg { width: 24px; height: 24px; color: var(--accent); }

.fch-icon h3 {
  font-family: 'Raleway', sans-serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--heading);
  margin-bottom: 2px;
}

.fch-icon p { font-size: 0.82rem; color: var(--default); }

.fch-text h3 {
  font-family: 'Raleway', sans-serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--heading);
  margin-bottom: 2px;
}

.fch-text p { font-size: 0.82rem; color: var(--default); }

.form-body { padding: 2rem; }

/* Form Groups */
.form-group { margin-bottom: 1.3rem; }

.form-group label {
  display: block;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--heading);
  margin-bottom: 7px;
  letter-spacing: .2px;
}

.form-group label .req { color: var(--accent); margin-left: 2px; }

.inp-wrap { position: relative; }

.inp-wrap > svg:first-child {
  position: absolute;
  left: 13px; top: 50%;
  transform: translateY(-50%);
  width: 16px; height: 16px;
  color: #94a3b8;
  pointer-events: none;
  transition: color .2s;
  z-index: 1;
}

.inp-wrap.textarea-wrap > svg:first-child {
  top: 15px; transform: none;
}

.inp-wrap input,
.inp-wrap textarea {
  width: 100%;
  height: 46px;
  padding: 0 14px 0 40px;
  border: 1.5px solid var(--border);
  border-radius: var(--r-sm);
  font-family: 'Lato', sans-serif;
  font-size: 0.9rem;
  color: var(--heading);
  background: #f8fafc;
  outline: none;
  transition: all .2s ease;
}

.inp-wrap textarea {
  height: auto;
  min-height: 120px;
  padding: 13px 14px 13px 40px;
  resize: vertical;
  line-height: 1.65;
}

.inp-wrap input::placeholder,
.inp-wrap textarea::placeholder { color: #94a3b8; }

.inp-wrap input:focus,
.inp-wrap textarea:focus {
  border-color: var(--accent);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(14,165,233,.11);
}

.inp-wrap:focus-within > svg:first-child { color: var(--accent); }

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.btn-post {
  width: 100%;
  height: 50px;
  margin-top: 0.25rem;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: var(--r-sm);
  font-family: 'Lato', sans-serif;
  font-size: 0.97rem;
  font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  gap: 10px;
  transition: all .25s ease;
  letter-spacing: .3px;
}

.btn-post svg { width: 19px; height: 19px; transition: transform .25s; }
.btn-post:hover { background: var(--accent-dark); transform: translateY(-2px); box-shadow: 0 10px 28px rgba(14,165,233,.38); }
.btn-post:hover svg { transform: translateX(4px); }
.btn-post:active { transform: translateY(0); }

        </style>
    </head>
<body>
<div class="form-body">
  <form id="jobForm" method="POST">

      <!-- Job Title -->
      <div class="form-group">
          <label for="jobTitle">Job Title <span class="req">*</span></label>
          <div class="inp-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2a5 5 0 015 5v2H7V7a5 5 0 015-5z" />
                  <rect x="3" y="9" width="18" height="13" rx="2" />
              </svg>
              <input type="text" id="jobTitle" name="job_title" placeholder="e.g. Senior Frontend Developer" required autocomplete="off" value="<?php echo htmlspecialchars($job['title']); ?>">
           </div>
        </div>

      <!-- Company + Location -->
      <div class="form-row">
          <div class="form-group">
              <label for="companyName">Company Name <span class="req">*</span></label>
              <div class="inp-wrap">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="2" y="7" width="20" height="15" rx="2" />
                      <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                  </svg>
                  <input type="text" id="companyName" name="company_name" placeholder="e.g. Acme Corp" required autocomplete="off" value="<?php echo htmlspecialchars($job['company']); ?>">
              </div>
          </div>
          <div class="form-group">
              <label for="location">Location <span class="req">*</span></label>
              <div class="inp-wrap">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z" />
                      <circle cx="12" cy="10" r="3" />
                  </svg>
                  <input type="text" id="location" name="location" placeholder="e.g. Karachi, Pakistan" required autocomplete="off" value="<?php echo htmlspecialchars($job['location']); ?>">
               </div>
            </div>
      </div>

   <div class="d-flex gap-3 mb-2">
              <!-- Category -->
               <div>
                 <label for="select-category">Select Category</label>
                 <select name="category_id" class="form-select" style="max-width: 260px; padding: 8px 14px; font-size: 14px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.15); background-color: var(--surface-color); color: var(--default-color); outline: none;">

                   <option disabled>Select Category</option>
                   <?php
                   while ($cat = mysqli_fetch_assoc($catResult)) {
                   ?>
                     <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($cat['id'] == $job['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                   <?php
                   }
                   ?>
                 </select>
               </div>
               <!-- Required Skills -->
                <div>
                  <label>Required Skills (comma separated)</label>
                  <input type="text" name="required_skills" placeholder="e.g. PHP, MySQL, JavaScript, Communication" required value="<?php echo htmlspecialchars($job['required_skills'] ?? ''); ?>">
                </div>
              <!-- Status -->
               <div>
                 <label for="select-status">Status</label>
                <select name="status" class="form-select" style="max-width: 260px; font-size: 14px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.15); background-color: var(--surface-color); color: var(--default-color); outline: none;">
                  <option value="open" <?php echo ($job['status'] == 'open') ? 'selected' : ''; ?>>Open</option>
                  <option value="closed" <?php echo ($job['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
                </select>
               </div>

            </div>

      <!-- Salary -->
      <div class="form-group">
          <label for="salary">Salary <span class="req">*</span></label>
          <div class="inp-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23" />
                  <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
              </svg>
              <input type="text" id="salary" name="salary" placeholder="e.g. PKR 80,000 – 120,000 / month" required autocomplete="off" value="<?php echo htmlspecialchars($job['salary']); ?>">
          </div>
      </div>

      <!-- Job Time / Type -->
      <div class="col-md-6">
        <label class="form-label">Job Type</label>
        <select name="job_type" class="form-select" required>
            <option value="" disabled <?php echo empty($job['job_type']) ? 'selected' : ''; ?>>Select Job Type</option>
            <?php
            $jobTypes = ["Full Time", "Part Time", "Internship", "Contract", "Remote", "Hybrid"];
            foreach ($jobTypes as $type) { ?>
                <option value="<?php echo $type; ?>" <?php echo ($job['job_type'] == $type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
            <?php } ?>
        </select>
      </div>

      <!-- Description -->
      <div class="form-group">
          <label for="description">Job Description <span class="req">*</span></label>
          <div class="inp-wrap textarea-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                  <polyline points="14 2 14 8 20 8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
              </svg>
              <textarea id="description" name="description" placeholder="Describe role, responsibilities, requirements…" required rows="5"><?php echo htmlspecialchars($job['description']); ?></textarea>
          </div>
      </div>

      <button type="submit" class="btn-post" name="post_job">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M22 2L11 13" />
              <path d="M22 2L15 22 11 13 2 9l20-7z" />
          </svg>
         Update Job
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="5" y1="12" x2="19" y2="12" />
              <polyline points="12 5 19 12 12 19" />
          </svg>
      </button>

  </form>
</div>
</body>    
      
  <?php      
    include "../../job-portal/includes/footer.php";   
    ?>   
      