<?php
include '../config/koneksi.php';

if (isset($_POST['nama_barang'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : 'all';

    // Pilih query berdasarkan jenis detail yang diminta
    if ($jenis === 'restock') {
        $sql = "SELECT tanggal_beli as tgl, 'Restock Barang' as ket, jumlah_beli as masuk, 0 as keluar FROM pembelian WHERE nama_barang = '$nama' ORDER BY tgl DESC";
    } elseif ($jenis === 'sell') {
        $sql = "SELECT tanggal as tgl, 'Penjualan' as ket, 0 as masuk, jumlah as keluar FROM transaksi WHERE nama_barang = '$nama' ORDER BY tgl DESC";
    } elseif ($jenis === 'return') {
        $sql = "SELECT tanggal_return as tgl, 'Barang Return' as ket, jumlah_return as masuk, 0 as keluar FROM pengembalian WHERE nama_barang = '$nama' ORDER BY tgl DESC";
    } else {
        $sql = "
            (SELECT tanggal_beli as tgl, 'Restock Barang' as ket, jumlah_beli as masuk, 0 as keluar FROM pembelian WHERE nama_barang = '$nama')
            UNION ALL
            (SELECT tanggal as tgl, 'Penjualan' as ket, 0 as masuk, jumlah as keluar FROM transaksi WHERE nama_barang = '$nama')
            UNION ALL
            (SELECT tanggal_return as tgl, 'Barang Return' as ket, jumlah_return as masuk, 0 as keluar FROM pengembalian WHERE nama_barang = '$nama')
            ORDER BY tgl DESC";
    }

    $query = mysqli_query($conn, $sql);
    ?>

    <style>
        .tbl-res { width: 100%; border-collapse: collapse; font-size: 14px; background-color: #ffffff; color: #2c3e50; }
        .tbl-res th { background: #f4f7f6; color: #2c3e50; padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; }
        .tbl-res td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; color: #2c3e50; }
        .tbl-res td:first-child { text-align: center; }
        .tbl-res td:nth-child(2) { text-align: center; }
        .tbl-res td:nth-child(3) { text-align: center; }
        .tbl-res td:nth-child(4) { text-align: center; }
        .badge-plus { background: #eafaf1; color: #27ae60; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .badge-min { background: #fef4f4; color: #e74c3c; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    </style>

    <table class="tbl-res">
        <thead>
            <tr>
                <th>Waktu (Tgl & Jam)</th>
                <th style="text-align:left;">Aktivitas</th>
                <th>Masuk (+)</th>
                <th>Keluar (-)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    // Format tanggal biar ada Jam-nya
                    $waktu = date('d M Y | H:i', strtotime($row['tgl']));
                    ?>
                    <tr>
                        <td style="color: #666; font-size: 12px;"><?php echo $waktu; ?></td>
                        <td style="text-align:left; font-weight: 500;"><?php echo $row['ket']; ?></td>
                        <td>
                            <?php echo ($row['masuk'] > 0) ? '<span class="badge-plus">+' . $row['masuk'] . '</span>' : '-'; ?>
                        </td>
                        <td>
                            <?php echo ($row['keluar'] > 0) ? '<span class="badge-min">-' . $row['keluar'] . '</span>' : '-'; ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4' style='padding:40px; color:#999;'>Belum ada aktivitas stok untuk barang ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>