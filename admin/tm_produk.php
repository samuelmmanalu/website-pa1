<?php 
include 'header.php';

// Generate automatic product code
$kode = mysqli_query($conn, "SELECT kode_produk FROM produk ORDER BY kode_produk DESC LIMIT 1");
$data = mysqli_fetch_assoc($kode);
$num = substr($data['kode_produk'] ?? 'P0000', 1, 4); // Start from 0 if empty
$add = (int)$num + 1;

if(strlen($add) == 1){
    $format = "P000" . $add;
} else if(strlen($add) == 2){
    $format = "P00" . $add;
} else if(strlen($add) == 3){
    $format = "P0" . $add;
} else {
    $format = "P" . $add;
}

// PROCESS ADD PRODUCT WHEN FORM IS SUBMITTED
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_produk = $_POST['kode'];
    $nama_produk = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['desk'];
    $panjang = $_POST['panjang'];
    $lebar = $_POST['lebar'];
    $jumlah_benang = $_POST['jumlah_benang'] ?? '';
    
    // Format textarea list menjadi baris-baris
    $detail = implode("\n", array_filter(array_map('trim', explode("\n", $_POST['detail']))));
    $cara_pembuatan = $_POST['cara_pembuatan'];
    $sejarah = $_POST['sejarah'];

    // Handle main product image upload
    $image = "";
    if (!empty($_FILES['files']['name'])) {
        $target_dir = "../image/produk/";
        $file_name = basename($_FILES["files"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["files"]["tmp_name"], $target_file)) {
            $image = $file_name;
        } else {
            echo "<script>alert('Gagal upload gambar utama!');</script>";
        }
    }

    // Insert product into produk table
    $query_produk = "INSERT INTO produk (kode_produk, nama, harga, kategori, deskripsi, image, panjang, lebar, detail, cara_pembuatan, sejarah) 
                     VALUES ('$kode_produk', '$nama_produk', '$harga', '$kategori', '$deskripsi', '$image', '$panjang', '$lebar', '$detail', '$cara_pembuatan', '$sejarah')";
    $insert_produk = mysqli_query($conn, $query_produk);

    // Handle video upload
    if (!empty($_FILES['video_file']['name'])) {
        $target_dir = "../video/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $video_name = time() . '_' . basename($_FILES["video_file"]["name"]);
        $target_file = $target_dir . $video_name;
        
        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file)) {
            $insert_video = "INSERT INTO produk_media (kode_produk, nama_file, tipe, jenis) 
                           VALUES ('$kode_produk', '$video_name', 'video', 'cara_pembuatan')";
            mysqli_query($conn, $insert_video);
        }
    }

    // Handle material photos upload
    if (!empty($_FILES['material_photos']['name'][0])) {
        $target_dir = "../image/materials/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        foreach ($_FILES['material_photos']['tmp_name'] as $key => $tmp_name) {
            if (!empty($_FILES['material_photos']['name'][$key])) {
                $photo_name = time() . '_' . $key . '_' . basename($_FILES['material_photos']['name'][$key]);
                $target_file = $target_dir . $photo_name;
                
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $description = $_POST['new_photo_descriptions'][$key] ?? '';
                    $insert_photo = "INSERT INTO produk_media (kode_produk, nama_file, tipe, jenis, urutan, keterangan) 
                                   VALUES ('$kode_produk', '$photo_name', 'gambar', 'cara_pembuatan', $key, '$description')";
                    mysqli_query($conn, $insert_photo);
                    
                    $photo_id = mysqli_insert_id($conn);
                    
                    // Handle material hotspots
                    if (isset($_POST['material_hotspots'][$key])) {
                        foreach ($_POST['material_hotspots'][$key] as $hotspot) {
                            $judul = mysqli_real_escape_string($conn, $hotspot['judul']);
                            $deskripsi = mysqli_real_escape_string($conn, $hotspot['deskripsi']);
                            $x_position = mysqli_real_escape_string($conn, $hotspot['x_position']);
                            $y_position = mysqli_real_escape_string($conn, $hotspot['y_position']);
                            
                            $insert_hotspot = "INSERT INTO produk_hotspots 
                                            (kode_produk, material_photo_id, judul, deskripsi, x_position, y_position, jenis) 
                                            VALUES 
                                            ('$kode_produk', '$photo_id', '$judul', '$deskripsi', '$x_position', '$y_position', 'material')";
                            mysqli_query($conn, $insert_hotspot);
                        }
                    }
                }
            }
        }
    }

    // Handle alat (tools) data
    if (isset($_POST['alat_names'])) {
        foreach ($_POST['alat_names'] as $index => $alat_name) {
            $alat_name = trim($alat_name);
            if (empty($alat_name)) continue;
            
            $image_name = '';
            $deskripsi = $_POST['alat_descriptions'][$index] ?? '';
            $cara_penggunaan = $_POST['alat_cara_penggunaan'][$index] ?? '';
            
            // Handle alat image upload
            if (isset($_FILES['alat_images']['name'][$index]) && 
                !empty($_FILES['alat_images']['name'][$index])) {
                
                $target_dir = "../image/alat/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $image_names = [];
                
                // Handle multiple files
                if (is_array($_FILES['alat_images']['name'][$index])) {
                    $file_count = count($_FILES['alat_images']['name'][$index]);
                    for ($i = 0; $i < $file_count; $i++) {
                        if (!empty($_FILES['alat_images']['name'][$index][$i])) {
                            $file_name = time() . '_' . $i . '_' . basename($_FILES['alat_images']['name'][$index][$i]);
                            $target_file = $target_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES['alat_images']['tmp_name'][$index][$i], $target_file)) {
                                $image_names[] = $file_name;
                            }
                        }
                    }
                } else {
                    // Handle single file
                    if (!empty($_FILES['alat_images']['name'][$index])) {
                        $file_name = time() . '_' . basename($_FILES['alat_images']['name'][$index]);
                        $target_file = $target_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['alat_images']['tmp_name'][$index], $target_file)) {
                            $image_names[] = $file_name;
                        }
                    }
                }
                
                $image_name = implode(',', $image_names);
            }
            
            $alat_name = mysqli_real_escape_string($conn, $alat_name);
            $image_name = mysqli_real_escape_string($conn, $image_name);
            $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
            $cara_penggunaan = mysqli_real_escape_string($conn, $cara_penggunaan);
            
            $insert_alat_query = "INSERT INTO produk_alat (kode_produk, nama_alat, gambar_alat, deskripsi, cara_penggunaan) 
                              VALUES ('$kode_produk', '$alat_name', '$image_name', '$deskripsi', '$cara_penggunaan')";
            mysqli_query($conn, $insert_alat_query);
        }
    }
    
    // Handle bahan (materials) data
    if (isset($_POST['bahan_names'])) {
        foreach ($_POST['bahan_names'] as $index => $bahan_name) {
            $bahan_name = trim($bahan_name);
            if (empty($bahan_name)) continue;
            
            $image_name = '';
            $deskripsi = $_POST['bahan_descriptions'][$index] ?? '';
            $cara_penggunaan = $_POST['bahan_cara_penggunaan'][$index] ?? '';
            
            // Handle bahan image upload
            if (isset($_FILES['bahan_images']['name'][$index]) && 
                !empty($_FILES['bahan_images']['name'][$index])) {
                
                $target_dir = "../image/bahan/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $image_names = [];
                
                // Handle multiple files
                if (is_array($_FILES['bahan_images']['name'][$index])) {
                    $file_count = count($_FILES['bahan_images']['name'][$index]);
                    for ($i = 0; $i < $file_count; $i++) {
                        if (!empty($_FILES['bahan_images']['name'][$index][$i])) {
                            $file_name = time() . '_' . $i . '_' . basename($_FILES['bahan_images']['name'][$index][$i]);
                            $target_file = $target_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES['bahan_images']['tmp_name'][$index][$i], $target_file)) {
                                $image_names[] = $file_name;
                            }
                        }
                    }
                } else {
                    // Handle single file
                    if (!empty($_FILES['bahan_images']['name'][$index])) {
                        $file_name = time() . '_' . basename($_FILES['bahan_images']['name'][$index]);
                        $target_file = $target_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['bahan_images']['tmp_name'][$index], $target_file)) {
                            $image_names[] = $file_name;
                        }
                    }
                }
                
                $image_name = implode(',', $image_names);
            }
            
            $bahan_name = mysqli_real_escape_string($conn, $bahan_name);
            $image_name = mysqli_real_escape_string($conn, $image_name);
            $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
            $cara_penggunaan = mysqli_real_escape_string($conn, $cara_penggunaan);
            
            // Only save jumlah_benang with the first bahan item
            $insert_bahan_query = "INSERT INTO produk_bahan (kode_produk, nama_bahan, gambar_bahan, deskripsi, cara_penggunaan, jumlah_benang) 
                                VALUES ('$kode_produk', '$bahan_name', '$image_name', '$deskripsi', '$cara_penggunaan', " . 
                                ($index === 0 ? "'$jumlah_benang'" : "NULL") . ")";
            mysqli_query($conn, $insert_bahan_query);
        }
    }
    
    // Handle main product hotspots
    if (isset($_POST['hotspots'])) {
        foreach ($_POST['hotspots'] as $hotspot) {
            $judul = mysqli_real_escape_string($conn, $hotspot['judul']);
            $deskripsi = mysqli_real_escape_string($conn, $hotspot['deskripsi']);
            $x_position = mysqli_real_escape_string($conn, $hotspot['x_position']);
            $y_position = mysqli_real_escape_string($conn, $hotspot['y_position']);
            
            $query = "INSERT INTO produk_hotspots 
                      (kode_produk, judul, deskripsi, x_position, y_position, jenis) 
                      VALUES 
                      ('$kode_produk', '$judul', '$deskripsi', '$x_position', '$y_position', 'utama')";
            
            mysqli_query($conn, $query);
        }
    }

    if ($insert_produk) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location='m_produk.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <style>
        .hotspot-editor-container, .alat-editor-container, .bahan-editor-container {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .image-hotspot-container {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }
        .hotspot-marker {
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: rgba(255, 0, 0, 0.7);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid white;
            cursor: pointer;
            z-index: 10;
        }
        .hotspot-marker:hover {
            background-color: rgba(255, 0, 0, 0.9);
            transform: translate(-50%, -50%) scale(1.2);
        }
        .hotspot-form {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .list-textarea {
            min-height: 150px;
            white-space: pre;
            font-family: monospace;
            line-height: 1.5;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .button-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .alat-item, .bahan-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .alat-image-preview, .bahan-image-preview {
            max-width: 100px;
            max-height: 100px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 3px;
        }
        .remove-alat-button, .remove-bahan-button {
            color: #fff;
            background-color: #dc3545;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 10px;
        }
        .add-alat-button, .add-bahan-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .row > div {
            margin-bottom: 15px;
        }
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #ff8680;
            box-shadow: 0 0 0 0.2rem rgba(255, 134, 128, 0.25);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #ff8680;
            padding: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        .btn-primary {
            background-color: #ff8680;
            border-color: #ff8680;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #ff6b63;
            border-color: #ff6b63;
            transform: translateY(-2px);
        }
        .btn-danger {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ff8680;
            color: #333;
            font-weight: 600;
        }
        .table tbody tr:hover {
            background-color: rgba(255, 134, 128, 0.05);
        }
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .nav-tabs {
            border-bottom: 2px solid #ff8680;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #ff8680;
            background-color: rgba(255, 134, 128, 0.1);
        }
        .nav-tabs .nav-link.active {
            color: #ff8680;
            background-color: rgba(255, 134, 128, 0.1);
            border: none;
            border-bottom: 2px solid #ff8680;
        }
        .photo-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin: 0.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .photo-description {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }
        .photo-description:focus {
            border-color: #ff8680;
            box-shadow: 0 0 0 0.2rem rgba(255, 134, 128, 0.25);
        }
        .hotspot-info {
            position: absolute;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 20;
            max-width: 200px;
            display: none;
        }
        .hotspot-info h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }
        .hotspot-info p {
            margin: 0;
            font-size: 12px;
        }
        .material-photo-container {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .material-photo-hotspot {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        .material-hotspot-marker {
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: rgba(0, 0, 255, 0.7);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid white;
            cursor: pointer;
            z-index: 10;
        }
        .material-hotspot-marker:hover {
            background-color: rgba(0, 0, 255, 0.9);
            transform: translate(-50%, -50%) scale(1.2);
        }
        .material-hotspot-form {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 style="width: 100%; border-bottom: 4px solid gray"><b>Tambah Produk</b></h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Upload gambar produk utama -->
        <div class="form-group">
            <label for="exampleInputFile">
                <img src="../image/no-image.jpg" width="100" id="previewImage">
            </label>
            <input type="file" id="exampleInputFile" name="files" required>
            <p class="help-block">Pilih Gambar untuk Produk</p>
        </div>

        <!-- Informasi dasar produk -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail1">Kode Produk</label>
                    <input type="text" class="form-control" disabled value="<?= $format; ?>">
                    <input type="hidden" name="kode" value="<?= $format; ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Kategori Produk</label>
                    <select class="form-control" name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Modern">Modern</option>
                        <option value="Tradisional">Tradisional</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Panjang (cm)</label>
                    <input type="number" step="0.1" class="form-control" name="panjang">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Lebar (cm)</label>
                    <input type="number" step="0.1" class="form-control" name="lebar">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="desk" class="form-control" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label>Jumlah Benang yang Dibutuhkan</label>
            <input type="text" class="form-control" name="jumlah_benang" placeholder="Contoh: 2 kg">
        </div>

        <div class="form-group">
            <label>Detail Produk</label>
            <textarea name="detail" class="form-control list-textarea" rows="5"></textarea>
        </div>

        <div class="form-group">
            <label>Cara Pembuatan</label>
            <textarea name="cara_pembuatan" class="form-control" rows="5"></textarea>
        </div>

        <!-- Video Upload Section -->
        <div class="form-group">
            <label>Video Cara Pembuatan</label>
            <input type="file" name="video_file" class="form-control" accept="video/*">
        </div>

        <!-- Material Photos Upload Section - Updated to match edit_produk.php -->
        <div class="form-group">
            <label>Foto Bahan dan Proses Pembuatan</label>
            <input type="file" name="material_photos[]" class="form-control" accept="image/*" multiple>
            
            <div id="materialPhotosPreview" class="mt-3">
                <!-- Material photos with hotspots will be added here dynamically -->
            </div>
        </div>

        <!-- Alat Editor Section -->
        <div class="alat-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Alat-alat</h3>
            
            <div id="alat-container">
                <div class="alat-item" data-index="0">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Alat</label>
                                <input type="text" class="form-control" name="alat_names[]">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gambar Alat</label>
                                <input type="file" name="alat_images[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Deskripsi Alat</label>
                                <textarea name="alat_descriptions[]" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cara Penggunaan</label>
                                <textarea name="alat_cara_penggunaan[]" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="remove-alat-button" onclick="removeAlatItem(this)">Hapus Alat</button>
                </div>
            </div>
            
            <button type="button" class="add-alat-button" onclick="addAlatItem()">
                <i class="glyphicon glyphicon-plus"></i> Tambah Alat
            </button>
        </div>

        <!-- Bahan Editor Section -->
        <div class="bahan-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Bahan-bahan</h3>
            
            <div id="bahan-container">
                <div class="bahan-item" data-index="0">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Bahan</label>
                                <input type="text" class="form-control" name="bahan_names[]">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gambar Bahan</label>
                                <input type="file" name="bahan_images[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Deskripsi Bahan</label>
                                <textarea name="bahan_descriptions[]" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cara Penggunaan</label>
                                <textarea name="bahan_cara_penggunaan[]" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="remove-bahan-button" onclick="removeBahanItem(this)">Hapus Bahan</button>
                </div>
            </div>
            
            <button type="button" class="add-bahan-button" onclick="addBahanItem()">
                <i class="glyphicon glyphicon-plus"></i> Tambah Bahan
            </button>
        </div>

        <div class="form-group">
            <label>Sejarah Produk</label>
            <textarea name="sejarah" class="form-control" rows="5"></textarea>
        </div>

        <!-- Hotspot Editor Section -->
        <div class="hotspot-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Hotspot Produk</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="image-hotspot-container">
                        <img src="../image/no-image.jpg" width="400" id="productImage">
                        <div id="hotspotContainer"></div>
                    </div>
                    <p class="help-block">Klik pada gambar untuk menambah posisi hotspot (setelah upload gambar)</p>
                </div>
                
                <div class="col-md-6">
                    <div id="hotspotForm" class="hotspot-form">
                        <input type="hidden" name="hotspot_action" value="save">
                        <input type="hidden" name="kode_produk" value="<?= $format; ?>">
                        <input type="hidden" name="x_position" id="xPosition">
                        <input type="hidden" name="y_position" id="yPosition">
                        
                        <div class="form-group">
                            <label>Judul Hotspot</label>
                            <input type="text" name="judul" class="form-control" id="hotspotTitle">
                        </div>
                        
                        <div class="form-group">
                            <label>Deskripsi Hotspot</label>
                            <textarea name="deskripsi" class="form-control" rows="3" id="hotspotDesc"></textarea>
                        </div>
                        
                        <button type="button" class="btn btn-primary" onclick="saveHotspot()">Simpan Hotspot</button>
                        <button type="button" class="btn btn-default" onclick="resetHotspotForm()">Batal</button>
                    </div>
                    
                    <div class="hotspot-list">
                        <h4>Daftar Hotspot</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Posisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="hotspotList">
                                <!-- Hotspots will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="button-section">
            <div class="row">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="glyphicon glyphicon-plus-sign"></i> Tambah Produk
                    </button>
                </div>
                <div class="col-md-6">
                    <a href="m_produk.php" class="btn btn-danger btn-block">Batal</a>
                </div>
            </div>
        </div>

        <!-- Hidden fields for hotspots -->
        <div id="hiddenHotspots"></div>
    </form>
</div>

<script>
// Global variables
let hotspots = [];
let materialHotspots = {};

// Alat Functions
let alatIndex = 1;
function addAlatItem() {
    const container = document.getElementById('alat-container');
    const newItem = document.createElement('div');
    newItem.className = 'alat-item';
    newItem.dataset.index = alatIndex++;
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nama Alat</label>
                    <input type="text" class="form-control" name="alat_names[]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Gambar Alat</label>
                    <input type="file" name="alat_images[]" class="form-control" accept="image/*" multiple>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Deskripsi Alat</label>
                    <textarea name="alat_descriptions[]" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Cara Penggunaan</label>
                    <textarea name="alat_cara_penggunaan[]" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        <button type="button" class="remove-alat-button" onclick="removeAlatItem(this)">Hapus Alat</button>
    `;
    container.appendChild(newItem);
}

function removeAlatItem(button) {
    if (confirm('Yakin hapus alat ini?')) {
        button.closest('.alat-item').remove();
    }
}

// Bahan Functions
let bahanIndex = 1;
function addBahanItem() {
    const container = document.getElementById('bahan-container');
    const newItem = document.createElement('div');
    newItem.className = 'bahan-item';
    newItem.dataset.index = bahanIndex++;
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nama Bahan</label>
                    <input type="text" class="form-control" name="bahan_names[]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Gambar Bahan</label>
                    <input type="file" name="bahan_images[]" class="form-control" accept="image/*" multiple>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Deskripsi Bahan</label>
                    <textarea name="bahan_descriptions[]" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Cara Penggunaan</label>
                    <textarea name="bahan_cara_penggunaan[]" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        <button type="button" class="remove-bahan-button" onclick="removeBahanItem(this)">Hapus Bahan</button>
    `;
    container.appendChild(newItem);
}

function removeBahanItem(button) {
    if (confirm('Yakin hapus bahan ini?')) {
        button.closest('.bahan-item').remove();
    }
}

// Main product image preview
document.querySelector('input[name="files"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('productImage').src = event.target.result;
            document.getElementById('previewImage').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Material photos with hotspots - matches edit_produk.php
document.querySelector('input[name="material_photos[]"]').addEventListener('change', function(e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('materialPhotosPreview');
    previewContainer.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const div = document.createElement('div');
            div.className = 'row mb-4';
            div.innerHTML = `
                <div class="col-md-6">
                    <div class="image-hotspot-container">
                        <img src="${event.target.result}" width="400" class="material-photo" data-index="${index}">
                        <div class="material-hotspot-container" id="materialHotspotContainer${index}"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="material-hotspot-form" id="materialHotspotForm${index}">
                        <input type="hidden" name="kode_produk" value="<?= $format; ?>">
                        <input type="hidden" name="material_photo_index" value="${index}">
                        <input type="hidden" name="x_position" id="materialXPosition${index}">
                        <input type="hidden" name="y_position" id="materialYPosition${index}">
                        <input type="hidden" name="hotspot_id" id="materialHotspotId${index}">
                        <div class="form-group">
                            <label>Judul Hotspot</label>
                            <input type="text" name="judul" class="form-control" id="materialHotspotTitle${index}">
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Hotspot</label>
                            <textarea name="deskripsi" class="form-control" rows="3" id="materialHotspotDesc${index}"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="saveMaterialHotspot(${index})">Simpan Hotspot</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="resetMaterialHotspotForm(${index})">Batal</button>
                    </div>
                    <div class="material-hotspot-list mt-3">
                        <h5>Daftar Hotspot</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Posisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="materialHotspotList${index}"></tbody>
                        </table>
                    </div>
                </div>
            `;
            previewContainer.appendChild(div);
            
            // Initialize material hotspots array for this photo
            materialHotspots[index] = [];
            
            // Add click event for hotspot creation
            const photo = div.querySelector('.material-photo');
            photo.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const xPercent = Math.round((x / rect.width) * 100);
                const yPercent = Math.round((y / rect.height) * 100);
                
                document.getElementById(`materialXPosition${index}`).value = xPercent + '%';
                document.getElementById(`materialYPosition${index}`).value = yPercent + '%';
                
                // Show temporary marker
                const container = document.getElementById(`materialHotspotContainer${index}`);
                container.innerHTML = '';
                const marker = document.createElement('div');
                marker.className = 'material-hotspot-marker';
                marker.style.left = xPercent + '%';
                marker.style.top = yPercent + '%';
                container.appendChild(marker);
                
                // Show the form
                document.getElementById(`materialHotspotForm${index}`).style.display = 'block';
            });
        };
        reader.readAsDataURL(file);
    });
});

// Save material hotspot
function saveMaterialHotspot(index) {
    const title = document.getElementById(`materialHotspotTitle${index}`).value;
    const desc = document.getElementById(`materialHotspotDesc${index}`).value;
    const xPos = document.getElementById(`materialXPosition${index}`).value;
    const yPos = document.getElementById(`materialYPosition${index}`).value;
    
    if (!title || !xPos || !yPos) {
        alert('Judul dan posisi hotspot harus diisi!');
        return;
    }
    
    // Add to material hotspots array
    const hotspotId = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    
    materialHotspots[index].push({
        id: hotspotId,
        judul: title,
        deskripsi: desc,
        x_position: xPos,
        y_position: yPos
    });
    
    // Add marker to image
    const container = document.getElementById(`materialHotspotContainer${index}`);
    const marker = document.createElement('div');
    marker.className = 'material-hotspot-marker';
    marker.style.left = xPos;
    marker.style.top = yPos;
    marker.dataset.hotspotId = hotspotId;
    marker.dataset.title = title;
    marker.dataset.desc = desc;
    marker.onclick = function() { showMaterialHotspotInfo(this); };
    container.appendChild(marker);
    
    // Add to hotspot list
    const hotspotList = document.getElementById(`materialHotspotList${index}`);
    const row = document.createElement('tr');
    row.id = `materialHotspotRow_${hotspotId}`;
    row.innerHTML = `
        <td>${title}</td>
        <td>${xPos}, ${yPos}</td>
        <td>
            <button class="btn btn-xs btn-warning" onclick="editMaterialHotspot(${index}, '${hotspotId}', '${title.replace(/'/g, "\\'")}', '${desc.replace(/'/g, "\\'")}', '${xPos}', '${yPos}')">Edit</button>
            <button class="btn btn-xs btn-danger" onclick="deleteMaterialHotspot(${index}, '${hotspotId}')">Hapus</button>
        </td>
    `;
    hotspotList.appendChild(row);
    
    // Reset form
    resetMaterialHotspotForm(index);
    
    // Update hidden fields
    updateHotspotHiddenFields();
}

// Edit material hotspot
function editMaterialHotspot(photoIndex, hotspotId, title, desc, xPos, yPos) {
    document.getElementById(`materialHotspotId${photoIndex}`).value = hotspotId;
    document.getElementById(`materialHotspotTitle${photoIndex}`).value = title;
    document.getElementById(`materialHotspotDesc${photoIndex}`).value = desc;
    document.getElementById(`materialXPosition${photoIndex}`).value = xPos;
    document.getElementById(`materialYPosition${photoIndex}`).value = yPos;
    
    // Show marker
    const container = document.getElementById(`materialHotspotContainer${photoIndex}`);
    container.innerHTML = '';
    const marker = document.createElement('div');
    marker.className = 'material-hotspot-marker';
    marker.style.left = xPos;
    marker.style.top = yPos;
    container.appendChild(marker);
    
    // Show form
    document.getElementById(`materialHotspotForm${photoIndex}`).style.display = 'block';
}

// Delete material hotspot
function deleteMaterialHotspot(photoIndex, hotspotId) {
    if (confirm('Yakin hapus hotspot ini?')) {
        // Remove from array
        materialHotspots[photoIndex] = materialHotspots[photoIndex].filter(h => h.id !== hotspotId);
        
        // Remove marker
        const marker = document.querySelector(`#materialHotspotContainer${photoIndex} .material-hotspot-marker[data-hotspot-id="${hotspotId}"]`);
        if (marker) {
            marker.remove();
        }
        
        // Remove from list
        const row = document.getElementById(`materialHotspotRow_${hotspotId}`);
        if (row) {
            row.remove();
        }
        
        // Update hidden fields
        updateHotspotHiddenFields();
    }
}

// Reset material hotspot form
function resetMaterialHotspotForm(index) {
    document.getElementById(`materialHotspotId${index}`).value = '';
    document.getElementById(`materialHotspotTitle${index}`).value = '';
    document.getElementById(`materialHotspotDesc${index}`).value = '';
    document.getElementById(`materialXPosition${index}`).value = '';
    document.getElementById(`materialYPosition${index}`).value = '';
    document.getElementById(`materialHotspotForm${index}`).style.display = 'none';
    document.getElementById(`materialHotspotContainer${index}`).innerHTML = '';
}

// Show material hotspot info
function showMaterialHotspotInfo(marker) {
    // Remove existing info if any
    const existingInfo = document.querySelector('.material-hotspot-info');
    if (existingInfo) {
        existingInfo.remove();
    }
    
    // Create info element
    const info = document.createElement('div');
    info.className = 'material-hotspot-info';
    info.innerHTML = `
        <h4>${marker.dataset.title}</h4>
        <p>${marker.dataset.desc}</p>
    `;
    
    // Position it near the marker
    const rect = marker.getBoundingClientRect();
    info.style.position = 'absolute';
    info.style.left = (rect.left + 20) + 'px';
    info.style.top = (rect.top - 10) + 'px';
    info.style.backgroundColor = 'white';
    info.style.padding = '10px';
    info.style.borderRadius = '5px';
    info.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
    info.style.zIndex = '100';
    
    // Add to container
    marker.parentElement.appendChild(info);
    
    // Close when clicking outside
    setTimeout(() => {
        document.addEventListener('click', function closeInfo(e) {
            if (!info.contains(e.target) && e.target !== marker) {
                info.remove();
                document.removeEventListener('click', closeInfo);
            }
        });
    }, 100);
}

// Main product hotspot functions
document.getElementById('productImage').addEventListener('click', function(e) {
    const rect = this.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const xPercent = Math.round((x / rect.width) * 100);
    const yPercent = Math.round((y / rect.height) * 100);
    
    document.getElementById('xPosition').value = xPercent + '%';
    document.getElementById('yPosition').value = yPercent + '%';
    
    showHotspotMarker(xPercent + '%', yPercent + '%');
});

function showHotspotMarker(x, y) {
    const container = document.getElementById('hotspotContainer');
    container.innerHTML = '';
    const marker = document.createElement('div');
    marker.className = 'hotspot-marker';
    marker.style.left = x;
    marker.style.top = y;
    container.appendChild(marker);
}

function resetHotspotForm() {
    document.getElementById('hotspotTitle').value = '';
    document.getElementById('hotspotDesc').value = '';
    document.getElementById('xPosition').value = '';
    document.getElementById('yPosition').value = '';
    document.getElementById('hotspotContainer').innerHTML = '';
}

function saveHotspot() {
    const title = document.getElementById('hotspotTitle').value;
    const desc = document.getElementById('hotspotDesc').value;
    const xPos = document.getElementById('xPosition').value;
    const yPos = document.getElementById('yPosition').value;
    
    if (!title || !desc || !xPos || !yPos) {
        alert('Semua field hotspot harus diisi!');
        return;
    }
    
    // Add to hotspots array
    const hotspotId = 'main_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    
    hotspots.push({
        id: hotspotId,
        judul: title,
        deskripsi: desc,
        x_position: xPos,
        y_position: yPos
    });
    
    // Add to hotspot list
    const hotspotList = document.getElementById('hotspotList');
    const row = document.createElement('tr');
    row.id = `hotspotRow_${hotspotId}`;
    row.innerHTML = `
        <td>${title}</td>
        <td>${xPos}, ${yPos}</td>
        <td>
            <button class="btn btn-xs btn-warning" onclick="editMainHotspot('${hotspotId}', '${title.replace(/'/g, "\\'")}', '${desc.replace(/'/g, "\\'")}', '${xPos}', '${yPos}')">Edit</button>
            <button class="btn btn-xs btn-danger" onclick="deleteMainHotspot('${hotspotId}')">Hapus</button>
        </td>
    `;
    hotspotList.appendChild(row);
    
    // Reset form
    resetHotspotForm();
    
    // Update hidden fields
    updateHotspotHiddenFields();
}

function editMainHotspot(hotspotId, title, desc, xPos, yPos) {
    document.getElementById('hotspotTitle').value = title;
    document.getElementById('hotspotDesc').value = desc;
    document.getElementById('xPosition').value = xPos;
    document.getElementById('yPosition').value = yPos;
    
    // Show marker
    showHotspotMarker(xPos, yPos);
    
    // Remove the old hotspot
    deleteMainHotspot(hotspotId, false);
}

function deleteMainHotspot(hotspotId, confirmDelete = true) {
    if (confirmDelete && !confirm('Yakin hapus hotspot ini?')) {
        return;
    }
    
    // Remove from array
    hotspots = hotspots.filter(h => h.id !== hotspotId);
    
    // Remove from list
    const row = document.getElementById(`hotspotRow_${hotspotId}`);
    if (row) {
        row.remove();
    }
    
    // Update hidden fields
    updateHotspotHiddenFields();
}

// Update hidden form fields with all hotspot data
function updateHotspotHiddenFields() {
    const hiddenFields = document.getElementById('hiddenHotspots');
    hiddenFields.innerHTML = '';
    
    // Add main product hotspots
    hotspots.forEach((hotspot, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `hotspots[${index}][judul]`;
        input.value = hotspot.judul;
        hiddenFields.appendChild(input);
        
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = `hotspots[${index}][deskripsi]`;
        input2.value = hotspot.deskripsi;
        hiddenFields.appendChild(input2);
        
        const input3 = document.createElement('input');
        input3.type = 'hidden';
        input3.name = `hotspots[${index}][x_position]`;
        input3.value = hotspot.x_position;
        hiddenFields.appendChild(input3);
        
        const input4 = document.createElement('input');
        input4.type = 'hidden';
        input4.name = `hotspots[${index}][y_position]`;
        input4.value = hotspot.y_position;
        hiddenFields.appendChild(input4);
    });
    
    // Add material hotspots
    Object.keys(materialHotspots).forEach(photoIndex => {
        materialHotspots[photoIndex].forEach((hotspot, hotspotIndex) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `material_hotspots[${photoIndex}][${hotspotIndex}][judul]`;
            input.value = hotspot.judul;
            hiddenFields.appendChild(input);
            
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = `material_hotspots[${photoIndex}][${hotspotIndex}][deskripsi]`;
            input2.value = hotspot.deskripsi;
            hiddenFields.appendChild(input2);
            
            const input3 = document.createElement('input');
            input3.type = 'hidden';
            input3.name = `material_hotspots[${photoIndex}][${hotspotIndex}][x_position]`;
            input3.value = hotspot.x_position;
            hiddenFields.appendChild(input3);
            
            const input4 = document.createElement('input');
            input4.type = 'hidden';
            input4.name = `material_hotspots[${photoIndex}][${hotspotIndex}][y_position]`;
            input4.value = hotspot.y_position;
            hiddenFields.appendChild(input4);
        });
    });
}

// Fungsi untuk menampilkan informasi hotspot saat diklik
function showHotspotInfo(marker) {
    // Hapus info hotspot yang sudah ada
    const existingInfo = document.querySelector('.hotspot-info');
    if (existingInfo) {
        existingInfo.remove();
    }
    
    // Buat info hotspot baru
    const info = document.createElement('div');
    info.className = 'hotspot-info';
    info.innerHTML = `
        <h4>${marker.dataset.title}</h4>
        <p>${marker.dataset.desc}</p>
    `;
    
    // Posisikan info di sebelah marker
    const markerRect = marker.getBoundingClientRect();
    const containerRect = marker.parentElement.getBoundingClientRect();
    
    info.style.left = (markerRect.left - containerRect.left + 20) + 'px';
    info.style.top = (markerRect.top - containerRect.top - 10) + 'px';
    
    // Tambahkan info ke container
    marker.parentElement.appendChild(info);
    
    // Tampilkan info
    info.style.display = 'block';
    
    // Tutup info saat klik di luar
    document.addEventListener('click', function closeInfo(e) {
        if (!info.contains(e.target) && e.target !== marker) {
            info.remove();
            document.removeEventListener('click', closeInfo);
        }
    });
}
</script>

<style>
    .hotspot-marker {
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: rgba(255, 80, 80, 0.9);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        border: 2px solid white;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
    }
    
    .hotspot-marker:hover,
    .hotspot-marker.active {
        transform: translate(-50%, -50%) scale(1.3);
        background-color: rgba(255, 50, 50, 1);
    }
    
    .hotspot-info {
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        position: absolute;
        z-index: 20;
        max-width: 200px;
        display: none;
    }
    
    .image-hotspot-container {
        position: relative;
        display: inline-block;
    }
    
    .material-photo-container {
        margin: 15px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
    }
    
    .alat-item, .bahan-item {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
    }
    
    .remove-alat-button, .remove-bahan-button {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        margin-top: 10px;
    }
    
    .add-alat-button, .add-bahan-button {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        margin: 15px 0;
    }
    
    /* Style untuk hotspot dan material hotspot form */
    .hotspot-form, .material-hotspot-form {
        background: #f2f2f2;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    /* Animasi untuk efek hover pada tombol dan elemen lainnya */
    button, .btn, .form-control, .photo-preview {
        transition: all 0.3s ease;
    }
    
    button:hover, .btn:hover {
        transform: translateY(-2px);
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 134, 128, 0.25);
        border-color: #ff8680;
    }
    
    .photo-preview:hover {
        transform: scale(1.03);
    }
</style>

<?php include 'footer.php'; ?>