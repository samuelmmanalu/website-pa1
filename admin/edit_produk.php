<?php 
include 'header.php';

// Ambil kode produk dari URL
$kode_produk = $_GET['kode'] ?? '';

if (empty($kode_produk)) {
    echo "<script>alert('Kode produk tidak ditemukan!'); window.location='m_produk.php';</script>";
    exit;
}

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form produk
    if (isset($_POST['kode'])) {
        $kode = $_POST['kode'];
        $nama = $_POST['nama'];
        $kategori = $_POST['kategori'];
        $desk = $_POST['desk'];
        $panjang = $_POST['panjang'];
        $lebar = $_POST['lebar'];
        
        // Format textarea list menjadi baris-baris
        $detail = implode("\n", array_filter(array_map('trim', explode("\n", $_POST['detail']))));
        $cara_pembuatan = $_POST['cara_pembuatan'];
        $sejarah = $_POST['sejarah'];

        // Handle file upload produk utama
        if (!empty($_FILES['files']['name'])) {
            $target_dir = "../image/produk/";
            $file_name = basename($_FILES["files"]["name"]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["files"]["tmp_name"], $target_file)) {
                $image = $file_name;
            } else {
                echo "<script>alert('Gagal upload gambar!');</script>";
                $image = '';
            }
        } else {
            $image = '';
        }

        // Handle video upload
        if (!empty($_FILES['video_file']['name'])) {
            $target_dir = "../video/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $video_name = time() . '_' . basename($_FILES["video_file"]["name"]);
            $target_file = $target_dir . $video_name;
            
            if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file)) {
                // Delete existing video for this product
                mysqli_query($conn, "DELETE FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'video' AND jenis = 'cara_pembuatan'");
                
                // Insert new video
                $insert_video = "INSERT INTO produk_media (kode_produk, nama_file, tipe, jenis) 
                               VALUES ('$kode', '$video_name', 'video', 'cara_pembuatan')";
                mysqli_query($conn, $insert_video);
            }
        }

        // Handle material photos upload
        if (!empty($_FILES['material_photos']['name'][0])) {
            $target_dir = "../image/materials/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Get the current maximum urutan value
            $max_urutan_query = mysqli_query($conn, "SELECT MAX(urutan) as max_urutan FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'gambar' AND jenis = 'cara_pembuatan'");
            $max_urutan = mysqli_fetch_assoc($max_urutan_query);
            $start_urutan = ($max_urutan['max_urutan'] ?? 0) + 1;
            
            foreach ($_FILES['material_photos']['tmp_name'] as $key => $tmp_name) {
                if (!empty($_FILES['material_photos']['name'][$key])) {
                    $photo_name = time() . '_' . $key . '_' . basename($_FILES['material_photos']['name'][$key]);
                    $target_file = $target_dir . $photo_name;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $urutan = $start_urutan + $key;
                        $description = $_POST['new_photo_descriptions'][$key] ?? '';
                        $insert_photo = "INSERT INTO produk_media (kode_produk, nama_file, tipe, jenis, urutan, keterangan) 
                                       VALUES ('$kode', '$photo_name', 'gambar', 'cara_pembuatan', $urutan, '$description')";
                        mysqli_query($conn, $insert_photo);
                    }
                }
            }
        }

        // Update existing photo descriptions
        if (!empty($_POST['photo_descriptions'])) {
            foreach ($_POST['photo_descriptions'] as $photo_id => $description) {
                $photo_id = mysqli_real_escape_string($conn, $photo_id);
                $description = mysqli_real_escape_string($conn, $description);
                mysqli_query($conn, "UPDATE produk_media SET keterangan = '$description' WHERE id = '$photo_id'");
            }
        }

        // Update produk
        $update_query = "UPDATE produk SET 
                        nama = '$nama',
                        kategori = '$kategori',
                        deskripsi = '$desk',
                        panjang = '$panjang',
                        lebar = '$lebar',
                        detail = '$detail',
                        cara_pembuatan = '$cara_pembuatan',
                        sejarah = '$sejarah'";

        if (!empty($image)) {
            $update_query .= ", image = '$image'";
        }

        $update_query .= " WHERE kode_produk = '$kode'";

        $update = mysqli_query($conn, $update_query);

        if (!$update) {
            echo "<script>alert('Gagal update produk: " . mysqli_error($conn) . "');</script>";
        }
    }
    
    // Handle form alat (tools)
    $delete_alat_query = "DELETE FROM produk_alat WHERE kode_produk = '$kode_produk'";
    mysqli_query($conn, $delete_alat_query);
    
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
                
                // Get existing images and remove deleted ones
                if (isset($_POST['existing_alat_images'][$index]) && !empty($_POST['existing_alat_images'][$index])) {
                    $existing_images = explode(',', $_POST['existing_alat_images'][$index]);
                    // Remove any images that were deleted
                    foreach ($existing_images as $key => $img) {
                        if (isset($_POST['removed_alat_images'][$index]) && 
                            in_array($img, explode(',', $_POST['removed_alat_images'][$index]))) {
                            unset($existing_images[$key]);
                            // Delete the file from server
                            $file_path = "../image/alat/" . $img;
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }
                    $image_names = array_merge($existing_images, $image_names);
                }
                
                $image_name = implode(',', $image_names);
            }
            elseif (isset($_POST['existing_alat_images'][$index]) && !empty($_POST['existing_alat_images'][$index])) {
                $existing_images = explode(',', $_POST['existing_alat_images'][$index]);
                // Remove any images that were deleted
                foreach ($existing_images as $key => $img) {
                    if (isset($_POST['removed_alat_images'][$index]) && 
                        in_array($img, explode(',', $_POST['removed_alat_images'][$index]))) {
                        unset($existing_images[$key]);
                        // Delete the file from server
                        $file_path = "../image/alat/" . $img;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                $image_name = implode(',', $existing_images);
            }
            else {
                $image_name = '';
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
    
    // Handle form bahan (materials)
    $delete_bahan_query = "DELETE FROM produk_bahan WHERE kode_produk = '$kode_produk'";
    mysqli_query($conn, $delete_bahan_query);
    
    if (isset($_POST['bahan_names'])) {
        $jumlah_benang = $_POST['jumlah_benang'] ?? '';
        $jumlah_benang = mysqli_real_escape_string($conn, $jumlah_benang);
        
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
                
                // Get existing images and remove deleted ones
                if (isset($_POST['existing_bahan_images'][$index]) && !empty($_POST['existing_bahan_images'][$index])) {
                    $existing_images = explode(',', $_POST['existing_bahan_images'][$index]);
                    // Remove any images that were deleted
                    foreach ($existing_images as $key => $img) {
                        if (isset($_POST['removed_bahan_images'][$index]) && 
                            in_array($img, explode(',', $_POST['removed_bahan_images'][$index]))) {
                            unset($existing_images[$key]);
                            // Delete the file from server
                            $file_path = "../image/bahan/" . $img;
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }
                    $image_names = array_merge($existing_images, $image_names);
                }
                
                $image_name = implode(',', $image_names);
            }
            elseif (isset($_POST['existing_bahan_images'][$index]) && !empty($_POST['existing_bahan_images'][$index])) {
                $existing_images = explode(',', $_POST['existing_bahan_images'][$index]);
                // Remove any images that were deleted
                foreach ($existing_images as $key => $img) {
                    if (isset($_POST['removed_bahan_images'][$index]) && 
                        in_array($img, explode(',', $_POST['removed_bahan_images'][$index]))) {
                        unset($existing_images[$key]);
                        // Delete the file from server
                        $file_path = "../image/bahan/" . $img;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                $image_name = implode(',', $existing_images);
            }
            else {
                $image_name = '';
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
    
    // Handle form hotspot
    if (isset($_POST['hotspot_action'])) {
        $action = $_POST['hotspot_action'];
        $kode_produk = mysqli_real_escape_string($conn, $_POST['kode_produk']);
        
        if ($action === 'save') {
            $judul = mysqli_real_escape_string($conn, $_POST['judul']);
            $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
            $x_position = mysqli_real_escape_string($conn, $_POST['x_position']);
            $y_position = mysqli_real_escape_string($conn, $_POST['y_position']);
            
            // Cek apakah ini hotspot untuk gambar utama atau gambar cara pembuatan
            if (isset($_POST['material_photo_id'])) {
                // Hotspot untuk gambar cara pembuatan
                $material_photo_id = mysqli_real_escape_string($conn, $_POST['material_photo_id']);
                if (!empty($_POST['hotspot_id'])) {
                    $hotspot_id = mysqli_real_escape_string($conn, $_POST['hotspot_id']);
                    $query = "UPDATE produk_hotspots 
                              SET judul = '$judul', deskripsi = '$deskripsi', 
                                  x_position = '$x_position', y_position = '$y_position'
                              WHERE id = '$hotspot_id' AND material_photo_id = '$material_photo_id'";
                } else {
                    $query = "INSERT INTO produk_hotspots 
                              (kode_produk, material_photo_id, judul, deskripsi, x_position, y_position, jenis) 
                              VALUES 
                              ('$kode_produk', '$material_photo_id', '$judul', '$deskripsi', '$x_position', '$y_position', 'material')";
                }
            } else {
                // Hotspot untuk gambar utama
                if (!empty($_POST['hotspot_id'])) {
                    $hotspot_id = mysqli_real_escape_string($conn, $_POST['hotspot_id']);
                    $query = "UPDATE produk_hotspots 
                              SET judul = '$judul', deskripsi = '$deskripsi', 
                                  x_position = '$x_position', y_position = '$y_position'
                              WHERE id = '$hotspot_id' AND material_photo_id IS NULL";
                } else {
                    $query = "INSERT INTO produk_hotspots 
                              (kode_produk, judul, deskripsi, x_position, y_position, jenis) 
                              VALUES 
                              ('$kode_produk', '$judul', '$deskripsi', '$x_position', '$y_position', 'utama')";
                }
            }
            
            mysqli_query($conn, $query);
        } 
        elseif ($action === 'delete' && isset($_POST['hotspot_id'])) {
            $hotspot_id = mysqli_real_escape_string($conn, $_POST['hotspot_id']);
            
            // Cek apakah ini hotspot untuk gambar cara pembuatan
            $check_query = mysqli_query($conn, "SELECT material_photo_id, jenis FROM produk_hotspots WHERE id = '$hotspot_id'");
            $check_result = mysqli_fetch_assoc($check_query);
            
            if ($check_result && $check_result['material_photo_id']) {
                // Hotspot untuk gambar cara pembuatan
                $query = "DELETE FROM produk_hotspots WHERE id = '$hotspot_id' AND jenis = 'material'";
            } else {
                // Hotspot untuk gambar utama
                $query = "DELETE FROM produk_hotspots WHERE id = '$hotspot_id' AND jenis = 'utama'";
            }
            
            mysqli_query($conn, $query);
        }
    }
    
    echo "<script>alert('Perubahan berhasil disimpan!'); window.location='edit_produk.php?kode=$kode_produk';</script>";
}

// Ambil data produk untuk edit
$query = mysqli_query($conn, "SELECT * FROM produk WHERE kode_produk = '$kode_produk'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='m_produk.php';</script>";
    exit;
}

// Ambil data alat yang sudah ada
$alat_items = [];
$alat_query = mysqli_query($conn, "SELECT * FROM produk_alat WHERE kode_produk = '$kode_produk'");
while ($alat = mysqli_fetch_assoc($alat_query)) {
    $alat_items[] = $alat;
}

// Ambil data bahan yang sudah ada
$bahan_items = [];
$bahan_query = mysqli_query($conn, "SELECT * FROM produk_bahan WHERE kode_produk = '$kode_produk'");
while ($bahan = mysqli_fetch_assoc($bahan_query)) {
    $bahan_items[] = $bahan;
}

// Ambil data hotspot yang sudah ada
$hotspots = [];
$hotspots_query = mysqli_query($conn, "SELECT * FROM produk_hotspots WHERE kode_produk = '$kode_produk'");
while ($hotspot = mysqli_fetch_assoc($hotspots_query)) {
    $hotspots[] = $hotspot;
}

// Handler AJAX simpan hotspot material
if (isset($_POST['ajax_material_hotspot'])) {
    $kode_produk = mysqli_real_escape_string($conn, $_POST['kode_produk']);
    $material_photo_id = mysqli_real_escape_string($conn, $_POST['material_photo_id']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $x_position = mysqli_real_escape_string($conn, $_POST['x_position']);
    $y_position = mysqli_real_escape_string($conn, $_POST['y_position']);

    $query = "INSERT INTO produk_material_hotspots (kode_produk, material_photo_id, x_position, y_position, judul, deskripsi)
              VALUES ('$kode_produk', '$material_photo_id', '$x_position', '$y_position', '$judul', '$deskripsi')";
    
    $result = mysqli_query($conn, $query);
    if ($result) {
        $hotspot_id = mysqli_insert_id($conn);
        echo json_encode(['status' => 'success', 'id' => $hotspot_id]);
    } else {
        echo json_encode(['status' => 'success', 'id' => 0]);
    }
    exit;
}

// Handler AJAX hapus hotspot material
if (isset($_POST['ajax_delete_material_hotspot'])) {
    $hotspot_id = mysqli_real_escape_string($conn, $_POST['hotspot_id']);
    $query = "DELETE FROM produk_material_hotspots WHERE id = '$hotspot_id'";
    mysqli_query($conn, $query);
    echo 'success';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
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
    </style>
</head>
<body>
<div class="container">
    <h2 style="width: 100%; border-bottom: 4px solid gray"><b>Edit Produk</b></h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Upload gambar produk utama -->
        <div class="form-group">
            <label for="exampleInputFile">
                <img src="../image/produk/<?= $data['image']; ?>" width="100">
            </label>
            <input type="file" id="exampleInputFile" name="files">
            <p class="help-block">Pilih Gambar untuk Produk</p>
        </div>

        <!-- Informasi dasar produk -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail1">Kode Produk</label>
                    <input type="text" class="form-control" disabled value="<?= $data['kode_produk']; ?>">
                    <input type="hidden" name="kode" value="<?= $data['kode_produk']; ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" class="form-control" name="nama" value="<?= $data['nama']; ?>">
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
                    <select class="form-control" name="kategori">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Modern" <?= ($data['kategori'] == 'Modern') ? 'selected' : ''; ?>>Modern</option>
                        <option value="Tradisional" <?= ($data['kategori'] == 'Tradisional') ? 'selected' : ''; ?>>Tradisional</option>
                        <option value="Lainnya" <?= ($data['kategori'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Panjang (cm)</label>
                    <input type="number" step="0.1" class="form-control" name="panjang" value="<?= $data['panjang'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Lebar (cm)</label>
                    <input type="number" step="0.1" class="form-control" name="lebar" value="<?= $data['lebar'] ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="desk" class="form-control" rows="5"><?= htmlspecialchars(trim($data['deskripsi'])); ?></textarea>
        </div>

        <div class="form-group">
            <label>Jumlah Benang yang Dibutuhkan</label>
            <input type="text" class="form-control" name="jumlah_benang" value="<?= htmlspecialchars($bahan_items[0]['jumlah_benang'] ?? '') ?>" placeholder="Contoh: 2 kg">
        </div>

        <div class="form-group">
            <label>Detail Produk</label>
            <textarea name="detail" class="form-control list-textarea" rows="5"><?= htmlspecialchars(trim($data['detail'])); ?></textarea>
        </div>

        <div class="form-group">
            <label>Cara Pembuatan</label>
            <textarea name="cara_pembuatan" class="form-control" rows="5"><?= htmlspecialchars(trim($data['cara_pembuatan'])); ?></textarea>
        </div>

        <!-- Video Upload Section -->
        <div class="form-group">
            <label>Video Cara Pembuatan</label>
            <input type="file" name="video_file" class="form-control" accept="video/*">
            <?php 
            $video_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode_produk' AND tipe = 'video' AND jenis = 'cara_pembuatan'");
            $video = mysqli_fetch_assoc($video_query);
            if ($video): 
            ?>
                <div class="mt-2">
                    <p>Video saat ini: <?= htmlspecialchars($video['nama_file']) ?></p>
                    <video width="320" height="240" controls>
                        <source src="../video/<?= htmlspecialchars($video['nama_file']) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="mt-2">
                        <a href="delete_media.php?id=<?= $video['id'] ?>&kode=<?= $kode_produk ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus video ini?')">
                            <i class="fas fa-trash"></i> Hapus Video
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Material Photos Upload Section -->
        <div class="form-group">
            <label>Foto Bahan dan Proses Pembuatan</label>
            <input type="file" name="material_photos[]" class="form-control" accept="image/*" multiple>
            
            <?php 
            $photos_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode_produk' AND tipe = 'gambar' AND jenis = 'cara_pembuatan' ORDER BY urutan");
            if (mysqli_num_rows($photos_query) > 0): 
            ?>
                <div class="mt-2">
                    <p>Foto saat ini:</p>
                    <?php 
                    $counter = 1;
                    while ($photo = mysqli_fetch_assoc($photos_query)): 
                        // Ambil hotspot khusus untuk foto ini
                        $material_hotspots_query = mysqli_query($conn, "SELECT * FROM produk_material_hotspots WHERE kode_produk = '$kode_produk' AND material_photo_id = '{$photo['id']}'");
                    ?>
                        <div class="row" style="margin-bottom:30px;">
                            <div class="col-md-6">
                                <div class="image-hotspot-container">
                                    <img src="../image/materials/<?= htmlspecialchars($photo['nama_file']) ?>" width="400" class="material-photo" data-id="<?= $photo['id'] ?>">
                                    <div class="material-hotspot-container" id="materialHotspotContainer<?= $photo['id'] ?>">
                                        <?php 
                                        // Reset pointer hasil query
                                        mysqli_data_seek($material_hotspots_query, 0);
                                        while ($hotspot = mysqli_fetch_assoc($material_hotspots_query)): 
                                        ?>
                                            <div class="hotspot-marker" 
                                                 style="left: <?= $hotspot['x_position'] ?>; top: <?= $hotspot['y_position'] ?>;"
                                                 data-title="<?= htmlspecialchars($hotspot['judul']) ?>"
                                                 data-desc="<?= htmlspecialchars($hotspot['deskripsi']) ?>"
                                                 onclick="showHotspotInfo(this)">
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <div class="mt-2">
                                        <a href="delete_media.php?id=<?= $photo['id'] ?>&kode=<?= $kode_produk ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')">
                                            <i class="fas fa-trash"></i> Hapus Gambar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="material-hotspot-form" id="materialHotspotForm<?= $photo['id'] ?>">
                                    <input type="hidden" name="kode_produk" value="<?= $kode_produk ?>">
                                    <input type="hidden" name="material_photo_id" value="<?= $photo['id'] ?>">
                                    <input type="hidden" name="x_position" id="materialXPosition<?= $photo['id'] ?>">
                                    <input type="hidden" name="y_position" id="materialYPosition<?= $photo['id'] ?>">
                                    <input type="hidden" name="hotspot_id" id="materialHotspotId<?= $photo['id'] ?>">
                                    <div class="form-group">
                                        <label>Nama Bahan</label>
                                        <input type="text" name="judul" class="form-control" id="materialHotspotTitle<?= $photo['id'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan Bahan</label>
                                        <textarea name="deskripsi" class="form-control" rows="3" id="materialHotspotDesc<?= $photo['id'] ?>"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="saveMaterialHotspot('<?= $photo['id'] ?>')">Simpan Hotspot</button>
                                    <button type="button" class="btn btn-default" onclick="resetMaterialHotspotForm('<?= $photo['id'] ?>')">Batal</button>
                                </div>
                                <div class="material-hotspot-list">
                                    <h4>Daftar Hotspot</h4>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Judul</th>
                                                <th>Posisi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="materialHotspotList<?= $photo['id'] ?>">
                                            <?php
                                            // Reset pointer hasil query
                                            mysqli_data_seek($material_hotspots_query, 0);
                                            while ($hotspot = mysqli_fetch_assoc($material_hotspots_query)):
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($hotspot['judul']) ?></td>
                                                    <td><?= htmlspecialchars($hotspot['x_position']) ?>, <?= htmlspecialchars($hotspot['y_position']) ?></td>
                                                    <td>
                                                        <button class="btn btn-xs btn-warning" onclick="editMaterialHotspot('<?= $photo['id'] ?>', <?= $hotspot['id'] ?>, '<?= addslashes($hotspot['judul']) ?>', '<?= addslashes($hotspot['deskripsi']) ?>', '<?= $hotspot['x_position'] ?>', '<?= $hotspot['y_position'] ?>')">Edit</button>
                                                        <button class="btn btn-xs btn-danger" onclick="deleteMaterialHotspot(<?= $hotspot['id'] ?>)">Hapus</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php 
                    $counter++;
                    endwhile; 
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tambahkan container preview untuk gambar bahan/proses pembuatan -->
        <div id="material-hotspot-preview"></div>

        <!-- Alat Editor Section -->
        <div class="alat-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Alat-alat</h3>
            
            <div id="alat-container">
                <?php foreach ($alat_items as $index => $alat): ?>
                <div class="alat-item" data-index="<?= $index ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Alat</label>
                                <input type="text" class="form-control" name="alat_names[]" value="<?= htmlspecialchars($alat['nama_alat']) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gambar Alat</label>
                                <?php if (!empty($alat['gambar_alat'])): ?>
                                <div>
                                    <?php 
                                    $images = explode(',', $alat['gambar_alat']);
                                    foreach ($images as $img): 
                                    ?>
                                        <div class="image-container" style="display: inline-block; margin-right: 10px; position: relative;">
                                            <img src="../image/alat/<?= $img ?>" class="alat-image-preview" style="max-width: 100px; max-height: 100px;">
                                            <button type="button" class="btn btn-danger btn-xs" style="position: absolute; top: 0; right: 0; padding: 2px 5px;" 
                                                    onclick="removeImage(this, 'alat', '<?= $img ?>')">
                                                <i class="glyphicon glyphicon-remove"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="existing_alat_images[]" value="<?= $alat['gambar_alat'] ?>">
                                    <input type="hidden" name="removed_alat_images[]" id="removed_alat_images_<?= $index ?>" value="">
                                </div>
                                <?php endif; ?>
                                <input type="file" name="alat_images[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Deskripsi Alat</label>
                                <textarea name="alat_descriptions[]" class="form-control" rows="3"><?= htmlspecialchars($alat['deskripsi']) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cara Penggunaan</label>
                                <textarea name="alat_cara_penggunaan[]" class="form-control" rows="3"><?= htmlspecialchars($alat['cara_penggunaan']) ?></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="remove-alat-button" onclick="removeAlatItem(this)">Hapus Alat</button>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($alat_items)): ?>
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
                <?php endif; ?>
            </div>
            
            <button type="button" class="add-alat-button" onclick="addAlatItem()">
                <i class="glyphicon glyphicon-plus"></i> Tambah Alat
            </button>
        </div>

        <!-- Bahan Editor Section -->
        <div class="bahan-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Bahan-bahan</h3>
            
            <div id="bahan-container">
                <?php foreach ($bahan_items as $index => $bahan): ?>
                <div class="bahan-item" data-index="<?= $index ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nama Bahan</label>
                                <input type="text" class="form-control" name="bahan_names[]" value="<?= htmlspecialchars($bahan['nama_bahan']) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gambar Bahan</label>
                                <?php if (!empty($bahan['gambar_bahan'])): ?>
                                <div>
                                    <?php 
                                    $images = explode(',', $bahan['gambar_bahan']);
                                    foreach ($images as $img): 
                                    ?>
                                        <div class="image-container" style="display: inline-block; margin-right: 10px; position: relative;">
                                            <img src="../image/bahan/<?= $img ?>" class="bahan-image-preview" style="max-width: 100px; max-height: 100px;">
                                            <button type="button" class="btn btn-danger btn-xs" style="position: absolute; top: 0; right: 0; padding: 2px 5px;" 
                                                    onclick="removeImage(this, 'bahan', '<?= $img ?>')">
                                                <i class="glyphicon glyphicon-remove"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="existing_bahan_images[]" value="<?= $bahan['gambar_bahan'] ?>">
                                    <input type="hidden" name="removed_bahan_images[]" id="removed_bahan_images_<?= $index ?>" value="">
                                </div>
                                <?php endif; ?>
                                <input type="file" name="bahan_images[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Deskripsi Bahan</label>
                                <textarea name="bahan_descriptions[]" class="form-control" rows="3"><?= htmlspecialchars($bahan['deskripsi']) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cara Penggunaan</label>
                                <textarea name="bahan_cara_penggunaan[]" class="form-control" rows="3"><?= htmlspecialchars($bahan['cara_penggunaan']) ?></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="remove-bahan-button" onclick="removeBahanItem(this)">Hapus Bahan</button>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($bahan_items)): ?>
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
                <?php endif; ?>
            </div>
            
            <button type="button" class="add-bahan-button" onclick="addBahanItem()">
                <i class="glyphicon glyphicon-plus"></i> Tambah Bahan
            </button>
        </div>

        <div class="form-group">
            <label>Sejarah Produk</label>
            <textarea name="sejarah" class="form-control" rows="5"><?= htmlspecialchars(trim($data['sejarah'])); ?></textarea>
        </div>

        <!-- Hotspot Editor Section -->
        <div class="hotspot-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Hotspot Produk</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="image-hotspot-container">
                        <img src="../image/produk/<?= $data['image']; ?>" width="400" id="productImage">
                        <div id="hotspotContainer"></div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div id="hotspotForm" class="hotspot-form">
                        <input type="hidden" name="hotspot_action" value="save">
                        <input type="hidden" name="kode_produk" value="<?= $kode_produk ?>">
                        <input type="hidden" name="x_position" id="xPosition">
                        <input type="hidden" name="y_position" id="yPosition">
                        <input type="hidden" name="hotspot_id" id="hotspotId">
                        
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
                            <tbody>
                                <?php foreach ($hotspots as $hotspot): ?>
                                <tr>
                                    <td><?= htmlspecialchars($hotspot['judul']) ?></td>
                                    <td><?= htmlspecialchars($hotspot['x_position']) ?>, <?= htmlspecialchars($hotspot['y_position']) ?></td>
                                    <td>
                                        <button class="btn btn-xs btn-warning" onclick="editHotspot(<?= $hotspot['id'] ?>, '<?= addslashes($hotspot['judul']) ?>', '<?= addslashes($hotspot['deskripsi']) ?>', '<?= $hotspot['x_position'] ?>', '<?= $hotspot['y_position'] ?>')">
                                            Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger" onclick="deleteHotspot(<?= $hotspot['id'] ?>)">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="button-section">
            <div class="row">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="glyphicon glyphicon-edit"></i> Simpan Perubahan
                    </button>
                </div>
                <div class="col-md-6">
                    <a href="m_produk.php" class="btn btn-danger btn-block">Batal</a>
                </div>
            </div>
        </div>

        <!-- Tempatkan di sini, sebelum </form> -->
        <div id="hidden-material-hotspots"></div>
    </form>
</div>

<!-- Tempatkan sebelum </form> -->
<div id="hidden-material-hotspots"></div>

<script>
// Alat Functions
let alatIndex = <?= count($alat_items) ?>;
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
let bahanIndex = <?= count($bahan_items) ?>;
function addBahanItem() {
    const bahanContainer = document.getElementById('bahan-container');
    const bahanItem = document.createElement('div');
    bahanItem.className = 'bahan-item';
    bahanItem.innerHTML = `
                <div class="form-group">
                    <label>Nama Bahan</label>
            <input type="text" class="form-control" name="bahan_names[]" placeholder="Masukkan nama bahan">
                </div>
                <div class="form-group">
                    <label>Gambar Bahan</label>
            <input type="file" class="form-control" name="bahan_images[]" accept="image/*" multiple>
                </div>
                <div class="form-group">
                    <label>Deskripsi Bahan</label>
            <textarea class="form-control" name="bahan_descriptions[]" rows="3" placeholder="Masukkan deskripsi bahan"></textarea>
                </div>
                <div class="form-group">
                    <label>Cara Penggunaan</label>
            <textarea class="form-control" name="bahan_cara_penggunaan[]" rows="3" placeholder="Masukkan cara penggunaan bahan"></textarea>
                </div>
        <button type="button" class="btn btn-danger" onclick="removeBahanItem(this)">Hapus Bahan</button>
    `;
    bahanContainer.appendChild(bahanItem);
}

function removeBahanItem(button) {
    if (confirm('Yakin hapus bahan ini?')) {
        button.closest('.bahan-item').remove();
    }
}

// Hotspot Functions
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

function editHotspot(id, title, desc, x, y) {
    document.getElementById('hotspotId').value = id;
    document.getElementById('hotspotTitle').value = title;
    document.getElementById('hotspotDesc').value = desc;
    document.getElementById('xPosition').value = x;
    document.getElementById('yPosition').value = y;
    showHotspotMarker(x, y);
}

function resetHotspotForm() {
    document.getElementById('hotspotId').value = '';
    document.getElementById('hotspotTitle').value = '';
    document.getElementById('hotspotDesc').value = '';
    document.getElementById('xPosition').value = '';
    document.getElementById('yPosition').value = '';
    document.getElementById('hotspotContainer').innerHTML = '';
}

function deleteHotspot(hotspotId) {
    if (confirm('Yakin hapus hotspot ini?')) {
        document.getElementById('hotspotId').value = hotspotId;
        document.querySelector('#hotspotForm [name="hotspot_action"]').value = 'delete';
        document.forms[0].submit();
    }
}

function saveHotspot() {
    const title = document.getElementById('hotspotTitle').value;
    const xPos = document.getElementById('xPosition').value;
    const yPos = document.getElementById('yPosition').value;
    
    if (!title || !xPos || !yPos) {
        alert('Judul dan posisi hotspot harus diisi!');
        return;
    }
    
    document.forms[0].submit();
}

// Tampilkan hotspot yang sudah ada
<?php foreach ($hotspots as $hotspot): ?>
showHotspotMarker('<?= $hotspot['x_position'] ?>', '<?= $hotspot['y_position'] ?>');
<?php endforeach; ?>

// Preview dan Hotspot untuk gambar bahan/proses pembuatan baru
document.querySelector('input[name="material_photos[]"]').addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('material-hotspot-preview');
    container.innerHTML = '';
    Array.from(files).forEach((file, idx) => {
        const tempId = 'material_' + Date.now() + '_' + idx;
        const wrapper = document.createElement('div');
        wrapper.className = 'row';
        wrapper.innerHTML = `
            <div class="col-md-6">
                <div class="image-hotspot-container">
                    <img src="${URL.createObjectURL(file)}" width="400" class="material-photo" data-id="${tempId}">
                    <div class="material-hotspot-container" id="materialHotspotContainer${tempId}"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="material-hotspot-form" id="materialHotspotForm${tempId}">
                    <input type="hidden" name="kode_produk" value="<?= $kode_produk ?>">
                    <input type="hidden" name="material_photo_id" value="${tempId}">
                    <input type="hidden" name="x_position" id="materialXPosition${tempId}">
                    <input type="hidden" name="y_position" id="materialYPosition${tempId}">
                    <input type="hidden" name="hotspot_id" id="materialHotspotId${tempId}">
                    <div class="form-group">
                        <label>Judul Hotspot</label>
                        <input type="text" class="form-control" id="materialHotspotTitle${tempId}">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Hotspot</label>
                        <textarea class="form-control" rows="3" id="materialHotspotDesc${tempId}"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="saveMaterialHotspot('${tempId}')">Simpan Hotspot</button>
                    <button type="button" class="btn btn-default" onclick="resetMaterialHotspotForm('${tempId}')">Batal</button>
                </div>
                <div class="material-hotspot-list">
                    <h4>Daftar Hotspot</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Posisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="materialHotspotList${tempId}"></tbody>
                    </table>
                </div>
            </div>
        `;
        container.appendChild(wrapper);
    });
});

// Event klik pada gambar bahan/proses pembuatan baru
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('material-photo')) {
        const photoId = e.target.dataset.id;
        const rect = e.target.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const xPercent = Math.round((x / rect.width) * 100);
        const yPercent = Math.round((y / rect.height) * 100);
        
        document.getElementById(`materialXPosition${photoId}`).value = xPercent + '%';
        document.getElementById(`materialYPosition${photoId}`).value = yPercent + '%';
        
        // Show temporary marker
        const container = document.getElementById(`materialHotspotContainer${photoId}`);
        container.innerHTML = '';
        const marker = document.createElement('div');
        marker.className = 'hotspot-marker';
        marker.style.left = xPercent + '%';
        marker.style.top = yPercent + '%';
        container.appendChild(marker);
    }
});

function showMaterialHotspotMarker(photoId, x, y) {
    const container = document.getElementById(`materialHotspotContainer${photoId}`);
    container.innerHTML = '';
    const marker = document.createElement('div');
    marker.className = 'hotspot-marker';
    marker.style.left = x;
    marker.style.top = y;
    container.appendChild(marker);
}

function removeImage(button, type, imageName) {
    if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        const container = button.closest('.image-container');
        const index = container.closest('[data-index]').dataset.index;
        const removedImagesInput = document.getElementById(`removed_${type}_images_${index}`);
        
        // Add image to removed images list
        let removedImages = removedImagesInput.value ? removedImagesInput.value.split(',') : [];
        removedImages.push(imageName);
        removedImagesInput.value = removedImages.join(',');
        
        // Remove the image container
        container.remove();
        
        // Update the existing images input
        const existingImagesInput = container.closest('div').querySelector('input[name^="existing_' + type + '_images"]');
        let existingImages = existingImagesInput.value.split(',');
        existingImages = existingImages.filter(img => img !== imageName);
        existingImagesInput.value = existingImages.join(',');
    }
}

function saveMaterialHotspot(photoId) {
    const title = document.getElementById(`materialHotspotTitle${photoId}`).value;
    const desc = document.getElementById(`materialHotspotDesc${photoId}`).value;
    const xPos = document.getElementById(`materialXPosition${photoId}`).value;
    const yPos = document.getElementById(`materialYPosition${photoId}`).value;
    
    if (!title || !xPos || !yPos) {
        return;
    }
    
    const formData = new FormData();
    formData.append('ajax_material_hotspot', '1');
    formData.append('kode_produk', '<?= $kode_produk ?>');
    formData.append('material_photo_id', photoId);
    formData.append('judul', title);
    formData.append('deskripsi', desc);
    formData.append('x_position', xPos);
    formData.append('y_position', yPos);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Add marker to image
        const marker = document.createElement('div');
        marker.className = 'hotspot-marker';
        marker.style.left = xPos;
        marker.style.top = yPos;
        marker.dataset.title = title;
        marker.dataset.desc = desc;
        marker.dataset.hotspotId = data.id;
        marker.onclick = function() { showHotspotInfo(this); };
        document.getElementById(`materialHotspotContainer${photoId}`).appendChild(marker);

        // Reset form
        resetMaterialHotspotForm(photoId);
    });
}

function editMaterialHotspot(photoId, hotspotId, title, desc, xPos, yPos) {
    document.getElementById(`materialHotspotId${photoId}`).value = hotspotId;
    document.getElementById(`materialHotspotTitle${photoId}`).value = title;
    document.getElementById(`materialHotspotDesc${photoId}`).value = desc;
    document.getElementById(`materialXPosition${photoId}`).value = xPos;
    document.getElementById(`materialYPosition${photoId}`).value = yPos;
    showMaterialHotspotMarker(photoId, xPos, yPos);
}

function resetMaterialHotspotForm(photoId) {
    document.getElementById(`materialHotspotId${photoId}`).value = '';
    document.getElementById(`materialHotspotTitle${photoId}`).value = '';
    document.getElementById(`materialHotspotDesc${photoId}`).value = '';
    document.getElementById(`materialXPosition${photoId}`).value = '';
    document.getElementById(`materialYPosition${photoId}`).value = '';
}

function deleteMaterialHotspot(hotspotId) {
    const formData = new FormData();
    formData.append('ajax_delete_material_hotspot', '1');
    formData.append('hotspot_id', hotspotId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        // Remove marker from image
        const marker = document.querySelector(`.hotspot-marker[data-hotspot-id="${hotspotId}"]`);
        if (marker) {
            marker.remove();
    }
    });
}

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

<?php 
include 'footer.php'; 
?>
