<?php
include "Database/connect.php";
include "Database/Query/menu_telaris.php";

$totalHarian = array_sum($total ?? []);
$totalMingguan = array_sum($total_mingguan ?? []);
$menuPopulerHariIni = !empty($menu[0]) ? $menu[0] : "Belum ada data";
$menuPopulerMingguIni = !empty($menu_mingguan[0]) ? $menu_mingguan[0] : "Belum ada data";
?>

<style>
    .home-dashboard {
        background:
            radial-gradient(circle at top right, rgba(244, 162, 97, 0.28), transparent 28%),
            linear-gradient(180deg, #f8f5ef 0%, #fffdf8 48%, #ffffff 100%);
    }

    .home-hero {
        position: relative;
        overflow: hidden;
        padding: 3rem;
        border-radius: 28px;
        background: linear-gradient(135deg, #1f3c31 0%, #2c5a4b 48%, #f29f58 100%);
        color: #fff;
        box-shadow: 0 24px 60px rgba(31, 60, 49, 0.18);
    }

    .home-hero::before,
    .home-hero::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.09);
    }

    .home-hero::before {
        width: 280px;
        height: 280px;
        top: -120px;
        right: -70px;
    }

    .home-hero::after {
        width: 180px;
        height: 180px;
        bottom: -80px;
        left: -50px;
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
        background: rgba(255, 255, 255, 0.14);
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
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .home-stat-card,
    .home-chart-card,
    .home-feature-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 18px 45px rgba(18, 38, 32, 0.08);
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
        border-radius: 16px;
        font-size: 1.2rem;
    }

    .home-chart-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfaf6 100%);
    }

    .home-feature-card {
        height: 100%;
        background: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .home-feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 50px rgba(18, 38, 32, 0.12);
    }

    .home-feature-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        font-size: 1.4rem;
        background: linear-gradient(135deg, #f7dcc4 0%, #f2a65a 100%);
        color: #5f3410;
    }

    .home-footer {
        color: #6c757d;
        font-size: .95rem;
    }

    @media (max-width: 767.98px) {
        .home-hero {
            padding: 2rem;
            border-radius: 22px;
        }
    }
</style>

<div class="container-fluid home-dashboard min-vh-100 py-4 px-3 px-md-4">
    <section class="home-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7 home-hero-content">
                <span class="home-kicker mb-3">
                    <i class="bi bi-stars"></i>
                    Dashboard Kantin
                </span>
                <h1 class="home-title fw-bold mb-3">Tampilan home yang lebih rapi, ringan, dan enak dilihat.</h1>
                <p class="home-subtitle mb-4">
                    Pantau menu favorit dan mulai transaksi lebih cepat dari halaman utama Sakina Kantin.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="order" class="btn btn-warning btn-lg px-4 text-dark fw-bold shadow-sm">
                        <i class="bi bi-basket2-fill me-2"></i>Mulai Order
                    </a>
                    <a href="menu" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-grid me-2"></i>Lihat Menu
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="home-hero-panel">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-uppercase small mb-2 text-white-50">Highlight Hari Ini</p>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($menuPopulerHariIni) ?></h4>
                            <p class="mb-0 text-white-50">Menu paling banyak dipesan hari ini.</p>
                        </div>
                        <span class="badge rounded-pill text-bg-light text-dark px-3 py-2">
                            <i class="bi bi-fire me-1"></i>Favorit
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 bg-white text-dark h-100">
                                <div class="small text-muted mb-1">Total penjualan hari ini</div>
                                <div class="fs-3 fw-bold"><?= number_format($totalHarian) ?></div>
                                <div class="small text-muted">porsi terjual</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 bg-dark bg-opacity-25 text-white h-100 border border-light border-opacity-10">
                                <div class="small text-white-50 mb-1">Menu favorit minggu ini</div>
                                <div class="fw-bold"><?= htmlspecialchars($menuPopulerMingguIni) ?></div>
                                <div class="small text-white-50 mt-2"><?= number_format($totalMingguan) ?> porsi minggu ini</div>
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
                        <div class="home-stat-icon" style="background:#eef7f1;color:#1f7a4d;">
                            <i class="bi bi-bar-chart-line-fill"></i>
                        </div>
                        <span class="badge text-bg-success-subtle text-success">Hari ini</span>
                    </div>
                    <div class="text-muted small mb-2">Penjualan hari ini</div>
                    <div class="h2 fw-bold mb-1"><?= number_format($totalHarian) ?></div>
                    <div class="text-muted">Porsi yang sudah terjual.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card home-stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="home-stat-icon" style="background:#fff3e8;color:#d97706;">
                            <i class="bi bi-cup-hot-fill"></i>
                        </div>
                        <span class="badge text-bg-warning-subtle text-warning-emphasis">Top menu</span>
                    </div>
                    <div class="text-muted small mb-2">Menu terlaris hari ini</div>
                    <div class="h4 fw-bold mb-1"><?= htmlspecialchars($menuPopulerHariIni) ?></div>
                    <div class="text-muted">Cocok buat pantauan cepat dari dashboard.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card home-stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="home-stat-icon" style="background:#eef2ff;color:#4f46e5;">
                            <i class="bi bi-calendar-week-fill"></i>
                        </div>
                        <span class="badge text-bg-primary-subtle text-primary">7 hari</span>
                    </div>
                    <div class="text-muted small mb-2">Akumulasi minggu ini</div>
                    <div class="h2 fw-bold mb-1"><?= number_format($totalMingguan) ?></div>
                    <div class="text-muted">Total porsi dari menu terlaris mingguan.</div>
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
                            <h5 class="fw-bold mb-0">Menu Terlaris Hari Ini</h5>
                        </div>
                        <span class="badge rounded-pill text-bg-info text-white px-3 py-2">Update hari ini</span>
                    </div>
                    <canvas id="menuTerlarisChart" height="130"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card home-chart-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <div class="small text-uppercase text-muted mb-1">Grafik Mingguan</div>
                            <h5 class="fw-bold mb-0">Menu Terlaris Minggu Ini</h5>
                        </div>
                        <span class="badge rounded-pill text-bg-info text-white px-3 py-2">Update 7 hari</span>
                    </div>
                    <canvas id="menuTerlarisMingguanChart" height="130"></canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="py-2 pb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div>
                <div class="small text-uppercase text-muted mb-1">Kenapa pilih Sakina Kantin</div>
                <h2 class="fw-bold mb-0">Simple dipakai, cepat dipantau, nyaman buat operasional.</h2>
            </div>
            <div class="text-muted">Halaman utama dibuat ringkas supaya informasi penting langsung kelihatan.</div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card home-feature-card">
                    <div class="card-body p-4">
                        <div class="home-feature-icon mb-4">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h5 class="fw-bold">Akses cepat</h5>
                        <p class="text-muted mb-0">Order baru dan daftar menu bisa dibuka langsung dari halaman utama tanpa muter-muter.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card home-feature-card">
                    <div class="card-body p-4">
                        <div class="home-feature-icon mb-4">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 class="fw-bold">Pantauan jelas</h5>
                        <p class="text-muted mb-0">Grafik harian dan mingguan ditata lebih bersih supaya tren penjualan lebih gampang dibaca.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card home-feature-card">
                    <div class="card-body p-4">
                        <div class="home-feature-icon mb-4">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <h5 class="fw-bold">Tampilan konsisten</h5>
                        <p class="text-muted mb-0">Warna, kartu, dan jarak elemen dirapikan supaya home terasa modern tapi tetap familiar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="home-footer text-center py-3">
        &copy; 2026 Sakina Kantin. Semua hak dilindungi.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode($menu) ?>;
    const dataValues = <?= json_encode($total) ?>;
    const labelsMingguan = <?= json_encode($menu_mingguan) ?>;
    const dataValuesMingguan = <?= json_encode($total_mingguan) ?>;
    const colors = ['#f29f58', '#2f7d5d', '#4c6ef5', '#ef4444', '#14b8a6'];

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
                                return context.raw + ' porsi';
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
                            color: 'rgba(31, 60, 49, 0.08)',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }

    renderMenuChart('menuTerlarisChart', labels, dataValues, 'Belum ada penjualan hari ini');
    renderMenuChart('menuTerlarisMingguanChart', labelsMingguan, dataValuesMingguan, 'Belum ada penjualan minggu ini');
</script>
