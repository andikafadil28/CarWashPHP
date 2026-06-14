<?php
session_start();

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$nama_tarif = (isset($_POST["nama_tarif"])) ? htmlentities($_POST["nama_tarif"]) : "";
$keterangan = (isset($_POST["keterangan"])) ? htmlentities($_POST["keterangan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$harga_PT = (isset($_POST["harga_PT"])) ? htmlentities($_POST["harga_PT"]) : "";
$harga_Karyawan = (isset($_POST["harga_Karyawan"])) ? htmlentities($_POST["harga_Karyawan"]) : "";
// $pajak = (isset($_POST["pajak"])) ? htmlentities($_POST["pajak"]) : "";
$status = isset($_POST["status_aktif"]) ? 1 : 0;

// $kode_rand = rand(1000, 9999) . "-";
// $target_dir = "../assets/img/" . $kode_rand;
// $target_file = $target_dir . basename($_FILES["foto"]["name"]);
// $imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


if (isset($_POST['input_menu_edit_proses'])) {
    $query = mysqli_query($conn, "update tb_tarif set nama_tarif = '$nama_tarif', keterangan_tarif = '$keterangan', ukuran_Kendaraan = '$ukuran_Kendaraan',jenis_Kendaraan = '$jenis_Kendaraan', 
    bill_PT = '$harga_PT', bill_Karyawan = '$harga_Karyawan' , status = $status where id = '$_POST[id]'");
    if ($query) {
        echo "<script>alert('Tarif berhasil Di Update'); window.location.href='../menu';</script>";
    } else {
        echo "<script>alert('Gagal Mengupdate Tarif'); window.location.href='../menu';</script>";
    }
}
