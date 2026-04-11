<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil hanya produk aktif
$products = mysqli_query($conn, "SELECT * FROM products WHERE status='aktif'");

if (isset($_POST['simpan'])) {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $type = $_POST['transaction_type'];
    $total = (int)$_POST['total'];
    $date = date('Y-m-d H:i:s');

    // VALIDASI
    if ($product_id <= 0) {
        $_SESSION['error'] = "Silakan pilih produk terlebih dahulu!";
    } elseif (!in_array($type, ['masuk','keluar'])) {
        $_SESSION['error'] = "Tipe transaksi tidak valid!";
    } elseif ($total <= 0) {
        $_SESSION['error'] = "Jumlah harus lebih dari 0";
    } else {

        // Cek produk aktif
$cek = mysqli_query($conn, "SELECT stock FROM products WHERE id = $product_id");

        if (mysqli_num_rows($cek) == 0) {
$_SESSION['error'] = "Produk tidak ditemukan!";
        } else {
            $dataStock = mysqli_fetch_assoc($cek);
            $stok_sekarang = $dataStock['stock'];

            // Cegah stok minus
            if ($type == 'keluar' && $total > $stok_sekarang) {
                $_SESSION['error'] = "Stock tidak mencukupi! Stok tersedia: $stok_sekarang";
            } else {

                // Simpan transaksi
                $insert = mysqli_query($conn, "INSERT INTO transactions 
                    (product_id,user_id,transaction_type,total,date) 
                    VALUES ('$product_id','1','$type','$total','$date')");

                if ($insert) {

                    // Update stok (HANYA SEKALI!)
                    if ($type == 'masuk') {
                        mysqli_query($conn, "UPDATE products SET stock = stock + $total WHERE id = $product_id");
                    } else {
                        mysqli_query($conn, "UPDATE products SET stock = stock - $total WHERE id = $product_id");
                    }

                    $_SESSION['success'] = "Transaksi berhasil disimpan";
                    header("Location: index.php");
                    exit;

                } else {
                    $_SESSION['error'] = "Gagal menyimpan transaksi!";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h5 class="logo">Inventaris Gudang</h5>
    <ul class="menu">
        <li>
            <a href="../dashboard.php" class="menu-item">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="../products/index.php" class="menu-item">
                <i class="bi bi-box-seam"></i> Data Produk
            </a>
        </li>
        <li>
            <a href="index.php" class="menu-item active">
                <i class="bi bi-arrow-left-right"></i> Transaksi
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
    <h4 class="mb-1">Tambah Transaksi</h4>
    <p class="text-muted small mb-4">Tambah data transaksi inventaris</p>
    
    <!-- ERROR -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- SUCCESS -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="card" style="max-width: 500px;">
        <div class="card-body">
            <form method="POST">

                <!-- PRODUK -->
                <div class="mb-3">
                    <label class="form-label">Produk</label>
                    <select name="product_id" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php while($p=mysqli_fetch_assoc($products)){ ?>
                            <option value="<?= $p['id']; ?>">
                                <?= $p['name']; ?> (Stok: <?= $p['stock']; ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- TIPE -->
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="transaction_type" class="form-control" required>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>

                <!-- JUMLAH -->
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="total" class="form-control" required min="1">
                </div>

                <!-- BUTTON -->
                <button type="submit" name="simpan" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>

            </form>
        </div>
    </div>
</div>

</body>
</html>