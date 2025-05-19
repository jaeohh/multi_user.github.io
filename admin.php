<?php
session_start();
include 'db.php'; // Your DB connection
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: signin.php");  // Redirect to signin if user is not logged in
  exit();
}
// Total Revenue
$revenue_result = mysqli_query($conn, "SELECT SUM(total_price) AS revenue FROM orders");
$revenue_row = mysqli_fetch_assoc($revenue_result);
$total_revenue = $revenue_row['revenue'] ?? 0;

// Total Users (customers only)
$user_result = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users WHERE role = 'customer'");
$user_row = mysqli_fetch_assoc($user_result);
$total_users = $user_row['total_users'] ?? 0;

// Total Baristas
$barista_result = mysqli_query($conn, "SELECT COUNT(*) AS total_baristas FROM users WHERE role = 'barista'");
$barista_row = mysqli_fetch_assoc($barista_result);
$total_baristas = $barista_row['total_baristas'] ?? 0;

// Orders Today
date_default_timezone_set('Asia/Manila'); // Adjust this if your server is in a different timezone

$today = date('Y-m-d'); // Today in 'YYYY-MM-DD'
$query = "SELECT COUNT(*) AS orders_today FROM orders WHERE DATE(created_at) = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$orders_today = $row['orders_today'] ?? 0;

// Total Products
$products_result = mysqli_query($conn, "SELECT COUNT(*) AS total_products FROM menu");
$products_row = mysqli_fetch_assoc($products_result);
$total_products = $products_row['total_products'] ?? 0;

// Assume $firstname is from session or default
$firstname = "Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coffee Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
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
      font-size: 28px;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 10px;
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
      font-size: 18px;
    }
    .sidebar ul li i {
      width: 25px;
      text-align: center;
    }
    .sidebar ul li:hover,
    .sidebar ul li.active {
      background: #eee;
      border-radius: 5px;
    }
    .logout {
      color: red;
      padding: 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 18px;
    }
    .content {
      margin-left: 20%;
      width: 80%;
      overflow-y: auto;
      background: #F9FAFB;
    }
    .dashboard-header {
      font-size: 32px;
      font-weight: bold;
      background: #FFFFFF;
      padding: 15px;
      border-bottom: 2px solid #ddd;
      position: sticky;
      top: 0;
      z-index: 1;
    }
    .dashboard-metrics {
      display: flex;
      flex-wrap: wrap;
      padding: 20px;
      gap: 20px;
    }
    .metric-box {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      flex: 1 1 calc(33% - 40px);
      min-width: 200px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .metric-title {
      font-size: 14px;
      color: #888;
    }
    .metric-value {
      font-size: 24px;
      font-weight: bold;
      color: #333;
    }
    .section-title {
      padding: 0 20px;
      font-size: 20px;
      margin-top: 30px;
      font-weight: bold;
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
      <h1><i class="fas fa-mug-hot"></i> Coffee Admin</h1>
      <ul>
        <li class="active" data-target="dashboard"><i class="fa fa-home"></i> Dashboard</li>
        <li data-target="products_inventory"><i class="fa fa-boxes"></i> Products Inventory</li>
        <li data-target="orders"><i class="fa fa-receipt"></i> Orders</li>
        <li data-target="transactions"><i class="fa fa-exchange-alt"></i> Transactions</li>
        <li data-target="customers"><i class="fa fa-users"></i> Customers</li>
        <li data-target="baristas"><i class="fa fa-user-tie"></i> Baristas</li>
        <li data-target="admin"><i class="fa fa-user-shield"></i> Admin</li>
        <li data-target="reports"><i class="fa fa-chart-pie"></i> Reports</li>
      </ul>
    </div>
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content" id="mainContent">
    <div class="dashboard-header">Welcome, <?php echo htmlspecialchars($firstname); ?></div>
    <div class="dashboard-metrics">
      <div class="metric-box">
        <div class="metric-title">Total Revenue</div>
        <div class="metric-value">₱<?php echo number_format($total_revenue, 2); ?></div>
      </div>
      <div class="metric-box">
        <div class="metric-title">Total Users</div>
        <div class="metric-value"><?php echo $total_users; ?></div>
      </div>
      <div class="metric-box">
        <div class="metric-title">Total Baristas</div>
        <div class="metric-value"><?php echo $total_baristas; ?></div>
      </div>
      <div class="metric-box">
        <div class="metric-title">Orders Today</div>
        <div class="metric-value"><?php echo $orders_today; ?></div>
      </div>
      <div class="metric-box">
        <div class="metric-title">Total Products</div>
        <div class="metric-value"><?php echo $total_products; ?></div>
      </div>
    </div>

    <div class="section-title">Recent 5 Orders</div>
    <iframe src="recent5orders.php" height="300"></iframe>

    <div class="section-title">Top Products</div>
    <iframe src="top3products.php" height="500"></iframe>
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
          location.reload(); // reload to show the dashboard content
        } else {
          const pageMap = {
            products_inventory: "products_inventory.php",
            orders: "orders.php",
            transactions: "orderss.php",
            customers: "customers.php",
            baristas: "baristas.php",
            admin: "admin_management.php",
            reports: "reports.php"
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
