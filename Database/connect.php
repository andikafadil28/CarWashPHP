<?php
require_once __DIR__ . '/../config.php';

$conn = mysqli_connect("localhost", "root", "", "carwashapp");
if (!$conn) {
      echo "Koneksi gagal";
      return;
}

carwash_sync_schema($conn);
