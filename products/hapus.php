<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Cek ID parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID produk tidak valid";
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Cek apakah produk ada di database
$cek = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
if (mysqli_num_rows($cek) == 0) {
    $_SESSION['error'] = "Produk tidak ditemukan";
    header("Location: index.php");
    exit;
}

// Cek apakah produk memiliki transaksi terkait
$cek_transaksi = mysqli_query($conn, "SELECT * FROM transactions WHERE product_id = $id");
if (mysqli_num_rows($cek_transaksi) > 0) {
    $_SESSION['error'] = "Produk tidak dapat dihapus karena memiliki transaksi terkait!";
    header("Location: index.php");
    exit;
}

// Hapus produk
$hapus = mysqli_query($conn, "DELETE FROM products WHERE id = $id");

if ($hapus) {
    $_SESSION['success'] = "Produk berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus produk: " . mysqli_error($conn);
}

header("Location: index.php");
exit;
?>
