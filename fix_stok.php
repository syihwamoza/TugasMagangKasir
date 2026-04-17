<?php
$files = [
    'return_barang.php',
    'actions/proses_return_banyak.php',
    'actions/keranjang_aksi.php',
    'actions/edit_barang_aksi.php',
    'edit.php',
    'beli.php',
    'belanja_supplier.php',
    'actions/aksi_barang.php',
    'tambah_barang.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remove UPDATE barang SET stok queries
        $content = preg_replace('/mysqli_query\(\$conn,\s*"UPDATE barang SET stok =[^"]+"\);/i', '// removed stok update', $content);
        $content = preg_replace('/\$update_stok\s*=\s*mysqli_query\(\$conn,\s*"UPDATE barang SET stok =[^"]+"\);/i', '// removed stok update', $content);
        
        // edit.php specific update (harga only)
        $content = preg_replace('/"UPDATE barang SET stok = \'\$stok_baru\', harga = \'\$harga_baru\' WHERE id_barang = \'\$id\'"/', '"UPDATE barang SET harga = \'$harga_baru\' WHERE id_barang = \'$id\'"', $content);

        // tambah_barang.php setup
        $content = preg_replace('/"INSERT INTO barang \(nama_barang, harga, stok, stok_awal\) VALUES \(\'\$nama\', \'\$harga\', \'\$stok\', \'\$stok\'\)"/', '"INSERT INTO barang (nama_barang, harga, stok, stok_awal) VALUES (\'$nama\', \'$harga\', \'$stok\', \'$stok\')" // NOTE: stok might be removed later', $content);

        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
?>
