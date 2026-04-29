<?php
// Script untuk migrasi ID lama ke format DDMMYYXXX
// Jalankan via browser: http://localhost/metafora/ci3/migrate_ids.php

define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development'); // Tambahkan ini agar database.php tidak error
require_once 'application/config/database.php';
$db_cfg = $db['default'];

$conn = mysqli_connect($db_cfg['hostname'], $db_cfg['username'], $db_cfg['password'], $db_cfg['database'], 3309);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

echo "<pre>Starting Migration...\n";

// 1. Migrasi Tabel Transaksi & Update Pengembalian (Reference)
echo "Migrating 'transaksi' table...\n";
$res = mysqli_query($conn, "SELECT id_transaksi, tanggal FROM transaksi ORDER BY tanggal ASC, id_transaksi ASC");
$mapping_trx = [];
$counters = [];

while ($row = mysqli_fetch_assoc($res)) {
    $date = date('dmy', strtotime($row['tanggal']));
    if (!isset($counters[$date])) $counters[$date] = 1;
    
    $new_id = $date . str_pad($counters[$date], 3, '0', STR_PAD_LEFT);
    $old_id = $row['id_transaksi'];
    
    $mapping_trx[$old_id] = $new_id;
    $counters[$date]++;
}

// Matikan foreign key check jika ada
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

foreach ($mapping_trx as $old => $new) {
    // Update transaksi
    mysqli_query($conn, "UPDATE transaksi SET id_transaksi = $new WHERE id_transaksi = $old");
    // Update pengembalian yang referensi ke transaksi ini
    mysqli_query($conn, "UPDATE pengembalian SET id_transaksi = $new WHERE id_transaksi = $old");
    echo "Trx: $old -> $new\n";
}

// 2. Migrasi Tabel Pembelian
echo "\nMigrating 'pembelian' table...\n";
$res = mysqli_query($conn, "SELECT id_pembelian, tanggal_beli FROM pembelian ORDER BY tanggal_beli ASC, id_pembelian ASC");
$counters_beli = [];
while ($row = mysqli_fetch_assoc($res)) {
    $date = date('dmy', strtotime($row['tanggal_beli']));
    if (!isset($counters_beli[$date])) $counters_beli[$date] = 1;
    
    $new_id = $date . str_pad($counters_beli[$date], 3, '0', STR_PAD_LEFT);
    $old_id = $row['id_pembelian'];
    
    mysqli_query($conn, "UPDATE pembelian SET id_pembelian = $new WHERE id_pembelian = $old_id");
    echo "Beli: $old_id -> $new\n";
    $counters_beli[$date]++;
}

// 3. Migrasi Tabel Pengembalian (ID Return)
echo "\nMigrating 'pengembalian' table (id_return)...\n";
$res = mysqli_query($conn, "SELECT id_return, tanggal_return FROM pengembalian ORDER BY tanggal_return ASC, id_return ASC");
$counters_ret = [];
while ($row = mysqli_fetch_assoc($res)) {
    $date = date('dmy', strtotime($row['tanggal_return']));
    if (!isset($counters_ret[$date])) $counters_ret[$date] = 1;
    
    $new_id = $date . str_pad($counters_ret[$date], 3, '0', STR_PAD_LEFT);
    $old_id = $row['id_return'];
    
    mysqli_query($conn, "UPDATE pengembalian SET id_return = $new WHERE id_return = $old_id");
    echo "Return: $old_id -> $new\n";
    $counters_ret[$date]++;
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
echo "\nMigration Finished!</pre>";
?>
