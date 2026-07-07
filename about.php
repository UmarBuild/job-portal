<?php
session_start();
include("includes/config.php");

$query1 = "SELECT COUNT(*) AS total FROM jobs WHERE status = 'open'";
$result1 = mysqli_query($conn, $query1);
$row1 = mysqli_fetch_assoc($result1);
$totalJobs = $row1['total'];

$query2 = "SELECT COUNT(*) AS total FROM categories";
$result2 = mysqli_query($conn, $query2);
$row2 = mysqli_fetch_assoc($result2);
$totalCategories = $row2['total'];

$query3 = "SELECT COUNT(*) AS total FROM users WHERE role = 'applicant'";
$result3 = mysqli_query($conn, $query3);
$row3 = mysqli_fetch_assoc($result3);
$totalProfessionals = $row3['total'];

$query4 = "SELECT COUNT(*) AS total FROM applications WHERE status = 'accepted'";
$result4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($result4);
$totalHired = $row4['total'];

$categories = [];
$query5 = "SELECT id, name FROM categories ORDER BY name ASC";
$result5 = mysqli_query($conn, $query5);
 
while ($row5 = mysqli_fetch_assoc($result5)) {
    $row5['icon'] = 'bi-briefcase';
 
    $categories[] = $row5;
}
?>

<style>
  .about-page {
    background-color: var(--background-color);
    color: var(--default-color);
    font-family: var(--default-font);
    overflow-x: hidden;
  }

  .about-page h1,
  .about-page h2,
  .about-page h3,
  .about-page h4 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    margin: 0;
  }

  .about-page .eyebrow {
    display: inline-block;
    font-family: var(--nav-font);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--accent-color);
    margin-bottom: 14px;
  }

  .about-page .container-narrow {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 24px;
  }

  /* ---------- Hero ---------- */
  .about-hero {
    position: relative;
    padding: 100px 0 90px;
    background-color: var(--background-color);
    overflow: hidden;
  }

  .about-hero .hero-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 50px;
    align-items: center;
  }

  .about-hero h1 {
    font-size: 46px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 20px;
  }

  .about-hero h1 span {
    color: var(--accent-color);
  }

  .about-hero p.lead {
    font-size: 17px;
    line-height: 1.8;
    max-width: 520px;
    margin-bottom: 30px;
  }

  .about-hero .hero-actions {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
  }

  .btn-accent,
  .btn-outline-accent {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 28px;
    border-radius: 50px;
    font-family: var(--nav-font);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid var(--accent-color);
  }

  .btn-accent {
    background-color: var(--accent-color);
    color: var(--contrast-color);
  }

  .btn-accent:hover {
    background-color: transparent;
    color: var(--accent-color);
  }

  .btn-outline-accent {
    background-color: transparent;
    color: var(--heading-color);
    border-color: var(--default-color);
  }

  .btn-outline-accent:hover {
    border-color: var(--accent-color);
    color: var(--accent-color);
  }

  /* Signature element: connection network graphic representing
   talent <-> opportunity matching, built from the accent color only */
  .hero-network {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    max-width: 420px;
    margin: 0 auto;
  }

  .hero-network svg {
    width: 100%;
    height: 100%;
  }

  .hero-network .node {
    fill: var(--surface-color);
    stroke: var(--accent-color);
    stroke-width: 2;
  }

  .hero-network .node-core {
    fill: var(--accent-color);
  }

  .hero-network .link {
    stroke: var(--accent-color);
    stroke-width: 1.5;
    opacity: 0.35;
  }

  .hero-network .pulse {
    fill: var(--accent-color);
    opacity: 0.9;
    animation: travel 4s linear infinite;
  }

  .hero-network .pulse.delay1 {
    animation-delay: 1s;
  }

  .hero-network .pulse.delay2 {
    animation-delay: 2.2s;
  }

  .hero-network .pulse.delay3 {
    animation-delay: 3.1s;
  }

  @keyframes travel {
    0% {
      offset-distance: 0%;
      opacity: 0;
    }

    10% {
      opacity: 1;
    }

    90% {
      opacity: 1;
    }

    100% {
      offset-distance: 100%;
      opacity: 0;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .hero-network .pulse {
      animation: none;
      opacity: 0.7;
    }
  }

  /* ---------- Stats bar ---------- */
  .about-stats {
    background-color: var(--accent-color);
    padding: 42px 0;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    text-align: center;
  }

  .stats-grid .stat-number {
    font-family: var(--heading-font);
    font-size: 34px;
    font-weight: 700;
    color: var(--contrast-color);
    line-height: 1;
    margin-bottom: 8px;
  }

  .stats-grid .stat-label {
    font-family: var(--nav-font);
    font-size: 13px;
    letter-spacing: 0.5px;
    color: var(--contrast-color);
    opacity: 0.9;
  }

  /* ---------- Mission / story ---------- */
  .about-mission {
    padding: 90px 0;
  }

  .mission-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
  }

  .mission-grid h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 22px;
    line-height: 1.3;
  }

  .mission-grid p {
    line-height: 1.85;
    margin-bottom: 16px;
  }

  .mission-card {
    background-color: var(--surface-color);
    border-radius: 16px;
    padding: 36px;
    box-shadow: 0 10px 30px rgba(30, 41, 59, 0.06);
  }

  .mission-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .mission-card li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--background-color);
  }

  .mission-card li:last-child {
    border-bottom: none;
  }

  .mission-card li i {
    color: var(--accent-color);
    font-size: 20px;
    margin-top: 2px;
  }

  .mission-card li strong {
    display: block;
    color: var(--heading-color);
    font-family: var(--heading-font);
    font-size: 15px;
    margin-bottom: 3px;
  }

  .mission-card li span {
    font-size: 14px;
    color: var(--default-color);
  }

  /* ---------- How it works ---------- */
  .about-steps {
    padding: 90px 0;
    background-color: var(--surface-color);
  }

  .section-head {
    text-align: center;
    max-width: 620px;
    margin: 0 auto 60px;
  }

  .section-head h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 14px;
  }

  .section-head p {
    line-height: 1.75;
  }

  .steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    position: relative;
  }

  .step-card {
    position: relative;
    background-color: var(--background-color);
    border-radius: 16px;
    padding: 40px 28px;
    text-align: left;
  }

  .step-card .step-number {
    font-family: var(--heading-font);
    font-size: 46px;
    font-weight: 700;
    color: var(--accent-color);
    opacity: 0.25;
    line-height: 1;
    margin-bottom: 18px;
  }

  .step-card h3 {
    font-size: 19px;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .step-card p {
    font-size: 14.5px;
    line-height: 1.7;
    margin: 0;
  }

  /* ---------- Categories ---------- */
  .about-categories {
    padding: 90px 0;
  }

  .categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
  }

  .category-chip {
    background-color: var(--surface-color);
    border-radius: 14px;
    padding: 28px 20px;
    text-align: center;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    box-shadow: 0 6px 18px rgba(30, 41, 59, 0.05);
  }

  .category-chip:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(14, 165, 233, 0.15);
  }

  .category-chip .cat-icon {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background-color: var(--background-color);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 22px;
    color: var(--accent-color);
  }

  .category-chip h4 {
    font-size: 15px;
    font-weight: 600;
  }

  /* ---------- Values ---------- */
  .about-values {
    padding: 90px 0;
    background-color: var(--background-color);
  }

  .values-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
  }

  .value-card {
    padding: 34px 26px;
    border-radius: 16px;
    background-color: var(--surface-color);
  }

  .value-card .value-icon {
    font-size: 26px;
    color: var(--accent-color);
    margin-bottom: 18px;
  }

  .value-card h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .value-card p {
    font-size: 14px;
    line-height: 1.7;
    margin: 0;
  }

  /* ---------- CTA ---------- */
  .about-cta {
    padding: 80px 0;
  }

  .cta-box {
    background-color: var(--heading-color);
    border-radius: 24px;
    padding: 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .cta-box h2 {
    color: var(--contrast-color);
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 14px;
  }

  .cta-box p {
    color: var(--contrast-color);
    opacity: 0.85;
    max-width: 460px;
    margin: 0 auto 28px;
    line-height: 1.7;
  }

  .cta-box .hero-actions {
    justify-content: center;
  }

  /* ---------- Responsive ---------- */
  @media (max-width: 992px) {

    .about-hero .hero-grid,
    .mission-grid {
      grid-template-columns: 1fr;
    }

    .hero-network {
      max-width: 300px;
      margin-top: 20px;
    }

    .stats-grid,
    .steps-grid,
    .categories-grid,
    .values-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .cta-box {
      padding: 44px 26px;
    }
  }

  @media (max-width: 576px) {
    .about-hero h1 {
      font-size: 32px;
    }

    .stats-grid,
    .steps-grid,
    .categories-grid,
    .values-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Focus visibility for accessibility */
  .about-page a:focus-visible,
  .about-page button:focus-visible {
    outline: 3px solid var(--accent-color);
    outline-offset: 3px;
  }
</style>

<?php include("includes/header.php"); ?>

<main class="about-page">

  <section class="about-hero">
    <div class="container-narrow hero-grid">
      <div>
        <span class="eyebrow">About Us</span>
        <h1>Connecting real <span>talent</span> with real <span>opportunities</span></h1>
        <p class="lead">
          We built this platform to make hiring and freelancing simple —
          a place where employers post real jobs, and skilled professionals
          find work that actually fits their skills, without the noise.
        </p>
        <div class="hero-actions">
          <a href="jobs.php" class="btn-accent"><i class="bi bi-search"></i> Browse Jobs</a>
          <a href="post-job.php" class="btn-outline-accent"><i class="bi bi-plus-circle"></i> Post a Job</a>
        </div>
      </div>

      <div class="hero-network" aria-hidden="true">
        <svg viewBox="0 0 400 400">
          <defs>
            <path id="path1" d="M200,200 L90,110"></path>
            <path id="path2" d="M200,200 L320,90"></path>
            <path id="path3" d="M200,200 L330,290"></path>
            <path id="path4" d="M200,200 L80,300"></path>
          </defs>

          <line class="link" x1="200" y1="200" x2="90" y2="110"></line>
          <line class="link" x1="200" y1="200" x2="320" y2="90"></line>
          <line class="link" x1="200" y1="200" x2="330" y2="290"></line>
          <line class="link" x1="200" y1="200" x2="80" y2="300"></line>

          <circle class="node" cx="90" cy="110" r="26"></circle>
          <circle class="node" cx="320" cy="90" r="20"></circle>
          <circle class="node" cx="330" cy="290" r="24"></circle>
          <circle class="node" cx="80" cy="300" r="18"></circle>
          <circle class="node node-core" cx="200" cy="200" r="34"></circle>

          <circle class="pulse" r="4">
            <animateMotion dur="4s" repeatCount="indefinite">
              <mpath href="#path1"></mpath>
            </animateMotion>
          </circle>
          <circle class="pulse delay1" r="4">
            <animateMotion dur="4s" repeatCount="indefinite">
              <mpath href="#path2"></mpath>
            </animateMotion>
          </circle>
          <circle class="pulse delay2" r="4">
            <animateMotion dur="4s" repeatCount="indefinite">
              <mpath href="#path3"></mpath>
            </animateMotion>
          </circle>
          <circle class="pulse delay3" r="4">
            <animateMotion dur="4s" repeatCount="indefinite">
              <mpath href="#path4"></mpath>
            </animateMotion>
          </circle>
        </svg>
      </div>
    </div>
  </section>

  <section class="about-stats">
    <div class="container-narrow">
      <div class="stats-grid">
        <div>
          <div class="stat-number"><?php echo $totalJobs; ?>+</div>
          <div class="stat-label">OPEN JOBS</div>
        </div>
        <div>
          <div class="stat-number"><?php echo $totalCategories; ?></div>
          <div class="stat-label">CATEGORIES</div>
        </div>
        <div>
          <div class="stat-number"><?php echo $totalProfessionals; ?>+</div>
          <div class="stat-label">PROFESSIONALS</div>
        </div>
        <div>
          <div class="stat-number"><?php echo $totalHired; ?>+</div>
          <div class="stat-label">SUCCESSFUL HIRES</div>
        </div>
      </div>
    </div>
  </section>

  <section class="about-mission">
    <div class="container-narrow mission-grid">
      <div>
        <span class="eyebrow">Our Mission</span>
        <h2>Making hiring simple, honest, and local</h2>
        <p>
          Finding the right job — or the right person for the job — shouldn't
          take weeks of scrolling through irrelevant listings. We started this
          platform to give employers a straightforward way to post work, and
          give job seekers and freelancers a clear path to apply and get hired.
        </p>
        <p>
          Every listing on our platform is organized by category, every profile
          shows real skills and experience, and every application is tracked —
          so both sides always know where they stand.
        </p>
      </div>

      <div class="mission-card">
        <ul>
          <li>
            <i class="bi bi-shield-check"></i>
            <div>
              <strong>Verified Listings</strong>
              <span>Every job goes through a simple review before it goes live.</span>
            </div>
          </li>
          <li>
            <i class="bi bi-lightning-charge"></i>
            <div>
              <strong>Fast Applications</strong>
              <span>Apply in a few clicks and track your application status anytime.</span>
            </div>
          </li>
          <li>
            <i class="bi bi-people"></i>
            <div>
              <strong>Built for Both Sides</strong>
              <span>Tools for employers to manage postings, and for applicants to manage their careers.</span>
            </div>
          </li>
          <li>
            <i class="bi bi-geo-alt"></i>
            <div>
              <strong>Local Focus</strong>
              <span>Designed around the way people actually hire and get hired here.</span>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </section>

  <section class="about-steps">
    <div class="container-narrow">
      <div class="section-head">
        <span class="eyebrow">How It Works</span>
        <h2>Three steps to your next opportunity</h2>
        <p>Whether you're hiring or looking to get hired, the process stays simple from start to finish.</p>
      </div>

      <div class="steps-grid">
        <div class="step-card">
          <div class="step-number">01</div>
          <h3>Create your profile</h3>
          <p>Sign up, add your skills and experience, and upload your CV — or list your company and start posting jobs.</p>
        </div>
        <div class="step-card">
          <div class="step-number">02</div>
          <h3>Browse & apply</h3>
          <p>Search jobs by category and location, save the ones you like, and apply directly through your dashboard.</p>
        </div>
        <div class="step-card">
          <div class="step-number">03</div>
          <h3>Get hired</h3>
          <p>Track your application status in real time and connect with employers once you're accepted.</p>
        </div>
      </div>
    </div>
  </section>

 <?php if (!empty($categories)) { ?>
    <section class="about-categories">
        <div class="container-narrow">
            <div class="section-head">
                <span class="eyebrow">What We Cover</span>
                <h2>Job categories on our platform</h2>
                <p>From development to design to marketing — find work across the fields that matter most.</p>
            </div>
 
            <div class="categories-grid">
                <?php foreach ($categories as $cat) { ?>
                <a href="jobs.php?filter=<?php echo (int)$cat['id']; ?>&submit-filter=#jobs-section" class="category-chip" style="text-decoration:none;">
                    <div class="cat-icon">
                        <i class="bi <?php echo $cat['icon']; ?>"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($cat['name']); ?></h4>
                </a>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php } ?>

  <section class="about-values">
    <div class="container-narrow">
      <div class="section-head">
        <span class="eyebrow">What We Stand For</span>
        <h2>The values behind the platform</h2>
      </div>

      <div class="values-grid">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-award"></i></div>
          <h4>Trust</h4>
          <p>Real listings, real applicants — no clutter, no fake postings.</p>
        </div>
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-eye"></i></div>
          <h4>Transparency</h4>
          <p>Clear application status and honest job details, always.</p>
        </div>
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <h4>Growth</h4>
          <p>A platform that grows with the people who use it, every single day.</p>
        </div>
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-hand-thumbs-up"></i></div>
          <h4>Simplicity</h4>
          <p>No unnecessary steps — post a job or apply for one in minutes.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="about-cta">
    <div class="container-narrow">
      <div class="cta-box">
        <h2>Ready to get started?</h2>
        <p>Join as an applicant to find your next role, or as an employer to find your next hire.</p>
        <div class="hero-actions">
          <?php if(!isset($_SESSION['user_id'])){  ?>
          <a href="register.php" class="btn-accent"><i class="bi bi-person-plus"></i> Create an Account</a>
          <?php } ?>
          <a href="jobs.php" class="btn-outline-accent" style="border-color: var(--contrast-color); color: var(--contrast-color);">
            <i class="bi bi-briefcase"></i> View Open Jobs
          </a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include "includes/footer.php";  ?>

</div>