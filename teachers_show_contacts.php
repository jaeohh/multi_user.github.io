<?php
session_start();
include 'db.php';

// Simulate login (for testing only)
// $_SESSION['teacher_id'] = 1;

if (!isset($_SESSION['teacher_id'])) {
    echo "You must be logged in as a teacher.";
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Add contact
if (isset($_POST['add_contact'])) {
    $name = $_POST['contact_name'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];
    $relationship = $_POST['relationship'];
    $user_type = 'teacher';

    $query = "INSERT INTO contacts (user_id, user_type, contact_name, contact_email, contact_phone, relationship)
              VALUES ('$teacher_id', '$user_type', '$name', '$email', '$phone', '$relationship')";
    mysqli_query($conn, $query);
}

// Edit contact
if (isset($_POST['edit_contact'])) {
    $id = $_POST['contact_id'];
    $name = $_POST['contact_name'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];
    $relationship = $_POST['relationship'];

    $query = "UPDATE contacts SET contact_name='$name', contact_email='$email', contact_phone='$phone', relationship='$relationship'
              WHERE contact_id='$id' AND user_id='$teacher_id' AND user_type='teacher'";
    mysqli_query($conn, $query);
}

// Delete contact
if (isset($_POST['delete_contact'])) {
    $id = $_POST['contact_id'];
    $query = "DELETE FROM contacts WHERE contact_id='$id' AND user_id='$teacher_id' AND user_type='teacher'";
    mysqli_query($conn, $query);
}

// Get contacts for logged-in teacher
$contacts = mysqli_query($conn, "SELECT * FROM contacts WHERE user_id='$teacher_id' AND user_type='teacher' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Contacts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function openModal() {
      document.getElementById('addModal').classList.remove('hidden');
    }
    function closeModal() {
      document.getElementById('addModal').classList.add('hidden');
      document.getElementById('editModal').classList.add('hidden');
      document.getElementById('deleteModal').classList.add('hidden');
    }
    function openEditModal(id, name, email, phone, relationship) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_name').value = name;
      document.getElementById('edit_email').value = email;
      document.getElementById('edit_phone').value = phone;
      document.getElementById('edit_relationship').value = relationship;
      document.getElementById('editModal').classList.remove('hidden');
    }
    function openDeleteModal(id) {
      document.getElementById('delete_id').value = id;
      document.getElementById('deleteModal').classList.remove('hidden');
    }
  </script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">My Contacts</h2>
      <button onclick="openModal()" class="bg-green-600 text-white px-4 py-2 rounded">+ Add Contact</button>
    </div>

    <table class="w-full border text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="py-2 px-4 border">Name</th>
          <th class="py-2 px-4 border">Email</th>
          <th class="py-2 px-4 border">Phone</th>
          <th class="py-2 px-4 border">Relationship</th>
          <th class="py-2 px-4 border">Created</th>
          <th class="py-2 px-4 border">Actions</th>
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
          <td class="py-2 px-4 border space-x-2">
            <button onclick="openEditModal('<?php echo $row['contact_id']; ?>','<?php echo addslashes($row['contact_name']); ?>','<?php echo $row['contact_email']; ?>','<?php echo $row['contact_phone']; ?>','<?php echo addslashes($row['relationship']); ?>')" class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
            <button onclick="openDeleteModal('<?php echo $row['contact_id']; ?>')" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
          </td>
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
          <button type="submit" name="add_contact" class="px-4 py-2 bg-green-600 text-white rounded">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Contact Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Edit Contact</h2>
      <form method="POST">
        <input type="hidden" name="contact_id" id="edit_id">
        <input type="text" name="contact_name" id="edit_name" placeholder="Contact Name" class="w-full p-2 mb-3 border rounded" required>
        <input type="email" name="contact_email" id="edit_email" placeholder="Contact Email" class="w-full p-2 mb-3 border rounded">
        <input type="text" name="contact_phone" id="edit_phone" placeholder="Contact Phone" class="w-full p-2 mb-3 border rounded">
        <input type="text" name="relationship" id="edit_relationship" placeholder="Relationship" class="w-full p-2 mb-3 border rounded">
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="edit_contact" class="px-4 py-2 bg-yellow-500 text-white rounded">Update</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Contact Modal -->
  <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-sm">
      <h2 class="text-lg font-semibold mb-4">Delete Contact</h2>
      <p class="mb-4">Are you sure you want to delete this contact?</p>
      <form method="POST">
        <input type="hidden" name="contact_id" id="delete_id">
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="delete_contact" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
