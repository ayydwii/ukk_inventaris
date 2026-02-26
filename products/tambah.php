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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="bg-dark text-white p-3 d-flex flex-column position-fixed" style="width:220px; min-height:100vh; top:0; left:0; z-index:1000;">   
    <h5 class="text-center mb-4">Inventaris</h5>
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
    <a href="../logout.php" class="btn btn-danger btn-sm w-100">
        Logout
    </a>    
</div>

<!-- MAIN CONTENT -->
<div class="p-3 w-100" style="margin-left:220px;">
    <h4 class="mb-1">Tambah Produk</h4>
    <p class="text-muted small mb-3">Tambah data produk inventaris</p>

    <!-- Tampilkan Pesan Error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm" style="max-width: 500px;">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" class="form-control form-control-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control form-control-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control form-control-sm" required min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" step="0.01" name="price" class="form-control form-control-sm" required min="0">
                </div>

                <button type="submit" name="simpan" class="btn btn-success btn-sm">Simpan</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Kembali</a>
            </form>
        </div>
    </div>
</div>

</div>
</body>
</html>
