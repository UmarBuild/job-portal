<?php
require_once '../includes/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../login.php");
    exit();
};

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

$upload_dir = "../uploads/company_logos/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $company_website = mysqli_real_escape_string($conn, $_POST['company_website']);
    $company_email = mysqli_real_escape_string($conn, $_POST['company_email']);
    $company_phone = mysqli_real_escape_string($conn, $_POST['company_phone']);
    $company_industry = mysqli_real_escape_string($conn, $_POST['company_industry']);
    $company_size = mysqli_real_escape_string($conn, $_POST['company_size']);
    $company_founded_year = mysqli_real_escape_string($conn, $_POST['company_founded_year']);
    $company_city = mysqli_real_escape_string($conn, $_POST['company_city']);
    $company_address = mysqli_real_escape_string($conn, $_POST['company_address']);
    $company_about = mysqli_real_escape_string($conn, $_POST['company_about']);
    $company_linkedin = mysqli_real_escape_string($conn, $_POST['company_linkedin']);

    if ($company_name == "") {
        $errors[] = "Company name is required";
    }

    if ($company_email != "" && !filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Company email is not valid";
    }

    if ($company_website != "" && !filter_var($company_website, FILTER_VALIDATE_URL)) {
        $errors[] = "Company website is not valid";
    }
    if ($company_linkedin != "" && !filter_var($company_linkedin, FILTER_VALIDATE_URL)) {
    $errors[] = "LinkedIn URL is not valid";
}

    $old_logo_query = mysqli_query($conn, "SELECT company_logo FROM users WHERE id = $user_id");
    $old_logo_row = mysqli_fetch_assoc($old_logo_query);
    $logo_name = $old_logo_row['company_logo'];

    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['name'] != "") {

        $file_name = $_FILES['company_logo']['name'];
        $file_tmp = $_FILES['company_logo']['tmp_name'];
        $file_size = $_FILES['company_logo']['size'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Logo must be jpg, jpeg, png, gif or webp";
        } elseif ($file_size > 2000000) {
            $errors[] = "Logo size must be less than 2MB";
        } else {
            $new_logo_name = "company_" . $user_id . "_" . time() . "." . $ext;

            if (!empty($logo_name) && file_exists($upload_dir . $logo_name)) {
                unlink($upload_dir . $logo_name);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $new_logo_name)) {
    $logo_name = $new_logo_name;
} else {
    $errors[] = "Logo upload failed.";
}
        }
    }

    if (count($errors) == 0) {

        $update_query = "UPDATE users SET
            company_logo = '$logo_name',
            company_name = '$company_name',
            company_website = '$company_website',
            company_email = '$company_email',
            company_phone = '$company_phone',
            company_industry = '$company_industry',
            company_size = '$company_size',
            company_founded_year = '$company_founded_year',
            company_city = '$company_city',
            company_address = '$company_address',
            company_about = '$company_about',
            company_linkedin = '$company_linkedin'
            WHERE id = $user_id";

        if (mysqli_query($conn, $update_query)) {
            $success = "Company profile updated successfully";
        } else {
            $errors[] = "Something went wrong, profile not saved";
        }
    }
}

$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$profile = mysqli_fetch_assoc($result);

require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Company Profile</h4>
                </div>

                <div class="card-body">

                    <?php if ($success != "") { ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php } ?>

                    <?php if (count($errors) > 0) { ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) { ?>
                                <div><?php echo $error; ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <form action="company-profile.php" method="POST" enctype="multipart/form-data">

                        <div class="mb-4 text-center">
                            <?php if (!empty($profile['company_logo'])) { ?>
                                <img src="../uploads/company_logos/<?php echo $profile['company_logo']; ?>" width="120" height="120" style="object-fit:cover; border-radius:5px;">
                            <?php } else { ?>
                                <div class="bg-light" style="width:120px; height:120px; margin:auto;"></div>
                            <?php } ?>

                            <div class="mt-2">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" name="company_logo">
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="company_name" value="<?php echo $profile['company_name']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Company Website</label>
                                <input type="text" class="form-control" name="company_website" value="<?php echo $profile['company_website']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Company Email</label>
                                <input type="text" class="form-control" name="company_email" value="<?php echo $profile['company_email']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Company Phone</label>
                                <input type="text" class="form-control" name="company_phone" value="<?php echo $profile['company_phone']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Industry</label>
                                <input type="text" class="form-control" name="company_industry" value="<?php echo $profile['company_industry']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Company Size</label>
                                <input type="text" class="form-control" name="company_size" value="<?php echo $profile['company_size']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Founded Year</label>
                                <input type="text" class="form-control" name="company_founded_year" value="<?php echo $profile['company_founded_year']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="company_city" value="<?php echo $profile['company_city']; ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="company_address" value="<?php echo $profile['company_address']; ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">About Company</label>
                                <textarea class="form-control" name="company_about" rows="5"><?php echo $profile['company_about']; ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">LinkedIn URL</label>
                                <input type="text" class="form-control" name="company_linkedin" value="<?php echo $profile['company_linkedin']; ?>">
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">Save Company Profile</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>