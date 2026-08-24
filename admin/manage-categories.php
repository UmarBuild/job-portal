<?php session_start();
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
$messageType = "success"; 

// Add Category Query (Prepared Statement)
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if ($name == "") {
        $message = "Category name cannot be empty";
        $messageType = "error";
    } else {
        $checkquery = "SELECT * FROM categories WHERE name = ?";
        $checkqueryresult = mysqli_execute_query($conn, $checkquery, [$name]);
        if (mysqli_num_rows($checkqueryresult) > 0) {
            $message = "Category already exists";
            $messageType = "error";
        } else {
            $insertquery = "INSERT INTO categories(name) VALUES(?)";
            $insertqueryresult = mysqli_execute_query($conn, $insertquery, [$name]);
            if ($insertqueryresult) {
                $message = "Category added successfully";
                header("Location: ./manage-categories.php");
                exit();
            }
        }
    }
} 

// Edit Category Query (Prepared Statement)
if (isset($_POST['edit_category'])) {
    $id = (int)$_POST['category_id'];
    $name = trim($_POST['category_name_edit']);
    if ($name == "") {
        $message = "Category name cannot be empty";
        $messageType = "error";
    } else {
        $updatequery = "UPDATE categories SET name = ? WHERE id = ?";
        $updatequeryresult = mysqli_execute_query($conn, $updatequery, [$name, $id]);
        if ($updatequeryresult) {
            $message = "Category updated successfully";
            header("Location: ./manage-categories.php");
            exit();
        }
    }
} 

// Delete Category Query (Prepared Statement)
if (isset($_POST['delete_category_id'])) {
    $id = (int)$_POST['delete_category_id'];
    $checkjobsquery = "SELECT COUNT(*) AS total FROM jobs WHERE category_id = ?";
    $checkjobsqueryresult = mysqli_execute_query($conn, $checkjobsquery, [$id]);
    $jobs = mysqli_fetch_assoc($checkjobsqueryresult);
    $jobCount = $jobs['total'];
    if ($jobCount > 0) {
        $message = "This category cannot be deleted because $jobCount jobs are linked with it";
        $messageType = "error";
    } else {
        $deletequery = "DELETE FROM categories WHERE id = ?";
        $deletequeryresult = mysqli_execute_query($conn, $deletequery, [$id]);
        if ($deletequeryresult) {
            $message = "Category deleted successfully";
            header("Location: ./manage-categories.php");
            exit();
        }
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { 
    $page = 1; 
}
$perPage = 10;
$offset = ($page - 1) * $perPage;

$countResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories");
$totalCategories = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalCategories / $perPage);

// Query 2: Fetch Categories with Pagination
$categoriesquery = "SELECT categories.*, (SELECT COUNT(*) FROM jobs WHERE jobs.category_id = categories.id) AS job_count FROM categories ORDER BY categories.name ASC LIMIT ? OFFSET ?";
$categories = mysqli_execute_query($conn, $categoriesquery, [$perPage, $offset]);
?>

<style>
    .admin-categories {
        background-color: var(--background-color);
        padding: 40px 20px;
        font-family: var(--default-font);
        color: var(--default-color);
    }

    .admin-categories h1 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 28px;
        margin-bottom: 6px;
    }

    .admin-categories .subtitle {
        margin-bottom: 24px;
        font-size: 15px;
    }

    .alert-msg {
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        border: 1px solid transparent;
    }

    .alert-msg.success {
        background: rgba(14, 165, 233, 0.10);
        color: var(--accent-color);
        border-color: rgba(14, 165, 233, 0.25);
    }

    .alert-msg.error {
        background: rgba(220, 38, 38, 0.10);
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.25);
    }

    .categories-layout {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 24px;
        align-items: start;
    }

    .add-category-panel {
        background: var(--surface-color);
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    }

    .add-category-panel h2 {
        font-family: var(--heading-font);
        color: var(--heading-color);
        font-size: 18px;
        margin-bottom: 16px;
    }

    .add-category-panel label {
        display: block;
        font-size: 13px;
        color: var(--default-color);
        margin-bottom: 6px;
    }

    .add-category-panel input[type="text"] {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.10);
        outline: none;
        font-size: 14px;
        margin-bottom: 16px;
        background: var(--background-color);
        color: var(--default-color);
    }

    .btn-accent {
        width: 100%;
        background: var(--accent-color);
        color: var(--contrast-color);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }

    .btn-accent:hover {
        opacity: 0.88;
    }

    .categories-table-wrap {
        background: var(--surface-color);
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow-x: auto;
    }

    .categories-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 460px;
    }

    .categories-table th {
        text-align: left;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--default-color);
        padding: 16px;
        border-bottom: 2px solid rgba(0, 0, 0, 0.06);
    }

    .categories-table td {
        padding: 14px 16px;
        font-size: 14px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    .categories-table tr:hover td {
        background-color: var(--background-color);
    }

    .categories-table strong {
        color: var(--heading-color);
    }

    .job-count-pill {
        background: rgba(14, 165, 233, 0.12);
        color: var(--accent-color);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
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
    }

    .btn-edit {
        background: rgba(14, 165, 233, 0.10);
        color: var(--accent-color);
    }

    .btn-delete {
        background: rgba(220, 38, 38, 0.10);
        color: #dc2626;
    }

    .btn-edit:hover {
        background: var(--accent-color);
        color: var(--contrast-color);
    }

    .btn-delete:hover {
        background: #dc2626;
        color: #fff;
    }

    .edit-row-form {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .edit-row-form input[type="text"] {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.10);
        font-size: 13.5px;
        outline: none;
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

    @media (max-width: 900px) {
        .categories-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include "../includes/header.php" ?>
<section class="admin-categories">
    <h1>Manage Categories</h1>
    <p class="subtitle"> Add, edit and delete job categories </p>
    <?php if ($message != "") { ?> <div class="alert-msg <?php echo $messageType; ?>"> <?php echo htmlspecialchars($message); ?> </div> <?php } ?>
    <div class="categories-layout">
        <div class="add-category-panel">
            <h2>Add New Category</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <label> Category Name </label>
                <input type="text" name="category_name" placeholder="e.g. UI/UX Design" required>
                <button type="submit" name="add_category" class="btn-accent"> Add Category </button>
            </form>
        </div>
        <div>
            <div class="categories-table-wrap">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Jobs Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($categories) > 0) {
                            while ($cat = mysqli_fetch_assoc($categories)) { ?>
                                <tr>
                                    <td>
                                        <form class="edit-row-form" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($cat['id']); ?>">
                                            <input type="text" name="category_name_edit" value="<?php echo htmlspecialchars($cat['name']); ?>">
                                            <button type="submit" name="edit_category" class="btn-icon btn-edit"> <i class="bi bi-check2"></i> Save </button>
                                        </form>
                                    </td>
                                    <td> <span class="job-count-pill"> <?php echo (int)$cat['job_count']; ?> jobs </span> </td>
                                    <td>
                                        <div class="action-btns">
                                            <form method="POST" onsubmit="return confirm('Do you want to delete this category?')">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                <input type="hidden" name="delete_category_id" value="<?php echo htmlspecialchars($cat['id']); ?>">
                                                <button type="submit" class="btn-icon btn-delete"> <i class="bi bi-trash"></i> Delete </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="3" class="no-data"> No categories found </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1) { ?>
                <div class="pagination-wrap">
                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php include "../includes/footer.php" ?>