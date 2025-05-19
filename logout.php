<?php
session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login or home page
header("Location: index.php"); // Change to your login/home page
exit();
?>
