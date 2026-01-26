<?php
require 'config.php';
checkAuth('muhaffizh');

date_default_timezone_set('Asia/Jakarta');

// --- LOGIC EDIT ---
if (isset($_POST['update'])) {
    $setoran->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_POST['edit_id'])],
        ['$set' => [
            'nama_santri' => $_POST['nama_santri'],
            'surah' => $_POST['surah'],
            'ayat' => $_POST['ayat'],
            'predikat' => $_POST['predikat']
        ]]
    );
    $pesan = "Data berhasil diupdate!";
}

// --- LOGIC HAPUS ---
if (isset($_GET['hapus_id'])) {
    $setoran->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['hapus_id'])]);
    header("Location: muhaffizh.php"); 
    exit;
}

// --- LOGIC SIMPAN BARU ---
if (isset($_POST['simpan'])) {
    $setoran->insertOne([
        'nama_santri' => $_POST['nama_santri'], 
        'surah'       => $_POST['surah'],
        'ayat'        => $_POST['ayat'],
        'predikat'    => $_POST['predikat'],
        'muhaffizh'   => $_SESSION['username'],
        'tanggal'     => new MongoDB\BSON\UTCDateTime()
    ]);
    $pesan = "Data berhasil disimpan!";
}

// --- PENCARIAN ---
$filter = ['muhaffizh' => $_SESSION['username']];
if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $keyword = $_GET['cari'];
    $filter['$or'] = [
        ['nama_santri' => new MongoDB\BSON\Regex($keyword, 'i')],
        ['surah' => new MongoDB\BSON\Regex($keyword, 'i')]
    ];
}

// AMBIL DATA
$listSantri = $santri->find([], ['sort' => ['nama' => 1]]); 
$riwayatSaya = $setoran->find($filter, ['limit' => 50, 'sort' => ['tanggal' => -1]]);

// Data untuk edit (jika ada parameter edit_id)
$dataEdit = null;
if (isset($_GET['edit_id'])) {
    $dataEdit = $setoran->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['edit_id'])]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel Muhaffizh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar-custom mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="fw-bold text-success fs-4">Muhaffizh: <?= $_SESSION['username'] ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if(isset($pesan)) echo "<div class='alert alert-success fw-bold'>$pesan</div>"; ?>
        
        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="glass-panel p-4">
                    <h4 class="fw-bold mb-4">
                        <?= $dataEdit ? '✏️ Edit Setoran' : '📖 Input Setoran' ?>
                    </h4>
                    <form method="POST">
                        <?php if($dataEdit): ?>
                            <input type="hidden" name="edit_id" value="<?= $_GET['edit_id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Nama Santri</label>
                            <select name="nama_santri" class="form-select" required>
                                <option value="">-- Pilih Santri --</option>
                                <?php foreach($listSantri as $m): ?>
                                    <option value="<?= $m['nama'] ?>" 
                                        <?= ($dataEdit && $dataEdit['nama_santri'] == $m['nama']) ? 'selected' : '' ?>>
                                        <?= $m['nama'] ?> (<?= $m['nim'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Surah</label>
                            <input type="text" name="surah" class="form-control" 
                                   placeholder="Contoh: An-Naba" 
                                   value="<?= $dataEdit['surah'] ?? '' ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="fw-bold small text-muted">Ayat</label>
                                <input type="text" name="ayat" class="form-control" 
                                       placeholder="1-10" 
                                       value="<?= $dataEdit['ayat'] ?? '' ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="fw-bold small text-muted">Nilai</label>
                                <select name="predikat" class="form-select text-center fw-bold">
                                    <option value="A" <?= ($dataEdit && $dataEdit['predikat']=='A')?'selected':'' ?>>A (Mumtaz)</option>
                                    <option value="B" <?= ($dataEdit && $dataEdit['predikat']=='B')?'selected':'' ?>>B (Jayyid)</option>
                                    <option value="C" <?= ($dataEdit && $dataEdit['predikat']=='C')?'selected':'' ?>>C (Maqbul)</option>
                                    <option value="D" <?= ($dataEdit && $dataEdit['predikat']=='D')?'selected':'' ?>>D (Rasib)</option>
                                </select>
                            </div>
                        </div>

                        <?php if($dataEdit): ?>
                            <button type="submit" name="update" class="btn btn-warning w-100 fw-bold">Update Data</button>
                            <a href="muhaffizh.php" class="btn btn-secondary w-100 mt-2">Batal</a>
                        <?php else: ?>
                            <button type="submit" name="simpan" class="btn btn-tahfizh w-100">Simpan Data</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="glass-panel p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-secondary">🕒 Riwayat Inputan</h5>
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="cari" class="form-control form-control-sm" 
                                   placeholder="🔍 Cari santri/surah..." 
                                   value="<?= $_GET['cari'] ?? '' ?>" style="width: 200px;">
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                            <?php if(isset($_GET['cari'])): ?>
                                <a href="muhaffizh.php" class="btn btn-secondary btn-sm">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Santri</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($riwayatSaya as $r): 
                                    $dt = $r['tanggal']->toDateTime();
                                    $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                    $waktu = $dt->format('d/m H:i');
                                ?>
                                <tr>
                                    <td><small><?= $waktu ?></small></td>
                                    <td class="fw-bold">
                                        <?= $r['nama_santri'] ?>
                                        <div class="small text-muted fw-normal" style="font-size: 0.75rem;">
                                            <?= $r['surah'] ?>:<?= $r['ayat'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeColor = match($r['predikat']) {
                                                'A' => 'bg-success',
                                                'B' => 'bg-primary',
                                                'C' => 'bg-warning text-dark',
                                                'D' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeColor ?> rounded-circle" style="width:25px; height:25px; line-height:18px;">
                                            <?= $r['predikat'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="muhaffizh.php?edit_id=<?= $r['_id'] ?>" 
                                           class="btn btn-warning btn-sm py-0 px-2 rounded-pill me-1" 
                                           style="font-size: 0.7rem;">
                                           Edit
                                        </a>
                                        <a href="muhaffizh.php?hapus_id=<?= $r['_id'] ?>" 
                                           class="btn btn-danger btn-sm py-0 px-2 rounded-pill" 
                                           style="font-size: 0.7rem;"
                                           onclick="return confirm('Yakin ingin menghapus data ini?')">
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