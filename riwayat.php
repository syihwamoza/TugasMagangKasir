<?php 
session_start();
include 'config/koneksi.php'; 

$batas = 20; 
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman_awal = ($halaman - 1) * $batas;

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

$where = " WHERE 1=1 ";
if(!empty($cari)) { $where .= " AND nama_barang LIKE '%$cari%' "; }
if(!empty($tgl_mulai) && !empty($tgl_selesai)) {
    $where .= " AND tanggal BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59' ";
}

$ambildata = mysqli_query($conn, "SELECT * FROM transaksi $where");
$jumlah_data = mysqli_num_rows($ambildata);
$total_halaman = ceil($jumlah_data / $batas);

$query_transaksi = mysqli_query($conn, "SELECT * FROM transaksi $where ORDER BY tanggal DESC LIMIT $halaman_awal, $batas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .table-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        thead th { background-color: #34495e; color: white; padding: 12px; text-transform: uppercase; font-size: 13px; }
        tbody td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
        
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 25px; }
        .page-link { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #34495e; border-radius: 5px; }
        .page-link.active { background-color: #27ae60; color: white; border-color: #27ae60; }
        
        .filter-box-transaksi { background: transparent; padding: 0; border-radius: 0; box-shadow: none; margin-bottom: 20px; }
        .filter-row { display: flex; gap: 15px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .input-modern-filter { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }
        .btn-filter-modern { background: #34495e; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-reset-modern { background: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; }
        
        /* Checkbox style */
        input[type="checkbox"] { cursor: pointer; }

        /* Modal Style */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal.active { display: flex; }
        .modal-content { background-color: white; margin: auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .modal-header { font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #2c3e50; border-bottom: 3px solid #e74c3c; padding-bottom: 10px; }
        .item-list { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .item-row { display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid #ddd; }
        .item-row:last-child { border-bottom: none; }
        .item-detail { flex: 1; }
        .item-name { font-weight: bold; color: #2c3e50; }
        .item-qty { color: #666; font-size: 14px; }
        .reason-input-group { margin-bottom: 20px; }
        .reason-input-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #2c3e50; }
        .reason-input-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial; font-size: 14px; resize: vertical; min-height: 100px; }
        .modal-buttons { display: flex; gap: 10px; justify-content: flex-end; }
        .btn-modal { padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-cancel { background: #95a5a6; color: white; }
        .btn-submit { background: #27ae60; color: white; }
        .btn-cancel:hover { background: #7f8c8d; }
        .btn-submit:hover { background: #229954; }
    </style>
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container">
        <div class="table-container">
            <h2 style="margin:0 0 20px 0; color: #2c3e50;">Riwayat Transaksi Penjualan</h2>

            <form method="GET" class="filter-box-transaksi" style="margin-bottom: 20px;">
                <div class="filter-row">
                    <div class="filter-group">
                        <label style="font-size: 12px; font-weight: bold;">Cari Barang</label>
                        <input type="text" name="cari" class="input-modern-filter" value="<?php echo $cari; ?>" placeholder="Nama barang...">
                    </div>
                    <div class="filter-group">
                        <label style="font-size: 12px; font-weight: bold;">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" class="input-modern-filter" value="<?php echo $tgl_mulai; ?>">
                    </div>
                    <div class="filter-group">
                        <label style="font-size: 12px; font-weight: bold;">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="input-modern-filter" value="<?php echo $tgl_selesai; ?>">
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <button type="submit" class="btn-filter-modern">Filter</button>
                        <a href="riwayat.php" class="btn-reset-modern">Reset</a>
                    </div>
                </div>
            </form>

            <form id="formReturn" action="actions/proses_return_banyak.php" method="POST">
            <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 12px 18px; border-radius: 10px; border: 1px solid #eee;">
                <div style="color: #555; font-size: 14px;">
                    Menampilkan <b><?php echo mysqli_num_rows($query_transaksi); ?></b> data dari total <b><?php echo $jumlah_data; ?></b> transaksi
                </div>
                <button type="button" onclick="openReturnModal()" style="background: #f39c12; color: white; padding: 10px 22px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                    Retur Barang Terpilih
                </button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="50">PILIH</th>
                        <th>WAKTU</th>
                        <th>ID TRANSAKSI</th>
                        <th>NAMA BARANG</th>
                        <th>JUMLAH</th>
                        <th>TOTAL BAYAR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_date = "";
                    $no_harian = 0;

                    if(mysqli_num_rows($query_transaksi) > 0){
                        while($t = mysqli_fetch_array($query_transaksi)){
                            // Logika ID Transaksi Unik per Hari
                            $date_only = date('Y-m-d', strtotime($t['tanggal']));
                            if($date_only !== $current_date){
                                $current_date = $date_only;
                                $no_harian = 1; 
                            } else {
                                $no_harian++; 
                            }
                        ?>
                        <tr>
                            <td align="center">
                                <input type="checkbox" name="id_return[]" value="<?php echo $t['id_transaksi']; ?>" style="width: 18px; height: 18px;">
                            </td>
                            <td align="center"><?php echo date('d/m/Y H:i', strtotime($t['tanggal'])); ?></td>
                            <td align="center">
                                <b><?php echo date('ndy', strtotime($t['tanggal'])) . "-" . str_pad($no_harian, 3, "0", STR_PAD_LEFT); ?></b>
                            </td>
                            <td style="text-align: left; font-weight: 500;"><?php echo $t['nama_barang']; ?></td>
                            <td align="center"><?php echo $t['jumlah']; ?></td>
                            <td align="center" style="color: #2c3e50;"><b>Rp <?php echo number_format($t['total_harga'], 0, ',', '.'); ?></b></td>
                        </tr>
                        <?php } 
                    } else {
                        echo "<tr><td colspan='6' align='center' style='padding: 40px; color: #999;'>Tidak ada data transaksi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>

        <div class="pagination">
            <?php 
            $qs = "&cari=$cari&tgl_mulai=$tgl_mulai&tgl_selesai=$tgl_selesai";
            if($halaman > 1): ?>
                <a class="page-link" href="?page=<?php echo ($halaman - 1) . $qs; ?>">Sebelumnya</a>
            <?php endif; ?>

            <?php for($x=1; $x<=$total_halaman; $x++): ?>
                <a class="page-link <?php echo ($x == $halaman) ? 'active' : ''; ?>" href="?page=<?php echo $x . $qs; ?>"><?php echo $x; ?></a>
            <?php endfor; ?>

            <?php if($halaman < $total_halaman): ?>
                <a class="page-link" href="?page=<?php echo ($halaman + 1) . $qs; ?>">Berikutnya</a>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Return -->
    <div id="returnModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Konfirmasi Retur Barang</div>
            
            <div class="item-list" id="itemListContainer">
                <!-- Items akan ditampilkan di sini -->
            </div>

            <div class="reason-input-group">
                <label for="reasonInput">Alasan Retur:</label>
                <textarea id="reasonInput" name="alasan_return" placeholder="Tuliskan alasan mengapa barang dikembalikan..."></textarea>
            </div>

            <div class="modal-buttons">
                <button type="button" class="btn-modal btn-cancel" onclick="closeReturnModal()">Batal</button>
                <button type="button" class="btn-modal btn-submit" onclick="submitReturn()">Konfirmasi Return</button>
            </div>
        </div>
    </div>

    <script>
        // Ambil data barang dari tabel
        function getSelectedItems() {
            const checkboxes = document.querySelectorAll('input[name="id_return[]"]:checked');
            const items = [];
            
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const waktu = row.cells[1].textContent.trim();
                const id_transaksi = row.cells[2].textContent.trim();
                const nama_barang = row.cells[3].textContent.trim();
                const jumlah = row.cells[4].textContent.trim();
                
                items.push({
                    id: checkbox.value,
                    waktu: waktu,
                    id_transaksi: id_transaksi,
                    nama_barang: nama_barang,
                    jumlah: jumlah
                });
            });
            
            return items;
        }

        function openReturnModal() {
            const items = getSelectedItems();
            
            if (items.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada pilihan',
                    text: 'Pilih barang yang akan di-retur terlebih dahulu!',
                    confirmButtonColor: '#34495e'
                });
                return;
            }
            
            // Tampilkan item di modal
            const itemListContainer = document.getElementById('itemListContainer');
            itemListContainer.innerHTML = '';
            
            items.forEach((item, index) => {
                const itemRow = document.createElement('div');
                itemRow.className = 'item-row';
                itemRow.innerHTML = `
                    <div class="item-detail">
                        <div class="item-name">${index + 1}. ${item.nama_barang}</div>
                        <div class="item-qty">ID: ${item.id_transaksi} | Jumlah: ${item.jumlah} | Tanggal: ${item.waktu}</div>
                    </div>
                `;
                itemListContainer.appendChild(itemRow);
            });
            
            // Tampilkan modal
            document.getElementById('returnModal').classList.add('active');
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.remove('active');
        }

        function submitReturn() {
            const reasonInput = document.getElementById('reasonInput').value.trim();
            
            if (reasonInput === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'Alasan diperlukan',
                    text: 'Tuliskan alasan retur terlebih dahulu!',
                    confirmButtonColor: '#27ae60'
                });
                return;
            }
            
            // Tambahkan hidden input untuk alasan
            const form = document.getElementById('formReturn');
            
            // Hapus hidden input lama jika ada
            const oldReasonInput = form.querySelector('input[name="alasan_return"]');
            if (oldReasonInput) {
                oldReasonInput.remove();
            }
            
            // Tambahkan hidden input baru
            const hiddenReasonInput = document.createElement('input');
            hiddenReasonInput.type = 'hidden';
            hiddenReasonInput.name = 'alasan_return';
            hiddenReasonInput.value = reasonInput;
            form.appendChild(hiddenReasonInput);
            
            // Submit form
            form.submit();
        }

        // Tutup modal jika klik di luar modal
        document.getElementById('returnModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReturnModal();
            }
        });
    </script>