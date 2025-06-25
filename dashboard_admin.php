<?php
session_start();

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin: 0;
        }

        main {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            margin: 15px 0;
        }

        ul li a {
            display: block;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        ul li a:hover {
            background-color: #2980b9;
        }

        .logout {
            display: inline-block;
            margin-top: 20px;
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<header>
    <h2>Selamat Datang, Admin <?= htmlspecialchars($username) ?>!</h2>
</header>

<main>
    <ul>
        <li><a href="admin/customers.php">👥 Kelola Pelanggan</a></li>
        <li><a href="admin/vehicles.php">🚗 Kelola Kendaraan</a></li>
        <li><a href="admin/bookings.php">📅 Pemesanan</a></li>
        <li><a href="admin/returns.php">🔁 Pengembalian</a></li>
    </ul>

    <a class="logout" href="logout.php">🚪 Logout</a>
</main>

</body>
</html>
