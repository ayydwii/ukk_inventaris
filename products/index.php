<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="bg-dark text-white p-3 d-flex flex-column position-fixed" style="width:220px; min-height:100vh; top:0; left:0; z-index:1000;">   
    <h5 class="text-center mb-4">Inventaris Gudang</h5>
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
    <h4 class="mb-1">Data Produk</h4>
    <p class="text-muted small mb-3">Kelola data produk inventaris</p>
    
    <!-- Tampilkan Pesan Sukses/Error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <a href="tambah.php" class="btn btn-primary btn-sm mb-3">Tambah Produk</a>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <tr class="table-dark">
                    <th class="text-center" style="width:50px;">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th class="text-center">Stock</th>
                    <th>Harga</th>
                    <th class="text-center" style="width:150px;">Aksi</th>
                </tr>

                <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $row['code']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td class="text-center"><?= $row['stock']; ?></td>
                    <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>
                    <td class="text-center">
                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

</div>
</body>
</html>
