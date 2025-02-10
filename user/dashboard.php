<?php
session_start();
require_once '../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='../auth/login.php';</script>";
    exit;
}

// Ambil jumlah lapangan tersedia
$query_lapangan = "SELECT COUNT(*) AS total FROM lapangan";
$result_lapangan = $conn->query($query_lapangan);
$total_lapangan = $result_lapangan->fetch_assoc()['total'];

// Ambil daftar pemesanan user
$user_id = $_SESSION['user_id'];
$query_jadwal = "SELECT t.tanggal_pesan, t.jam_mulai, t.jam_selesai, t.status, l.nama AS lapangan_nama 
                 FROM transaksi t 
                 JOIN lapangan l ON t.lapangan_id = l.id 
                 WHERE t.user_id = ? 
                 ORDER BY t.tanggal_pesan, t.jam_mulai";
$stmt_jadwal = $conn->prepare($query_jadwal);
$stmt_jadwal->bind_param("i", $user_id);
$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Dashboard User</h2>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
    
    <p class="text-center">Selamat datang, <strong><?php echo $_SESSION['user_nama']; ?></strong>!</p>

    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Lapangan Tersedia</h5>
                    <p class="card-text"><?php echo $total_lapangan; ?> Lapangan</p>
                    <a href="cari_lapangan.php" class="btn btn-light">Cari Lapangan</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Upload Bukti</h5>
                    <p class="card-text">Konfirmasi Pembayaran</p>
                    <a href="../booking/upload_bukti.php" class="btn btn-light">Upload Sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mt-4">Jadwal Pemesanan Anda</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_jadwal->num_rows > 0): ?>
                <?php while ($row = $result_jadwal->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['lapangan_nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_pesan']); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_mulai']); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_selesai']); ?></td>
                        <td>
                            <?php 
                            if ($row['status'] == 'Menunggu') {
                                echo '<span class="badge bg-warning">Menunggu</span>';
                            } elseif ($row['status'] == 'Berhasil') {
                                echo '<span class="badge bg-success">Berhasil</span>';
                            } else {
                                echo '<span class="badge bg-danger">Ditolak</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Anda belum memiliki pemesanan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
