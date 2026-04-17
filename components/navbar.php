<nav class="navbar">
    <a href="barang.php" class="nav-brand">
        <span style="font-size: 1.5rem;"></span> Maju Jaya
    </a>

    <div class="nav-menu">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin'): ?>
        <a href="barang.php" class="nav-link <?php echo(basename($_SERVER['PHP_SELF']) == 'barang.php') ? 'active-link' : ''; ?>">Katalog</a>
        <a href="riwayat.php" class="nav-link <?php echo(basename($_SERVER['PHP_SELF']) == 'riwayat.php') ? 'active-link' : ''; ?>">Riwayat</a>
        <a href="riwayat_return.php" class="nav-link <?php echo(basename($_SERVER['PHP_SELF']) == 'riwayat_return.php') ? 'active-link' : ''; ?>">Retur</a>
        <a href="tracking_stok.php" class="nav-link <?php echo(basename($_SERVER['PHP_SELF']) == 'tracking_stok.php') ? 'active-link' : ''; ?>">Tracking Stock</a>
        <?php
endif; ?>
    </div>

    <div class="nav-right">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="user-badge">
                Hi, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (<?php echo ucfirst($_SESSION['role']); ?>)
            </span>
            <a href="actions/logout.php" class="nav-link" style="color: #fda4af;">Logout</a>
        <?php
endif; ?>
        
        <a href="keranjang.php" class="btn-cart">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Keranjang
            <span class="cart-badge">
                <?php echo isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : '0'; ?>
            </span>
        </a>
    </div>
</nav>