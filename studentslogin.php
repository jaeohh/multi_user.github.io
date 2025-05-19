<?php
session_start();
require 'db.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM students WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['status'] === 'inactive') {
                $error_message = "Your account is inactive. Please go to admin office to activate Account.";
            } else {
                // Generate new verification code
                $verification_code = rand(100000, 999999);
                $conn->query("UPDATE students SET verification_code = '$verification_code' WHERE email = '$email'");

                // Send verification code email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'hahahdadhello@gmail.com';
                    $mail->Password = 'yzhw uxrx dvxk byts';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('no-reply@yourdomain.com', 'Student System');
                    $mail->addAddress($email, $user['first_name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'New Email Verification Code';
                    $mail->Body = "<p>Hello <strong>{$user['first_name']}</strong>,</p>
                                   <p>Your new verification code is:</p>
                                   <h2>$verification_code</h2>
                                   <p>Please enter this code on the verification page to activate your account.</p>
                                   <br><small>Do not reply to this email.</small>";

                    $mail->send();
                    $_SESSION['email_for_verification'] = $email;
                } catch (Exception $e) {
                    $error_message = "Verification code email failed to send. Please contact admin.";
                }
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "User not found.";
    }
}

// Handle Registration
if (isset($_POST['register'])) {
    $student_number = $conn->real_escape_string($_POST['student_number']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);
    $gender = $conn->real_escape_string($_POST['gender']);

    $check = $conn->query("SELECT * FROM students WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error_message = "Email already registered!";
    } else {
        $insert = $conn->query("INSERT INTO students (student_number, first_name, last_name, email, password, phone, gender, status, is_verified)
                                VALUES ('$student_number', '$first_name', '$last_name', '$email', '$password', '$phone', '$gender', 'inactive', 0)");
        if ($insert) {
            $success_message = "Registration successful! Please log in to receive a verification code.";
        } else {
            $error_message = "Registration failed!";
        }
    }
}

// Handle Email Verification
if (isset($_POST['verify_email'])) {
    $verification_code = $conn->real_escape_string($_POST['verification_code']);
    $email = $_SESSION['email_for_verification'];

    $result = $conn->query("SELECT * FROM students WHERE email = '$email' AND verification_code = '$verification_code'");
    if ($result->num_rows > 0) {
        $conn->query("UPDATE students SET is_verified = 1, status = 'active' WHERE email = '$email'");
        unset($_SESSION['email_for_verification']);

        $userData = $conn->query("SELECT * FROM students WHERE email = '$email'")->fetch_assoc();
        $_SESSION['student_name'] = $userData['first_name'] . ' ' . $userData['last_name'];
        $_SESSION['student_id'] = $userData['id'];  // <-- Fixed here

        header("Location: student_dashboard.php");
        exit;
    } else {
        $error_message = "Invalid verification code.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Login</title>
 <style>
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    html, body {
      height: 100%;
      width: 100%;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .left {
      flex: 7;
      background: url('hello.JPG') no-repeat center center/cover;
    }

    .right {
      flex: 3;
      background-color:rgb(246, 242, 242);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 20px;
    }

    .login-form {
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .logo {
      width: 200px;
      margin-bottom: 20px;
    }

    .login-form h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .login-form input[type="email"],
    .login-form input[type="password"],
    .login-form input[type="text"] {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      letter-spacing: 0.05em;
    }

    .show-password {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      font-size: 0.9rem;
    }

    .show-password input {
      margin-right: 8px;
    }

    .forgot-link {
      display: block;
      margin-bottom: 20px;
      text-align: right;
      font-size: 0.9rem;
      color: #2a7de1;
      text-decoration: none;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }

    .btn-group {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 15px;
    }

    .login-form button {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-login {
      background-color: #2a7de1;
      color: white;
    }

    .btn-login:hover {
      background-color: #1c5fb0;
    }

    .btn-register {
      background-color: #e0e0e0;
      color: #333;
    }

    .btn-register:hover {
      background-color: #c0c0c0;
    }

    .back-home-btn {
      padding: 12px 20px;
      background-color:rgb(48, 163, 234);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      display: inline-block;
      width: 100%;
      text-align: center;
      text-decoration: none;
      margin-top: 10px;
    }

    .back-home-btn:hover {
      background-color:rgb(15, 204, 97);
    }

    .modal {
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
    }

    .modal-content {
      background-color: rgba(255, 255, 255, 0.4); 
      margin: 10% auto;
      padding: 30px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
      text-align: right;
    }

    .modal-content h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .modal-content input,
    .modal-content select {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      letter-spacing: 0.05em;
    }

    .modal-content button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #2a7de1;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
    }

    .modal-content .close {
      float: right;
      font-size: 24px;
      cursor: pointer;
      color: #aaa;
    }

    .modal-content .close:hover {
      color: #000;
    }

    .info-section {
      margin-top: 30px;
      font-size: 0.9rem;
      text-align: center;
      color: #333;
      max-width: 350px;
    }

    .info-section a {
      color: #2a7de1;
      text-decoration: none;
    }

    .info-section a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .left {
        display: none;
      }

      .right {
        flex: 1;
        width: 100%;
        padding: 20px;
      }

      .login-form {
        max-width: 100%;
      }

      .info-section {
        max-width: 100%;
      }
    }

 </style>
</head>
<body>
<div class="container">
  <div class="left"></div>
  <div class="right">
    <form class="login-form" action="studentslogin.php" method="POST">
      <img src="15.png" alt="Logo" class="logo" />
      <h2>Student Login</h2>

      <input type="email" name="email" placeholder="Email Address" required />
      <input type="password" name="password" id="password" placeholder="Password" required />
      <div class="show-password">
        <input type="checkbox" onclick="togglePassword()"> Show Password
      </div>

      <div class="btn-group">
        <button type="submit" name="login" class="btn-login">Login</button>
        <button type="button" class="btn-register" onclick="openRegisterModal()">Register</button>
      </div>

      <a href="index.php" class="back-home-btn">Back to Homepage</a>
    </form>

    <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
    <?php if (isset($success_message)) echo "<p class='success'>$success_message</p>"; ?>

    <div class="info-section">
      <p>By logging in, you agree to our <a href="terms_and_conditions.php">Terms & Conditions</a> and <a href="privacy_policy.php">Privacy Policy</a>.</p>
      <p>If you have any questions, feel free to <a href="contact_us.php">Contact Us</a>.</p>
    </div>
  </div>
</div>

<!-- Register Modal -->
<div id="studentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeRegisterModal()">&times;</span>
    <h2>Register New Student</h2>
    <form action="studentslogin.php" method="POST">
      <input type="text" name="student_number" placeholder="Student Number" required />
      <input type="text" name="first_name" placeholder="First Name" required />
      <input type="text" name="last_name" placeholder="Last Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="tel" name="phone" placeholder="Phone Number" required />
      <select name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>
      <button type="submit" name="register">Register</button>
    </form>
    <a href="index.php" class="back-home-btn">Back to Homepage</a>
  </div>
</div>

<!-- Email Verification Modal -->
<?php if (isset($_SESSION['email_for_verification'])): ?>
<div id="verificationModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeVerificationModal()">&times;</span>
    <h2>Email Verification</h2>
    <p>Please enter the verification code sent to your email.</p>
    <form action="studentslogin.php" method="POST">
      <input type="text" name="verification_code" placeholder="Enter Verification Code" required />
      <button type="submit" name="verify_email">Verify Email</button>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
  function togglePassword() {
  const pwd = document.getElementById('password');
  pwd.type = pwd.type === 'password' ? 'text' : 'password';
}
function openRegisterModal() {
  document.getElementById('studentModal').style.display = 'block';
}
function closeRegisterModal() {
  document.getElementById('studentModal').style.display = 'none';
}
function openVerificationModal() {
  document.getElementById('verificationModal').style.display = 'block';
}
function closeVerificationModal() {
  document.getElementById('verificationModal').style.display = 'none';
}

<?php if (isset($_SESSION['email_for_verification'])): ?>
  window.onload = openVerificationModal;
<?php endif; ?>
</script>

</body>
</html>
