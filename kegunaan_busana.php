<?php include 'header.php'; ?>
<div class="container fadein-panel" style="max-width:900px; padding:60px 0 40px 0; min-height:60vh;">
    <div class="card-accent-top"></div>
    <h1 style="font-family:'Playfair Display',serif; color:#ff8680; font-weight:700; margin-bottom:30px; text-align:center;">Kegunaan Ulos sebagai Busana Modern</h1>
    <div class="fadein-card info-card">
        <img src="slebew2.jpg" alt="Busana Modern" class="fadein-img info-img">
        <p style="font-family:'Montserrat',sans-serif; font-size:18px; color:#444; line-height:1.7; margin-bottom:18px; text-align:justify;">Dalam dunia busana modern, ulos tidak lagi terbatas pada fungsi tradisionalnya sebagai kain sakral dalam upacara adat Batak, melainkan telah berevolusi menjadi bahan tekstil yang bernilai tinggi dalam industri fashion kontemporer. Perancang busana dari dalam dan luar negeri mulai mengangkat ulos sebagai elemen utama dalam karya-karya mereka, karena coraknya yang unik, warnanya yang tegas, serta teknik tenun tangan yang mencerminkan kualitas dan keotentikan tinggi. Ulos kini banyak dimodifikasi menjadi berbagai jenis pakaian modern seperti dress, blazer, jas, tunik, rok, celana, outer, dan bahkan kebaya modern yang menampilkan kombinasi antara nuansa tradisional dan gaya masa kini. Selain busana, ulos juga diaplikasikan dalam produk-produk fashion lainnya seperti tas, dompet, sepatu, syal, ikat pinggang, dan bahkan masker wajah, yang menjadikannya semakin relevan dan fungsional dalam kehidupan sehari-hari. Penggunaan ulos dalam fashion modern juga menjadi sarana ekspresi identitas budaya, terutama bagi generasi muda Batak yang ingin tampil modis tanpa melepaskan akar tradisinya. Banyak desainer terkenal Indonesia seperti Anne Avantie, Samuel Wattimena, dan desainer lokal Sumatera Utara mengangkat ulos ke panggung nasional dan internasional, termasuk dalam ajang fashion week, sebagai upaya melestarikan sekaligus mempromosikan kain tradisional ini kepada dunia. Ulos juga sering digunakan dalam busana kerja, busana formal, hingga pakaian kasual, menunjukkan fleksibilitas dan daya adaptasinya dalam berbagai konteks sosial dan estetika. Bahkan dalam perayaan Hari Batik Nasional atau peringatan budaya di lingkungan formal, ulos telah menjadi pilihan utama selain batik dan tenun lain, menegaskan eksistensinya dalam dunia fashion Tanah Air. Dengan kombinasi antara teknik pewarnaan alami, nilai filosofi tinggi dari setiap motifnya, serta kekayaan sejarah yang melekat, ulos dalam busana modern tidak hanya tampil sebagai produk mode, melainkan juga sebagai simbol warisan budaya yang terus hidup dan berkembang. Oleh karena itu, pemanfaatan ulos dalam fashion modern adalah bentuk konkret dari pelestarian budaya yang dikemas secara kreatif, inovatif, dan berdaya saing tinggi di era global.</p>
        <ul style="font-family:'Montserrat',sans-serif; font-size:17px; color:#555; padding-left:20px;"></ul>
    </div>
    <div class="card-accent-bottom"></div>
</div>

<div class="kembali-wrap">
    <button class="btn-kembali" onclick="window.history.back()">
        &larr; Kembali
    </button>
</div>

<style>
.kembali-wrap {
    width: 100%;
    display: flex;
    justify-content: center;
    margin: 36px 0 24px 0;
}
.btn-kembali {
    background: #fff;
    color: #ff8680;
    border: 2px solid #ff8680;
    border-radius: 24px;
    padding: 11px 34px;
    font-size: 1.07rem;
    font-family: inherit;
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(255,134,128,0.06);
    cursor: pointer;
    transition: background 0.18s, color 0.18s, border 0.18s, box-shadow 0.18s, transform 0.14s;
    letter-spacing: 0.5px;
    outline: none;
}
.btn-kembali:hover, .btn-kembali:focus {
    background: linear-gradient(90deg, #ff8680 0%, #e6736d 100%);
    color: #fff;
    border: 2px solid #ff8680;
    box-shadow: 0 4px 18px rgba(255,134,128,0.13);
    transform: translateY(-2px) scale(1.03);
}
.card-accent-top, .card-accent-bottom {
    width: 100%;
    height: 7px;
    background: linear-gradient(90deg, #ffd6d4 0%, #ff8680 100%);
    border-radius: 8px;
    margin: 0 auto 24px auto;
}
.card-accent-top { margin-bottom: 24px; }
.card-accent-bottom { margin-top: 32px; margin-bottom: 0; }
.info-card {
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 8px 40px rgba(255,134,128,0.10);
    padding: 44px 34px 34px 34px;
    margin-bottom: 0;
    position: relative;
    overflow: hidden;
    transition: box-shadow 0.3s, transform 0.3s;
}
.info-card:hover {
    box-shadow: 0 16px 56px rgba(255,134,128,0.16);
    transform: translateY(-2px) scale(1.012);
}
.info-img {
    display: block;
    margin: 0 auto 32px auto;
    border-radius: 18px;
    max-width: 340px;
    width: 100%;
    box-shadow: 0 2px 16px #ffd6d4;
    border: 2px solid #ffd6d4;
    transition: box-shadow 0.3s, border 0.3s;
}
@media (max-width: 700px) {
    .info-card { padding: 18px 6px 16px 6px; }
    .info-img { max-width: 95vw !important; }
    .card-accent-top, .card-accent-bottom { height: 5px; }
}
.fadein-panel {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.9s cubic-bezier(.4,0,.2,1), transform 0.9s cubic-bezier(.4,0,.2,1);
}
.fadein-panel.fadein-active {
    opacity: 1;
    transform: none;
}
.fadein-card {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.8s cubic-bezier(.4,0,.2,1) 0.18s, transform 0.8s cubic-bezier(.4,0,.2,1) 0.18s;
}
.fadein-card.fadein-active {
    opacity: 1;
    transform: none;
}
.fadein-img {
    opacity: 0;
    transform: scale(0.97);
    transition: opacity 0.85s cubic-bezier(.4,0,.2,1) 0.36s, transform 0.85s cubic-bezier(.4,0,.2,1) 0.36s;
}
.fadein-img.fadein-active {
    opacity: 1;
    transform: none;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.fadein-panel')?.classList.add('fadein-active');
    setTimeout(function() {
        document.querySelector('.fadein-card')?.classList.add('fadein-active');
        document.querySelector('.fadein-img')?.classList.add('fadein-active');
    }, 100);
});
</script>

<?php include 'footer.php'; ?>
