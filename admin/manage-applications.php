<?php session_start();
include("../includes/config.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
$message = ""; // Update Application Status Query
if (isset($_POST['update_status'])) {
    $appId = $_POST['application_id'];
    $newStatus = $_POST['status'];
    if ($newStatus == "pending" || $newStatus == "accepted" || $newStatus == "rejected") {
        $updatestatusquery = "UPDATE applications SET status='$newStatus' WHERE id='$appId'";
        $updatestatusqueryresult = mysqli_query($conn, $updatestatusquery);
        if ($updatestatusqueryresult) {
            $message = "Application status updated successfully";
        }
    }
} // Delete Application Query
if (isset($_POST['delete_application_id'])) {
    $deleteId = $_POST['delete_application_id'];
    $deletequery = "DELETE FROM applications WHERE id='$deleteId'";
    $deletequeryresult = mysqli_query($conn, $deletequery);
    if ($deletequeryresult) {
        $message = "Application deleted successfully";
    }
} // Applications Query 
$statusFilter = "all";
if (isset($_GET['status'])) {
    $statusFilter = $_GET['status'];
}
$applicationsquery = "SELECT applications.*, jobs.title AS job_title, jobs.company AS job_company, users.fullname AS applicant_name, users.email AS applicant_email, users.cv_file FROM applications LEFT JOIN jobs ON applications.job_id = jobs.id LEFT JOIN users ON applications.applicant_id = users.id";
if ($statusFilter != "all") {
    $applicationsquery .= " WHERE applications.status='$statusFilter'";
}
$applicationsquery .= " ORDER BY applications.applied_at DESC";
$applications = mysqli_query($conn, $applicationsquery);
?>

<style>
    .admin-applications {
        background-color: var(--background-color);
        padding: 40px 20px;
        font-family: var(--default-font);
        color: var(--default-color);
    }

    .admin-applications h1 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 28px;
        margin-bottom: 6px;
    }

    .admin-applications .subtitle {
        margin-bottom: 24px;
        font-size: 15px;
    }

    .alert-msg {
        background: rgba(14, 165, 233, 0.10);
        color: var(--accent-color);
        border: 1px solid rgba(14, 165, 233, 0.25);
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .status-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .status-tabs a {
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        background: var(--surface-color);
        color: var(--default-color);
        border: 1px solid rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .status-tabs a.active,
    .status-tabs a:hover {
        background: var(--accent-color);
        color: var(--contrast-color);
        border-color: var(--accent-color);
    }

    .applications-table-wrap {
        background: var(--surface-color);
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow-x: auto;
    }

    .applications-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .applications-table th {
        text-align: left;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--default-color);
        padding: 16px;
        border-bottom: 2px solid rgba(0, 0, 0, 0.06);
        white-space: nowrap;
    }

    .applications-table td {
        padding: 14px 16px;
        font-size: 14px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    .applications-table tr:hover td {
        background-color: var(--background-color);
    }

    .applications-table strong {
        color: var(--heading-color);
        display: block;
    }

    .applications-table span.sub {
        font-size: 12.5px;
        color: var(--default-color);
    }

    .status-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.10);
        font-size: 13px;
        outline: none;
        background: var(--background-color);
        color: var(--default-color);
        cursor: pointer;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-badge.pending {
        background: rgba(234, 179, 8, 0.14);
        color: #b45309;
    }

    .status-badge.accepted {
        background: rgba(34, 197, 94, 0.14);
        color: #15803d;
    }

    .status-badge.rejected {
        background: rgba(220, 38, 38, 0.12);
        color: #dc2626;
    }

    .action-btns {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-icon {
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-view {
        background: rgba(14, 165, 233, 0.10);
        color: var(--accent-color);
    }

    .btn-delete {
        background: rgba(220, 38, 38, 0.10);
        color: #dc2626;
    }

    .btn-view:hover {
        background: var(--accent-color);
        color: var(--contrast-color);
    }

    .btn-delete:hover {
        background: #dc2626;
        color: #fff;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: var(--default-color);
    }

    @media (max-width: 576px) {
        .status-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 6px;
        }
    }
</style>
<?php include "../includes/header.php" ?>
<section class="admin-applications">
    <h1>Manage Applications</h1>
    <p class="subtitle"> Track and update all job applications </p> <?php if ($message != "") { ?> <div class="alert-msg"> <?php echo $message; ?> </div> <?php } ?> <div class="status-tabs"> <a href="?status=all" class="<?php if ($statusFilter == 'all') {
                                                                                                                                                                                                                                echo 'active';
                                                                                                                                                                                                                            } ?>"> All </a> <a href="?status=pending" class="<?php if ($statusFilter == 'pending') {
                                                                                                                                                                                                                                                                                    echo 'active';
                                                                                                                                                                                                                                                                                } ?>"> Pending </a> <a href="?status=accepted" class="<?php if ($statusFilter == 'accepted') {
                                                                                                                                                                                                                                                                                                                                                                                        echo 'active';
                                                                                                                                                                                                                                                                                                                                                                                    } ?>"> Accepted </a> <a href="?status=rejected" class="<?php if ($statusFilter == 'rejected') {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    echo 'active';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                } ?>"> Rejected </a> </div>
    <div class="applications-table-wrap">
        <table class="applications-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Job Applied For</th>
                    <th>Applied On</th>
                    <th>Status</th>
                    <th>Update Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody> <?php if (mysqli_num_rows($applications) > 0) {
                        while ($app = mysqli_fetch_assoc($applications)) { ?> <tr>
                            <td> <strong> <?php echo $app['applicant_name']; ?> </strong> <span class="sub"> <?php echo $app['applicant_email']; ?> </span> </td>
                            <td> <strong> <?php echo $app['job_title']; ?> </strong> <span class="sub"> <?php echo $app['job_company']; ?> </span> </td>
                            <td> <?php echo date("d M Y", strtotime($app['applied_at'])); ?> </td>
                            <td> <span class="status-badge <?php echo $app['status']; ?>"> <?php echo $app['status']; ?> </span> </td>
                            <td>
                                <form method="POST" style="display:flex; gap:8px;"> <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>"> <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="pending" <?php if ($app['status'] == 'pending') {
                                                                    echo 'selected';
                                                                } ?>> Pending </option>
                                        <option value="accepted" <?php if ($app['status'] == 'accepted') {
                                                                        echo 'selected';
                                                                    } ?>> Accepted </option>
                                        <option value="rejected" <?php if ($app['status'] == 'rejected') {
                                                                        echo 'selected';
                                                                    } ?>> Rejected </option>
                                    </select> <input type="hidden" name="update_status" value="1"> </form>
                            </td>
                            <td>
                                <div class="action-btns"> <?php if ($app['cv_file'] != "") { ?> <a class="btn-icon btn-view" href="../uploads/cv/<?php echo $app['cv_file']; ?>" target="_blank"> <i class="bi bi-file-earmark-person"></i> CV </a> <?php } ?> <form method="POST" onsubmit="return confirm('Do you want to delete this application?')"> <input type="hidden" name="delete_application_id" value="<?php echo $app['id']; ?>"> <button type="submit" class="btn-icon btn-delete"> <i class="bi bi-trash"></i> Delete </button> </form>
                                </div>
                            </td>
                        </tr> <?php }
                        } else { ?> <tr>
                        <td colspan="6" class="no-data"> No applications found </td>
                    </tr> <?php } ?> </tbody>
        </table>
    </div>
</section>
<?php include "../includes/footer.php" ?>