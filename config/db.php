<?php
// Konfigurasi koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'rentaldb';

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>
