<?php 
include 'header.php';
?>

<style>
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
    .row .col-sm-6:nth-child(4) .product-card { animation-delay: 0.4s; }
    .row .col-sm-6:nth-child(5) .product-card { animation-delay: 0.5s; }
    .row .col-sm-6:nth-child(6) .product-card { animation-delay: 0.6s; }
    
    /* Custom scrollbar for a more polished look */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f9f9f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #ff8680;
    }
    
    /* Colorful page background */
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #fff5f4 100%);
    }
</style>

<div class="container page-load-animation" style="font-family: 'Montserrat', 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 1200px;">

    <!-- Colorful Accent Stripe -->
    <div style="height: 5px; background: linear-gradient(to right, #ff8680, #e6736d, #ffd6d4); margin-bottom: 40px; border-radius: 50px;"></div>

    <!-- Sophisticated Page Header with color -->
    <div class="page-header" style="margin: 40px 0 40px; position: relative; text-align: center;">
        <h2 style="font-weight: 500; color: #333333; margin: 0; padding-bottom: 15px; display: inline-block; letter-spacing: 1.2px; position: relative; font-family: 'Playfair Display', serif;">
            <span style="background: transparent; padding: 0 25px; position: relative; z-index: 2;">Koleksi Ulos Modern</span>
            <span style="content: ''; position: absolute; bottom: 15px; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, #ff8680, transparent); z-index: 1;"></span>
        </h2>
        <p style="color: #555555; margin-top: 18px; font-size: 16px; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.7; font-weight: 300; letter-spacing: 0.2px; font-family: 'Montserrat', sans-serif;">Eksplorasi interpretasi kontemporer dari Ulos, menggabungkan desain tradisional dengan sentuhan modern</p>
    </div>

    <div class="row" style="margin-bottom: 60px;">
        <div class="col-md-8 col-md-offset-2">
            <form method="GET" action="">
                <div class="input-group" style="box-shadow: 0 5px 25px rgba(255, 134, 128, 0.1); border-radius: 30px; overflow: hidden; border: 1px solid #ffeae9; transition: all 0.3s ease;">
                    <input type="text" class="form-control" name="search" placeholder="Cari produk ulos modern..." 
                           style="border: none; padding: 20px 25px; font-size: 14px; height: auto; background: #fcfcfc; transition: all 0.3s ease; font-family: 'Montserrat', sans-serif; letter-spacing: 0.2px;" 
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <span class="input-group-btn" style="width: auto;">
                        <button class="btn btn-elegant-search" type="submit" style="border-radius: 0 30px 30px 0; padding: 20px 30px; border: none; background: #f8f8f8; transition: all 0.3s ease; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px; font-weight: 500;">
                            <i class="glyphicon glyphicon-search" style="margin-right: 8px;"></i> Cari
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php 

        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
        $whereClause = "WHERE kategori = 'Modern'";
        
        if ($search) {
            $whereClause .= " AND (nama LIKE '%$search%' OR deskripsi LIKE '%$search%')";
        }
        
        $query = "SELECT * FROM produk $whereClause ORDER BY kode_produk DESC";
        $result = mysqli_query($conn, $query);
        
        $produkCount = mysqli_num_rows($result);
        
        if ($produkCount > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $kategori = $row['kategori'];
                $badgeColor = 'modern-badge'; // Custom class for modern badge
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
            // No products found
            echo '<div class="col-md-12 text-center animate-fade" style="padding: 80px 0;">';
            if ($search) {
                echo "<img src='https://cdn-icons-png.flaticon.com/512/7486/7486744.png' style='width: 120px; opacity: 0.7; margin-bottom: 20px;'>";
                echo "<h4 style='color: #555; font-weight: 600; margin-bottom: 15px;'>Produk Tidak Ditemukan</h4>";
                echo "<p style='font-size: 15px; color: #777; max-width: 500px; margin: 0 auto 25px;'>Maaf, kami tidak menemukan produk ulos modern dengan kata kunci '<strong>" . htmlspecialchars($search) . "</strong>'.</p>";
                echo "<a href='modern.php' class='btn-elegant' style='padding: 12px 30px;'>Tampilkan Semua Produk</a>";
            } else {
                echo "<img src='https://cdn-icons-png.flaticon.com/512/4076/4076478.png' style='width: 120px; opacity: 0.7; margin-bottom: 20px;'>";
                echo "<h4 style='color: #555; font-weight: 600; margin-bottom: 15px;'>Belum Ada Produk</h4>";
                echo "<p style='font-size: 15px; color: #777; max-width: 500px; margin: 0 auto 25px;'>Saat ini belum ada produk ulos modern yang tersedia.</p>";
            }
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- Elegant Unified Styles with Colors -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap');
    
    body {
        background-color: #f8f9fa;
        color: #333333;
        font-family: 'Montserrat', sans-serif;
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Playfair Display', serif;
    }
    
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
        font-family: 'Montserrat', sans-serif;
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
    
    .btn-elegant-search {
        background: transparent;
        color: #777;
        font-weight: 400;
        transition: all 0.3s ease;
    }
    
    .btn-elegant-search:hover {
        color: #ff8680;
        background: #f3f3f3;
    }
    
    .product-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 35px rgba(255, 134, 128, 0.15);
        border-color: #ffeae9;
    }
    
    .product-card:hover img {
        transform: scale(1.07);
    }
    
    .input-group:hover {
        box-shadow: 0 8px 30px rgba(255, 134, 128, 0.12);
    }
    
    .input-group:focus-within {
        box-shadow: 0 8px 30px rgba(255, 134, 128, 0.15);
        border-color: #ffeae9;
    }
    
    @media (max-width: 768px) {
        .page-header h2 {
            font-size: 24px;
        }
        
        .btn-elegant {
            width: 100%;
            justify-content: center;
        }
    }
    
    /* Row hover effect */
    .row:hover .product-card:not(:hover) {
        opacity: 0.85;
        filter: grayscale(15%);
    }
    
    /* Colorful accent for no results */
    .col-md-12.text-center .glyphicon {
        background: linear-gradient(135deg, #ff8680, #e6736d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        opacity: 0.6;
    }
</style>

<!-- Final colorful accent stripe -->
<div style="height: 5px; background: linear-gradient(to right, #ffd6d4, #e6736d, #ff8680); margin-top: 60px; border-radius: 50px;"></div>

<style>
    /* Efek animasi pada scroll */
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

<?php 
include 'footer.php';
?>