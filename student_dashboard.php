<?php
session_start();
include('db.php');

if (!isset($_SESSION['student_name']) || !isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$student_name = $_SESSION['student_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            background: #F9FAFB;
            min-height: 100vh;
            overflow: hidden;
        }

        .top-bar {
            background: #FFFFFF;
            padding: 15px 20px;
            border-bottom: 2px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-area {
            display: flex;
            align-items: center;
        }

        .logo-area h1 { /* Changed h2 to h1 and increased font-size */
            font-size: 36px;
        }

        .navigation-buttons {
            background: #FFFFFF;
            padding: 10px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            gap: 20px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .nav-btn {
            background: none;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 16px;
            color: #333;
            transition: color 0.3s ease;
        }

        .nav-btn i {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .nav-btn:hover,
        .nav-btn.active {
            color: #007bff; /* Highlight color */
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: #F9FAFB;
        }

        iframe {
            width: 100%;
            border: none;
            min-height: 600px; /* Adjust as needed */
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .top-bar .logo-area h1 { /* Adjusted font size for mobile */
                font-size: 28px;
            }

            .navigation-buttons {
                gap: 10px;
                padding: 10px;
            }

            .nav-btn {
                font-size: 14px;
                padding: 8px 10px;
            }

            .nav-btn i {
                font-size: 20px;
                margin-bottom: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="logo-area">
            <h1><?php echo htmlspecialchars($student_name); ?></h1> </div>
        <div class="user-area"> <a href="logout.php" style="color: red;"><i class="fa fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="navigation-buttons">
        <button class="nav-btn active" data-target="home">
            <i class="fas fa-home"></i>
            Home
        </button>
        <button class="nav-btn" data-target="contact">
            <i class="fa fa-envelope"></i>
            Contact
        </button>
        <button class="nav-btn" data-target="activities">
            <i class="fa fa-list-alt"></i>
            Activities
        </button>
        <button class="nav-btn" data-target="profile">
            <i class="fa fa-user"></i>
            Profile
        </button>
        </div>

    <div class="content" id="mainContent">
        <h2>Announcements</h2>
        <p>Welcome to your dashboard! Stay tuned for the latest announcements.</p>
    </div>

    <script>
        document.querySelectorAll('.navigation-buttons .nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.navigation-buttons .nav-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const target = this.getAttribute('data-target');
                const mainContent = document.getElementById('mainContent');

                if (target === 'home') {
                    mainContent.innerHTML = `
                        <h2>Announcements</h2>
                        <p>Welcome to your dashboard! Stay tuned for the latest announcements.</p>
                    `;
                } else {
                    const pageMap = {
                        contact: "student_contact.php",
                        activities: "student_class_activities.php",
                        profile: "student_profile.php"
                    };
                    mainContent.innerHTML = `<iframe src="${pageMap[target]}" height="900"></iframe>`;
                }
            });
        });

        // Set 'Home' as active on initial load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.navigation-buttons .nav-btn[data-target="home"]').classList.add('active');
        });
    </script>
</body>
</html>