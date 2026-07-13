<?php
session_start();
include "includes/config.php";

$error = ""; // yahan hum error message store karenge, jo form ke upar dikhega

if (isset($_POST['login'])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = strtolower($_POST["role"]);

    $check = "select * from users where email = '$email' ";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) >= 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            if (strtolower($role) == strtolower($user['role'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
$lowerrole = strtolower($role);
                if ($lowerrole == "employer") {
                    header("Location: ../job-portal/employer/employerDashboard.php");
                    exit();
                } elseif ($lowerrole == "applicant") {
                    header("Location: ../job-portal/applicant/applicantDashboard.php");
                    exit();
                } elseif ($lowerrole == "admin") {
                    header("Location: ../job-portal/admin/Dashboard.php");
                    exit();
                }

            } else {
                $error = "You have selected the wrong role for this account.";
            }

        } else {
            $error = "Incorrect password. Please try again.";
        }

    } else {
        $error = "No account found with this email.";
    }
};


include "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — NovaSphere</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

  .page-wrapper {
    min-height: 100vh;
    background-color: #f1f5f9;
    display: flex;
    flex-direction: row;
  }

  /* ── LEFT: Form ── */
  .left-panel {
    flex: 1;
    background-color: #ffffff;
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-right: 1px solid #e2e8f0;
  }

  .form-header { margin-bottom: 2rem; }

  .logo {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1.5rem;
  }

  .logo-icon {
    width: 36px; height: 36px;
    background-color: #0ea5e9;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
  }

  .logo-icon i { color: #ffffff; font-size: 18px; }
  .logo-name { font-size: 18px; font-weight: 600; color: #1e293b; }

  .form-header h2 { font-size: 22px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
  .form-header p { font-size: 14px; color: #475569; }

  /* ── Error message box ── */
  .login-error-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-size: 13.5px;
    font-weight: 500;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 1.25rem;
  }

  .login-error-box i { font-size: 16px; flex-shrink: 0; }

  .form-group { margin-bottom: 1.1rem; }

  .form-group label {
    display: block;
    font-size: 13px; font-weight: 500;
    color: #1e293b; margin-bottom: 6px;
  }

  .input-wrap { position: relative; }

  .input-wrap i {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    font-size: 16px; color: #94a3b8;
    pointer-events: none;
  }

  .input-wrap input {
    width: 100%;
    height: 42px;
    padding: 0 12px 0 38px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px; color: #1e293b;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
  }

  .input-wrap input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,0.12);
    background: #ffffff;
  }

  .input-wrap input::placeholder { color: #94a3b8; }

  .submit-btn {
    width: 100%; height: 44px;
    background-color: #0ea5e9;
    color: #ffffff;
    border: none; border-radius: 8px;
    font-size: 15px; font-weight: 500;
    cursor: pointer; margin-top: 1.5rem;
    transition: background-color 0.15s, transform 0.1s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
  }

  .submit-btn:hover { background-color: #0284c7; }
  .submit-btn:active { transform: scale(0.98); }

  .login-link {
    text-align: center; margin-top: 1.25rem;
    font-size: 13px; color: #475569;
  }

  .login-link a { color: #0ea5e9; text-decoration: none; font-weight: 500; }

  /* ── RIGHT: Info Panel ── */
  .right-panel {
    flex: 1.1;
    background: linear-gradient(145deg, #0c4a6e 0%, #0369a1 45%, #0ea5e9 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 3.5rem 3rem;
    position: relative;
    overflow: hidden;
  }

  .right-panel::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: rgba(255,255,255,0.05); border-radius: 50%;
  }

  .right-panel::after {
    content: '';
    position: absolute; bottom: -80px; left: -40px;
    width: 340px; height: 340px;
    background: rgba(255,255,255,0.04); border-radius: 50%;
  }

  .right-content { position: relative; z-index: 1; }

  .badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.15);
    color: #e0f2fe; font-size: 12px; font-weight: 500;
    padding: 5px 12px; border-radius: 20px; margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.2);
  }

  .right-content h1 {
    font-size: 30px; font-weight: 600;
    color: #ffffff; line-height: 1.3; margin-bottom: 1rem;
  }

  .right-content h1 span { color: #7dd3fc; }

  .right-content p {
    font-size: 15px; color: #bae6fd;
    line-height: 1.7; margin-bottom: 2rem; max-width: 360px;
  }

  .features { display: flex; flex-direction: column; gap: 14px; }

  .feature-item { display: flex; align-items: flex-start; gap: 12px; }

  .feature-icon {
    width: 34px; height: 34px;
    background: rgba(255,255,255,0.12);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(255,255,255,0.15);
  }

  .feature-icon i { font-size: 16px; color: #7dd3fc; }

  .feature-text strong {
    display: block; font-size: 14px; font-weight: 500;
    color: #ffffff; margin-bottom: 2px;
  }

  .feature-text span { font-size: 13px; color: #93c5fd; line-height: 1.5; }

  /* ── RESPONSIVE ── */
  @media (max-width: 768px) {
    .page-wrapper {
      flex-direction: column;
    }

    .left-panel {
      order: 1;
      border-right: none;
      border-bottom: 1px solid #e2e8f0;
      padding: 2rem 1.25rem;
      justify-content: flex-start;
    }

    .right-panel {
      order: 2;
      padding: 2.5rem 1.25rem;
      align-items: flex-start;
    }

    .right-content h1 { font-size: 22px; }
    .right-content p { font-size: 14px; margin-bottom: 1.5rem; }
  }
</style>
</head>
<body>  
  <?php 
if(isset($_GET["msg"]) && $_GET["msg"] == "password_reset"){ ?>
  <script>alert("Password Reset Successfully ! Please Login")</script>
<?php } ?>
<div class="page-wrapper">

  <!-- LEFT: Form -->
  <div class="left-panel">
    <div class="form-header">
      <div class="logo">
        <div class="logo-icon"><i class="ti ti-bolt"></i></div>
        <span class="logo-name">NovaSphere</span>
      </div>
      <h2>Create your account</h2>
      <p>Sign up for free — no credit card needed</p>
    </div>

    <?php if (!empty($error)) { ?>
      <div class="login-error-box">
        <i class="ti ti-alert-circle"></i>
        <span><?php echo $error; ?></span>
      </div>
    <?php } ?>

    <form method="POST">

      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrap">
          <i class="ti ti-mail"></i>
          <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
        </div>
        <a href="forgot-email.php" class="btn btn-link text-decoration-none">Forgot Email?</a>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <i class="ti ti-lock"></i>
          <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
        </div>
        <button class="btn btn-link text-decoration-none" onclick="window.location.href='forgot-password.php'">Forgot Password ?</button>
      </div>

  <div class="form-group">
        <label for="role">Role</label>
        <div class="input-wrap">
          <i class="ti ti-lock"></i>
          <input type="text" id="role" name="role" placeholder=" Select your role ex: employer , applicant " required value="<?php echo isset($_POST['role']) ? $_POST['role'] : ''; ?>">
        </div>
      </div>


      <button type="submit" name="login" class="login-btn">
        <i class="ti ti-user-plus"></i>
     Login 
      </button>
    </form>

    <p class="login-link">Don't have an account <a >Log in</a></p>
  </div>

  <!-- RIGHT: Info -->
  <div class="right-panel">
    <div class="right-content">
      <div class="badge">
        <i class="ti ti-sparkles"></i>
        Trusted by 50,000+ users
      </div>
      <h1>Everything you need, <span>in one place.</span></h1>
      <p>Join thousands of professionals who use NovaSphere to manage their projects, collaborate in real-time, and ship faster.</p>

      <div class="features">
        <div class="feature-item">
          <div class="feature-icon"><i class="ti ti-shield-check"></i></div>
          <div class="feature-text">
            <strong>Enterprise-grade security</strong>
            <span>Your data is encrypted end-to-end at all times</span>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="ti ti-users"></i></div>
          <div class="feature-text">
            <strong>Real-time collaboration</strong>
            <span>Work with your team seamlessly, anywhere in the world</span>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="ti ti-chart-bar"></i></div>
          <div class="feature-text">
            <strong>Powerful analytics</strong>
            <span>Track progress and insights with live dashboards</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
</body>
</html>
<?php include "includes/footer.php"; ?>