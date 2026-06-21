<?php
session_start();

include("../Database/connect.php");
$kodeorder = (isset($_POST["kode_order"])) ? htmlentities($_POST["kode_order"]) : "";
$no_Kendaraan = (isset($_POST["no_Kendaraan"])) ? htmlentities($_POST["no_Kendaraan"]) : "";
$pelanggan = (isset($_POST["pelanggan"])) ? htmlentities($_POST["pelanggan"]) : "";
$catatan = (isset($_POST["catatan"])) ? htmlentities($_POST["catatan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$catatan_order = (isset($_POST["catatan_order"])) ? htmlentities($_POST["catatan_order"]) : "";
$jumlah = (isset($_POST["jumlah"])) ? htmlentities($_POST["jumlah"]) : "";
$menu = (isset($_POST["menu"])) ? htmlentities($_POST["menu"]) : "";
$id_list_order = (isset($_POST["id_list_order"])) ? htmlentities($_POST["id_list_order"]) : "";
$redirect = "../?x=orderitem&kode_order=" . urlencode($kodeorder) .
        "&no_Kendaraan=" . urlencode($no_Kendaraan) .
        "&pelanggan=" . urlencode($pelanggan) .
        "&catatan=" . urlencode($catatan) .
        "&jenis_Kendaraan=" . urlencode($jenis_Kendaraan) .
        "&ukuran_Kendaraan=" . urlencode($ukuran_Kendaraan);
$message = "";

if (isset($_POST['edit_order_item'])) {
        $select_query = mysqli_query($conn, "UPDATE tb_list_order SET tarif = '$menu', jumlah = '$jumlah', catatan_order = '$catatan_order' WHERE id_list_order = '$id_list_order'");
        if ($select_query) {
                $message = '<script>window.location.href="' . $redirect . '";</script>';
        } else {
                echo "<script>alert('Gagal memperbarui item'); window.location.href='../order';</script>";
        }       
}
echo $message;


