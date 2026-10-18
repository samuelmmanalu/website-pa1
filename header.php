<?php 
session_start();
include 'koneksi/koneksi.php';
if(isset($_SESSION['kd_cs'])){
    $kode_cs = $_SESSION['kd_cs'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Informasi Ulos Modern dan Tradisional</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <style>
        :root {
            --primary: #ff8680;
            --primary-dark: #e6736d;
            --primary-light: #ffd6d4;
            --dark: #333333;
            --light: #ffffff;
            --gray: #f8f9fa;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 95px; /* 2.5cm gap between header and content */
            color: var(--dark);
            background-color: var(--light);
        }

        /* Unified Header */
        .main-header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            box-shadow: 0 4px 20px rgba(230, 115, 109, 0.1);
        }

        /* Top Info Bar */
        .top-bar {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 10px 0;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .top-bar-item {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .top-bar i {
            margin-right: 8px;
            font-size: 14px;
        }

        /* Main Navigation */
        .main-nav {
            background: var(--light);
            padding: 0;
            border-bottom: 1px solid rgba(230, 115, 109, 0.08);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: 70px;
        }

        .nav-brand {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 22px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.25s ease;
        }

        .nav-brand:hover {
            transform: translateY(-2px);
            color: var(--primary);
        }

        .nav-brand i {
            margin-right: 10px;
            font-size: 24px;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-link {
            color: var(--dark);
            font-weight: 500;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link:hover, 
        .nav-link.active {
            color: var(--primary-dark);
        }

        .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            color: var(--dark);
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-btn:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(230, 115, 109, 0.2);
        }

        .caret {
            margin-left: 5px;
            transition: transform 0.3s;
        }

        .caret.rotated {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            margin-top: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 8px 0;
            min-width: 200px;
            z-index: 1000;
            border: 1px solid var(--primary-light);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 10px 20px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .dropdown-item i {
            color: var(--primary);
            width: 18px;
            text-align: center;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--primary-dark);
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .mobile-menu-btn:hover {
            color: var(--primary);
        }

        .content-container {
            padding-top: 15px;
        }

        @media (max-width: 991px) {
            body {
                padding-top: 140px; /* Diubah dari 110px ke 140px untuk jarak 2cm di mobile */
            }
            
            .top-bar-content {
                flex-direction: column;
                gap: 5px;
                text-align: center;
            }
            
            .top-bar-item {
                justify-content: center;
            }
            
            .nav-menu {
                display: none;
                position: fixed;
                top: 140px; /* Diubah sesuai dengan padding-top body */
                left: 0;
                right: 0;
                background: var(--light);
                flex-direction: column;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                padding: 20px;
                gap: 15px;
                z-index: 1000;
                border-top: 1px solid var(--primary-light);
            }
            
            .nav-link {
                padding: 12px 15px;
                display: block;
            }
            
            .nav-link.active:after {
                left: 15px;
                right: 15px;
            }
            
            .dropdown-menu {
                position: static;
                box-shadow: none;
                display: none;
            }
            
            .dropdown-menu.show {
                display: block;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }

        .logo-image {
            height: 50px;
            width: auto;
            margin-right: 15px;
            border-radius: 5px;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 8px rgba(230, 115, 109, 0.2);
        }

        .nav-brand:hover .logo-image {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(230, 115, 109, 0.3);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav-brand span {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary);
        }

        .social-icons {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
            padding-left: 20px;
            border-left: 1px solid rgba(0,0,0,0.1);
        }

        .social-icon {
            color: var(--dark);
            font-size: 16px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--gray);
        }

        .social-icon:hover {
            color: var(--light);
            background-color: var(--primary);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <!-- Unified Header -->
    <header class="main-header">
        <!-- Top Info Bar -->
        <div class="top-bar">
            <div class="top-bar-content">
                <div class="top-bar-item">
                    <a href="https://wa.me/6282272604189" style="color: white; text-decoration: none; transition: all 0.3s ease; display: flex; align-items: center;" onmouseover="this.style.color='#ffd6d4'" onmouseout="this.style.color='white'">
                        <i class="fas fa-phone"></i> +6282272604189
                    </a>
                </div>
                <div class="top-bar-item">
                    <a href="mailto:informasiulos24@gmail.com" style="color: white; text-decoration: none; transition: all 0.3s ease; display: flex; align-items: center;" onmouseover="this.style.color='#ffd6d4'" onmouseout="this.style.color='white'">
                        <i class="fas fa-envelope"></i> informasi-ulos@gmail.com
                    </a>
                </div>
                <div class="top-bar-item">
                    <a href="https://www.google.com/maps/place/Lembaga+Pelatihan+dan+Penerapan+Teknologi/@2.3685582,99.1330971,14.31z/data=!4m6!3m5!1s0x302e010054d43183:0x7776d2b1b309bf1e!8m2!3d2.3693056!4d99.1406076!16s%2Fg%2F11w3cs6zdy?hl=id&entry=ttu&g_ep=EgoyMDI1MDUxMy4xIKXMDSoASAFQAw%3D%3D" target="_blank" style="color: white; text-decoration: none; transition: all 0.3s ease; display: flex; align-items: center;" onmouseover="this.style.color='#ffd6d4'" onmouseout="this.style.color='white'">
                        <i class="fas fa-map-marker-alt"></i> Informasi-Ulos Indonesia
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="main-nav">
            <div class="nav-container">
                <a href="index.php" class="nav-brand">
                    <img src="kiw2.jpg" alt="Logo Batak" class="logo-image">
                    <span>Informasi Ulos</span>
                </a>
                
                <div class="nav-menu" id="mainMenu">
                    <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
                    <a href="modern.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'modern.php' ? 'active' : '' ?>">Modern</a>
                    <a href="tradisional.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tradisional.php' ? 'active' : '' ?>">Tradisional</a>
                    <a href="about.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">Tentang Kami</a>
                    
                    <div class="social-icons">
                        <a href="https://www.facebook.com/groups/1636370553329731/" target="_blank" class="social-icon" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/dameulos/" target="_blank" class="social-icon" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/watch?v=AZQTg5eU_t0" target="_blank" class="social-icon" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="https://www.tiktok.com/@batakkerenofficial/video/7512793761731087672?q=informasi%20ulos&t=1749477375968" target="_blank" class="social-icon" title="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </header>

    <div class="content-container">
        <main class="content-section">
            <!-- Your page content goes here -->
        </main>
    </div>

    <script>
        // Simple dropdown toggle function
        function toggleDropdown(button) {
            const dropdown = button.parentElement;
            const menu = dropdown.querySelector('.dropdown-menu');
            const caret = button.querySelector('.caret');
            
            // Toggle the dropdown menu
            menu.classList.toggle('show');
            
            // Rotate the caret icon
            caret.classList.toggle('rotated');
            
            // Close other open dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu && otherMenu.classList.contains('show')) {
                    otherMenu.classList.remove('show');
                    const otherCaret = otherMenu.parentElement.querySelector('.caret');
                    if (otherCaret) otherCaret.classList.remove('rotated');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.caret').forEach(caret => {
                    caret.classList.remove('rotated');
                });
            }
        });

        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const menu = document.getElementById('mainMenu');
            const icon = this.querySelector('i');
            
            if (menu.style.display === 'flex' || menu.style.display === '') {
                menu.style.display = 'none';
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                menu.style.display = 'flex';
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mainMenu .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) {
                    document.getElementById('mainMenu').style.display = 'none';
                    document.getElementById('mobileMenuBtn').querySelector('i').classList.remove('fa-times');
                    document.getElementById('mobileMenuBtn').querySelector('i').classList.add('fa-bars');
                }
            });
        });
    </script>
</body>
</html>