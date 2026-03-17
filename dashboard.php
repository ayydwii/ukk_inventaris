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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- <div class="d-flex"> -->

<!-- SIDEBAR -->
<div class="sidebar">
    <h5 class="logo">Inventaris Gudang</h5>
    <ul class="menu">
        <li>
            <a href="dashboard.php" class="menu-item active">
                <i class="bi bi-grid-1x2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="products/index.php" class="menu-item">
                <i class="bi bi-box-seam"></i>
                Data Produk
            </a>
        </li>
        <li>
            <a href="transactions/index.php" class="menu-item">
                <i class="bi bi-arrow-left-right"></i>
                Transaksi
            </a>
        </li>
    </ul>

    <div class="logout-area">
        <a href="logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</div>

<!-- MAIN CONTENT -->
    <div class="main-content">
        <h4 class="mb-1">Dashboard</h4>
        <p class="text-muted small mb-4">Selamat datang, <strong><?= $_SESSION['username']; ?></strong></p>

        <!-- Cards Row -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card stat-card stat-card-primary">
                    <div class="text-center">
                        <h6 class="text-uppercase small mb-1">Total Produk</h6>
                        <h3 class="fw-bold mb-0"><?= $total_produk; ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card stat-card stat-card-success">
                    <div class="text-center">
                        <h6 class="text-uppercase small mb-1">Transaksi</h6>
                        <h3 class="fw-bold mb-0"><?= $total_transaksi; ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card stat-card stat-card-warning">
                    <div class="text-center">
                        <h6 class="text-uppercase small mb-1">Total Stok</h6>
                        <h3 class="fw-bold mb-0"><?= $total_stock ?? 0; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Stok Produk -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Grafik Stok Produk</h6>
                <div style="height: 250px;">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Transaksi Masuk vs Keluar -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Grafik Transaksi Masuk vs Keluar</h6>
                <div style="height: 250px;">
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
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1,
            borderRadius: 6
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
                ticks: { font: { size: 11 } },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
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
                'rgba(16, 185, 129, 0.7)',
                'rgba(239, 68, 68, 0.7)'
            ],
            borderColor: [
                'rgba(16, 185, 129, 1)',
                'rgba(239, 68, 68, 1)'
            ],
            borderWidth: 1,
            borderRadius: 6
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
                ticks: { font: { size: 11 } },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});
</script>
</body>
</html>

