<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tracking Stok - Maju Jaya</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <style>
        .container-tracking { margin: 30px auto; max-width: 1100px; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background-color: #34495e; color: white; padding: 15px; font-size: 12px; text-transform: uppercase; }
        tbody td { padding: 15px; border-bottom: 1px solid #edf2f7; text-align: center; }
        .klik-detail { color: inherit; text-decoration: none; display: block; width: 100%; height: 100%; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .klik-detail:hover { background-color: #f0f0f0; border-radius: 5px; color: #27ae60; }
        .stok-akhir-col { background: #f8f9fa; font-weight: bold; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
        .page-link { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #34495e; border-radius: 5px; }
    </style>
</head>
<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div class="container-tracking">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin:0; color: #2c3e50;">Tracking Pergerakan Stok</h2>
            <form method="GET" style="display: flex; gap: 8px; align-items:center; flex-wrap:wrap;">
    
                <input type="text" name="cari" value="<?= $cari ?>" placeholder="Cari barang..." style="padding:8px; border-radius:5px; border:1px solid #ddd;">
                
                <input type="date" name="dari" value="<?= $this->input->get('dari') ?>" style="padding:8px; border-radius:5px; border:1px solid #ddd;">
                
                <input type="date" name="sampai" value="<?= $this->input->get('sampai') ?>" style="padding:8px; border-radius:5px; border:1px solid #ddd;">
                
                <button type="submit" style="padding:8px 15px; background:#34495e; color:white; border:none; border-radius:5px; cursor:pointer;">
                    Filter
                </button>
            </form>
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
                <?php foreach($barang as $b): ?>
                <tr>
                    <td style="text-align: left; padding-left: 20px; font-weight: bold; color: #2c3e50;">
                        <a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'all')"><?= $b['nama_barang'] ?></a>
                    </td>
                    <td><?= $b['stok_awal_calc'] ?></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'restock')" style="color:#2980b9;"><?= $b['tambah'] ?></a></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'sell')" style="color:#e67e22;"><?= $b['terjual'] ?></a></td>
                    <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'return')" style="color:#27ae60;"><?= $b['direturn'] ?></a></td>
                    <td class="stok-akhir-col"><?= $b['stok_akhir'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tbody>
    <?php if (!empty($barang)): ?>
        <?php foreach($barang as $b): ?>
        <tr>
            <td style="text-align: left; padding-left: 20px; font-weight: bold; color: #2c3e50;">
                <a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'all')"><?= $b['nama_barang'] ?></a>
            </td>
            <td><?= $b['stok_awal_calc'] ?></td>
            <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'restock')" style="color:#2980b9;"><?= $b['tambah'] ?></a></td>
            <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'sell')" style="color:#e67e22;"><?= $b['terjual'] ?></a></td>
            <td><a href="javascript:void(0)" class="klik-detail" onclick="tampilDetail('<?= $b['nama_barang'] ?>', 'return')" style="color:#27ae60;"><?= $b['direturn'] ?></a></td>
            <td class="stok-akhir-col"><?= $b['stok_akhir'] ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="padding: 50px; color: #999; font-style: italic;">
                Tidak ada aktivitas pergerakan stok.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
        <?= $this->pagination->create_links() ?>
    </div>

    <!-- Modal Detail -->
    <div id="modalDetail" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; backdrop-filter: blur(4px);">
        <div style="background:white; width:700px; margin:5% auto; padding:0; border-radius:15px; overflow:hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.3);">
            <div style="background:#34495e; padding:20px; color:white; display:flex; justify-content:space-between; align-items:center;">
                <h3 id="judulModal" style="margin:0; font-size:18px;">Detail Riwayat</h3>
                <button onclick="tutupModal()" style="border:none; background:none; color:white; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div id="isiDetail" style="max-height:450px; overflow-y:auto; padding:20px;">
                <p style="text-align:center;">Memuat data riwayat...</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function tampilDetail(nama, jenis = 'all') {
        $('#modalDetail').fadeIn(300);
        $('#judulModal').text(" Riwayat Stok: " + nama);
        $.ajax({
            url: '<?= site_url('barang/tracking/get_detail_ajax') ?>',
            method: 'POST',
            data: {nama_barang: nama, jenis: jenis},
            success: function(data) { $('#isiDetail').html(data); }
        });
    }
    function tutupModal() { $('#modalDetail').fadeOut(200); }
    </script>
</body>
</html>
