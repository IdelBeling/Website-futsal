<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Simpan password dalam bentuk teks biasa

    if (empty($email) || empty($password)) {
        echo "<script>alert('Email dan password tidak boleh kosong!'); window.location='login.php';</script>";
        exit;
    }

    // Cek login sebagai user
    $stmt_user = $conn->prepare("SELECT id, nama, password FROM user WHERE email = ?");
    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    // Cek login sebagai admin
    $stmt_admin = $conn->prepare("SELECT id, nama, password FROM admin WHERE email = ?");
    $stmt_admin->bind_param("s", $email);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_user->num_rows > 0) {
        // Login sebagai user
        $row = $result_user->fetch_assoc();
        if ($password === $row['password']) { // Langsung bandingkan teks password
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nama'] = $row['nama'];
            $_SESSION['role'] = 'user';
            echo "<script>alert('Login berhasil sebagai User!'); window.location='../user/dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
        }
    } elseif ($result_admin->num_rows > 0) {
        // Login sebagai admin
        $row = $result_admin->fetch_assoc();
        if ($password === $row['password']) { // Langsung bandingkan teks password
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_nama'] = $row['nama'];
            $_SESSION['role'] = 'admin';
            echo "<script>alert('Login berhasil sebagai Admin!'); window.location='../admin/dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Login</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3">Belum punya akun? <a href="register_user.php">Daftar</a></p>
        </div>
    </div>
</div>

</body>
</html>
