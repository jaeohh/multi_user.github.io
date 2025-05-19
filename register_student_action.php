<?php
session_start();
require 'db.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['register'])) {
    $student_number = $conn->real_escape_string($_POST['student_number']);
    $first_name     = $conn->real_escape_string($_POST['first_name']);
    $last_name      = $conn->real_escape_string($_POST['last_name']);
    $email          = $conn->real_escape_string($_POST['email']);
    $password_plain = $_POST['password'];
    $password       = password_hash($password_plain, PASSWORD_DEFAULT);
    $phone          = $conn->real_escape_string($_POST['phone']);
    $gender         = $conn->real_escape_string($_POST['gender']);
    $code           = rand(100000, 999999);

    // Check if email already exists
    $check = $conn->query("SELECT * FROM students WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit;
    }

    // Insert student into database
    $insert = $conn->query("INSERT INTO students (student_number, first_name, last_name, email, password, phone, gender, verification_code, is_verified)
                            VALUES ('$student_number', '$first_name', '$last_name', '$email', '$password', '$phone', '$gender', '$code', 0)");

    if ($insert) {
        // Send verification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'contawerodel2@gmail.com'; // Replace with your email
            $mail->Password   = 'winy eyyk nzjj ntdx';      // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('no-reply@yourdomain.com', 'Student System');
            $mail->addAddress($email, $first_name);
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification Code';
            $mail->Body    = "<p>Hello <strong>$first_name</strong>,</p>
                              <p>Thank you for registering. Your verification code is:</p>
                              <h2>$code</h2>
                              <p>Please enter this code on the verification page to activate your account.</p>
                              <br><small>Do not reply to this email.</small>";

            $mail->send();

            // Redirect to verification page
            echo "<script>alert('Registration successful! A verification code was sent to your email.'); 
                  window.location='verify_email.php?email=$email';</script>";
            exit;

        } catch (Exception $e) {
            echo "<script>alert('Registration saved but email failed to send. Please contact admin.'); 
                  window.location='index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Registration failed!'); window.history.back();</script>";
        exit;
    }
}

// Handle login from the same file (optional)
if (isset($_POST['login'])) {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM students WHERE email = '$email'");

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        if ($student['is_verified'] == 0) {
            echo "<script>alert('Your account is not verified. Please check your email.'); 
                  window.location='verify_email.php?email=$email';</script>";
            exit;
        }

        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
            $_SESSION['email'] = $student['email'];

            header("Location: student_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.history.back();</script>";
        exit;
    }
}
?>
