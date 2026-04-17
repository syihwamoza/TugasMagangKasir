<?php 
session_start();
include 'config/koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: barang.php');
    exit;
}
$id = mysqli_real_escape_string($conn, $_GET['id']);
$sukses = false; // Variabel untuk trigger SweetAlert

// --- LOGIKA PROSES SIMPAN ---
if (isset($_POST['update'])) {
    $stok_baru = $_POST['stok'];
    $harga_baru = $_POST['harga'];
    
    // 1. Ambil data lama buat bandingin stok (untuk laporan TAMBAH)
    $query_lama = mysqli_query($conn, "SELECT b.*, (b.stok_awal + COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = b.nama_barang), 0) - COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = b.nama_barang), 0) + COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = b.nama_barang), 0)) AS stok_aktif FROM barang b WHERE id_barang = '$id'");
    
    if ($query_lama && mysqli_num_rows($query_lama) > 0) {
        $data_lama = mysqli_fetch_assoc($query_lama);
        $stok_lama = $data_lama['stok_aktif'];
        $nama_barang = $data_lama['nama_barang'];

        // 2. CEK: Apakah stoknya ditambah?
        if ($stok_baru > $stok_lama) {
            $selisih = $stok_baru - $stok_lama;
            // Kalau nambah, otomatis catat sebagai "TAMBAH" di Tracking
            mysqli_query($conn, "INSERT INTO pembelian (nama_barang, jumlah_beli, tanggal_beli) 
                                VALUES ('$nama_barang', '$selisih', NOW())");
        }
    }

    $updateGambarSQL = "";
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
            $updateGambarSQL = ", gambar = '$gambarBaru'";
        }
    }

    // 3. Update data barang
    $update = mysqli_query($conn, "UPDATE barang SET harga = '$harga_baru' $updateGambarSQL WHERE id_barang = '$id'");

    if ($update) {
        $sukses = true; // Set jadi true agar pop-up muncul
    }
}

// Ambil data terbaru untuk ditampilin di form
$query = mysqli_query($conn, "SELECT b.*, (b.stok_awal + COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = b.nama_barang), 0) - COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = b.nama_barang), 0) + COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = b.nama_barang), 0)) AS stok_aktif FROM barang b WHERE id_barang = '$id'");
$d = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <div style="max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: var(--primary); margin-top: 0; margin-bottom: 25px;">Edit Data Barang</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            
            <?php if(!empty($d['gambar']) && file_exists('assets/uploads/'.$d['gambar'])): ?>
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="assets/uploads/<?php echo $d['gambar']; ?>" alt="Foto" style="max-height: 150px; border-radius: 8px;">
            </div>
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Ganti Foto (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px dashed #aaa; background: #fafafa;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Nama Barang</label>
                <input type="text" value="<?php echo $d['nama_barang']; ?>" disabled style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #eee; cursor: not-allowed;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Harga (Rp)</label>
                <input type="number" name="harga" value="<?php echo $d['harga']; ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Stok Saat Ini</label>
                <input type="number" name="stok" value="<?php echo $d['stok_aktif']; ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
                <small style="color: #7f8c8d; display:block; margin-top:5px;">*Ubah angka untuk menambah stok (akan tercatat di Tracking).</small>
            </div>
            
            <button type="submit" name="update" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;">Simpan Perubahan</button>
            <a href="barang.php" style="display: block; text-align: center; margin-top: 15px; color: var(--danger); text-decoration: none; font-size: 14px; font-weight:600;">Kembali ke Daftar</a>
        </form>
    </div>

    <?php if ($sukses): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data Barang Berhasil Diupdate!',
            icon: 'success',
            confirmButtonColor: '#2980b9',
            confirmButtonText: 'Oke'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'barang.php';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>