<?php
session_start();
require_once '../config/database.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='../auth/login.php';</script>";
    exit;
}

// Ambil jumlah lapangan yang tersedia
$query_lapangan = "SELECT COUNT(*) AS total FROM lapangan";
$result_lapangan = $conn->query($query_lapangan);
$total_lapangan = $result_lapangan->fetch_assoc()['total'];

// Ambil jumlah transaksi yang menunggu validasi
$query_transaksi = "SELECT COUNT(*) AS total FROM transaksi WHERE status = 'Menunggu'";
$result_transaksi = $conn->query($query_transaksi);
$total_transaksi = $result_transaksi->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Dashboard Admin</h2>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>

    <p class="text-center">Selamat datang, <strong><?php echo $_SESSION['admin_nama']; ?></strong>!</p>

    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Lapangan Dikelola</h5>
                    <p class="card-text"><?php echo $total_lapangan; ?> Lapangan</p>
                    <a href="manage_lapangan.php" class="btn btn-light">Kelola Lapangan</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pembayaran Menunggu</h5>
                    <p class="card-text"><?php echo $total_transaksi; ?> Pembayaran</p>
                    <a href="validasi_pembayaran.php" class="btn btn-light">Validasi Pembayaran</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
