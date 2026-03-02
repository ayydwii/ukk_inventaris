<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Total Produk
$q1 = mysqli_query($conn, "SELECT COUNT(*) as total_produk FROM products");
$total_produk = mysqli_fetch_assoc($q1)['total_produk'];

// Total Transaksi
$q2 = mysqli_query($conn, "SELECT COUNT(*) as total_transaksi FROM transactions");
$total_transaksi = mysqli_fetch_assoc($q2)['total_transaksi'];

// Total Stock
$q3 = mysqli_query($conn, "SELECT SUM(stock) as total_stock FROM products");
$total_stock = mysqli_fetch_assoc($q3)['total_stock'];

// Data untuk Grafik Stok Produk
$data = mysqli_query($conn, "SELECT name, stock FROM products");

$labels = [];
$stocks = [];

while($row = mysqli_fetch_assoc($data)){
    $labels[] = $row['name'];
    $stocks[] = $row['stock'];
}

// Data untuk Grafik Transaksi Masuk vs Keluar
$q_masuk = mysqli_query($conn, "SELECT SUM(total) as total FROM transactions WHERE transaction_type = 'masuk'");
$masuk = mysqli_fetch_assoc($q_masuk)['total'] ?? 0;

$q_keluar = mysqli_query($conn, "SELECT SUM(total) as total FROM transactions WHERE transaction_type = 'keluar'");
$keluar = mysqli_fetch_assoc($q_keluar)['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="bg-dark text-white p-3 d-flex flex-column position-fixed" style="width:220px; min-height:100vh; top:0; left:0; z-index:1000;">
    <h5 class="text-center mb-4">Inventaris Gudang</h5>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link text-white active">
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="products/index.php" class="nav-link text-white">
                Data Produk
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="transactions/index.php" class="nav-link text-white">
                Transaksi
            </a>
        </li>
    </ul>
    <hr>
    <a href="logout.php" class="btn btn-danger btn-sm w-100">
        Logout
    </a>    
</div>

<!-- MAIN CONTENT -->
<div class="p-3 w-100" style="margin-left:220px;">
    <h4 class="mb-1">Dashboard</h4>
    <p class="text-muted small mb-3">Selamat datang, <strong><?= $_SESSION['username']; ?></strong></p>

    <!-- Cards Row - Smaller -->
    <div class="row g-3 mb-3">
        <div class="col-4">
            <div class="card border-primary shadow-sm">
                <div class="card-body py-2 px-3 text-center">
                    <h6 class="text-primary text-uppercase small mb-1">Total Produk</h6>
                    <h3 class="text-primary fw-bold mb-0"><?= $total_produk; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card border-success shadow-sm">
                <div class="card-body py-2 px-3 text-center">
                    <h6 class="text-success text-uppercase small mb-1">Transaksi</h6>
                    <h3 class="text-success fw-bold mb-0"><?= $total_transaksi; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body py-2 px-3 text-center">
                    <h6 class="text-warning text-uppercase small mb-1">Total Stok</h6>
                    <h3 class="text-warning fw-bold mb-0"><?= $total_stock ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Stok Produk -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h6 class="card-title mb-3">Grafik Stok Produk</h6>
            <div style="height: 200px;">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik Transaksi Masuk vs Keluar -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="card-title mb-3">Grafik Transaksi Masuk vs Keluar</h6>
            <div style="height: 200px;">
                <canvas id="transaksiChart"></canvas>
            </div>
        </div>
    </div>

</div>

</div>

<script>
const ctx = document.getElementById('stockChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            label: 'Stock',
            data: <?= json_encode($stocks); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { font: { size: 10 } }
            },
            x: {
                ticks: { font: { size: 10 } }
            }
        }
    }
});

const ctx2 = document.getElementById('transaksiChart');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Masuk', 'Keluar'],
        datasets: [{
            label: 'Jumlah',
            data: [<?= $masuk; ?>, <?= $keluar; ?>],
            backgroundColor: [
                'rgba(40, 167, 69, 0.7)',
                'rgba(220, 53, 69, 0.7)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { font: { size: 10 } }
            },
            x: {
                ticks: { font: { size: 10 } }
            }
        }
    }
});
</script>
</body>
</html>
