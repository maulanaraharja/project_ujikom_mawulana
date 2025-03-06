<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profile_picture = "default.jpg"; // Default foto profil

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif (!preg_match('/^[A-Za-z ]+$/', $full_name)) {
        $error = "Nama lengkap hanya boleh berisi huruf dan spasi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } elseif (!preg_match('/^[A-Za-z0-9]+$/', $password)) {
        $error = "Password hanya boleh berisi huruf dan angka, tanpa simbol.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Periksa apakah email sudah terdaftar
        $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_email->bind_param('s', $email);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $error = "Email sudah digunakan, coba email lain.";
        } else {
            // Simpan ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $full_name, $email, $hashed_password, $profile_picture);
            $success = $stmt->execute() ? "Registrasi berhasil! Silakan login." : "Gagal mendaftar, coba lagi nanti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="register-container">
        <h1 class="welcome-text">Buat Akun</h1>
        <p class="text-muted">Kelola tugas harianmu dengan lebih efisien.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Masukkan ulang password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Daftar</button>
        </form>
        <p class="mt-3 text-muted">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>
