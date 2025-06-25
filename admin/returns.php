<?php
session_start();
require '../config/db.php';

// Cek apakah user admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses pengembalian
if (isset($_POST['submit'])) {
    $booking_id = $_POST['booking_id'];
    $return_date = $_POST['return_date'];
    $condition_notes = $_POST['condition_notes'];

    // Simpan ke tabel returns
    $stmt = $conn->prepare("INSERT INTO returns (booking_id, return_date, condition_notes) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $booking_id, $return_date, $condition_notes);
    $stmt->execute();

    // Ambil vehicle_id dari booking
    $result = $conn->query("SELECT vehicle_id FROM bookings WHERE id = $booking_id");
    $vehicle = $result->fetch_assoc();
    $vehicle_id = $vehicle['vehicle_id'];

    // Update status kendaraan jadi tersedia
    $conn->query("UPDATE vehicles SET status = 'tersedia' WHERE id = $vehicle_id");

    echo "<script>alert('✅ Pengembalian berhasil disimpan'); window.location='returns.php';</script>";
}

// Ambil data booking yang belum dikembalikan
$bookings = $conn->query("
    SELECT b.id, c.name AS customer, v.brand, v.model
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN vehicles v ON b.vehicle_id = v.id
    WHERE b.id NOT IN (SELECT booking_id FROM returns)
");

// Ambil daftar pengembalian
$returns = $conn->query("
    SELECT r.id, c.name AS customer, v.brand, v.model, r.return_date, r.condition_notes
    FROM returns r
    JOIN bookings b ON r.booking_id = b.id
    JOIN customers c ON b.customer_id = c.id
    JOIN vehicles v ON b.vehicle_id = v.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengembalian Kendaraan</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        h2 {
            text-align: center;
        }
        .form-container {
            max-width: 600px;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        table th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<h2>🔄 Form Pengembalian Kendaraan</h2>

<form method="POST" class="form-container">
    <label>Pilih Pemesanan:</label>
    <select name="booking_id" required>
        <option value="">-- Pilih --</option>
        <?php while ($row = $bookings->fetch_assoc()) { ?>
            <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['customer']) ?> - <?= htmlspecialchars($row['brand']) ?> <?= htmlspecialchars($row['model']) ?>
            </option>
        <?php } ?>
    </select>

    <label>Tanggal Pengembalian:</label>
    <input type="date" name="return_date" required>

    <label>Catatan Kondisi:</label>
    <textarea name="condition_notes" rows="3"></textarea>

    <button type="submit" name="submit">Simpan Pengembalian</button>
</form>

<h3>📋 Riwayat Pengembalian</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Pelanggan</th>
        <th>Kendaraan</th>
        <th>Tanggal Kembali</th>
        <th>Catatan</th>
    </tr>
    <?php while ($row = $returns->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['customer']) ?></td>
        <td><?= htmlspecialchars($row['brand']) ?> <?= htmlspecialchars($row['model']) ?></td>
        <td><?= $row['return_date'] ?></td>
        <td><?= htmlspecialchars($row['condition_notes']) ?></td>
    </tr>
    <?php } ?>
</table>

<br><a class="btn" href="../dashboard_admin.php">⬅️ Kembali ke Dashboard</a>

</body>
</html>
