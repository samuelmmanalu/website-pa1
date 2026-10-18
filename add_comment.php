<?php
session_start();
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $produk_id = mysqli_real_escape_string($conn, $_POST['produk_id']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Validate comment
    if (empty($comment)) {
        $_SESSION['error'] = "Komentar tidak boleh kosong";
        header("Location: detail_produk.php?produk=" . $produk_id);
        exit();
    }
    
    // Insert comment into database
    $query = "INSERT INTO comments (user_id, produk_id, comment, created_at) 
              VALUES ('$user_id', '$produk_id', '$comment', NOW())";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Komentar berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menambahkan komentar";
    }
    
    header("Location: detail_produk.php?produk=" . $produk_id);
    exit();
} else {
    header('Location: index.php');
    exit();
}
?> 