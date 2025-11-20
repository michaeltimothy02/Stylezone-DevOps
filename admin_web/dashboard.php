<?php
include "../config/connection.php";

// Query total data untuk card overview
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];

// Query recent orders
$sql = "
  SELECT o.order_id, u.name AS customer, o.order_date, o.total, o.status
  FROM orders o
  JOIN users u ON o.user_id = u.user_id
  ORDER BY o.order_date DESC
  LIMIT 5
";
$recentOrders = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Stylezone Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    * {
      scroll-behavior: smooth;
      scrollbar-width: none;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f5f7;
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 260px;
      background: #1e1e1e;
      color: #fff;
      padding: 30px 20px;
      box-sizing: border-box;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar h2 {
      font-weight: 700;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .logout-btn {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      color: #fff;
      border-radius: 8px;
      padding: 8px 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .nav-item {
      display: block;
      padding: 10px 15px;
      border-radius: 10px;
      color: #ccc;
      text-decoration: none;
      margin-bottom: 10px;
      transition: 0.3s;
    }

    .nav-item.active,
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .main {
      flex: 1;
      margin-left: 260px;
      padding: 30px;
      box-sizing: border-box;
      width: calc(100% - 260px);
    }

    .glass-box {
      background: #fff;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
    }

    h1 {
      font-size: 24px;
      margin-bottom: 25px;
      color: #222;
    }

    .card-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .card {
      flex: 1;
      min-width: 250px;
      background: #fff;
      border-radius: 20px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .card h2 {
      font-size: 16px;
      color: #555;
      margin-bottom: 10px;
    }

    .card p {
      font-size: 28px;
      font-weight: 600;
      color: #111;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
    }

    th {
      background: rgba(255, 255, 255, 0.15);
      color: #333;
      font-weight: 600;
    }

    tr {
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    tr:hover {
      background: rgba(0, 0, 0, 0.02);
    }

    .status {
      padding: 6px 12px;
      border-radius: 12px;
      font-size: 13px;
      text-transform: capitalize;
    }

    .status.completed {
      background: #c8e6c9;
      color: #2e7d32;
    }

    .status.pending {
      background: #fff3cd;
      color: #856404;
    }

    .status.cancelled {
      background: #f8d7da;
      color: #721c24;
    }

    .status.processing {
      background: #cce7ff;
      color: #0066cc;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div>
      <h2>STYLEZONE
        <button class="logout-btn" onclick="logout()">Logout</button>
      </h2>
      <a href="dashboard.php" class="nav-item active">🏠 Dashboard</a>
      <a href="products.php" class="nav-item">🛍️ Products</a>
      <a href="users.php" class="nav-item">👤 Users</a>
      <a href="orders.php" class="nav-item">📦 Orders</a>
      <a href="settings.php" class="nav-item">⚙️ Settings</a>
    </div>
  </div>

  <div class="main">
    <div class="glass-box">
      <h1>Dashboard Overview</h1>
      <div class="card-container">
        <div class="card">
          <h2>Total Products</h2>
          <p><?= $totalProducts ?></p>
        </div>
        <div class="card">
          <h2>Users</h2>
          <p><?= $totalUsers ?></p>
        </div>
        <div class="card">
          <h2>Total Orders</h2>
          <p><?= $totalOrders ?></p>
        </div>
      </div>

      <h1>Recent Orders</h1>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $recentOrders->fetch_assoc()): ?>
            <tr>
              <td>#<?= $row['order_id'] ?></td>
              <td><?= htmlspecialchars($row['customer']) ?></td>
              <td><?= date('M d, Y', strtotime($row['order_date'])) ?></td>
              <td>Rp <?= number_format((float)$row['total'], 0, ',', '.') ?></td>
              <td>
                <span class="status <?= strtolower($row['status']) ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function logout() {
      if (confirm('Are you sure you want to log out?')) {
        window.location.href = '../logout.php';
      }
    }
  </script>
</body>

</html>