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

<!-- HERO SECTION -->
<div class="container-fluid page-load-animation" style="background: linear-gradient(135deg, #fff5f4 0%, #f9f3f2 100%); padding: 80px 0; margin-bottom: 50px; position: relative; overflow: hidden;">
    <div style="height: 5px; background: linear-gradient(to right, #ff8680, #e6736d, #ffd6d4); position: absolute; top: 0; left: 0; right: 0;"></div>
    <div class="container">
        <h1 style="font-size: 42px; font-weight: 600; color: #333; text-align: center; font-family: 'Playfair Display', serif; letter-spacing: 0.5px;">
            Koleksi <span style="color: #ff8680;">Ulos Batak</span>
        </h1>
        <p style="font-size: 18px; color: #555; text-align: center; max-width: 800px; margin: 25px auto; line-height: 1.7; font-family: 'Montserrat', sans-serif; font-weight: 300; letter-spacing: 0.2px;">
            Temukan berbagai jenis ulos tradisional dan modern dengan kualitas terbaik. Setiap helai mengandung makna dan filosofi mendalam.
        </p>
    </div>
</div>

<!-- PRODUK SECTION -->
<div class="container page-load-animation" style="font-family: 'Montserrat', 'Helvetica Neue', Helvetica, Arial, sans-serif; margin-bottom: 80px; max-width: 1200px;">

    <div class="row">
        <?php 
        $result = mysqli_query($conn, "SELECT * FROM produk");
        while ($row = mysqli_fetch_assoc($result)) {
            // Kategori styling
            $kategori = $row['kategori'];
            $badgeColor = '';
            if ($kategori == 'Modern') {
                $badgeColor = 'modern-badge';
            } elseif ($kategori == 'Tradisional') {
                $badgeColor = 'traditional-badge';
            } else {
                $badgeColor = 'default-badge';
            }
        ?>
            <div class="col-sm-6 col-md-4" style="margin-bottom: 40px;">
                <div class="product-card" style="position: relative; border: none; box-shadow: 0 3px 35px rgba(255, 134, 128, 0.08); transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border-radius: 12px; overflow: hidden; background: #fff; height: 100%; border: 1px solid #f5f5f5;">

                    <!-- Colorful Category Badge -->
                    <div style="position: absolute; top: 15px; left: 15px; z-index: 10;">
                        <span style="font-size: 10px; font-weight: 600; padding: 5px 14px; background: linear-gradient(to right, #ff8680, #e6736d); color: #fff; border-radius: 30px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 10px rgba(255, 134, 128, 0.25); font-family: 'Montserrat', sans-serif;">
                            <?= $kategori; ?>
                        </span>
                    </div>

                    <!-- Product Image with Elegant Hover & Subtle Overlay -->
                    <div style="width: 100%; height: 300px; overflow: hidden; position: relative;">
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
        ?>
    </div>
</div>

<!-- Final colorful accent stripe -->
<div style="height: 5px; background: linear-gradient(to right, #ffd6d4, #e6736d, #ff8680); margin-top: 60px; margin-bottom: 60px; border-radius: 50px;"></div>

<!-- TESTIMONIAL SECTION DENGAN REDESIGN -->
<div class="container-fluid page-load-animation" style="background: linear-gradient(135deg, #fff5f4 0%, #f9f3f2 100%); padding: 90px 0; position: relative; overflow: hidden; margin-bottom: 60px; box-shadow: 0 5px 25px rgba(230, 115, 109, 0.08);">
    <div style="height: 5px; background: linear-gradient(to right, #ff8680, #e6736d, #ffd6d4); position: absolute; top: 0; left: 0; right: 0;"></div>
    
    <div class="container">
        <h2 style="text-align: center; font-size: 38px; font-weight: 600; color: #333; margin-bottom: 15px; font-family: 'Playfair Display', serif; letter-spacing: 0.8px; line-height: 1.2;">
            Apa Kata <span style="color: #e6736d; position: relative; display: inline-block;">Pelanggan<span style="position: absolute; bottom: 5px; left: 0; width: 100%; height: 8px; background: rgba(255, 214, 212, 0.4); z-index: -1;"></span></span> Kami?
        </h2>
        
        <p style="font-size: 16px; color: #666; text-align: center; max-width: 600px; margin: 0 auto 40px; line-height: 1.8; font-family: 'Montserrat', sans-serif; font-weight: 300; letter-spacing: 0.3px;">
            Testimoni dari pelanggan setia yang telah merasakan kualitas produk ulos kami
        </p>
        
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="display: inline-block; width: 40px; height: 2px; background: linear-gradient(to right, #ff8680, transparent); margin-right: 10px; vertical-align: middle;"></span>
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ff8680; vertical-align: middle;"></span>
            <span style="display: inline-block; width: 40px; height: 2px; background: linear-gradient(to left, #ff8680, transparent); margin-left: 10px; vertical-align: middle;"></span>
        </div>
        
        <div class="row">
            <div class="col-md-4" style="margin-bottom: 30px;">
                <div class="testimonial-card" style="background: #fff; padding: 35px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(255, 134, 128, 0.08); height: 100%; border: 1px solid #f9f1f0; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); position: relative;">
                    <!-- Quote mark -->
                    <div style="position: absolute; top: 20px; right: 25px; font-size: 40px; color: rgba(230, 115, 109, 0.1); font-family: 'Playfair Display', serif; line-height: 1;">"</div>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 25px;">
                        <div style="position: relative; margin-right: 20px;">
                            <img src="pin4.png" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; box-shadow: 0 5px 15px rgba(255, 134, 128, 0.15); border: 3px solid #fff;">
                            <div style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 10px rgba(230, 115, 109, 0.1);">
                                <div style="color: #e6736d; font-size: 12px;">
                                    <i class="glyphicon glyphicon-heart"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; margin: 0; color: #333; font-family: 'Playfair Display', serif; font-size: 18px;">Arfin Melkisodok</h4>
                            <p style="color: #ff8680; margin: 5px 0 0; font-size: 13px; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px; font-weight: 500;">Mahasiswa IT Del</p>
                            <div style="margin-top: 8px;">
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                            </div>
                        </div>
                    </div>
                    <p style="color: #555; line-height: 1.8; font-family: 'Montserrat', sans-serif; font-weight: 300; letter-spacing: 0.2px; font-size: 15px; font-style: italic; position: relative; z-index: 1;">
                        "Kualitas ulosnya sangat bagus dan motifnya autentik. Pelayanannya juga ramah dan profesional."
                    </p>
                    <!-- Bottom quote mark -->
                    <div style="position: absolute; bottom: 20px; left: 25px; font-size: 40px; color: rgba(230, 115, 109, 0.1); font-family: 'Playfair Display', serif; line-height: 1; transform: rotate(180deg);">"</div>
                </div>
            </div>
            
            <div class="col-md-4" style="margin-bottom: 30px;">
                <div class="testimonial-card" style="background: #fff; padding: 35px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(255, 134, 128, 0.08); height: 100%; border: 1px solid #f9f1f0; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); position: relative;">
                    <!-- Quote mark -->
                    <div style="position: absolute; top: 20px; right: 25px; font-size: 40px; color: rgba(230, 115, 109, 0.1); font-family: 'Playfair Display', serif; line-height: 1;">"</div>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 25px;">
                        <div style="position: relative; margin-right: 20px;">
                            <img src="pal5.png" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; box-shadow: 0 5px 15px rgba(255, 134, 128, 0.15); border: 3px solid #fff;">
                            <div style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 10px rgba(230, 115, 109, 0.1);">
                                <div style="color: #e6736d; font-size: 12px;">
                                    <i class="glyphicon glyphicon-heart"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; margin: 0; color: #333; font-family: 'Playfair Display', serif; font-size: 18px;">Palando Parhusip</h4>
                            <p style="color: #ff8680; margin: 5px 0 0; font-size: 13px; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px; font-weight: 500;">Pecinta Budaya</p>
                            <div style="margin-top: 8px;">
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                            </div>
                        </div>
                    </div>
                    <p style="color: #555; line-height: 1.8; font-family: 'Montserrat', sans-serif; font-weight: 300; letter-spacing: 0.2px; font-size: 15px; font-style: italic; position: relative; z-index: 1;">
                        "Sebagai orang Batak perantauan, saya sangat menghargai usaha melestarikan budaya melalui ulos ini."
                    </p>
                    <!-- Bottom quote mark -->
                    <div style="position: absolute; bottom: 20px; left: 25px; font-size: 40px; color: rgba(230, 115, 109, 0.1); font-family: 'Playfair Display', serif; line-height: 1; transform: rotate(180deg);">"</div>
                </div>
            </div>
            
            <div class="col-md-4" style="margin-bottom: 30px;">
                <div class="testimonial-card" style="background: #fff; padding: 35px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(255, 134, 128, 0.08); height: 100%; border: 1px solid #f9f1f0; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); position: relative;">
                    <!-- Quote mark -->
                    <div style="position: absolute; top: 20px; right: 25px; font-size: 40px; color: rgba(230, 115, 109, 0.1); font-family: 'Playfair Display', serif; line-height: 1;">"</div>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 25px;">
                        <div style="position: relative; margin-right: 20px;">
                            <img src="wil6.png" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; box-shadow: 0 5px 15px rgba(255, 134, 128, 0.15); border: 3px solid #fff;">
                            <div style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 10px rgba(230, 115, 109, 0.1);">
                                <div style="color: #e6736d; font-size: 12px;">
                                    <i class="glyphicon glyphicon-heart"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; margin: 0; color: #333; font-family: 'Playfair Display', serif; font-size: 18px;">Wilson Zebua</h4>
                            <p style="color: #ff8680; margin: 5px 0 0; font-size: 13px; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px; font-weight: 500;">Desainer Pangkas</p>
                            <div style="margin-top: 8px;">
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star" style="color: #FFD700; font-size: 12px;"></i>
                                <i class="glyphicon glyphicon-star-empty" style="color: #FFD700; font-size: 12px;"></i>
                            </div>
                        </div>
                    </div>
                    <p style="color: #555; line-height: 1.8; font-family: 'Montserrat', sans-serif; font-weight: 300; letter-spacing: 0.2px; font-size: 15px; font-style: italic; position: relative; z-index: 1;">
                        "Ulosnya sangat cocok untuk bahan fashion modern tapi tetap mempertahankan nilai tradisionalnya."
                    </p>
                    <!-- Bottom quote mark -->
                    <div style="position: absolute; bottom: 20px; left: 25px; font-size: 40px; color: rgba(230, 115, 109, 0.1); font-family: 'Playfair Display', serif; line-height: 1; transform: rotate(180deg);">"</div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="height: 5px; background: linear-gradient(to right, #ffd6d4, #e6736d, #ff8680); position: absolute; bottom: 0; left: 0; right: 0;"></div>
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
    
    .product-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 35px rgba(255, 134, 128, 0.15);
        border-color: #ffeae9;
    }
    
    .product-card:hover img {
        transform: scale(1.07);
    }
    
    /* Row hover effect */
    .row:hover .product-card:not(:hover) {
        opacity: 0.85;
        filter: grayscale(15%);
    }
</style>

<?php 
include 'footer.php';
?>