<?php
session_start();
include "../config/connection.php";

// Debug session
error_log("=== PAYMENT PROCESS START ===");
error_log("Session ID: " . session_id());
error_log("Session Data: " . print_r($_SESSION, true));

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// CEK SESSION USER
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    error_log("USER NOT LOGGED IN - Session data: " . print_r($_SESSION, true));

    echo json_encode([
        'success' => false,
        'message' => 'Please login to continue payment. User not logged in.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
error_log("Processing payment for user_id: " . $user_id);

// Dapatkan data payment
$input = file_get_contents('php://input');
error_log("Raw payment data: " . $input);

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}

if (!$data || !isset($data['cart_items']) || !isset($data['total_amount']) || !isset($data['payment_method'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment data structure']);
    exit;
}

// VALIDASI DATA
if (empty($data['cart_items'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// PROSES PEMBAYARAN KE DATABASE
try {
    // Mulai transaction
    mysqli_begin_transaction($conn);

    error_log("Starting database transaction for user: " . $user_id);

    // 1. INSERT KE TABEL ORDERS
    $order_date = date('Y-m-d H:i:s');
    $address = "Jl. Merdeka No.123, Jakarta";
    $shipping = "JNE Regular";
    $total = floatval($data['total_amount']);
    $status = 'pending';

    $order_query = "INSERT INTO orders (user_id, order_date, address, shipping, total, status) 
                   VALUES (?, ?, ?, ?, ?, ?)";

    error_log("Order query: " . $order_query);
    error_log("Order values: user_id=$user_id, total=$total");

    $order_stmt = mysqli_prepare($conn, $order_query);

    if (!$order_stmt) {
        throw new Exception('Failed to prepare order statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($order_stmt, 'isssds', $user_id, $order_date, $address, $shipping, $total, $status);

    if (!mysqli_stmt_execute($order_stmt)) {
        throw new Exception('Failed to create order: ' . mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);
    error_log("Order created with ID: " . $order_id);

    mysqli_stmt_close($order_stmt);

    // 2. INSERT KE TABEL ORDER_ITEMS (SESUAI STRUKTUR ANDA: order_item_id, order_id, product_id, quantity, size)
    foreach ($data['cart_items'] as $index => $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $size = isset($item['size']) ? $item['size'] : 'One Size';

        error_log("Processing item $index: product_id=$product_id, quantity=$quantity, size=$size");

        // Validasi item
        if ($product_id <= 0 || $quantity <= 0) {
            throw new Exception("Invalid item data at index $index: product_id=$product_id, quantity=$quantity");
        }

        // Cek apakah produk exists dan dapatkan harga dari database
        $check_product = mysqli_query($conn, "SELECT price FROM products WHERE product_id = $product_id");
        if (!$check_product || mysqli_num_rows($check_product) == 0) {
            throw new Exception("Product with ID $product_id not found");
        }

        $product_data = mysqli_fetch_assoc($check_product);
        $price = floatval($product_data['price']);

        $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, size) 
                            VALUES (?, ?, ?, ?)";

        error_log("Order item query: " . $order_item_query);

        $order_item_stmt = mysqli_prepare($conn, $order_item_query);

        if (!$order_item_stmt) {
            throw new Exception('Failed to prepare order item statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($order_item_stmt, 'iiis', $order_id, $product_id, $quantity, $size);

        if (!mysqli_stmt_execute($order_item_stmt)) {
            throw new Exception('Failed to insert order item: ' . mysqli_error($conn));
        }

        mysqli_stmt_close($order_item_stmt);
        error_log("Order item inserted successfully");
    }

    // 3. INSERT KE TABEL PAYMENTS (SESUAI STRUKTUR ANDA: payment_id, order_id, method, amount, payment_date, status)
    $payment_method = mysqli_real_escape_string($conn, $data['payment_method']);
    $payment_status = ($payment_method === 'cod') ? 'pending' : 'paid';
    $payment_date = $order_date;

    $payment_query = "INSERT INTO payments (order_id, method, amount, payment_date, status) 
                     VALUES (?, ?, ?, ?, ?)";

    error_log("Payment query: " . $payment_query);

    $payment_stmt = mysqli_prepare($conn, $payment_query);

    if (!$payment_stmt) {
        throw new Exception('Failed to prepare payment statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($payment_stmt, 'issss', $order_id, $payment_method, $total, $payment_date, $payment_status);

    if (!mysqli_stmt_execute($payment_stmt)) {
        throw new Exception('Failed to create payment record: ' . mysqli_error($conn));
    }

    $payment_id = mysqli_insert_id($conn);
    error_log("Payment created with ID: " . $payment_id);

    mysqli_stmt_close($payment_stmt);

    // 4. INSERT KE TABEL PAYMENT_DETAILS (SESUAI STRUKTUR ANDA: detail_id, payment_id, provider, account_number, status_message)
    $provider = getPaymentProvider($payment_method);
    $account_number = generateAccountNumber($payment_method);
    $status_message = ($payment_method === 'cod') ? 'Menunggu pembayaran saat pengiriman' : 'Payment successfully processed';

    $payment_detail_query = "INSERT INTO payment_details (payment_id, provider, account_number, status_message) 
                            VALUES (?, ?, ?, ?)";

    error_log("Payment detail query: " . $payment_detail_query);

    $payment_detail_stmt = mysqli_prepare($conn, $payment_detail_query);

    if (!$payment_detail_stmt) {
        throw new Exception('Failed to prepare payment detail statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($payment_detail_stmt, 'isss', $payment_id, $provider, $account_number, $status_message);

    if (!mysqli_stmt_execute($payment_detail_stmt)) {
        throw new Exception('Failed to create payment detail: ' . mysqli_error($conn));
    }

    $detail_id = mysqli_insert_id($conn);
    error_log("Payment detail inserted successfully with ID: " . $detail_id);

    mysqli_stmt_close($payment_detail_stmt);

    // Commit transaction jika semua berhasil
    mysqli_commit($conn);

    error_log("=== PAYMENT PROCESS COMPLETED SUCCESSFULLY ===");
    error_log("Order ID: $order_id, Payment ID: $payment_id, Detail ID: $detail_id");

    echo json_encode([
        'success' => true,
        'message' => 'Payment successful! Your order has been placed.',
        'order_id' => $order_id,
        'payment_id' => $payment_id,
        'detail_id' => $detail_id
    ]);
} catch (Exception $e) {
    // Rollback transaction jika ada error
    mysqli_rollback($conn);
    error_log("=== PAYMENT PROCESS FAILED ===");
    error_log("ERROR: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Payment failed: ' . $e->getMessage()
    ]);
}

// Helper functions
function getPaymentProvider($payment_method)
{
    $providers = [
        'credit_card' => 'VISA/MasterCard',
        'bank_transfer' => 'BCA Virtual Account',
        'ewallet' => 'Gopay',
        'cod' => 'Cash on Delivery'
    ];
    return $providers[$payment_method] ?? 'Unknown Provider';
}

function generateAccountNumber($payment_method)
{
    switch ($payment_method) {
        case 'bank_transfer':
            return rand(1000000000, 9999999999); // Format: 3901187654321
        case 'credit_card':
            return rand(1000000000000000, 9999999999999999); // Format kartu kredit 16 digit
        case 'ewallet':
            return '08' . rand(100000000, 999999999); // Format nomor HP
        case 'cod':
            return 'COD-' . rand(1000, 9999);
        default:
            return 'VA' . rand(1000000000, 9999999999);
    }
}
