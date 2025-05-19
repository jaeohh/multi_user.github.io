<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student_id'])) {
    echo "You must be logged in.";
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch current student data
$query = mysqli_query($conn, "SELECT * FROM students WHERE id = '$student_id'");
$student = mysqli_fetch_assoc($query);

// Update logic
if (isset($_POST['save_changes'])) {
    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $first_name     = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name      = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $phone          = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender         = mysqli_real_escape_string($conn, $_POST['gender']);
    $password       = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE students SET 
            student_number = '$student_number',
            first_name = '$first_name', 
            last_name = '$last_name', 
            email = '$email',
            phone = '$phone',
            gender = '$gender',
            password = '$hashed_password'
            WHERE id = '$student_id'");
    } else {
        $update = mysqli_query($conn, "UPDATE students SET 
            student_number = '$student_number',
            first_name = '$first_name', 
            last_name = '$last_name', 
            email = '$email',
            phone = '$phone',
            gender = '$gender'
            WHERE id = '$student_id'");
    }

    if ($update) {
        header("Location: student_profile.php?updated=true");
        exit();
    } else {
        echo "<script>alert('Update failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function enableEdit() {
            document.querySelectorAll('input, select').forEach(input => {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
            });
            document.getElementById('saveButton').classList.remove('hidden');
            document.getElementById('editButton').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">My Profile</h2>
        <form method="POST">
            <label class="block mb-2 text-sm">Student Number</label>
            <input type="text" name="student_number" value="<?= htmlspecialchars($student['student_number']) ?>" class="w-full p-2 mb-3 border rounded" readonly required>

            <label class="block mb-2 text-sm">First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" class="w-full p-2 mb-3 border rounded" readonly required>

            <label class="block mb-2 text-sm">Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" class="w-full p-2 mb-3 border rounded" readonly required>

            <label class="block mb-2 text-sm">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="w-full p-2 mb-3 border rounded" readonly required>

            <label class="block mb-2 text-sm">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" class="w-full p-2 mb-3 border rounded" readonly required>

            <label class="block mb-2 text-sm">Gender</label>
            <select name="gender" class="w-full p-2 mb-4 border rounded" disabled required>
                <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>

            <label class="block mb-2 text-sm">Password <span class="text-gray-500 text-xs">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="w-full p-2 mb-4 border rounded" readonly>

            <div class="flex justify-between">
                <button type="button" id="editButton" onclick="enableEdit()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Update</button>
                <button type="submit" id="saveButton" name="save_changes" class="hidden bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
