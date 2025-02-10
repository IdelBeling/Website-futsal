<?php
session_start();
require_once '../config/database.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Silakan login sebagai admin!'); window.location='../auth/login.php';</script>";
    exit;
}

// Ambil data lapangan berdasarkan ID
if (!isset($_GET['id'])) {
    echo "<script>alert('Lapangan tidak ditemukan!'); window.location='manage_lapangan.php';</script>";
    exit;
}

$lapangan_id = $_GET['id'];
$query = "SELECT * FROM lapangan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lapangan_id);
$stmt->execute();
$result = $stmt->get_result();
$lapangan = $result->fetch_assoc();

// Proses update lapangan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $harga_per_jam = $_POST['harga_per_jam'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $query_update = "UPDATE lapangan SET nama=?, lokasi=?, harga_per_jam=?, latitude=?, longitude=? WHERE id=?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("ssdddi", $nama, $lokasi, $harga_per_jam, $latitude, $longitude, $lapangan_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Lapangan berhasil diperbarui!'); window.location='manage_lapangan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lapangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Edit Lapangan</h2>
    <form action="" method="POST">
        <input type="text" name="nama" class="form-control mb-2" value="<?php echo $lapangan['nama']; ?>" required>
        <input type="text" name="lokasi" class="form-control mb-2" value="<?php echo $lapangan['lokasi']; ?>" required>
        <input type="number" name="harga_per_jam" class="form-control mb-2" value="<?php echo $lapangan['harga_per_jam']; ?>" required>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

</body>
</html>
