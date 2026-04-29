<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi - Maju Jaya</title>
    <!-- Assets are usually in root directory, adjust path if needed -->
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .table-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        thead th { background-color: #34495e; color: white; padding: 12px; text-transform: uppercase; font-size: 13px; }
        tbody td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
        
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 25px; }
        .page-link { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #34495e; border-radius: 5px; cursor: pointer; }
        .page-link.active { background-color: #27ae60; color: white; border-color: #27ae60; }
        
        .filter-box-transaksi { background: transparent; padding: 0; border-radius: 0; box-shadow: none; margin-bottom: 20px; }
        .filter-row { display: flex; gap: 15px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .input-modern-filter { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }
        .btn-filter-modern { background: #34495e; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-reset-modern { background: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; }
        
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
    </style>
</head>
<body>

    <?php $this->load->view('templates/navbar'); ?>

    <div class="container">
        <div class="table-container">
            <h2 style="margin:0 0 20px 0; color: #2c3e50;">Riwayat Transaksi Penjualan</h2>

            <form method="GET" action="<?= site_url('riwayat') ?>" class="filter-box-transaksi" style="margin-bottom: 20px;">
                <div class="filter-row">
                    <div class="filter-group">
                        <label style="font-size: 12px; font-weight: bold;">Cari Barang</label>
                        <input type="text" name="cari" class="input-modern-filter" value="<?= $cari ?>" placeholder="Nama barang...">
                    </div>
                    <div class="filter-group">
                        <label style="font-size: 12px; font-weight: bold;">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" class="input-modern-filter" value="<?= $tgl_mulai ?>">
                    </div>
                    <div class="filter-group">
                        <label style="font-size: 12px; font-weight: bold;">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="input-modern-filter" value="<?= $tgl_selesai ?>">
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <button type="submit" class="btn-filter-modern">Filter</button>
                        <a href="<?= site_url('riwayat') ?>" class="btn-reset-modern">Reset</a>
                    </div>
                </div>
            </form>

            <form id="formReturn" action="<?= site_url('riwayat/return_barang') ?>" method="POST">
                <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 12px 18px; border-radius: 10px; border: 1px solid #eee;">
                    <div style="color: #555; font-size: 14px;">
                        Total data ditemukan: <b><?= $total_rows ?></b> transaksi
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
                            <th>ID</th>
                            <th>NAMA BARANG</th>
                            <th>JUMLAH</th>
                            <th>TOTAL BAYAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($transaksi)): ?>
                            <?php foreach($transaksi as $t): ?>
                            <tr>
                                <td align="center">
                                    <input type="checkbox" name="id_return[]" value="<?= $t['id_transaksi'] ?>" style="width: 18px; height: 18px;">
                                </td>
                                <td align="center"><?= date('d/m/Y H:i', strtotime($t['tanggal'])) ?></td>
                                <td align="center"><b>#<?= $t['id_transaksi'] ?></b></td>
                                <td style="text-align: left; font-weight: 500;"><?= $t['nama_barang'] ?></td>
                                <td align="center"><?= $t['jumlah'] ?></td>
                                <td align="center" style="color: #2c3e50;"><b>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></b></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan='6' align='center' style='padding: 40px; color: #999;'>Tidak ada data transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>

            <?= $this->pagination->create_links(); ?>
        </div>
    </div>

    <!-- Modal Return -->
    <div id="returnModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Konfirmasi Retur Barang</div>
            <div class="item-list" id="itemListContainer"></div>
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
        function getSelectedItems() {
            const checkboxes = document.querySelectorAll('input[name="id_return[]"]:checked');
            const items = [];
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                items.push({
                    id: checkbox.value,
                    nama: row.cells[3].textContent.trim(),
                    jumlah: row.cells[4].textContent.trim()
                });
            });
            return items;
        }

        function openReturnModal() {
            const items = getSelectedItems();
            if (items.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Belum ada pilihan', text: 'Pilih barang yang akan di-retur terlebih dahulu!' });
                return;
            }
            const container = document.getElementById('itemListContainer');
            container.innerHTML = '';
            items.forEach((item, index) => {
                container.innerHTML += `<div class="item-row"><div class="item-detail"><div class="item-name">${index + 1}. ${item.nama}</div><div class="item-qty">Jumlah: ${item.jumlah}</div></div></div>`;
            });
            document.getElementById('returnModal').classList.add('active');
        }

        function closeReturnModal() { document.getElementById('returnModal').classList.remove('active'); }

        function submitReturn() {
            const reason = document.getElementById('reasonInput').value.trim();
            if (reason === '') {
                Swal.fire({ icon: 'info', title: 'Alasan diperlukan', text: 'Tuliskan alasan retur terlebih dahulu!' });
                return;
            }
            const form = document.getElementById('formReturn');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'alasan_return';
            hiddenInput.value = reason;
            form.appendChild(hiddenInput);
            form.submit();
        }

        <?php if($this->session->flashdata('return_sukses')): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $this->session->flashdata('return_sukses') ?>' });
        <?php endif; ?>
    </script>
</body>
</html>
