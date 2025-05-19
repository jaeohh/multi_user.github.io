<?php
include 'db.php';

// Function to output CSV for a given SQL and headers
function output_csv($conn, $filename, $headers, $sql) {
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename");

    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);

    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        // Ensure output order matches headers exactly
        $line = [];
        foreach ($headers as $col) {
            $line[] = $row[$col] ?? '';
        }
        fputcsv($output, $line);
    }
    fclose($output);
    exit();
}

if (isset($_GET['download'])) {
    switch ($_GET['download']) {
        case 'students':
            output_csv(
                $conn,
                'students.csv',
                ['id','student_number','first_name','last_name','email','password','phone','gender','verification_code','is_verified','status'],
                "SELECT id, student_number, first_name, last_name, email, password, phone, gender, verification_code, is_verified, status FROM students"
            );
            break;

        case 'teachers':
            output_csv(
                $conn,
                'teachers.csv',
                ['teacher_id','first_name','last_name','email','password','verification_code','is_verified','status'],
                "SELECT teacher_id, first_name, last_name, email, password, verification_code, is_verified, status FROM teacher"
            );
            break;

        case 'classes':
            output_csv(
                $conn,
                'classes.csv',
                ['class_id','class_name','teacher_id','teacher_name','student_id','student_first_name','student_last_name'],
                "SELECT class_id, class_name, teacher_id, teacher_name, student_id, student_first_name, student_last_name FROM class"
            );
            break;

        case 'contacts':
            output_csv(
                $conn,
                'contacts.csv',
                ['contact_id','user_id','user_type','contact_name','contact_email','contact_phone','relationship','created_at'],
                "SELECT contact_id, user_id, user_type, contact_name, contact_email, contact_phone, relationship, created_at FROM contact"
            );
            break;

        default:
            echo "Invalid download request.";
            exit();
    }
}

// Otherwise show the page with buttons
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports Download</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex justify-center pt-20">
    <div class="max-w-md w-full text-center">
        <h1 class="text-3xl font-bold mb-6">Download Reports (CSV)</h1>
        <div class="space-y-4">
            <a href="?download=students" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Download Students CSV</a>
            <a href="?download=teachers" class="block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Download Teachers CSV</a>
            <a href="?download=classes" class="block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Download Classes CSV</a>
            <a href="?download=contacts" class="block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Download Contacts CSV</a>
        </div>
    </div>
</body>

</html>
