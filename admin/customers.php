<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Tambah pelanggan
if (isset($_POST['tambah'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $address);
    $stmt->execute();
    echo "<script>alert('✅ Pelanggan ditambahkan'); window.location='customers.php';</script>";
}

// Update pelanggan
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $address, $id);
    $stmt->execute();
    echo "<script>alert('✅ Pelanggan diperbarui'); window.location='customers.php';</script>";
}

// Cek jika ingin edit
$editMode = false;
$editData = [];

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
}

// Validasi sebelum hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $cek = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE customer_id = ?");
    $cek->bind_param("i", $id);
    $cek->execute();
    $result = $cek->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        echo "<script>alert('❌ Tidak bisa dihapus: masih ada pemesanan!'); window.location='customers.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('✅ Pelanggan dihapus'); window.location='customers.php';</script>";
}

// Ambil semua data
$customers = $conn->query("SELECT * FROM customers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pelanggan</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Kelola Pelanggan</h2>

    <form method="POST">
        <h3><?= $editMode ? 'Edit Pelanggan' : 'Tambah Pelanggan' ?></h3>
        <input type="hidden" name="id" value="<?= $editMode ? $editData['id'] : '' ?>">
        <input type="text" name="name" placeholder="Nama" required value="<?= $editMode ? htmlspecialchars($editData['name']) : '' ?>">
        <input type="text" name="phone" placeholder="Nomor HP" value="<?= $editMode ? htmlspecialchars($editData['phone']) : '' ?>">
        <textarea name="address" placeholder="Alamat"><?= $editMode ? htmlspecialchars($editData['address']) : '' ?></textarea>
        <button type="submit" name="<?= $editMode ? 'update' : 'tambah' ?>">
            <?= $editMode ? 'Perbarui' : 'Tambah' ?>
        </button>
        <?php if ($editMode): ?>
            <a href="customers.php">❌ Batal</a>
        <?php endif; ?>
    </form>

    <h3>Daftar Pelanggan</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>No HP</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $customers->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
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
