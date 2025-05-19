<?php
require_once 'db.php'; // Ensure db.php connects to your database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validations
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        echo "All fields are required.";
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Email not found.";
        exit;
    }

    // Update the user's password
    $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->bind_param("ss", $hashed_password, $email);
    
    if ($update->execute()) {
        echo "<script>
                alert('Password successfully reset.');
                window.location.href = 'signin.php';
              </script>";
    } else {
        echo "Error resetting password. Please try again.";
    }

    $stmt->close();
    $update->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
