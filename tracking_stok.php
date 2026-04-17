<?php 
session_start();
include 'config/koneksi.php'; 

ensureBarangStokAwalColumn($conn);
ensureStockAdjustmentTable($conn);

// --- LOGIKA PENCARIAN ---
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = !empty($cari) ? " WHERE nama_barang LIKE '%$cari%' " : "";

// --- LOGIKA PAGINATION ---
$jumlahDataPerHalaman = 20; 
$queryCek = mysqli_query($conn, "SELECT * FROM barang $where");
$jumlahData = mysqli_num_rows($queryCek);
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
$halamanAktif = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

function formatAngka($angka) {
    if ($angka === 0 || $angka === '0' || $angka === null) {
        return "-";
    }

    return (int)$angka;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tracking Stok - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <style>
        /* Penyesuaian agar tidak ketutup sidebar */
        .container-tracking { 
            margin: 30px auto; 
            max-width: 1100px;
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background-color: #34495e; color: white; padding: 15px; font-size: 12px; text-transform: uppercase; }
        tbody td { padding: 15px; border-bottom: 1px solid #edf2f7; text-align: center; }

        .klik-detail { color: inherit; text-decoration: none; display: block; width: 100%; height: 100%; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .klik-detail:hover { background-color: #f0f0f0; border-radius: 5px; color: #27ae60; }

        .stok-akhir-col { background: #f8f9fa; font-weight: bold; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
        .page-link { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #34495e; border-radius: 5px; }
        .active-page { background-color: #27ae60; color: white; border-color: #27ae60; }
        
        .btn-print { background: #27ae60; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; display: flex; align-items: center; }
    </style>
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container-tracking">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin:0; color: #2c3e50;">Tracking Pergerakan Stok</h2>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <form method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="cari" value="<?php echo $cari; ?>" placeholder="Cari barang..." style="padding:8px; border-radius:5px; border:1px solid #ddd;">
                    <button type="submit" style="padding:8px 15px; background:#34495e; color:white; border:none; border-radius:5px; cursor:pointer;">Cari</button>
                </form>

                <a href="exports/cetak_laporan_stok.php?cari=<?php echo $cari; ?>" target="_blank" class="btn-print">Print Laporan</a>

                <a href="exports/export_stok_excel.php?cari=<?php echo $cari; ?>" class="btn-print" style="background: #27ae60;">Export Excel</a>

                <?php if(!empty($cari)): ?>
                    <a href="tracking_stok.php" style="text-decoration:none; color:#e74c3c; font-size:12px; font-weight:bold;">Reset</a>
                <?php endif; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: left; padding-left: 20px;">Nama Barang</th>
                    <th>Stok Awal</th>
                    <th>Tambah</th>
                    <th>Terjual</th>
                    <th>Diretur</th>
                    <th>Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM barang $where ORDER BY nama_barang ASC LIMIT $awalData, $jumlahDataPerHalaman");
                while($b = mysqli_fetch_array($res)){
                    $nama = $b['nama_barang'];
                    $q_beli = mysqli_query($conn, "SELECT SUM(jumlah_beli) as total FROM pembelian WHERE nama_barang = '$nama'");
                    $tambah = mysqli_fetch_assoc($q_beli)['total'] ?? 0;

                    $q_jual = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE nama_barang = '$nama'");
                    $terjual = mysqli_fetch_assoc($q_jual)['total'] ?? 0;

                    $q_return = mysqli_query($conn, "SELECT SUM(jumlah_return) as total FROM pengembalian WHERE nama_barang = '$nama'");
                    $direturn = mysqli_fetch_assoc($q_return)['total'] ?? 0;

                    // Gunakan stok_awal yang tersimpan, agar nilai Stok Awal tetap sama meskipun ada restock / edit stok
                    $stok_awal = getBarangStokAwal($conn, $b);
                    $stok_akhir = $stok_awal + $tambah - $terjual + $direturn;
                ?>
                <tr>
                    <td style="text-align: left; padding-left: 20px; font-weight: bold; color: #2c3e50;"><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?php echo $nama; ?>', 'all')"><?php echo $nama; ?></a></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?php echo $nama; ?>', 'all')"><?php echo formatAngka($stok_awal); ?></a></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?php echo $nama; ?>', 'restock')" style="color:#2980b9;"><?php echo formatAngka($tambah); ?></a></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?php echo $nama; ?>', 'sell')" style="color:#e67e22;"><?php echo formatAngka($terjual); ?></a></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?php echo $nama; ?>', 'return')" style="color:#27ae60;"><?php echo formatAngka($direturn); ?></a></td>
                    <td class="stok-akhir-col"><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?php echo $nama; ?>', 'all')"><?php echo formatAngka($stok_akhir); ?></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for($i = 1; $i <= $jumlahHalaman; $i++) : ?>
                <a href="?halaman=<?php echo $i; ?>&cari=<?php echo $cari; ?>" class="page-link <?php echo ($i == $halamanAktif) ? 'active-page' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <div id="modalDetail" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; backdrop-filter: blur(4px);">
        <div style="background:white; width:700px; margin:5% auto; padding:0; border-radius:15px; overflow:hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.3);">
            <div style="background:#34495e; padding:20px; color:white; display:flex; justify-content:space-between; align-items:center;">
                <h3 id="judulModal" style="margin:0; font-size:18px;">Detail Riwayat</h3>
                <button onclick="tutupModal()" style="border:none; background:none; color:white; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div id="isiDetail" style="max-height:450px; overflow-y:auto; padding:20px;">
                <p style="text-align:center;">Memuat data riwayat...</p>
            </div>
            <div style="padding:15px; border-top:1px solid #eee; text-align:right; background:#f9f9f9;">
                <button onclick="tutupModal()" style="padding:8px 20px; border-radius:5px; border:1px solid #ccc; cursor:pointer;">Tutup</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function tampilDetail(nama, jenis = 'all') {
        $('#modalDetail').fadeIn(300);
        $('#judulModal').text(" Riwayat Stok: " + nama);
        $('#isiDetail').html("<div style='text-align:center; padding:20px;'><p>Mengambil data dari server...</p></div>");
        
        $.ajax({
            url: 'actions/get_detail_stok.php',
            method: 'POST',
            data: {nama_barang: nama, jenis: jenis},
            success: function(data) {
                $('#isiDetail').html(data);
            },
            error: function() {
                $('#isiDetail').html("<p style='color:red; text-align:center;'>Gagal mengambil data. Pastikan file get_detail_stok.php tersedia.</p>");
            }
        });
    }

    function tutupModal() {
        $('#modalDetail').fadeOut(200);
    }

    $(window).click(function(event) {
        if (event.target.id == "modalDetail") {
            tutupModal();
        }
    });
    </script>
</body>
</html>