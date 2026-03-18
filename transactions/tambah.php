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

    if ($product_id <= 0) {
        $_SESSION['error'] = "Produk tidak valid";
    } elseif ($total <= 0) {
        $_SESSION['error'] = "Jumlah harus lebih dari 0";
    } else {
        $cek = mysqli_query($conn, "SELECT stock FROM products WHERE id = $product_id");
        if (mysqli_num_rows($cek) == 0) {
            $_SESSION['error'] = "Produk tidak ditemukan";
        } else {
            $dataStock = mysqli_fetch_assoc($cek);
            $stok_sekarang = $dataStock['stock'];

            if ($type == 'keluar' && $total > $stok_sekarang) {
                $_SESSION['error'] = "Stock tidak mencukupi! Stok tersedia: $stok_sekarang";
            } else {
                mysqli_query($conn, "INSERT INTO transactions (product_id,user_id,transaction_type,total,date) VALUES ('$product_id','1','$type','$total','$date')");

                if ($type == 'masuk') {
                    mysqli_query($conn, "UPDATE products SET stock = stock + $total WHERE id = $product_id");
                } else {
                    mysqli_query($conn, "UPDATE products SET stock = stock - $total WHERE id = $product_id");
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
        <h4 class="mb-1">Tambah Transaksi</h4>
        <p class="text-muted small mb-4">Tambah data transaksi inventaris</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card" style="max-width: 500px;">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Pilih Produk</option>
                            <?php while($p=mysqli_fetch_assoc($products)){ ?>
                                <option value="<?= $p['id']; ?>" data-stock="<?= $p['stock']; ?>"><?= $p['name']; ?> (Stok: <?= $p['stock']; ?>)</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="transaction_type" class="form-control" required>
                            <option value="">Pilih Tipe</option>
                            <option value="masuk">Masuk</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="total" class="form-control" required min="1" id="total">
                        <div id="stock-warning" class="text-danger mt-1" style="display:none;"></div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>


    </script>

</div>
</body>
</html>

<script>
let currentStock = 0;

document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.querySelector('select[name="product_id"]');
    const typeSelect = document.querySelector('select[name="transaction_type"]');
    const totalInput = document.querySelector('#total');
    const submitBtn = document.querySelector('button[name="simpan"]');
    const warningDiv = document.querySelector('#stock-warning');

    function validateForm() {
        const productId = productSelect.value;
        const type = typeSelect.value;
        const total = parseInt(totalInput.value);

        if (!productId || !type || !total || total <= 0) {
            submitBtn.disabled = true;
            return;
        }

        if (type === 'keluar' && total > currentStock) {
            warningDiv.textContent = `Stock hanya ${currentStock}, tidak boleh keluar lebih dari stock!`;
            warningDiv.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            warningDiv.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    productSelect.addEventListener('change', function() {
        currentStock = parseInt(this.options[this.selectedIndex].dataset.stock) || 0;
        validateForm();
    });

    typeSelect.addEventListener('change', validateForm);
    totalInput.addEventListener('input', validateForm);

    // Initial validation
    validateForm();
});

document.querySelector('form').addEventListener('submit', function(e) {
    if (submitBtn.disabled) {
        e.preventDefault();
        alert('Form tidak valid! Periksa input.');
    }
});
</script>
</html>

