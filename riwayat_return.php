<?php 
session_start();
include 'config/koneksi.php'; 

// --- LOGIKA PENCARIAN ---
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = !empty($cari) ? " WHERE nama_barang LIKE '%$cari%' " : "";

// --- LOGIKA PAGINATION ---
$jumlahDataPerHalaman = 20; 
$queryCek = mysqli_query($conn, "SELECT * FROM pengembalian $where"); 
$jumlahData = mysqli_num_rows($queryCek);
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
$halamanAktif = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Retur - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .table-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background-color: #34495e; color: white; padding: 15px; text-transform: uppercase; font-size: 12px; }
        tbody td { padding: 15px; border-bottom: 1px solid #edf2f7; text-align: center; }

        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
        .page-link { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #34495e; border-radius: 5px; transition: 0.3s; }
        .active-page { background-color: #27ae60; color: white; border-color: #27ae60; font-weight: bold; }

        /* Style Form Cari */
        .btn-filter { background: #34495e; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .input-cari { padding: 8px 15px; border: 1px solid #ddd; border-radius: 5px; width: 250px; }
    </style>
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container">
        <div class="table-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin:0; color: #2c3e50; border-left: 5px solid #e74c3c; padding-left: 15px;">Riwayat Pengembalian Barang</h2>
                
                <form method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="cari" class="input-cari" value="<?php echo $cari; ?>" placeholder="Cari barang yang di-return...">
                    <button type="submit" class="btn-filter">Cari</button>
                    <?php if(!empty($cari)): ?>
                        <a href="riwayat_return.php" style="text-decoration:none; color:#666; font-size:12px; align-self:center;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Alasan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $awalData + 1;
                    $res = mysqli_query($conn, "SELECT * FROM pengembalian $where ORDER BY tanggal_return DESC LIMIT $awalData, $jumlahDataPerHalaman");
                    
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td style="text-align: left; font-weight: bold;"><?php echo $row['nama_barang']; ?></td>
                        <td style="color: #e74c3c; font-weight: bold;"><?php echo $row['jumlah_return']; ?></td>
                        <td><?php echo $row['alasan']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_return'])); ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding:30px; color:#999;'>Data tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if($halamanAktif > 1) : ?>
                    <a href="?halaman=<?php echo $halamanAktif - 1; ?>&cari=<?php echo $cari; ?>" class="page-link">&laquo; Prev</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $jumlahHalaman; $i++) : ?>
                    <a href="?halaman=<?php echo $i; ?>&cari=<?php echo $cari; ?>" 
                       class="page-link <?php echo ($i == $halamanAktif) ? 'active-page' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if($halamanAktif < $jumlahHalaman) : ?>
                    <a href="?halaman=<?php echo $halamanAktif + 1; ?>&cari=<?php echo $cari; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>