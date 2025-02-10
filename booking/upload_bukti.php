<?php
session_start();
require_once '../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='../auth/login.php';</script>";
    exit;
}

// Ambil daftar transaksi yang masih menunggu pembayaran
$user_id = $_SESSION['user_id'];
$query = "SELECT t.id, l.nama AS lapangan_nama, t.tanggal_pesan, t.jam_mulai, t.jam_selesai, t.total_harga, t.bukti_pembayaran, t.status
          FROM transaksi t
          JOIN lapangan l ON t.lapangan_id = l.id
          WHERE t.user_id = ? AND t.status = 'Menunggu'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Proses upload bukti pembayaran
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaksi_id'])) {
    $transaksi_id = $_POST['transaksi_id'];

    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_name = time() . "_" . $_FILES['bukti_pembayaran']['name'];
        $destination = "../uploads/" . $file_name;

        if (move_uploaded_file($file_tmp, $destination)) {
            // Simpan nama file di database
            $query_update = "UPDATE transaksi SET bukti_pembayaran = ? WHERE id = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("si", $file_name, $transaksi_id);

            if ($stmt_update->execute()) {
                echo "<script>alert('Bukti pembayaran berhasil diunggah! Tunggu validasi admin.'); window.location='upload_bukti.php';</script>";
            } else {
                echo "<script>alert('Terjadi kesalahan, coba lagi!');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah file!');</script>";
        }
    } else {
        echo "<script>alert('Harap pilih file untuk diunggah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Upload Bukti Pembayaran</h2>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Upload Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['lapangan_nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal_pesan']); ?></td>
                    <td><?php echo htmlspecialchars($row['jam_mulai'] . " - " . $row['jam_selesai']); ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['bukti_pembayaran']): ?>
                            <span class="text-success">Sudah Upload</span>
                        <?php else: ?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="transaksi_id" value="<?php echo $row['id']; ?>">
                                <input type="file" name="bukti_pembayaran" class="form-control mb-2" required>
                                <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
