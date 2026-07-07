<?php session_start();
include("../includes/config.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
$message = ""; // DELETE USER 
if (isset($_POST['delete_user_id'])) {
    $deleteId = $_POST['delete_user_id'];
    if ($deleteId == $_SESSION['user_id']) {
        $message = "You cannot delete your own account";
    } else {
        $deletequery = "DELETE FROM users WHERE id='$deleteId'";
        $deletequeryresult = mysqli_query($conn, $deletequery);
        if ($deletequeryresult) {
            $message = "User deleted successfully";
        } else {
            $message = "Error while deleting user";
        }
    }
} // FILTER + SEARCH 
$roleFilter = "all";
if (isset($_GET['role'])) {
    $roleFilter = $_GET['role'];
}
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
$usersquery = "SELECT * FROM users WHERE 1";
if ($roleFilter != "all") {
    $usersquery .= " AND role='$roleFilter'";
}
if ($search != "") {
    $usersquery .= " AND (fullname LIKE '%$search%' OR email LIKE '%$search%')";
}
$usersquery .= " ORDER BY created_at DESC";
$users = mysqli_query($conn, $usersquery);
?>
<style>
    .admin-users {
        background-color: var(--background-color);
        padding: 40px 20px;
        font-family: var(--default-font);
        color: var(--default-color);
    }

    .admin-users h1 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 28px;
        margin-bottom: 6px;
    }

    .admin-users .subtitle {
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

    .controls-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
    }

    .role-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .role-tabs a {
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

    .role-tabs a.active,
    .role-tabs a:hover {
        background: var(--accent-color);
        color: var(--contrast-color);
        border-color: var(--accent-color);
    }

    .search-box {
        display: flex;
        align-items: center;
        background: var(--surface-color);
        border-radius: 30px;
        padding: 6px 6px 6px 18px;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .search-box input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 14px;
        color: var(--default-color);
        width: 220px;
    }

    .search-box button {
        background: var(--accent-color);
        color: var(--contrast-color);
        border: none;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        cursor: pointer;
    }

    .users-table-wrap {
        background: var(--surface-color);
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .users-table th {
        text-align: left;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--default-color);
        padding: 16px;
        border-bottom: 2px solid rgba(0, 0, 0, 0.06);
        white-space: nowrap;
    }

    .users-table td {
        padding: 14px 16px;
        font-size: 14px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    .users-table tr:hover td {
        background-color: var(--background-color);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-info img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--accent-color);
    }

    .user-info .avatar-placeholder {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--accent-color);
        color: var(--contrast-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .user-info strong {
        display: block;
        color: var(--heading-color);
        font-size: 14.5px;
    }

    .user-info span {
        font-size: 12.5px;
        color: var(--default-color);
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge.applicant {
        background: rgba(14, 165, 233, 0.12);
        color: var(--accent-color);
    }

    .badge.employer {
        background: rgba(30, 41, 59, 0.10);
        color: var(--heading-color);
    }

    .badge.admin {
        background: rgba(220, 38, 38, 0.10);
        color: #dc2626;
    }

    .action-btns {
        display: flex;
        gap: 8px;
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
        .controls-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box input {
            width: 100%;
        }
    }
</style>
<?php include '../includes/header.php'; ?>

<section class="admin-users">
    <h1>Manage Users</h1>
    <p class="subtitle"> Manage all applicants, employers and admins </p> <?php if ($message != "") { ?> <div class="alert-msg"> <?php echo $message; ?> </div> <?php } ?> <div class="controls-bar">
        <div class="role-tabs"> <a href="?role=all" class="<?php if ($roleFilter == "all") {
                                                                echo "active";
                                                            } ?>"> All </a> <a href="?role=applicant" class="<?php if ($roleFilter == "applicant") {
                                                                                                                                                                echo "active";
                                                                                                                                                            } ?>"> Applicants </a> <a href="?role=employer" class="<?php if ($roleFilter == "employer") {
                                                                                                                                                                                                                                                                        echo "active";
                                                                                                                                                                                                                                                                    } ?>"> Employers </a> <a href="?role=admin" class="<?php if ($roleFilter == "admin") {
                                                                                                                                                                                                                                                                                                                                                                            echo "active";
                                                                                                                                                                                                                                                                                                                                                                        } ?>"> Admins </a> </div>
        <form class="search-box" method="GET"> <input type="hidden" name="role" value="<?php echo $roleFilter; ?>"> <input type="text" name="search" placeholder="Search by name or email" value="<?php echo $search; ?>"> <button type="submit"> <i class="bi bi-search"></i> Search </button> </form>
    </div>
    <div class="users-table-wrap">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Experience</th>
                    <th>Skills</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody> <?php if (mysqli_num_rows($users) > 0) {
                        while ($user = mysqli_fetch_assoc($users)) { ?> <tr>
                            <td>
                                <div class="user-info"> <?php if ($user['profile_image'] != "") { ?> <img src="../uploads/profile/<?php echo $user['profile_image']; ?>"> <?php } else { ?> <div class="avatar-placeholder"> <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?> </div> <?php } ?> <div> <strong> <?php echo $user['fullname']; ?> </strong> <span> <?php echo $user['email']; ?> </span> </div>
                                </div>
                            </td>
                            <td> <span class="badge <?php echo $user['role']; ?>"> <?php echo $user['role']; ?> </span> </td>
                            <td> <?php echo $user['experience']; ?> </td>
                            <td> <?php echo $user['skills']; ?> </td>
                            <td> <?php echo date("d M Y", strtotime($user['created_at'])); ?> </td>
                            <td>
                                <div class="action-btns"> <?php if ($user['cv_file'] != "") { ?> <a class="btn-icon btn-view" href="../uploads/cv/<?php echo $user['cv_file']; ?>" target="_blank"> <i class="bi bi-file-earmark-person"></i> CV </a> <?php } ?> <form method="POST" onsubmit="return confirm('Do you want to delete this user?')" style="display:inline;"> <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>"> <button type="submit" class="btn-icon btn-delete"> <i class="bi bi-trash"></i> Delete </button> </form>
                                </div>
                            </td>
                        </tr> <?php }
                        } else { ?> <tr>
                        <td colspan="6" class="no-data"> No users found </td>
                    </tr> <?php } ?> </tbody>
        </table>
    </div>
</section>

<?php include '../includes/footer.php'; ?>