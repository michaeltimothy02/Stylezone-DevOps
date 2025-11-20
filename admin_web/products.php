<?php
include "../config/connection.php";

/* ===== Hapus produk ===== */
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $query_delete = "DELETE FROM products WHERE product_id = '$delete_id'";
  if (mysqli_query($conn, $query_delete)) {
    echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
    exit;
  } else {
    echo "<script>alert('Error deleting product.');</script>";
  }
}

/* ===== Tambah produk ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
  $category_id = $_POST['category_id'];
  $name_product = $_POST['name_product'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $size = $_POST['size'];

  $target_dir = "../uploads/";
  if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  $image_name = basename($_FILES["image"]["name"]);
  $target_file = $target_dir . $image_name;

  if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    // Buat link URL absolut (ubah sesuai path project kamu)
    $base_url = "http://localhost/STYLEZONE/"; 
    $image_path = $base_url . "uploads/" . $image_name;
  } else {
    die("<script>alert('Error uploading image.'); window.history.back();</script>");
  }

  $query_insert = "INSERT INTO products (category_id, image, name_product, description, price, stock, size)
                   VALUES ('$category_id', '$image_path', '$name_product', '$description', '$price', '$stock', '$size')";

  if (mysqli_query($conn, $query_insert)) {
    echo "<script>alert('Product added successfully!'); window.location.href='products.php';</script>";
    exit;
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}

/* ===== Edit produk ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_product'])) {
  $product_id = $_POST['product_id'];
  $category_id = $_POST['category_id'];
  $name_product = $_POST['name_product'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $size = $_POST['size'];
  $image_path = $_POST['existing_image'];

  if (!empty($_FILES["image"]["name"])) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      $base_url = "http://localhost/STYLEZONE/"; 
      $image_path = $base_url . "uploads/" . $image_name;
    }
  }

  $query_update = "UPDATE products 
                   SET category_id='$category_id', name_product='$name_product', description='$description',
                       price='$price', stock='$stock', size='$size', image='$image_path'
                   WHERE product_id='$product_id'";

  if (mysqli_query($conn, $query_update)) {
    echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
    exit;
  } else {
    echo "<script>alert('Error updating product.');</script>";
  }
}

/* ===== Tampilkan data produk ===== */
$query = "
  SELECT 
    p.*,
    c.category_gender,
    c.category_type
  FROM products p
  INNER JOIN categories c 
    ON p.category_id = c.category_id
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products | Stylezone Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
      min-height: 100vh; /* Pastikan body selalu setinggi layar */
      overflow-x: hidden; /* Hindari scroll horizontal */
}

/* === Sidebar Tetap dan Stabil === */
    .sidebar {
      width: 260px;
      background: #1e1e1e;
      color: #fff;
      padding: 30px 20px;
      box-sizing: border-box;
      flex-shrink: 0; /* Pastikan sidebar tidak mengecil saat zoom out */
      position: sticky; /* Ganti dari fixed → sticky untuk lebih smooth */
      top: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
}

/* === Area Konten === */
    .main {
      flex: 1;
      padding: 30px;
      min-height: 100vh;
      box-sizing: border-box;
      background: #f4f5f7;
      overflow-x: auto;
}
    .sidebar h2 {
      margin-bottom: 40px;
      font-size: 24px;        /* Ukuran huruf sesuai file Settings */
      font-weight: 600;
      color: #ffffff;
      text-transform: uppercase;
      letter-spacing: 1px;    /* Biar hurufnya agak renggang dan elegan */
      text-align: left;       /* Rata kiri agar tetap konsisten */
    }
    .nav-item {
      display: block;
      padding: 14px 20px;
      border-radius: 12px;
      color: #ccc;
      text-decoration: none;
      margin-bottom: 6px;   /* lebih rapat tapi tetap lega */
      transition: 0.3s;
      font-size: 15px;
      font-weight: 500;
}

    .nav-item.active,
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.1);
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
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.3);
}

    h1 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #222;
      font-weight: bold;
}

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      table-layout: auto;
}

    th,
    td {
      padding: 12px 15px;
      text-align: left;
      vertical-align: middle;
      font-size: 14px;
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
      background: rgba(255, 255, 255, 0.25);
}

    .product-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid rgba(0, 0, 0, 0.1);
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

    .btn-add:hover {
      background: #4CAF50;
      opacity: 0.9;
      color: white;
    }

    .btn-edit {
      background: #2196F3;
      color: white;
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

    .size-container {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
}

    .size-badge {
      background: rgba(0, 0, 0, 0.05);
      color: #333;
      font-size: 13px;
      font-weight: 500;
      padding: 5px 10px;
      border-radius: 8px;
}
  </style>
</head>

<body>
  <div class="sidebar">
    <h2>STYLEZONE</h2>
    <a href="dashboard.php" class="nav-item">🏠 Dashboard</a>
    <a href="products.php" class="nav-item active">🛍️ Products</a>
    <a href="users.php" class="nav-item">👤 Users</a>
    <a href="orders.php" class="nav-item">📦 Orders</a>
    <a href="settings.php" class="nav-item">⚙️ Settings</a>
  </div>

  <div class="main">
    <div class="glass-box">
      <h1>Manage Products</h1>
      <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</button>

      <table>
        <thead>
          <tr>
            <th>ID</th><th>Image</th><th>Category</th><th>Name</th><th>Description</th><th>Price (IDR)</th><th>Stock</th><th>Size</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td>#<?= $row['product_id']; ?></td>
              <td><img src="<?= $row['image']; ?>" alt="Product" class="product-img"></td>
              <td><?= $row['category_gender'].' - '.$row['category_type']; ?></td>
              <td><?= $row['name_product']; ?></td>
              <td><?= strlen($row['description'])>100?substr($row['description'],0,100).'...':$row['description']; ?></td>
              <td>Rp.<?= number_format($row['price'],0,',','.'); ?></td>
              <td><?= $row['stock']; ?></td>
              <td><div class="size-container"><?php foreach(explode(',',$row['size']) as $s){echo '<span class="size-badge">'.trim($s).'</span>';} ?></div></td>
              <td>
                <div class="action-buttons">
                  <button class="btn btn-edit" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editProductModal"
                    data-id="<?= $row['product_id']; ?>"
                    data-category="<?= $row['category_id']; ?>"
                    data-name="<?= htmlspecialchars($row['name_product']); ?>"
                    data-description="<?= htmlspecialchars($row['description']); ?>"
                    data-price="<?= $row['price']; ?>"
                    data-stock="<?= $row['stock']; ?>"
                    data-size="<?= htmlspecialchars($row['size']); ?>"
                    data-image="<?= htmlspecialchars($row['image']); ?>"
                  >Edit</button>
                  <a href="products.php?delete=<?= $row['product_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="btn btn-delete">Delete</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Add Product -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content p-4 rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_product" value="1">
            <div class="mb-3">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <option value="7001">Men - Tops</option><option value="7002">Men - Outwear</option><option value="7003">Men - Bottom</option><option value="7004">Men - Accessories</option><option value="7005">Women - Tops</option><option value="7006">Women - Outwear</option><option value="7007">Women - Bottom</option><option value="7008">Women - Accessories</option>
              </select>
            </div>
            <div class="mb-3"><label class="form-label">Product Name</label><input type="text" name="name_product" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <div class="row">
              <div class="col-md-4 mb-3"><label class="form-label">Stock</label><input type="number" name="stock" class="form-control" required></div>
              <div class="col-md-4 mb-3"><label class="form-label">Price (IDR)</label><input type="number" name="price" class="form-control" required></div>
              <div class="col-md-4 mb-3"><label class="form-label">Available Sizes</label><input type="text" name="size" class="form-control" placeholder="e.g. XS, S, M, L, XL, XXL"></div>
            </div>
            <div class="mb-4"><label class="form-label">Product Image</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
            <button type="submit" class="btn btn-dark w-100">Add Product</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Product -->
  <div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content p-4 rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_product" value="1">
            <input type="hidden" name="product_id" id="edit_product_id">
            <input type="hidden" name="existing_image" id="existing_image">

            <div class="mb-3">
              <label class="form-label">Category</label>
              <select name="category_id" id="edit_category_id" class="form-select" required>
                <option value="7001">Men - Tops</option><option value="7002">Men - Outwear</option><option value="7003">Men - Bottom</option><option value="7004">Men - Accessories</option><option value="7005">Women - Tops</option><option value="7006">Women - Outwear</option><option value="7007">Women - Bottom</option><option value="7008">Women - Accessories</option>
              </select>
            </div>
            <div class="mb-3"><label class="form-label">Product Name</label><input type="text" name="name_product" id="edit_name_product" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="edit_description" class="form-control" rows="3"></textarea></div>
            <div class="row">
              <div class="col-md-4 mb-3"><label class="form-label">Stock</label><input type="number" name="stock" id="edit_stock" class="form-control" required></div>
              <div class="col-md-4 mb-3"><label class="form-label">Price (IDR)</label><input type="number" name="price" id="edit_price" class="form-control" required></div>
              <div class="col-md-4 mb-3"><label class="form-label">Available Sizes</label><input type="text" name="size" id="edit_size" class="form-control"></div>
            </div>
            <div class="mb-4"><label class="form-label">Product Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <button type="submit" class="btn btn-dark w-100">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    const editModal = document.getElementById('editProductModal');
    editModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      document.getElementById('edit_product_id').value = button.getAttribute('data-id');
      document.getElementById('edit_category_id').value = button.getAttribute('data-category');
      document.getElementById('edit_name_product').value = button.getAttribute('data-name');
      document.getElementById('edit_description').value = button.getAttribute('data-description');
      document.getElementById('edit_price').value = button.getAttribute('data-price');
      document.getElementById('edit_stock').value = button.getAttribute('data-stock');
      document.getElementById('edit_size').value = button.getAttribute('data-size');
      document.getElementById('existing_image').value = button.getAttribute('data-image');
    });
  </script>
</body>
</html>
