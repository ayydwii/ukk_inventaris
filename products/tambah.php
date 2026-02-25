<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $stock = (int)$_POST['stock'];
    $price = (float)$_POST['price'];

    // Validasi input
    if (empty($code) || empty($name)) {
        $_SESSION['error'] = "Kode dan Nama tidak boleh kosong";
    } elseif ($stock < 0) {
        $_SESSION['error'] = "Stock tidak boleh negatif";
    } elseif ($price < 0) {
        $_SESSION['error'] = "Harga tidak boleh negatif";
    } else {
        // Cek duplikasi kode
        $cek_kode = mysqli_query($conn, "SELECT * FROM products WHERE code = '$code'");
        if (mysqli_num_rows($cek_kode) > 0) {
            $_SESSION['error'] = "Kode produk sudah digunakan!";
        } else {
            mysqli_query($conn, "INSERT INTO products (code,name,stock,price) 
            VALUES ('$code','$name','$stock','$price')");
            $_SESSION['success'] = "Produk berhasil ditambahkan";
            header("Location: index.php");
            exit;
        }
    }
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
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php" class="nav-link text-white active">
                Data Produk
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="../transactions/index.php" class="nav-link text-white">
                Transaksi
            </a>
        </li>
    </ul>
    <hr>
    <a href="../logout.php" class="btn btn-danger w-100">
        Logout
    </a>    
</div>

<div class="p-4 w-100">
    <h3>Tambah Produk</h3>

    <!-- Tampilkan Pesan Error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

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
