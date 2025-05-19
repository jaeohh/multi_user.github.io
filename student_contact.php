<?php
session_start();
include 'db.php';

// Simulate login (for testing purposes)
// $_SESSION['id'] = 2; // Replace or uncomment to test

if (!isset($_SESSION['student_id'])) {
    echo "You must be logged in as a student.";
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle contact addition
if (isset($_POST['add_contact'])) {
    $name = $_POST['contact_name'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];
    $relationship = $_POST['relationship'];
    $user_type = 'student';

    $query = "INSERT INTO contacts (user_id, user_type, contact_name, contact_email, contact_phone, relationship)
              VALUES ('$student_id', '$user_type', '$name', '$email', '$phone', '$relationship')";
    mysqli_query($conn, $query);
}

// Retrieve contacts for this student
$contacts = mysqli_query($conn, "SELECT * FROM contacts WHERE user_id='$student_id' AND user_type='student'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Contacts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function openModal() {
      document.getElementById('addModal').classList.remove('hidden');
    }
    function closeModal() {
      document.getElementById('addModal').classList.add('hidden');
    }
  </script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">My Contacts</h2>
      <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Contact</button>
    </div>

    <table class="w-full border text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="py-2 px-4 border">Name</th>
          <th class="py-2 px-4 border">Email</th>
          <th class="py-2 px-4 border">Phone</th>
          <th class="py-2 px-4 border">Relationship</th>
          <th class="py-2 px-4 border">Created</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($contacts)): ?>
        <tr class="text-center">
          <td class="py-2 px-4 border"><?php echo $row['contact_name']; ?></td>
          <td class="py-2 px-4 border"><?php echo $row['contact_email']; ?></td>
          <td class="py-2 px-4 border"><?php echo $row['contact_phone']; ?></td>
          <td class="py-2 px-4 border"><?php echo $row['relationship']; ?></td>
          <td class="py-2 px-4 border"><?php echo $row['created_at']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Add Contact Modal -->
  <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Add New Contact</h2>
      <form method="POST">
        <input type="text" name="contact_name" placeholder="Contact Name" class="w-full p-2 mb-3 border rounded" required>
        <input type="email" name="contact_email" placeholder="Contact Email" class="w-full p-2 mb-3 border rounded">
        <input type="text" name="contact_phone" placeholder="Contact Phone" class="w-full p-2 mb-3 border rounded">
        <input type="text" name="relationship" placeholder="Relationship" class="w-full p-2 mb-3 border rounded">
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="add_contact" class="px-4 py-2 bg-blue-600 text-white rounded">Add</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
