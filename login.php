<?php
session_start();
require 'config/db.php'; // koneksi database

// Tes koneksi (opsional)
if (!$conn) {
    die("Koneksi database gagal!");
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            if ($user['role'] === 'superadmin') {
                header("Location: dashboard_superadmin.php");
            } else {
                header("Location: dashboard_admin.php");
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - SAAS RENTAL</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page"> <!-- Biar CSS login aktif -->
    <div class="login-container">
        <h2>Login</h2>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="login">Masuk</button>
        </form>

        <?php if (isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
    </div>
</body>
</html>
