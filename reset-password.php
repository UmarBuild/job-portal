<?php
session_start();
include("includes/config.php");
$email = $_GET["email"];
if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
$password = password_hash($new_password, PASSWORD_DEFAULT);
    $query = "update users set password = '$password' WHERE email='$email' ";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: login.php?msg='password_reset'");
    } else {
        echo "Password Reset Failed";
    };
};
include("includes/header.php");
?>
<style>
    body {
        min-height: 100vh;
        overflow: hidden;
        background-color: var(--background-color);
        font-family: var(--default-font);
        margin: 0;
        padding: 20px;
        box-sizing: border-box;
    }

    /* Reset Password Main Card */
    .reset-password-card {
        background-color: var(--surface-color);
        border-radius: 16px;
        padding: 40px 32px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 10px 25px rgba(30, 41, 59, 0.05);
        border: 1px solid #e2e8f0;
        text-align: center;
        animation: slideUp 0.5s ease-in-out;
    }

    /* Shield/Keyhole Visual Icon */
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

    .reset-password-card h2 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .reset-password-card p {
        font-size: 14px;
        color: var(--default-color);
        margin-bottom: 28px;
        line-height: 1.5;
    }

    /* Input Wrapper Group */
    .form-group {
        position: relative;
        margin-bottom: 24px;
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

    .form-group input[type="password"] {
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
        box-sizing: border-box;
    }

    .form-group input[type="password"]:focus {
        border-color: var(--accent-color);
        background-color: var(--surface-color);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
    }

    /* Submit Button styling */
    .btn-reset {
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

    .btn-reset:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(14, 165, 233, 0.35);
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<div class="d-flex justify-content-center align-items-center m-5">
    <div class="reset-password-card">
        <div class="icon-box">
            <i class="bi bi-key-fill"></i>
        </div>
        
        <h2>Create New Password</h2>
        <p>Please enter a strong, secure new password to regain access to your dashboard account.</p>
    
        <form method="POST">
            <div class="form-group">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="new_password" placeholder="New Password" required>
            </div>
    
            <button type="submit" name="reset_password" class="btn-reset">
                Reset Password <i class="bi bi-check-circle-fill ms-1 align-middle fs-6"></i>
            </button>
        </form>
    </div>   
</div>
<?php include "includes/footer.php"; ?>