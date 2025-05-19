<?php
session_start();
include('db.php');

if (!isset($_SESSION['teacher_name']) || !isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit;
}

$teachers_name = $_SESSION['teacher_name'];
$teachers_id = $_SESSION['teacher_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>teachers Dashboard</title>
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
      background: #F9FAFB;
      height: 100vh;
      overflow: hidden;
    }

    .hamburger {
      display: none;
      position: fixed;
      top: 20px;
      left: 20px;
      font-size: 28px;
      z-index: 1001;
      cursor: pointer;
    }

    .sidebar {
      width: 20%;
      background: #FFFFFF;
      padding: 20px;
      height: 100vh;
      border-right: 2px solid #ddd;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: fixed;
      left: 0;
      top: 0;
      overflow-y: auto;
      transition: left 0.3s ease;
      z-index: 1000;
    }

    .sidebar h1 {
      margin-bottom: 20px;
      font-size: 42px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 20px;
    }

    .sidebar ul li i {
      width: 25px;
      text-align: center;
    }

    .sidebar ul li:hover,
    .sidebar ul li.active {
      background: #ddd;
      border-radius: 5px;
    }

    .logout {
      color: red;
      padding: 12px;
      text-align: left;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 18px;
    }

    .logout i {
      width: 25px;
      text-align: center;
    }

    .content {
      margin-left: 20%;
      padding: 0;
      height: 100vh;
      overflow-y: auto;
      width: 80%;
      background: #F9FAFB;
      transition: margin-left 0.3s ease;
    }

    .dashboard-header {
      font-size: 46px;
      background: #FFFFFF;
      padding: 15px;
      border-bottom: 2px solid #ddd;
      width: 100%;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    iframe {
      width: 100%;
      border: none;
      min-height: 800px;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 60%;
        left: -100%;
      }

      .sidebar.active {
        left: 0;
      }

      .sidebar h1 {
        margin-top: 60px;
        margin-bottom: 20px;
        font-size: 42px;
      }

      .content {
        margin-left: 0;
        width: 100%;
      }

      .hamburger {
        display: block;
      }

      .hamburger i {
        font-size: 28px;
        color: #333;
      }

      .dashboard-header {
        padding-left: 60px;
      }
    }
  </style>
</head>
<body>
  <!-- Hamburger -->
  <div class="hamburger" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>

  <div class="sidebar">
    <div>
      <h1><i class="fas fa-user-shield"></i> teachers</h1>
      <br>
      <ul>
        <li class="active" data-target="dashboard"><i class="fas fa-chart-line"></i> Dashboard</li>
        <br>
        <li data-target="students"><i class="fas fa-users"></i> View Students</li>
        <br>
        <li data-target="activity"><i class="fas fa-address-book"></i> Activity</li>
        <br>
        <li data-target="contacts"><i class="fas fa-address-book"></i> Show All Contact</li>
        <br>
        <li data-target="profile"><i class="fas fa-user"></i> Profile</li>
      </ul> 
    </div>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content" id="mainContent">
    <div class="dashboard-header">Welcome, <?php echo htmlspecialchars($teachers_name); ?></div>
    <div id="menuContent">
      <h2>Dashboard</h2>
      <p>Here you can manage all teachers operations such as users, teachers, reports, and more.</p>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('active');
    }

    document.querySelectorAll('.sidebar ul li').forEach(item => {
      item.addEventListener('click', function () {
        document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
        this.classList.add('active');

        const target = this.getAttribute('data-target');
        const mainContent = document.getElementById('mainContent');

        if (target === 'dashboard') {
          mainContent.innerHTML = `
            <div class="dashboard-header">Dashboard</div>
            <div id="menuContent" style="padding: 20px;">
              <h2>Dashboard</h2>
              <p>Here you can manage all teachers operations such as users, teachers, reports, and more.</p>
            </div>
          `;
        } else {
          const pageMap = {
            students: "teachers_view_students.php",
            activity: "teacher_activity.php",
            contacts: "teachers_show_contacts.php",
            profile: "teacher_profile.php"
                    };
          mainContent.innerHTML = `
            <div class="dashboard-header">${this.textContent.trim()}</div>
            <iframe src="${pageMap[target]}" height="900"></iframe>
          `;
        }
      });
    });
  </script>
</body>
</html>
