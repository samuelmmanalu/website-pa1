<?php 
include 'header.php';

$kode = mysqli_real_escape_string($conn, $_GET['produk']);
$result = mysqli_query($conn, "SELECT * FROM produk WHERE kode_produk = '$kode'");
$row = mysqli_fetch_assoc($result);


$hotspots = [];
$hotspots_query = mysqli_query($conn, "SELECT * FROM produk_hotspots WHERE kode_produk = '$kode'");
if ($hotspots_query) {
    while ($hotspot = mysqli_fetch_assoc($hotspots_query)) {
        $hotspots[] = $hotspot;
    }
}

$alat_items = [];
$alat_query = mysqli_query($conn, "SELECT * FROM produk_alat WHERE kode_produk = '$kode'");
if ($alat_query) {
    while ($alat = mysqli_fetch_assoc($alat_query)) {
        $alat_items[] = $alat;
    }
}

$bahan_items = [];
$bahan_query = mysqli_query($conn, "SELECT * FROM produk_bahan WHERE kode_produk = '$kode'");
if ($bahan_query) {
    while ($bahan = mysqli_fetch_assoc($bahan_query)) {
        $bahan_items[] = $bahan;
    }
}

$material_hotspots = [];
$material_hotspots_query = mysqli_query($conn, "SELECT * FROM produk_material_hotspots WHERE kode_produk = '$kode'");
if ($material_hotspots_query) {
    while ($hotspot = mysqli_fetch_assoc($material_hotspots_query)) {
        $material_hotspots[] = $hotspot;
    }
}

$photos_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'gambar' AND jenis = 'cara_pembuatan' ORDER BY urutan");

function formatContent($text, $intro) {
    $items = array_filter(explode("\n", $text), function($item) {
        return trim($item) !== '';
    });
    
    if (empty($items)) {
        return '<p>'.$intro.' '.htmlspecialchars(trim($text)).'</p>';
    }
    
    $html = '<p class="mb-3">'.$intro.'</p>';
    $html .= '<ul class="content-list">';
    foreach ($items as $item) {
        $html .= '<li>' . htmlspecialchars(trim($item)) . '</li>';
    }
    $html .= '</ul>';
    return $html;
}

function displayRawContent($content) {
    $alignment = 'left';
    $lines = explode("\n", $content);
    
    if (count($lines) > 0) {
        $firstLine = strtolower(trim($lines[0]));
        if (strpos($firstLine, '[center]') !== false) {
            $alignment = 'center';
            $content = implode("\n", array_slice($lines, 1));
        } 
        elseif (strpos($firstLine, '[right]') !== false) {
            $alignment = 'right';
            $content = implode("\n", array_slice($lines, 1));
        }
        elseif (strpos($firstLine, '[left]') !== false) {
            $alignment = 'left';
            $content = implode("\n", array_slice($lines, 1));
        }
    }
    
    $content = htmlspecialchars($content);
    $content = str_replace("\n", "<br>", $content);
    $content = str_replace("  ", " &nbsp;", $content);
    
    return '<div class="raw-content preserve-formatting" style="text-align: '.$alignment.'">'.$content.'</div>';
}

function formatAlatDariDatabase($alat_items, $intro) {
    if (empty($alat_items)) {
        return '<p>'.$intro.' Tidak ada data alat.</p>';
    }
    
    $html = '<p class="mb-3">'.$intro.'</p>';
    $html .= '<div class="alat-container">';
    
    foreach ($alat_items as $alat) {
        $nama_alat = htmlspecialchars($alat['nama_alat']);
        $deskripsi = htmlspecialchars($alat['deskripsi'] ?? '');
        $cara_penggunaan = htmlspecialchars($alat['cara_penggunaan'] ?? '');
        $gambar = $alat['gambar_alat'];
        
        $html .= '<div class="alat-item">';
        $html .= '<div class="alat-image-container">';
        
        $images = explode(',', $gambar);
        foreach ($images as $img) {
            $image_path = 'image/alat/'.htmlspecialchars($img);
            $html .= '<img src="'.$image_path.'" class="alat-image" ';
            $html .= 'onclick="openAlatImageModal(\''.$image_path.'\')" ';
            $html .= 'loading="eager">';
        }
        
        $html .= '<button class="alat-slider-nav prev" onclick="prevAlatImage(this)">❮</button>';
        $html .= '<button class="alat-slider-nav next" onclick="nextAlatImage(this)">❯</button>';
        $html .= '</div>';
        
        $html .= '<div class="alat-content">';
        $html .= '<div class="alat-text">'.$nama_alat.'</div>';
        
        if (!empty($deskripsi)) {
            $html .= '<div class="alat-desc">'.$deskripsi.'</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    $has_cara_penggunaan = false;
    foreach ($alat_items as $alat) {
        if (!empty($alat['cara_penggunaan'])) {
            if (!$has_cara_penggunaan) {
                $html .= '<div class="cara-penggunaan-section">';
                $html .= '<h4 class="cara-penggunaan-title">Cara Penggunaan Alat</h4>';
                $has_cara_penggunaan = true;
            }
            
            $nama_alat = htmlspecialchars($alat['nama_alat']);
            $cara_penggunaan = htmlspecialchars($alat['cara_penggunaan']);
            
            $html .= '<div class="alat-cara-penggunaan">';
            $html .= '<h5>'.$nama_alat.'</h5>';
            $html .= '<div class="cara-penggunaan-content">'.$cara_penggunaan.'</div>';
            $html .= '</div>';
        }
    }
    
    if ($has_cara_penggunaan) {
        $html .= '</div>';
    }
    
    return $html;
}

function formatBahanDariDatabase($bahan_items, $intro) {
    if (empty($bahan_items)) {
        return '<p>'.$intro.' Tidak ada data bahan.</p>';
    }
    
    $html = '<p class="mb-3">'.$intro.'</p>';
    $html .= '<div class="bahan-container">';
    
    foreach ($bahan_items as $bahan) {
        $nama_bahan = htmlspecialchars($bahan['nama_bahan']);
        $deskripsi = htmlspecialchars($bahan['deskripsi'] ?? '');
        $cara_penggunaan = htmlspecialchars($bahan['cara_penggunaan'] ?? '');
        $gambar = $bahan['gambar_bahan'];
        
        $html .= '<div class="bahan-item">';
        $html .= '<div class="bahan-image-container">';
        
        $images = explode(',', $gambar);
        foreach ($images as $img) {
            $image_path = 'image/bahan/'.htmlspecialchars($img);
            $html .= '<img src="'.$image_path.'" class="bahan-image" ';
            $html .= 'onclick="openBahanImageModal(\''.$image_path.'\')" ';
            $html .= 'loading="eager">';
        }
        
        $html .= '<button class="bahan-slider-nav prev" onclick="prevBahanImage(this)">❮</button>';
        $html .= '<button class="bahan-slider-nav next" onclick="nextBahanImage(this)">❯</button>';
        $html .= '</div>';
        
        $html .= '<div class="bahan-content">';
        $html .= '<div class="bahan-text">'.$nama_bahan.'</div>';
        
        if (!empty($deskripsi)) {
            $html .= '<div class="bahan-desc">'.$deskripsi.'</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    $has_cara_penggunaan = false;
    foreach ($bahan_items as $bahan) {
        if (!empty($bahan['cara_penggunaan'])) {
            if (!$has_cara_penggunaan) {
                $html .= '<div class="cara-penggunaan-section">';
                $html .= '<h4 class="cara-penggunaan-title">Cara Penggunaan Bahan</h4>';
                $has_cara_penggunaan = true;
            }
            
            $nama_bahan = htmlspecialchars($bahan['nama_bahan']);
            $cara_penggunaan = htmlspecialchars($bahan['cara_penggunaan']);
            
            $html .= '<div class="bahan-cara-penggunaan">';
            $html .= '<h5>'.$nama_bahan.'</h5>';
            $html .= '<div class="cara-penggunaan-content">'.$cara_penggunaan.'</div>';
            $html .= '</div>';
        }
    }
    
    if ($has_cara_penggunaan) {
        $html .= '</div>';
    }
    
    return $html;
}

function formatHotspotContent($hotspots, $intro) {
    if (empty($hotspots)) {
        return '<p>'.$intro.' Tidak ada data detail.</p>';
    }
    
    $html = '<p class="mb-3">'.$intro.'</p>';
    $html .= '<div class="hotspot-detail-container">';
    
    foreach ($hotspots as $hotspot) {
        $html .= '<div class="hotspot-detail-item">';
        $html .= '<h5 class="hotspot-detail-title">'.$hotspot['judul'].'</h5>';
        $html .= '<div class="hotspot-detail-content">'.$hotspot['deskripsi'].'</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    
    body {
      font-family: 'Roboto', sans-serif;
      overflow-x: hidden;
      background-color: #f8f9fa;
    }

    .container {
      max-width: 1200px;
      padding: 30px 15px;
    }

    .judul-produk {
      font-size: 2.8rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 25px;
      position: relative;
      padding-bottom: 15px;
    }

    .judul-produk::after {
      content: '';
      display: block;
      width: 120px;
      height: 4px;
      background: linear-gradient(to right, #ff8680, #ff6b63);
      position: absolute;
      bottom: 0;
      left: 0;
      border-radius: 2px;
    }

    .image-container {
      position: relative;
      cursor: pointer;
      display: inline-block;
      width: 100%;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .image-container:hover {
      transform: translateY(-5px);
    }

    .image-container img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      border-radius: 15px;
      transition: transform 0.3s ease;
    }

    .image-container:hover img {
      transform: scale(1.05);
    }

    .content-section {
      background: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      border: none;
      transition: all 0.3s ease;
    }

    .content-section:hover {
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .btn-custom {
      display: block;
      width: 100%;
      margin-bottom: 12px;
      padding: 12px 20px;
      font-size: 1.5rem;
      font-weight: 500;
      border-radius: 10px;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      background: white;
      color: #2c3e50;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .btn-custom:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      border-color: #ff8680;
      color: #ff8680;
    }

    .btn-custom.btn-outline-primary:hover {
      background: #ff8680;
      color: white;
    }

    .hotspot-detail-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }

    .hotspot-detail-item {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s ease;
    }

    .hotspot-detail-item:hover {
      transform: translateY(-5px);
    }

    .hotspot-detail-title {
      font-size: 1.8rem;
      color: #ff8680;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 2px solid #f0f0f0;
    }

    .alat-container, .bahan-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }

    .alat-item, .bahan-item {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }

    .alat-item:hover, .bahan-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .alat-image-container, .bahan-image-container {
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .alat-image, .bahan-image {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      margin-bottom: 25px;
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-body {
      padding: 25px;
    }

    .form-control {
      border: 2px solid #eee;
      border-radius: 10px;
      padding: 15px;
      font-size: 1.5rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #ff8680;
      box-shadow: 0 0 0 0.2rem rgba(255, 134, 128, 0.25);
    }

    .star-rating {
      font-size: 1.8rem;
    }

    .star-rating label {
      color: #ddd;
      transition: color 0.2s ease;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
      color: #FFD700;
    }

    .image-modal {
      background-color: rgba(0,0,0,0.95);
    }

    .modal-original-image {
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .modal-back-btn {
      background: #ff8680;
      padding: 12px 25px;
      border-radius: 30px;
      font-size: 1.4rem;
      box-shadow: 0 5px 15px rgba(255, 134, 128, 0.3);
    }

    .modal-back-btn:hover {
      background: #ff6b63;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(255, 107, 99, 0.4);
    }

    @media (max-width: 768px) {
      .judul-produk {
        font-size: 2.2rem;
      }

      .content-section {
        padding: 20px;
      }

      .btn-custom {
        padding: 10px 15px;
        font-size: 1.4rem;
      }

      .alat-item, .bahan-item {
        padding: 20px;
      }
    }

    .page-load-animation { opacity: 0; }
    .animate-title { animation: fadeIn 0.8s ease 0.2s forwards; }
    .animate-image { animation: fadeIn 0.8s ease 0.4s forwards; }
    .animate-buttons { animation: fadeIn 0.8s ease 0.6s forwards; }
    .animate-content { animation: fadeInUp 0.8s ease 0.8s forwards; }
    .animate-hotspot { animation: fadeIn 0.6s ease forwards; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      overflow-x: hidden;
    }
    
    .btn-custom {
      display: block;
      width: 100%;
      margin-bottom: 8px;
      transition: all 0.3s ease;
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 14px;
    }
    .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .judul-produk {
      font-size: 2.5rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 20px;
      position: relative;
    }
    .judul-produk::after {
      content: '';
      display: block;
      width: 100px;
      height: 4px;
      background-color: #ff8680;
      position: absolute;
      bottom: -10px;
      left: 0;
    }
    
    .content-section {
      font-size: 1.8rem;
      line-height: 1.8;
      color: #555;
      margin-bottom: 25px;
      padding: 20px;
      background-color: #f9f9f9;
      border-left: 5px solid #ff8680;
      border-radius: 8px;
      transition: all 0.6s ease;
      transform: translateY(20px);
      opacity: 0;
      display: none;
    }

    .content-section.active {
      transform: translateY(0);
      opacity: 1;
    }

    .content-section::-webkit-scrollbar {
      width: 8px;
    }

    .content-section::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .content-section::-webkit-scrollbar-thumb {
      background: #ff8680;
      border-radius: 4px;
    }

    .content-section::-webkit-scrollbar-thumb:hover {
      background: #ff6b63;
    }

    .content-section > * {
      margin-bottom: 15px;
    }

    .content-section > *:last-child {
      margin-bottom: 0;
    }

    .content-section::after {
      content: '';
      display: block;
      padding-bottom: 20px;
    }

    #cara_pembuatanContent .raw-content {
      margin-bottom: 30px;
    }

    @media (max-width: 768px) {
      #cara_pembuatanContent .raw-content {
        margin-bottom: 20px;
      }
    }
    
    .hotspot-detail-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    
    .hotspot-detail-item {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      width: 100%;
    }
    
    .hotspot-detail-title {
      font-size: 1.8rem;
      color: #ff8680;
      margin-bottom: 10px;
      border-bottom: 2px solid #eee;
      padding-bottom: 5px;
    }
    
    .hotspot-detail-content {
      font-size: 1.6rem;
      line-height: 1.6;
      color: #555;
    }
    
    .image-container {
      position: relative;
      cursor: pointer;
      display: inline-block;
      width: 100%;
    }
    .image-container img {
      border: 1px solid #e0e0e0;
      padding: 5px;
      background: white;
      border-radius: 8px;
      max-width: 100%;
      height: auto;
      object-fit: cover;
      width: 400px;
      height: 300px;
    }
    
    .image-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.9);
      overflow: auto;
    }
    
    .modal-content-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      position: relative;
    }
    
    .modal-image-container {
      position: relative;
      display: inline-block;
      margin: 40px;
    }
    
    .modal-original-image {
      max-width: 80vw;
      max-height: 70vh;
      border: 5px solid white;
    }
    
    /* DIMENSION STYLES */
    .dimension-container {
      position: absolute;
      pointer-events: none;
    }
    
    .height-dimension {
      left: -50px;
      top: 0;
      width: 40px;
      height: 100%;
    }
    
    .width-dimension {
      bottom: -50px;
      left: 0;
      width: 100%;
      height: 40px;
    }
    
    .dimension-line {
      position: absolute;
      background-color: rgba(255,255,255,0.8);
    }
    
    .height-dimension .dimension-line {
      right: 5px;
      top: 0;
      width: 2px;
      height: 100%;
    }
    
    .width-dimension .dimension-line {
      left: 0;
      top: 5px;
      height: 2px;
      width: 100%;
    }
    
    .dimension-label {
      position: absolute;
      color: white;
      font-weight: bold;
      font-size: 1.4rem;
      background-color: rgba(255,134,128,0.9);
      padding: 3px 8px;
      border-radius: 4px;
    }
    
    .height-dimension .dimension-label {
      right: 0;
      top: 50%;
      transform: translateY(-50%) rotate(-90deg);
      transform-origin: center;
      white-space: nowrap;
    }
    
    .width-dimension .dimension-label {
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
    }
    
    .modal-back-button, .close-modal {
      position: fixed;
      color: #f1f1f1;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
      cursor: pointer;
      z-index: 100;
    }
    
    .modal-back-button {
      top: 20px;
      left: 30px;
    }
    
    .close-modal {
      top: 20px;
      right: 30px;
    }
    
    .modal-back-button:hover, 
    .close-modal:hover { 
      color: #ff8680;
      transform: scale(1.1);
    }
    
    .modal-back-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      padding: 10px 20px;
      background-color: #ff8680;
      color: white;
      border: none;
      border-radius: 30px;
      font-size: 1.2rem;
      cursor: pointer;
      z-index: 100;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
    }
    
    .modal-back-btn:hover {
      background-color: #ff6b63;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .modal-back-btn i {
      margin-right: 8px;
    }
    
    .hotspot-info-container {
      position: absolute;
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      z-index: 10;
      border-left: 4px solid #ff8680;
      max-width: 300px;
      display: none;
    }
    
    .hotspot-info-title {
      font-size: 1.8rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 10px;
      border-bottom: 2px solid #ff8680;
      padding-bottom: 5px;
    }
    
    .hotspot-info-content {
      font-size: 1.4rem;
      line-height: 1.6;
      color: #555;
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid #eee;
    }
    
    .modal-hotspot {
      position: absolute;
      width: 24px;
      height: 24px;
      background-color: rgba(255, 80, 80, 0.9);
      border-radius: 50%;
      cursor: pointer;
      transform: translate(-50%, -50%);
      z-index: 6;
      border: 2px solid #fff;
      transition: all 0.3s ease;
      opacity: 0;
      visibility: hidden;
    }
    
    .modal-image-container:hover .modal-hotspot {
      opacity: 1;
      visibility: visible;
    }
    
    .modal-hotspot:hover, .modal-hotspot.active {
      transform: translate(-50%, -50%) scale(1.3);
      background-color: rgba(255, 50, 50, 1);
    }
    
    .alat-container, .bahan-container {
      display: flex;
      flex-direction: column;
      gap: 30px;
      margin-top: 15px;
    }

    .alat-item, .bahan-item {
      display: flex;
      align-items: flex-start;
      gap: 20px;
      background: #fff;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .alat-item:hover, .bahan-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .alat-item:nth-child(odd), .bahan-item:nth-child(odd) {
      flex-direction: row-reverse;
    }

    .alat-image-container, .bahan-image-container {
      flex: 0 0 200px;
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f5f5f5;
      border-radius: 8px;
      overflow: hidden;
      position: relative;
    }

    .alat-image, .bahan-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .alat-image:hover, .bahan-image:hover {
      transform: scale(1.05);
    }

    .alat-slider-nav, .bahan-slider-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0,0,0,0.5);
      color: white;
      border: none;
      padding: 8px;
      cursor: pointer;
      opacity: 0;
      transition: opacity 0.3s;
      z-index: 2;
    }

    .alat-slider-nav:hover, .bahan-slider-nav:hover {
      background: rgba(0,0,0,0.7);
    }

    .alat-image-container:hover .alat-slider-nav,
    .bahan-image-container:hover .bahan-slider-nav {
      opacity: 1;
    }

    .alat-slider-nav.prev, .bahan-slider-nav.prev {
      left: 0;
    }

    .alat-slider-nav.next, .bahan-slider-nav.next {
      right: 0;
    }

    .alat-content, .bahan-content {
      flex: 1;
    }

    .alat-text, .bahan-text {
      font-size: 1.8rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
    }

    .alat-desc, .bahan-desc {
      font-size: 1.4rem;
      line-height: 1.6;
      color: #555;
    }

    .cara-penggunaan-section {
      margin-top: 30px;
      width: 100%;
    }

    .cara-penggunaan-title {
      font-size: 1.8rem;
      color: #ff8680;
      margin-bottom: 15px;
      border-bottom: 2px solid #eee;
      padding-bottom: 5px;
    }

    .alat-cara-penggunaan, 
    .bahan-cara-penggunaan {
      background: #fff;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      border-left: 3px solid #ff8680;
    }

    .alat-cara-penggunaan h5, 
    .bahan-cara-penggunaan h5 {
      font-size: 1.5rem;
      color: #333;
      margin-bottom: 10px;
    }

    .cara-penggunaan-content {
      font-size: 1.4rem;
      line-height: 1.6;
      color: #555;
    }

    @media (max-width: 768px) {
      .alat-item, .bahan-item {
        flex-direction: column;
      }
      
      .alat-item:nth-child(odd), .bahan-item:nth-child(odd) {
        flex-direction: column;
      }
      
      .alat-image-container, .bahan-image-container {
        flex: 0 0 100%;
        height: 250px;
      }
    }
    
    /* BACK BUTTON STYLES */
    .back-button {
      display: inline-flex;
      align-items: center;
      padding: 10px 20px;
      font-size: 1.6rem;
      margin-bottom: 30px;
      background-color: #ffc107;
      color: #212529;
      border: none;
      border-radius: 8px;
      transition: all 0.3s;
    }
    
    .back-button:hover {
      background-color: #e0a800;
      color: #212529;
      text-decoration: none;
      transform: translateY(-2px);
    }
    
    .back-button i {
      margin-right: 8px;
    }
    
    /* RESPONSIVE */
    @media (max-width: 768px) {
      .judul-produk { font-size: 2rem; }
      .content-section { font-size: 1.6rem; padding: 15px; }
      
      .modal-original-image {
        max-width: 90vw;
        max-height: 60vh;
      }
      
      .height-dimension {
        left: -40px;
        width: 30px;
      }
      
      .width-dimension {
        bottom: -40px;
        height: 30px;
      }
      
      .dimension-label {
        font-size: 1.1rem;
      }
      
      .modal-back-btn {
        bottom: 20px;
        right: 20px;
        padding: 8px 15px;
        font-size: 1rem;
      }
      
      .hotspot-info-container {
        max-width: 250px;
      }
      
      .alat-item, .bahan-item {
        width: 100%;
      }
      
      .image-container img {
        width: 100%;
        height: auto;
      }
    }
    
    @media (max-width: 576px) {
      .hotspot-info-container {
        max-width: 200px;
      }
    }

    .video-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 30px;
    }

    .video-title {
        color: #333;
        font-size: 1.8rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #ff8680;
        padding-bottom: 10px;
    }

    .video-title i {
        color: #ff8680;
        font-size: 1.8rem;
    }

    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        height: 0;
        overflow: hidden;
        border-radius: 8px;
        background: #f5f5f5;
    }

    .video-player {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .photo-card {
        background: transparent;
        box-shadow: none;
        transition: transform 0.3s ease;
    }

    .photo-image {
        width: 200px;
        height: auto;
        transition: transform 0.3s ease;
        border-radius: 0;
    }

    .photo-card:hover {
        transform: translateY(-5px);
    }

    .photos-title {
        color: #333;
        font-size: 2rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ff8680;
    }

    .photos-title i {
        color: #ff8680;
        margin-right: 10px;
    }

    /* Photos Grid Styles */
    .photos-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 15px;
    }

    .image-hint {
        text-align: center;
        margin-top: 10px;
        color: #666;
        font-size: 1.4rem;
        font-style: italic;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 8px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
    }

    .image-hint i {
        color: #ff8680;
        font-size: 1.6rem;
    }

    @media (max-width: 768px) {
        .image-hint {
            font-size: 1.2rem;
        }
        
        .image-hint i {
            font-size: 1.4rem;
        }
    }

    /* Image Slider Styles */
    .image-slider {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    .slides-container {
        display: flex;
        transition: transform 0.5s ease;
    }

    .slide {
        min-width: 100%;
        padding: 10px;
        box-sizing: border-box;
    }

    .slide img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }

    .slider-nav {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        padding: 0 10px;
        pointer-events: none;
    }

    .slider-nav button {
        background: rgba(255, 134, 128, 0.8);
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;
        pointer-events: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .slider-nav button:hover {
        background: rgba(255, 107, 99, 0.9);
        transform: scale(1.1);
    }

    .slider-nav button:first-child {
        margin-right: auto;
    }

    .slider-nav button:last-child {
        margin-left: auto;
    }

    /* CARA PEMBUATAN TEMPLATE STYLES */
    .cara-pembuatan-template {
      margin-bottom: 20px;
    }

    .template-section {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }

    .template-title {
      font-size: 1.8rem;
      line-height: 1.8;
      color: #555;
      margin: 0;
    }

    .template-link {
      display: inline-flex;
      align-items: center;
      padding: 4px 8px;
      background: #ff8680;
      color: white;
      border-radius: 4px;
      text-decoration: none;
      font-size: 1.8rem;
      line-height: 1.8;
    }

    .template-link:hover {
      background: #ff6b63;
    }

    .template-link i {
      margin-right: 5px;
      font-size: 1.8rem;
    }

    /* Add these styles to your existing CSS */
    .pattern-image-container {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .pattern-image-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .pattern-caption {
        font-size: 1.6rem;
        color: #333;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #ff8680;
    }

    .pattern-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        background: #f8f8f8;
    }

    .pattern-image {
        width: 60%;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .pattern-image:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    .image-hint {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 8px;
        text-align: center;
        font-size: 1.4rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pattern-image-wrapper:hover .image-hint {
        opacity: 1;
    }

    .image-hint i {
        margin-right: 5px;
        color: #ff8680;
    }

    @media (max-width: 768px) {
        .pattern-image {
            height: 200px;
        }
        
        .pattern-caption {
            font-size: 1.4rem;
        }
        
        .image-hint {
            font-size: 1.2rem;
            padding: 6px;
        }
    }

    /* Add these styles to your existing CSS */
    .pattern-image {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .pattern-image:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    .template-title {
        color: #333;
        font-size: 1.8rem;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .template-title::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #ff8680;
    }

    .hotspot-marker {
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: rgba(255, 80, 80, 0.9);
        border-radius: 50%;
        cursor: pointer;
        transform: translate(-50%, -50%);
        z-index: 6;
        border: 2px solid #fff;
        transition: all 0.3s ease;
    }

    .hotspot-marker:hover {
        transform: translate(-50%, -50%) scale(1.3);
        background-color: rgba(255, 50, 50, 1);
    }

    .image-hotspot-container {
        position: relative;
        display: inline-block;
        text-align: center;
    }

    .material-hotspot-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        display: none;
    }

    .material-hotspot-container.active {
        display: block;
    }

    .material-hotspot-container .hotspot-marker {
        pointer-events: auto;
    }

    .card {
      border: none;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-2px);
    }

    .form-control {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 12px;
      font-size: 1.4rem;
    }

    .form-control:focus {
      border-color: #ff8680;
      box-shadow: 0 0 0 0.2rem rgba(255, 134, 128, 0.25);
    }

    .btn-primary {
      background-color: #ff8680;
      border-color: #ff8680;
      font-size: 1.4rem;
      padding: 8px 20px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #ff6b63;
      border-color: #ff6b63;
      transform: translateY(-2px);
    }

    .card-title {
      font-size: 1.6rem;
      color: #333;
    }

    .card-text {
      font-size: 1.4rem;
      color: #555;
      line-height: 1.6;
    }

    .text-muted {
      font-size: 1.2rem;
    }

    .alert {
      font-size: 1.4rem;
      border-radius: 8px;
    }

    .alert a {
      color: #ff8680;
      text-decoration: none;
      font-weight: bold;
    }

    .alert a:hover {
      text-decoration: underline;
    }

    .star-rating {
      display: inline-flex;
      flex-direction: row-reverse;
      font-size: 1.5em;
      justify-content: flex-start;
      margin-bottom: 0.5rem;
    }

    .star-rating input {
      display: none;
    }

    .star-rating label {
      color: #ddd;
      cursor: pointer;
      padding: 0 0.1em;
      transition: color 0.2s ease-in-out;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
      color: #FFD700;
    }

    .star-rating-display {
      font-size: 1.2em;
      margin-bottom: 0.5rem;
    }

    .star-rating-display .fas {
      margin-right: 2px;
    }

    .star-rating-display .fas.text-warning {
      color: #FFD700 !important;
    }

    .btn-group {
      display: flex;
      gap: 5px;
    }

    .btn-group .btn {
      padding: 4px 8px;
      font-size: 1.2rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="page-load-animation animate-title" style="border-bottom: 4px solid #ff8680"><b>Detail Ulos</b></h2>
      <a href="javascript:history.back()" class="back-button page-load-animation animate-buttons">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="image-container page-load-animation animate-image">
          <img src="image/produk/<?= $row['image']; ?>" class="img-responsive thumbnail-image" onclick="openImageModal('image/produk/<?= $row['image']; ?>')">
        </div>
        <p class="text-center mt-2" style="color: #666; font-style: italic;">
          <i class="fas fa-info-circle" style="color: #ff8680;"></i> 
          Klik gambar untuk melihat ukuran dan detail
        </p>

        <div class="page-load-animation animate-buttons" style="margin-top: 10px;">
          <button type="button" class="btn btn-sm btn-outline-primary btn-custom" onclick="showContent('deskripsi')">Deskripsi</button>
          <button type="button" class="btn btn-sm btn-outline-danger btn-custom" onclick="showContent('sejarah')">Sejarah</button>
          <button type="button" class="btn btn-sm btn-outline-success btn-custom" onclick="showContent('detail')">Detail</button>
          <button type="button" class="btn btn-sm btn-outline-warning btn-custom" onclick="showContent('bahan')">Bahan</button>
          <button type="button" class="btn btn-sm btn-outline-warning btn-custom" onclick="showContent('alat')">Alat</button>
          <button type="button" class="btn btn-sm btn-outline-info btn-custom" onclick="showContent('cara_pembuatan')">Cara Pembuatan</button>
        </div>
      </div>

      <div class="col-md-8">
        <div class="product-details page-load-animation animate-content">
          <h3 class="judul-produk"><?= $row['nama']; ?></h3>

          <div id="deskripsiContent" class="content-section">
            <?= $row['deskripsi']; ?>
          </div>
          
          <div id="detailContent" class="content-section" style="display: none;">
            <h4>Detail Ulos</h4>
            <?= formatHotspotContent($hotspots, 'Berikut detail lengkap dari ulos ini:') ?>
          </div>
          
          <div id="cara_pembuatanContent" class="content-section" style="display: none;">
            <h4>Cara Pembuatan</h4>
            
            <div class="cara-pembuatan-template">
                <div class="template-section">
                    <h5 class="template-title">1. Persiapan Bahan</h5>
                    <a href="#bahanContent" class="template-link" onclick="showContent('bahan')">
                        <i class="fas fa-arrow-right"></i> Bahan
                    </a>
                </div>
                
                <div class="template-section">
                    <h5 class="template-title">2. Persiapan Alat</h5>
                    <a href="#alatContent" class="template-link" onclick="showContent('alat')">
                        <i class="fas fa-arrow-right"></i> Alat
                    </a>
                </div>

                <?php 

                if (mysqli_num_rows($photos_query) > 0): 
                ?>
                    <div class="text-center mb-4">
                        <h5 class="template-title">Gambar Pola</h5>
                    </div>
                    <div class="row justify-content-center">
                        <?php 
                        $counter = 1;
                        while ($photo = mysqli_fetch_assoc($photos_query)): 
                        ?>
                            <div class="col-12 text-center mb-4">
                                <?php if (!empty($photo['keterangan'])): ?>
                                    <div class="mb-2">
                                        <?= $counter . '. ' . htmlspecialchars($photo['keterangan']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="d-inline-block">
                                    <div class="image-hotspot-container">
                                        <img src="image/materials/<?= htmlspecialchars($photo['nama_file']) ?>" 
                                             class="img-fluid pattern-image" 
                                             alt="Gambar Pola"
                                             onclick="openImageModal('image/materials/<?= htmlspecialchars($photo['nama_file']) ?>', <?= $photo['id'] ?>)"
                                             style="width: 60%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                        <div class="material-hotspot-container" id="materialHotspotContainer<?= $photo['id'] ?>">
                                            <?php 
                                            foreach ($material_hotspots as $hotspot):
                                                if ($hotspot['material_photo_id'] == $photo['id']):
                                            ?>
                                                <div class="hotspot-marker" 
                                                     style="left: <?= $hotspot['x_position'] ?>; top: <?= $hotspot['y_position'] ?>;"
                                                     data-title="<?= htmlspecialchars($hotspot['judul']) ?>"
                                                     data-desc="<?= htmlspecialchars($hotspot['deskripsi']) ?>"
                                                     onclick="showHotspotInfo(this)">
                                                </div>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        $counter++;
                        endwhile; 
                        ?>
                    </div>
                <?php endif; ?>

                <?php 
              
                $rumus_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'gambar' AND jenis = 'rumus' ORDER BY urutan");
                if (mysqli_num_rows($rumus_query) > 0): 
                ?>
                    <div class="template-section">
                        <h5 class="template-title">3. Rumus Pembuatan</h5>
                        <div class="rumus-container">
                            <?php 
                            while ($rumus = mysqli_fetch_assoc($rumus_query)): 
                            ?>
                                <div class="rumus-item">
                                    <div class="image-container">
                                        <img src="image/materials/<?= htmlspecialchars($rumus['nama_file']) ?>" 
                                             class="rumus-image" 
                                             onclick="openImageModal('image/materials/<?= htmlspecialchars($rumus['nama_file']) ?>')"
                                             alt="Rumus Pembuatan">
                                        <div class="image-hint">
                                            <i class="fas fa-info-circle"></i> Klik gambar untuk melihat detail rumus
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?= displayRawContent($row['cara_pembuatan']) ?>
            
            <?php 
          
            $video_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'video' AND jenis = 'cara_pembuatan'");
            $video = mysqli_fetch_assoc($video_query);
            if ($video): 
            ?>
                <div class="mt-4">
                    <div class="video-container">
                        <h5 class="video-title">
                            <i class="fas fa-video"></i> Video Cara Pembuatan
                        </h5>
                        <div class="video-wrapper">
                            <video class="video-player" controls poster="image/produk/<?= $row['image'] ?>">
                                <source src="video/<?= htmlspecialchars($video['nama_file']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
          </div>
          
          <div id="alatContent" class="content-section" style="display: none;">
            <h4>Alat</h4>
            <?= formatAlatDariDatabase($alat_items, 'Alat-alat yang digunakan dalam pembuatan ulos:') ?>
          </div>
          
          <div id="bahanContent" class="content-section" style="display: none;">
            <h4>Bahan</h4>
            <?php 

            $jumlah_benang = '';
            if (!empty($bahan_items)) {
                $jumlah_benang = $bahan_items[0]['jumlah_benang'] ?? '';
            }
            ?>
            <p class="mb-3">Bahan-bahan yang diperlukan pada ulos:</p>
            <?php if (!empty($jumlah_benang)): ?>
            <div class="bahan-jumlah-benang">
                <strong>Jumlah Benang yang dibutuhkan:</strong> <?= htmlspecialchars($jumlah_benang) ?>
            </div>
            <?php endif; ?>
            <?= formatBahanDariDatabase($bahan_items, '') ?>
          </div>
          
          <div id="sejarahContent" class="content-section" style="display: none;">
            <h4>Sejarah</h4>
            <?= formatContent($row['sejarah'], 'Sejarah dan asal-usul dari ulos ini:') ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div id="imageModal" class="image-modal">
    <a href="javascript:void(0)" class="modal-back-button" onclick="closeImageModal()">←</a>
    <span class="close-modal" onclick="closeImageModal()">&times;</span>
    <div class="modal-content-wrapper">
      <div class="modal-image-container">
        <img class="modal-original-image" id="modalImage">
      </div>
    </div>
    <button class="modal-back-btn" onclick="closeImageModal()">
      <i class="fas fa-arrow-left"></i> Kembali
    </button>
  </div>



  <script>
    
    document.addEventListener('DOMContentLoaded', function() {
      
      const alatImages = document.querySelectorAll('.alat-image, .bahan-image');
      alatImages.forEach(img => {
        const image = new Image();
        image.src = img.src;
      });

      showContent('deskripsi');

      document.querySelectorAll('.image-slider').forEach(slider => {
        const slidesContainer = slider.querySelector('.slides-container');
        const slides = slider.querySelectorAll('.slide');
        const prevButton = slider.querySelector('.prev-slide');
        const nextButton = slider.querySelector('.next-slide');
        let currentSlide = 0;

        function updateSlider() {
          slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        prevButton.addEventListener('click', () => {
          currentSlide = (currentSlide - 1 + slides.length) % slides.length;
          updateSlider();
        });

        nextButton.addEventListener('click', () => {
          currentSlide = (currentSlide + 1) % slides.length;
          updateSlider();
        });

        updateSlider();
      });
    });

    let activeHotspot = null;
    const hotspots = <?php echo json_encode($hotspots); ?>;
    
    function showContent(type) {
      
      document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
        section.classList.remove('active');
      });

      const selectedContent = document.getElementById(type + 'Content');
      selectedContent.style.display = 'block';
     
      setTimeout(() => {
        selectedContent.classList.add('active');
      }, 10);
    }
    
    function openAlatImageModal(imageSrc) {
      const modal = document.getElementById('imageModal');
      const modalImg = document.getElementById('modalImage');
      
      modal.style.display = 'block';
      modalImg.src = imageSrc;
      document.body.style.overflow = 'hidden';

      document.querySelectorAll('.modal-hotspot, .dimension-container, #hotspotInfoPanel').forEach(el => el.remove());
    }
    
    function openBahanImageModal(imageSrc) {
      const modal = document.getElementById('imageModal');
      const modalImg = document.getElementById('modalImage');
      
      modal.style.display = 'block';
      modalImg.src = imageSrc;
      document.body.style.overflow = 'hidden';

      document.querySelectorAll('.modal-hotspot, .dimension-container, #hotspotInfoPanel').forEach(el => el.remove());
    }
    
    function openImageModal(imageSrc, photoId) {
      const modal = document.getElementById('imageModal');
      const modalImg = document.getElementById('modalImage');
      
      modal.style.display = 'block';
      modalImg.src = imageSrc;
      document.body.style.overflow = 'hidden';

      document.querySelectorAll('.modal-hotspot, .dimension-container, #hotspotInfoPanel').forEach(el => el.remove());

      if (imageSrc.includes('image/produk/')) {
        const container = document.querySelector('.modal-image-container');
        
        const heightDimension = document.createElement('div');
        heightDimension.className = 'dimension-container height-dimension';
        heightDimension.innerHTML = `
          <div class="dimension-line"></div>
          <div class="dimension-label"><?= $row['panjang'] ?> cm</div>
        `;
        
        const widthDimension = document.createElement('div');
        widthDimension.className = 'dimension-container width-dimension';
        widthDimension.innerHTML = `
          <div class="dimension-line"></div>
          <div class="dimension-label"><?= $row['lebar'] ?> cm</div>
        `;
        
        container.appendChild(heightDimension);
        container.appendChild(widthDimension);
        
        const infoPanel = document.createElement('div');
        infoPanel.id = 'hotspotInfoPanel';
        infoPanel.className = 'hotspot-info-container';
        infoPanel.innerHTML = `
          <h4 id="hotspotTitle" class="hotspot-info-title"></h4>
          <div id="hotspotContent" class="hotspot-info-content"></div>
        `;
        container.appendChild(infoPanel);
        
        createModalHotspots(<?php echo json_encode($hotspots); ?>, photoId);
      } 
      
      else if (imageSrc.includes('image/materials/')) {
        const container = document.querySelector('.modal-image-container');
        
        const infoPanel = document.createElement('div');
        infoPanel.id = 'hotspotInfoPanel';
        infoPanel.className = 'hotspot-info-container';
        infoPanel.innerHTML = `
          <h4 id="hotspotTitle" class="hotspot-info-title"></h4>
          <div id="hotspotContent" class="hotspot-info-content"></div>
        `;
        container.appendChild(infoPanel);

        const materialHotspots = <?php echo json_encode($material_hotspots); ?>;
        const imageFileName = imageSrc.split('/').pop();
        const currentPhoto = <?php 
            $photos = [];
            $photos_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'gambar' AND jenis = 'cara_pembuatan' ORDER BY urutan");
            if ($photos_query) {
                while ($photo = mysqli_fetch_assoc($photos_query)) {
                    $photos[] = $photo;
                }
            }
            echo json_encode($photos);
        ?>;
        const currentPhotoId = currentPhoto.find(p => p.nama_file === imageFileName)?.id;

        if (currentPhotoId) {
            const filteredHotspots = materialHotspots.filter(h => h.material_photo_id == currentPhotoId);
            createModalHotspots(filteredHotspots, photoId);
        }
      }
    }
    
    function createModalHotspots(hotspots, photoId) {
      const container = document.querySelector('.modal-image-container');
      const infoPanel = document.getElementById('hotspotInfoPanel');
      
      document.querySelectorAll('.modal-hotspot').forEach(el => el.remove());
      
      hotspots.forEach((hotspot, index) => {
        const dot = document.createElement('div');
        dot.className = 'modal-hotspot';
        dot.style.left = hotspot.x_position;
        dot.style.top = hotspot.y_position;
        dot.dataset.title = hotspot.judul;
        dot.dataset.content = hotspot.deskripsi;
        
        dot.addEventListener('click', function(e) {
          e.stopPropagation();
          
          const titleElement = document.getElementById('hotspotTitle');
          const contentElement = document.getElementById('hotspotContent');
          
          if (activeHotspot === dot) {
            infoPanel.style.display = 'none';
            dot.classList.remove('active');
            activeHotspot = null;
            return;
          }
          
          if (activeHotspot) {
            activeHotspot.classList.remove('active');
          }
          
          titleElement.textContent = this.dataset.title;
          contentElement.textContent = this.dataset.content;
          
          positionInfoPanel(infoPanel, this);
          
          infoPanel.style.display = 'block';
          dot.classList.add('active');
          activeHotspot = dot;
        });

        container.appendChild(dot);
      });
      
      container.addEventListener('mouseleave', function() {
        if (activeHotspot) {
          activeHotspot.classList.remove('active');
          activeHotspot = null;
          infoPanel.style.display = 'none';
        }
      });
    }
    
    function positionInfoPanel(panel, hotspot) {
      const container = document.querySelector('.modal-image-container');
      const containerRect = container.getBoundingClientRect();
      const hotspotRect = hotspot.getBoundingClientRect();
      
      const hotspotX = hotspotRect.left - containerRect.left;
      const hotspotY = hotspotRect.top - containerRect.top;
      
      if (hotspotX + 200 < containerRect.width) {
        panel.style.left = `${hotspotX + 15}px`;
      } else {
        panel.style.left = `${hotspotX - 230}px`;
      }
      
      if (hotspotY + 100 < containerRect.height) {
        panel.style.top = `${hotspotY + 15}px`;
      } else {
        panel.style.top = `${hotspotY - 130}px`;
      }
    }
    
    function closeImageModal() {
      document.getElementById('imageModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    
    window.onclick = function(event) {
      const modal = document.getElementById('imageModal');
      if (event.target == modal) {
        closeImageModal();
      }
    }
    
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeImageModal();
      }
    });

    function prevAlatImage(button) {
      const container = button.closest('.alat-image-container');
      const images = container.querySelectorAll('.alat-image');
      const currentIndex = Array.from(images).findIndex(img => img.style.display !== 'none');
      const prevIndex = (currentIndex - 1 + images.length) % images.length;
      
      images[currentIndex].style.display = 'none';
      images[prevIndex].style.display = 'block';
    }

    function nextAlatImage(button) {
      const container = button.closest('.alat-image-container');
      const images = container.querySelectorAll('.alat-image');
      const currentIndex = Array.from(images).findIndex(img => img.style.display !== 'none');
      const nextIndex = (currentIndex + 1) % images.length;
      
      images[currentIndex].style.display = 'none';
      images[nextIndex].style.display = 'block';
    }

    function prevBahanImage(button) {
      const container = button.closest('.bahan-image-container');
      const images = container.querySelectorAll('.bahan-image');
      const currentIndex = Array.from(images).findIndex(img => img.style.display !== 'none');
      const prevIndex = (currentIndex - 1 + images.length) % images.length;
      
      images[currentIndex].style.display = 'none';
      images[prevIndex].style.display = 'block';
    }

    function nextBahanImage(button) {
      const container = button.closest('.bahan-image-container');
      const images = container.querySelectorAll('.bahan-image');
      const currentIndex = Array.from(images).findIndex(img => img.style.display !== 'none');
      const nextIndex = (currentIndex + 1) % images.length;
      
      images[currentIndex].style.display = 'none';
      images[nextIndex].style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', function() {

      document.querySelectorAll('.alat-image-container, .bahan-image-container').forEach(container => {
        const images = container.querySelectorAll('img');
        if (images.length > 0) {
          images.forEach((img, index) => {
            if (index !== 0) {
              img.style.display = 'none';
            }
          });
        }
      });
    });

    function showHotspotInfo(element) {
        const title = element.getAttribute('data-title');
        const desc = element.getAttribute('data-desc');
        
        let infoPanel = document.getElementById('hotspotInfoPanel');
        if (!infoPanel) {
            infoPanel = document.createElement('div');
            infoPanel.id = 'hotspotInfoPanel';
            infoPanel.className = 'hotspot-info-container';
            element.closest('.image-hotspot-container').appendChild(infoPanel);
        }
        
        infoPanel.innerHTML = `
            <h4 class="hotspot-info-title">${title}</h4>
            <div class="hotspot-info-content">${desc}</div>
        `;
        
        const left = element.style.left;
        const top = element.style.top;
        
        infoPanel.style.left = left;
        infoPanel.style.top = top;
        
        infoPanel.style.display = 'block';
        
        document.addEventListener('click', function closePanel(e) {
            if (!infoPanel.contains(e.target) && e.target !== element) {
                infoPanel.style.display = 'none';
                document.removeEventListener('click', closePanel);
            }
        });
    }

    function editComment(commentId, commentText, rating) {

      document.getElementById('comment-text-' + commentId).style.display = 'none';
      
      document.getElementById('edit-form-' + commentId).style.display = 'block';
      
      document.querySelector('input[name="rating"][value="' + rating + '"]').checked = true;
    }

    function cancelEdit(commentId) {

      document.getElementById('comment-text-' + commentId).style.display = 'block';
      
      document.getElementById('edit-form-' + commentId).style.display = 'none';
    }
  </script>
</body>
</html>

<?php include 'footer.php'; ?>