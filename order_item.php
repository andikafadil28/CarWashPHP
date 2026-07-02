<?php
include "Database/connect.php";

$kode = mysqli_real_escape_string($conn, $_GET['kode_order'] ?? '');
$no_Kendaraan = $_GET['no_Kendaraan'] ?? '';
$customer = $_GET['pelanggan'] ?? '';
$diskon = $_GET['diskon'] ?? 0;
$ukuran_Kendaraan = $_GET['ukuran_Kendaraan'] ?? $_GET['ukuran_K'] ?? '';
$jenis_Kendaraan = $_GET['jenis_Kendaraan'] ?? $_GET['Jenis_K'] ?? '';
$catatan = $_GET['catatan'] ?? '';
$waktu_order = $_GET['waktu_order'] ?? date('Y-m-d H:i:s');
$result = [];

$order_header = mysqli_query($conn, "SELECT * FROM tb_order WHERE id_order = '$kode' LIMIT 1");
if ($order_header && $header = mysqli_fetch_array($order_header, MYSQLI_ASSOC)) {
    $no_Kendaraan = $header['no_Kendaraan'] ?? $no_Kendaraan;
    $customer = $header['pelanggan'] ?? $customer;
    $ukuran_Kendaraan = $header['ukuran_Kendaraan'] ?? $ukuran_Kendaraan;
    $jenis_Kendaraan = $header['jenis_Kendaraan'] ?? $jenis_Kendaraan;
    $catatan = $header['catatan'] ?? $catatan;
    $waktu_order = $header['waktu_order'] ?? $waktu_order;
}

$query = mysqli_query($conn, "SELECT
    tb_order.*,
    tb_order.jenis_Kendaraan AS jenis_K,
    tb_order.ukuran_Kendaraan AS ukuran_K,
    user.username,
    tb_list_order.*,
    tb_list_order.tarif AS menu,
    tb_tarif.nama_tarif AS nama,
    tb_tarif.nama_tarif,
    tb_tarif.keterangan_tarif,
    tb_tarif.bill_Tarif,
    tb_tarif.bill_PT,
    tb_tarif.bill_Karyawan,
    tb_tarif.bill_Operasional,
    tb_bayar.id_bayar
FROM tb_order
LEFT JOIN user ON user.id = tb_order.kasir
LEFT JOIN tb_list_order ON tb_list_order.kode_order = tb_order.id_order
LEFT JOIN tb_tarif ON tb_tarif.id = tb_list_order.tarif
LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_order
WHERE tb_order.id_order = '$kode'
ORDER BY tb_list_order.id_list_order ASC");

$jenis_Kendaraan_sql = mysqli_real_escape_string($conn, $jenis_Kendaraan);
$ukuran_Kendaraan_sql = mysqli_real_escape_string($conn, $ukuran_Kendaraan);
$set_menu = mysqli_query($conn, "SELECT tb_tarif.id, tb_tarif.nama_tarif AS nama, tb_tarif.nama_tarif
FROM tb_tarif
WHERE tb_tarif.jenis_Kendaraan = '$jenis_Kendaraan_sql'
AND tb_tarif.ukuran_Kendaraan = '$ukuran_Kendaraan_sql'
AND tb_tarif.status = 1");
$menu_options = [];
if ($set_menu) {
    while ($menu_record = mysqli_fetch_array($set_menu, MYSQLI_ASSOC)) {
        $menu_options[] = $menu_record;
    }
}

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

while ($record = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    if (!empty($record['id_list_order'])) {
        $jumlahItem = (int) ($record['jumlah'] ?? 0);
        $pembagian = carwash_resolve_tarif_breakdown($record);
        $record['harga_jual'] = $pembagian['bill_tarif'];
        $record['harga_jual_ppn'] = $pembagian['harga_jual'];
        $record['harga'] = $pembagian['bill_tarif'];
        $record['harganya'] = $pembagian['bill_tarif'] * $jumlahItem;
        $record['harganya_ppn'] = $pembagian['harga_jual'] * $jumlahItem;
        $record['harganya_pt'] = $pembagian['bill_pt'] * $jumlahItem;
        $record['harganya_karyawan'] = $pembagian['bill_karyawan'] * $jumlahItem;
        $record['harganya_operasional'] = $pembagian['bill_operasional'] * $jumlahItem;
        $record['harganya_toko'] = $record['harganya_karyawan'];
        $record['ppn_pajak'] = $pembagian['pajak'] * $jumlahItem;
        $record['billing'] = $pembagian;
        $result[] = $record;
    }
}

$diskonInput = isset($_POST['diskon_nominal']) ? $_POST['diskon_nominal'] : $diskon;
$diskonNominal = carwash_round_money($diskonInput);
$ringkasanOrder = carwash_calculate_order_totals($result, $diskonNominal);
$diskonNominal = $ringkasanOrder['diskon'];
$pajakPersenLabel = carwash_get_config()['pajak_persen'] ?? 0;
?>

<!-- Conten -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-fork-knife"></i>
            Setingan User
        </div>
        <div class="card-body-scrollable">
            <a href="order" class="btn btn-info mb-3">back</a>
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-floating ">
                        <input disabled type="text" class="form-control" id="id_order"
                            value="<?php echo $kode ?>" name="id_order">
                        <label for="floatingInputGambar">Kode Order</label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-floating ">
                        <input disabled type="text" class="form-control" id="no_Kendaraan"
                            value="<?php echo $no_Kendaraan ?>" name="no_Kendaraan">
                        <label for="floatingInputGambar">Nomor Kendaraan</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-floating ">
                        <input disabled type="text" class="form-control" id="pelanggan"
                            value="<?php echo $customer ?>" name="pelanggan">
                        <label for="floatingInputGambar">Type Kendaraan</label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-3">
                    <div class="form-floating ">
                        <input disabled type="text" class="form-control" id="jenis_Kendaraan"
                            value="<?php echo $jenis_Kendaraan ?>" name="jenis_Kendaraan">
                        <label for="jenis_Kendaraan">Jenis Kendaraan</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-floating ">
                        <input disabled type="text" class="form-control" id="ukuran_Kendaraan"
                            value="<?php echo $ukuran_Kendaraan ?>" name="ukuran_Kendaraan">
                        <label for="ukuran_Kendaraan">Ukuran Kendaraan</label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-floating ">
                        <input disabled type="text" class="form-control" id="toko"
                            value="<?php echo $catatan ?>" name="catatan">
                        <label for="floatingInputGambar">catatan</label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">

                </div>
                <div class="row mt-3">
                    <?php
                    include "inc/modal/modal_order_item.php";
                    ?>
                    <?php
                    if (empty($result)) {
                    } else {
                    ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Menu</th>
                                        <th scope="col">Harga</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Catatan</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    foreach ($result as $row) {
                                    ?>
                                        <tr>
                                            <td><?php echo $row['nama'] ?></td>
                                            <td>
                                                <?php
                                                if (isset($row['jenis_menu']) && (int)$row['jenis_menu'] === 3) {
                                                    echo number_format(($row['harga'] + $row['pajak']), 0, ',', '.');
                                                } else {
                                                    echo number_format($row['harga_jual'], 0, ',', '.');
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $row['jumlah'] ?></td>
                                            <td><?php echo $row['catatan_order'] ?></td>
                                            <td>
                                                <?php
                                                if (isset($row['jenis_menu']) && (int)$row['jenis_menu'] === 3) {
                                                    echo number_format($row['harganyanon'], 0, ',', '.');
                                                } else {
                                                    echo number_format($row['harganya'], 0, ',', '.');
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <?php
                                                    if ($_SESSION["level_kantin"] == 1) {

                                                    ?>
                                                        <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['id_list_order'] ?>"> <i class="bi bi-pencil-fill"></i></button>
                                                        <button class="btn btn-danger btn-sm me-2" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['id_list_order'] ?>"> <i class="bi bi-trash-fill"></i></button>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-2 disabled" : "btn btn-warning btn-sm me-2 ";  ?> " data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['id_list_order'] ?>"> <i class="bi bi-pencil-fill"></i></button>
                                                        <button class="<?php echo (!empty($row['id_bayar'])) ? "btn btn-secondary btn-sm me-2 disabled" : "btn btn-danger btn-sm me-2";  ?> " data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['id_list_order'] ?>"> <i class="bi bi-trash-fill"></i></button>
                                                    <?php
                                                    }
                                                    ?>


                                                </div>

                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td class="fw-bold" colspan="4">
                                            Total Harga
                                        </td>
                                        <td class="fw-bold">
                                            <?php echo number_format($ringkasanOrder['subtotal_tarif'], 0, ',', '.'); ?>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td colspan="4" class="fw-bold">
                                            Grand Total
                                        </td>
                                        <td class="fw-bold">
                                            <form method="post" action="">
                                                <div class="mb-2">
                                                    <label for="diskon_nominal" class="form-label">Diskon (Rp)</label>
                                                    <input type="number" min="0" max="<?php echo $ringkasanOrder['subtotal_tarif']; ?>" step="1" name="diskon_nominal" id="diskon_nominal" class="form-control form-control-sm d-inline-block" style="width:120px;" value="<?php echo htmlspecialchars($diskonNominal); ?>" onchange="this.form.submit()">
                                                </div>
                                            </form>
                                            <div>Diskon: -<?php echo number_format($ringkasanOrder['diskon'], 0, ',', '.'); ?></div>
                                            <div>Total Harga: <?php echo number_format($ringkasanOrder['subtotal_setelah_diskon'], 0, ',', '.'); ?></div>
                                            <div>PPN <?php echo $pajakPersenLabel; ?>%: <?php echo number_format($ringkasanOrder['subtotal_ppn'], 0, ',', '.'); ?></div>
                                            <div class="fw-bold">Grand Total: <?php echo number_format($ringkasanOrder['grand_total'], 0, ',', '.'); ?></div>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    <?php
                    }
                    ?>
                    <div>
                        <?php
                        if ($_SESSION["level_kantin"] == 1) {
                        ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahItem"><i class="bi bi-plus-square-dotted"></i> Item</button>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#tambahAddon"><i class="bi bi-plus-square-dotted"></i> Addon</button>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bayar"><i class="bi bi-cash-coin"></i> Bayar</button>
                            <button class="btn btn-info" onclick="printStruk()"><i class="bi bi-printer"></i> Print Struk</button>
                        <?php
                        } else {
                            // Cek apakah sudah bayar (id_bayar tidak kosong pada salah satu item)
                            $sudah_bayar = false;
                            if (!empty($result)) {
                                foreach ($result as $row) {
                                    if (!empty($row['id_bayar'])) {
                                        $sudah_bayar = true;
                                        break;
                                    }
                                }
                            }
                        ?>
                            <button class="<?php echo $sudah_bayar ? "btn btn-secondary disabled" : "btn btn-primary"; ?>" data-bs-toggle="modal" data-bs-target="#tambahItem"><i class="bi bi-plus-square-dotted"></i> Item</button>
                            <button class="<?php echo $sudah_bayar ? "btn btn-secondary disabled" : "btn btn-info"; ?>" data-bs-toggle="modal" data-bs-target="#tambahAddon"><i class="bi bi-plus-square-dotted"></i> Addon</button>
                            <button class="<?php echo $sudah_bayar ? "btn btn-secondary disabled" : "btn btn-success"; ?>" data-bs-toggle="modal" data-bs-target="#bayar"><i class="bi bi-cash-coin"></i> Bayar</button>
                            <button class="btn btn-info<?php echo $sudah_bayar ? '' : ' disabled'; ?>" onclick="if(<?php echo $sudah_bayar ? 'true' : 'false'; ?>) printStruk()"><i class="bi bi-printer"></i> Print Struk</button>
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="strukContent" style="display: none;">

        <style>
            /* --- CSS STYLING UNTUK STRUK KASIR (60MM) - FONT TEBAL --- */
            #struk_body {
                width: 60mm;
                font-family: 'Courier New', monospace;
                text-align: left;
                font-size: 12px;
                padding: 5px;
                /* PENTING: Membuat semua teks di body menjadi tebal secara default */
                font-weight: bold;
            }

            #struk_body h2 {
                text-align: center;
                margin: 5px 0;
                /* Judul lebih besar dan tebal */
                font-size: 16px;
                font-weight: 900;
                /* Atau bolder */
            }

            #struk_body table {
                width: 100%;
                font-size: 12px;
                text-align: left;
                margin: 5px 0;
                border-collapse: collapse;
            }

            #struk_body table th,
            #struk_body table td {
                border: none;
                padding: 1px 0;
                vertical-align: top;
                font-weight: bold;
            }

            /* Utility classes untuk perataan teks */
            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            /* Garis pemisah */
            .separator {
                border-top: 1px dashed black;
                margin: 3px 0;
            }

            .grand-total-line {
                border-top: 2px solid black;
                padding-top: 5px;
                margin-top: 5px;
            }

            /* Style untuk detail kecil (harga satuan, catatan) - dibuat tidak tebal agar ada kontras */
            .small-detail {
                font-size: 10px;
                font-style: italic;
                font-weight: normal;
                /* Override bold agar tidak terlalu ramai */
            }
        </style>

        <div id="struk_body" class="container">
            <h2 class="text-center">Carwash Demo</h2>
            <h2 class="text-center">Struk Pembayaran</h2>

            <div class="separator"></div>

            <div>Waktu Order: <?php echo $waktu_order ?></div>
            <div>Kode Order: <?php echo $kode; ?></div>
            <div>No Kendaraan: <?php echo $no_Kendaraan; ?> / Pelanggan: <?php echo $customer; ?></div>
            <div>Jenis Kendaraan: <?php echo $jenis_Kendaraan; ?></div>
            <div>Ukuran Kendaraan: <?php echo $ukuran_Kendaraan; ?></div>
            <div>Catatan: <?php echo $catatan; ?></div>

            <div class="separator"></div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 55%;">Menu</th>
                        <th class="text-center" style="width: 10%;">Qty</th>
                        <th class="text-right" style="width: 35%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result as $row) {
                        $harga_unit = number_format($row['harga_jual'], 0, ',', '.');
                        $total_item = number_format($row['harganya'], 0, ',', '.');
                    ?>
                        <tr>
                            <td colspan="1">
                                <?php echo $row['nama']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $row['jumlah']; ?>
                            </td>
                            <td class="text-right">
                                <?php echo $total_item; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="small-detail">
                                @ <?php echo $harga_unit; ?>
                            </td>
                            <td colspan="2" class="small-detail">
                                <?php echo (!empty($row['catatan_order']) ? 'Catatan: ' . $row['catatan_order'] : ''); ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

            <div class="separator"></div>

            <div class="text-right">
                <div>Subtotal: <?php echo number_format($ringkasanOrder['subtotal_tarif'], 0, ',', '.'); ?></div>
                <div>Diskon: -<?php echo number_format($ringkasanOrder['diskon'], 0, ',', '.'); ?></div>
                <div>Total Harga: <?php echo number_format($ringkasanOrder['subtotal_setelah_diskon'], 0, ',', '.'); ?></div>
                <div>PPN <?php echo $pajakPersenLabel; ?>%: <?php echo number_format($ringkasanOrder['subtotal_ppn'], 0, ',', '.'); ?></div>

                <h3 class="grand-total-line">
                    Grand Total: <?php echo number_format($ringkasanOrder['grand_total'], 0, ',', '.'); ?>
                </h3>

            </div>
            <div class="text-center small-detail" style="margin-top: 10px;font-weight: bold;">
                *PPN <?php echo $pajakPersenLabel; ?>% ditambahkan pada total akhir
            </div>
            <div class="text-center small-detail" style="margin-top: 10px;font-weight: bold;">
                TERIMA KASIH ATAS KUNJUNGAN ANDA!
            </div>

        </div>
    </div>



    <script>
        function printStruk() {
            var strukContent = document.getElementById("strukContent").innerHTML;
            var printFrame = document.createElement("iframe");
            printFrame.style.display = "none";
            document.body.appendChild(printFrame);
            printFrame.contentDocument.write(strukContent);
            printFrame.contentWindow.print();
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#menu-pilihan').select2({
                // Opsi untuk placeholder, akan muncul di kotak pencarian
                placeholder: 'Ketik nama menu...',
                allowClear: true // Memungkinkan pengguna untuk menghapus pilihan
            });
        });
    </script>


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
    <style>
        .logo-struk {
            width: 5px;
            height: auto;
        }
    </style>

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