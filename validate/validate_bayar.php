<?php
session_start();

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$no_Kendaraan = (isset($_POST["no_Kendaraan"])) ? htmlentities($_POST["no_Kendaraan"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$catatan = (isset($_POST["catatan"])) ? htmlentities($_POST["catatan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$bayar = carwash_round_money($_POST["bayar"] ?? 0);
$diskonRequest = carwash_round_money($_POST["diskon"] ?? 0);
$redirect = "../?x=orderitem&kode_order=" . urlencode($kodeorder) .
        "&no_Kendaraan=" . urlencode($no_Kendaraan) .
        "&pelanggan=" . urlencode($pelanggan) .
        "&catatan=" . urlencode($catatan) .
        "&jenis_Kendaraan=" . urlencode($jenis_Kendaraan) .
        "&ukuran_Kendaraan=" . urlencode($ukuran_Kendaraan) .
        "&diskon=" . urlencode($diskonRequest);

$totalTarif = 0;
$totalPT = 0;
$totalKaryawan = 0;
$totalOperasional = 0;
$totalPpn = 0;
$items = [];

$itemQuery = mysqli_query($conn, "SELECT tb_list_order.jumlah, tb_tarif.bill_Tarif, tb_tarif.bill_PT, tb_tarif.bill_Karyawan, tb_tarif.bill_Operasional
        FROM tb_list_order
        LEFT JOIN tb_tarif ON tb_tarif.id = tb_list_order.tarif
        WHERE tb_list_order.kode_order = '$kodeorder'");

if (!$itemQuery) {
        echo "<script>alert('Gagal membaca data order'); window.location.href='" . $redirect . "';</script>";
        exit();
}

while ($item = mysqli_fetch_array($itemQuery, MYSQLI_ASSOC)) {
        $jumlah = (int) ($item['jumlah'] ?? 0);
        if ($jumlah <= 0) {
                continue;
        }

        $pembagian = carwash_resolve_tarif_breakdown($item);
        $totalTarif += $pembagian['bill_tarif'] * $jumlah;
        $totalPpn += $pembagian['pajak'] * $jumlah;
        $totalPT += $pembagian['bill_pt'] * $jumlah;
        $totalKaryawan += $pembagian['bill_karyawan'] * $jumlah;
        $totalOperasional += $pembagian['bill_operasional'] * $jumlah;
        $item['jumlah'] = $jumlah;
        $item['billing'] = $pembagian;
        $items[] = $item;
}

$ringkasanBayar = carwash_calculate_order_totals($items, $diskonRequest);
$diskon = $ringkasanBayar['diskon'];
$ppn = $ringkasanBayar['subtotal_ppn'];
$grand_total = $ringkasanBayar['grand_total'];
$nominal_pt = $ringkasanBayar['nominal_pt'];
$nominal_karyawan = $ringkasanBayar['nominal_karyawan'];
$nominal_operasional = $ringkasanBayar['nominal_operasional'];
$kembalian = $bayar - $grand_total;

if (isset($_POST['proses_bayar'])) {
        if ($totalTarif <= 0) {
                echo "<script>alert('Order belum memiliki item yang bisa dibayar'); window.location.href='" . $redirect . "';</script>";
                exit();
        }

        if ($bayar < $grand_total) {
                echo "<script>alert('Jumlah bayar tidak cukup'); window.location.href='" . $redirect . "';</script>";
                exit();
        }

        $query1 = mysqli_query($conn, "INSERT INTO tb_bayar (id_bayar, nominal_uang, jumlah_bayar, ppn, nominal_pt, nominal_karyawan, nominal_operasional, diskon, waktu_bayar, kode_order_bayar) VALUES ('$kodeorder', '$bayar', '$grand_total', '$ppn', '$nominal_pt', '$nominal_karyawan', '$nominal_operasional', '$diskon', NOW(), '$kodeorder')");
        $query = mysqli_query($conn, "UPDATE tb_list_order SET status = 'Lunas' WHERE kode_order = '$kodeorder'");
        if ($query1 && $query) {
                echo "<script>alert('Pembayaran berhasil. Kembalian: Rp. " . number_format($kembalian, 0, ',', '.') . "'); window.location.href='" . $redirect . "';</script>";
        } else {
                echo "<script>alert('Gagal memproses pembayaran'); window.location.href='" . $redirect . "';</script>";
        }
}
