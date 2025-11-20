<?php
include "../config/connection.php";

// Sementara hardcode dulu admin_id kalau belum pakai sistem login
// Setelah login system aktif, hapus baris di bawah
if (!isset($_SESSION['user_id'])) {
  $_SESSION['user_id'] = 1001; // user_id dari admin David Frans
  $_SESSION['role'] = 'admin';
}

// Cek apakah user admin
if ($_SESSION['role'] != 'admin') {
  header("Location: ../index.php");
  exit();
}

$admin_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$admin_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $admin = mysqli_fetch_assoc($result);
} else {
  $admin = ['name' => '', 'email' => '', 'password' => ''];
}

// Update data admin jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  if (!empty($password)) {
    $update = "UPDATE users SET name='$name', email='$email', password='$password' WHERE user_id='$admin_id'";
  } else {
    $update = "UPDATE users SET name='$name', email='$email' WHERE user_id='$admin_id'";
  }

  if (mysqli_query($conn, $update)) {
    echo "<script>alert('Profile updated successfully!'); window.location.href='settings.php';</script>";
  } else {
    echo "<script>alert('Failed to update profile!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | Stylezone Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      scroll-behavior: smooth;
      scrollbar-width: none;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f5f7;
      margin: 0;
      padding: 0;
      display: flex;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      background: #1e1e1e;
      color: #fff;
      padding: 30px 20px;
      box-sizing: border-box;
      border-right: 2px dotted rgba(255, 255, 255, 0.2);
      overflow-y: auto;
    }

    .sidebar h2 {
      margin-bottom: 40px;
      font-size: 24px;
      font-weight: 600;
      color: #ffffff;
      text-transform: uppercase;
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
      padding: 30px;
      margin-left: 260px;
      min-height: 100vh;
    }

    .glass-box {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.3);
      margin-bottom: 25px;
    }

    h1 {
      font-size: 24px;
      margin-bottom: 25px;
      color: #222;
    }

    h2 {
      font-size: 18px;
      color: #333;
    }

    label {
      color: #333;
      font-size: 15px;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      outline: none;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    input:focus,
    select:focus {
      border-color: #000;
    }

    .form-section {
      margin-bottom: 35px;
    }

    .btn-save {
      background-color: #000;
      color: #fff;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 15px;
      transition: background 0.3s ease;
    }

    .btn-save:hover {
      background-color: #333;
    }

    .divider {
      height: 1px;
      background: rgba(0, 0, 0, 0.1);
      margin: 25px 0;
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <h2>STYLEZONE</h2>
    <a href="dashboard.php" class="nav-item">🏠 Dashboard</a>
    <a href="products.php" class="nav-item">🛍️ Products</a>
    <a href="users.php" class="nav-item">👤 Users</a>
    <a href="orders.php" class="nav-item">📦 Orders</a>
    <a href="settings.php" class="nav-item active">⚙️ Settings</a>
  </div>

  <div class="main">
    <div class="glass-box">
      <h1>Admin Settings</h1>

      <!-- Profile Settings -->
      <div class="form-section">
        <h2>👤 Profile Settings</h2>
        <form method="POST">
          <label for="admin-name">Admin Name</label>
          <input type="text" id="admin-name" name="name" value="<?= htmlspecialchars($admin['name']); ?>" required>

          <label for="admin-email">Email</label>
          <input type="email" id="admin-email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" required>

          <label for="admin-password">Password</label>
          <input type="password" id="admin-password" name="password" placeholder="Enter new password (leave blank if unchanged)">

          <button type="submit" name="update_profile" class="btn-save">Save Profile</button>
        </form>
      </div>

      <div class="divider"></div>
    </div>

</body>

</html>