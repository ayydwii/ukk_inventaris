<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID tidak ditemukan!";
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn, "UPDATE products SET status='nonaktif' WHERE id='$id'");

if ($query) {
    $_SESSION['success'] = "Produk berhasil dinonaktifkan!";
} else {
    $_SESSION['error'] = "Gagal menonaktifkan produk!";
}

header("Location: index.php");
exit;
?>

