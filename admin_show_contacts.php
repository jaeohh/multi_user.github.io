<?php
session_start();
include 'db.php';

// Optional: verify admin session here
// if ($_SESSION['role'] !== 'admin') { exit('Unauthorized'); }

// Update contact
if (isset($_POST['edit_contact'])) {
    $id = $_POST['contact_id'];
    $name = $_POST['contact_name'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];
    $relationship = $_POST['relationship'];

    $update = "UPDATE contacts SET contact_name='$name', contact_email='$email', contact_phone='$phone', relationship='$relationship' WHERE contact_id=$id";
    mysqli_query($conn, $update);
}

// Delete contact
if (isset($_POST['delete_contact'])) {
    $id = $_POST['contact_id'];
    mysqli_query($conn, "DELETE FROM contacts WHERE contact_id=$id");
}

// Fetch all contacts
$contacts = mysqli_query($conn, "SELECT * FROM contacts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - All Contacts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
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

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    function filterTable() {
      const input = document.getElementById("searchInput");
      const filter = input.value.toLowerCase();
      const table = document.querySelector("table tbody");
      const rows = table.getElementsByTagName("tr");

      for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName("td")[0]; // Name column
        if (nameCell) {
          const name = nameCell.textContent || nameCell.innerText;
          rows[i].style.display = name.toLowerCase().includes(filter) ? "" : "none";
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">All Contacts</h2>

    <!-- Search Bar -->
    <div class="mb-4">
      <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by contact name..." class="w-full max-w-sm p-2 border rounded shadow-sm">
    </div>

    <table class="w-full border text-sm">
      <thead class="bg-gray-200 text-center">
        <tr>
          <th class="py-2 px-3 border">Name</th>
          <th class="py-2 px-3 border">Email</th>
          <th class="py-2 px-3 border">Phone</th>
          <th class="py-2 px-3 border">Relationship</th>
          <th class="py-2 px-3 border">User ID</th>
          <th class="py-2 px-3 border">User Type</th>
          <th class="py-2 px-3 border">Created</th>
          <th class="py-2 px-3 border">Actions</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php while ($row = mysqli_fetch_assoc($contacts)): ?>
        <tr>
          <td class="py-2 px-3 border"><?php echo $row['contact_name']; ?></td>
          <td class="py-2 px-3 border"><?php echo $row['contact_email']; ?></td>
          <td class="py-2 px-3 border"><?php echo $row['contact_phone']; ?></td>
          <td class="py-2 px-3 border"><?php echo $row['relationship']; ?></td>
          <td class="py-2 px-3 border"><?php echo $row['user_id']; ?></td>
          <td class="py-2 px-3 border capitalize"><?php echo $row['user_type']; ?></td>
          <td class="py-2 px-3 border"><?php echo $row['created_at']; ?></td>
          <td class="py-2 px-3 border space-x-2">
            <button onclick="openEditModal('<?php echo $row['contact_id']; ?>', '<?php echo addslashes($row['contact_name']); ?>', '<?php echo $row['contact_email']; ?>', '<?php echo $row['contact_phone']; ?>', '<?php echo addslashes($row['relationship']); ?>')" class="bg-yellow-400 text-white px-2 py-1 rounded">Edit</button>
            <button onclick="openDeleteModal('<?php echo $row['contact_id']; ?>')" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Edit Contact Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Edit Contact</h2>
      <form method="POST">
        <input type="hidden" name="contact_id" id="edit_id">
        <input type="text" name="contact_name" id="edit_name" placeholder="Contact Name" class="w-full p-2 mb-3 border rounded" required>
        <input type="email" name="contact_email" id="edit_email" placeholder="Contact Email" class="w-full p-2 mb-3 border rounded">
        <input type="text" name="contact_phone" id="edit_phone" placeholder="Contact Phone" class="w-full p-2 mb-3 border rounded">
        <input type="text" name="relationship" id="edit_relationship" placeholder="Relationship" class="w-full p-2 mb-3 border rounded">
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="edit_contact" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Contact Modal -->
  <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-full max-w-sm">
      <h2 class="text-lg font-semibold mb-4">Delete Contact</h2>
      <p class="mb-4">Are you sure you want to delete this contact?</p>
      <form method="POST">
        <input type="hidden" name="contact_id" id="delete_id">
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="delete_contact" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
