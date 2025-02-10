<?php
session_start();
require_once '../config/database.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Silakan login sebagai admin!'); window.location='../auth/login.php';</script>";
    exit;
}

// Ambil daftar transaksi yang menunggu validasi
$query = "SELECT t.id, u.nama AS user_nama, l.nama AS lapangan_nama, t.tanggal_pesan, 
                 t.jam_mulai, t.jam_selesai, t.total_harga, t.bukti_pembayaran, t.status
          FROM transaksi t
          JOIN user u ON t.user_id = u.id
          JOIN lapangan l ON t.lapangan_id = l.id
          WHERE t.status = 'Menunggu'";
$result = $conn->query($query);

// Validasi pembayaran (Konfirmasi / Tolak)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaksi_id'])) {
    $transaksi_id = $_POST['transaksi_id'];
    $status_baru = $_POST['status'];

    // Debugging: Cek nilai transaksi_id dan status
    error_log("Updating transaksi ID: $transaksi_id, Status: $status_baru");

    // Update status transaksi
    $query_update = "UPDATE transaksi SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query_update);
    $stmt->bind_param("si", $status_baru, $transaksi_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $conn->commit();  // Simpan perubahan
            echo "<script>alert('Status pembayaran diperbarui menjadi $status_baru!'); window.location='validasi_pembayaran.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui status. Tidak ada perubahan yang dilakukan!');</script>";
        }
    } else {
        error_log("Query Error: " . $conn->error);
        echo "<script>alert('Terjadi kesalahan, coba lagi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Pembayaran</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Validasi Pembayaran</h2>
        <a href="http://localhost/ptifutsal/admin/dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Nama User</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Total Harga</th>
                <th>Bukti Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['lapangan_nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal_pesan']); ?></td>
                    <td><?php echo htmlspecialchars($row['jam_mulai'] . " - " . $row['jam_selesai']); ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($row['bukti_pembayaran']): ?>
                            <a href="../uploads/<?php echo $row['bukti_pembayaran']; ?>" target="_blank">Lihat</a>
                        <?php else: ?>
                            <span class="text-danger">Belum upload</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="transaksi_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="status" value="Berhasil" class="btn btn-success btn-sm">Konfirmasi</button>
                            <button type="submit" name="status" value="Ditolak" class="btn btn-danger btn-sm">Tolak</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
