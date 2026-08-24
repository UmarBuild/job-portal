

<?php
// include(__DIR__ . "/config.php");   
if (session_status() === PHP_SESSION_NONE) {
   session_start(); 
   } ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>HireHub — Find Your Next Job</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/job-portal/node_modules/@tabler/icons-webfont/dist/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&family=Lato:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link href="/job-portal/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/job-portal/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="/job-portal/assets/css/main.css" rel="stylesheet">

<style>
/* =========================================================
   HireHub - Responsive Job Portal Header
   Independent styles: avoids Bootstrap dropdown positioning
   conflicts and old .btn-getstarted rules.
   ========================================================= */

.header {
  z-index: 9999;
}

.header .container {
  min-height: 72px;
  width: 100%;
  gap: 20px;
}

.header .logo {
  flex: 0 0 auto;
  gap: 9px !important;
  text-decoration: none !important;
  margin-right: 0 !important;
}

.header .logo-mark {
  width: 38px;
  height: 38px;
  flex: 0 0 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 11px;
  /* background: linear-gradient(135deg, #0ea5e9, #0284c7); */
  color: #fff;
  box-shadow: 0 6px 16px rgba(14,165,233,.22);
}

.header .logo-mark i {
  font-size: 19px;
  line-height: 1;
}

.header .logo .sitename {
  margin: 0 !important;
  font-size: 25px;
  line-height: 1;
  font-weight: 800;
  letter-spacing: -0.7px;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  color: #0ea5e9;
}

.header .logo span {
  color: #0ea5e9;
  margin-left: -7px;
}

/* Main navigation */
.header .navmenu {
  margin-left: auto;
}

.header .navmenu > ul {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 2px;
  margin: 0;
  padding: 0;
}

.header .navmenu > ul > li {
  white-space: nowrap;
}

.header .navmenu > ul > li > a:not(.auth-btn) {
  display: inline-flex;
  align-items: center;
  min-height: 40px;
  padding: 8px 11px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: background .2s ease, color .2s ease;
}

.header .navmenu > ul > li > a:not(.auth-btn):hover {
  background: #f0f9ff;
  color: #0284c7;
}

/* Notification */
.nav-notif {
  display: flex !important;
  align-items: center;
  margin: 0 2px 0 6px;
}

.nav-notif > a {
  position: relative;
  width: 40px;
  height: 40px;
  display: inline-flex !important;
  align-items: center;
  justify-content: center;
  padding: 0 !important;
  border-radius: 50%;
  text-decoration: none;
}

.nav-notif > a:hover {
  background: #f0f9ff;
}

.nav-notif i {
  font-size: 19px;
  line-height: 1;
}

.notification-badge {
  position: absolute;
  top: 0;
  right: -1px;
  min-width: 17px;
  height: 17px;
  padding: 0 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #dc2626;
  color: #fff;
  border: 2px solid #fff;
  border-radius: 999px;
  font-size: 9px;
  font-weight: 700;
  line-height: 1;
}

/* =========================================================
   Account menu - custom dropdown
   No Bootstrap dropdown classes/positioning.
   ========================================================= */
.profile-nav {
  position: relative !important;
  display: flex !important;
  align-items: center;
  margin-left: 3px;
}

.profile-trigger {
  appearance: none;
  -webkit-appearance: none;
  display: inline-flex !important;
  align-items: center;
  gap: 8px;
  height: 44px;
  padding: 5px 10px 5px 6px !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 11px !important;
  background: #fff !important;
  color: #374151 !important;
  box-shadow: 0 1px 2px rgba(15,23,42,.04) !important;
  font: inherit;
  cursor: pointer;
  transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
}

.profile-trigger:hover,
.profile-trigger:focus-visible,
.profile-nav.is-open .profile-trigger {
  outline: none;
  border-color: rgba(14,165,233,.45) !important;
  background: #f8fafc !important;
  box-shadow: 0 0 0 3px rgba(14,165,233,.08) !important;
}

.profile-avatar {
  width: 32px;
  height: 32px;
  flex: 0 0 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: #fff;
  box-shadow: 0 3px 8px rgba(14,165,233,.22);
}

.profile-avatar i {
  font-size: 15px;
  line-height: 1;
}

.profile-label {
  font-size: 13px;
  line-height: 1;
  font-weight: 600;
}

.profile-chevron {
  font-size: 11px;
  margin-left: 1px;
  transition: transform .2s ease;
}

.profile-nav.is-open .profile-chevron {
  transform: rotate(180deg);
}

/* Fixed desktop anchor: always opens under Account, never jumps sides */
.profile-panel {
  position: absolute !important;
  top: calc(100% + 9px) !important;
  right: 0 !important;
  left: auto !important;
  transform: none !important;
  z-index: 10050;
  width: 245px;
  max-width: calc(100vw - 24px);
  padding: 7px;
  margin: 0 !important;
  display: none;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 13px;
  box-shadow: 0 18px 45px rgba(15,23,42,.14);
}

.profile-nav.is-open .profile-panel {
  display: block !important;
}

.profile-panel-header {
  padding: 9px 10px 8px;
  margin-bottom: 3px;
  border-bottom: 1px solid #f1f5f9;
}

.profile-panel-title {
  margin: 0;
  color: #111827;
  font-size: 13px;
  font-weight: 700;
}

.profile-panel-role {
  margin-top: 2px;
  color: #94a3b8;
  font-size: 11px;
  font-weight: 500;
}

.profile-panel a {
  display: flex !important;
  align-items: center !important;
  gap: 9px !important;
  width: 100% !important;
  min-height: 39px !important;
  box-sizing: border-box !important;
  padding: 8px 10px !important;
  margin: 1px 0 !important;
  border-radius: 8px !important;
  color: #374151 !important;
  background: transparent !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  line-height: 1.2 !important;
  text-decoration: none !important;
  white-space: nowrap;
}

.profile-panel a:hover {
  color: #0284c7 !important;
  background: #f0f9ff !important;
}

.profile-panel a i {
  width: 20px !important;
  min-width: 20px !important;
  margin: 0 !important;
  padding: 0 !important;
  text-align: center !important;
  font-size: 16px !important;
  line-height: 1 !important;
}

.profile-panel .panel-divider {
  height: 1px;
  margin: 6px 3px;
  background: #eef2f7;
}

.profile-panel a.logout-item {
  color: #dc2626 !important;
}

.profile-panel a.logout-item:hover {
  color: #b91c1c !important;
  background: #fef2f2 !important;
}

/* Login / Register - own flex container, never full width */
.auth-actions {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-end !important;
  gap: 8px !important;
  margin-left: 8px !important;
  padding: 0 !important;
  width: auto !important;
  max-width: none !important;
}

.auth-actions .auth-btn {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: auto !important;
  min-width: 0 !important;
  max-width: none !important;
  height: 40px !important;
  padding: 0 16px !important;
  margin: 0 !important;
  border-radius: 9px !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  line-height: 1 !important;
  text-decoration: none !important;
  white-space: nowrap !important;
  box-sizing: border-box !important;
}

.auth-actions .login-btn {
  color: #0284c7 !important;
  background: #fff !important;
  border: 1px solid #dbeafe !important;
}

.auth-actions .login-btn:hover {
  background: #f0f9ff !important;
  border-color: #7dd3fc !important;
}

.auth-actions .register-btn {
  color: #fff !important;
  background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
  border: 1px solid transparent !important;
  box-shadow: 0 5px 13px rgba(14,165,233,.18) !important;
}

.auth-actions .register-btn:hover {
  color: #fff !important;
  opacity: .93;
  transform: translateY(-1px);
}

/* Mobile nav panel compatibility */
@media (max-width: 1199px) {
  .header .container {
    min-height: 66px;
    gap: 10px;
  }

  .header .logo {
    margin-right: auto !important;
  }

  .header .logo-mark {
    width: 36px;
    height: 36px;
    flex-basis: 36px;
  }

  .header .navmenu > ul > li > a:not(.auth-btn) {
    padding: 10px 12px;
  }

  .profile-label {
    display: none;
  }

  .profile-trigger {
    width: 40px;
    height: 40px;
    justify-content: center;
    padding: 4px !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
  }

  .profile-trigger:hover,
  .profile-trigger:focus-visible,
  .profile-nav.is-open .profile-trigger {
    background: #f0f9ff !important;
    box-shadow: none !important;
  }

  .profile-chevron {
    display: none;
  }

  /* The panel is anchored to Account itself, not the viewport */
  .profile-panel {
    top: calc(100% + 8px) !important;
    right: 0 !important;
    left: auto !important;
    width: 245px;
  }

  .auth-actions {
    width: auto !important;
    justify-content: center !important;
    margin: 12px 0 4px !important;
    padding: 12px 0 0 !important;
    border-top: 1px solid rgba(0,0,0,.08);
    gap: 8px !important;
  }

  .auth-actions .auth-btn {
    width: auto !important;
    flex: 0 0 auto !important;
  }
}

@media (max-width: 575px) {
  .header .container {
    padding-left: 14px;
    padding-right: 14px;
  }

  .header .logo {
    gap: 7px !important;
  }

  .header .logo-mark {
    width: 34px;
    height: 34px;
    flex-basis: 34px;
    border-radius: 10px;
  }

  .header .logo-mark i {
    font-size: 17px;
  }

  .header .logo .sitename {
    font-size: 23px;
  }

  .header .logo span {
    margin-left: -6px;
  }

  .nav-notif {
    margin-left: 0;
  }

  .nav-notif > a {
    width: 38px;
    height: 38px;
  }

  .profile-avatar {
    width: 31px;
    height: 31px;
    flex-basis: 31px;
  }

  /* Keep dropdown inside viewport on phones */
  .profile-panel {
    position: fixed !important;
    top: 62px !important;
    right: 12px !important;
    left: auto !important;
    width: min(245px, calc(100vw - 24px)) !important;
    max-width: calc(100vw - 24px) !important;
  }

  .auth-actions .auth-btn {
    height: 39px !important;
    padding: 0 13px !important;
  }
}

@media (max-width: 360px) {
  .header .logo .sitename {
    font-size: 21px;
  }

  .header .logo-mark {
    width: 32px;
    height: 32px;
    flex-basis: 32px;
  }

  .auth-actions {
    gap: 6px !important;
  }

  .auth-actions .auth-btn {
    padding: 0 10px !important;
    font-size: 12px !important;
  }
}
</style>

<style id="hirehub-final-overrides">
/* Final isolation from main.css */
.header .container { width:100% !important; max-width:1320px !important; }
.header .logo { margin-right:0 !important; flex-shrink:0 !important; }
.header .navmenu > ul > li > a.auth-btn {
  display:inline-flex !important;
}
.auth-actions .auth-btn {
  position:relative !important;
  overflow:hidden !important;
  transform:none !important;
}
.auth-actions .login-btn,
.auth-actions .login-btn:visited,
.auth-actions .login-btn:focus,
.auth-actions .login-btn:active {
  color:#0284c7 !important;
  background:#fff !important;
  border:1px solid #0ea5e9 !important;
  box-shadow:none !important;
}
.auth-actions .login-btn:hover {
  color:#fff !important;
  background:#0ea5e9 !important;
  border-color:#0ea5e9 !important;
  box-shadow:0 4px 12px rgba(14,165,233,.18) !important;
}
.auth-actions .register-btn,
.auth-actions .register-btn:visited,
.auth-actions .register-btn:focus,
.auth-actions .register-btn:active {
  color:#fff !important;
  background:#0ea5e9 !important;
  border:1px solid #0ea5e9 !important;
  box-shadow:0 4px 12px rgba(14,165,233,.20) !important;
  opacity:1 !important;
}
.auth-actions .register-btn:hover {
  color:#0284c7 !important;
  background:#fff !important;
  border-color:#0ea5e9 !important;
  box-shadow:none !important;
  opacity:1 !important;
}
.profile-nav { position:relative !important; overflow:visible !important; }
.profile-panel {
  position:absolute !important;
  top:calc(100% + 9px) !important;
  right:0 !important; left:auto !important;
  transform:none !important;
  margin:0 !important;
  z-index:100000 !important;
}
@media (max-width:575px) {
  .profile-panel {
    position:fixed !important;
    top:62px !important; right:12px !important; left:auto !important;
    width:min(245px,calc(100vw - 24px)) !important;
    transform:none !important;
  }
  .auth-actions .auth-btn {
    height:36px !important; padding:0 10px !important; font-size:12px !important;
  }
}
</style>


<style id="hirehub-size-spacing-final">
/* ===== Larger, cleaner typography ===== */
.header .navmenu > ul > li > a:not(.auth-btn) {
  min-height: 44px !important;
  padding: 9px 14px !important;
  font-family: "Lato", sans-serif !important;
  font-size: 15.5px !important;
  font-weight: 600 !important;
  letter-spacing: .05px !important;
}

/* ===== Bigger notification bell ===== */
.nav-notif {
  margin: 0 5px 0 9px !important;
}
.nav-notif > a {
  width: 46px !important;
  height: 46px !important;
}
.nav-notif i {
  font-size: 24px !important;
  font-weight: 500 !important;
}
.notification-badge {
  top: 0 !important;
  right: -1px !important;
  min-width: 18px !important;
  height: 18px !important;
  font-size: 10px !important;
}

/* ===== Larger Account control ===== */
.profile-trigger {
  height: 46px !important;
  padding: 5px 11px 5px 6px !important;
  gap: 8px !important;
  font-family: "Lato", sans-serif !important;
}
.profile-avatar {
  width: 36px !important;
  height: 36px !important;
  flex-basis: 36px !important;
}
.profile-avatar i {
  font-size: 18px !important;
}
.profile-label {
  font-size: 14.5px !important;
  font-weight: 600 !important;
}

/* ===== Dropdown: bigger items, tight icon/text spacing ===== */
.profile-panel {
  width: 275px !important;
  padding: 9px !important;
  border-radius: 14px !important;
}
.profile-panel-header {
  padding: 11px 12px 10px !important;
  margin-bottom: 4px !important;
}
.profile-panel-title {
  font-family: "Raleway", sans-serif !important;
  font-size: 15px !important;
  font-weight: 700 !important;
}
.profile-panel-role {
  font-family: "Lato", sans-serif !important;
  font-size: 12px !important;
}
.profile-panel a {
  min-height: 44px !important;
  padding: 9px 11px !important;
  gap: 7px !important;
  margin: 1px 0 !important;
  border-radius: 9px !important;
  font-family: "Lato", sans-serif !important;
  font-size: 14.5px !important;
  font-weight: 600 !important;
}
/* Remove the artificial fixed icon width that was creating the big gap. */
.profile-panel a i {
  width: 22px !important;
  min-width: 22px !important;
  flex: 0 0 22px !important;
  margin: 0 !important;
  font-size: 18px !important;
}
.profile-panel a span {
  margin: 0 !important;
  padding: 0 !important;
}
.profile-panel .panel-divider {
  margin: 7px 3px !important;
}

/* ===== Login + Register: both blue, hover white ===== */
.auth-actions {
  gap: 9px !important;
}
.auth-actions .auth-btn {
  height: 42px !important;
  padding: 0 17px !important;
  border-radius: 9px !important;
  font-family: "Lato", sans-serif !important;
  font-size: 14px !important;
  font-weight: 700 !important;
  transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease !important;
}
.auth-actions .login-btn,
.auth-actions .login-btn:visited,
.auth-actions .login-btn:focus,
.auth-actions .login-btn:active,
.auth-actions .register-btn,
.auth-actions .register-btn:visited,
.auth-actions .register-btn:focus,
.auth-actions .register-btn:active {
  color: #fff !important;
  background: #0ea5e9 !important;
  border: 1px solid #0ea5e9 !important;
  box-shadow: 0 5px 14px rgba(14,165,233,.18) !important;
  opacity: 1 !important;
}
.auth-actions .login-btn:hover,
.auth-actions .register-btn:hover {
  color: #0284c7 !important;
  background: #fff !important;
  border-color: #0ea5e9 !important;
  box-shadow: 0 5px 14px rgba(14,165,233,.12) !important;
  transform: translateY(-1px) !important;
}

/* Mobile: keep everything usable while still larger than before */
@media (max-width: 1199px) {
  .header .navmenu > ul > li > a:not(.auth-btn) {
    font-size: 15px !important;
    padding: 10px 13px !important;
  }
  .nav-notif > a {
    width: 44px !important;
    height: 44px !important;
  }
  .nav-notif i {
    font-size: 23px !important;
  }
  .profile-panel {
    width: 275px !important;
  }
}
@media (max-width: 575px) {
  .profile-panel {
    width: min(275px, calc(100vw - 24px)) !important;
  }
  .auth-actions .auth-btn {
    height: 40px !important;
    padding: 0 13px !important;
    font-size: 13px !important;
  }
}
</style>

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="/job-portal/index.php" class="logo d-flex align-items-center">
        <span class="logo-mark"><i class="bi bi-briefcase-fill"></i></span>
        <h1 class="sitename">HireHub</h1><span>.</span>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "admin") { ?>
            <li><a href="/job-portal/index.php">Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>

          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "employer") { ?>
            <li><a href="/job-portal/index.php">Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>

          <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "applicant") { ?>
            <li><a href="/job-portal/index.php">Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
          <?php } ?>

          <?php if (!isset($_SESSION["user_id"])) { ?>
            <li><a href="/job-portal/index.php">Home</a></li>
            <li><a href="/job-portal/jobs.php">All Jobs</a></li>
            <li><a href="/job-portal/about.php">About</a></li>
            <li><a href="/job-portal/contact.php">Contact</a></li>
            <li class="auth-actions">
              <a class="auth-btn login-btn" href="/job-portal/login.php">Login</a>
              <a class="auth-btn register-btn" href="/job-portal/register.php">Register</a>
            </li>
          <?php } else { ?>

            <?php
              $unreadCount = 0;
              if (isset($conn)) {
                $notifCountQuery = "SELECT COUNT(*) AS total FROM notifications WHERE user_id = ? AND is_read = 0";
                $notifCountResult = mysqli_execute_query($conn, $notifCountQuery, [$_SESSION['user_id']]);
                if ($notifCountResult) {
                  $unreadCount = (int) mysqli_fetch_assoc($notifCountResult)['total'];
                }
              }
            ?>

            <!-- Notification -->
            <li class="nav-notif">
              <a href="/job-portal/notifications.php" aria-label="Notifications">
                <i class="bi bi-bell"></i>
                <?php if ($unreadCount > 0) { ?>
                  <span class="notification-badge">
                    <?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?>
                  </span>
                <?php } ?>
              </a>
            </li>

            <!-- Account -->
            <li class="profile-nav">
              <button
                class="profile-trigger"
                type="button"
                id="profileTrigger"
                aria-expanded="false"
                aria-controls="profilePanel"
                aria-label="Open account menu"
              >
                <span class="profile-avatar">
                  <i class="bi bi-person-fill"></i>
                </span>
                <span class="profile-label">Account</span>
                <i class="bi bi-chevron-down profile-chevron"></i>
              </button>

              <div class="profile-panel" id="profilePanel" aria-hidden="true">
                <div class="profile-panel-header">
                  <div class="profile-panel-title">My Account</div>
                  <div class="profile-panel-role"><?php echo ucfirst($_SESSION["role"]); ?> account</div>
                </div>

                <?php if ($_SESSION["role"] === "applicant") { ?>
                  <a href="/job-portal/applicant/applicantDashboard.php">
                    <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
                  </a>
                  <a href="/job-portal/applicant/applicant-profile.php">
                    <i class="bi bi-person"></i><span>Profile</span>
                  </a>
                  <a href="/job-portal/applicant/applied-jobs.php">
                    <i class="bi bi-briefcase"></i><span>Applied Jobs</span>
                  </a>
                  <a href="/job-portal/applicant/saved-jobs.php">
                    <i class="bi bi-bookmark"></i><span>Saved Jobs</span>
                  </a>

                <?php } elseif ($_SESSION["role"] === "employer") { ?>
                  <a href="/job-portal/employer/employerDashboard.php">
                    <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
                  </a>
                  <a href="/job-portal/employer/company-profile.php">
                    <i class="bi bi-building"></i><span>Company Profile</span>
                  </a>
                  <a href="/job-portal/employer/add-manage-jobs.php">
                    <i class="bi bi-briefcase"></i><span>Manage Jobs</span>
                  </a>
                  <a href="/job-portal/employer/employer-applications.php">
                    <i class="bi bi-people"></i><span>Applications</span>
                  </a>

                <?php } elseif ($_SESSION["role"] === "admin") { ?>
                  <a href="/job-portal/admin/dashboard.php">
                    <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
                  </a>
                  <a href="/job-portal/admin/manage-users.php">
                    <i class="bi bi-people"></i><span>Manage Users</span>
                  </a>
                  <a href="/job-portal/admin/manage-jobs.php">
                    <i class="bi bi-briefcase"></i><span>Manage Jobs</span>
                  </a>
                  <a href="/job-portal/admin/manage-applications.php">
                    <i class="bi bi-file-earmark-text"></i><span>Applications</span>
                  </a>
                  <a href="/job-portal/admin/manage-categories.php">
                    <i class="bi bi-tags"></i><span>Categories</span>
                  </a>
                <?php } ?>

                <div class="panel-divider"></div>
                <a class="logout-item" href="/job-portal/logout.php">
                  <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                </a>
              </div>
            </li>
          <?php } ?>

        </ul>

        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <script>
    (function () {
      const nav = document.querySelector('.profile-nav');
      const trigger = document.getElementById('profileTrigger');
      const panel = document.getElementById('profilePanel');
      if (!nav || !trigger || !panel) return;

      function closeMenu() {
        nav.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
      }

      trigger.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const open = !nav.classList.contains('is-open');
        if (open) {
          nav.classList.add('is-open');
          trigger.setAttribute('aria-expanded', 'true');
          panel.setAttribute('aria-hidden', 'false');
        } else {
          closeMenu();
        }
      });

      panel.addEventListener('click', function (e) {
        e.stopPropagation();
      });

      document.addEventListener('click', function (e) {
        if (!nav.contains(e.target)) closeMenu();
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
      });

      window.addEventListener('resize', closeMenu);
    })();
  </script>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>