<?php
$totalHarian = 0;
$totalMingguan = 0;
$pendapatanMingguan = 0;
$menuPopulerHariIni = "Belum ada data";
$menuPopulerMingguIni = "Belum ada data";
$labels = [];
$dataValues = [];
$labelsMingguan = [];
$dataValuesMingguan = [];

if (!isset($conn)) {
    include "Database/connect.php";
}

date_default_timezone_set("Asia/Jakarta");

$todayStart = date('Y-m-d 00:00:00');
$tomorrowStart = date('Y-m-d 00:00:00', strtotime('+1 day'));
$weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));
$weekEnd = $tomorrowStart;

function fetchSingleValue($conn, $query, $default = 0)
{
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return $default;
    }

    $row = mysqli_fetch_assoc($result);
    return $row ? ($row['value'] ?? $default) : $default;
}

function fetchPopularVehicle($conn, $startDate, $endDate)
{
    $query = "
        SELECT jenis_Kendaraan, COUNT(*) AS total
        FROM tb_order
        WHERE waktu_order >= '$startDate' AND waktu_order < '$endDate'
        GROUP BY jenis_Kendaraan
        ORDER BY total DESC, jenis_Kendaraan ASC
        LIMIT 1
    ";
    $result = mysqli_query($conn, $query);
    if (!$result || mysqli_num_rows($result) === 0) {
        return "Belum ada data";
    }

    $row = mysqli_fetch_assoc($result);
    return $row['jenis_Kendaraan'] . " (" . number_format((int) $row['total']) . " transaksi)";
}

$totalHarian = (int) fetchSingleValue($conn, "
    SELECT COUNT(*) AS value
    FROM tb_order
    WHERE waktu_order >= '$todayStart' AND waktu_order < '$tomorrowStart'
");

$totalMingguan = (int) fetchSingleValue($conn, "
    SELECT COUNT(*) AS value
    FROM tb_order
    WHERE waktu_order >= '$weekStart' AND waktu_order < '$weekEnd'
");

$pendapatanMingguan = (float) fetchSingleValue($conn, "
    SELECT COALESCE(SUM(tb_bayar.jumlah_bayar), 0) AS value
    FROM tb_order
    INNER JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
    WHERE tb_order.waktu_order >= '$weekStart' AND tb_order.waktu_order < '$weekEnd'
");

$menuPopulerHariIni = fetchPopularVehicle($conn, $todayStart, $tomorrowStart);
$menuPopulerMingguIni = fetchPopularVehicle($conn, $weekStart, $weekEnd);

$dailyChartQuery = mysqli_query($conn, "
    SELECT jenis_Kendaraan, COUNT(*) AS total
    FROM tb_order
    WHERE waktu_order >= '$todayStart' AND waktu_order < '$tomorrowStart'
    GROUP BY jenis_Kendaraan
    ORDER BY total DESC, jenis_Kendaraan ASC
");
if ($dailyChartQuery) {
    while ($row = mysqli_fetch_assoc($dailyChartQuery)) {
        $labels[] = $row['jenis_Kendaraan'];
        $dataValues[] = (int) $row['total'];
    }
}

$weeklyData = [];
$weeklyChartQuery = mysqli_query($conn, "
    SELECT DATE(waktu_order) AS tanggal, COUNT(*) AS total
    FROM tb_order
    WHERE waktu_order >= '$weekStart' AND waktu_order < '$weekEnd'
    GROUP BY DATE(waktu_order)
    ORDER BY tanggal ASC
");
if ($weeklyChartQuery) {
    while ($row = mysqli_fetch_assoc($weeklyChartQuery)) {
        $weeklyData[$row['tanggal']] = (int) $row['total'];
    }
}

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labelsMingguan[] = date('d M', strtotime($date));
    $dataValuesMingguan[] = $weeklyData[$date] ?? 0;
}

if (array_sum($dataValuesMingguan) === 0) {
    $labelsMingguan = [];
    $dataValuesMingguan = [];
}
?>

<style>
    .home-dashboard {
        background:
            linear-gradient(180deg, #e7f6ff 0%, #f5fbff 48%, #ffffff 100%);
    }

    .home-hero {
        position: relative;
        overflow: hidden;
        padding: 2.75rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #063a7a 0%, #0b5ed7 54%, #0dcaf0 100%);
        color: #fff;
        box-shadow: 0 20px 50px rgba(8, 66, 152, 0.18);
    }

    .home-hero::before {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 8px;
        background: linear-gradient(90deg, #9ee7ff 0%, #ffffff 50%, #9ee7ff 100%);
        opacity: .75;
    }

    .home-hero-content,
    .home-hero-panel {
        position: relative;
        z-index: 1;
    }

    .home-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        font-size: .85rem;
        letter-spacing: .04em;
    }

    .home-title {
        max-width: 680px;
        font-size: clamp(2rem, 4vw, 3.35rem);
        line-height: 1.1;
    }

    .home-subtitle {
        max-width: 600px;
        color: rgba(255, 255, 255, 0.84);
        font-size: 1rem;
    }

    .home-hero-panel {
        padding: 1.5rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .home-stat-card,
    .home-chart-card,
    .home-feature-card {
        border: 0;
        border-radius: 8px;
        box-shadow: 0 16px 38px rgba(8, 66, 152, 0.08);
    }

    .home-stat-card {
        background: #fff;
    }

    .home-stat-icon {
        width: 52px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.2rem;
    }

    .home-chart-card {
        background: linear-gradient(180deg, #ffffff 0%, #f5fbff 100%);
    }

    .home-chart-wrap {
        position: relative;
        height: 320px;
    }

    .home-feature-card {
        height: 100%;
        background: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .home-feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 46px rgba(8, 66, 152, 0.12);
    }

    .home-feature-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.4rem;
        background: linear-gradient(135deg, #e7f6ff 0%, #9ee7ff 100%);
        color: #084298;
    }

    .home-footer {
        color: #6c757d;
        font-size: .95rem;
    }

    @media (max-width: 767.98px) {
        .home-hero {
            padding: 2rem;
            border-radius: 12px;
        }
    }
</style>

<div class="container-fluid home-dashboard min-vh-100 py-4 px-3 px-md-4">
    <section class="home-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7 home-hero-content">
                <span class="home-kicker mb-3">
                    <i class="bi bi-droplet-half"></i>
                    Dashboard Carwash
                </span>
                <h1 class="home-title fw-bold mb-3">Kelola transaksi cuci kendaraan dengan tampilan biru yang bersih.</h1>
                <p class="home-subtitle mb-4">
                    Pantau antrian cuci, transaksi, dan performa operasional dari satu halaman utama.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="order" class="btn btn-light btn-lg px-4 text-primary fw-bold shadow-sm">
                        <i class="bi bi-car-front-fill me-2"></i>Mulai Transaksi
                    </a>
                    <a href="menu" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-droplet-half me-2"></i>Lihat Layanan
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="home-hero-panel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-uppercase small mb-2 text-white-50">Highlight Hari Ini</p>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($menuPopulerHariIni) ?></h4>
                            <p class="mb-0 text-white-50">Ringkasan operasional harian akan tampil otomatis saat data aktif.</p>
                        </div>
                        <span class="badge rounded-pill text-bg-light text-primary px-3 py-2">
                            <i class="bi bi-water me-1"></i>Aktif
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded bg-white text-dark h-100">
                                <div class="small text-muted mb-1">Total kendaraan hari ini</div>
                                <div class="fs-3 fw-bold"><?= number_format($totalHarian) ?></div>
                                <div class="small text-muted">kendaraan selesai dicuci</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded bg-primary bg-opacity-25 text-white h-100 border border-light border-opacity-10">
                                <div class="small text-white-50 mb-1">Performa minggu ini</div>
                                <div class="fw-bold"><?= htmlspecialchars($menuPopulerMingguIni) ?></div>
                                <div class="small text-white-50 mt-2"><?= number_format($totalMingguan) ?> kendaraan minggu ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card home-stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="home-stat-icon" style="background:#e7f6ff;color:#0b5ed7;">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <span class="badge text-bg-primary-subtle text-primary">Hari ini</span>
                    </div>
                    <div class="text-muted small mb-2">Kendaraan masuk hari ini</div>
                    <div class="h2 fw-bold mb-1"><?= number_format($totalHarian) ?></div>
                    <div class="text-muted">Jumlah unit yang sudah diproses hari ini.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card home-stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="home-stat-icon" style="background:#e6fbff;color:#0891b2;">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <span class="badge text-bg-info-subtle text-info-emphasis">Top service</span>
                    </div>
                    <div class="text-muted small mb-2">Layanan unggulan</div>
                    <div class="h4 fw-bold mb-1"><?= htmlspecialchars($menuPopulerHariIni) ?></div>
                    <div class="text-muted">Layanan yang paling sering dipakai pelanggan.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card home-stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="home-stat-icon" style="background:#edf5ff;color:#084298;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <span class="badge text-bg-primary-subtle text-primary">7 hari</span>
                    </div>
                    <div class="text-muted small mb-2">Pendapatan minggu ini</div>
                    <div class="h2 fw-bold mb-1">Rp <?= number_format($pendapatanMingguan, 0, ',', '.') ?></div>
                    <div class="text-muted">Akumulasi pemasukan dari transaksi yang sudah dibayar.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card home-chart-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <div class="small text-uppercase text-muted mb-1">Grafik Harian</div>
                            <h5 class="fw-bold mb-0">Aktivitas Harian</h5>
                        </div>
                        <span class="badge rounded-pill text-bg-info text-white px-3 py-2">Update hari ini</span>
                    </div>
                    <div class="home-chart-wrap">
                        <canvas id="menuTerlarisChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card home-chart-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <div class="small text-uppercase text-muted mb-1">Grafik Mingguan</div>
                            <h5 class="fw-bold mb-0">Aktivitas Mingguan</h5>
                        </div>
                        <span class="badge rounded-pill text-bg-info text-white px-3 py-2">Update 7 hari</span>
                    </div>
                    <div class="home-chart-wrap">
                        <canvas id="menuTerlarisMingguanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-2 pb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div>
                <div class="small text-uppercase text-muted mb-1">Carwash App</div>
                <h2 class="fw-bold mb-0">Simple dipakai, cepat dipantau, cocok buat operasional carwash.</h2>
            </div>
            <div class="text-muted">Halaman utama dibuat ringkas supaya status operasional langsung kelihatan.</div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card home-feature-card">
                    <div class="card-body p-4">
                        <div class="home-feature-icon mb-4">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h5 class="fw-bold">Akses cepat</h5>
                        <p class="text-muted mb-0">Buka transaksi, layanan, dan laporan lebih cepat dari dashboard utama.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card home-feature-card">
                    <div class="card-body p-4">
                        <div class="home-feature-icon mb-4">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <h5 class="fw-bold">Pantauan kendaraan</h5>
                        <p class="text-muted mb-0">Status kendaraan masuk, proses, dan selesai bisa dipantau lebih jelas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card home-feature-card">
                    <div class="card-body p-4">
                        <div class="home-feature-icon mb-4">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="fw-bold">Operasional rapi</h5>
                        <p class="text-muted mb-0">Tampilan dibuat stabil dan konsisten supaya enak dipakai harian oleh admin atau kasir.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="home-footer text-center py-3">
        &copy; 2026 Carwash App. Semua hak dilindungi.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode($labels) ?>;
    const dataValues = <?= json_encode($dataValues) ?>;
    const labelsMingguan = <?= json_encode($labelsMingguan) ?>;
    const dataValuesMingguan = <?= json_encode($dataValuesMingguan) ?>;
    const colors = ['#0b5ed7', '#0dcaf0', '#084298', '#38bdf8', '#60a5fa'];

    function renderMenuChart(canvasId, labelsData, valuesData, emptyMessage) {
        if (labelsData.length === 0) {
            document.getElementById(canvasId).outerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bar-chart-line fs-1 d-block mb-3"></i>
                    <p class="mb-0">${emptyMessage}</p>
                </div>
            `;
            return;
        }

        const ctx = document.getElementById(canvasId);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [{
                    data: valuesData,
                    backgroundColor: labelsData.map((_, index) => colors[index % colors.length]),
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' transaksi';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        },
                        grid: {
                            color: 'rgba(8, 66, 152, 0.08)',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }

    renderMenuChart('menuTerlarisChart', labels, dataValues, 'Belum ada data dashboard hari ini');
    renderMenuChart('menuTerlarisMingguanChart', labelsMingguan, dataValuesMingguan, 'Belum ada data dashboard minggu ini');
</script>
