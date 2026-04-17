<?php
$root = __DIR__;

$dirs = ['config', 'components', 'assets', 'actions', 'exports'];
foreach ($dirs as $dir) {
    if (!is_dir("$root/$dir")) mkdir("$root/$dir");
}

$files_config = ['koneksi.php'];
$files_components = ['navbar.php'];
$files_assets = ['style.css'];
$files_actions = [
    'aksi_barang.php', 'edit_barang_aksi.php', 'hapus_barang.php', 
    'keranjang_aksi.php', 'login_aksi.php', 'logout.php', 
    'proses_return_banyak.php', 'proses_tambah.php', 'get_detail_stok.php'
];
$files_exports = [
    'cetak_laporan_stok.php', 'cetak_struk.php', 'cetak_invoice.php', 
    'export_stok_excel.php'
];

$all_moves = [
    'config' => $files_config,
    'components' => $files_components,
    'assets' => $files_assets,
    'actions' => $files_actions,
    'exports' => $files_exports
];

// Perform Moves
foreach ($all_moves as $dir => $files) {
    foreach ($files as $file) {
        if (file_exists("$root/$file")) {
            rename("$root/$file", "$root/$dir/$file");
        }
    }
}

// Function to process file contents based on depth
function process_file($filePath, $depth = 0) {
    if (!file_exists($filePath)) return;
    $content = file_get_contents($filePath);
    
    $prefix = ($depth == 1) ? '../' : '';

    // Replace koneksi include
    $content = str_replace(["include 'koneksi.php'", "require 'koneksi.php'"], "include '{$prefix}config/koneksi.php'", $content);
    $content = str_replace(["include \"koneksi.php\"", "require \"koneksi.php\""], "include \"{$prefix}config/koneksi.php\"", $content);

    if ($depth == 0) {
        // Root file
        $content = str_replace("include 'navbar.php'", "include 'components/navbar.php'", $content);
        $content = str_replace('href="style.css"', 'href="assets/style.css?v=<?php echo time(); ?>"', $content);
        
        // Actions
        foreach ($GLOBALS['files_actions'] as $act) {
            $content = preg_replace("/('|\")($act)('|\")/", "$1actions/$act$3", $content);
        }
        // Exports
        foreach ($GLOBALS['files_exports'] as $exp) {
            $content = preg_replace("/('|\")($exp)/", "$1exports/$exp", $content);
        }
    } else {
        // Nested file (Actions or Exports)
        // Adjust Header Locations pointing to root
        $root_pages = [
            'barang.php', 'login.php', 'keranjang.php', 'beli.php', 'edit.php', 
            'tambah_barang.php', 'return_barang.php', 'riwayat.php', 'riwayat_return.php', 'tracking_stok.php'
        ];
        foreach ($root_pages as $page) {
            $content = preg_replace("/Location:\s*('|\")?($page)/", "Location: $1../$2", $content);
        }
    }
    
    // Specially fix koneksi.php role checking paths because SCRIPT_NAME will now have the subdirectories occasionally
    // We don't strictly need to do this if SCRIPT_NAME returns basename but let's just make sure
    
    file_put_contents($filePath, $content);
}

// Process Root Files
$root_files = glob("$root/*.php");
foreach ($root_files as $f) {
    if (basename($f) == 'refactor.php') continue;
    process_file($f, 0);
}

// Process Nested Files
foreach (['actions', 'exports'] as $dir) {
    if ($handle = opendir("$root/$dir")) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && pathinfo($entry, PATHINFO_EXTENSION) == 'php') {
                process_file("$root/$dir/$entry", 1);
            }
        }
        closedir($handle);
    }
}

echo "Refactoring successful!";
?>
