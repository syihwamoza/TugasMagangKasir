<?php
$files = ['tracking_stok.php', 'exports/cetak_laporan_stok.php', 'exports/export_stok_excel.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remove the early incorrect calculation
        $content = preg_replace('/\$stok_akhir\s*=\s*\$stok_awal\s*\+\s*\$tambah\s*-\s*\$terjual\s*\+\s*\$direturn;\s*/', '', $content);
        
        // Add the calculation correctly AFTER variables are defined
        // We look for where getBarangStokAwal is defined and place it right after
        $content = preg_replace('/(\$stok_awal\s*=\s*getBarangStokAwal\(\$conn,\s*\$[a-zA-Z]+\);)/', "$1\n                    \$stok_akhir = \$stok_awal + \$tambah - \$terjual + \$direturn;", $content);
        
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
?>
