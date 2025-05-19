<?php
include 'db.php';

// Handle update student
if (isset($_POST['update_student'])) {
    $id = $_POST['id'];
    $student_number = $_POST['student_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $password = $_POST['password'];

    $sql = "UPDATE students SET 
        student_number='$student_number', 
        first_name='$first_name', 
        last_name='$last_name', 
        email='$email', 
        phone='$phone', 
        gender='$gender', 
        status='$status'";

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password='$hashedPassword'";
    }

    $sql .= " WHERE id='$id'";
    mysqli_query($conn, $sql);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM students WHERE id='$id'");
}

// Handle toggle status
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM students WHERE id='$id'"));
    $newStatus = ($get['status'] == 'active') ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE students SET status='$newStatus' WHERE id='$id'");
}

// Handle Live Search
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM students 
              WHERE student_number LIKE '%$search%' 
                 OR first_name LIKE '%$search%' 
                 OR last_name LIKE '%$search%' 
              ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <tr class="text-center">
            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['student_number']); ?></td>
            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['gender']); ?></td>
            <td class="py-2 px-4 border">
                <a href="?toggle_status=<?php echo $row['id']; ?>"
                   class="px-2 py-1 text-white rounded <?php echo $row['status'] == 'active' ? 'bg-green-500' : 'bg-red-500'; ?>">
                    <?php echo ucfirst($row['status']); ?>
                </a>
            </td>
            <td class="py-2 px-4 border space-x-2">
                <button onclick='openEditModal(<?php echo json_encode($row); ?>)'
                        class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                <a href="?delete=<?php echo $row['id']; ?>"
                   class="bg-red-600 text-white px-2 py-1 rounded"
                   onclick="return confirm('Are you sure to delete?')">Delete</a>
            </td>
        </tr>
        <?php
    }
    exit; // Stop further output for AJAX
}

// Load all students initially
$students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - View Students</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function openEditModal(data) {
      document.getElementById('edit_id').value = data.id;
      document.getElementById('edit_student_number').value = data.student_number;
      document.getElementById('edit_first_name').value = data.first_name;
      document.getElementById('edit_last_name').value = data.last_name;
      document.getElementById('edit_email').value = data.email;
      document.getElementById('edit_phone').value = data.phone;
      document.getElementById('edit_gender').value = data.gender;
      document.getElementById('edit_status').value = data.status;
      document.getElementById('edit_password').value = '';
      document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('editModal').classList.add('hidden');
    }

    function searchStudents() {
      const search = document.getElementById('searchInput').value;
      const xhr = new XMLHttpRequest();
      xhr.open("GET", "?search=" + encodeURIComponent(search), true);
      xhr.onload = function () {
        if (this.status === 200) {
          document.getElementById('studentTableBody').innerHTML = this.responseText;
        }
      };
      xhr.send();
    }

    document.addEventListener("DOMContentLoaded", function () {
      document.getElementById('searchInput').addEventListener('input', searchStudents);
    });
  </script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="container mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Student Management</h1>

    <!-- Live Search Input -->
    <div class="mb-4 flex justify-end">
      <input type="text" id="searchInput" placeholder="Search by name or student number"
             class="border p-2 rounded w-64">
    </div>

    <table class="min-w-full border border-gray-200 text-sm">
      <thead class="bg-gray-200">
        <tr>
          <th class="py-2 px-4 border">Student Number</th>
          <th class="py-2 px-4 border">Name</th>
          <th class="py-2 px-4 border">Email</th>
          <th class="py-2 px-4 border">Phone</th>
          <th class="py-2 px-4 border">Gender</th>
          <th class="py-2 px-4 border">Status</th>
          <th class="py-2 px-4 border">Actions</th>
        </tr>
      </thead>
      <tbody id="studentTableBody">
        <?php while ($row = mysqli_fetch_assoc($students)): ?>
        <tr class="text-center">
          <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['student_number']); ?></td>
          <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
          <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['email']); ?></td>
          <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['phone']); ?></td>
          <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['gender']); ?></td>
          <td class="py-2 px-4 border">
            <a href="?toggle_status=<?php echo $row['id']; ?>"
               class="px-2 py-1 text-white rounded <?php echo $row['status'] == 'active' ? 'bg-green-500' : 'bg-red-500'; ?>">
              <?php echo ucfirst($row['status']); ?>
            </a>
          </td>
          <td class="py-2 px-4 border space-x-2">
            <button onclick='openEditModal(<?php echo json_encode($row); ?>)' 
                    class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
            <a href="?delete=<?php echo $row['id']; ?>" 
               class="bg-red-600 text-white px-2 py-1 rounded" 
               onclick="return confirm('Are you sure to delete?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg relative">
      <h2 class="text-lg font-semibold mb-4">Edit Student</h2>
      <form method="POST">
        <input type="hidden" name="id" id="edit_id">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <input type="text" name="student_number" id="edit_student_number" placeholder="Student Number" class="p-2 border rounded" required>
          <input type="text" name="first_name" id="edit_first_name" placeholder="First Name" class="p-2 border rounded" required>
          <input type="text" name="last_name" id="edit_last_name" placeholder="Last Name" class="p-2 border rounded" required>
          <input type="email" name="email" id="edit_email" placeholder="Email" class="p-2 border rounded" required>
          <input type="text" name="phone" id="edit_phone" placeholder="Phone" class="p-2 border rounded" required>
          <select name="gender" id="edit_gender" class="p-2 border rounded" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
          <select name="status" id="edit_status" class="p-2 border rounded" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <input type="text" name="password" id="edit_password" placeholder="Password (leave blank to keep current)" class="p-2 border rounded">
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
          <button type="submit" name="update_student" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
