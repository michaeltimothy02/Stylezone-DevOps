<?php
session_start();
include __DIR__ . "/connection.php"; // gunakan __DIR__ agar path selalu benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Jika password di database sudah di-hash, gunakan password_verify()
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: ../admin_web/dashboard.php");
                exit;
            } elseif ($user['role'] === 'customer') {
                header("Location: ../index.php");
                exit;
            } else {
                echo "<script>alert('Role tidak dikenal'); window.location='../interface_web/index.php';</script>";
            }
        } else {
            echo "<script>alert('Password salah!'); window.location='../interface_web/index.php';</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!'); window.location='../interface_web/index.php';</script>";
    }
}
