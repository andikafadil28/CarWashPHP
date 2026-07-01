<?php

const CARWASH_PAJAK_PERSEN = 5;
const CARWASH_BILL_KARYAWAN_PERSEN = 25;
const CARWASH_BILL_PT_PERSEN = 65;
const CARWASH_BILL_OPERASIONAL_PERSEN = 10;

function carwash_get_config()
{
    return [
        'pajak_persen' => CARWASH_PAJAK_PERSEN,
        'bill_karyawan_persen' => CARWASH_BILL_KARYAWAN_PERSEN,
        'bill_pt_persen' => CARWASH_BILL_PT_PERSEN,
        'bill_operasional_persen' => CARWASH_BILL_OPERASIONAL_PERSEN,
    ];
}

function carwash_normalize_amount($value)
{
    if (is_string($value)) {
        $value = str_replace([',', ' '], ['', ''], $value);
    }

    return max(0, (float) $value);
}

function carwash_round_money($value)
{
    return (int) round((float) $value);
}

function carwash_hitung_pembagian($hargaDasar)
{
    $config = carwash_get_config();
    $hargaDasar = carwash_round_money(carwash_normalize_amount($hargaDasar));

    $billKaryawan = carwash_round_money($hargaDasar * ($config['bill_karyawan_persen'] / 100));
    $billPT = carwash_round_money($hargaDasar * ($config['bill_pt_persen'] / 100));
    $billOperasional = carwash_round_money($hargaDasar * ($config['bill_operasional_persen'] / 100));

    $selisih = $hargaDasar - ($billKaryawan + $billPT + $billOperasional);
    if ($selisih !== 0) {
        $billPT += $selisih;
    }

    $pajak = carwash_round_money($hargaDasar * ($config['pajak_persen'] / 100));

    return [
        'harga_dasar' => $hargaDasar,
        'bill_karyawan' => $billKaryawan,
        'bill_pt' => $billPT,
        'bill_operasional' => $billOperasional,
        'pajak' => $pajak,
        'harga_jual' => $hargaDasar + $pajak,
    ];
}

function carwash_resolve_tarif_breakdown(array $row)
{
    $billTarif = carwash_round_money($row['bill_Tarif'] ?? 0);
    $billPT = carwash_round_money($row['bill_PT'] ?? 0);
    $billKaryawan = carwash_round_money($row['bill_Karyawan'] ?? 0);
    $billOperasional = carwash_round_money($row['bill_Operasional'] ?? 0);
    $pajak = 0;
    $hargaJual = $billTarif;

    if ($billTarif <= 0) {
        $billTarif = $billPT + $billKaryawan + $billOperasional;
    }

    if ($billTarif > 0) {
        $pembagian = carwash_hitung_pembagian($billTarif);
        $billPT = $pembagian['bill_pt'];
        $billKaryawan = $pembagian['bill_karyawan'];
        $billOperasional = $pembagian['bill_operasional'];
        $pajak = $pembagian['pajak'];
        $hargaJual = $pembagian['harga_jual'];
    }

    return [
        'bill_tarif' => $billTarif,
        'bill_pt' => $billPT,
        'bill_karyawan' => $billKaryawan,
        'bill_operasional' => $billOperasional,
        'pajak' => $pajak,
        'harga_jual' => $hargaJual,
    ];
}

function carwash_calculate_order_totals(array $items, $diskon = 0)
{
    $subtotalTarif = 0;
    $subtotalPpn = 0;
    $totalPT = 0;
    $totalKaryawan = 0;
    $totalOperasional = 0;

    foreach ($items as $item) {
        $jumlah = (int) ($item['jumlah'] ?? 0);
        if ($jumlah <= 0) {
            continue;
        }

        $pembagian = $item['billing'] ?? carwash_resolve_tarif_breakdown($item);
        $subtotalTarif += $pembagian['bill_tarif'] * $jumlah;
        $subtotalPpn += $pembagian['pajak'] * $jumlah;
        $totalPT += $pembagian['bill_pt'] * $jumlah;
        $totalKaryawan += $pembagian['bill_karyawan'] * $jumlah;
        $totalOperasional += $pembagian['bill_operasional'] * $jumlah;
    }

    $ringkasanDiskon = carwash_apply_discount_to_breakdown(
        $subtotalTarif,
        $totalPT,
        $totalKaryawan,
        $totalOperasional,
        $diskon
    );

    return [
        'subtotal_tarif' => $subtotalTarif,
        'subtotal_ppn' => $subtotalPpn,
        'subtotal_setelah_diskon' => $ringkasanDiskon['grand_total'],
        'diskon' => $ringkasanDiskon['diskon'],
        'grand_total' => $ringkasanDiskon['grand_total'] + $subtotalPpn,
        'nominal_pt' => $ringkasanDiskon['nominal_pt'],
        'nominal_karyawan' => $ringkasanDiskon['nominal_karyawan'],
        'nominal_operasional' => $ringkasanDiskon['nominal_operasional'],
    ];
}

function carwash_apply_discount_to_breakdown($totalTarif, $billPT, $billKaryawan, $billOperasional, $diskon)
{
    $totalTarif = carwash_round_money($totalTarif);
    $billPT = carwash_round_money($billPT);
    $billKaryawan = carwash_round_money($billKaryawan);
    $billOperasional = carwash_round_money($billOperasional);
    $diskon = min(carwash_round_money($diskon), $totalTarif);

    if ($totalTarif <= 0) {
        return [
            'diskon' => 0,
            'grand_total' => 0,
            'nominal_pt' => 0,
            'nominal_karyawan' => 0,
            'nominal_operasional' => 0,
        ];
    }

    $nominalPT = carwash_round_money($billPT - (($billPT / $totalTarif) * $diskon));
    $nominalKaryawan = carwash_round_money($billKaryawan - (($billKaryawan / $totalTarif) * $diskon));
    $nominalOperasional = carwash_round_money($billOperasional - (($billOperasional / $totalTarif) * $diskon));
    $grandTotal = $totalTarif - $diskon;
    $selisih = $grandTotal - ($nominalPT + $nominalKaryawan + $nominalOperasional);

    if ($selisih !== 0) {
        $nominalPT += $selisih;
    }

    return [
        'diskon' => $diskon,
        'grand_total' => $grandTotal,
        'nominal_pt' => $nominalPT,
        'nominal_karyawan' => $nominalKaryawan,
        'nominal_operasional' => $nominalOperasional,
    ];
}

function carwash_column_exists($conn, $table, $column)
{
    $table = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");

    return $result && mysqli_num_rows($result) > 0;
}

function carwash_sync_schema($conn)
{
    static $isSynced = false;

    if ($isSynced || !$conn) {
        return;
    }

    if (!carwash_column_exists($conn, 'tb_tarif', 'bill_Operasional')) {
        mysqli_query($conn, "ALTER TABLE tb_tarif ADD COLUMN bill_Operasional DOUBLE NOT NULL DEFAULT 0 AFTER bill_Karyawan");
    }

    if (!carwash_column_exists($conn, 'tb_tarif', 'bill_Tarif')) {
        mysqli_query($conn, "ALTER TABLE tb_tarif ADD COLUMN bill_Tarif DOUBLE NOT NULL DEFAULT 0 AFTER keterangan_tarif");
    }

    if (!carwash_column_exists($conn, 'tb_bayar', 'nominal_operasional')) {
        mysqli_query($conn, "ALTER TABLE tb_bayar ADD COLUMN nominal_operasional DOUBLE NOT NULL DEFAULT 0 AFTER nominal_karyawan");
    }

    if (carwash_column_exists($conn, 'tb_tarif', 'bill_Tarif')) {
        mysqli_query(
            $conn,
            "UPDATE tb_tarif
            SET bill_Tarif = COALESCE(bill_PT, 0) + COALESCE(bill_Karyawan, 0) + COALESCE(bill_Operasional, 0)
            WHERE COALESCE(bill_Tarif, 0) = 0
            AND (COALESCE(bill_PT, 0) + COALESCE(bill_Karyawan, 0) + COALESCE(bill_Operasional, 0)) > 0"
        );
    }

    $isSynced = true;
}
