<?php
session_start();



include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$no_Kendaraan = (isset($_POST["no_Kendaraan"])) ? htmlentities($_POST["no_Kendaraan"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$catatan = (isset($_POST["catatan"])) ? htmlentities($_POST["catatan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$total_bayar = (isset($_POST["total_bayar"])) ? htmlentities($_POST["total_bayar"]) : "";
$bayar = (isset($_POST["bayar"])) ? htmlentities($_POST["bayar"]) : "";
$grand_total = (isset($_POST["grand_total"])) ? htmlentities($_POST["grand_total"]) : "";
$diskon = (isset($_POST["diskon"])) ? htmlentities($_POST["diskon"]) : "";
$nominal_karyawan = (isset($_POST["harga_toko"])) ? htmlentities($_POST["harga_toko"]) : "";
$ppn = (isset($_POST["ppn"])) ? htmlentities($_POST["ppn"]) : "";
$nominal_karyawan_final = $nominal_karyawan;
$nominal_pt = $grand_total - $nominal_karyawan_final;
$redirect = "../?x=orderitem&kode_order=" . urlencode($kodeorder) .
        "&no_Kendaraan=" . urlencode($no_Kendaraan) .
        "&pelanggan=" . urlencode($pelanggan) .
        "&catatan=" . urlencode($catatan) .
        "&jenis_Kendaraan=" . urlencode($jenis_Kendaraan) .
        "&ukuran_Kendaraan=" . urlencode($ukuran_Kendaraan) .
        "&diskon=" . urlencode($diskon);

$kembalian = $bayar - $grand_total;
if (isset($_POST['proses_bayar'])) {
        if ($bayar < $grand_total) {
                echo "<script>alert('Jumlah bayar tidak cukup'); window.location.href='" . $redirect . "';</script>";
                exit();
        } else {
                $query1 = mysqli_query($conn, "INSERT INTO tb_bayar (id_bayar,nominal_uang,jumlah_bayar,ppn,nominal_pt,nominal_karyawan,diskon,waktu_bayar,kode_order_bayar) VALUES ('$kodeorder', '$bayar', '$grand_total','$ppn','$nominal_pt','$nominal_karyawan_final','$diskon',NOW(),'$kodeorder')");
                $query = mysqli_query($conn, "UPDATE tb_list_order SET status = 'Lunas' WHERE kode_order = '$kodeorder'");
                if ($query1 && $query) {
                        echo "<script>alert('Pembayaran berhasil. Kembalian: Rp. " . number_format($kembalian, 0, ',', '.') . "'); window.location.href='" . $redirect . "';</script>";
                } else {
                        echo "<script>alert('Gagal memproses pembayaran'); window.location.href='" . $redirect . "';</script>";
                }
        }
}

