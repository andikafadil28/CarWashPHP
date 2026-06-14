<?php
session_start();

include("../Database/connect.php");
$id = (isset($_POST["id"])) ? htmlentities($_POST["id"]) : "";
// $foto = (isset($_POST["foto"])) ? htmlentities($_POST["foto"]) : "";



if (isset($_POST['input_menu_delete'])) {
      $query = mysqli_query($conn, "DELETE FROM tb_tarif WHERE id = '$id'");
      if ($query) {
            // Hapus file foto hanya jika memang ada.
            echo "<script>alert('Menu berhasil Di Hapus'); window.location.href='../menu';</script>";
      } else {
            $dbError = mysqli_error($conn);
            if (stripos($dbError, 'foreign key') !== false) {
                  echo "<script>alert('Menu tidak bisa dihapus karena masih dipakai pada data order.'); window.location.href='../menu';</script>";
            } else {
                  echo "<script>alert('Gagal Menghapus menu'); window.location.href='../menu';</script>";
            }
      }
}
