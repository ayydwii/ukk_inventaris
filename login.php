<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100" style="background: #f8FAFC;">

<div class="card shadow-sm" style="width: 350px; border: 1px solid #D6DCEC;">
    <div class="card-body p-4">
        <h5 class="text-center mb-4" style="color: #528CF6;">Login Admin</h5>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control form-control-sm" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-sm" required>
            </div>

            <button type="submit" class="btn btn-primary btn-sm w-100">Login</button>
        </form>
    </div>
</div>

</body>
</html>
