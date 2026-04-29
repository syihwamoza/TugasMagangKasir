<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maju Jaya - Portal Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== CSS diambil 100% dari login.php lama, tidak diubah ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Ambient floating circles background */
        .circle {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(#27ae60, transparent);
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            z-index: 1;
            animation: float 8s ease-in-out infinite both;
        }
        .circle:nth-child(1) { top: -100px; left: -100px; }
        .circle:nth-child(2) { bottom: -100px; right: -100px; animation-delay: -4s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.1); }
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2), 0 0 0 1px rgba(255,255,255,0.1);
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header .logo-icon {
            font-size: 40px;
            color: #27ae60;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 6px rgba(39, 174, 96, 0.3));
        }

        .login-header h2 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            color: #2c3e50;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: #fff;
            border-color: #27ae60;
            box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #219150;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }

        .alert-error {
            background: #feeceb;
            color: #e74c3c;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 25px;
            border-left: 4px solid #e74c3c;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        
        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #95a5a6;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <!-- Ornamental Background Effects -->
    <div class="circle"></div>
    <div class="circle"></div>

    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo-icon">💠</div>
            <h2>Maju Jaya</h2>
            <p>Portal Manajemen Toko</p>
        </div>

        <?php
        // CI3 pakai flashdata, bukan $_SESSION langsung
        // Flashdata otomatis dihapus setelah sekali tampil
        $error = $this->session->flashdata('error_login');
        if ($error): ?>
            <div class="alert-error">
                ⚠ <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- 
            PERUBAHAN dari login.php lama:
            - action lama: "actions/login_aksi.php"
            - action baru: "auth/proses" (CI3 routing ke controller Auth, method proses)
        -->
        <form action="<?= site_url('auth/proses'); ?>" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan Username" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi" required>
            </div>
            
            <button type="submit" id="btn-masuk" class="btn-login">Masuk</button>
        </form>

        <div class="footer-text">
            Sistem Informasi terintegrasi &copy; <?= date('Y'); ?>
        </div>
    </div>

</body>
</html>
