<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$data = mysqli_query($conn, "
SELECT t.*, p.name 
FROM transactions t
JOIN products p ON t.product_id = p.id
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
<div class="bg-dark text-white p-3 d-flex flex-column" style="width:250px; min-height:100vh;">   
    <h4 class="text-center mb-4">Inventaris</h4>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="../dashboard.php" class="nav-link text-white">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="../products/index.php" class="nav-link text-white">
                📦 Data Produk
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="index.php" class="nav-link text-white active">
                💰 Transaksi
            </a>
        </li>
    </ul>
    <hr>
    <a href="../logout.php" class="btn btn-danger w-100">
        🚪 Logout
    </a>    
</div>

<div class="p-4 w-100">
    <h3>Data Transaksi</h3>
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Transaksi</a>

    <a href="laporan.php" class="btn btn-danger mb-3">Download PDF</a>

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
