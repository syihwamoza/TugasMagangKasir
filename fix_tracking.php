<?php
$files = ['tracking_stok.php', 'exports/cetak_laporan_stok.php', 'exports/export_stok_excel.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Re-calculate stok_akhir on the fly instead of relying on $b['stok']
        $content = preg_replace('/\$stok_akhir\s*=\s*\$b\[\'stok\'\];/', '$stok_akhir = $stok_awal + $tambah - $terjual + $direturn;', $content);
        
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
?>
