<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products");

if (isset($_POST['simpan'])) {
    $product_id = (int)$_POST['product_id'];
    $type = $_POST['transaction_type'];
    $total = (int)$_POST['total'];
    $date = date('Y-m-d H:i:s');

    // VALIDASI
    if ($product_id <= 0) {
        $_SESSION['error'] = "Produk tidak valid";
    } elseif ($total <= 0) {
        $_SESSION['error'] = "Jumlah harus lebih dari 0";
    } else {
        // ambil stok sekarang
        $cek = mysqli_query($conn, "SELECT stock FROM products WHERE id = $product_id");
        if (mysqli_num_rows($cek) == 0) {
            $_SESSION['error'] = "Produk tidak ditemukan";
        } else {
            $dataStock = mysqli_fetch_assoc($cek);
            $stok_sekarang = $dataStock['stock'];

            if ($type == 'keluar' && $total > $stok_sekarang) {
                $_SESSION['error'] = "Stock tidak mencukupi! Stok tersedia: $stok_sekarang";
            } else {
                // simpan transaksi
                mysqli_query($conn, "INSERT INTO transactions 
                (product_id,user_id,transaction_type,total,date)
                VALUES ('$product_id','1','$type','$total','$date')");

                // update stock
                if ($type == 'masuk') {
                    mysqli_query($conn, "UPDATE products 
                    SET stock = stock + $total WHERE id = $product_id");
                } else {
                    mysqli_query($conn, "UPDATE products 
                    SET stock = stock - $total WHERE id = $product_id");
                }

                $_SESSION['success'] = "Transaksi berhasil disimpan";
                header("Location: index.php");
                exit;
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
            <a href="../products/index.php" class="nav-link text-white">
                Data Produk
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php" class="nav-link text-white active">
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
    <h3>Tambah Transaksi</h3>

    <!-- Tampilkan Pesan Error/Sukses -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Produk</label>
            <select name="product_id" class="form-control">
                <?php while($p=mysqli_fetch_assoc($products)){ ?>
                    <option value="<?= $p['id']; ?>"><?= $p['name']; ?> (Stok: <?= $p['stock']; ?>)</option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Tipe</label>
            <select name="transaction_type" class="form-control">
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="total" class="form-control" required min="1">
        </div>

        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
    </form>
</div>

</div>
</body>
</html>
