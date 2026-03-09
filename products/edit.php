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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="text-dark p-3 d-flex flex-column position-fixed sidebar" style="width:220px; min-height:100vh; top:0; left:0; z-index:1000; background: #ffffff;">
    <h5 class="text-center mb-4" style="color: #528CF6;">Inventaris Gudang</h5>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="../dashboard.php" class="nav-link text-dark">
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="index.php" class="nav-link text-dark active">
                Data Produk
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="../transactions/index.php" class="nav-link text-dark">
                Transaksi
            </a>
        </li>
    </ul>
    <hr style="border-color: #D6DCEC;">
    <a href="../logout.php" class="btn btn-danger btn-sm w-100">
        Logout
    </a>    
</div>

<!-- MAIN CONTENT -->
<div class="p-3 w-100" style="margin-left:220px;">
    <h4 class="mb-1">Edit Produk</h4>
    <p class="text-muted small mb-3">Edit data produk inventaris</p>
    
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
                    <input type="text" name="code" class="form-control form-control-sm" value="<?= $produk['code']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="<?= $produk['name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control form-control-sm" value="<?= $produk['stock']; ?>" required min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" step="0.01" name="price" class="form-control form-control-sm" value="<?= $produk['price']; ?>" required min="0">
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
