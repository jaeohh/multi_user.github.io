<?php
session_start();
include 'db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get all classes of the student
$class_query = mysqli_query($conn, "
    SELECT * FROM class WHERE student_id = '$student_id'
");

$classes = [];
if (mysqli_num_rows($class_query) > 0) {
    while ($row = mysqli_fetch_assoc($class_query)) {
        $class_name = $row['class_name'];

        // Get only class-wide activities (student_id IS NULL)
        $activities_result = mysqli_query($conn, "
            SELECT * FROM class_activities 
            WHERE class_name = '" . mysqli_real_escape_string($conn, $class_name) . "' 
            AND student_id IS NULL
            ORDER BY created_at DESC
        ");

        $activities = [];
        while ($activity = mysqli_fetch_assoc($activities_result)) {
            $activities[] = $activity;
        }

        $classes[] = [
            'class_name' => $class_name,
            'teacher_name' => $row['teacher_name'],
            'activities' => $activities
        ];
    }
}

// Get specific activities for this student (across any class)
$specific_activities_query = mysqli_query($conn, "
    SELECT * FROM class_activities 
    WHERE student_id = '$student_id'
    ORDER BY created_at DESC
");

$student_specific_activities = [];
while ($activity = mysqli_fetch_assoc($specific_activities_query)) {
    $student_specific_activities[] = $activity;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Classes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal(id) {
            document.getElementById("activityModal-" + id).classList.toggle("hidden");
        }

        function toggleNotificationModal() {
            document.getElementById("notificationModal").classList.toggle("hidden");
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <!-- Notification bell -->
    <div class="fixed top-4 left-4 z-50">
        <button onclick="toggleNotificationModal()" class="relative text-gray-700">
            <svg class="w-8 h-8 text-blue-600 hover:text-blue-800" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586L2.707 14.879a1 1 0 001.414 1.414L6 14.414V8a4 4 0 118 0v6.414l1.879 1.879a1 1 0 001.414-1.414L16 11.586V8a6 6 0 00-6-6z" />
            </svg>
            <?php if (count($student_specific_activities) > 0): ?>
                <span class="absolute top-0 right-0 block h-2 w-2 bg-red-600 rounded-full animate-ping"></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-40">
        <div class="bg-white w-[90%] max-w-3xl p-6 rounded shadow max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-blue-600">Your Personal Activities</h3>
                <button onclick="toggleNotificationModal()" class="text-red-600 font-bold text-xl">&times;</button>
            </div>
            <?php if (!empty($student_specific_activities)): ?>
                <div class="space-y-4">
                    <?php foreach ($student_specific_activities as $activity): ?>
                        <div class="bg-yellow-50 border border-yellow-300 p-4 rounded shadow">
                            <h4 class="text-lg font-bold"><?= htmlspecialchars($activity['title']) ?></h4>
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($activity['description'])) ?></p>
                            <p class="text-xs text-gray-500 mt-2">Posted on <?= htmlspecialchars($activity['created_at']) ?></p>
                            <?php if (!empty($activity['activity_file'])): 
                                $file_url = 'uploads/' . rawurlencode($activity['activity_file']);
                            ?>
                                <div class="mt-2 flex gap-2">
                                    <a href="<?= $file_url ?>" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">View</a>
                                    <a href="<?= $file_url ?>" download class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Download</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No personal activities available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Classes Area -->
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4 text-center">My Classes</h1>

        <?php if (!empty($classes)): ?>
            <div class="grid md:grid-cols-2 gap-4">
                <?php foreach ($classes as $index => $class): ?>
                    <!-- Class Card -->
                    <div class="bg-green-100 border border-green-400 p-4 rounded shadow cursor-pointer hover:bg-green-200" onclick="toggleModal(<?= $index ?>)">
                        <h2 class="text-xl font-semibold">Class: <?= htmlspecialchars($class['class_name']) ?></h2>
                        <p class="text-gray-700">Teacher: <?= htmlspecialchars($class['teacher_name']) ?></p>
                        <p class="text-sm text-green-600 mt-1">Click to view activities</p>
                    </div>

                    <!-- Modal for class -->
                    <div id="activityModal-<?= $index ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-40">
                        <div class="bg-white w-[90%] max-w-3xl p-6 rounded shadow max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Activities for <?= htmlspecialchars($class['class_name']) ?></h3>
                                <button onclick="toggleModal(<?= $index ?>)" class="text-red-600 font-bold text-xl">&times;</button>
                            </div>

                            <?php if (!empty($class['activities'])): ?>
                                <div class="grid gap-4">
                                    <?php foreach ($class['activities'] as $activity): ?>
                                        <div class="border border-gray-200 p-4 rounded shadow bg-gray-50">
                                            <p class="font-bold text-lg"><?= htmlspecialchars($activity['title']) ?></p>
                                            <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($activity['description'])) ?></p>

                                            <?php if (!empty($activity['activity_file'])): 
                                                $file_url = 'uploads/' . rawurlencode($activity['activity_file']);
                                            ?>
                                                <div class="flex flex-wrap gap-2 mb-2">
                                                    <a href="<?= $file_url ?>" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View</a>
                                                    <a href="<?= $file_url ?>" download class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Download</a>
                                                </div>
                                            <?php endif; ?>

                                            <p class="text-xs text-gray-500 mt-1">Posted on <?= htmlspecialchars($activity['created_at']) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-600">No class-wide activities yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-center">You are not assigned to any class yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
