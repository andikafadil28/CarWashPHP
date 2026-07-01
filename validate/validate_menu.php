<?php
session_start();

include("../Database/connect.php");
$nama_tarif = (isset($_POST["nama_tarif"])) ? htmlentities($_POST["nama_tarif"]) : "";
$keterangan = (isset($_POST["keterangan"])) ? htmlentities($_POST["keterangan"]) : "";
$ukuran_Kendaraan = (isset($_POST["ukuran_Kendaraan"])) ? htmlentities($_POST["ukuran_Kendaraan"]) : "";
$jenis_Kendaraan = (isset($_POST["jenis_Kendaraan"])) ? htmlentities($_POST["jenis_Kendaraan"]) : "";
$hargaTarif = (isset($_POST["harga_Tarif"])) ? $_POST["harga_Tarif"] : 0;
$status = isset($_POST["status_aktif"]) ? 1 : 0;

$kode_rand = rand(1000, 9999) . "-";
$target_dir = "../assets/img/" . $kode_rand;
$foto_name = isset($_FILES["foto"]["name"]) ? $_FILES["foto"]["name"] : "";
$target_file = $target_dir . basename($foto_name);
$imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (isset($_POST['input_menu_proses'])) {
    $hasPhoto = isset($_FILES["foto"]["tmp_name"]) && is_uploaded_file($_FILES["foto"]["tmp_name"]);
    $statusUpload = 1;
    $namaFileFoto = "";

    if ($statusUpload == 0) {
        exit();
    }

    $pembagian = carwash_hitung_pembagian($hargaTarif);
    $billTarif = $pembagian['harga_dasar'];
    $billPT = $pembagian['bill_pt'];
    $billKaryawan = $pembagian['bill_karyawan'];
    $billOperasional = $pembagian['bill_operasional'];

    $query = mysqli_query($conn, "INSERT INTO tb_tarif (nama_tarif, keterangan_tarif, bill_Tarif, bill_PT, bill_Karyawan, bill_Operasional, jenis_Kendaraan, ukuran_Kendaraan, status)
            VALUES ('$nama_tarif', '$keterangan', '$billTarif', '$billPT', '$billKaryawan', '$billOperasional', '$jenis_Kendaraan', '$ukuran_Kendaraan', '$status')");
    if ($query) {
        echo "<script>alert('Tarif berhasil ditambahkan'); window.location.href='../menu';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan Tarif'); window.location.href='../menu';</script>";
    }

    exit();
}
