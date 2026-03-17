<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$where = "";

if (isset($_GET['dari']) && isset($_GET['sampai']) && $_GET['dari'] != "" && $_GET['sampai'] != "") {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    $where = "WHERE DATE(t.date) BETWEEN '$dari' AND '$sampai'";
}

$data = mysqli_query($conn, "
SELECT t.*, p.name 
FROM transactions t
JOIN products p ON t.product_id = p.id
$where
ORDER BY t.date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Transaksi</title>
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
            <a href="../products/index.php" class="menu-item">
                <i class="bi bi-box-seam"></i>
                Data Produk
            </a>
        </li>
        <li>
            <a href="index.php" class="menu-item active">
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
        <h4 class="mb-1">Data Transaksi</h4>
        <p class="text-muted small mb-4">Kelola data transaksi inventaris</p>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="mb-4">
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Transaksi
            </a>
            <a href="laporan.php" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>
        </div>

        <!-- Filter Tanggal -->
        <form method="GET" class="row g-2 mb-4">
            <div class="col-auto">
                <input type="date" name="dari" class="form-control" placeholder="Dari">
            </div>
            <div class="col-auto">
                <input type="date" name="sampai" class="form-control" placeholder="Sampai">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <!-- Tabel Transaksi -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Produk</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Jumlah</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>
                    <tbody>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= $row['name']; ?></td>
                            <td class="text-center">
                                <?php if($row['transaction_type'] == 'masuk'): ?>
                                    <span class="badge" style="background: #10B981;">Masuk</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #EF4444;">Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $row['total']; ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['date'])); ?></td>
                        </tr>
                    </tbody>
                    <?php } ?>

                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>

