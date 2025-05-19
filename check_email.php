<?php
require_once 'db.php'; // Ensure db.php connects to your database

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);
    
    // Prepare the SQL statement to check if the email exists and is associated with the "customer" role
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any record is returned
    if ($result->num_rows > 0) {
        // Email exists and is associated with the "customer" role
        echo json_encode(['exists' => true]);
    } else {
        // Email does not exist or is not associated with the "customer" role
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['exists' => false]);
}
?>
