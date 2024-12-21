<?php
require '../vendor/autoload.php'; // MongoDB PHP Library

use MongoDB\Client;

session_start();

// Koneksi ke MongoDB
$client = new Client("mongodb://localhost:27017");
$collection = $client->webberita->admins;

// Periksa apakah admin sudah login melalui cookie
if (isset($_COOKIE['admin_logged_in']) && $_COOKIE['admin_logged_in'] === 'true') {
    $_SESSION['admin_logged_in'] = true;
    header('Location: admin_dashboard.php');
    exit;
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // Hapus spasi tambahan
    $password = $_POST['password'];

    try {
        $admin = $collection->findOne(['username' => $username]);
        
        if ($admin) {
            $storedPassword = $admin['password']; // Ambil password dari dokumen
            if (password_verify($password, $storedPassword)) {
                // Login berhasil
                $_SESSION['admin_logged_in'] = true;
                setcookie('admin_logged_in', 'true', time() + (86400 * 1), "/");
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error_message = "Password salah.";
            }
        } else {
            $error_message = "Username tidak ditemukan.";
        }
    } catch (Exception $e) {
        $error_message = "Terjadi kesalahan saat mengakses database: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <style>
        body {
            background-color: #E9E9E9;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
        }

        .btn.btn-outline-dark:hover {
            background-color: #b61318;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px #333;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center fw-bold" style="color: #b61318">Login Admin</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div style="margin: 40px 0 ">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-dark">Login</button>
            </div>
        </form>
    </div>
</body>

</html>