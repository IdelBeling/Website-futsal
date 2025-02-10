<?php
session_start();
require_once '../config/database.php';

// Ambil data lapangan dari database
$query = "SELECT * FROM lapangan";
$result = $conn->query($query);
$lapangan_list = [];

while ($row = $result->fetch_assoc()) {
    $lapangan_list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Lapangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAGXj8RaWrcxpo8DHH9IthHfWsnt5sasjU&callback=initMap" async defer></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-center">Cari Lapangan Futsal</h2>
        <a href="http://localhost/ptifutsal/user/dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div id="map" style="width: 100%; height: 400px;"></div>
        </div>
        <div class="col-md-6">
            <ul class="list-group">
                <?php if (count($lapangan_list) > 0): ?>
                    <?php foreach ($lapangan_list as $lapangan): ?>
                        <li class="list-group-item">
                            <h5><?php echo htmlspecialchars($lapangan['nama']); ?></h5>
                            <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($lapangan['lokasi']); ?></p>
                            <p><strong>Harga:</strong> Rp <?php echo number_format($lapangan['harga_per_jam'], 0, ',', '.'); ?> / jam</p>
                            <p><strong>Jam Operasional:</strong> <?php echo htmlspecialchars($lapangan['jam_mulai']); ?> - <?php echo htmlspecialchars($lapangan['jam_selesai']); ?></p>
                            <a href="detail_lapangan.php?id=<?php echo $lapangan['id']; ?>" class="btn btn-primary btn-sm">Lihat Detail</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-center">Belum ada lapangan tersedia.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Variabel JavaScript dari PHP -->
<script>
    var lapanganList = <?php echo json_encode($lapangan_list, JSON_PRETTY_PRINT); ?>;
</script>
<script src="../assets/js/cari_lapangan.js"></script>

</body>
</html>
