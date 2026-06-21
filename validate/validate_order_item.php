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
$redirect = "../?x=orderitem&kode_order=" . urlencode($kodeorder) .
        "&no_Kendaraan=" . urlencode($no_Kendaraan) .
        "&pelanggan=" . urlencode($pelanggan) .
        "&catatan=" . urlencode($catatan) .
        "&jenis_Kendaraan=" . urlencode($jenis_Kendaraan) .
        "&ukuran_Kendaraan=" . urlencode($ukuran_Kendaraan);
$message = "";


if (isset($_POST['input_order_item_proses'])) {
        $select_query = mysqli_query($conn, "SELECT * FROM tb_list_order WHERE kode_order = '$kodeorder' AND tarif = '$menu'");
        if (mysqli_num_rows($select_query) > 0) {
                $message = "<script>alert('Item sudah terdaftar dalam order ini'); window.location.href='" . $redirect . "';</script>";
        } else {
                $query = mysqli_query($conn, "INSERT INTO tb_list_order (kode_order, tarif, jumlah, catatan_order,status) VALUES ('$kodeorder', '$menu', '$jumlah', '$catatan_order','0')");
                if ($query) {
                        $message = '<script>
                        window.location.href="' . $redirect . '";</script>';
                } else {
                        echo "<script>alert('Gagal menambahkan item'); window.location.href='../order';</script>";
                }
        }

}
echo $message;



