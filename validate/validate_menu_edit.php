<?php
session_start();

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
$nama_tarif = (isset($_POST["nama_tarif"])) ? htmlentities($_POST["nama_tarif"]) : "";
$keterangan = (isset($_POST["keterangan"])) ? htmlentities($_POST["keterangan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$hargaTarif = (isset($_POST["harga_Tarif"])) ? $_POST["harga_Tarif"] : 0;
$status = isset($_POST["status_aktif"]) ? 1 : 0;

if (isset($_POST['input_menu_edit_proses'])) {
    $pembagian = carwash_hitung_pembagian($hargaTarif);
    $billTarif = $pembagian['harga_dasar'];
    $billPT = $pembagian['bill_pt'];
    $billKaryawan = $pembagian['bill_karyawan'];
    $billOperasional = $pembagian['bill_operasional'];

    $query = mysqli_query($conn, "UPDATE tb_tarif SET nama_tarif = '$nama_tarif', keterangan_tarif = '$keterangan', ukuran_Kendaraan = '$ukuran_Kendaraan', jenis_Kendaraan = '$jenis_Kendaraan', bill_Tarif = '$billTarif', bill_PT = '$billPT', bill_Karyawan = '$billKaryawan', bill_Operasional = '$billOperasional', status = $status WHERE id = '$id'");
    if ($query) {
        echo "<script>alert('Tarif berhasil Di Update'); window.location.href='../menu';</script>";
    } else {
        echo "<script>alert('Gagal Mengupdate Tarif'); window.location.href='../menu';</script>";
    }
}
