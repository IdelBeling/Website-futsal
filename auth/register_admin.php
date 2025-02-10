<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah digunakan
    $checkEmail = $conn->prepare("SELECT id FROM admin WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan!'); window.location='register_admin.php';</script>";
    } else {
        // Insert data ke tabel admin
        $stmt = $conn->prepare("INSERT INTO admin (nama, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registrasi admin berhasil! Silakan login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan, coba lagi.');</script>";
        }

        $stmt->close();
    }

    $checkEmail->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Registrasi Admin</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar</button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

</body>
</html>
