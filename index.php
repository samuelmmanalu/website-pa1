<?php 
include 'header.php';
?>

<!-- HERO SECTION WITH DARKER OVERLAY -->
<div class="container-fluid hero-section" style="
    margin: 0; 
    padding: 0; 
    position: relative;
    height: 100vh;
    min-height: 650px;
    max-height: 900px;
    overflow: hidden;">
    
    <!-- Original background image with darker overlay -->
    <div class="image" style="
        margin-top: -21px;
        position: relative;
        height: 100%;">
        <img src="yohodo.jpg" style="
            width: 100%;
            height: 100%;
            object-fit: cover;" 
            class="fade-in">
            
        <!-- Darker overlay -->
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);">
        </div>
    </div>
    
    <!-- Hero content -->
    <div class="hero-content" style="
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        padding: 20px;
        z-index: 2;">
        
        <div style="max-width: 800px; padding: 20px;">
            <h1 class="fade-in-delay-1" style="
                font-size: 48px; 
                font-weight: 700; 
                margin-bottom: 20px;
                text-shadow: 0 2px 4px rgba(0,0,0,0.5);
                line-height: 1.2;">
                <span style="color: #ff8d87;">Ulos Batak</span> - Warisan Budaya Yang Tak Ternilai Harganya
            </h1>
            
            <p class="fade-in-delay-2" style="
                font-size: 20px; 
                line-height: 1.6;
                margin-bottom: 30px;
                text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                Setiap helai benang mengandung cerita, setiap motif menyimpan makna. 
                Temukan keindahan dan filosofi kain ulos yang telah menjadi identitas budaya Batak selama berabad-abad.
            </p>
            
            <a href="#produk" class="btn-custom fade-in-delay-3 smooth-scroll" style="
                padding: 12px 30px;
                font-size: 16px;
                background-color: #ff8d87;
                border: 2px solid #ff8d87;
                cursor: pointer;">
                Jelajahi Koleksi Kami
            </a>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="scroll-indicator" style="
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        animation: bounce 2s infinite;
        cursor: pointer;">
        <svg width="30" height="50" viewBox="0 0 30 50" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="1" y="1" width="28" height="48" rx="14" stroke="white" stroke-opacity="0.5" stroke-width="2"/>
            <circle cx="15" cy="15" r="5" fill="white" fill-opacity="0.8">
                <animate attributeName="cy" from="15" to="35" dur="2s" repeatCount="indefinite"/>
            </circle>
        </svg>
    </div>
</div>

<!-- [REST OF YOUR CONTENT REMAINS EXACTLY THE SAME AS YOUR ORIGINAL CODE] -->
<br><br>

<!-- PRODUK TERBARU -->
<div class="container page-load-animation" style="font-family: 'Montserrat', 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 1200px;" id="produk">

    <!-- Colorful Accent Stripe -->
    <div style="height: 5px; background: linear-gradient(to right, #ff8680, #e6736d, #ffd6d4); margin-bottom: 40px; border-radius: 50px;"></div>

    <!-- Sophisticated Page Header with color -->
    <div class="page-header" style="margin: 40px 0 40px; position: relative; text-align: center;">
        <h2 style="font-weight: 500; color: #333333; margin: 0; padding-bottom: 15px; display: inline-block; letter-spacing: 1.2px; position: relative; font-family: 'Playfair Display', serif;">
            <span style="background: transparent; padding: 0 25px; position: relative; z-index: 2;">Produk Kami</span>
            <span style="content: ''; position: absolute; bottom: 15px; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, #ff8680, transparent); z-index: 1;"></span>
        </h2>
        <p style="color: #555555; margin-top: 18px; font-size: 16px; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.7; font-weight: 300; letter-spacing: 0.2px; font-family: 'Montserrat', sans-serif;">Koleksi kain ulos berkualitas tinggi dengan beragam motif tradisional dan modern</p>
    </div>

    <div class="row">
        <?php 
        $result = mysqli_query($conn, "SELECT * FROM produk LIMIT 3");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $kategori = $row['kategori'];
                
                if ($kategori == 'Modern') {
                    $badgeColor = 'modern-badge';
                } elseif ($kategori == 'Tradisional') {
                    $badgeColor = 'traditional-badge';
                } else {
                    $badgeColor = 'default-badge';
                }
        ?>
            <div class="col-sm-6 col-md-4 animate-fade-up" style="padding: 15px; margin-bottom: 20px;">
                <div class="product-card" style="position: relative; border: none; box-shadow: 0 3px 35px rgba(255, 134, 128, 0.08); transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border-radius: 12px; overflow: hidden; background: #fff; height: 100%; border: 1px solid #f5f5f5;">

                    <!-- Colorful Category Badge -->
                    <div style="position: absolute; top: 15px; left: 15px; z-index: 10;">
                        <span style="font-size: 10px; font-weight: 600; padding: 5px 14px; background: linear-gradient(to right, #ff8680, #e6736d); color: #fff; border-radius: 30px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 10px rgba(255, 134, 128, 0.25); font-family: 'Montserrat', sans-serif;">
                            <?= $kategori; ?>
                        </span>
                    </div>

                    <!-- Product Image with Elegant Hover & Subtle Overlay -->
                    <div style="width: 100%; height: 250px; overflow: hidden; position: relative;">
                        <img src="image/produk/<?= $row['image']; ?>" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.9s cubic-bezier(0.23, 1, 0.32, 1);">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(0,0,0,0) 50%, rgba(255, 134, 128, 0.05) 100%);"></div>
                    </div>
                    
                    <!-- Product Info with Refined Typography & Colors -->
                    <div style="padding: 28px;">
                        <h4 style="font-weight: 500; color: #333333; margin-bottom: 14px; font-size: 17px; line-height: 1.5; letter-spacing: 0.3px; font-family: 'Playfair Display', serif;">
                            <?= $row['nama']; ?>
                        </h4>
                        <p style="font-size: 14px; color: #666; margin: 15px 0; font-weight: 400; font-family: 'Montserrat', sans-serif; line-height: 1.6; max-height: 68px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                            <?php 
                            // Ambil 150 karakter pertama dari deskripsi
                            $short_desc = substr($row['deskripsi'], 0, 150);
                            // Potong di akhir kalimat atau spasi terakhir jika melebihi batas
                            $lastSpace = strrpos($short_desc, ' ');
                            $short_desc = substr($short_desc, 0, $lastSpace);
                            echo $short_desc . '...'; 
                            ?>
                        </p>

                        <!-- Sophisticated Detail Button with Color -->
                        <div style="border-top: 1px solid #ffeae9; padding-top: 20px; margin-top: 5px;">
                            <a href="detail_produk.php?produk=<?= $row['kode_produk']; ?>" class="btn-elegant" style="font-family: 'Montserrat', sans-serif; font-weight: 500; letter-spacing: 0.5px;">
                                <span>Lihat Detail</span>
                                <i class="glyphicon glyphicon-chevron-right" style="margin-left: 8px; font-size: 12px; transition: all 0.3s ease;"></i>
                            </a> 
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="col-md-12 text-center" style="padding: 80px 0;">';
            echo "<img src='https://cdn-icons-png.flaticon.com/512/4076/4076478.png' style='width: 120px; opacity: 0.7; margin-bottom: 20px;'>";
            echo "<h4 style='color: #555; font-weight: 600; margin-bottom: 15px;'>Belum Ada Produk</h4>";
            echo "<p style='font-size: 15px; color: #777; max-width: 500px; margin: 0 auto 25px;'>Saat ini belum ada produk yang tersedia.</p>";
            echo '</div>';
        }
        ?>
    </div>
    
    <div style="display: flex; justify-content: center; margin: 40px 0 60px;">
        <a href="produk.php" class="btn-elegant animate-fade-up" style="font-size: 15px; padding: 12px 30px; font-family: 'Montserrat', sans-serif; font-weight: 500; letter-spacing: 0.5px; border-bottom: 1px solid #ff8680;">
            <span>Lihat Selengkapnya</span>
            <i class="glyphicon glyphicon-chevron-right" style="margin-left: 8px; font-size: 12px; transition: all 0.3s ease;"></i>
        </a>
    </div>

    <!-- Final colorful accent stripe -->
    <div style="height: 5px; background: linear-gradient(to right, #ffd6d4, #e6736d, #ff8680); margin-top: 20px; margin-bottom: 60px; border-radius: 50px;"></div>
</div>

<!-- Add elegant styling for products -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap');
    
    /* Elegant page load animation */
    .page-load-animation {
        animation: gentleFadeIn 1.2s ease-out both;
    }
    @keyframes gentleFadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Refined card animations */
    .product-card {
        animation: cardAppear 0.9s cubic-bezier(0.23, 1, 0.32, 1) both;
        transform-origin: center bottom;
    }
    @keyframes cardAppear {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    /* Staggered animations with elegant timing */
    .row .col-sm-6:nth-child(1) .product-card { animation-delay: 0.1s; }
    .row .col-sm-6:nth-child(2) .product-card { animation-delay: 0.2s; }
    .row .col-sm-6:nth-child(3) .product-card { animation-delay: 0.3s; }
    
    .product-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 35px rgba(255, 134, 128, 0.15);
        border-color: #ffeae9;
    }
    
    .product-card:hover img {
        transform: scale(1.07);
    }
    
    /* Elegant button styling */
    .btn-elegant {
        display: inline-flex;
        align-items: center;
        background: transparent;
        color: #ff8680;
        font-weight: 500;
        padding: 8px 0;
        font-size: 13px;
        letter-spacing: 0.5px;
        border-radius: 0;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none;
        text-decoration: none;
        position: relative;
    }
    
    .btn-elegant:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background: #ff8680;
        transition: width 0.3s ease;
    }
    
    .btn-elegant:hover {
        color: #e6736d;
        text-decoration: none;
    }
    
    .btn-elegant:hover:after {
        width: 100%;
    }
    
    .btn-elegant:hover i {
        transform: translateX(4px);
    }
    
    /* Row hover effect */
    .row:hover .product-card:not(:hover) {
        opacity: 0.85;
        filter: grayscale(15%);
    }
</style>

<!-- Efek animasi pada scroll -->
<style>
    .animate-fade-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }
    
    .animate-fade-right {
        opacity: 0;
        transform: translateX(-30px);
        transition: all 0.8s ease-out;
    }
    
    .animate-fade-left {
        opacity: 0;
        transform: translateX(30px);
        transition: all 0.8s ease-out;
    }
    
    .animate-fade {
        opacity: 0;
        transition: all 0.8s ease-out;
    }
    
    .animate-delay-1 {
        transition-delay: 0.1s;
    }
    
    .animate-delay-2 {
        transition-delay: 0.3s;
    }
    
    .animate-delay-3 {
        transition-delay: 0.5s;
    }
    
    .animate-delay-4 {
        transition-delay: 0.7s;
    }
    
    .animate-delay-5 {
        transition-delay: 0.9s;
    }
    
    .animated {
        opacity: 1;
        transform: translate(0, 0);
    }
</style>

<script>
// Fungsi untuk menambahkan kelas 'animated' ke elemen-elemen yang terlihat
function checkIfInView() {
    const elements = document.querySelectorAll('.animate-fade-up, .animate-fade-right, .animate-fade-left, .animate-fade');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementBottom = element.getBoundingClientRect().bottom;
        const isVisible = elementTop < window.innerHeight - 50 && elementBottom >= 0;
        
        if (isVisible) {
            element.classList.add('animated');
        }
    });
}

// Initialize animation on load
document.addEventListener('DOMContentLoaded', function() {
    // Trigger initial check
    setTimeout(checkIfInView, 100);
    
    // Check when scrolling
    window.addEventListener('scroll', checkIfInView);
    window.addEventListener('resize', checkIfInView);
});
</script>

<!-- ABOUT ULOS SECTION -->
<div class="container-fluid" style="background-color: #f9f3f2; padding: 80px 0; margin: 60px 0;">
    <div class="container" style="max-width: 1200px;">
        <div class="row">
            <div class="col-md-6">
                <h2 style="font-size: 42px; font-weight: 700; color: #333; margin-bottom: 30px; line-height: 1.3;">
                    Filosofi Mendalam <br><span style="color: #ff8d87;">Dalam Setiap Tenunan</span>
                </h2>
                <p style="font-size: 20px; line-height: 1.8; color: #555;">
                    Setiap motif ulos mengandung makna spiritual dan filosofi hidup masyarakat Batak. Dari kelahiran hingga kematian, ulos menjadi bagian tak terpisahkan dari siklus kehidupan.
                </p>
            </div>
            <div class="col-md-6">
                <img src="watepak.jpg" alt="Ulos Batak" style="width: 100%; border-radius: 8px; box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</div>

<!-- ENHANCED CULTURE PRESERVATION SECTION -->
<div class="container-fluid" style="
    background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1518895949257-7621c3c786d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 180px 0;
    text-align: center;
    margin: 80px 0;
    position: relative;">
    
    <div style="
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(255,141,135,0.1) 0%, rgba(255,141,135,0.05) 100%);
        z-index: 1;">
    </div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <h2 style="
            font-size: 52px; 
            color: #fff; 
            font-weight: 700; 
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            letter-spacing: 1px;">
            PELESTARIAN BUDAYA BATAK
        </h2>
        
        <div style="
            width: 100px;
            height: 4px;
            background: #ff8d87;
            margin: 0 auto 30px;
            border-radius: 2px;">
        </div>
        
        <p style="
            font-size: 22px; 
            color: rgba(255,255,255,0.9); 
            max-width: 800px; 
            margin: 0 auto; 
            line-height: 1.7;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
            font-weight: 300;">
            Kami berkomitmen untuk melestarikan dan memperkenalkan kekayaan budaya Batak melalui kain ulos kepada generasi muda. Dengan setiap tenunan, kami menjaga warisan leluhur tetap hidup di era modern.
        </p>
        
        <div style="margin-top: 40px;">
            <div style="
                display: inline-block;
                border: 2px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                padding: 15px;
                animation: pulse 2s infinite;">
                <i class="fa fa-angle-down" style="
                    color: #fff;
                    font-size: 28px;
                    opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</div>

<!-- ULOS USAGE SECTION -->
<div class="container" style="padding: 80px 0; max-width: 1200px;">
    <h2 class="animate-fade-up" style="
        font-size: 42px; 
        font-weight: 700; 
        color: #333; 
        text-align: center; 
        margin-bottom: 60px;
        position: relative;
        display: inline-block;
        left: 50%;
        transform: translateX(-50%);">
        <span style="border-bottom: 4px solid #ff8d87; padding-bottom: 10px;">
            Ragam <span style="color: #ff8d87;">Kegunaan Ulos</span>
        </span>
    </h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-4 animate-fade-left" style="margin-bottom: 40px;">
    <a href="kegunaan_upacara.php" style="display:block; text-decoration:none; color:inherit;">
        <div style="
            background: #fff; 
            padding: 40px 30px; 
            border-radius: 8px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            height: 100%;
            transition: all 0.3s ease;
            border-top: 4px solid #ff8d87;">
            <div style="
                width: 200px;
                height: 200px;
                margin: 0 auto 25px;
                overflow: hidden;
                border-radius: 50%;">
                <img src="slebew.jpg" alt="Upacara Adat" style="
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;">
            </div>
            <h3 style="
                font-size: 28px; 
                color: #ff8d87; 
                margin-bottom: 20px; 
                text-align: center;">Upacara Adat</h3>
            <p style="
                font-size: 18px; 
                line-height: 1.8; 
                color: #555; 
                text-align: center;">
                Ulos menjadi bagian penting dalam berbagai upacara adat Batak seperti pernikahan, kelahiran, dan kematian.
            </p>
        </div>
    </a>
</div>
        
        <div class="col-md-8 col-lg-4 animate-fade-up" style="margin-bottom: 40px;">
    <a href="kegunaan_busana.php" style="display:block; text-decoration:none; color:inherit;">
        <div style="
            background: #fff; 
            padding: 40px 30px; 
            border-radius: 8px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            height: 100%;
            transition: all 0.3s ease;
            border-top: 4px solid #ff8d87;">
            <div style="
                width: 200px;
                height: 200px;
                margin: 0 auto 25px;
                overflow: hidden;
                border-radius: 50%;">
                <img src="slebew2.jpg" alt="Busana Modern" style="
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;">
            </div>
            <h3 style="
                font-size: 28px; 
                color: #ff8d87; 
                margin-bottom: 20px; 
                text-align: center;">Busana Modern</h3>
            <p style="
                font-size: 18px; 
                line-height: 1.8; 
                color: #555; 
                text-align: center;">
                Kini ulos banyak diadaptasi menjadi busana modern yang elegan tanpa meninggalkan nilai tradisionalnya.
            </p>
        </div>
    </a>
</div>
        
        <div class="col-md-8 col-lg-4 animate-fade-right" style="margin-bottom: 40px;">
    <a href="kegunaan_dekorasi.php" style="display:block; text-decoration:none; color:inherit;">
        <div style="
            background: #fff; 
            padding: 40px 30px; 
            border-radius: 8px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            height: 100%;
            transition: all 0.3s ease;
            border-top: 4px solid #ff8d87;">
            <div style="
                width: 200px;
                height: 200px;
                margin: 0 auto 25px;
                overflow: hidden;
                border-radius: 50%;">
                <img src="slebew3.jpg" alt="Dekorasi" style="
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;">
            </div>
            <h3 style="
                font-size: 28px; 
                color: #ff8d87; 
                margin-bottom: 20px; 
                text-align: center;">Dekorasi</h3>
            <p style="
                font-size: 18px; 
                line-height: 1.8; 
                color: #555; 
                text-align: center;">
                Ulos juga digunakan sebagai elemen dekorasi yang mempercantik ruangan dengan nuansa budaya.
            </p>
        </div>
    </a>
</div>
    </div>
</div>

<!-- STYLE CUSTOM TOMBOL -->
<style>
    .btn-custom {
        display: inline-block;
        background-color: #ff8d87;
        color: #fff;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 14px;
        text-transform: uppercase;
        border-radius: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
    }
    
    .btn-view-more {
        display: inline-block;
        background-color: transparent;
        color: #ff8d87;
        font-weight: 600;
        padding: 12px 30px;
        font-size: 16px;
        border: 2px solid #ff8d87;
        border-radius: 30px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-view-more:hover {
        background-color: #ff8d87;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(255, 141, 135, 0.3);
        text-decoration: none;
    }

    .btn-custom:hover {
        background-color: #ff5e57;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
        text-decoration: none;
    }

    .thumbnail:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Animasi Fade In */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .fade-in {
        animation: fadeIn 1s ease forwards;
        opacity: 0;
    }
    
    .fade-in-delay-1 {
        animation: fadeIn 1s ease 0.5s forwards;
        opacity: 0;
    }
    
    .fade-in-delay-2 {
        animation: fadeIn 1s ease 1s forwards;
        opacity: 0;
    }
    
    .fade-in-delay-3 {
        animation: fadeIn 1s ease 1.5s forwards;
        opacity: 0;
    }
    
    .fade-in-delay-4 {
        animation: fadeIn 1s ease 2s forwards;
        opacity: 0;
    }
    
    .fade-in-delay-5 {
        animation: fadeIn 1s ease 2.5s forwards;
        opacity: 0;
    }
    
    .fade-in-delay-6 {
        animation: fadeIn 1s ease 3s forwards;
        opacity: 0;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0) translateX(-50%);
        }
        40% {
            transform: translateY(-20px) translateX(-50%);
        }
        60% {
            transform: translateY(-10px) translateX(-50%);
        }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 36px !important;
        }
        
        .hero-content p {
            font-size: 18px !important;
        }
        
        .btn-custom, .btn-view-more {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 576px) {
        h1 {
            font-size: 28px !important;
        }
        
        .hero-content p {
            font-size: 16px !important;
        }
    }
</style>

<!-- Smooth scroll script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for the button
        document.querySelector('.smooth-scroll').addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
        
        // Smooth scroll for the scroll indicator
        document.querySelector('.scroll-indicator').addEventListener('click', function() {
            window.scrollTo({
                top: window.innerHeight,
                behavior: 'smooth'
            });
        });
    });
</script>

<?php 
include 'footer.php';
?>