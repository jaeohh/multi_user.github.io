<?php
session_start();
include 'db.php';

// Fetch teachers for dropdown
$teachers = mysqli_query($conn, "SELECT teacher_id, last_name FROM teachers");

// Fetch unassigned students
$unassigned_students = mysqli_query($conn, "SELECT * FROM students");


// Handle add class and assignment
if (isset($_POST['create_class'])) {
    $class_name = $_POST['class_name'];
    $teacher_id = $_POST['teacher_id'];
    
    // Get teacher full name
    $teacher = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name, last_name FROM teachers WHERE teacher_id='$teacher_id'"));
    $teacher_name = $teacher['first_name'] . ' ' . $teacher['last_name'];

    if (!empty($_POST['student_ids'])) {
        foreach ($_POST['student_ids'] as $student_id) {
            $student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id='$student_id'"));
            $first = $student['first_name'];
            $last = $student['last_name'];

            mysqli_query($conn, "INSERT INTO class (class_name, teacher_id, teacher_name, student_id, student_first_name, student_last_name)
            VALUES ('$class_name', '$teacher_id', '$teacher_name', '$student_id', '$first', '$last')");
        }
    }
    header("Location: admin_assign_teacher.php?success=assigned");
    exit();
}

// Delete class
if (isset($_POST['delete_class'])) {
    $class_id = $_POST['class_id'];
    $class_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT class_name FROM class WHERE class_id='$class_id'"));
    $class_name = $class_info['class_name'];
    mysqli_query($conn, "DELETE FROM class WHERE class_name='$class_name'");
    header("Location: admin_assign_teacher.php?deleted=1");
    exit();
}

// Get all classes
$classes = mysqli_query($conn, "SELECT DISTINCT class_name FROM class");

// Get selected class students
$selected_students = [];
if (isset($_GET['view_class'])) {
    $selected_class = $_GET['view_class'];
    $selected_students = mysqli_query($conn, "SELECT * FROM class WHERE class_name='$selected_class'");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Teacher to Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleCard() {
            document.getElementById("studentCard").classList.toggle("hidden");
        }
        function cancelForm() {
            document.getElementById("studentCard").classList.add("hidden");
        }
    </script>
</head>
<body class="p-6 bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Assign Teacher to Students</h1>

        <form method="POST">
            <div class="flex gap-4 mb-4">
                <input type="text" name="class_name" placeholder="Class Name" required class="border p-2 rounded w-1/3">
                
                <select name="teacher_id" required class="border p-2 rounded w-1/3">
                    <option value="">Select Teacher</option>
                    <?php while ($t = mysqli_fetch_assoc($teachers)): ?>
                        <option value="<?= $t['teacher_id'] ?>"><?= $t['last_name'] ?></option>
                    <?php endwhile; ?>
                </select>
                
                <button type="button" onclick="toggleCard()" class="bg-blue-600 text-white px-4 py-2 rounded">Select Students</button>
            </div>

            <div id="studentCard" class="hidden mb-4 border p-4 rounded bg-gray-50">
                <h2 class="font-semibold mb-2">Select Students</h2>
                <div class="grid grid-cols-3 gap-4">
                    <?php while ($s = mysqli_fetch_assoc($unassigned_students)): ?>
                        <label class="flex items-center space-x-2 bg-white p-2 rounded shadow">
                            <input type="checkbox" name="student_ids[]" value="<?= $s['id'] ?>">
                            <span><?= $s['first_name'] . ' ' . $s['last_name'] ?> (<?= $s['id'] ?>)</span>
                        </label>
                    <?php endwhile; ?>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="cancelForm()" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" name="create_class" class="bg-green-600 text-white px-4 py-2 rounded">Assign Now</button>
                </div>
            </div>
        </form>

        <!-- Class List -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">Class List</h2>
            <form method="GET" class="flex items-center space-x-4">
                <select name="view_class" class="border p-2 rounded">
                    <option value="">Select Class</option>
                    <?php while ($c = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?= $c['class_name'] ?>" <?= isset($_GET['view_class']) && $_GET['view_class'] == $c['class_name'] ? 'selected' : '' ?>>
                            <?= $c['class_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">View</button>
            </form>
        </div>

        <?php if (!empty($selected_students)): ?>
            <div class="mt-6">
                <h3 class="text-lg font-bold mb-2">Students in <?= htmlspecialchars($_GET['view_class']) ?></h3>
                <table class="w-full border text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border py-2">Student ID</th>
                            <th class="border py-2">Full Name</th>
                            <th class="border py-2">Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($s = mysqli_fetch_assoc($selected_students)): ?>
                        <tr class="text-center">
                            <td class="border py-2"><?= $s['student_id'] ?></td>
                            <td class="border py-2"><?= $s['student_first_name'] . ' ' . $s['student_last_name'] ?></td>
                            <td class="border py-2"><?= $s['teacher_name'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <form method="POST" class="mt-4">
                    <input type="hidden" name="class_id" value="<?= mysqli_fetch_assoc(mysqli_query($conn, "SELECT class_id FROM class WHERE class_name='{$_GET['view_class']}' LIMIT 1"))['class_id'] ?>">
                    <button type="submit" name="delete_class" class="bg-red-600 text-white px-4 py-2 rounded">Delete Class</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
