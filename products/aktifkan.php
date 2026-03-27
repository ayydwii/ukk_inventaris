<?php
session_start();
include '../config/koneksi.php';

// 🔒 Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// 🔒 Cek apakah ada ID yang dikirim
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID produk tidak ditemukan!";
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// 🔄 Update status jadi aktif
$query = mysqli_query($conn, "UPDATE products SET status='aktif' WHERE id='$id'");

// ✅ Cek hasil query
if ($query) {
    $_SESSION['success'] = "Produk berhasil diaktifkan kembali!";
} else {
    $_SESSION['error'] = "Gagal mengaktifkan produk!";
}

// 🔙 Kembali ke halaman produk
header("Location: index.php");
exit;
?>