<?php 
session_start();
include("../includes/config.php");
require_once("../includes/csrf.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Verify CSRF Token on POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        die("Invalid CSRF Token.");
    }
}

$message = ""; 

// DELETE USER 
if (isset($_POST['delete_user_id'])) {
    $deleteId = (int)$_POST['delete_user_id'];
    if ($deleteId == $_SESSION['user_id']) {
        $message = "You cannot delete your own account";
    } else {
        $deletequery = "DELETE FROM users WHERE id=?";
        $deletequeryresult = mysqli_execute_query($conn, $deletequery, [$deleteId]);
        if ($deletequeryresult) {
            $message = "User deleted successfully";
        } else {
            $message = "Error while deleting user";
        }
    }
} 

$roleFilter = $_GET['role'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$whereClause = " WHERE 1=1";
$params = [];

if ($roleFilter != "all") {
    $whereClause .= " AND role = ?";
    $params[] = $roleFilter;
}

if ($search != "") {
    $whereClause .= " AND (fullname LIKE ? OR email LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// TOTAL COUNT QUERY
$countQuery = "SELECT COUNT(*) as total FROM users" . $whereClause;
$countResult = mysqli_execute_query($conn, $countQuery, $params);
$totalUsers = mysqli_fetch_assoc($countResult)['total'];

// PAGINATION
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$perPage = 10;
$offset = ($page - 1) * $perPage;
$totalPages = ceil($totalUsers / $perPage);

$usersQuery = "SELECT * FROM users" . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$queryParams = array_merge($params, [$perPage, $offset]);

$users = mysqli_execute_query($conn, $usersQuery, $queryParams);
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
    .pagination-wrap {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 24px;
        flex-wrap: wrap;
    }
    .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 6px;
        border-radius: 8px;
        background: var(--surface-color);
        border: 1px solid rgba(0,0,0,0.08);
        color: var(--default-color);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }
    .page-link:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
        text-decoration: none;
    }
    .page-link.active {
        background: var(--accent-color);
        color: var(--contrast-color);
        border-color: var(--accent-color);
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
    <p class="subtitle">Manage all applicants, employers and admins</p> 
    
    <?php if ($message != "") { ?> 
        <div class="alert-msg"><?php echo htmlspecialchars($message); ?></div> 
    <?php } ?> 
    
    <div class="controls-bar">
        <div class="role-tabs"> 
            <a href="?role=all" class="<?php echo ($roleFilter == "all") ? 'active' : ''; ?>">All</a> 
            <a href="?role=applicant" class="<?php echo ($roleFilter == "applicant") ? 'active' : ''; ?>">Applicants</a> 
            <a href="?role=employer" class="<?php echo ($roleFilter == "employer") ? 'active' : ''; ?>">Employers</a> 
            <a href="?role=admin" class="<?php echo ($roleFilter == "admin") ? 'active' : ''; ?>">Admins</a> 
        </div>
        <form class="search-box" method="GET"> 
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($roleFilter); ?>"> 
            <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>"> 
            <button type="submit"><i class="bi bi-search"></i> Search</button> 
        </form>
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
            <tbody> 
                <?php if (mysqli_num_rows($users) > 0) {
                    while ($user = mysqli_fetch_assoc($users)) { ?> 
                        <tr>
                            <td>
                                <div class="user-info"> 
                                    <?php if (!empty($user['profile_image'])) { ?> 
                                        <img src="../uploads/profile/<?php echo htmlspecialchars($user['profile_image']); ?>"> 
                                    <?php } else { ?> 
                                        <div class="avatar-placeholder"> 
                                            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?> 
                                        </div> 
                                    <?php } ?> 
                                    <div> 
                                        <strong><?php echo htmlspecialchars($user['fullname']); ?></strong> 
                                        <span><?php echo htmlspecialchars($user['email']); ?></span> 
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge <?php echo htmlspecialchars($user['role']); ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td><?php echo htmlspecialchars($user['experience'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['skills'] ?? '-'); ?></td>
                            <td><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-btns"> 
                                    <?php if (!empty($user['cv_file'])) { ?> 
                                        <a download class="btn-icon btn-view" href="../uploads/cv/<?php echo htmlspecialchars($user['cv_file']); ?>" target="_blank"> 
                                            <i class="bi bi-file-earmark-person"></i> CV 
                                        </a> 
                                    <?php } ?> 
                                    <form method="POST" onsubmit="return confirm('Do you want to delete this user?')" style="display:inline;"> 
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <input type="hidden" name="delete_user_id" value="<?php echo (int)$user['id']; ?>"> 
                                        <button type="submit" class="btn-icon btn-delete"><i class="bi bi-trash"></i> Delete</button> 
                                    </form>
                                </div>
                            </td>
                        </tr> 
                    <?php }
                } else { ?> 
                    <tr>
                        <td colspan="6" class="no-data">No users found</td>
                    </tr> 
                <?php } ?> 
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1) { ?>
        <div class="pagination-wrap">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>&role=<?php echo urlencode($roleFilter); ?>&search=<?php echo urlencode($search); ?>"
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</section>

<?php include '../includes/footer.php'; ?>