<?php
session_start();
require '../config/db.php';

// Cek apakah superadmin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit;
}

$customers = $conn->query("SELECT * FROM customers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lihat Data Pelanggan</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>👥 Daftar Pelanggan</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Nomor HP</th>
            <th>Alamat</th>
        </tr>
        <?php while ($row = $customers->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
        </tr>
        <?php } ?>
    </table>

    <br><a href="../dashboard_superadmin.php">⬅️ Kembali ke Dashboard</a>
</body>
</html>
