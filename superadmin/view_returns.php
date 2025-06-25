<?php
session_start();
require '../config/db.php';

// Cek apakah user superadmin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit;
}

// Ambil daftar pengembalian
$returns = $conn->query("
    SELECT r.id, a.username AS admin, c.name AS customer, 
           v.brand, v.model, r.return_date, r.condition_notes
    FROM returns r
    JOIN bookings b ON r.booking_id = b.id
    JOIN users a ON b.admin_id = a.id
    JOIN customers c ON b.customer_id = c.id
    JOIN vehicles v ON b.vehicle_id = v.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lihat Pengembalian</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #f0f0f0;
        }

        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔄 Riwayat Pengembalian Kendaraan</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Admin</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Tanggal Kembali</th>
                <th>Catatan Kondisi</th>
            </tr>
            <?php if ($returns->num_rows > 0): ?>
                <?php while ($row = $returns->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['admin']) ?></td>
                    <td><?= htmlspecialchars($row['customer']) ?></td>
                    <td><?= htmlspecialchars($row['brand'] . ' ' . $row['model']) ?></td>
                    <td><?= $row['return_date'] ?></td>
                    <td><?= htmlspecialchars($row['condition_notes']) ?></td>
                </tr>
                <?php } ?>
            <?php else: ?>
                <tr><td colspan="6">Belum ada data pengembalian.</td></tr>
            <?php endif; ?>
        </table>

        <a href="../dashboard_superadmin.php" class="btn">⬅️ Kembali ke Dashboard</a>
    </div>
</body>
</html>
