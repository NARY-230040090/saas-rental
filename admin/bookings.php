<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user']['id'];

// Tambah pemesanan
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $booking_date = $_POST['booking_date'];
    $return_date = $_POST['return_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (admin_id, customer_id, vehicle_id, booking_date, return_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $admin_id, $customer_id, $vehicle_id, $booking_date, $return_date);
    $stmt->execute();

    $conn->query("UPDATE vehicles SET status = 'dipinjam' WHERE id = $vehicle_id");
    echo "<script>alert('✅ Pemesanan berhasil ditambahkan'); window.location='bookings.php';</script>";
}

// Ambil data
$customers = $conn->query("SELECT * FROM customers");
$vehicles = $conn->query("SELECT * FROM vehicles WHERE status = 'tersedia'");
$bookings = $conn->query("
    SELECT b.id, c.name AS customer, v.brand, v.model, b.booking_date, b.return_date
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN vehicles v ON b.vehicle_id = v.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pemesanan Kendaraan</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin-bottom: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
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

    <h2>📅 Form Pemesanan Kendaraan</h2>

    <form method="POST" class="form-container">
        <label>🧍 Pelanggan</label>
        <select name="customer_id" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php while ($row = $customers->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php } ?>
        </select>

        <label>🚗 Kendaraan</label>
        <select name="vehicle_id" required>
            <option value="">-- Pilih Kendaraan --</option>
            <?php while ($row = $vehicles->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['brand']) ?> <?= htmlspecialchars($row['model']) ?>
                </option>
            <?php } ?>
        </select>

        <label>📆 Tanggal Booking</label>
        <input type="date" name="booking_date" required>

        <label>📆 Tanggal Kembali</label>
        <input type="date" name="return_date" required>

        <button type="submit" name="submit">Simpan Pemesanan</button>
    </form>

    <h3>📋 Daftar Pemesanan</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Kendaraan</th>
            <th>Tanggal Booking</th>
            <th>Tanggal Kembali</th>
        </tr>
        <?php while ($row = $bookings->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['customer']) ?></td>
            <td><?= htmlspecialchars($row['brand']) ?> <?= htmlspecialchars($row['model']) ?></td>
            <td><?= $row['booking_date'] ?></td>
            <td><?= $row['return_date'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <br><a class="btn" href="../dashboard_admin.php">⬅️ Kembali ke Dashboard</a>

</body>
</html>
