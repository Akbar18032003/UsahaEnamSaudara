<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Usaha 6 Saudara - Sistem Penjualan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 50px;
            text-align: center;
            max-width: 800px;
            width: 100%;
            position: relative;
            overflow: hidden;
            animation: slideIn 1s ease-out;
        }

        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: pulse 2s infinite;
            padding: 10px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
            margin-bottom: 50px;
            font-weight: 300;
        }

        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .menu-item {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 15px;
            padding: 30px 20px;
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(240, 147, 251, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .menu-item:nth-child(1) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.3);
        }

        .menu-item:nth-child(2) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            box-shadow: 0 10px 25px rgba(67, 233, 123, 0.3);
        }

        .menu-item:nth-child(3) {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            box-shadow: 0 10px 25px rgba(250, 112, 154, 0.3);
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-item:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .menu-item:active {
            transform: translateY(-5px) scale(1.02);
        }

        .menu-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            display: block;
        }

        .menu-title {
            font-size: 1.3em;
            margin-bottom: 8px;
        }

        .menu-desc {
            font-size: 0.9em;
            opacity: 0.9;
            font-weight: 400;
        }

        .footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(127, 140, 141, 0.2);
            color: #7f8c8d;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }

            h1 {
                font-size: 2em;
            }

            .menu-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .menu-item {
                padding: 25px 15px;
            }
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            width: 80px;
            height: 80px;
            background: #667eea;
            border-radius: 50%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            width: 60px;
            height: 60px;
            background: #764ba2;
            border-radius: 10px;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            width: 100px;
            height: 100px;
            background: #f093fb;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="logo">
            <img src="gambar/u6s.jpg" alt="U6S Logo">
        </div>
        <h1>Selamat Datang</h1>
        <p class="subtitle">Sistem Penjualan PT Usaha 6 Saudara</p>
        
        <div class="menu-container">
            <a href="user/login.php" class="menu-item">
                <span class="menu-icon">👤</span>
                <div class="menu-title">User</div>
                <div class="menu-desc">Akses menu pelanggan dan pemesanan</div>
            </a>
            
            <a href="admin/login-admin.php" class="menu-item">
                <span class="menu-icon">⚙️</span>
                <div class="menu-title">Admin</div>
                <div class="menu-desc">Kelola sistem dan data penjualan</div>
            </a>
            
            <a href="pimpinan/login-pimpinan.php" class="menu-item">
                <span class="menu-icon">📊</span>
                <div class="menu-title">Pimpinan</div>
                <div class="menu-desc">Dashboard laporan dan analisis</div>
            </a>
        </div>
        
        <div class="footer">
            <p>&copy; 2025 PT Usaha 6 Saudara. Semua hak cipta dilindungi.</p>
        </div>
    </div>

    <script>
        // Animasi tambahan untuk interaksi
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.05)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Efek paralaks untuk floating shapes
        window.addEventListener('mousemove', (e) => {
            const shapes = document.querySelectorAll('.shape');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.5;
                shape.style.transform += ` translate(${x * speed}px, ${y * speed}px)`;
            });
        });
    </script>
</body>
</html>