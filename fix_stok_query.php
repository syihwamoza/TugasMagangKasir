<?php
$files = ['barang.php', 'keranjang.php', 'beli.php', 'edit.php'];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace SELECT * FROM barang
    $content = preg_replace('/SELECT \*\s+FROM barang\s*(WHERE|ORDER|LIMIT|)/i', 'SELECT b.*, (b.stok_awal + COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = b.nama_barang), 0) - COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = b.nama_barang), 0) + COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = b.nama_barang), 0)) AS stok_aktif FROM barang b $1', $content);

    // Replace usages of $d['stok'] with calculated
    $content = str_replace('[\'stok\']', '[\'stok_aktif\']', $content);
    $content = str_replace('["stok"]', '["stok_aktif"]', $content);
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
?>
