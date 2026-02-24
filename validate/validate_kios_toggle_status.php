<?php
session_start();

include("../Database/connect.php");

if (isset($_POST['toggle_kios_status'])) {
    $id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
    $status = (isset($_POST["status"])) ? (int) $_POST["status"] : 0;
    $new_status = ($status === 1) ? 0 : 1;

    if ($id !== "") {
        $query = mysqli_query($conn, "UPDATE tb_kios SET status = '$new_status' WHERE id = '$id'");
        if ($query) {
            echo "<script>alert('Status kios berhasil diubah'); window.location.href='../kios';</script>";
        } else {
            echo "<script>alert('Gagal mengubah status kios'); window.location.href='../kios';</script>";
        }
    } else {
        echo "<script>alert('ID kios tidak valid'); window.location.href='../kios';</script>";
    }
}
?>
