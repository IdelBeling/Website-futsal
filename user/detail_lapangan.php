<?php
session_start();
require_once '../config/database.php';

// Pastikan ID lapangan tersedia di URL
if (!isset($_GET['id'])) {
    echo "<script>alert('Lapangan tidak ditemukan!'); window.location='cari_lapangan.php';</script>";
    exit;
}

$lapangan_id = $_GET['id'];
$query = "SELECT * FROM lapangan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lapangan_id);
$stmt->execute();
$result = $stmt->get_result();
$lapangan = $result->fetch_assoc();

// Jika lapangan tidak ditemukan
if (!$lapangan) {
    echo "<script>alert('Lapangan tidak ditemukan!'); window.location='cari_lapangan.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lapangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAGXj8RaWrcxpo8DHH9IthHfWsnt5sasjU&callback=initMap" async defer></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-center"><?php echo htmlspecialchars($lapangan['nama']); ?></h2>
        <a href="cari_lapangan.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($lapangan['lokasi']); ?></p>
            <p><strong>Harga per jam:</strong> Rp <?php echo number_format($lapangan['harga_per_jam'], 0, ',', '.'); ?></p>
            <p><strong>Jam Operasional:</strong> <?php echo htmlspecialchars($lapangan['jam_mulai']); ?> - <?php echo htmlspecialchars($lapangan['jam_selesai']); ?></p>
            <a href="../booking/pemesanan.php?id=<?php echo $lapangan['id']; ?>" class="btn btn-primary">Pesan</a>
        </div>
        <div class="col-md-6">
            <div id="map" style="width: 100%; height: 400px;"></div>
        </div>
    </div>
</div>

<script>
    var lapanganLocation = { 
        lat: <?php echo $lapangan['latitude']; ?>, 
        lng: <?php echo $lapangan['longitude']; ?> 
    };
</script>
<script src="../assets/js/detail_lapangan.js"></script>

</body>
</html>
