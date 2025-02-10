<?php
session_start();
require_once '../config/database.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Silakan login sebagai admin!'); window.location='../auth/login.php';</script>";
    exit;
}

// Proses tambah lapangan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_lapangan'])) {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi']; // Ambil input lokasi
    $harga_per_jam = $_POST['harga_per_jam'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $jam_operasional = $_POST['jam_operasional'];

    // Hitung waktu tutup (otomatis +12 jam dari jam operasional)
    $waktu_tutup = date("H:i", strtotime($jam_operasional) + 43200); // 43200 detik = 12 jam

    $query = "INSERT INTO lapangan (nama, lokasi, harga_per_jam, latitude, longitude, jam_mulai, jam_selesai) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssddsss", $nama, $lokasi, $harga_per_jam, $latitude, $longitude, $jam_operasional, $waktu_tutup);

    if ($stmt->execute()) {
        echo "<script>alert('Lapangan berhasil ditambahkan!'); window.location='manage_lapangan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi!');</script>";
    }
}

// Ambil daftar lapangan
$query = "SELECT * FROM lapangan ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lapangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAGXj8RaWrcxpo8DHH9IthHfWsnt5sasjU&callback=initMap" async defer></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Kelola Lapangan</h2>
        <a href="http://localhost/ptifutsal/admin/dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <h4 class="mt-4">Tambah Lapangan</h4>
    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Nama Lapangan</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pilih Lokasi di Peta</label>
            <div id="map" style="width: 100%; height: 400px;"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Latitude</label>
            <input type="text" name="latitude" id="latitude" class="form-control" required readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Longitude</label>
            <input type="text" name="longitude" id="longitude" class="form-control" required readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga Per Jam</label>
            <input type="number" name="harga_per_jam" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jam Operasional</label>
            <select name="jam_operasional" id="jam_operasional" class="form-control" required onchange="setWaktuTutup()">
                <option value="">Pilih Jam Operasi</option>
                <option value="08:00">08:00</option>
                <option value="09:00">09:00</option>
                <option value="10:00">10:00</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Waktu Tutup</label>
            <input type="text" id="waktu_tutup" name="waktu_tutup" class="form-control" readonly>
        </div>
        <button type="submit" name="tambah_lapangan" class="btn btn-primary">Tambah Lapangan</button>
    </form>
</div>

<script>
function initMap() {
    var defaultLocation = { lat: -6.914744, lng: 107.609810 }; // Default Bandung
    var map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 12
    });

    var marker = new google.maps.Marker({
        position: defaultLocation,
        map: map
    });

    // Set latitude & longitude awal
    document.getElementById('latitude').value = defaultLocation.lat;
    document.getElementById('longitude').value = defaultLocation.lng;

    // Pindahkan marker ke lokasi yang diklik
    map.addListener("click", function(event) {
        var clickedLocation = event.latLng;
        marker.setPosition(clickedLocation);
        document.getElementById('latitude').value = clickedLocation.lat();
        document.getElementById('longitude').value = clickedLocation.lng();
    });
}

// Fungsi untuk mengatur waktu tutup otomatis (+12 jam dari jam operasional)
function setWaktuTutup() {
    var jamOperasional = document.getElementById('jam_operasional').value;
    if (jamOperasional) {
        var waktuTutup = new Date("2023-01-01 " + jamOperasional);
        waktuTutup.setHours(waktuTutup.getHours() + 12);
        document.getElementById('waktu_tutup').value = waktuTutup.toTimeString().split(" ")[0].substring(0, 5);
    } else {
        document.getElementById('waktu_tutup').value = "";
    }
}
</script>

</body>
</html>
