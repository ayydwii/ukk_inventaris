<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    mysqli_query($conn, "INSERT INTO products (code,name,stock,price) 
    VALUES ('$code','$name','$stock','$price')");

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="bg-dark text-white p-3 d-flex flex-column" style="width:250px; min-height:100vh;">   
    <h4 class="text-center mb-4">Inventaris</h4>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="../dashboard.php" class="nav-link text-white">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php" class="nav-link text-white active">
                📦 Data Produk
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="../transactions/index.php" class="nav-link text-white">
                💰 Transaksi
            </a>
        </li>
    </ul>
    <hr>
    <a href="../logout.php" class="btn btn-danger w-100">
        🚪 Logout
    </a>    
</div>

<div class="p-4 w-100">
    <h3>Tambah Produk</h3>

    <form method="POST">
        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
    </form>
</div>

</div>
</body>
</html>
