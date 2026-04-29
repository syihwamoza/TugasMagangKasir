<style>
    .profile-dropdown {
        position: relative;
        display: inline-block;
        margin-left: 20px;
    }
    .profile-trigger {
        cursor: pointer;
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 15px;
        border-radius: 30px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .profile-trigger:hover {
        background: rgba(255, 255, 255, 0.25);
    }
    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background-color: #f8f9fa !important; /* Ganti ke abu-abu sangat muda agar beda */
        min-width: 140px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        border-radius: 8px;
        z-index: 999999; /* Sangat tinggi */
        margin-top: 10px;
        border: 1px solid #ccc;
        padding: 5px 0;
    }
    .dropdown-content a {
        display: block !important;
        color: #000000 !important;
        padding: 12px 20px !important;
        text-decoration: none !important;
        font-size: 15px !important;
        font-weight: bold !important;
        text-align: center !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .dropdown-content a:hover {
        background-color: #e74c3c !important;
        color: #ffffff !important;
    }
    .dropdown-content.show {
        display: block !important;
    }
</style>

<nav class="navbar">
    <div class="nav-container">
        <a href="<?= site_url('barang') ?>" class="nav-brand">MAJU JAYA</a>
        <div class="nav-menu">
            <a href="<?= site_url('barang') ?>" class="nav-item">Katalog</a>
            
            <?php if($this->session->userdata('role') != 'superadmin'): ?>
                <a href="<?= site_url('keranjang') ?>" class="nav-item">
                    Keranjang (<?= count($this->session->userdata('keranjang') ?? []) ?>)
                </a>
            <?php endif; ?>

            <?php if($this->session->userdata('role') == 'superadmin'): ?>
                <a href="<?= site_url('riwayat') ?>" class="nav-item">Riwayat</a>
                <a href="<?= site_url('riwayat/riwayat_return') ?>" class="nav-item">Riwayat Return</a>
            <?php endif; ?>
            
            <?php if($this->session->userdata('role') == 'superadmin'): ?>
                <a href="<?= site_url('barang/tracking') ?>" class="nav-item">Tracking Stok</a>
            <?php endif; ?>

            <div class="profile-dropdown">
                <div class="profile-trigger" onclick="toggleDropdown(event)">
                    Hi, <?= $this->session->userdata('username') ?> (<?= $this->session->userdata('role') ?>)
                    <span style="font-size: 10px; margin-left: 8px;">▼</span>
                </div>
                <div id="myDropdown" class="dropdown-content">
                    <a href="<?= site_url('auth/logout') ?>" style="color: #000000 !important; display: block !important; font-weight: bold !important;">Logout</a>
                </div>
            </div>

            <script>
                function toggleDropdown(e) {
                    e.stopPropagation();
                    document.getElementById("myDropdown").classList.toggle("show");
                }
                
                window.addEventListener('click', function(e) {
                    const dropdown = document.getElementById("myDropdown");
                    if (!document.querySelector('.profile-dropdown').contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            </script>
        </div>
    </div>
</nav>
