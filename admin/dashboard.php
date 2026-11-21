<?php
define('APP_ROOT', '..');
require __DIR__ . '/../includes/auth.php';
require_role('admin');
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

$page_title = 'Ringkasan Admin';

// 1. Data Statistik Ringkas Utama
$stats = [
    ['Judul Buku', query_count($koneksi, 'SELECT COUNT(*) FROM buku'), 'fa-book'],
    ['Eksemplar Tersedia', query_count($koneksi, 'SELECT COALESCE(SUM(stok),0) FROM buku'), 'fa-cubes-stacked'],
    ['Pengajuan Pending', query_count($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE status='pending'"), 'fa-hourglass-half'],
    ['Sedang Dipinjam', query_count($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'"), 'fa-book-open-reader'],
    ['Denda Terkumpul', format_rupiah(query_count($koneksi, 'SELECT COALESCE(SUM(denda),0) FROM peminjaman')), 'fa-wallet']
];

// 2. Data Tren 7 Hari Terakhir (Real-Time dari DB)
$labels7Days = [];
$pinjamData = [];
$dendaData = [];
$userData = [];

for ($i = 6; $i >= 0; $i--) {
    $dateStr = date('Y-m-d', strtotime("-$i days"));
    $displayDate = date('d M', strtotime($dateStr));
    $labels7Days[] = $displayDate;

    // Pinjaman pada tanggal pengajuan ini
    $qPinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total, COALESCE(SUM(denda), 0) as denda FROM peminjaman WHERE tanggal_pengajuan = '$dateStr'");
    $rPinjam = mysqli_fetch_assoc($qPinjam);
    $pinjamData[] = (int) $rPinjam['total'];
    $dendaData[] = (int) $rPinjam['denda'];

    // Anggota baru terdaftar pada tanggal ini
    $qUser = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = '$dateStr' AND role = 'user'");
    $rUser = mysqli_fetch_assoc($qUser);
    $userData[] = (int) $rUser['total'];
}

// 3. Distribusi Status Peminjaman Real-Time (Donut Chart)
$statusMap = ['pending' => 0, 'dipinjam' => 0, 'dikembalikan' => 0, 'ditolak' => 0];
$qStatus = mysqli_query($koneksi, "SELECT status, COUNT(*) as c FROM peminjaman GROUP BY status");
while ($r = mysqli_fetch_assoc($qStatus)) {
    if (isset($statusMap[$r['status']])) {
        $statusMap[$r['status']] = (int) $r['c'];
    }
}
$statusValues = array_values($statusMap);

// 4. Data Top 4 Buku Paling Sering Dipinjam
$topBooks = mysqli_query($koneksi, "SELECT b.judul, b.penulis, b.cover, COUNT(p.id_peminjaman) as total_pinjam FROM buku b LEFT JOIN peminjaman p ON p.id_buku = b.id_buku GROUP BY b.id_buku ORDER BY total_pinjam DESC, b.judul ASC LIMIT 4");

require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <div>
        <p class="eyebrow">PUSAT ADMINISTRASI</p>
        <h2>Ringkasan operasional</h2>
    </div>
    <a class="btn secondary" href="tampilan_user.php"><i class="fa-solid fa-eye"></i> Lihat sebagai user</a>
</div>

<!-- Kartu Statistik Atas -->
<section class="stats">
    <?php foreach ($stats as [$label, $value, $icon]): ?>
        <article class="stat">
            <div class="stat-top">
                <span><?= e($label) ?></span>
                <i class="fa-solid <?= e($icon) ?> stat-accent-icon"></i>
            </div>
            <strong><?= e((string)$value) ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<!-- Panel Analisis & Grafik Real-Time Profesional -->
<section class="chart-dashboard-grid">
    <!-- Grafik Tren Utama (7 Hari Terakhir) -->
    <article class="panel chart-card-main">
        <div class="chart-head">
            <div>
                <p class="eyebrow">METRIK REAL-TIME</p>
                <h2>Aktivitas peminjaman & pertumbuhan</h2>
                <small class="text-muted">Volume pengajuan peminjaman, registrasi anggota baru, dan denda 7 hari terakhir.</small>
            </div>
            <div class="chart-legend-custom">
                <span class="legend-badge legend-pinjam"><i class="fa-solid fa-circle"></i> Peminjaman</span>
                <span class="legend-badge legend-user"><i class="fa-solid fa-circle"></i> Anggota Baru</span>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="trafficTrendChart"></canvas>
        </div>
    </article>

    <!-- Grafik Distribusi Status Peminjaman (Donut Chart) -->
    <article class="panel chart-card-side">
        <div class="chart-head">
            <div>
                <p class="eyebrow">STATUS TRANSAKSI</p>
                <h2>Distribusi sirkulasi</h2>
                <small class="text-muted">Komposisi status peminjaman buku saat ini.</small>
            </div>
        </div>
        <div class="donut-container">
            <canvas id="statusDonutChart"></canvas>
        </div>
        <div class="donut-meta-list">
            <div class="donut-meta-item">
                <span class="dot-status dot-pending"></span>
                <span>Pending (Butuh ACC)</span>
                <b><?= $statusMap['pending'] ?></b>
            </div>
            <div class="donut-meta-item">
                <span class="dot-status dot-dipinjam"></span>
                <span>Sedang Dipinjam</span>
                <b><?= $statusMap['dipinjam'] ?></b>
            </div>
            <div class="donut-meta-item">
                <span class="dot-status dot-dikembalikan"></span>
                <span>Sudah Kembali</span>
                <b><?= $statusMap['dikembalikan'] ?></b>
            </div>
            <div class="donut-meta-item">
                <span class="dot-status dot-ditolak"></span>
                <span>Ditolak</span>
                <b><?= $statusMap['ditolak'] ?></b>
            </div>
        </div>
    </article>
</section>

<!-- Koleksi Terpopuler & Akses Cepat -->
<section class="panel chart-bottom-panel">
    <div class="section-head">
        <div>
            <p class="eyebrow">SIRKULASI TERTINGGI</p>
            <h2>Buku paling banyak diminati</h2>
        </div>
        <a class="btn secondary" href="peminjaman.php"><i class="fa-solid fa-list-check"></i> Kelola semua peminjaman</a>
    </div>
    <div class="top-books-grid">
        <?php while ($b = mysqli_fetch_assoc($topBooks)): ?>
            <div class="top-book-card">
                <img src="../uploads/covers/<?= e($b['cover']) ?>" alt="Cover <?= e($b['judul']) ?>">
                <div class="top-book-info">
                    <h4><?= e($b['judul']) ?></h4>
                    <small><i class="fa-regular fa-user"></i> <?= e($b['penulis']) ?></small>
                    <div class="top-book-badge">
                        <i class="fa-solid fa-arrows-rotate"></i> <?= (int)$b['total_pinjam'] ?> kali dipinjam
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Line & Bar Chart: Tren Aktivitas 7 Hari
    const ctxTrend = document.getElementById('trafficTrendChart').getContext('2d');

    const gradientPinjam = ctxTrend.createLinearGradient(0, 0, 0, 300);
    gradientPinjam.addColorStop(0, 'rgba(139, 30, 45, 0.45)');
    gradientPinjam.addColorStop(1, 'rgba(139, 30, 45, 0.0)');

    const gradientUser = ctxTrend.createLinearGradient(0, 0, 0, 300);
    gradientUser.addColorStop(0, 'rgba(233, 196, 120, 0.45)');
    gradientUser.addColorStop(1, 'rgba(233, 196, 120, 0.0)');

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels7Days) ?>,
            datasets: [
                {
                    label: 'Peminjaman Buku',
                    data: <?= json_encode($pinjamData) ?>,
                    borderColor: '#A42436',
                    backgroundColor: gradientPinjam,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.38,
                    pointBackgroundColor: '#F2EFEA',
                    pointBorderColor: '#8B1E2D',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5
                },
                {
                    label: 'Anggota Baru',
                    data: <?= json_encode($userData) ?>,
                    borderColor: '#E9C478',
                    backgroundColor: gradientUser,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.38,
                    pointBackgroundColor: '#F2EFEA',
                    pointBorderColor: '#E9C478',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#141417',
                    titleColor: '#F2EFEA',
                    bodyColor: '#B4B0AC',
                    borderColor: '#3A3A45',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(58, 58, 69, 0.35)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#7E7A77',
                        font: { family: 'Arial', size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(58, 58, 69, 0.35)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#7E7A77',
                        stepSize: 1,
                        precision: 0,
                        font: { family: 'Arial', size: 11 }
                    }
                }
            }
        }
    });

    // 2. Donut Chart: Distribusi Status Transaksi
    const ctxStatus = document.getElementById('statusDonutChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Dipinjam', 'Dikembalikan', 'Ditolak'],
            datasets: [{
                data: <?= json_encode($statusValues) ?>,
                backgroundColor: [
                    '#E9C478', // Pending - Gold/Warning
                    '#94BEE8', // Dipinjam - Sky Blue/Info
                    '#7ED3A5', // Dikembalikan - Green/Success
                    '#F28B96'  // Ditolak - Rose/Danger
                ],
                borderColor: '#1A1A1F',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#141417',
                    titleColor: '#F2EFEA',
                    bodyColor: '#B4B0AC',
                    borderColor: '#3A3A45',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function (context) {
                            return ' ' + context.label + ': ' + context.raw + ' transaksi';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
