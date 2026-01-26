<?php
// TAMBAHKAN 3 BARIS INI DI PALING ATAS (SEBELUM require)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
checkAuth('admin');

// --- LOGIC USER (Akun Login) ---
if (isset($_POST['add_user'])) {
    $users->insertOne([
        'username' => $_POST['username'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'role' => $_POST['role'],
        'nama_lengkap' => $_POST['nama_lengkap']
    ]);
}
if (isset($_GET['del_user'])) {
    $users->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['del_user'])]);
    header("Location: admin.php"); exit;
}

// --- LOGIC SANTRI (Semester Updated) ---
if (isset($_POST['add_santri'])) {
    $santri->insertOne([
        'nim' => $_POST['nim'],
        'nama' => $_POST['nama'],
        'semester' => $_POST['semester'] // Dulu Kelas, sekarang Semester
    ]);
}
if (isset($_GET['del_santri'])) {
    $santri->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['del_santri'])]);
    header("Location: admin.php"); exit;
}

$dataUser = $users->find([]);
$dataSantri = $santri->find([], ['sort' => ['nama' => 1]]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar-custom mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="fw-bold text-success fs-4">Admin Panel</span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="glass-panel p-4 h-100">
                    <h5 class="fw-bold text-success mb-3">👤 Kelola Akun</h5>
                    <form method="POST" class="mb-4">
                        <input type="text" name="nama_lengkap" class="form-control mb-2" placeholder="Nama Lengkap" required>
                        <input type="text" name="username" class="form-control mb-2" placeholder="Username Login" required>
                        <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                        <select name="role" class="form-select mb-3">
                            <option value="muhaffizh">Muhaffizh (Penguji)</option>
                            <option value="dosen">Dosen (Monitoring)</option>
                            <option value="admin">Admin</option>
                        </select>
                        <button type="submit" name="add_user" class="btn btn-tahfizh w-100">Buat Akun</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <?php foreach($dataUser as $u): ?>
                            <tr class="border-bottom">
                                <td>
                                    <strong><?= $u['nama_lengkap'] ?></strong><br>
                                    <small class="text-muted"><?= $u['role'] ?></small>
                                </td>
                                <td class="text-end align-middle">
                                    <?php if($u['username'] !== $_SESSION['username']): ?>
                                        <a href="admin.php?del_user=<?= $u['_id'] ?>" class="badge bg-danger text-decoration-none" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-4">
                <div class="glass-panel p-4 h-100">
                    <h5 class="fw-bold text-primary mb-3">🎓 Data Santri</h5>
                    
                    <form method="POST" class="row g-2 mb-4">
                        <div class="col-md-3"><input type="text" name="nim" class="form-control" placeholder="NIM" required></div>
                        <div class="col-md-5"><input type="text" name="nama" class="form-control" placeholder="Nama Santri" required></div>
                        <div class="col-md-2"><input type="text" name="semester" class="form-control" placeholder="Sem" required></div>
                        <div class="col-md-2"><button type="submit" name="add_santri" class="btn btn-primary w-100">Add</button></div>
                    </form>

                    <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
                        <table class="table table-custom table-hover">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Semester</th> <th></th> </tr>
                            </thead>
                            <tbody>
                                <?php foreach($dataSantri as $s): ?>
                                <tr>
                                    <td><?= $s['nim'] ?></td>
                                    <td class="fw-bold"><?= $s['nama'] ?></td>
                                    <td><?= $s['semester'] ?? '-' ?></td> <td class="text-end">
                                        <a href="admin.php?del_santri=<?= $s['_id'] ?>" 
                                           class="btn btn-danger btn-sm rounded-pill px-3 py-1" 
                                           style="font-size: 0.8rem;"
                                           onclick="return confirm('Hapus data santri ini?')">
                                           Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>