<?php
include "connection.php"; // file koneksi di folder yang sama

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name  = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $conn->real_escape_string($_POST['password']);
    $role  = "customer";

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role) 
            VALUES ('$name', '$email', '$hashed_pass', '$role')";

    if ($conn->query($sql)) {
        echo "<script>
                alert('Registration successful!');
                window.location='../index.php';
              </script>";
    } else {
        echo "<script>
                alert('Registration failed: " . $conn->error . "');
                window.history.back();
              </script>";
    }
}
?>
