<?php
session_start();

include("../Database/connect.php");

if (isset($_POST['toggle_menu_status'])) {
    $id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
    $status = (isset($_POST["status"])) ? (int) $_POST["status"] : 0;
    $new_status = ($status === 1) ? 0 : 1;

    if ($id !== "") {
        $query = mysqli_query($conn, "UPDATE tb_menu SET status = '$new_status' WHERE id = '$id'");
        if ($query) {
            echo "<script>alert('Status menu berhasil diubah'); window.location.href='../menu';</script>";
        } else {
            echo "<script>alert('Gagal mengubah status menu'); window.location.href='../menu';</script>";
        }
    } else {
        echo "<script>alert('ID menu tidak valid'); window.location.href='../menu';</script>";
    }
}
?>
