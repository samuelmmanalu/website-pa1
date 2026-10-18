<?php include 'header.php'; ?>
<div class="container fadein-panel" style="max-width:900px; padding:60px 0 40px 0; min-height:60vh;">
    <div class="card-accent-top"></div>
    <h1 style="font-family:'Playfair Display',serif; color:#ff8680; font-weight:700; margin-bottom:30px; text-align:center;">Kegunaan Ulos dalam Upacara Adat</h1>
    <div class="fadein-card info-card">
        <img src="slebew.jpg" alt="Upacara Adat" class="fadein-img info-img">
        <p style="font-family:'Montserrat',sans-serif; font-size:18px; color:#444; line-height:1.7; margin-bottom:18px; text-align:justify;">Ulos adalah kain tenun khas suku Batak yang tidak hanya digunakan sebagai pelindung tubuh, tetapi memiliki makna simbolis yang sangat kuat dalam berbagai upacara adat sebagai perwujudan kasih sayang, restu, penghormatan, dan hubungan sosial. Dalam upacara pernikahan, pemberian ulos dari orang tua atau pihak hula-hula kepada pasangan pengantin disebut mangulosi, yang melambangkan restu agar pernikahan mereka diberkati, penuh kasih, dan sejahtera; ulos yang digunakan biasanya ulos Ragidup atau Mangiring yang dianggap membawa kesuburan dan kebahagiaan. Saat kelahiran anak, ulos diberikan kepada ibu dan bayi sebagai bentuk doa agar keduanya sehat, selamat, dan dijauhkan dari roh jahat, karena ulos dipercaya membawa kehangatan lahir dan batin. Dalam upacara kematian, ulos dipakaikan pada jenazah sebagai tanda penghormatan terakhir, perlindungan spiritual, serta lambang cinta dan duka dari keluarga yang ditinggalkan; biasanya digunakan ulos Saput atau Ragi Hotang. Ulos juga diberikan dalam upacara naik rumah baru, pesta syukuran, atau pelantikan tokoh adat, dengan harapan rumah tersebut diberkati dan penghuninya hidup dalam kedamaian. Pemberian ulos bukanlah tindakan sembarangan, karena harus mengikuti struktur adat Batak seperti dalihan na tolu, yang membagi relasi sosial menjadi tiga: hula-hula (pemberi ulos), dongan tubu (kerabat sedarah), dan boru (penerima ulos); hubungan ini menentukan siapa yang berhak memberi ulos dan siapa yang pantas menerimanya. Jenis ulos yang digunakan juga berbeda tergantung peristiwa dan status sosial penerima, sehingga ulos menjadi identitas budaya dan simbol posisi dalam masyarakat. Ulos yang telah diberikan dalam upacara sering disimpan sebagai warisan keluarga, bukan hanya karena nilainya secara budaya dan sejarah, tetapi juga karena dipercaya mengandung kekuatan spiritual serta menjadi pengingat akan doa dan kasih sayang yang melekat padanya. Dengan demikian, ulos dalam upacara adat tidak hanya berfungsi secara estetis, tetapi juga sebagai media pewaris nilai-nilai luhur, norma sosial, dan spiritualitas masyarakat Batak yang diwariskan dari generasi ke generasi.</p>
        <ul style="font-family:'Montserrat',sans-serif; font-size:17px; color:#555; padding-left:20px;">
        </ul>
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
