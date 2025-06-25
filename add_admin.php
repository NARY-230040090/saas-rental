<?php
session_start();
require 'config/db.php';

// Hanya superadmin yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: login.php");
    exit;
}

$success = "";
$error = "";

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek duplikat username
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Username sudah terdaftar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $success = "Admin baru berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan admin.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Admin Baru</h2>

        <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" name="submit">Tambah Admin</button>
        </form>

        <p><a href="dashboard_superadmin.php">⬅️ Kembali ke Dashboard</a></p>
    </div>
</body>
</html>
