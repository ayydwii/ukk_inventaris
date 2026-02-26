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
    <h3>Data Transaksi</h3>
    
    <!-- Tampilkan Pesan Sukses/Error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <!-- button tambah transaksi -->
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Transaksi</a>
    
    <!-- button download pdf -->
    <a href="laporan.php" class="btn btn-danger mb-3">Download PDF</a>

    <!-- filter tanggal transaksi -->
    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <input type="date" name="dari" class="form-control">
        </div>

        <div class="col-md-3">
            <input type="date" name="sampai" class="form-control">
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>
    </form> 

    <table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Produk</th>
        <th>Tipe</th>
        <th>Jumlah</th>
        <th>Tanggal</th>
    </tr>

    <?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['name']; ?></td>
        <td><?= $row['transaction_type']; ?></td>
        <td><?= $row['total']; ?></td>
        <td><?= $row['date']; ?></td>
    </tr>
    <?php } ?>

    </table>
</div>

</div>
</body>
</html>
