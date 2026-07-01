<?php
include "Database/connect.php";
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : 'all';
$where_status = "";
if ($status_filter === '1' || $status_filter === '0') {
    $status_val = (int) $status_filter;
    $where_status = "OR tb_tarif.status = $status_val";
}

$query = mysqli_query($conn, "select tb_tarif.* from tb_tarif
WHERE tb_tarif.status = 1
$where_status
ORDER BY tb_tarif.status DESC, tb_tarif.nama_tarif ASC");
$query2 = mysqli_query($conn, "select * from tb_tarif WHERE status = 1");
$result2 = [];
$result = [];
while ($record2 = mysqli_fetch_array($query2)) {
    $record2['billing'] = carwash_resolve_tarif_breakdown($record2);
    $result2[] = $record2;
}
while ($record = mysqli_fetch_array($query)) {
    $record['billing'] = carwash_resolve_tarif_breakdown($record);
    $result[] = $record;
}
?>

<!-- Conten -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-fork-knife"></i>
            Tarif
        </div>
        <div class="card-body-scrollable">
            <div class="row">
                <?php
                if ($_SESSION["level_kantin"] == 1) {
                ?>
                    <div class="col d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambah">Tambah Tarif</button>
                    </div>
                <?php
                } else {
                }
                ?>
                <div class="col-12 mb-3">
                    <form method="post" class="d-flex align-items-end gap-2">
                        <div>
                            <label for="status_filter" class="form-label mb-1">Filter Status</label>
                            <select class="form-select" id="status_filter" name="status_filter">
                                <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>Semua</option>
                                <option value="1" <?php echo ($status_filter === '1') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="0" <?php echo ($status_filter === '0') ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-secondary">Terapkan</button>
                        </div>
                    </form>
                </div>

                <!-- Modal tambah menu -->
                <div class="modal fade" id="ModalTambah" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Tarif</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" novalidate action="validate/validate_menu.php"
                                    method="post" enctype="multipart/form-data">
                                    <!-- <div class="row mt-3">
                                        <div class="col lg-12">
                                            <div class="input-group">
                                                <input type="file" class="form-control py-9" id="floatingInputGambar"
                                                    placeholder="Masukan Gambar" name="foto">
                                                <label class="input-group-text" for="floatingInputGambar">Upload Foto
                                                    Menu</label>
                                            </div>
                                            <small class="text-muted">Foto opsional, boleh dikosongkan.</small>
                                        </div>
                                    </div> -->
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="floatingNama"
                                                    placeholder="Masukan Nama" name="nama_tarif" required>
                                                <label for="floatingNama">Nama Tarif</label>
                                                <div class="invalid-feedback">
                                                    Nama tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="floatingKeterangan"
                                                    placeholder="Masukan Keterangan" name="keterangan">
                                                <label for="floatingKeterangan">Keterangan</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">

                                        <div class="col lg-4">
                                            <div class="form-floating mt-3">
                                                <input type="number" class="form-control" id="floatingHarga"
                                                    placeholder="Masukan Harga" name="harga_Tarif" required>
                                                <label for="floatingHarga">Harga Tarif</label>
                                                <div class="invalid-feedback">
                                                    Harga tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col lg-4">
                                            <div class="form-floating mt-3">
                                                <input type="number" class="form-control" id="floatingStok"
                                                    placeholder="Masukan Stok" name="harga_Karyawan_preview" required>
                                                <label for="floatingStok">Harga Karyawan</label>
                                                <div class="invalid-feedback">
                                                    Harga tidak boleh kosong
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col lv-6">
                                            <div class="form-floating mt-3">
                                                <select class="form-select" aria-label="Default select example"
                                                    name="ukuran_Kendaraan" required>
                                                    <option selected hidden value="">Pilih Ukuran Kendaraan</option>
                                                    <option value="Besar">Besar</option>
                                                    <option value="Sedang">Sedang</option>
                                                    <option value="Kecil">Kecil</option>
                                                </select>
                                                <label for="floatingKategori">Kategori Ukuran</label>
                                                <div class="invalid-feedback">
                                                    Jenis Menu tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col lg-6">
                                            <div class="form-floating mt-3">
                                                <select class="form-select" aria-label="Default select example"
                                                    name="jenis_Kendaraan" required>
                                                    <option selected hidden value="">Pilih Jenis Kendaraan</option>
                                                    <option value="Mobil">Mobil</option>
                                                    <option value="Motor">Motor</option>
                                                </select>
                                                <label for="floatingKategori">Kategori Kendaraan</label>
                                                <div class="invalid-feedback">
                                                    Jenis Menu tidak boleh kosong
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="status_aktif" name="status_aktif" checked>
                                                <label class="form-check-label" for="status_aktif">
                                                    Aktifkan menu
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary"
                                            name="input_menu_proses">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php

                foreach ($result as $row) {
                ?>
                    <!-- Modal edit -->
                    <div class="modal fade" id="ModalEdit<?php echo $row['id'] ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Makanan Dan Minuman</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_menu_edit.php"
                                        method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <!-- <div class="row mt-3">
                                            <div class="col lg-12">
                                                <div class="input-group">
                                                    <input type="file" class="form-control py-9" id="floatingInputGambar"
                                                        placeholder="Masukan Gambar" name="foto">
                                                    <label class="input-group-text" for="floatingInputGambar">Upload Foto
                                                        Menu</label>
                                                    <div class="invalid-feedback">
                                                        Gambar tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingNama"
                                                        placeholder="Masukan Nama" name="nama_tarif"
                                                        value="<?php echo $row['nama_tarif'] ?>" required>
                                                    <label for="floatingNama">Nama Tarif</label>
                                                    <div class="invalid-feedback">
                                                        Nama tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingKeterangan"
                                                        placeholder="Masukan Keterangan" name="keterangan"
                                                        value="<?php echo $row['keterangan_tarif'] ?>">
                                                    <label for="floatingKeterangan">Keterangan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col lg-6">
                                                <div class="form-floating mt-3">
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="ukuran_Kendaraan" required>
                                                        <option selected hidden value="<?php echo $row['ukuran_Kendaraan'] ?>"><?php echo $row['ukuran_Kendaraan'] ?></option>
                                                        <option value="Besar">Besar</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Kecil">Kecil</option>
                                                    </select>
                                                    <label for="floatingKategori">Kategori Ukuran</label>
                                                    <div class="invalid-feedback">
                                                        Jenis Menu tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col lg-6">
                                                <div class="form-floating mt-3">
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="jenis_Kendaraan" required>
                                                        <option selected hidden value="<?php echo $row['jenis_Kendaraan'] ?>"><?php echo $row['jenis_Kendaraan'] ?></option>
                                                        <option value="Mobil">Mobil</option>
                                                        <option value="Motor">Motor</option>
                                                    </select>
                                                    <label for="floatingKategori">Kategori Kendaraan</label>
                                                    <div class="invalid-feedback">
                                                        Jenis Menu tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col lg-6">
                                                    <div class="form-floating mt-3">
                                                        <input type="number" class="form-control" id="floatingHarga"
                                                            placeholder="Masukan Harga" name="harga_Tarif"
                                                            value="<?php echo $row['billing']['bill_tarif'] ?>" required>
                                                        <label for="floatingHarga">Harga</label>
                                                        <div class="invalid-feedback">
                                                            Harga tidak boleh kosong
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col lg-6">
                                                    <div class="form-floating mt-3">
                                                        <input type="number" class="form-control" id="floatingStok"
                                                            placeholder="Masukan Stok" name="harga_Karyawan_preview"
                                                            value="<?php echo $row['billing']['bill_karyawan'] ?>" readonly>
                                                        <label for="floatingStok">Bill Karyawan</label>
                                                        <div class="invalid-feedback">
                                                            Marjin tidak boleh kosong
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="status_aktif_edit_<?php echo $row['id']; ?>" name="status_aktif"
                                                        <?php echo ((int) ($row['status'] ?? 0) === 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label"
                                                        for="status_aktif_edit_<?php echo $row['id']; ?>">
                                                        Aktifkan menu
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary"
                                                name="input_menu_edit_proses">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal delete -->
                    <div class="modal fade" id="ModalDelete<?php echo $row['id'] ?>" tabindex="-1"
                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_menu_delete.php"
                                        method="post">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <!-- <input type="hidden" name="foto" value="<?php echo $row['foto'] ?>"> -->
                                        <div class="col-lg-12">

                                            <h5>Apakah Anda yakin ingin menghapus menu
                                                <strong><?php echo $row['nama_tarif'] ?></strong>?
                                            </h5>
                                            <p>Data yang dihapus tidak dapat dikembalikan.</p>

                                        </div>


                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger" name="input_menu_delete">Hapus
                                                Data</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Modal view -->
                    <div class="modal fade" id="ModalView<?php echo $row['id'] ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Makanan Dan Minuman</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="needs-validation" novalidate action="validate/validate_menu_edit.php"
                                        method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                        <!-- <div class="row mt-3">
                                            <div class="col lg-12">
                                                <div class="input-group">
                                                    <input type="file" class="form-control py-9" id="floatingInputGambar"
                                                        placeholder="Masukan Gambar" name="foto">
                                                    <label class="input-group-text" for="floatingInputGambar">Upload Foto
                                                        Menu</label>
                                                    <div class="invalid-feedback">
                                                        Gambar tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingNama"
                                                        placeholder="Masukan Nama" name="nama_tarif"
                                                        value="<?php echo $row['nama_tarif'] ?>" disabled>
                                                    <label for="floatingNama">Nama Tarif</label>
                                                    <div class="invalid-feedback">
                                                        Nama tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingKeterangan"
                                                        placeholder="Masukan Keterangan" name="keterangan"
                                                        value="<?php echo $row['keterangan_tarif'] ?>" disabled>
                                                    <label for="floatingKeterangan">Keterangan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col lg-6">
                                                <div class="form-floating mt-3">
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="ukuran_Kendaraan" disabled>
                                                        <option selected hidden value="<?php echo $row['ukuran_Kendaraan'] ?>"><?php echo $row['ukuran_Kendaraan'] ?></option>
                                                        <option value="Besar">Besar</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Kecil">Kecil</option>
                                                    </select>
                                                    <label for="floatingKategori">Kategori Ukuran</label>
                                                    <div class="invalid-feedback">
                                                        Jenis Menu tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col lg-6">
                                                <div class="form-floating mt-3">
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="jenis_Kendaraan" disabled>
                                                        <option selected hidden value="<?php echo $row['jenis_Kendaraan'] ?>"><?php echo $row['jenis_Kendaraan'] ?></option>
                                                        <option value="Mobil">Mobil</option>
                                                        <option value="Motor">Motor</option>
                                                    </select>
                                                    <label for="floatingKategori">Kategori Kendaraan</label>
                                                    <div class="invalid-feedback">
                                                        Jenis Menu tidak boleh kosong
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col lg-6">
                                                    <div class="form-floating mt-3">
                                                        <input type="number" class="form-control" id="floatingHarga"
                                                            placeholder="Masukan Harga" name="harga_Tarif"
                                                            value="<?php echo $row['billing']['bill_tarif'] ?>" disabled>
                                                        <label for="floatingHarga">Harga</label>
                                                        <div class="invalid-feedback">
                                                            Harga tidak boleh kosong
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col lg-6">
                                                    <div class="form-floating mt-3">
                                                        <input type="number" class="form-control" id="floatingStok"
                                                            placeholder="Masukan Stok" name="harga_Karyawan_preview"
                                                            value="<?php echo $row['billing']['bill_karyawan'] ?>" disabled>
                                                        <label for="floatingStok">Bill Karyawan</label>
                                                        <div class="invalid-feedback">
                                                            Marjin tidak boleh kosong
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <!-- <button type="submit" class="btn btn-primary"
                                                name="input_menu_edit_proses">Simpan</button> -->
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <?php
                if (empty($result)) {
                    echo "<div class='alert alert-warning'>Data tidak ditemukan</div>";
                } else {
                ?>
                    <div class="table-responsive-lg-12">
                        <table class="table table-hover" id="table_menu">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Tarif</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Ukuran Kendaraan</th>
                                    <th scope="col">Jenis Kendaraan</th>
                                    <th scope="col">Harga Tarif</th>
                                    <th scope="col">Bill PT</th>
                                    <th scope="col">Bill Karyawan</th>
                                    <th scope="col">Bill Operasional</th>
                                    <th scope="col">Status</th>
                                    <?php
                                    if ($_SESSION["level_kantin"] == 1) {
                                    ?>
                                        <th scope="col">Aksi</th>
                                    <?php
                                    } else {
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id_nomor = 1;
                                foreach ($result as $row) {
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $id_nomor++ ?></th>
                                        <td><?php echo $row['nama_tarif'] ?></td>
                                        <td><?php echo $row['keterangan_tarif'] ?></td>
                                        <td><?php echo $row['ukuran_Kendaraan'] ?></td>
                                        <td><?php echo $row['jenis_Kendaraan'] ?></td>
                                        <td><?php echo number_format($row['billing']['bill_tarif'], 0, ',', '.') ?></td>
                                        <td><?php echo number_format($row['billing']['bill_pt'], 0, ',', '.') ?></td>
                                        <td><?php echo number_format($row['billing']['bill_karyawan'], 0, ',', '.') ?></td>
                                        <td><?php echo number_format($row['billing']['bill_operasional'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if ((int) ($row['status'] ?? 0) === 1) { ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php } ?>
                                        </td>
                                        <?php
                                        if ($_SESSION["level_kantin"] == 1) {
                                        ?>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal"
                                                        data-bs-target="#ModalView<?php echo $row['id'] ?>"> <i
                                                            class="bi bi-eye-fill"></i></button>
                                                    <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal"
                                                        data-bs-target="#ModalEdit<?php echo $row['id'] ?>"> <i
                                                            class="bi bi-pencil-fill"></i></button>
                                                    <form action="validate/validate_menu_toggle_status.php" method="post" class="me-2">
                                                        <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                                        <input type="hidden" name="status" value="<?php echo (int) ($row['status'] ?? 0); ?>">
                                                        <button type="submit" name="toggle_menu_status"
                                                            class="btn btn-<?php echo ((int) ($row['status'] ?? 0) === 1) ? 'secondary' : 'success'; ?> btn-sm"
                                                            onclick="return confirm('Ubah status menu ini?')">
                                                            <?php echo ((int) ($row['status'] ?? 0) === 1) ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#ModalDelete<?php echo $row['id'] ?>"> <i
                                                            class="bi bi-trash-fill"></i></button>
                                                </div>

                                            </td>
                                        <?php
                                        } else {
                                        }
                                        ?>

                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }
                ?>


            </div>





        </div>


    </div>
</div>
<script>
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<script>
    let table = new DataTable('#table_menu');
</script>

<style>
    /* Include the CSS here or link to an external stylesheet */
    .card {
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 97%;
        /* Example width for the card */
        margin: 20px;
        display: flex;
        flex-direction: column;
    }

    .card-header {
        background-color: #f0f0f0;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        font-weight: bold;
    }

    .card-body-scrollable {
        overflow-x: auto;
        /* Adds horizontal scrollbar when content overflows */
        padding: 15px;
        /* white-space: nowrap; /* Uncomment if you want text to stay on one line */
    }

    .long-content {
        min-width: 800px;
        /* Ensure content is wide enough to trigger scroll */
        /* Adjust this value based on your content's natural width */
    }
</style>





