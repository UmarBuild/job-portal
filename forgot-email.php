<?php
session_start();
include("includes/config.php");
 
$errorMsg = "";
$successMsg = "";
 
if (isset($_POST['change_email'])) {
    $oldEmail = $_POST['old_email'];
    $password = $_POST['password'];
    $newEmail = $_POST['new_email'];
    
    $check = "select * from users where email = '$oldEmail' ";
    $result = mysqli_query($conn, $check);
 
    if (mysqli_num_rows($result) >= 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
 
            $userid = $user['id'];
            $update = "update users set email = '$newEmail' where id = $userid";
            $updateResult = mysqli_query($conn, $update);
 
            if ($updateResult) { ?>
                <script>
                    alert("Email updated successfully! Please login with your new email.");
                    window.location.href = "login.php";
                </script>
      <?php      } else {
                $errorMsg = "Something went wrong. Please try again.";
            }
        } else {
            $errorMsg = "Incorrect password.";
        }
    } else {
        $errorMsg = "This email is not registered.";
    }
}

?>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: var(--default-font);
        background-color: var(--background-color);
        color: var(--default-color);
        min-height: 100vh;
        overflow: hidden;
        padding: 20px;
    }

    .forgot-password-card {
        background-color: var(--surface-color);
        border-radius: 16px;
        padding: 40px 32px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 10px 25px rgba(30, 41, 59, 0.05);
        border: 1px solid #e2e8f0;
        text-align: center;
        animation: fadeIn 0.5s ease-in-out;
    }

    .icon-box {
        width: 64px;
        height: 64px;
        background: rgba(14, 165, 233, 0.1);
        color: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 20px;
    }

    .forgot-password-card h2 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .forgot-password-card p {
        font-size: 14px;
        color: var(--default-color);
        margin-bottom: 28px;
        line-height: 1.5;
    }

    .form-group {
        position: relative;
        margin-bottom: 20px;
        text-align: left;
    }

    .form-group i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
    }

    .form-group input[type="text"] {
        width: 100%;
        padding: 14px 14px 14px 42px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        color: var(--heading-color);
        font-size: 14px;
        font-family: var(--default-font);
        outline: none;
        transition: all 0.3s;
    }

    .form-group input[type="text"]:focus {
        border-color: var(--accent-color);
        background-color: var(--surface-color);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--accent-color), #0284c7);
        color: var(--contrast-color);
        font-size: 15px;
        font-weight: 700;
        font-family: var(--nav-font);
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(14, 165, 233, 0.35);
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }

    .back-to-login {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 24px;
        font-size: 13px;
        font-weight: 600;
        color: var(--default-color);
        text-decoration: none;
        transition: color 0.2s;
    }

    .back-to-login:hover {
        color: var(--accent-color);
    }

    .result-box {
        background: rgba(14, 165, 233, 0.08);
        border: 1px solid rgba(14, 165, 233, 0.25);
        color: var(--heading-color);
        padding: 14px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .error-box {
        background: rgba(220, 38, 38, 0.08);
        border: 1px solid rgba(220, 38, 38, 0.25);
        color: #dc2626;
        padding: 14px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<?php include("includes/header.php"); ?>  


<body>
    <div class="d-flex justify-content-center align-items-center m-5">
        <div class="forgot-password-card">
            <div class="icon-box">
                <i class="bi bi-envelope-paper-fill"></i>
            </div>
 
            <h2>Change Email</h2>
            <p>Enter your current email and password to verify it's you, then set your new email.</p>
 
            <?php if (!empty($successMsg)) { ?>
                <div class="success-box"><?php echo $successMsg; ?></div>
            <?php } ?>
 
            <?php if (!empty($errorMsg)) { ?>
                <div class="error-box"><?php echo $errorMsg; ?></div>
            <?php } ?>
 
            <form method="POST">
                <div class="form-group">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="old_email" placeholder="Current Email" required>
                </div>
 
                <div class="form-group">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" placeholder="Current Password" required>
                </div>
 
                <div class="form-group">
                    <i class="bi bi-envelope-plus"></i>
                    <input type="email" name="new_email" placeholder="New Email" required>
                </div>
 
                <button type="submit" name="change_email" class="btn-submit">
                    Update Email <i class="bi bi-arrow-right-short fs-5 align-middle"></i>
                </button>
            </form>
 
            <a href="./login.php" class="back-to-login">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</body>

<?php include "includes/footer.php"; ?>