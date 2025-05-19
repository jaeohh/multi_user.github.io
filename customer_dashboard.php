<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");  // Redirect to signin if user is not logged in
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query to fetch user details
$query = "SELECT fullname FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Dashboard</title>
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

    .sidebar h2 {
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

    .cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      padding: 20px;
    }

    .card {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      height: 150px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .card span {
      font-size: 20px;
      color: #777;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .card span i {
      width: 25px;
      text-align: center;
    }

    .card h3 {
      font-size: 26px;
      margin-top: 10px;
    }
    iframe {
      width: 100%;
      border: none;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 50%;
        left: -100%;
      }

      .sidebar.active {
        left: 0;
      }
        .sidebar h1 {
        margin-top: 60px; /* <-- added spacing from the top */
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
        font-size: 28px; /* Adjust as needed, e.g., 32px or 40px for bigger */
        color: #333;     /* Optional: change icon color */
      }

      .dashboard-header {
        padding-left: 60px;
      }

      .cards {
        grid-template-columns: 1fr;
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
      <h1><i class="fas fa-user"></i> Customer</h1>
      <ul>
        <li class="active" data-target="dashboard"><i class="fa fa-home"></i> Dashboard</li>
        <li data-target="coffee"><i class="fa fa-shopping-cart"></i> Coffee</li>
        <li data-target="cart"><i class="fa fa-shopping-cart"></i> My Cart</li>
        <li data-target="orders"><i class="fa fa-box"></i> Orders</li>
        <li data-target="completed"><i class="fa fa-check-circle"></i> Completed</li>
        <li data-target="profile"><i class="fa fa-users"></i> Profile</li>
      </ul>
    </div>
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content" id="mainContent">
    <div class="dashboard-header">Welcome, <?php echo htmlspecialchars($user_name); ?></div>
    <div id="menuContent"></div>
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

              <div class="dashboard-header">Welcome, <?php echo htmlspecialchars($user_name); ?></div>
              <div id="menuContent"></div>

          `;
        } else {
          const pageMap = {
            coffee: "coffee.php",
            cart: "customer_cart.php",
            orders: "customer_orders.php",
            completed: "customer_completed.php",
            profile: "customer_profile.php"
          };
          mainContent.innerHTML = `
            <div class="dashboard-header">${this.textContent.trim()}</div>
            <iframe src="${pageMap[target]}" height="1000"></iframe>
          `;
        }
      });
    });
  </script>
</body>
</html>
