<?php
require 'config.php';
checkAuth('dosen');

// --- FILTER & PENCARIAN ---
$filter = [];

// Filter berdasarkan pencarian
if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $keyword = $_GET['cari'];
    $filter['$or'] = [
        ['nama_santri' => new MongoDB\BSON\Regex($keyword, 'i')],
        ['surah' => new MongoDB\BSON\Regex($keyword, 'i')],
        ['muhaffizh' => new MongoDB\BSON\Regex($keyword, 'i')]
    ];
}

// Filter berdasarkan predikat/nilai
if (isset($_GET['predikat']) && !empty($_GET['predikat'])) {
    $filter['predikat'] = $_GET['predikat'];
}

// Filter berdasarkan tanggal
if (isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $tanggal = new DateTime($_GET['tanggal'], new DateTimeZone('Asia/Jakarta'));
    $tanggal->setTime(0, 0, 0);
    $tanggalAkhir = clone $tanggal;
    $tanggalAkhir->setTime(23, 59, 59);
    
    $filter['tanggal'] = [
        '$gte' => new MongoDB\BSON\UTCDateTime($tanggal),
        '$lte' => new MongoDB\BSON\UTCDateTime($tanggalAkhir)
    ];
}

// Ambil data dengan filter
$semuaData = $setoran->find($filter, ['sort' => ['tanggal' => -1], 'limit' => 100]);

// Hitung statistik
$totalSetoran = $setoran->countDocuments($filter);
$totalMumtaz = $setoran->countDocuments(array_merge($filter, ['predikat' => 'A']));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar-custom mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="fw-bold text-primary fs-4">Monitoring Tahfizh</span>
            <div>
                <span class="me-3 fw-bold">Dosen: <?= $_SESSION['username'] ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- STATISTIK CARD -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="glass-panel p-3 text-center">
                    <h5 class="fw-bold text-primary mb-0"><?= $totalSetoran ?></h5>
                    <small class="text-muted">Total Setoran</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-panel p-3 text-center">
                    <h5 class="fw-bold text-success mb-0"><?= $totalMumtaz ?></h5>
                    <small class="text-muted">Nilai A (Mumtaz)</small>
                </div>
            </div>
        </div>

        <div class="glass-panel p-4">
            <!-- HEADER & FILTER -->
            <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                <h4 class="fw-bold">📊 Laporan Perkembangan Santri</h4>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">🖨️ Cetak Laporan</button>
            </div>

            <!-- FORM PENCARIAN & FILTER -->
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="cari" class="form-control" 
                           placeholder="🔍 Cari santri/surah/muhaffizh..." 
                           value="<?= $_GET['cari'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <select name="predikat" class="form-select">
                        <option value="">Semua Nilai</option>
                        <option value="A" <?= (isset($_GET['predikat']) && $_GET['predikat']=='A')?'selected':'' ?>>A (Mumtaz)</option>
                        <option value="B" <?= (isset($_GET['predikat']) && $_GET['predikat']=='B')?'selected':'' ?>>B (Jayyid)</option>
                        <option value="C" <?= (isset($_GET['predikat']) && $_GET['predikat']=='C')?'selected':'' ?>>C (Maqbul)</option>
                        <option value="D" <?= (isset($_GET['predikat']) && $_GET['predikat']=='D')?'selected':'' ?>>D (Rasib)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="tanggal" class="form-control" 
                           value="<?= $_GET['tanggal'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                    <?php if(!empty($_GET)): ?>
                        <a href="dosen.php" class="btn btn-secondary w-100 mt-2">Reset</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- TABEL DATA -->
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Santri</th>
                            <th>Hafalan (Surah/Ayat)</th>
                            <th>Predikat</th>
                            <th>Penguji (Muhaffizh)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0;
                        foreach($semuaData as $d): 
                            $count++;
                            $tgl = $d['tanggal']->toDateTime()->format('d M Y, H:i');
                        ?>
                        <tr>
                            <td><?= $tgl ?></td>
                            <td class="fw-bold fs-5"><?= $d['nama_santri'] ?></td>
                            <td><?= $d['surah'] ?> <span class="text-muted small">(Ayat <?= $d['ayat'] ?>)</span></td>
                            <td>
                                <?php 
                                $bg = match($d['predikat']) {
                                    'A' => 'bg-success',
                                    'B' => 'bg-primary',
                                    'C' => 'bg-warning text-dark',
                                    'D' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                $label = match($d['predikat']) {
                                    'A' => 'Mumtaz',
                                    'B' => 'Jayyid',
                                    'C' => 'Maqbul',
                                    'D' => 'Rasib',
                                    default => $d['predikat']
                                };
                                ?>
                                <span class="badge <?= $bg ?> rounded-pill px-3"><?= $label ?></span>
                            </td>
                            <td class="text-muted"><?= $d['muhaffizh'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if($count == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada data yang sesuai dengan filter
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($count > 0): ?>
            <div class="text-muted small mt-3">
                Menampilkan <?= $count ?> dari <?= $totalSetoran ?> total data
            </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        @media print {
            .btn, form, nav { display: none !important; }
            .glass-panel { background: white !important; box-shadow: none !important; }
        }
    </style>
</body>
</html>