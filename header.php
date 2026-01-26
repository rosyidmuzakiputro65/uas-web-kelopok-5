<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistem Tahfizh' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php if(isset($_SESSION['username'])): ?>
    <nav class="navbar navbar-expand-lg navbar-pro fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="#">
                <i class="fa-solid fa-quran me-2"></i>Sistem Tahfizh
            </a>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold text-dark"><?= e($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?></div>
                    <div class="small text-muted text-uppercase"><?= e($_SESSION['role']) ?></div>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
        </div>
    </nav>
    <div style="margin-top: 80px;"></div> <?php endif; ?>