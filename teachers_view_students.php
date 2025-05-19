<?php
session_start();
include('db.php');

if (!isset($_SESSION['teacher_name']) || !isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit;
}

$teacher_name = $_SESSION['teacher_name'];
$teacher_id = $_SESSION['teacher_id'];

// Handle deletion of selected students from a class
if (isset($_POST['delete_students']) && isset($_POST['class_name']) && isset($_POST['student_ids'])) {
    $class_name = $_POST['class_name'];
    $student_ids = $_POST['student_ids']; // array

    // Use prepared statement to delete each selected student for this teacher and class
    $stmt = $conn->prepare("DELETE FROM class WHERE class_name = ? AND teacher_id = ? AND student_id = ?");

    foreach ($student_ids as $student_id) {
        $stmt->bind_param("sii", $class_name, $teacher_id, $student_id);
        $stmt->execute();
    }

    $stmt->close();

    header("Location: teachers_view_students.php?class=" . urlencode($class_name) . "&deleted=1");
    exit;
}

// Fetch all classes for this teacher
$classes_stmt = $conn->prepare("SELECT DISTINCT class_name FROM class WHERE teacher_id = ?");
$classes_stmt->bind_param("i", $teacher_id);
$classes_stmt->execute();
$classes_result = $classes_stmt->get_result();
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);
$classes_stmt->close();

$selected_class = $_GET['class'] ?? null;
$students = [];

if ($selected_class) {
    $stmt = $conn->prepare("SELECT student_id, student_first_name, student_last_name FROM class WHERE teacher_id = ? AND class_name = ?");
    $stmt->bind_param("is", $teacher_id, $selected_class);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Classes & Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto">

        <h1 class="text-3xl font-bold mb-6">Welcome, <?= htmlspecialchars($teacher_name) ?></h1>

        <h2 class="text-2xl mb-4">Your Classes</h2>

        <div class="grid grid-cols-3 gap-6 mb-8">
            <?php if (count($classes) === 0): ?>
                <p class="col-span-3 text-center text-gray-600">You have no assigned classes.</p>
            <?php else: ?>
                <?php foreach ($classes as $class): ?>
                    <a href="?class=<?= urlencode($class['class_name']) ?>" 
                       class="bg-white p-6 rounded shadow hover:shadow-lg transition cursor-pointer block text-center">
                        <h3 class="text-xl font-semibold"><?= htmlspecialchars($class['class_name']) ?></h3>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($selected_class): ?>
            <h2 class="text-2xl mb-4">Students in <?= htmlspecialchars($selected_class) ?></h2>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">Selected students deleted successfully.</div>
            <?php endif; ?>

            <?php if (count($students) === 0): ?>
                <p>No students in this class.</p>
            <?php else: ?>
                <form method="POST" onsubmit="return confirm('Are you sure you want to remove selected students from this class?')">
                    <input type="hidden" name="class_name" value="<?= htmlspecialchars($selected_class) ?>">
                    <table class="w-full border-collapse border border-gray-300 mb-4">
                        <thead>
                            <tr class="bg-gray-200 text-left">
                                <th class="border border-gray-300 p-2">Select</th>
                                <th class="border border-gray-300 p-2">Student ID</th>
                                <th class="border border-gray-300 p-2">Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 p-2 text-center">
                                        <input type="checkbox" name="student_ids[]" value="<?= (int)$student['student_id'] ?>">
                                    </td>
                                    <td class="border border-gray-300 p-2"><?= (int)$student['student_id'] ?></td>
                                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($student['student_first_name'] . ' ' . $student['student_last_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button type="submit" name="delete_students" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Remove Selected Students
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
