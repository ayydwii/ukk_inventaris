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

    if (empty($code) || empty($name)) {
        $_SESSION['error'] = "Kode dan Nama tidak boleh kosong";
    } elseif ($stock < 0) {
        $_SESSION['error'] = "Stock tidak boleh negatif";
    } elseif ($price < 0) {
        $_SESSION['error'] = "Harga tidak boleh negatif";
    } else {
        $cek_kode = mysqli_query($conn, "SELECT * FROM products WHERE code = '$code'");
        if (mysqli_num_rows($cek_kode) > 0) {
            $_SESSION['error'] = "Kode produk sudah digunakan!";
        } else {
            mysqli_query($conn, "INSERT INTO products (code,name,stock,price) VALUES ('$code','$name','$stock','$price')");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
<!-- <div class="d-flex"> -->

<!-- SIDEBAR -->
<div class="sidebar">
    <h5 class="logo">Inventaris Gudang</h5>
    <ul class="menu">
        <li>
            <a href="../dashboard.php" class="menu-item">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="index.php" class="menu-item active">
                <i class="bi bi-box-seam"></i>
                Data Produk
            </a>
        </li>
        <li>
            <a href="../transactions/index.php" class="menu-item">
                <i class="bi bi-arrow-left-right"></i>
                Transaksi
            </a>
        </li>
    </ul>

    <div class="logout-area">
        <a href="../logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</div>

<!-- MAIN CONTENT -->
    <div class="main-content">
        <h4 class="mb-1">Tambah Produk</h4>
        <p class="text-muted small mb-4">Tambah data produk inventaris</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card" style="max-width: 500px;">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" required min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" step="0.01" name="price" class="form-control" required min="0">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

</div>

    <script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const code = document.querySelector('input[name="code"]').value.trim();
        const name = document.querySelector('input[name="name"]').value.trim();
        const stock = parseInt(document.querySelector('input[name="stock"]').value);
        const price = parseFloat(document.querySelector('input[name="price"]').value);
        
        if (!code || !name) {
            e.preventDefault();
            alert('Kode dan Nama tidak boleh kosong!');
            return;
        }
        if (isNaN(stock) || stock < 0) {
            e.preventDefault();
            alert('Stock harus angka >= 0!');
            return;
        }
        if (isNaN(price) || price < 0) {
            e.preventDefault();
            alert('Harga harus angka >= 0!');
            return;
        }
    });
    </script>

</html>

