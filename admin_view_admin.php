<?php
include 'db.php';

// ADD ADMIN
if (isset($_POST['add_admin'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $verification_code = bin2hex(random_bytes(4));
    $is_verified = 0;
    $status = 'inactive';

    mysqli_query($conn, "INSERT INTO admin (first_name, last_name, email, password, verification_code, is_verified, status) 
        VALUES ('$first_name', '$last_name', '$email', '$password', '$verification_code', '$is_verified', '$status')");
}

// UPDATE ADMIN
if (isset($_POST['update_admin'])) {
    $id = $_POST['admin_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $status = $_POST['status'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $getPassword = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM admin WHERE admin_id='$id'"));
        $password = $getPassword['password'];
    }

    mysqli_query($conn, "UPDATE admin SET 
        first_name='$first_name', 
        last_name='$last_name', 
        email='$email', 
        password='$password', 
        status='$status' 
        WHERE admin_id='$id'");
}

// DELETE ADMIN
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM admin WHERE admin_id='$id'");
}

// TOGGLE STATUS
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM admin WHERE admin_id='$id'"));
    $newStatus = ($get['status'] == 'active') ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE admin SET status='$newStatus' WHERE admin_id='$id'");
}

// FETCH ADMINS
$admins = mysqli_query($conn, "SELECT * FROM admin");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Admins</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function openEditModal(data) {
      document.getElementById('edit_admin_id').value = data.admin_id;
      document.getElementById('edit_first_name').value = data.first_name;
      document.getElementById('edit_last_name').value = data.last_name;
      document.getElementById('edit_email').value = data.email;
      document.getElementById('edit_password').value = '';
      document.getElementById('edit_status').value = data.status;
      document.getElementById('editModal').classList.remove('hidden');
    }

    function openAddModal() {
      document.getElementById('addModal').classList.remove('hidden');
    }

    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
    }
  </script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="container mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Admin Management</h1>
      <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded">+ Add Admin</button>
    </div>
    <table class="min-w-full border border-gray-200 text-sm">
<thead class="bg-gray-200">
  <tr>
    <th class="py-2 px-4 border">Name</th>
    <th class="py-2 px-4 border">Email</th>
    <th class="py-2 px-4 border">Password</th>
    <th class="py-2 px-4 border">Verified</th>
    <th class="py-2 px-4 border">Status</th>
    <th class="py-2 px-4 border">Actions</th>
  </tr>
</thead>

      <tbody>
        <?php while ($row = mysqli_fetch_assoc($admins)): ?>
        <tr class="text-center">
          <td class="py-2 px-4 border"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
          <td class="py-2 px-4 border"><?php echo $row['email']; ?></td>
          <td class="py-2 px-4 border"><?php echo substr($row['password'], 0, 10); ?>...</td>

          <td class="py-2 px-4 border"><?php echo $row['is_verified'] ? 'Yes' : 'No'; ?></td>
          <td class="py-2 px-4 border">
            <a href="?toggle_status=<?php echo $row['admin_id']; ?>" class="px-2 py-1 text-white rounded <?php echo $row['status'] == 'active' ? 'bg-green-500' : 'bg-red-500'; ?>">
              <?php echo ucfirst($row['status']); ?>
            </a>
          </td>
          <td class="py-2 px-4 border space-x-2">
            <button onclick='openEditModal(<?php echo json_encode($row); ?>)' class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
            <a href="?delete=<?php echo $row['admin_id']; ?>" class="bg-red-600 text-white px-2 py-1 rounded" onclick="return confirm('Delete this admin?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Add Modal -->
  <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
      <h2 class="text-lg font-semibold mb-4">Add Admin</h2>
      <form method="POST">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <input type="text" name="first_name" placeholder="First Name" class="p-2 border rounded" required>
          <input type="text" name="last_name" placeholder="Last Name" class="p-2 border rounded" required>
          <input type="email" name="email" placeholder="Email" class="p-2 border rounded" required>
          <input type="text" name="password" placeholder="Password" class="p-2 border rounded" required>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="add_admin" class="px-4 py-2 bg-green-600 text-white rounded">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
      <h2 class="text-lg font-semibold mb-4">Edit Admin</h2>
      <form method="POST">
        <input type="hidden" name="admin_id" id="edit_admin_id">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <input type="text" name="first_name" id="edit_first_name" placeholder="First Name" class="p-2 border rounded" required>
          <input type="text" name="last_name" id="edit_last_name" placeholder="Last Name" class="p-2 border rounded" required>
          <input type="email" name="email" id="edit_email" placeholder="Email" class="p-2 border rounded" required>
          <input type="text" name="password" id="edit_password" placeholder="New Password (optional)" class="p-2 border rounded">
          <select name="status" id="edit_status" class="p-2 border rounded col-span-2">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="update_admin" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
