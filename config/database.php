<?php
$host = "localhost";  
$user = "root";       
$pass = "";     
$dbname = "booking_futsal"; 


$conn = new mysqli($host, $user, $pass, $dbname);


if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
