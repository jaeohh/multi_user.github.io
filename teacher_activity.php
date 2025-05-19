<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    echo "You must be logged in.";
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch classes assigned to the teacher
$classes = mysqli_query($conn, "SELECT DISTINCT class_name FROM class WHERE teacher_id = '$teacher_id'");

// Fetch students assigned to the teacher
$students = mysqli_query($conn, "SELECT DISTINCT student_id, student_first_name, student_last_name FROM class WHERE teacher_id = '$teacher_id'");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $getFile = mysqli_query($conn, "SELECT activity_file FROM class_activities WHERE activity_id = $id AND teacher_id = $teacher_id");
    if ($row = mysqli_fetch_assoc($getFile)) {
        $file = 'uploads/' . $row['activity_file'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
    mysqli_query($conn, "DELETE FROM class_activities WHERE activity_id = $id AND teacher_id = $teacher_id");
    header("Location: teacher_activity.php?deleted=1");
    exit();
}

// Handle Update
if (isset($_POST['update_activity'])) {
    $activity_id = intval($_POST['activity_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    mysqli_query($conn, "UPDATE class_activities SET title='$title', description='$description' WHERE activity_id=$activity_id AND teacher_id = $teacher_id");
    header("Location: teacher_activity.php?updated=1");
    exit();
}

// Handle Upload
if (isset($_POST['upload_activity'])) {
    $class_name = mysqli_real_escape_string($conn, $_POST['class_name']);
    $student_id = !empty($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : NULL;
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $filename = $_FILES['activity_file']['name'];
    $tempname = $_FILES['activity_file']['tmp_name'];
    $folder = 'uploads/' . $filename;

    if (move_uploaded_file($tempname, $folder)) {
        $insert = "INSERT INTO class_activities (class_name, student_id, title, description, activity_file, teacher_id, created_at) 
                   VALUES ('$class_name', " . ($student_id ? "'$student_id'" : "NULL") . ", '$title', '$description', '$filename', '$teacher_id', NOW())";
        mysqli_query($conn, $insert);
        header("Location: teacher_activity.php?uploaded=1");
        exit();
    } else {
        $error = "Failed to upload file.";
    }
}

// Fetch uploaded activities
$activities = mysqli_query($conn, "SELECT * FROM class_activities WHERE teacher_id = '$teacher_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Activities</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-center">Upload Class Activity</h2>

    <?php if (isset($_GET['uploaded'])): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded">Activity uploaded successfully!</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 mb-4 rounded">Activity deleted successfully.</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="bg-blue-100 text-blue-800 px-4 py-2 mb-4 rounded">Activity updated successfully.</div>
    <?php elseif (isset($error)): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 mb-4 rounded"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4 mb-10">
        <label class="block text-sm font-medium">Select Class:</label>
        <select name="class_name" required class="border p-2 rounded w-full">
            <option value="">-- Choose class --</option>
            <?php
            // Re-fetch classes inside form because the earlier result pointer moved to the end after while
            $class_results = mysqli_query($conn, "SELECT DISTINCT class_name FROM class WHERE teacher_id = '$teacher_id'");
            while ($c = mysqli_fetch_assoc($class_results)): ?>
                <option value="<?= htmlspecialchars($c['class_name']) ?>"><?= htmlspecialchars($c['class_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label class="block text-sm font-medium">(Optional) Assign to Specific Student:</label>
        <select name="student_id" class="border p-2 rounded w-full">
            <option value="">-- Whole Class --</option>
            <?php
            // Re-fetch students as well
            $student_results = mysqli_query($conn, "SELECT DISTINCT student_id, student_first_name, student_last_name FROM class WHERE teacher_id = '$teacher_id'");
            while ($s = mysqli_fetch_assoc($student_results)): ?>
                <option value="<?= htmlspecialchars($s['student_id']) ?>">
                    <?= htmlspecialchars($s['student_first_name'] . ' ' . $s['student_last_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="text" name="title" placeholder="Activity Title" required class="border p-2 rounded w-full">
        <textarea name="description" placeholder="Description (optional)" class="border p-2 rounded w-full"></textarea>
        <input type="file" name="activity_file" required class="border p-2 rounded w-full">

        <button type="submit" name="upload_activity" class="bg-blue-600 text-white px-4 py-2 rounded">Upload Activity</button>
    </form>

    <h3 class="text-xl font-semibold mt-8 mb-2">Uploaded Activities</h3>

    <table class="w-full mt-4 border text-sm">
        <thead class="bg-gray-200">
        <tr>
            <th class="border py-2 px-2">Class</th>
            <th class="border py-2 px-2">Student</th>
            <th class="border py-2 px-2">Title</th>
            <th class="border py-2 px-2">Description</th>
            <th class="border py-2 px-2">File</th>
            <th class="border py-2 px-2">Uploaded</th>
            <th class="border py-2 px-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Re-fetch activities again for safety
        $activities = mysqli_query($conn, "SELECT * FROM class_activities WHERE teacher_id = '$teacher_id' ORDER BY created_at DESC");
        while ($a = mysqli_fetch_assoc($activities)): ?>
            <tr class="text-center">
                <td class="border py-2 px-2"><?= htmlspecialchars($a['class_name']) ?></td>
                <td class="border py-2 px-2"><?= $a['student_id'] ? htmlspecialchars($a['student_id']) : 'All Students' ?></td>
                <td class="border py-2 px-2"><?= htmlspecialchars($a['title']) ?></td>
                <td class="border py-2 px-2"><?= nl2br(htmlspecialchars($a['description'])) ?></td>
                <td class="border py-2 px-2">
                    <a href="uploads/<?= htmlspecialchars($a['activity_file']) ?>" target="_blank" class="text-blue-600 underline"><?= htmlspecialchars($a['activity_file']) ?></a>
                </td>
                <td class="border py-2 px-2"><?= htmlspecialchars($a['created_at']) ?></td>
                <td class="border py-2 px-2">
                    <button onclick="document.getElementById('edit<?= $a['activity_id'] ?>').classList.remove('hidden')" class="text-blue-500 hover:underline">Edit</button> |
                    <a href="?delete=<?= $a['activity_id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this activity?')">Delete</a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div id="edit<?= $a['activity_id'] ?>" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
                    <h3 class="text-xl font-bold mb-4">Edit Activity</h3>
                    <form method="POST">
                        <input type="hidden" name="activity_id" value="<?= $a['activity_id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($a['title']) ?>" required class="border p-2 rounded w-full mb-2">
                        <textarea name="description" class="border p-2 rounded w-full mb-4"><?= htmlspecialchars($a['description']) ?></textarea>
                        <div class="flex justify-between">
                            <button type="submit" name="update_activity" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                            <button type="button" onclick="document.getElementById('edit<?= $a['activity_id'] ?>').classList.add('hidden')" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
