<footer id="footer" class="footer light-background">
  <style>
    .footer {
      background-color: var(--background-color);
      font-family: var(--default-font);
      color: var(--default-color);
      padding-top: 60px;
      border-top: 1px solid color-mix(in srgb, var(--heading-color) 8%, transparent);
    }
 
    .footer .footer-top { padding-bottom: 40px; }
 
    .footer .logo {
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      margin-bottom: 18px;
    }
    .footer .logo .sitename {
      font-family: var(--heading-font);
      font-size: 26px;
      font-weight: 800;
      color: var(--heading-color);
      letter-spacing: 0.02em;
    }
    .footer .logo .sitename span { color: var(--accent-color); }
 
    .footer-info p {
      font-size: 14px;
      line-height: 1.8;
      color: var(--default-color);
      max-width: 320px;
      margin-bottom: 20px;
    }
 
    .footer .social-links a {
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background-color: color-mix(in srgb, var(--accent-color) 10%, var(--surface-color));
      color: var(--accent-color);
      margin-right: 10px;
      font-size: 15px;
      text-decoration: none;
      transition: all 0.25s ease;
    }
    .footer .social-links a:hover {
      background-color: var(--accent-color);
      color: var(--contrast-color);
      transform: translateY(-3px);
    }
 
    .footer h4 {
      font-family: var(--heading-font);
      font-size: 16px;
      font-weight: 700;
      color: var(--heading-color);
      margin-bottom: 22px;
      position: relative;
      padding-bottom: 10px;
    }
    .footer h4::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 32px;
      height: 3px;
      background-color: var(--accent-color);
      border-radius: 2px;
    }
 
    .footer-links ul { list-style: none; padding: 0; margin: 0; }
    .footer-links ul li { margin-bottom: 12px; }
    .footer-links ul a {
      color: var(--default-color);
      text-decoration: none;
      font-size: 14px;
      transition: color 0.2s ease, padding-left 0.2s ease;
    }
    .footer-links ul a:hover {
      color: var(--accent-color);
      padding-left: 4px;
    }
 
    .footer-contact p {
      font-size: 14px;
      line-height: 1.9;
      margin-bottom: 8px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .footer-contact p i { color: var(--accent-color); margin-top: 3px; }
 
    .footer-newsletter p {
      font-size: 14px;
      line-height: 1.8;
      margin-bottom: 18px;
      color: var(--default-color);
    }
    .footer-newsletter form { position: relative; }
    .footer-newsletter input[type="email"] {
      width: 100%;
      padding: 12px 50px 12px 16px;
      border-radius: 8px;
      border: 1px solid color-mix(in srgb, var(--heading-color) 15%, transparent);
      background-color: var(--surface-color);
      font-size: 14px;
      color: var(--default-color);
      outline: none;
    }
    .footer-newsletter input[type="email"]:focus {
      border-color: var(--accent-color);
    }
    .footer-newsletter .btn-subscribe {
      position: absolute;
      right: 4px;
      top: 4px;
      bottom: 4px;
      width: 40px;
      border: none;
      border-radius: 6px;
      background-color: var(--accent-color);
      color: var(--contrast-color);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }
    .footer-newsletter .btn-subscribe:hover {
      background-color: color-mix(in srgb, var(--accent-color) 85%, black);
    }
    .footer-newsletter .loading,
    .footer-newsletter .error-message,
    .footer-newsletter .sent-message { font-size: 13px; margin-top: 10px; display: none; }
    .footer-newsletter .sent-message { color: var(--accent-color); }
    .footer-newsletter .error-message { color: #ef4444; }
 
    .footer-bottom {
      border-top: 1px solid color-mix(in srgb, var(--heading-color) 8%, transparent);
      padding: 22px 0;
      margin-top: 40px;
    }
    .footer-bottom .copyright {
      font-size: 14px;
      color: var(--default-color);
    }
    .footer-bottom .copyright .sitename { color: var(--heading-color); font-weight: 700; }
    .footer-bottom .credits {
      font-size: 12px;
      margin-top: 4px;
      color: var(--default-color);
      opacity: 0.75;
    }
    .footer-bottom .credits a { color: var(--accent-color); text-decoration: none; }
 
    .legal-links { display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-end; }
    .legal-links a {
      font-size: 13px;
      color: var(--default-color);
      text-decoration: none;
    }
    .legal-links a:hover { color: var(--accent-color); }
 
    @media (max-width: 768px) {
      .footer { padding-top: 40px; text-align: center; }
      .footer .logo { justify-content: center; }
      .footer-info p { margin-left: auto; margin-right: auto; }
      .footer .social-links { justify-content: center; }
      .footer h4::after { left: 50%; transform: translateX(-50%); }
      .footer-contact p { justify-content: center; }
      .legal-links { justify-content: center; }
      .footer-bottom .col-md-6 { text-align: center !important; }
    }
  </style>
 
  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-3 col-md-6 footer-info">
        <a href="index.html" class="logo">
          <span class="sitename">Job<span>Portal</span></span>
        </a>
        <p>A trusted platform connecting talented job seekers with the right employers — post jobs, find jobs, and grow your career or your team, all in one place.</p>
 
        <div class="social-links d-flex">
          <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
 <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] == "applicant") { ?>
      <div class="col-lg-2 col-md-6 footer-links">
        <h4>Applicant</h4>
        <ul>
          <li><a href="/job-portal/jobs.php">Browse Jobs</a></li>
          <!-- <li><a href="/job-portal/register.php">Create Account</a></li> -->
          <li><a href="/job-portal/applicant/applied-jobs.php">Applied Jobs</a></li>
          <li><a href="/job-portal/applicant/saved-jobs.php">Saved Jobs</a></li>
        </ul>
      </div>
 <?php } ?>
  <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] == "employer") { ?>
      <div class="col-lg-2 col-md-6 footer-links">
        <h4>Employer</h4>
        <ul>
          <li><a href="/job-portal/employer/add-manage-jobs.php">Post a Job</a></li>
          <li><a href="/job-portal/employer/employerDashboard.php">Employer Dashboard</a></li>
          <li><a href="/job-portal/employer/employer-applications.php">Manage Applications</a></li>
        </ul>
      </div>
 <?php } ?>
      <div class="col-lg-2 col-md-6 footer-links">
        <h4>Company</h4>
        <ul>
          <li><a href="/job-portal/index.php">Home</a></li>
          <li><a href="/job-portal/about.php">About Us</a></li>
          <li><a href="/job-portal/contact.php">Contact Us</a></li>
        </ul>
      </div>
 
      <div class="col-lg-3 col-md-6">
        <div class="footer-newsletter">
          <h4>Stay Updated</h4>
          <p>Subscribe to get the latest job openings and hiring tips straight to your inbox.</p>
        </div>
      </div>
    </div>
  </div>
 
  <div class="container footer-bottom">
    <div class="row gy-3">
      <div class="col-md-6 order-2 order-md-1">
        <div class="copyright">
          <p>© <span>Copyright</span> <strong class="sitename">JobPortal</strong>. All Rights Reserved.</p>
        </div>
        <div class="credits">
          Designed with care for job seekers and employers.
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Saare Scripts Footer ke BAHAR aur body close hone se PEHLE aane chahiye -->
<!-- Paths ko bhi lowercase 'job-portal' kar diya hai taaki koi breakdown na ho -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/job-portal/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/job-portal/assets/vendor/php-email-form/validate.js"></script>
<script src="/job-portal/assets/vendor/aos/aos.js"></script>
<script src="/job-portal/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/job-portal/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="/job-portal/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="/job-portal/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/job-portal/assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File (Isi file me mobile toggle ka actual code hota hai) -->
<script src="/job-portal/assets/js/main.js"></script>

</body>

</html>