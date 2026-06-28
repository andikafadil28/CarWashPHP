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

if (isset($_POST['input_order_edit'])) {
        $query = mysqli_query($conn, "UPDATE tb_order SET pelanggan = '$pelanggan', jenis_Kendaraan = '$jenis_Kendaraan', ukuran_Kendaraan = '$ukuran_Kendaraan', no_Kendaraan = '$no_Kendaraan', catatan = '$catatan' WHERE id_order = '$kodeorder'");
        if ($query) {
                $message = '<script>alert("Order berhasil diupdate"); window.location.href="../order";</script>';
        } else {
                echo "<script>alert('Gagal mengupdate order'); window.location.href='../order';</script>";
        }
}
echo $message;


