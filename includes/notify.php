<?php
// includes/notify.php

function sendNotification($conn, $userId, $type, $message, $link = null) {
    $query = "insert into notifications (user_id, type, message, link) values (?, ?, ?, ?)";
    return mysqli_execute_query($conn, $query, [$userId, $type, $message, $link]);
}
?>