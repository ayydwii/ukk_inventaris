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
    <a href="../logout.php" class="btn btn-danger btn-sm w-100">
        Logout
    </a>    
</div>

<!-- MAIN CONTENT -->
<div class="p-3 w-100" style="margin-left:220px;">
    <h4 class="mb-1">Data Transaksi</h4>
    <p class="text-muted small mb-3">Kelola data transaksi inventaris</p>
    
    <!-- Tampilkan Pesan Sukses/Error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="mb-3">
        <a href="tambah.php" class="btn btn-primary btn-sm">Tambah Transaksi</a>
        <a href="laporan.php" class="btn btn-danger btn-sm">Download PDF</a>
    </div>

    <!-- Filter Tanggal -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="date" name="dari" class="form-control form-control-sm" placeholder="Dari">
        </div>
        <div class="col-auto">
            <input type="date" name="sampai" class="form-control form-control-sm" placeholder="Sampai">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="index.php" class="btn btn-secondary btn-sm">Reset</a>
        </div>
    </form> 

    <!-- Tabel Transaksi -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <tr class="table-dark">
                    <th class="text-center" style="width:50px;">No</th>
                    <th>Produk</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Jumlah</th>
                    <th>Tanggal</th>
                </tr>

                <?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td class="text-center">
                        <?php if($row['transaction_type'] == 'masuk'): ?>
                            <span class="badge bg-success">Masuk</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Keluar</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $row['total']; ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['date'])); ?></td>
                </tr>
                <?php } ?>

            </table>
        </div>
    </div>
</div>

</div>
</body>
</html>
