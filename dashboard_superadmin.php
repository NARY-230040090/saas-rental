<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Superadmin</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Pastikan path sesuai -->
    <style>
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 40px 20px;
        }

        .dashboard-title {
            margin-bottom: 30px;
            font-size: 24px;
            color: #333;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            width: 200px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card a {
            display: block;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .logout {
            margin-top: 30px;
        }

        .logout a {
            color: #ff4444;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2 class="dashboard-title">Halo, Superadmin <?= htmlspecialchars($username) ?> 👑</h2>

        <div class="dashboard-cards">
            <div class="card"><a href="add_admin.php">➕ Tambah Admin</a></div>
            <div class="card"><a href="superadmin/view_customers.php">👥 Lihat Pelanggan</a></div>
            <div class="card"><a href="superadmin/view_vehicles.php">🚗 Lihat Kendaraan</a></div>
            <div class="card"><a href="superadmin/view_bookings.php">📅 Lihat Pemesanan</a></div>
            <div class="card"><a href="superadmin/view_returns.php">🔄 Lihat Pengembalian</a></div>
        </div>

        <div class="logout">
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
</body>
</html>
