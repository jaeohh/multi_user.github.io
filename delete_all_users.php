<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Adjust table name based on your schema. Example assumes `users` table excluding admins.
$sql = "DELETE FROM users WHERE role != 'admin'";  // Prevent deleting admins
if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "All users deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting users: " . mysqli_error($conn);
}

header("Location: admin_dashboard.php");
exit;
?>
