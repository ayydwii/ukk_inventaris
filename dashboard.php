<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$data = mysqli_query($conn, "SELECT name, stock FROM products");

$labels = [];
$stocks = [];

while($row = mysqli_fetch_assoc($data)){
    $labels[] = $row['name'];
    $stocks[] = $row['stock'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="d-flex">

<!-- SIDEBAR -->
<div class="bg-dark text-white p-3 d-flex flex-column" style="width:250px; min-height:100vh;">   
    <h4 class="text-center mb-4">Inventaris</h4>
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
    <a href="logout.php" class="btn btn-danger w-100">
        Logout
    </a>    
</div>

<div class="p-4 w-100">
    <h3>Dashboard</h3>
    <p>Selamat datang, <?= $_SESSION['username']; ?></p>

    <!-- GRAFIK -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <canvas id="stockChart" height="100"></canvas>

        <script>
        const ctx = document.getElementById('stockChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels); ?>,
                datasets: [{
                    label: 'Stock Produk',
                    data: <?= json_encode($stocks); ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        </script>
</div>

</div>
</body>
</html>

