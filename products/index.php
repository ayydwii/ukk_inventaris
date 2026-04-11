<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$where = "";
if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $where = "WHERE name LIKE '%$cari%' OR code LIKE '%$cari%'";
}
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'status';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$order_next = $order == 'ASC' ? 'DESC' : 'ASC';
$orderby = "ORDER BY $sort $order, id DESC";

$data = mysqli_query($conn, "SELECT * FROM products $where $orderby");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Produk</title>
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
            <a href="index.php" class="menu-item active">
                <i class="bi bi-box-seam"></i> Data Produk
            </a>
        </li>
        <li>
            <a href="../transactions/index.php" class="menu-item">
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
    <h4 class="mb-1">Data Produk</h4>
    <p class="text-muted small mb-4">Kelola data produk inventaris</p>
    
    <!-- Search Form -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="cari" class="form-control" placeholder="Cari nama atau kode produk..." value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="?cari=<?= urlencode($_GET['cari'] ?? '') ?>&sort=name&order=<?= $order_next ?>" class="btn btn-outline-primary <?= $sort == 'name' ? 'active' : '' ?>">
                    Nama <i class="bi bi-arrow-<?= $order == 'ASC' ? 'up' : 'down' ?>-square"></i>
                </a>
                <a href="?cari=<?= urlencode($_GET['cari'] ?? '') ?>&sort=status&order=<?= $order_next ?>" class="btn btn-outline-primary <?= $sort == 'status' ? 'active' : '' ?>">
                    Status <i class="bi bi-arrow-<?= $order == 'ASC' ? 'up' : 'down' ?>-square"></i>
                </a>
                <a href="?cari=<?= urlencode($_GET['cari'] ?? '') ?>&sort=stock&order=<?= $order_next ?>" class="btn btn-outline-primary <?= $sort == 'stock' ? 'active' : '' ?>">
                    Stock <i class="bi bi-arrow-<?= $order == 'ASC' ? 'up' : 'down' ?>-square"></i>
                </a>
            </div>
        </div>
    </form>
    
    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <a href="tambah.php" class="btn btn-primary mb-4">
        <i class="bi bi-plus-circle"></i> Tambah Produk
    </a>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
<th class="text-center" style="width: 60px;">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="text-center">Stock</th>
                        <th>Status</th>
                        <th>Harga</th>
                        <th class="text-center" style="width: 220px;">Aksi</th>

                    </tr>
                </thead>

                <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= $row['code']; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td class="text-center"><?= $row['stock']; ?></td>
                        <td>
                            <?php if ($row['status'] == 'aktif') { ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php } else { ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php } ?>
                        </td>
                        <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>

                        <!-- AKSI -->
                        <td class="text-center">

                            <!-- EDIT -->
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <?php if ($row['status'] == 'aktif') { ?>
                                <!-- NONAKTIFKAN -->
                                <a href="nonaktif.php?id=<?= $row['id']; ?>" 
                                    class="btn btn-warning btn-sm"
                                    onclick="return confirm('Nonaktifkan produk ini?')">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            <?php } else { ?>
                                <!-- AKTIFKAN -->
                                <a href="aktifkan.php?id=<?= $row['id']; ?>" 
                                    class="btn btn-success btn-sm"
                                    onclick="return confirm('Aktifkan kembali produk ini?')">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            <?php } ?>

                            <!-- HAPUS -->
                            <a href="hapus.php?id=<?= $row['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin hapus produk ini? History transaksi tetap tersimpan!');">
                               <i class="bi bi-trash"></i>
                            </a>

                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

</body>
</html>