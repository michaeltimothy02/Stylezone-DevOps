<?php
include "../config/connection.php";

// ===== Hapus User =====
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $deleteQuery = "DELETE FROM users WHERE user_id = $id";
  if ($conn->query($deleteQuery)) {
    echo "<script>alert('User berhasil dihapus!'); window.location='users.php';</script>";
  } else {
    echo "<script>alert('Gagal menghapus user: " . $conn->error . "');</script>";
  }
}

// ===== Tambah User =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
  $name  = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $pass  = $_POST['password'];
  $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
  $role  = "admin";

  $sql = "INSERT INTO users (name, email, password, role) 
          VALUES ('$name', '$email', '$hashed_pass', '$role')";
  if ($conn->query($sql)) {
    echo "<script>alert('User berhasil ditambahkan!'); window.location='users.php';</script>";
  } else {
    echo "<script>alert('Gagal menambah user: " . $conn->error . "');</script>";
  }
}

// ===== Ambil Data Users =====
$sql = "SELECT * FROM users ORDER BY user_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users | Stylezone Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f5f7;
      margin: 0;
      padding: 0;
      display: flex;
    }

    .sidebar {
      width: 260px;
      background: #1e1e1e;
      color: #fff;
      height: 100vh;
      padding: 30px 20px;
      box-sizing: border-box;
    }

    .sidebar h2 {
      margin-bottom: 40px;
      font-weight: 700;
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

    .nav-item.active, .nav-item:hover {
      background: rgba(255,255,255,0.1);
      color: #fff;
    }

    .main {
      flex: 1;
      padding: 30px;
    }

    .glass-box {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    h1 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #222;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
    }

    th {
      background: rgba(255,255,255,0.15);
      color: #333;
      font-weight: 600;
    }

    tr {
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    tr:hover {
      background: rgba(255,255,255,0.25);
    }

    .btn {
      padding: 7px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: 0.2s;
      font-size: 13px;
    }

    .btn-add {
      background: #4CAF50;
      color: white;
      margin-bottom: 15px;
    }

    .btn-delete {
      background: #f44336;
      color: white;
    }

    .btn:hover {
      opacity: 0.85;
    }

    .action-buttons {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      padding: 25px;
      border-radius: 10px;
      width: 400px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
      color: #333;
    }

    .form-group input {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: 'Poppins', sans-serif;
    }

    .modal-actions { 
      display: flex; 
      justify-content: flex-end; 
      gap: 10px; 
    }

    .btn-cancel, .btn-submit {
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      width: 90px;
      height: 38px;
    }

    .btn-cancel { 
      background: #ccc;
      color: #333; 
    }

    .btn-submit { 
      background: #4CAF50; 
      color: #fff; 
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>STYLEZONE</h2>
    <a href="dashboard.php" class="nav-item">🏠 Dashboard</a>
    <a href="products.php" class="nav-item">🛍️ Products</a>
    <a href="users.php" class="nav-item active">👤 Users</a>
    <a href="orders.php" class="nav-item">📦 Orders</a>
    <a href="settings.php" class="nav-item">⚙️ Settings</a>
  </div>

  <div class="main">
    <div class="glass-box">
      <h1>Manage Users</h1>
      <button class="btn btn-add" id="openModalAdd">+ Add User</button>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>#<?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= ucfirst($row['role']) ?></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-delete" onclick="confirmDelete(<?= $row['user_id'] ?>)">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Add -->
  <div class="modal" id="addModal">
    <div class="modal-content">
      <h2>Add New User</h2>
      <form method="POST">
        <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
          <button type="submit" name="add_user" class="btn-submit">Add</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    document.getElementById('openModalAdd').onclick = () => {
      document.getElementById('addModal').style.display = 'flex';
    };

    window.onclick = function(e) {
      if (e.target.classList.contains('modal')) e.target.style.display = 'none';
    }

    function confirmDelete(id) {
      if (confirm('Yakin ingin menghapus user ini?')) {
        window.location = 'users.php?delete=' + id;
      }
    }
  </script>
</body>
</html>

<?php $conn->close(); ?>
