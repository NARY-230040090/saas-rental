<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Tambah kendaraan
if (isset($_POST['tambah'])) {
    $type = $_POST['type'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $license = $_POST['license'];

    $stmt = $conn->prepare("INSERT INTO vehicles (type, brand, model, license_plate) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $type, $brand, $model, $license);
    $stmt->execute();
    echo "<script>alert('✅ Kendaraan ditambahkan'); window.location='vehicles.php';</script>";
}

// Update kendaraan
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $license = $_POST['license'];

    $stmt = $conn->prepare("UPDATE vehicles SET type = ?, brand = ?, model = ?, license_plate = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $type, $brand, $model, $license, $id);
    $stmt->execute();
    echo "<script>alert('✅ Kendaraan diperbarui'); window.location='vehicles.php';</script>";
}

// Edit kendaraan
$editMode = false;
$editData = [];

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
}

// Hapus kendaraan (hanya jika belum dipinjam)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $cek = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE vehicle_id = ?");
    $cek->bind_param("i", $id);
    $cek->execute();
    $result = $cek->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        echo "<script>alert('❌ Tidak bisa hapus: kendaraan sedang atau pernah dipinjam!'); window.location='vehicles.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('✅ Kendaraan dihapus'); window.location='vehicles.php';</script>";
}

// Ambil semua data kendaraan
$vehicles = $conn->query("SELECT * FROM vehicles");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kendaraan</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Kelola Data Kendaraan</h2>

    <form method="POST">
        <h3><?= $editMode ? 'Edit Kendaraan' : 'Tambah Kendaraan' ?></h3>
        <input type="hidden" name="id" value="<?= $editMode ? $editData['id'] : '' ?>">

        <label>Jenis Kendaraan</label>
        <select name="type" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="mobil" <?= ($editMode && $editData['type'] == 'mobil') ? 'selected' : '' ?>>Mobil</option>
            <option value="sepeda" <?= ($editMode && $editData['type'] == 'sepeda') ? 'selected' : '' ?>>Sepeda</option>
        </select>

        <input type="text" name="brand" placeholder="Merek" required value="<?= $editMode ? htmlspecialchars($editData['brand']) : '' ?>">
        <input type="text" name="model" placeholder="Model" value="<?= $editMode ? htmlspecialchars($editData['model']) : '' ?>">
        <input type="text" name="license" placeholder="Nomor Plat" value="<?= $editMode ? htmlspecialchars($editData['license_plate']) : '' ?>">

        <button type="submit" name="<?= $editMode ? 'update' : 'tambah' ?>">
            <?= $editMode ? 'Perbarui' : 'Tambah' ?>
        </button>
        <?php if ($editMode): ?>
            <a href="vehicles.php">❌ Batal</a>
        <?php endif; ?>
    </form>

    <h3>Daftar Kendaraan</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Jenis</th>
            <th>Merek</th>
            <th>Model</th>
            <th>Plat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $vehicles->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['brand']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['license_plate']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>">✏️ Edit</a> |
                <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">🗑️ Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <br><a href="../dashboard_admin.php">⬅️ Kembali ke Dashboard</a>
</body>
</html>
