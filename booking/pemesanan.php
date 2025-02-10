<?php
session_start();
require_once '../config/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='../auth/login.php';</script>";
    exit;
}

// Ambil ID lapangan dari parameter URL
if (!isset($_GET['id'])) {
    echo "<script>alert('Lapangan tidak ditemukan!'); window.location='../user/cari_lapangan.php';</script>";
    exit;
}

$lapangan_id = $_GET['id'];

// Ambil informasi lapangan
$query_lapangan = "SELECT * FROM lapangan WHERE id = ?";
$stmt = $conn->prepare($query_lapangan);
$stmt->bind_param("i", $lapangan_id);
$stmt->execute();
$result_lapangan = $stmt->get_result();
$lapangan = $result_lapangan->fetch_assoc();

// Jika lapangan tidak ditemukan
if (!$lapangan) {
    echo "<script>alert('Lapangan tidak ditemukan!'); window.location='../user/cari_lapangan.php';</script>";
    exit;
}

// Ambil daftar pemesanan untuk lapangan ini
$query_pemesanan = "SELECT * FROM transaksi WHERE lapangan_id = ? ORDER BY tanggal_pesan, jam_mulai";
$stmt_pemesanan = $conn->prepare($query_pemesanan);
$stmt_pemesanan->bind_param("i", $lapangan_id);
$stmt_pemesanan->execute();
$result_pemesanan = $stmt_pemesanan->get_result();

// Proses pemesanan baru menggunakan stored procedure
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $tanggal_pesan = $_POST['tanggal_pesan'];
    $jam_mulai = $_POST['jam_mulai'];

    // Hitung jam selesai (otomatis +1 jam dari jam mulai)
    $jam_selesai = date("H:i", strtotime($jam_mulai) + 3600);
    $total_harga = $lapangan['harga_per_jam'];

    // Panggil Stored Procedure untuk melakukan pemesanan
    $stmt_pesan = $conn->prepare("CALL InsertPemesanan(?, ?, ?, ?, ?, ?, @status)");
    $stmt_pesan->bind_param("iisssd", $user_id, $lapangan_id, $tanggal_pesan, $jam_mulai, $jam_selesai, $total_harga);
    $stmt_pesan->execute();

    // Ambil hasil status dari stored procedure
    $result_status = $conn->query("SELECT @status AS status");
    $status = $result_status->fetch_assoc()['status'];

    if ($status === 'Duplikasi') {
        echo "<script>alert('WARNING !!! Lapangan sudah dipesan di waktu tersebut! Pilih waktu lain.');</script>";
    } else {
        echo "<script>alert('Pemesanan berhasil! Tunggu konfirmasi admin.'); window.location='pemesanan.php?id=$lapangan_id';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Lapangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-center"><?php echo htmlspecialchars($lapangan['nama']); ?> - Pemesanan</h2>
        <a href="http://localhost/ptifutsal/user/cari_lapangan.php" class="btn btn-secondary">Back</a>
    </div>

    <h4 class="mt-4">Jadwal Pemesanan</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_pemesanan->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tanggal_pesan']); ?></td>
                    <td><?php echo htmlspecialchars($row['jam_mulai']); ?></td>
                    <td><?php echo htmlspecialchars($row['jam_selesai']); ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h4 class="mt-4">Pesan Lapangan</h4>
    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Tanggal Pemesanan</label>
            <input type="date" name="tanggal_pesan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jam Mulai</label>
            <select name="jam_mulai" id="jam_mulai" class="form-control" required onchange="setJamSelesai()">
                <option value="pilihjammulai">Pilih Jam Mulai</option>
                <option value="09:00">09:00</option>
                <option value="11:00">11:00</option>
                <option value="13:00">13:00</option>
                <option value="15:00">15:00</option>
                <option value="17:00">17:00</option>
                <option value="19:00">19:00</option>
                <option value="21:00">21:00</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Jam Selesai</label>
            <input type="text" id="jam_selesai" name="jam_selesai" class="form-control" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
    </form>
</div>

<script>
function setJamSelesai() {
    var jamMulai = document.getElementById('jam_mulai').value;
    var jamSelesai = new Date("2023-01-01 " + jamMulai);
    jamSelesai.setHours(jamSelesai.getHours() + 1);
    document.getElementById('jam_selesai').value = jamSelesai.toTimeString().split(" ")[0].substring(0, 5);
}
</script>

</body>
</html>
