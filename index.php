<?php
require 'config.php';

if (isset($_POST['login'])) {
    $u = $users->findOne(['username' => $_POST['username']]);
    if ($u && password_verify($_POST['password'], $u['password'])) {
        $_SESSION['username'] = $u['username'];
        $_SESSION['role'] = $u['role'];
        $_SESSION['nama_lengkap'] = $u['nama_lengkap'];

        if ($u['role'] == 'admin') header("Location: admin.php");
        elseif ($u['role'] == 'muhaffizh') header("Location: muhaffizh.php");
        elseif ($u['role'] == 'dosen') header("Location: dosen.php");
        exit;
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="col-md-4 px-3">
        <div class="glass-panel p-5 text-center">
            <h2 class="fw-bold text-success mb-4">SISTEM TAHFIZH</h2>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" name="login" class="btn btn-tahfizh w-100 fw-bold">MASUK</button>
            </form>
        </div>
    </div>
</body>
</html>