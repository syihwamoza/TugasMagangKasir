<?php 
session_start();
include 'config/koneksi.php'; 

// Variabel untuk trigger SweetAlert
$sukses = false;

if (isset($_POST['simpan_baru'])) {
    ensureBarangStokAwalColumn($conn);

    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar = null;

    // Handle Upload Gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $namaFile = $_FILES['gambar']['name'];
        $tmpName = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $valid_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $valid_ext)) {
            $gambarBaru = uniqid() . '.' . $ext;
            if (!is_dir('assets/uploads')) mkdir('assets/uploads', 0777, true);
            move_uploaded_file($tmpName, 'assets/uploads/' . $gambarBaru);
            $gambar = $gambarBaru;
        }
    }

    $query = mysqli_query($conn, "INSERT INTO barang (nama_barang, harga, stok, stok_awal, gambar) VALUES ('$nama', '$harga', '$stok', '$stok', " . ($gambar ? "'$gambar'" : "NULL") . ")");

    if ($query) {
        $sukses = true; // Set jadi true kalau berhasil
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <div style="max-width: 450px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color:var(--primary); margin-bottom: 25px;">Katalog Baru</h2>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Foto Produk (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px dashed #aaa; background: #fafafa;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Nama Barang</label>
                <input type="text" name="nama_barang" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Harga (Rp)</label>
                <input type="number" name="harga" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Stok Awal</label>
                <input type="number" name="stok" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <button type="submit" name="simpan_baru" style="width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s;">Simpan ke Etalase</button>
        </form>
    </div>

    <?php if ($sukses): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Barang Baru Berhasil Didaftarkan!',
            icon: 'success',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#2c3e50'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'barang.php'; // Pindah halaman setelah klik Oke
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>