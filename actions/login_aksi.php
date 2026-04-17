<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // No hashing for now, as inserted as plain text 'admin123'
    
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if ($user['password'] === $password) { // Wait, MD5 or plain text? I used plain text in INSERT: 'admin123'
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: ../barang.php');
            exit;
        } else {
            $_SESSION['error_login'] = "Password salah!";
            header('Location: ../login.php');
            exit;
        }
    } else {
        $_SESSION['error_login'] = "Username tidak ditemukan!";
        header('Location: ../login.php');
        exit;
    }
} else {
    header('Location: ../login.php');
    exit;
}
?>
