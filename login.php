<?php
session_start();



$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name']; // Simpan nama pengguna
            $_SESSION['profile_picture'] = $user['profile_picture']; // Simpan foto profil
            header('Location: index.php');
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}
$pesanHari = [
    "Monday" => "Selamat Hari Senin! Awali minggu dengan senyuman 😁",
    "Tuesday" => "Selamat Hari Selasa! Jadikan hari ini lebih baik dari kemarin 👌",
    "Wednesday" => "Selamat Hari Rabu! Istirahatlah sejenak, lalu lanjutkan perjuanganmu ☕",
    "Thursday" => "Selamat Hari Kamis! Semangat menjelang akhir pekan 🎉",
    "Friday" => "Selamat Hari Jumat! Besok libur eyy 😂",
    "Saturday" => "Selamat Hari Sabtu! Santai dulu ga sih 😎",
    "Sunday" => "Selamat Hari Minggu! Libur eyy 🤙"
];

$pesanHariIni = $pesanHari[date("l")];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Todo App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<div class="container mt-4">
        <div class="bg-message">
            <?= htmlspecialchars($pesanHariIni) ?>
        </div>

        <div class="container description">
            <h4>Selamat datang di To-Do List!</h4>
            <p>Kelola tugas harianmu dengan lebih mudah dan efisien.</p>
        </div>

        <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
            <div class="login-container">
                <h2>Login</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                    <p class="mt-2 text-center text-muted">Belum punya akun? <a href="register.php">Daftar</a></p>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var icon = document.querySelector('.toggle-password i');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = "password";
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>