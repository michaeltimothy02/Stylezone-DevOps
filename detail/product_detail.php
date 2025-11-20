<?php
session_start();
include "../config/connection.php";

// Get product_id from URL parameter
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';

if (!$product_id) {
    die("Product ID is required");
}

// Query to get product details
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found");
}

// Parse size data (assuming format: "XS, S, M, L, XL, XXL")
$sizes = [];
if (!empty($product['size'])) {
    $sizes = array_map('trim', explode(',', $product['size']));
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($product['name_product']) ?> | Stylezone</title>
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
  <style>
    /* Global Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Didot", serif;
    }
    
    body {
      background: #fff;
      color: #111;
      line-height: 1.6;
    }
    
    /* Header & Navigation */
    .header {
      border-bottom: 1px solid #eee;
      padding: 15px 0;
    }
    
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .logo {
      font-weight: bold;
      font-size: 24px;
      letter-spacing: 1px;
      text-decoration: none;
      color: #111;
    }

    /* Cart Icon Styles */
    .icon-btn {
      color: #111;
      text-decoration: none;
      font-size: 18px;
      transition: color 0.3s;
    }

    .icon-btn:hover {
      color: #666;
    }

    /* Cart Sidebar Styles */
    .cart-sidebar {
      position: fixed;
      top: 0;
      right: -400px;
      width: 380px;
      height: 100vh;
      background: white;
      box-shadow: -2px 0 10px rgba(0,0,0,0.1);
      transition: right 0.3s ease;
      z-index: 1050;
      display: flex;
      flex-direction: column;
    }

    .cart-sidebar.active {
      right: 0;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 1px solid #eee;
    }

    .close-cart {
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #111;
    }

    .cart-items {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
    }

    .cart-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px 0;
      border-bottom: 1px solid #eee;
    }

    .cart-item img {
      width: 60px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
    }

    .item-details {
      flex: 1;
    }

    .item-name {
      font-weight: bold;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .item-price {
      color: #666;
      font-size: 14px;
    }

    .item-quantity {
      color: #666;
      font-size: 12px;
    }

    .remove-item {
      background: none;
      border: none;
      color: #999;
      cursor: pointer;
      font-size: 18px;
      padding: 5px;
    }

    .remove-item:hover {
      color: #ff4444;
    }

    .cart-footer {
      padding: 20px;
      border-top: 1px solid #eee;
    }

    .total {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      margin-bottom: 15px;
      font-size: 16px;
    }

    /* .btn-checkout {
      background: #111;
      color: white;
      border: none;
      padding: 12px;
      width: 100%;
      cursor: pointer;
      transition: background 0.3s;
    } */

    /* .btn-checkout:hover {
      background: #333;
    } */

    .cart-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1049;
      display: none;
    }

    .cart-overlay.active {
      display: block;
    }
    
    /* Product Page Layout */
    .product-page {
      display: flex;
      gap: 60px;
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
    }
    
    /* Product Gallery */
    .product-gallery {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .main-image {
      width: 100%;
      max-width: 500px;
      border-radius: 4px;
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    
    .main-image:hover {
      transform: scale(1.01);
    }
    
    .thumbnail-list {
      display: flex;
      gap: 15px;
      margin-top: 20px;
      justify-content: center;
    }
    
    .thumbnail-list img {
      width: 80px;
      height: 90px;
      object-fit: cover;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .thumbnail-list img:hover {
      border-color: #000;
    }
    
    .thumbnail-list img.active {
      border: 2px solid #000;
    }
    
    /* Product Info */
    .product-info {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .product-title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 15px;
    }
    
    .product-desc {
      font-size: 15px;
      color: #555;
      margin-bottom: 20px;
      text-align: justify;
    }
    
    .price-stock {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 25px;
      font-size: 16px;
    }
    
    .price {
      font-weight: bold;
      font-size: 20px;
      color: #000;
    }
    
    .stock {
      color: #333;
      font-size: 14px;
    }
    
    /* Size Selection */
    .size-section {
      margin-bottom: 25px;
    }
    
    .size-label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }
    
    .size-options {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .size-btn {
      border: 1px solid #ddd;
      background: #fff;
      padding: 10px 18px;
      cursor: pointer;
      transition: all 0.3s;
      font-size: 14px;
    }
    
    .size-btn:hover {
      border-color: #000;
    }
    
    .size-btn.active {
      background: #000;
      color: #fff;
      border-color: #000;
    }
    
    .size-btn:disabled {
      background: #f5f5f5;
      color: #999;
      cursor: not-allowed;
      border-color: #ddd;
    }
    
    /* Quantity Control */
    .quantity-section {
      margin-bottom: 25px;
    }
    
    .quantity-label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }
    
    .quantity-control {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .quantity-btn {
      width: 35px;
      height: 35px;
      border: 1px solid #ddd;
      background: #fff;
      font-size: 18px;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .quantity-btn:hover {
      background: #f5f5f5;
    }
    
    .quantity-btn:disabled {
      background: #f5f5f5;
      color: #999;
      cursor: not-allowed;
    }
    
    .quantity-display {
      width: 40px;
      text-align: center;
      font-size: 16px;
    }
    
    /* Add to Cart Button */
    .cart-btn {
      background: #111;
      color: #fff;
      border: none;
      padding: 15px;
      width: 100%;
      max-width: 300px;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }
    
    .cart-btn:hover {
      background: #333;
    }
    
    .cart-btn:disabled {
      background: #999;
      cursor: not-allowed;
    }
    
    /* Modal for Image Zoom */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
    }
    
    .modal-content {
      max-width: 80%;
      max-height: 80%;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .close-btn {
      position: absolute;
      top: 30px;
      right: 50px;
      color: #fff;
      font-size: 35px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.2s;
    }
    
    .close-btn:hover {
      color: #ccc;
    }
    
    /* Responsive Design */
    @media (max-width: 900px) {
      .product-page {
        flex-direction: column;
        gap: 30px;
        margin: 30px auto;
      }
      
      .navbar {
        flex-direction: column;
        gap: 15px;
      }

      .cart-sidebar {
        width: 100%;
        right: -100%;
      }
    }
    
    @media (max-width: 600px) {
      .thumbnail-list img {
        width: 60px;
        height: 70px;
      }
      
      .size-btn {
        padding: 8px 12px;
      }
    }
  </style>
</head>
<body>
  <!-- Cart Overlay -->
  <div class="cart-overlay" id="cartOverlay"></div>

  <!-- Cart Sidebar -->
  <div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
      <h4>YOUR CART</h4>
      <button class="close-cart">&times;</button>
    </div>

    <div class="cart-items" id="cartItems">
      <!-- Cart items will be loaded here -->
    </div>

    <div class="cart-footer">
      <p class="total">TOTAL: <span id="cartTotal">0 IDR</span></p>
      <!-- <button class="btn-checkout" id="checkoutBtn">CHECKOUT</button> -->
    </div>
  </div>

  <header class="header">
    <nav class="navbar">
      <a href="../index.php" class="logo">STYLEZONE</a>
      <div class="d-flex align-items-center gap-3">
        <a href="#" id="openCart" class="icon-btn">
          <i class="bi bi-cart3"></i>
          <span id="cartCount" style="font-size: 12px; margin-left: 2px;">0</span>
        </a>
      </div>
    </nav>
  </header>

  <main class="product-page">
    <section class="product-gallery">
      <img id="mainImage" src="<?= htmlspecialchars($product['image']); ?>" 
           alt="<?= htmlspecialchars($product['name_product']); ?>" 
           class="main-image" />
    </section>

    <section class="product-info">
      <h2 class="product-title"><?= htmlspecialchars($product['name_product']); ?></h2>
      <p class="product-desc">
        <?= htmlspecialchars($product['description']); ?>
      </p>

      <div class="price-stock">
        <span class="price">Rp <?= number_format($product['price'], 0, ',', '.'); ?></span>
        <span class="stock">Stock: <?= htmlspecialchars($product['stock']); ?></span>
      </div>

      <?php if (!empty($sizes)): ?>
      <div class="size-section">
        <span class="size-label">Select Size</span>
        <div class="size-options" id="sizeOptions">
          <?php foreach ($sizes as $index => $size): ?>
            <button class="size-btn <?= $index === 0 ? 'active' : '' ?>" 
                    data-size="<?= htmlspecialchars(trim($size)); ?>">
              <?= htmlspecialchars(trim($size)); ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="quantity-section">
        <span class="quantity-label">Quantity</span>
        <div class="quantity-control">
          <button class="quantity-btn" id="minusBtn">-</button>
          <span class="quantity-display" id="quantity">1</span>
          <button class="quantity-btn" id="plusBtn">+</button>
        </div>
      </div>

      <button class="cart-btn" id="cartBtn">Add to Cart</button>
    </section>
  </main>

  <!-- Modal for Image Zoom -->
  <div class="modal" id="imageModal">
    <span class="close-btn" id="closeModal">&times;</span>
    <img class="modal-content" id="modalImg" alt="Zoomed Product Image">
  </div>

  <script>
    // Cart Management Functions
    function getCart() {
      return JSON.parse(localStorage.getItem('cart')) || [];
    }

    function saveCart(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartUI();
    }

    function updateCartUI() {
      const cart = getCart();
      const cartCount = document.getElementById('cartCount');
      const cartItems = document.getElementById('cartItems');
      const cartTotal = document.getElementById('cartTotal');
      
      // Update cart count
      const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
      cartCount.textContent = totalItems;
      
      // Update cart items
      cartItems.innerHTML = '';
      let totalPrice = 0;
      
      if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-center text-muted py-4">Your cart is empty</p>';
      } else {
        cart.forEach((item, index) => {
          totalPrice += item.price * item.quantity;
          
          const cartItem = document.createElement('div');
          cartItem.className = 'cart-item';
          cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="item-details">
              <p class="item-name">${item.name}</p>
              <p class="item-price">Rp ${item.price.toLocaleString('id-ID')}</p>
              <p class="item-quantity">Size: ${item.size} | Qty: ${item.quantity}</p>
            </div>
            <button class="remove-item" onclick="removeFromCart(${index})">&times;</button>
          `;
          cartItems.appendChild(cartItem);
        });
      }
      
      // Update total
      cartTotal.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
    }

    function addToCart(item) {
      let cart = getCart();
      
      // Check if item already exists in cart with same size
      const existingItemIndex = cart.findIndex(cartItem => 
        cartItem.product_id === item.product_id && cartItem.size === item.size
      );

      if (existingItemIndex > -1) {
        // Update quantity if item exists
        const newQuantity = cart[existingItemIndex].quantity + item.quantity;
        if (newQuantity > item.stock) {
          alert(`Cannot add more items. Only ${item.stock} available in stock.`);
          return false;
        }
        cart[existingItemIndex].quantity = newQuantity;
      } else {
        // Add new item to cart
        cart.push(item);
      }

      saveCart(cart);
      return true;
    }

    function removeFromCart(index) {
      let cart = getCart();
      cart.splice(index, 1);
      saveCart(cart);
    }

    // DOM Elements
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail-list img');
    const sizeButtons = document.querySelectorAll('.size-btn');
    const minusBtn = document.getElementById('minusBtn');
    const plusBtn = document.getElementById('plusBtn');
    const quantitySpan = document.getElementById('quantity');
    const cartBtn = document.getElementById('cartBtn');
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');
    const closeModal = document.getElementById('closeModal');
    const openCartBtn = document.getElementById('openCart');
    const closeCartBtn = document.querySelector('.close-cart');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    // const checkoutBtn = document.getElementById('checkoutBtn');

    // Product data from PHP
    const productData = {
      id: '<?= $product['product_id'] ?>',
      name: '<?= addslashes($product['name_product']) ?>',
      price: <?= $product['price'] ?>,
      stock: <?= $product['stock'] ?>,
      image: '<?= addslashes($product['image']) ?>'
    };

    // Thumbnail switching
    thumbnails.forEach(thumb => {
      thumb.addEventListener('click', () => {
        thumbnails.forEach(img => img.classList.remove('active'));
        thumb.classList.add('active');
        mainImage.src = thumb.src;
      });
    });

    // Size selection
    sizeButtons.forEach(button => {
      button.addEventListener('click', () => {
        sizeButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
      });
    });

    // Quantity control
    let quantity = 1;
    const maxStock = productData.stock;

    function updateQuantityControls() {
      quantitySpan.textContent = quantity;
      minusBtn.disabled = quantity <= 1;
      plusBtn.disabled = quantity >= maxStock;
      
      // Update cart button text if stock is low
      if (maxStock === 0) {
        cartBtn.textContent = 'Out of Stock';
        cartBtn.disabled = true;
      } else if (quantity > maxStock) {
        quantity = maxStock;
        quantitySpan.textContent = quantity;
      }
    }

    plusBtn.addEventListener('click', () => {
      if (quantity < maxStock) {
        quantity++;
        updateQuantityControls();
      }
    });

    minusBtn.addEventListener('click', () => {
      if (quantity > 1) {
        quantity--;
        updateQuantityControls();
      }
    });

    // Add to cart
    cartBtn.addEventListener('click', () => {
      const selectedSize = document.querySelector('.size-btn.active');
      
      if (sizeButtons.length > 0 && !selectedSize) {
        alert('Please select a size before adding to cart.');
        return;
      }

      const cartItem = {
        product_id: productData.id,
        name: productData.name,
        price: productData.price,
        size: selectedSize ? selectedSize.getAttribute('data-size') : 'One Size',
        quantity: quantity,
        image: productData.image,
        stock: productData.stock
      };

      if (addToCart(cartItem)) {
        
        // Reset quantity
        quantity = 1;
        updateQuantityControls();
        
        // Open cart sidebar
        openCart();
      }
    });

    // Cart Sidebar Functions
    function openCart() {
      cartSidebar.classList.add('active');
      cartOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeCart() {
      cartSidebar.classList.remove('active');
      cartOverlay.classList.remove('active');
      document.body.style.overflow = 'auto';
    }

    openCartBtn.addEventListener('click', (e) => {
      e.preventDefault();
      openCart();
    });

    closeCartBtn.addEventListener('click', closeCart);
    cartOverlay.addEventListener('click', closeCart);

    // Checkout button
    // checkoutBtn.addEventListener('click', () => {
    //   const cart = getCart();
    //   if (cart.length === 0) {
    //     alert('Your cart is empty!');
    //     return;
    //   }
    //   alert('Proceeding to checkout...');
    //   // Here you would redirect to checkout page
    //   // window.location.href = 'checkout.php';
    // });

    // Image modal
    mainImage.addEventListener('click', () => {
      modal.style.display = 'flex';
      modalImg.src = mainImage.src;
    });

    closeModal.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        modal.style.display = 'none';
        closeCart();
      }
    });

    // Initialize
    updateQuantityControls();
    updateCartUI();
  </script>
</body>
</html>