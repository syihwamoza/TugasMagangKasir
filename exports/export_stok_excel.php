<?php
include '../config/koneksi.php';

ensureBarangStokAwalColumn($conn);
ensureStockAdjustmentTable($conn);

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = !empty($cari) ? " WHERE nama_barang LIKE '%$cari%' " : "";

// Get data
$res = mysqli_query($conn, "SELECT * FROM barang $where ORDER BY nama_barang ASC");

// Header untuk download file CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="Laporan_Stok_' . date('d-m-Y_Hi') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM untuk UTF-8
echo chr(0xEF) . chr(0xBB) . chr(0xBF);

// Header
echo "No,Nama Barang,Stok Awal,Barang Masuk,Terjual,Direturn,Saldo Akhir\n";

$no = 1;
if(mysqli_num_rows($res) > 0) {
    while($b = mysqli_fetch_array($res)){
        $nama = $b['nama_barang'];
        // Hitung data pendukung
        $tambah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_beli) as total FROM pembelian WHERE nama_barang = '$nama'"))['total'] ?? 0;
        $terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE nama_barang = '$nama'"))['total'] ?? 0;
        $direturn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_return) as total FROM pengembalian WHERE nama_barang = '$nama'"))['total'] ?? 0;

        $stok_awal = getBarangStokAwal($conn, $b);
                    $stok_akhir = $stok_awal + $tambah - $terjual + $direturn;

        // Escape quotes dan format data
        $nama_escaped = '"' . str_replace('"', '""', $nama) . '"';
        
        echo $no . "," . $nama_escaped . "," . 
             (($stok_awal > 0) ? $stok_awal : 0) . "," . 
             (($tambah > 0) ? $tambah : 0) . "," . 
             (($terjual > 0) ? $terjual : 0) . "," . 
             (($direturn > 0) ? $direturn : 0) . "," . 
             $stok_akhir . "\n";
        $no++;
    }
}
?>



