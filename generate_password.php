<?php
// Cek apakah ada input password dari form
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "<h3>Hasil Hash</h3>";
    echo "Password: <strong>" . htmlspecialchars($password) . "</strong><br>";
    echo "Hash: <strong>" . $hash . "</strong><br><br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Hash Password</title>
</head>
<body>
    <h2>Generate Hash Password Admin</h2>
    <form method="POST">
        <label>Masukkan Password:</label><br>
        <input type="text" name="password" required><br><br>
        <button type="submit">Generate</button>
    </form>
</body>
</html>
