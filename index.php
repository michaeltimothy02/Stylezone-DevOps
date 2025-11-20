<?php
session_start();

include "./config/connection.php";

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: ./admin_web/dashboard.php");
    exit();
}

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>STYLEZONE - FASHION</title>

    <!-- BOOTSTRAP & ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="./style/style.css">
    <style>

    </style>
</head>

<body>
    <!-- 🧭 NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top glass-navbar">
        <div class="container">
            <a class="navbar-brand fs-4" href="#">STYLEZONE</a>

            <!-- MOBILE TOGGLE -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="#shop">SHOP</a></li>
                    <li class="nav-item"><a class="nav-link" href="#new_arrival">NEW ARRIVAL</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">ABOUT</a></li>
                </ul>

                <!-- RIGHT ICONS -->
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['role'])): ?>
                        <!-- Cart Icon hanya muncul jika sudah login -->
                        <a href="#" id="openCart" class="icon-btn cart-icon-wrapper">
                            <i class="bi bi-cart3"></i>
                            <span class="cart-badge" id="cartCount">0</span>
                        </a>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['role'])): ?>
                        <button type="button" class="btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">LOGIN</button>
                    <?php else: ?>
                        <!-- Profile Dropdown -->
                        <div class="profile-dropdown">
                            <button class="profile-btn" id="profileDropdown">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="profile.php" class="dropdown-item">
                                    <i class="bi bi-person"></i> MY PROFILE
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item text-danger" onclick="logout()">
                                    <i class="bi bi-box-arrow-right me-2"></i> LOG OUT
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    </nav>

    <!-- 🪟 LOGIN MODAL -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="loginModalLabel">SIGN IN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form action="./config/login.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label" for="emailInput">Email Address</label>
                            <input type="email" name="email" id="emailInput" class="form-control" placeholder="Enter your email" required />
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="passwordInput">Password</label>
                            <input type="password" name="password" id="passwordInput" class="form-control"
                                placeholder="Enter your password" required />
                        </div>

                        <button type="submit" class="btn btn-dark w-100 mb-3">Sign In</button>

                        <div class="text-center">
                            <p>Does not have an account?
                                <a href="#"
                                    data-bs-toggle="modal"
                                    data-bs-target="#registerModal"
                                    data-bs-dismiss="modal">
                                    Register
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 🪟 REGISTER MODAL -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="registerModalLabel">SIGN UP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form action="./config/register.php" method="POST">
                        <!-- Name -->
                        <div class="mb-4">
                            <label class="form-label" for="nameInput">Full Name</label>
                            <input type="text" name="name" id="nameInput" class="form-control" placeholder="Enter your name" required />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label" for="emailRegisterInput">Email Address</label>
                            <input type="email" name="email" id="emailRegisterInput" class="form-control" placeholder="Enter your email" required />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label" for="passwordRegisterInput">Password</label>
                            <input type="password" name="password" id="passwordRegisterInput" class="form-control" placeholder="Create a password" required />
                        </div>

                        <button type="submit" name="add_user" class="btn btn-dark w-100 mb-3">Register</button>

                        <div class="text-center">
                            <p>Already have an account?
                                <a href="#"
                                    data-bs-toggle="modal"
                                    data-bs-target="#loginModal"
                                    data-bs-dismiss="modal">
                                    Sign In
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 🪟 PAYMENT MODAL -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">PAYMENT METHOD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="paymentForm">
                        <!-- Order Summary -->
                        <div class="order-summary mb-4 p-3 border rounded">
                            <h6 class="fw-bold mb-3">ORDER SUMMARY</h6>
                            <div id="orderSummary">
                                <!-- Order items will be loaded here -->
                            </div>
                            <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                <strong>TOTAL:</strong>
                                <strong id="orderTotal">0 IDR</strong>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">SELECT PAYMENT METHOD</h6>

                            <div class="payment-options">
                                <!-- Credit Card -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="credit_card" checked>
                                    <label class="form-check-label w-100" for="creditCard">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-credit-card me-3 fs-5"></i>
                                            <div>
                                                <strong>Credit Card</strong>
                                                <p class="mb-0 text-muted small">Pay with your credit card</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Bank Transfer -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="bank_transfer">
                                    <label class="form-check-label w-100" for="bankTransfer">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-bank me-3 fs-5"></i>
                                            <div>
                                                <strong>Bank Transfer</strong>
                                                <p class="mb-0 text-muted small">Transfer via bank</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- E-Wallet -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet" value="ewallet">
                                    <label class="form-check-label w-100" for="ewallet">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-phone me-3 fs-5"></i>
                                            <div>
                                                <strong>E-Wallet</strong>
                                                <p class="mb-0 text-muted small">Gopay, OVO, Dana, etc.</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- COD -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="cod">
                                    <label class="form-check-label w-100" for="cod">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-truck me-3 fs-5"></i>
                                            <div>
                                                <strong>Cash on Delivery</strong>
                                                <p class="mb-0 text-muted small">Pay when you receive the item</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details (akan muncul berdasarkan metode yang dipilih) -->
                        <div id="paymentDetails">
                            <!-- Detail pembayaran akan dimuat di sini -->
                        </div>

                        <button type="submit" class="btn btn-dark w-100 mb-3" id="confirmPaymentBtn">
                            CONFIRM PAYMENT
                        </button>

                        <div class="text-center">
                            <p class="small text-muted">
                                By confirming your payment, you agree to our <a href="#">Terms of Service</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 🛒 CART SIDEBAR -->
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
            <button class="btn-checkout" id="checkoutBtn">CHECKOUT</button>
        </div>
    </div>

    <!-- Cart Overlay -->
    <div class="cart-overlay" id="cartOverlay"></div>

    <!-- 🦸 HERO SECTION -->
    <div class="hero">
        <img src="https://im.uniqlo.com/global-cms/spa/rese48f58ea629bce2832eda95c7353e7d1fr.jpg"
            class="img-fluid w-100 position-absolute top-0 start-0"
            style="z-index:-1; height:100%; object-fit:cover;" />
        <div class="text-center">
            <h1>DISCOVER YOUR STYLE</h1>
            <p class="lead mb-4 text-uppercase">TRENDY FASHION & ACCESSORIES WITH EXCLUSIVE DISCOUNTS</p>
            <a href="#shop" class="btn-shop-now">SHOP NOW</a>
        </div>
    </div>

    <!-- New Arrival Carousel -->
    <div class="container py-5" id="new_arrival">
        <h2 class="section-title">New Arrival</h2>
        <div id="newArrivalCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="row g-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card">
                                <img src="https://static.zara.net/assets/public/9936/361a/6b324642b558/5c0c8bd7650a/01165360800-p/01165360800-p.jpg?ts=1759934185900&w=375"
                                    class="card-img-top" alt="PRODUCT 1">
                                <div class="card-body p-0 mt-2">
                                    <p class="product-name">JAKET REVERSIBEL DUA SISI BULU IMITASI</p>
                                    <p class="product-price">759.900 IDR</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card">
                                <img src="https://static.zara.net/assets/public/abb5/33e7/65374e4cbc42/74d9113df97d/09076217099-p/09076217099-p.jpg?ts=1759934199035&w=375"
                                    class="card-img-top" alt="PRODUCT 2">
                                <div class="card-body p-0 mt-2">
                                    <p class="product-name">JAKET MOTIF HEWAN</p>
                                    <p class="product-price">759.900 IDR</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card">
                                <img src="https://static.zara.net/assets/public/c694/790f/21144d54b2dd/ef2c53d307f8/06147160407-a1/06147160407-a1.jpg?ts=1758285815012&w=375"
                                    class="card-img-top" alt="PRODUCT 3">
                                <div class="card-body p-0 mt-2">
                                    <p class="product-name">JEANS Z1975 RELAXED SLIM LOW RISE</p>
                                    <p class="product-price">759.900 IDR</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card">
                                <img src="https://static.zara.net/assets/public/ea81/9987/d4ce498d8705/e1d52795ee7a/04087374412-a5/04087374412-a5.jpg?ts=1758882849951&w=472"
                                    class="card-img-top" alt="PRODUCT 4">
                                <div class="card-body p-0 mt-2">
                                    <p class="product-name">KAUS PATCHES CHAMPION ® X ZARA</p>
                                    <p class="product-price">499.900 IDR</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card">
                                <img src="https://static.zara.net/assets/public/2173/6908/3ad942618d27/bbc24ed40194/06224356700-a1/06224356700-a1.jpg?ts=1758730469721&w=472"
                                    class="card-img-top" alt="PRODUCT 5">
                                <div class="card-body p-0 mt-2">
                                    <p class="product-name">T-SHIRT GRAFIS HARRY LAMBERT FOR ZARA X DISNEY</p>
                                    <p class="product-price">699.900 IDR</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="card product-card">
                                <img src="https://static.zara.net/assets/public/5392/c7ee/913e4cc69205/e6c6e3bb121b/05955549104-a4/05955549104-a4.jpg?ts=1757002343339&w=472"
                                    class="card-img-top" alt="PRODUCT 6">
                                <div class="card-body p-0 mt-2">
                                    <p class="product-name">CELANA PANJANG SETELAN GARIS</p>
                                    <p class="product-price">899.900 IDR</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#newArrivalCarousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#newArrivalCarousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <div class="container text-center my-3">
        <!-- Gender Tabs -->
        <ul class="nav justify-content-center category-gender mb-3" id="genderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="category-btn active" id="women-tab" data-bs-toggle="tab" data-bs-target="#women"
                    type="button" role="tab" aria-controls="women" aria-selected="true" data-gender="Women">FOR WOMEN</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="category-btn" id="men-tab" data-bs-toggle="tab" data-bs-target="#men" type="button"
                    role="tab" aria-controls="men" aria-selected="false" data-gender="Men">FOR MEN</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab WOMEN -->
            <div class="tab-pane fade show active" id="women" role="tabpanel" aria-labelledby="women-tab">
                <ul class="nav justify-content-center product-category-tabs" data-gender="Women">
                    <!-- Tombol ALL -->
                    <li class="nav-item"><a class="product-tab active" href="#" data-category="all">ALL</a></li>

                    <?php
                    $query = "SELECT * FROM categories WHERE category_gender='Women'";
                    $result_categories = $conn->query($query);

                    if ($result_categories->num_rows > 0) {
                        while ($row = $result_categories->fetch_assoc()) {
                            echo '<li class="nav-item"><a class="product-tab" href="#" data-category="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['category_type']) . '</a></li>';
                        }
                    } else {
                        echo '<li class="nav-item text-muted">No categories found</li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- Tab MEN -->
            <div class="tab-pane fade" id="men" role="tabpanel" aria-labelledby="men-tab">
                <ul class="nav justify-content-center product-category-tabs" data-gender="Men">
                    <!-- Tombol ALL -->
                    <li class="nav-item"><a class="product-tab active" href="#" data-category="all">ALL</a></li>

                    <?php
                    $query = "SELECT * FROM categories WHERE category_gender='Men'";
                    $result_categories = $conn->query($query);

                    if ($result_categories->num_rows > 0) {
                        while ($row = $result_categories->fetch_assoc()) {
                            echo '<li class="nav-item"><a class="product-tab" href="#" data-category="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['category_type']) . '</a></li>';
                        }
                    } else {
                        echo '<li class="nav-item text-muted">No categories found</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- 🛒 PRODUCT SECTION -->
    <div class="container py-5" id="shop">
        <div class="row g-4" id="product-container">
            <!-- Produk akan diload via AJAX -->
        </div>
    </div>

    <!-- 🦶 FOOTER -->
    <footer class="bg-dark text-light pt-5 pb-3 text-uppercase" id="about">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>ABOUT US</h5>
                    <p class="text-muted">YOUR ONE-STOP SHOP FOR TRENDY FASHION, ELECTRONICS, AND MORE.</p>
                </div>
                <div class="col-md-2 mb-3">
                    <h6>SHOP</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">MEN</a></li>
                        <li><a href="#">WOMEN</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-3">
                    <h6>HELP</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">CONTACT</a></li>
                        <li><a href="#">SHIPPING</a></li>
                        <li><a href="#">RETURNS</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>FOLLOW US</h6>
                    <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-twitter"></i></a>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0">&copy; 2025 STYLEZONE. ALL RIGHTS RESERVED.</p>
            </div>
        </div>
    </footer>

    <!-- 📜 SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // NAVBAR SCROLL EFFECT
        window.addEventListener("scroll", function() {
            const navbar = document.querySelector(".navbar");
            navbar.classList.toggle("scrolled", window.scrollY > 50);
        });

        // Profile Dropdown Functionality
        const profileDropdown = document.getElementById("profileDropdown");
        const dropdownMenu = document.getElementById("dropdownMenu");

        if (profileDropdown && dropdownMenu) {
            profileDropdown.addEventListener("click", function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle("show");
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function() {
                dropdownMenu.classList.remove("show");
            });

            // Prevent dropdown from closing when clicking inside it
            dropdownMenu.addEventListener("click", function(e) {
                e.stopPropagation();
            });
        }

        function logout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = './logout.php';
            }
        }

        // Cart Management Functions
        function getCart() {
            try {
                return JSON.parse(localStorage.getItem('cart')) || [];
            } catch (error) {
                console.error('Error reading cart from localStorage:', error);
                return [];
            }
        }

        function saveCart(cart) {
            try {
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartUI();
            } catch (error) {
                console.error('Error saving cart to localStorage:', error);
            }
        }

        function updateCartUI() {
            try {
                const cart = getCart();
                const cartCount = document.getElementById('cartCount');
                const cartItems = document.getElementById('cartItems');
                const cartTotal = document.getElementById('cartTotal');

                if (!cartCount || !cartItems || !cartTotal) {
                    console.error('Cart elements not found');
                    return;
                }

                // Update cart count
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = totalItems;

                // Show/hide badge based on cart count
                if (totalItems > 0) {
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.style.display = 'none';
                }

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
                            <img src="${item.image}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/60x80?text=Image'">
                            <div class="item-details">
                                <p class="item-name">${item.name}</p>
                                <p class="item-price">Rp ${item.price.toLocaleString('id-ID')}</p>
                                <p class="item-quantity">Size: ${item.size} | Qty: ${item.quantity}</p>
                            </div>
                            <button class="remove-item" data-index="${index}">&times;</button>
                        `;
                        cartItems.appendChild(cartItem);
                    });

                    // Add event listeners for remove buttons
                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', function() {
                            const index = parseInt(this.getAttribute('data-index'));
                            removeFromCart(index);
                        });
                    });
                }

                // Update total
                cartTotal.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;

            } catch (error) {
                console.error('Error updating cart UI:', error);
            }
        }

        function removeFromCart(index) {
            let cart = getCart();
            if (index >= 0 && index < cart.length) {
                cart.splice(index, 1);
                saveCart(cart);
            }
        }

        function addToCart(item) {
            try {
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
            } catch (error) {
                console.error('Error adding to cart:', error);
                return false;
            }
        }

        // Cart Sidebar Functions
        function openCart() {
            try {
                const cartSidebar = document.getElementById('cartSidebar');
                const cartOverlay = document.getElementById('cartOverlay');

                if (cartSidebar && cartOverlay) {
                    cartSidebar.classList.add('active');
                    cartOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            } catch (error) {
                console.error('Error opening cart:', error);
            }
        }

        function closeCart() {
            try {
                const cartSidebar = document.getElementById('cartSidebar');
                const cartOverlay = document.getElementById('cartOverlay');

                if (cartSidebar && cartOverlay) {
                    cartSidebar.classList.remove('active');
                    cartOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            } catch (error) {
                console.error('Error closing cart:', error);
            }
        }

        // Initialize Cart Functionality
        function initCart() {
            try {
                // Event Listeners for Cart
                const openCartBtn = document.getElementById('openCart');
                const closeCartBtn = document.querySelector('.close-cart');
                const cartOverlay = document.getElementById('cartOverlay');
                const checkoutBtn = document.getElementById('checkoutBtn');

                if (openCartBtn) {
                    openCartBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        openCart();
                    });
                }

                if (closeCartBtn) {
                    closeCartBtn.addEventListener('click', closeCart);
                }

                if (cartOverlay) {
                    cartOverlay.addEventListener('click', closeCart);
                }

                if (checkoutBtn) {
                    checkoutBtn.addEventListener('click', () => {
                        const cart = getCart();
                        if (cart.length === 0) {
                            alert('Your cart is empty!');
                            return;
                        }
                    });
                }

                // Initial UI update
                updateCartUI();

                console.log('Cart initialized successfully');

            } catch (error) {
                console.error('Error initializing cart:', error);
            }
        }

        // Quick Add to Cart functionality
        function initQuickAddToCart() {
            document.addEventListener('click', function(e) {
                // Quick add to cart button
                if (e.target.classList.contains('quick-add-cart')) {
                    const button = e.target;
                    const hasSizes = button.hasAttribute('data-has-sizes') && button.getAttribute('data-has-sizes') === 'true';

                    if (!hasSizes) {
                        // Add directly to cart if no sizes
                        const item = {
                            product_id: button.getAttribute('data-product-id'),
                            name: button.getAttribute('data-product-name'),
                            price: parseFloat(button.getAttribute('data-product-price')),
                            image: button.getAttribute('data-product-image'),
                            stock: parseInt(button.getAttribute('data-product-stock')),
                            size: 'One Size',
                            quantity: 1
                        };

                        if (addToCart(item)) {
                            alert(`Added ${item.name} to cart!`);
                            openCart();
                        }
                    } else {
                        // Show size options
                        const sizeOptions = button.nextElementSibling;
                        if (sizeOptions && sizeOptions.classList.contains('size-options')) {
                            sizeOptions.style.display = 'block';
                            button.style.display = 'none';
                        }
                    }
                }

                // Size selection
                if (e.target.classList.contains('size-option')) {
                    const sizeBtn = e.target;
                    const cardBody = sizeBtn.closest('.card-body');
                    const quickAddBtn = cardBody.querySelector('.quick-add-cart');
                    const sizeOptions = cardBody.querySelector('.size-options');

                    const item = {
                        product_id: quickAddBtn.getAttribute('data-product-id'),
                        name: quickAddBtn.getAttribute('data-product-name'),
                        price: parseFloat(quickAddBtn.getAttribute('data-product-price')),
                        image: quickAddBtn.getAttribute('data-product-image'),
                        stock: parseInt(quickAddBtn.getAttribute('data-product-stock')),
                        size: sizeBtn.getAttribute('data-size'),
                        quantity: 1
                    };

                    if (addToCart(item)) {
                        alert(`Added ${item.name} (${item.size}) to cart!`);
                        openCart();

                        // Reset UI
                        sizeOptions.style.display = 'none';
                        quickAddBtn.style.display = 'block';
                    }
                }
            });
        }

        // Make functions globally available for product detail pages
        window.getCart = getCart;
        window.saveCart = saveCart;
        window.updateCartUI = updateCartUI;
        window.removeFromCart = removeFromCart;
        window.addToCart = addToCart;
        window.openCart = openCart;
        window.closeCart = closeCart;

        // Product Loading Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const productContainer = document.getElementById('product-container');
            let currentGender = 'Women';
            let currentCategory = 'all';

            // Fungsi untuk load produk via AJAX
            function loadProducts(gender, category) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'load_products.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (this.status === 200) {
                        productContainer.innerHTML = this.responseText;
                        // Re-initialize quick add to cart after loading products
                        initQuickAddToCart();
                    } else {
                        productContainer.innerHTML = '<div class="col-12 text-center"><p>Error loading products</p></div>';
                    }
                };

                xhr.onerror = function() {
                    productContainer.innerHTML = '<div class="col-12 text-center"><p>Connection error</p></div>';
                };

                xhr.send('gender=' + encodeURIComponent(gender) + '&category=' + encodeURIComponent(category));
            }

            // Event listener untuk tab gender (Bootstrap tab events)
            const genderTabs = document.querySelectorAll('#genderTabs [data-bs-toggle="tab"]');

            genderTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    currentGender = this.getAttribute('data-gender');
                    currentCategory = 'all'; // Reset ke ALL ketika ganti gender

                    // Update active state untuk kategori tabs
                    const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
                    const categoryTabs = targetPane.querySelectorAll('.product-tab');
                    categoryTabs.forEach(tab => tab.classList.remove('active'));
                    targetPane.querySelector('[data-category="all"]').classList.add('active');

                    // Load produk
                    loadProducts(currentGender, currentCategory);
                });
            });

            // Event listener untuk tab kategori produk
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('product-tab')) {
                    e.preventDefault();

                    const tab = e.target;
                    const tabGroup = tab.closest('.product-category-tabs');
                    const gender = tabGroup.getAttribute('data-gender');

                    // Update active tab dalam group yang sama
                    tabGroup.querySelectorAll('.product-tab').forEach(t => {
                        t.classList.remove('active');
                    });
                    tab.classList.add('active');

                    currentGender = gender;
                    currentCategory = tab.getAttribute('data-category');

                    // Load produk
                    loadProducts(currentGender, currentCategory);
                }
            });

            // Initialize cart and load initial products
            initCart();
            initQuickAddToCart();
            loadProducts(currentGender, currentCategory);
        });

        // Ganti event listener checkoutBtn yang lama dengan ini
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => {
                const cart = getCart();
                if (cart.length === 0) {
                    alert('Your cart is empty!');
                    return;
                }

                // Close cart sidebar
                closeCart();

                // Show payment modal
                showPaymentModal(cart);
            });
        }

        // Fungsi untuk menampilkan modal pembayaran
        function showPaymentModal(cart) {
            try {
                const orderSummary = document.getElementById('orderSummary');
                const orderTotal = document.getElementById('orderTotal');
                const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

                // Update order summary
                let totalPrice = 0;
                orderSummary.innerHTML = '';

                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    totalPrice += itemTotal;

                    const orderItem = document.createElement('div');
                    orderItem.className = 'order-item';
                    orderItem.innerHTML = `
                <div class="order-item-name">
                    ${item.name} (${item.size}) × ${item.quantity}
                </div>
                <div class="order-item-price">
                    Rp ${itemTotal.toLocaleString('id-ID')}
                </div>
            `;
                    orderSummary.appendChild(orderItem);
                });

                // Update total
                orderTotal.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;

                // Load initial payment details
                loadPaymentDetails('credit_card');

                // Show modal
                paymentModal.show();

            } catch (error) {
                console.error('Error showing payment modal:', error);
                alert('Error loading payment page. Please try again.');
            }
        }

        // Fungsi untuk memuat detail pembayaran berdasarkan metode
        function loadPaymentDetails(paymentMethod) {
            const paymentDetails = document.getElementById('paymentDetails');

            switch (paymentMethod) {
                case 'credit_card':
                    paymentDetails.innerHTML = `
                <div class="payment-detail-section">
                    <h6>CREDIT CARD INFORMATION</h6>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="cardNumber" placeholder="Card Number" required>
                        <label for="cardNumber">Card Number</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY" required>
                                <label for="expiryDate">Expiry Date (MM/YY)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="cvv" placeholder="CVV" required>
                                <label for="cvv">CVV</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="cardHolder" placeholder="Card Holder Name" required>
                        <label for="cardHolder">Card Holder Name</label>
                    </div>
                </div>
            `;
                    break;

                case 'bank_transfer':
                    paymentDetails.innerHTML = `
                <div class="payment-detail-section">
                    <h6>BANK TRANSFER</h6>
                    <p class="text-muted mb-3">Transfer to one of our bank accounts below:</p>
                    
                    <div class="bank-option" onclick="selectBankOption(this)">
                        <strong>BCA</strong>
                        <p class="mb-1">Account: 1234567890</p>
                        <p class="mb-0">A/N: STYLEZONE STORE</p>
                    </div>
                    
                    <div class="bank-option" onclick="selectBankOption(this)">
                        <strong>Mandiri</strong>
                        <p class="mb-1">Account: 0987654321</p>
                        <p class="mb-0">A/N: STYLEZONE STORE</p>
                    </div>
                    
                    <div class="bank-option" onclick="selectBankOption(this)">
                        <strong>BNI</strong>
                        <p class="mb-1">Account: 1122334455</p>
                        <p class="mb-0">A/N: STYLEZONE STORE</p>
                    </div>
                    
                    <div class="mt-3">
                        <p class="small text-muted">
                            <strong>Important:</strong> Please include your order ID in the transfer description.
                        </p>
                    </div>
                </div>
            `;
                    break;

                case 'ewallet':
                    paymentDetails.innerHTML = `
                <div class="payment-detail-section">
                    <h6>E-WALLET</h6>
                    <p class="text-muted mb-3">Select your e-wallet provider:</p>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="ewallet-option" onclick="selectEwalletOption(this)">
                                <div>Gopay</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ewallet-option" onclick="selectEwalletOption(this)">
                                <div>OVO</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ewallet-option" onclick="selectEwalletOption(this)">
                                <div>Dana</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ewallet-option" onclick="selectEwalletOption(this)">
                                <div>LinkAja</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    break;

                case 'cod':
                    paymentDetails.innerHTML = `
                <div class="payment-detail-section">
                    <h6>CASH ON DELIVERY</h6>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        You will pay with cash when you receive your order. 
                        Please prepare exact amount for faster transaction.
                    </div>
                    <p class="text-muted">
                        Our delivery partner will contact you before delivery to confirm.
                    </p>
                </div>
            `;
                    break;
            }
        }

        // Fungsi untuk memilih opsi bank
        function selectBankOption(element) {
            document.querySelectorAll('.bank-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            element.classList.add('selected');
        }

        // Fungsi untuk memilih opsi e-wallet
        function selectEwalletOption(element) {
            document.querySelectorAll('.ewallet-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            element.classList.add('selected');
        }

        // Event listener untuk perubahan metode pembayaran
        document.addEventListener('change', function(e) {
            if (e.target.name === 'paymentMethod') {
                loadPaymentDetails(e.target.value);
            }
        });

        // Event listener untuk form pembayaran
        document.addEventListener('submit', function(e) {
            if (e.target.id === 'paymentForm') {
                e.preventDefault();
                processPayment();
            }
        });

        // Fungsi untuk memproses pembayaran - VERSI FIXED
        async function processPayment() {
            console.log('Starting payment process...');

            const cart = getCart();
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            console.log('Payment method:', paymentMethod);

            // Validasi form
            if (!validatePaymentForm(paymentMethod)) {
                return;
            }

            // Siapkan data
            const paymentData = {
                cart_items: cart.map(item => ({
                    product_id: parseInt(item.product_id),
                    quantity: parseInt(item.quantity),
                    price: parseFloat(item.price),
                    name: item.name,
                    size: item.size
                })),
                total_amount: cart.reduce((total, item) => total + (item.price * item.quantity), 0),
                payment_method: paymentMethod
            };

            console.log('Payment data to send:', paymentData);

            // Tampilkan loading
            const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
            const originalText = confirmPaymentBtn.innerHTML;
            confirmPaymentBtn.disabled = true;
            confirmPaymentBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            try {
                const response = await fetch('./config/process_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(paymentData)
                });

                const result = await response.json();
                console.log('Payment response:', result);

                if (result.success) {
                    // SUCCESS
                    saveCart([]); // Clear cart
                    updateCartUI();

                    // Close modal
                    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    paymentModal.hide();

                    // Show success message
                    showSuccessMessage(result.order_id, result.payment_id);

                } else if (result.message === 'SESSION_EXPIRED') {
                    // Handle session expired specifically
                    alert('Your session has expired. Please login again.');
                    window.location.reload();

                } else {
                    throw new Error(result.message);
                }

            } catch (error) {
                console.error('Payment error:', error);
                alert('Payment failed: ' + error.message);
            } finally {
                // Reset button
                confirmPaymentBtn.disabled = false;
                confirmPaymentBtn.innerHTML = originalText;
            }
        }

        // Fungsi validasi form
        function validatePaymentForm(paymentMethod) {
            switch (paymentMethod) {
                case 'credit_card':
                    const cardNumber = document.getElementById('cardNumber')?.value.trim();
                    const expiryDate = document.getElementById('expiryDate')?.value.trim();
                    const cvv = document.getElementById('cvv')?.value.trim();
                    const cardHolder = document.getElementById('cardHolder')?.value.trim();

                    if (!cardNumber || !expiryDate || !cvv || !cardHolder) {
                        alert('Please fill all credit card fields');
                        return false;
                    }
                    break;

                case 'bank_transfer':
                    if (!document.querySelector('.bank-option.selected')) {
                        alert('Please select a bank');
                        return false;
                    }
                    break;

                case 'ewallet':
                    if (!document.querySelector('.ewallet-option.selected')) {
                        alert('Please select an e-wallet');
                        return false;
                    }
                    break;
            }
            return true;
        }

        // Fungsi tampilkan success message
        function showSuccessMessage(orderId, paymentId) {
            // Anda bisa menggunakan alert sederhana atau modal Bootstrap
            alert(`Payment Successful!\n\nOrder ID: ${orderId}\nPayment ID: ${paymentId}\n\nThank you for your order!`);

            // Atau gunakan modal Bootstrap yang lebih bagus
            /*
            const successHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Payment Successful!</h4>
                    <p>Thank you for your order. Your order has been placed successfully.</p>
                    <hr>
                    <p class="mb-0">
                        <strong>Order ID:</strong> ${orderId}<br>
                        <strong>Payment ID:</strong> ${paymentId}
                    </p>
                </div>
            `;
            document.body.insertAdjacentHTML('afterbegin', successHTML);
            */
        }
    </script>
</body>

</html>