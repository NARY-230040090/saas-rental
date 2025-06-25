<?php
session_start();
require '../config/db.php';

// Hanya superadmin yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data kendaraan
$vehicles = $conn->query("SELECT * FROM vehicles");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Kendaraan</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>📄 Lihat Data Kendaraan</h2>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Jenis</th>
            <th>Merk</th>
            <th>Model</th>
            <th>Plat Nomor</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $vehicles->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= ucfirst($row['type']) ?></td>
            <td><?= htmlspecialchars($row['brand']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['license_plate']) ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <br><a href="../dashboard_superadmin.php">⬅️ Kembali ke Dashboard</a>
</body>
</html>
