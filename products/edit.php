<?php
session_start();
include '../config/koneksi.php';

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

$produk = mysqli_fetch_assoc($cek);

if (isset($_POST['simpan'])) {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $stock = (int)$_POST['stock'];
    $price = (float)$_POST['price'];

    // Validasi input
    if (empty($code) || empty($name)) {
        $_SESSION['error'] = "Kode dan Nama tidak boleh kosong";
        header("Location: edit.php?id=$id");
        exit;
    }

    if ($stock < 0) {
        $_SESSION['error'] = "Stock tidak boleh negatif";
        header("Location: edit.php?id=$id");
        exit;
    }

    if ($price < 0) {
        $_SESSION['error'] = "Harga tidak boleh negatif";
        header("Location: edit.php?id=$id");
        exit;
    }

    // Cek duplikasi kode (kecuali untuk produk saat ini)
    $cek_kode = mysqli_query($conn, "SELECT * FROM products WHERE code = '$code' AND id != $id");
    if (mysqli_num_rows($cek_kode) > 0) {
        $_SESSION['error'] = "Kode produk sudah digunakan!";
        header("Location: edit.php?id=$id");
        exit;
    }

    $update = mysqli_query($conn, "UPDATE products SET code='$code', name='$name', stock=$stock, price=$price WHERE id=$id");

    if ($update) {
        $_SESSION['success'] = "Produk berhasil diperbarui";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui produk: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="bg-dark text-white p-3 d-flex flex-column position-fixed" style="width:220px; min-height:100vh; top:0; left:0; z-index:1000;">   
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

<div class="p-4 w-100" style="margin-left:220px;">
    <h3>Edit Produk</h3>
    
    <!-- Tampilkan Pesan Error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="code" class="form-control" value="<?= $produk['code']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="<?= $produk['name']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= $produk['stock']; ?>" required min="0">
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $produk['price']; ?>" required min="0">
        </div>

        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</div>
</body>
</html>
