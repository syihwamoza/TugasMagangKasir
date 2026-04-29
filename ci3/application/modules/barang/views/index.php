<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Barang - Maju Jaya</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2>Katalog Produk</h2>
            <div style="display: flex; gap: 10px;">
                <form method="GET" style="display: flex; gap: 5px;">
                    <input type="text" name="cari" value="<?= $cari ?>" placeholder="Cari produk..." class="form-control" style="width: 200px;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
                <?php if($this->session->userdata('role') == 'superadmin'): ?>
                    <a href="<?= site_url('barang/tambah') ?>" class="btn btn-success">+ Tambah Barang</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="product-grid">
            <?php foreach($barang as $b): ?>
                <div class="product-card">
                    <?php if(!empty($b['gambar'])): ?>
                        <img src="<?= base_url('../assets/uploads/'.$b['gambar']) ?>" alt="Foto" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="background:#eee; display:flex; align-items:center; justify-content:center; color:#999;">No Image</div>
                    <?php endif; ?>
                    
                    <div class="product-info">
                        <h3 class="product-name"><?= $b['nama_barang'] ?></h3>
                        <p class="product-price">Rp <?= number_format($b['harga'], 0, ',', '.') ?></p>
                        <p class="product-stock <?= ($b['stok_aktif'] <= 5) ? 'stock-low' : '' ?>"> Stok: <?= number_format($b['stok_aktif'], 0, ',', '.') ?> </p>
                        
                        <div style="margin-top: 15px; display: flex; gap: 5px;">
                            <?php if($this->session->userdata('role') == 'superadmin'): ?>
                                <a href="<?= site_url('barang/edit/'.$b['id_barang']) ?>" class="btn btn-edit" style="flex:1; text-align:center;">Edit</a>
                                <a href="javascript:void(0)" onclick="confirmHapus('<?= site_url('barang/hapus/'.$b['id_barang']) ?>')" class="btn btn-danger" style="flex:1; text-align:center;">Hapus</a>
                            <?php else: ?>
                                <form action="<?= site_url('keranjang/tambah') ?>" method="POST" style="width:100%; display:flex; gap:5px;">
                                    <input type="hidden" name="id_barang" value="<?= $b['id_barang'] ?>">
                                    <input type="number" name="jumlah" value="1" min="1" max="<?= $b['stok_aktif'] ?>" class="form-control" style="width:60px;">
                                    <button type="submit" class="btn-beli" style="flex:1;">Beli</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?= $this->pagination->create_links(); ?>
        </div>
    </div>

    <script>
        function confirmHapus(url) {
            Swal.fire({
                title: 'Hapus Barang?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }

        <?php if($this->session->flashdata('sukses')): ?>
        Swal.fire('Berhasil!', '<?= $this->session->flashdata('sukses') ?>', 'success');
        <?php endif; ?>
    </script>
</body>
</html>
