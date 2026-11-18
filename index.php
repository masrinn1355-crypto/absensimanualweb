<?php
session_start();

// Menyimpan nama user dari index.html jika baru login
if (isset($_POST['login']) && !empty($_POST['username'])) {
    $_SESSION['user'] = htmlspecialchars(trim($_POST['username']));
}
$currentUser = $_SESSION['user'] ?? 'Guru / Admin';

// Inisialisasi daftar siswa default sesuai gambar
if (!isset($_SESSION['siswa'])) {
    $_SESSION['siswa'] = [
        1 => 'JENNIVER ANDERSON',
        2 => 'ALEX MCWEEN',
        3 => 'JULIUS FORK',
        4 => 'TOMMY MAXIMOFF',
        5 => 'BILLY MAXIMOFF'
    ];
}

// Inisialisasi penyimpanan data absensi
if (!isset($_SESSION['absensi'])) {
    $_SESSION['absensi'] = [];
}

// Menentukan tanggal yang sedang dipilih
$tanggal = $_POST['tanggal'] ?? ($_GET['tanggal'] ?? date('Y-m-d'));

// Proses Tambah Siswa Baru
if (isset($_POST['tambah_siswa'])) {
    $namaBaru = strtoupper(trim($_POST['nama_baru']));
    if (!empty($namaBaru)) {
        $_SESSION['siswa'][] = htmlspecialchars($namaBaru);
    }
}

// Proses Hapus Siswa
if (isset($_POST['hapus_siswa'])) {
    $idHapus = (int)$_POST['hapus_siswa'];
    unset($_SESSION['siswa'][$idHapus]);
}

// Proses Simpan Absensi & Edit Nama Siswa
if (isset($_POST['simpan_absensi'])) {
    // Update nama siswa jika diedit
    if (isset($_POST['nama_siswa']) && is_array($_POST['nama_siswa'])) {
        foreach ($_POST['nama_siswa'] as $id => $namaEdit) {
            $namaBersih = strtoupper(trim($namaEdit));
            if (!empty($namaBersih) && isset($_SESSION['siswa'][$id])) {
                $_SESSION['siswa'][$id] = htmlspecialchars($namaBersih);
            }
        }
    }

    // Simpan status kehadiran berdasarkan tanggal
    if (isset($_POST['status']) && is_array($_POST['status'])) {
        $_SESSION['absensi'][$tanggal] = $_POST['status'];
    }
}

$dataHariIni = $_SESSION['absensi'][$tanggal] ?? null;
$bulanIni = date('Y-m', strtotime($tanggal));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Kelas - Fireburn OS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="desktop-bg">

    <div class="win98-window main-window">
        <!-- Title Bar ala Windows 98 -->
        <div class="win98-titlebar">
            <span>📋 Sistem Absensi Kelas — [User: <?= $currentUser ?>]</span>
            <a href="index.html" class="win98-close-btn" title="Keluar ke Login">X</a>
        </div>

        <div class="win98-body">
            <!-- Header Judul -->
            <div class="app-header">
                <span class="fire-mini">🔥</span>
                <h2>Sistem Absensi Kelas</h2>
            </div>

            <!-- Panel Tambah Siswa -->
            <form method="POST" action="index.php" class="win98-panel">
                <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                <div class="inline-form">
                    <input type="text" name="nama_baru" class="win98-input" placeholder="Ketik nama siswa baru...">
                    <button type="submit" name="tambah_siswa" class="win98-btn btn-primary">+ Tambah Siswa</button>
                </div>
            </form>

            <!-- Panel Pilih Tanggal -->
            <form method="GET" action="index.php" class="win98-panel">
                <div class="inline-form">
                    <label for="tanggal"><strong>Tanggal Absen:</strong></label>
                    <input type="date" id="tanggal" name="tanggal" class="win98-input date-input" value="<?= htmlspecialchars($tanggal) ?>" onchange="this.form.submit()">
                </div>
            </form>

            <!-- Form Utama Tabel Absensi -->
            <form method="POST" action="index.php">
                <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                
                <div class="table-container">
                    <table class="win98-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Siswa (Klik nama untuk edit)</th>
                                <th width="70">Hadir</th>
                                <th width="70">Sakit</th>
                                <th width="70">Izin</th>
                                <th width="70">Alpa</th>
                                <th width="90">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($_SESSION['siswa'] as $id => $nama): 
                                $statusTerpilih = $dataHariIni[$id] ?? 'Hadir';
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <input type="text" name="nama_siswa[<?= $id ?>]" value="<?= htmlspecialchars($nama) ?>" class="win98-input table-input">
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="status[<?= $id ?>]" value="Hadir" <?= $statusTerpilih === 'Hadir' ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="status[<?= $id ?>]" value="Sakit" <?= $statusTerpilih === 'Sakit' ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="status[<?= $id ?>]" value="Izin" <?= $statusTerpilih === 'Izin' ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="status[<?= $id ?>]" value="Alpa" <?= $statusTerpilih === 'Alpa' ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <button type="submit" name="hapus_siswa" value="<?= $id ?>" class="win98-btn btn-danger" onclick="return confirm('Hapus siswa ini?')">Hapus</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="save-bar">
                    <button type="submit" name="simpan_absensi" class="win98-btn btn-primary">💾 Simpan Absensi & Nama</button>
                </div>
            </form>

            <hr class="win98-divider">

            <!-- Rekap Harian -->
            <div class="rekap-section">
                <h3>📊 Rekap Harian (Tanggal: <?= date('d-m-Y', strtotime($tanggal)) ?>)</h3>
                
                <?php if ($dataHariIni === null): ?>
                    <div class="win98-alert">
                        ⚠️ Absensi tanggal ini belum disimpan. Klik tombol <strong>Simpan Absensi</strong> di atas agar rekap harian muncul.
                    </div>
                <?php else: 
                    $count = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
                    foreach ($_SESSION['siswa'] as $id => $nama) {
                        $st = $dataHariIni[$id] ?? 'Alpa';
                        if (isset($count[$st])) $count[$st]++;
                    }
                ?>
                    <div class="rekap-grid">
                        <div class="rekap-box"><strong>Hadir:</strong> <?= $count['Hadir'] ?> Siswa</div>
                        <div class="rekap-box"><strong>Sakit:</strong> <?= $count['Sakit'] ?> Siswa</div>
                        <div class="rekap-box"><strong>Izin:</strong> <?= $count['Izin'] ?> Siswa</div>
                        <div class="rekap-box"><strong>Alpa:</strong> <?= $count['Alpa'] ?> Siswa</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rekap Bulanan -->
            <div class="rekap-section">
                <h3>📅 Rekap Bulanan (<?= date('F Y', strtotime($tanggal)) ?>)</h3>
                <div class="table-container">
                    <table class="win98-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th width="80">Hadir</th>
                                <th width="80">Sakit</th>
                                <th width="80">Izin</th>
                                <th width="80">Alpa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $noB = 1;
                            foreach ($_SESSION['siswa'] as $id => $nama): 
                                $bHadir = $bSakit = $bIzin = $bAlpa = 0;
                                foreach ($_SESSION['absensi'] as $tglKey => $dataTgl) {
                                    if (strpos($tglKey, $bulanIni) === 0 && isset($dataTgl[$id])) {
                                        if ($dataTgl[$id] === 'Hadir') $bHadir++;
                                        elseif ($dataTgl[$id] === 'Sakit') $bSakit++;
                                        elseif ($dataTgl[$id] === 'Izin') $bIzin++;
                                        elseif ($dataTgl[$id] === 'Alpa') $bAlpa++;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="text-center"><?= $noB++ ?></td>
                                <td><?= htmlspecialchars($nama) ?></td>
                                <td class="text-center"><?= $bHadir ?></td>
                                <td class="text-center"><?= $bSakit ?></td>
                                <td class="text-center"><?= $bIzin ?></td>
                                <td class="text-center"><?= $bAlpa ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>