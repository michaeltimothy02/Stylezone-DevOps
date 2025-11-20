<?php
include "../config/connection.php";

// === Ambil data orders ===
$sql = "
  SELECT 
    o.order_id, 
    u.name AS customer, 
    o.order_date, 
    CAST(o.total AS DECIMAL(10,2)) AS total,   
    o.status, 
    o.address, 
    o.shipping, 
    p.method AS payment_method
  FROM orders o
  JOIN users u ON o.user_id = u.user_id
  LEFT JOIN payments p ON o.order_id = p.order_id
  ORDER BY o.order_date DESC
";
$result = $conn->query($sql);

// === Jika request AJAX popup ===
if (isset($_GET['popup_id'])) {
  $id = intval($_GET['popup_id']);

  $order_sql = "
    SELECT o.*, u.name AS customer, p.method AS payment_method 
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    WHERE o.order_id = $id
  ";
  $order = $conn->query($order_sql)->fetch_assoc();

  $items_sql = "
    SELECT pr.name_product, oi.size, oi.quantity, pr.price, (oi.quantity * pr.price) AS subtotal
    FROM order_items oi
    JOIN products pr ON oi.product_id = pr.product_id
    WHERE oi.order_id = $id
  ";
  $items = $conn->query($items_sql);
?>
  <div class="order-detail-popup">
    <div class="popup-content">
      <span class="close" onclick="closePopup()">&times;</span>
      <h2>Order Details — #<?php echo str_pad($order['order_id'], 4, STR_PAD_LEFT); ?></h2>

      <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer']); ?></p>
      <p><strong>Order Time:</strong> <?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></p>
      <p><strong>Total Price:</strong> Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></p>
      <p><strong>Status:</strong>
        <span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
      </p>

      <hr>
      <p><strong>Items:</strong></p>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $items->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name_product']); ?></td>
              <td><?php echo htmlspecialchars($row['size']); ?></td>
              <td><?php echo $row['quantity']; ?></td>
              <td>Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></td>
              <td>Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
            <td><strong>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></strong></td>
          </tr>
        </tfoot>
      </table>

      <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
      <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
      <p><strong>Shipping Method:</strong> <?php echo htmlspecialchars($order['shipping']); ?></p>
    </div>
  </div>
<?php exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders | Stylezone Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
      scroll-behavior: smooth;
      scrollbar-width: none;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f5f7;
      margin: 0;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      height: 100vh;
      background: #1e1e1e;
      color: #fff;
      padding: 30px 20px;
      position: fixed;
    }

    .sidebar h2 {
      margin-bottom: 40px;
      font-weight: 700;
    }

    .nav-item {
      display: block;
      padding: 10px 15px;
      margin-bottom: 10px;
      border-radius: 10px;
      color: #ccc;
      text-decoration: none;
      transition: 0.3s;
    }

    .nav-item.active,
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    /* Main */
    .main {
      flex: 1;
      width: calc(100% - 260px);
      padding: 30px;
      margin-left: 260px;
    }

    .glass-box {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    /* Heading */
    h1 {
      font-size: 24px;
      color: #222;
      margin-bottom: 20px;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
    }

    th {
      background: rgba(255, 255, 255, 0.15);
      color: #333;
    }

    tr {
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    tr:hover {
      background: rgba(255, 255, 255, 0.25);
    }

    /* Buttons */
    .btn {
      padding: 8px 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: 0.2s;
    }

    .btn-view {
      background: #2196F3;
      color: #fff;
    }

    .btn-delete {
      background: #f44336;
      color: #fff;
    }

    .btn:hover {
      opacity: 0.85;
    }

    /* Status */
    .status {
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 13px;
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

    /* Filter & Search */
    .search-filter {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .search-box,
    .filter-select {
      padding: 10px 15px;
      border-radius: 8px;
      border: 1px solid #ddd;
      background: #fff;
    }

    .search-box {
      flex: 1;
      min-width: 250px;
    }

    .filter-select {
      min-width: 150px;
    }

    /* Popup */
    .order-detail-popup {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .popup-content {
      position: relative;
      width: 520px;
      max-height: 90vh;
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      overflow-y: auto;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
    }

    .close {
      position: absolute;
      right: 20px;
      top: 15px;
      font-size: 26px;
      color: #666;
      cursor: pointer;
    }

    .popup-content h2 {
      margin: 0 0 15px;
    }

    .popup-content p {
      margin: 8px 0;
      line-height: 1.5;
    }

    .popup-content table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0 15px;
    }

    .popup-content th,
    .popup-content td {
      padding: 10px 8px;
      font-size: 14px;
      border-bottom: 1px solid #eee;
    }

    .popup-content th {
      background: #fafafa;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>STYLEZONE</h2>
    <a href="dashboard.php" class="nav-item">🏠 Dashboard</a>
    <a href="products.php" class="nav-item">🛍️ Products</a>
    <a href="users.php" class="nav-item">👤 Users</a>
    <a href="orders.php" class="nav-item active">📦 Orders</a>
    <a href="settings.php" class="nav-item">⚙️ Settings</a>
  </div>

  <div class="main">
    <div class="glass-box">
      <h1>Manage Orders</h1>

      <!-- Search & Filter -->
      <div class="search-filter">
        <input type="text" id="searchInput" class="search-box" placeholder="Search orders...">
        <select id="statusFilter" class="filter-select">
          <option value="">All Statuses</option>
          <option value="completed">Completed</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      <!-- Tabel Orders -->
      <table id="ordersTable">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Order Time</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="ordersTableBody">
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td>#<?php echo $row['order_id']; ?></td>
              <td><?php echo htmlspecialchars($row['customer']); ?></td>
              <td><?php echo date('Y-m-d H:i', strtotime($row['order_date'])); ?></td>
              <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
              <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
              <td>
                <button class="btn btn-view" onclick="viewOrder(<?php echo $row['order_id']; ?>)">View</button>
                <button class="btn btn-delete" onclick="deleteOrder(<?php echo $row['order_id']; ?>)">Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function viewOrder(id) {
      fetch(window.location.pathname + '?popup_id=' + id)
        .then(res => res.text())
        .then(html => document.body.insertAdjacentHTML('beforeend', html))
        .catch(err => console.error(err));
    }

    function closePopup() {
      const popup = document.querySelector('.order-detail-popup');
      if (popup) popup.remove();
    }

    function deleteOrder(id) {
      if (confirm("Yakin ingin menghapus order ini?")) {
        window.location.href = "delete_order.php?id=" + id;
      }
    }

    // === Pencarian & Filter ===
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('ordersTableBody');

    function filterOrders() {
      const searchTerm = searchInput.value.toLowerCase();
      const filterStatus = statusFilter.value;
      for (let row of tableBody.rows) {
        const id = row.cells[0].textContent.toLowerCase();
        const name = row.cells[1].textContent.toLowerCase();
        const status = row.cells[4].textContent.toLowerCase();
        const matchSearch = id.includes(searchTerm) || name.includes(searchTerm);
        const matchStatus = !filterStatus || status.includes(filterStatus);
        row.style.display = matchSearch && matchStatus ? '' : 'none';
      }
    }
    searchInput.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
  </script>
</body>

</html>

<?php $conn->close(); ?>