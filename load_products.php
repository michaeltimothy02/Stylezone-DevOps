<?php
// load_products.php
include './config/connection.php'; // Sesuaikan dengan file koneksi database Anda

if (isset($_POST['gender']) && isset($_POST['category'])) {
    $gender = $_POST['gender'];
    $category = $_POST['category'];

    // Build query berdasarkan filter
    $query = "SELECT p.*, c.category_gender, c.category_type 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              WHERE c.category_gender = ?";

    $params = [$gender];
    $types = "s";

    if ($category !== 'all') {
        $query .= " AND p.category_id = ?";
        $params[] = $category;
        $types .= "s";
    }

    $query .= " ORDER BY p.product_id DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
?>
            <div class="col-md-4 col-sm-6">
                <a href="detail/product_detail.php?product_id=<?= htmlspecialchars($row['product_id']); ?>" class="product-link" style="text-decoration: none; color: inherit;">
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($row['image']); ?>"
                            alt="<?= htmlspecialchars($row['name_product']); ?>"
                            class="img-fluid">
                        <p class="product-name"><?= htmlspecialchars($row['name_product']); ?></p>
                        <p class="product-price"><?= number_format($row['price'], 0, ',', '.'); ?> IDR</p>
                    </div>
                </a>
            </div>

            <script>
                // JavaScript untuk redirect ke halaman detail produk
                document.addEventListener('DOMContentLoaded', function() {
                    const productCards = document.querySelectorAll('.product-card[data-product-id]');

                    productCards.forEach(card => {
                        card.addEventListener('click', function() {
                            const productId = this.getAttribute('data-product-id');
                            if (productId) {
                                window.location.href = `product_detail.php?product_id=${productId}`;
                            }
                        });
                    });
                });
            </script>
<?php
        }
    } else {
        echo '<div class="col-12 text-center"><p>No products found for this category</p></div>';
    }

    $stmt->close();
} else {
    echo '<div class="col-12 text-center"><p>Invalid request</p></div>';
}

$conn->close();
?>