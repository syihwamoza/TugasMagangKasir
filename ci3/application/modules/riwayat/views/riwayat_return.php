<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Return - Maju Jaya</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>
<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div class="container">
        <div class="table-container">
            <h2 style="margin-bottom: 25px; color: #2c3e50;">Riwayat Pengembalian Barang (Return)</h2>

            <!-- Filter Panel -->
            <div class="filter-box-transaksi">
                <form action="<?= site_url('riwayat/riwayat_return') ?>" method="GET" class="filter-row">
                    <div class="filter-group">
                        <label>Cari Barang</label>
                        <input type="text" name="cari" class="input-modern-filter" value="<?= $cari ?>" placeholder="Nama barang...">
                    </div>
                    <div class="filter-group">
                        <label>Mulai Tanggal</label>
                        <input type="date" name="tgl_mulai" class="input-modern-filter" value="<?= $tgl_mulai ?>">
                    </div>
                    <div class="filter-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="input-modern-filter" value="<?= $tgl_selesai ?>">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-filter-modern">Filter</button>
                        <a href="<?= site_url('riwayat/riwayat_return') ?>" class="btn-reset-modern">Reset</a>
                    </div>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>ID Transaksi</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($returns)): ?>
                        <?php foreach($returns as $r): ?>
                            <tr>
                                <td style="font-size: 12px; color: #666;"><?= date('d/m/Y H:i', strtotime($r['tanggal_return'])) ?></td>
                                <td><b>#<?= !empty($r['id_transaksi']) ? $r['id_transaksi'] : $r['id_return'] ?></b></td>
                                <td align="left" style="padding-left: 20px;"><?= $r['nama_barang'] ?></td>
                                <td><span class="badge badge-danger"><?= $r['jumlah_return'] ?></span></td>
                                <td align="left"><?= $r['alasan'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" align="center" style="padding: 30px; color: #999;">Tidak ada data pengembalian.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?= $this->pagination->create_links() ?>
        </div>
    </div>
</body>
</html>
