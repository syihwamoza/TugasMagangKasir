<?php

session_start();
include 'config/koneksi.php';


// --- LOGIKA PENCARIAN ---
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = !empty($cari) ? " WHERE nama_barang LIKE '%$cari%' " : "";

// --- LOGIKA PAGINATION ---
$jumlahDataPerHalaman = 12; // Adjusted for grid layout
$queryCek = mysqli_query($conn, "SELECT b.*, (b.stok_awal + COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = b.nama_barang), 0) - COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = b.nama_barang), 0) + COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = b.nama_barang), 0)) AS stok_aktif FROM barang b $where");

$jumlahData = mysqli_num_rows($queryCek);
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
$halamanAktif = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maju Jaya - Katalog</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container">
        
        <!-- HEADER ROW -->
        <div class="page-header">
            <div>
                <h2 class="page-title">Katalog Produk</h2>
                <div style="color: var(--text-muted); font-size: 0.875rem;">Menampilkan <?php echo $jumlahData; ?> produk tersedia</div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <form method="GET" class="search-form">
                    <input type="text" name="cari" class="form-control" value="<?php echo $cari; ?>" placeholder="Cari barang..." style="min-width: 250px;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if (!empty($cari)): ?>
                    <a href="barang.php" class="btn" style="background:#e5e7eb; color:#374151;">Reset</a>
                    <?php
endif; ?>
                </form>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin'): ?>
                <a href="tambah_barang.php" class="btn btn-success">+ Tambah</a>
                <?php
endif; ?>
            </div>
        </div>

        <!-- PRODUCT GRID -->
        <?php if ($jumlahData > 0): ?>
            <div class="product-grid">
                <?php
    $query = mysqli_query($conn, "SELECT b.*, (b.stok_awal + COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = b.nama_barang), 0) - COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = b.nama_barang), 0) + COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = b.nama_barang), 0)) AS stok_aktif FROM barang b $where ORDER BY nama_barang ASC LIMIT $awalData, $jumlahDataPerHalaman");

    while ($d = mysqli_fetch_array($query)) {
        $isHabis = $d['stok_aktif'] <= 0;
?>
                <div class="product-card">
                    <?php if ($isHabis): ?>
                        <div class="product-badge out-of-stock">HABIS</div>
                    <?php
        else: ?>
                        <div class="product-badge">TERSEDIA</div>
                    <?php
        endif; ?>
                    
                    <?php if (!empty($d['gambar']) && file_exists('assets/uploads/' . $d['gambar'])): ?>
                        <div style="width: 100%; height: 180px; margin-bottom: 1rem; border-radius: 8px; overflow: hidden; background: #f3f4f6;">
                            <img src="assets/uploads/<?php echo $d['gambar']; ?>" alt="<?php echo $d['nama_barang']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php
        else: ?>
                        <div style="font-size: 4rem; text-align: center; color: #cbd5e1; margin-bottom: 1rem; padding: 2rem 0; background: #f8fafc; border-radius: 8px;">📦</div>
                    <?php
        endif; ?>
                    
                    <h3 class="product-name"><?php echo $d['nama_barang']; ?></h3>
                    <div class="product-price">Rp <?php echo number_format($d['harga'], 0, ',', '.'); ?></div>
                    <div class="product-stock">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Tersisa <?php echo $d['stok_aktif']; ?> unit
                    </div>
                    
                    <div class="product-actions">
                        <?php if (!$isHabis): ?>
                            <form action="actions/keranjang_aksi.php" method="POST" class="cart-controls">
                                <input type="hidden" name="id_barang" value="<?php echo $d['id_barang']; ?>">
                                <input type="number" name="jumlah" class="form-control" value="1" min="1" max="<?php echo $d['stok_aktif']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Beli</button>
                            </form>
                        <?php
        else: ?>
                            <button class="btn" style="background:var(--light); color:var(--text-muted); cursor:not-allowed;" disabled>Barang Kosong</button>
                        <?php
        endif; ?>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin'): ?>
                        <div class="admin-actions">
                            <a href="edit.php?id=<?php echo $d['id_barang']; ?>" style="color:var(--primary); font-weight:600; text-decoration:none;">Edit</a>
                            <a href="javascript:void(0)" onclick="konfirmasiHapus('<?php echo $d['id_barang']; ?>', '<?php echo addslashes($d['nama_barang']); ?>')" style="color:var(--danger); font-weight:600; text-decoration:none;">Hapus</a>
                        </div>
                        <?php
        endif; ?>
                    </div>
                </div>
                <?php
    }?>
            </div>
        <?php
else: ?>
            <div class="empty-state">
                <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;">🔍</div>
                <h3>Tidak ada barang yang ditemukan</h3>
                <p>Coba gunakan kata kunci pencarian yang lain.</p>
            </div>
        <?php
endif; ?>

        <!-- PAGINATION -->
        <?php if ($jumlahHalaman > 1): ?>
        <div class="pagination">
            <?php if ($halamanAktif > 1): ?>
                <a href="?halaman=<?php echo $halamanAktif - 1; ?>&cari=<?php echo $cari; ?>" class="page-link">&laquo;</a>
            <?php
    endif; ?>

            <?php for ($i = 1; $i <= $jumlahHalaman; $i++): ?>
                <a href="?halaman=<?php echo $i; ?>&cari=<?php echo $cari; ?>" 
                   class="page-link <?php echo($i == $halamanAktif) ? 'active-page' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php
    endfor; ?>

            <?php if ($halamanAktif < $jumlahHalaman): ?>
                <a href="?halaman=<?php echo $halamanAktif + 1; ?>&cari=<?php echo $cari; ?>" class="page-link">&raquo;</a>
            <?php
    endif; ?>
        </div>
        <?php
endif; ?>

    </div>

    <script>
    function konfirmasiHapus(id, nama) {
        Swal.fire({
            title: 'Hapus barang?',
            text: "Barang '" + nama + "' akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            border_radius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'actions/hapus_barang.php?id=' + id;
            }
        })
    }
    </script>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_berhasil'): ?>
    <script>
        Swal.fire({
            title: 'Terhapus!',
            text: 'Data barang telah berhasil dihapus.',
            icon: 'success',
            confirmButtonColor: '#10b981'
        });
    </script>
    <?php
endif; ?>

</body>
</html>