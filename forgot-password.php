<?php
session_start();
include("includes/config.php");

if (isset($_POST['check_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "SELECT id FROM users WHERE email=? ";

    $result = mysqli_execute_query($conn, $query,[$email]);

    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $query = "update users set reset_token = ?, reset_token_expiry = ? where email = ?";
        $result = mysqli_execute_query($conn, $query,[$token,$expiry,$email]);
        if ($result) {
            $url = "http://localhost/job-portal/reset-password.php?email=$email&token=$token";
            $to = $email;
            $subject = "Password Reset Link";
            $message = "Click on the link to reset your password: $url";
            require "mail/mail.php";
            if(sendMail($to, $subject, $message)){
                $success = "If this email is registered, a password reset link has been sent to your email.";
            }else{
                $error = "Email Not Sent";
            }
        }
        // else {
        //     echo "Email Not Found";
        // }
        }
}
include("includes/header.php");
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
        /* Screen ke perfectly center me lane ke liye */
        /* overflow: hidden; */
        /* 100vh ke under pakka bound rakhne ke liye */
        padding: 20px;
    }

    /* Forgot Password Wrapper Card */
    .forgot-password-card {
        background-color: var(--surface-color);
        border-radius: 16px;
        padding: 40px 32px;
        width: 100%;
        max-width: 420px;
        /* Bada dikhe par bounds me rahe */
        box-shadow: 0 10px 25px rgba(30, 41, 59, 0.05);
        border: 1px solid #e2e8f0;
        text-align: center;
        animation: fadeIn 0.5s ease-in-out;
    }

    /* Top Icon Styling */
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

    /* Form Controls */
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

    .form-group input[type="email"] {
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

    .form-group input[type="email"]:focus {
        border-color: var(--accent-color);
        background-color: var(--surface-color);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
    }

    /* Submit Button Styling */
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

    /* Bottom Back Link */
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

    /* ---------- Success State ---------- */
    .success-box {
        background-color: rgba(22, 163, 74, 0.08);
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 24px 20px;
        text-align: center;
        margin-bottom: 8px;
    }

    .success-icon {
        width: 56px;
        height: 56px;
        background: rgba(22, 163, 74, 0.12);
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: 0 auto 14px;
    }

    .success-box h3 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .success-box p {
        font-size: 13.5px;
        color: var(--default-color);
        line-height: 1.6;
        margin-bottom: 0;
    }

    .success-box .highlight-email {
        color: var(--heading-color);
        font-weight: 700;
    }

    .resend-note {
        margin-top: 18px;
        font-size: 12.5px;
        color: #94a3b8;
    }

    @media (max-width: 480px) {
    .forgot-password-card {
        padding: 28px 20px;
    }
}

</style>

<body>
    <div class="d-flex justify-content-center align-items-center m-5">
        <div class="forgot-password-card">
            <div class="icon-box">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h2>Forgot Password?</h2>
            <p>Enter your email address below and we will send you instructions to reset your password.</p>
            <?php if (!empty($success)) { ?>
                <div class="success-box">
                    <div class="success-icon">
                        <i class="bi bi-envelope-check-fill"></i>
                    </div>
                    <h3>Check your inbox</h3>
                    <p><?php echo $success; ?></p>
                </div>
                <p class="resend-note">Didn't get the email? Check your spam folder, or try again in a few minutes.</p>
            <?php } else { ?>
                <form method="POST">
                    <div class="form-group">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="Enter Registered Email" required>
                    </div>

                    <button type="submit" name="check_email" class="btn-submit">
                        Continue <i class="bi bi-arrow-right-short fs-5 align-middle"></i>
                    </button>
                </form>
            <?php } ?>
            <a href="./login.php" class="back-to-login">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</body>
<?php include "includes/footer.php"; ?>