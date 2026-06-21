<style>
    #accordionSidebar.carwash-sidebar {
        background: linear-gradient(180deg, #084298 0%, #0b5ed7 52%, #0dcaf0 100%);
    }

    .carwash-sidebar .sidebar-brand {
        min-height: 5rem;
        letter-spacing: .02em;
    }

    .carwash-sidebar .sidebar-logo {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .16);
        padding: 5px;
        box-shadow: 0 10px 24px rgba(4, 30, 66, .25);
    }

    .carwash-sidebar .nav-link {
        border-radius: 8px;
        margin: 0 .65rem;
    }

    .carwash-sidebar .nav-item.active .nav-link,
    .carwash-sidebar .nav-link:hover {
        background: rgba(255, 255, 255, .16);
    }

    .carwash-sidebar .collapse-inner {
        border-radius: 8px !important;
    }

    .carwash-sidebar .collapse-item:active,
    .carwash-sidebar .collapse-item:hover {
        color: #084298;
        background: #e7f6ff;
    }
</style>

<ul class="navbar-nav sidebar sidebar-dark accordion toggled carwash-sidebar" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="home">
        <div class="sidebar-brand-icon">
            <img class="sidebar-logo" src="assets/brand/carwash-logo.svg" alt="Carwash App">
        </div>
        <div class="sidebar-brand-text mx-2">Carwash App</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="home">
            <i class="fas fa-fw fa-home"></i> <span>Home</span>
        </a>
    </li>

    <!-- <li class="nav-item">
        <a class="nav-link" href="http://192.168.0.215:8000/app/login">
            <i class="fas fa-fw fa-external-link-alt"></i> <span>aplikasi baru</span>
        </a>
    </li> -->

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOrder"
            aria-expanded="true" aria-controls="collapseOrder">
            <i class="bi bi-water"></i> <span>Transaksi & Layanan</span>
        </a>
        <div id="collapseOrder" class="collapse" aria-labelledby="headingOrder" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="menu">Daftar Layanan</a>
                <a class="collapse-item" href="order">Order Cuci Baru</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReport"
            aria-expanded="true" aria-controls="collapseReport">
            <i class="bi bi-graph-up-arrow"></i> <span>Laporan Carwash</span>
        </a>
        <div id="collapseReport" class="collapse" aria-labelledby="headingReport" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Detail Pendapatan:</h6>
                <a class="collapse-item" href="laporan">Pendapatan Detail</a>
                <!-- <a class="collapse-item" href="laporanrs">Pendapatan PT Detail</a>
                <a class="collapse-item" href="laporantoko">Pendapatan Karyawan</a>
                <h6 class="collapse-header mt-2">Rekapitulasi:</h6>
                <a class="collapse-item" href="history">Riwayat Transaksi</a>
                <a class="collapse-item" href="rekaprs">Rekap PT</a>
                <a class="collapse-item" href="rekapmenurs">Rekap Layanan</a>
                <a class="collapse-item" href="rekapkeuangan">Rekap Keuangan</a>
                <a class="collapse-item" href="rekapkeuanganmenu">Rekap Keuangan Layanan</a> -->
            </div>
        </div>
    </li>

    <?php
    // Bagian PHP untuk Level 1 (Admin)
    if ($hasil['level'] == 1) {
    ?>
        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings"
                aria-expanded="true" aria-controls="collapseSettings">
                <i class="bi bi-gear-fill"></i> <span>Pengaturan Sistem</span>
            </a>
            <div id="collapseSettings" class="collapse" aria-labelledby="headingSettings" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Manajemen Data:</h6>
                    <a class="collapse-item" href="user">Manajemen User</a>
                    <!-- <a class="collapse-item" href="kios">Manajemen Toko</a> -->
                </div>
            </div>
        </li>
    <?php
    }
    ?>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
