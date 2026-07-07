<?php
session_start();
include "includes/config.php";  

$successMsg = "";

// ---------------- CONTACT FORM SUBMIT ----------------
if (isset($_POST['send_message'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // contact_messages table me naya message insert karo
    $insertQuery = "insert into contact_messages (name, email, subject, message) values ('$name', '$email', '$subject', '$message')";
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) { ?>  
        <script>
            alert("Your message has been sent successfully! We will get back to you soon.");
            window.location.href = "./contact.php";
        </script>
    <?php
    } else {
        $successMsg = "Something went wrong. Please try again.";
    }
}
?>

<style>
.contact-hero {
    background: linear-gradient(135deg, var(--accent-color) 0%, #0284c7 100%);
    padding: 70px 20px;
    text-align: center;
    color: var(--contrast-color);
}

.contact-hero h1 {
    font-family: var(--heading-font);
    font-size: 38px;
    font-weight: 800;
    margin-bottom: 12px;
    color: var(--contrast-color);
}

.contact-hero p {
    font-size: 16px;
    max-width: 600px;
    margin: 0 auto;
    opacity: 0.95;
}

/* ---------------- INFO CARDS ---------------- */
.contact-info-section {
    background-color: var(--background-color);
    padding: 50px 20px;
}

.contact-info-grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.info-card {
    background: var(--surface-color);
    border-radius: 14px;
    padding: 28px 22px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.info-card .icon-circle {
    width: 54px;
    height: 54px;
    background: rgba(14,165,233,0.12);
    color: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin: 0 auto 16px;
}

.info-card h3 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 16px;
    margin-bottom: 6px;
}

.info-card p {
    color: var(--default-color);
    font-size: 14px;
    line-height: 1.6;
}

/* ---------------- FORM SECTION ---------------- */
.contact-form-section {
    background-color: var(--background-color);
    padding: 20px 20px 70px;
}

.contact-form-wrap {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: 30px;
    background: var(--surface-color);
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    overflow: hidden;
}

.contact-side-panel {
    background: linear-gradient(160deg, var(--heading-color), #0f172a);
    color: var(--contrast-color);
    padding: 40px 32px;
}

.contact-side-panel h2 {
    font-family: var(--heading-font);
    font-size: 24px;
    margin-bottom: 14px;
    color: var(--contrast-color);
}

.contact-side-panel p {
    font-size: 14px;
    line-height: 1.7;
    opacity: 0.85;
    margin-bottom: 26px;
}

.office-hours {
    border-top: 1px solid rgba(255,255,255,0.15);
    padding-top: 20px;
}

.office-hours h4 {
    font-family: var(--heading-font);
    font-size: 14px;
    margin-bottom: 10px;
    color: var(--contrast-color);
}

.office-hours-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    opacity: 0.85;
    margin-bottom: 8px;
}

.contact-form-panel {
    padding: 40px 32px;
}

.contact-form-panel h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 22px;
    margin-bottom: 6px;
}

.contact-form-panel > p {
    color: var(--default-color);
    font-size: 14px;
    margin-bottom: 26px;
}

.success-banner {
    background: rgba(14,165,233,0.10);
    color: var(--accent-color);
    border: 1px solid rgba(14,165,233,0.25);
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 22px;
}

.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.field-group {
    margin-bottom: 18px;
}

.field-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--heading-color);
    margin-bottom: 7px;
}

.field-group input,
.field-group textarea {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1.5px solid rgba(0,0,0,0.10);
    font-size: 14px;
    color: var(--heading-color);
    background: var(--background-color);
    outline: none;
    transition: border-color 0.2s ease;
}

.field-group textarea {
    min-height: 130px;
    resize: vertical;
    line-height: 1.6;
}

.field-group input:focus,
.field-group textarea:focus {
    border-color: var(--accent-color);
    background: var(--surface-color);
}

.btn-send {
    width: 100%;
    background: var(--accent-color);
    color: var(--contrast-color);
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.2s ease;
}

.btn-send:hover {
    opacity: 0.9;
}

/* ---------------- FAQ SECTION ---------------- */
.faq-section {
    background-color: var(--background-color);
    padding: 0 20px 70px;
}

.faq-wrap {
    max-width: 800px;
    margin: 0 auto;
}

.faq-heading {
    text-align: center;
    margin-bottom: 30px;
}

.faq-heading h2 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 28px;
    margin-bottom: 8px;
}

.faq-heading p {
    color: var(--default-color);
    font-size: 14.5px;
}

.faq-item {
    background: var(--surface-color);
    border-radius: 12px;
    padding: 18px 22px;
    margin-bottom: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.faq-item h4 {
    font-family: var(--heading-font);
    color: var(--heading-color);
    font-size: 15.5px;
    margin-bottom: 8px;
}

.faq-item p {
    color: var(--default-color);
    font-size: 13.5px;
    line-height: 1.7;
}

@media (max-width: 992px) {
    .contact-info-grid { grid-template-columns: 1fr; }
    .contact-form-wrap { grid-template-columns: 1fr; }
    .contact-side-panel { order: 2; }
}

@media (max-width: 576px) {
    .contact-hero h1 { font-size: 28px; }
    .form-row-2 { grid-template-columns: 1fr; }
    .contact-form-panel, .contact-side-panel { padding: 28px 20px; }
}
</style>

<?php include "includes/header.php"; ?>

<section class="contact-hero">
    <h1>Get In Touch</h1>
    <p>Have a question, feedback, or need help with your account? Our team is here for you.</p>
</section>

<section class="contact-info-section">
    <div class="contact-info-grid">
        <div class="info-card">
            <div class="icon-circle"><i class="bi bi-envelope-fill"></i></div>
            <h3>Email Us</h3>
            <p>support@jobportal.com<br>We reply within 24 hours</p>
        </div>
        <div class="info-card">
            <div class="icon-circle"><i class="bi bi-telephone-fill"></i></div>
            <h3>Call Us</h3>
            <p>+92 300 1234567<br>Mon - Fri, 9am to 6pm</p>
        </div>
        <div class="info-card">
            <div class="icon-circle"><i class="bi bi-geo-alt-fill"></i></div>
            <h3>Visit Us</h3>
            <p>Karachi, Sindh<br>Pakistan</p>
        </div>
    </div>
</section>

<section class="contact-form-section">
    <div class="contact-form-wrap">

        <div class="contact-side-panel">
            <h2>Let's talk</h2>
            <p>Whether you're an employer looking to hire or a job seeker looking for your next opportunity, we're happy to help you get the most out of JobPortal.</p>

            <div class="office-hours">
                <h4>Office Hours</h4>
                <div class="office-hours-row"><span>Monday - Friday</span><span>9:00 AM - 6:00 PM</span></div>
                <div class="office-hours-row"><span>Saturday</span><span>10:00 AM - 2:00 PM</span></div>
                <div class="office-hours-row"><span>Sunday</span><span>Closed</span></div>
            </div>
        </div>

        <div class="contact-form-panel">
            <h2>Send us a message</h2>
            <p>Fill out the form below and we'll get back to you as soon as possible.</p>

            <?php if (!empty($successMsg)) { ?>
                <div class="success-banner"><?php echo $successMsg; ?></div>
            <?php } ?>

            <form method="POST">
                <div class="form-row-2">
                    <div class="field-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="e.g. Ali Khan" required>
                    </div>
                    <div class="field-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="field-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="What is this about?" required>
                </div>

                <div class="field-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                </div>

                <button type="submit" name="send_message" class="btn-send">Send Message</button>
            </form>
        </div>

    </div>
</section>

<section class="faq-section">
    <div class="faq-wrap">
        <div class="faq-heading">
            <h2>Frequently Asked Questions</h2>
            <p>Quick answers to things people often ask us</p>
        </div>

        <div class="faq-item">
            <h4>How do I create an account?</h4>
            <p>Click the "Register" button at the top of the page, choose whether you're an applicant or an employer, and fill in your details to get started.</p>
        </div>

        <div class="faq-item">
            <h4>Is it free to post a job?</h4>
            <p>Yes, employers can post jobs on JobPortal at no cost. Simply log in to your employer dashboard and click "Post a New Job".</p>
        </div>

        <div class="faq-item">
            <h4>How can I edit my profile?</h4>
            <p>Applicants can update their profile anytime from their dashboard by clicking the "Edit Profile" button.</p>
        </div>

        <div class="faq-item">
            <h4>How long does it take to get a response?</h4>
            <p>Our support team typically responds to messages within 24 hours on business days.</p>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>