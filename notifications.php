<?php
session_start();
include "includes/config.php";
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit(); }

$userid = $_SESSION["user_id"];

mysqli_execute_query($conn, "update notifications set is_read = 1 where user_id = ?", [$userid]);

$query = "select * from notifications where user_id = ? order by created_at desc";
$result = mysqli_execute_query($conn, $query, [$userid]);

include "includes/header.php";
?>
<div style="max-width:700px; margin:40px auto; padding:0 20px;">
  <h2 style="font-family:'Raleway',sans-serif; margin-bottom:20px;">Notifications</h2>
  <?php if (mysqli_num_rows($result) > 0) { ?>
    <?php while ($n = mysqli_fetch_assoc($result)) { ?>
      <a href="<?php echo htmlspecialchars($n['link'] ?? '#'); ?>"
         style="display:block; padding:14px 16px; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:10px; text-decoration:none; color:#0f172a;">
        <?php echo htmlspecialchars($n['message']); ?>
        <div style="font-size:12px; color:#94a3b8; margin-top:4px;"><?php echo date("d M, h:i A", strtotime($n['created_at'])); ?></div>
      </a>
    <?php } ?>
  <?php } else { ?>
    <p style="color:#94a3b8;">No notifications yet.</p>
  <?php } ?>
</div>
<?php include "includes/footer.php"; ?>