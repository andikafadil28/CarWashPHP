<?php
session_start();

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$no_Kendaraan = (isset($_POST["no_Kendaraan"])) ? htmlentities($_POST["no_Kendaraan"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$catatan = (isset($_POST["catatan"])) ? htmlentities($_POST["catatan"]) : "";
$message = "";

if (isset($_POST['input_order_proses'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_order WHERE id_order = '$kodeorder'");
        if (mysqli_num_rows($select_query) > 0) {
                echo "<script>alert('Kode order sudah terdaftar'); window.location.href='../order';</script>";
                exit();
        } else {
                $query = mysqli_query($conn, "INSERT INTO tb_order (id_order, pelanggan, jenis_Kendaraan, ukuran_Kendaraan,no_Kendaraan, catatan,kasir) 
                VALUES ('$kodeorder', '$pelanggan', '$jenis_Kendaraan', '$ukuran_Kendaraan','$no_Kendaraan', '$catatan', '$_SESSION[id_kantin]')");
                if ($query) {
                        $message = '<script> window.location.href="../?x=orderitem&kode_order=' . urlencode($kodeorder) .
                                '&no_Kendaraan=' . urlencode($no_Kendaraan) .
                                '&pelanggan=' . urlencode($pelanggan) .
                                '&ukuran_Kendaraan=' . urlencode($ukuran_Kendaraan) .
                                '&jenis_Kendaraan=' . urlencode($jenis_Kendaraan) .
                                '&catatan=' . urlencode($catatan) . '";</script>';
                } else {
                        echo "<script>alert('Gagal menambahkan order'); window.location.href='../order';</script>";
                }
        }
}
echo $message;
