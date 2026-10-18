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
        $harga = $_POST['harga'];
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

        // Handle rumus upload
        if (!empty($_FILES['rumus']['name'][0])) {
            $target_dir = "../image/materials/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Delete existing rumus for this product
            mysqli_query($conn, "DELETE FROM produk_media WHERE kode_produk = '$kode' AND tipe = 'gambar' AND jenis = 'rumus'");
            
            foreach ($_FILES['rumus']['tmp_name'] as $key => $tmp_name) {
                if (!empty($_FILES['rumus']['name'][$key])) {
                    $rumus_name = time() . '_' . $key . '_' . basename($_FILES['rumus']['name'][$key]);
                    $target_file = $target_dir . $rumus_name;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $insert_rumus = "INSERT INTO produk_media (kode_produk, nama_file, tipe, jenis, urutan) 
                                       VALUES ('$kode', '$rumus_name', 'gambar', 'rumus', $key)";
                        mysqli_query($conn, $insert_rumus);
                    }
                }
            }
        }

        // Update produk
        $update_query = "UPDATE produk SET 
                        nama = '$nama',
                        harga = '$harga',
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
                
                $file_name = time() . '_' . basename($_FILES['alat_images']['name'][$index]);
                $target_file = $target_dir . $file_name;
                
                if (move_uploaded_file($_FILES['alat_images']['tmp_name'][$index], $target_file)) {
                    $image_name = $file_name;
                }
            }
            elseif (isset($_POST['existing_alat_images'][$index]) && !empty($_POST['existing_alat_images'][$index])) {
                $image_name = $_POST['existing_alat_images'][$index];
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
                
                $file_name = time() . '_' . basename($_FILES['bahan_images']['name'][$index]);
                $target_file = $target_dir . $file_name;
                
                if (move_uploaded_file($_FILES['bahan_images']['tmp_name'][$index], $target_file)) {
                    $image_name = $file_name;
                }
            }
            elseif (isset($_POST['existing_bahan_images'][$index]) && !empty($_POST['existing_bahan_images'][$index])) {
                $image_name = $_POST['existing_bahan_images'][$index];
            }
            
            $bahan_name = mysqli_real_escape_string($conn, $bahan_name);
            $image_name = mysqli_real_escape_string($conn, $image_name);
            $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
            $cara_penggunaan = mysqli_real_escape_string($conn, $cara_penggunaan);
            
            $insert_bahan_query = "INSERT INTO produk_bahan (kode_produk, nama_bahan, gambar_bahan, deskripsi, cara_penggunaan) 
                                VALUES ('$kode_produk', '$bahan_name', '$image_name', '$deskripsi', '$cara_penggunaan')";
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
            $jenis = mysqli_real_escape_string($conn, $_POST['jenis'] ?? 'produk');
            
            if (!empty($_POST['hotspot_id'])) {
                $hotspot_id = mysqli_real_escape_string($conn, $_POST['hotspot_id']);
                $query = "UPDATE produk_hotspots 
                          SET judul = '$judul', deskripsi = '$deskripsi', 
                              x_position = '$x_position', y_position = '$y_position',
                              jenis = '$jenis'
                          WHERE id = '$hotspot_id'";
            } else {
                $query = "INSERT INTO produk_hotspots 
                          (kode_produk, judul, deskripsi, x_position, y_position, jenis) 
                          VALUES 
                          ('$kode_produk', '$judul', '$deskripsi', '$x_position', '$y_position', '$jenis')";
            }
            
            mysqli_query($conn, $query);
        } 
        elseif ($action === 'delete' && isset($_POST['hotspot_id'])) {
            $hotspot_id = mysqli_real_escape_string($conn, $_POST['hotspot_id']);
            $query = "DELETE FROM produk_hotspots WHERE id = '$hotspot_id'";
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
            margin-bottom: 20px;
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
                    <label>Harga</label>
                    <input type="number" class="form-control" name="harga" value="<?= $data['harga']; ?>">
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

        <!-- Rumus Upload Section -->
        <div class="form-group">
            <label>Gambar Rumus Pembuatan</label>
            <div class="input-group">
                <input type="file" name="rumus[]" class="form-control" accept="image/*" multiple>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('input[name=\"rumus[]\"]').click()">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </div>
            <small class="form-text text-muted">Upload gambar rumus pembuatan dengan hotspot (bisa multiple)</small>
            
            <?php 
            $rumus_query = mysqli_query($conn, "SELECT * FROM produk_media WHERE kode_produk = '$kode_produk' AND tipe = 'gambar' AND jenis = 'rumus' ORDER BY urutan");
            if (mysqli_num_rows($rumus_query) > 0): 
            ?>
                <div class="mt-2">
                    <p>Gambar rumus saat ini:</p>
                    <?php while ($rumus = mysqli_fetch_assoc($rumus_query)): ?>
                        <div style="display: inline-block; margin-right: 10px; margin-bottom: 10px;">
                            <img src="../image/materials/<?= htmlspecialchars($rumus['nama_file']) ?>" width="200" class="mr-2 mb-2">
                            <div>
                                <a href="delete_media.php?id=<?= $rumus['id'] ?>&kode=<?= $kode_produk ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus gambar rumus ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rumus Hotspot Editor Section -->
        <div class="hotspot-editor-container">
            <h3 style="width: 100%; border-bottom: 4px solid gray">Pengaturan Hotspot Rumus</h3>
            
                    <div class="row">
                <div class="col-md-6">
                    <div class="image-hotspot-container">
                        <img src="../image/materials/<?= $rumus['nama_file'] ?? '' ?>" width="400" id="rumusImage">
                        <div id="rumusHotspotContainer"></div>
                            </div>
                        </div>
                
                <div class="col-md-6">
                    <div id="rumusHotspotForm" class="hotspot-form">
                        <input type="hidden" name="rumus_hotspot_action" value="save">
                        <input type="hidden" name="kode_produk" value="<?= $kode_produk ?>">
                        <input type="hidden" name="rumus_x_position" id="rumusXPosition">
                        <input type="hidden" name="rumus_y_position" id="rumusYPosition">
                        <input type="hidden" name="rumus_hotspot_id" id="rumusHotspotId">
                        <input type="hidden" name="jenis" value="rumus">
                        
                            <div class="form-group">
                            <label>Judul Hotspot</label>
                            <input type="text" name="rumus_judul" class="form-control" id="rumusHotspotTitle">
                                </div>
                        
                            <div class="form-group">
                            <label>Deskripsi Hotspot</label>
                            <textarea name="rumus_deskripsi" class="form-control" rows="3" id="rumusHotspotDesc"></textarea>
                            </div>
                        
                        <button type="button" class="btn btn-primary" onclick="saveRumusHotspot()">Simpan Hotspot</button>
                        <button type="button" class="btn btn-default" onclick="resetRumusHotspotForm()">Batal</button>
            </div>
            
                    <div class="hotspot-list">
                        <h4>Daftar Hotspot Rumus</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Posisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rumus_hotspots_query = mysqli_query($conn, "SELECT * FROM produk_hotspots WHERE kode_produk = '$kode_produk' AND jenis = 'rumus'");
                                while ($hotspot = mysqli_fetch_assoc($rumus_hotspots_query)): 
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($hotspot['judul']) ?></td>
                                    <td><?= htmlspecialchars($hotspot['x_position']) ?>, <?= htmlspecialchars($hotspot['y_position']) ?></td>
                                    <td>
                                        <button class="btn btn-xs btn-warning" onclick="editRumusHotspot(<?= $hotspot['id'] ?>, '<?= addslashes($hotspot['judul']) ?>', '<?= addslashes($hotspot['deskripsi']) ?>', '<?= $hotspot['x_position'] ?>', '<?= $hotspot['y_position'] ?>')">
                                            Edit
            </button>
                                        <button class="btn btn-xs btn-danger" onclick="deleteRumusHotspot(<?= $hotspot['id'] ?>)">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
        </div>
                            </div>
                        </div>
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
    </form>
</div>

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
                    <input type="file" name="alat_images[]" class="form-control" accept="image/*">
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
                    <input type="file" name="bahan_images[]" class="form-control" accept="image/*">
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

// Rumus Hotspot Functions
document.getElementById('rumusImage').addEventListener('click', function(e) {
    const rect = this.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const xPercent = Math.round((x / rect.width) * 100);
    const yPercent = Math.round((y / rect.height) * 100);
    
    document.getElementById('rumusXPosition').value = xPercent + '%';
    document.getElementById('rumusYPosition').value = yPercent + '%';
    
    showRumusHotspotMarker(xPercent + '%', yPercent + '%');
});

function showRumusHotspotMarker(x, y) {
    const container = document.getElementById('rumusHotspotContainer');
    container.innerHTML = '';
    const marker = document.createElement('div');
    marker.className = 'hotspot-marker';
    marker.style.left = x;
    marker.style.top = y;
    container.appendChild(marker);
}

function editRumusHotspot(id, title, desc, x, y) {
    document.getElementById('rumusHotspotId').value = id;
    document.getElementById('rumusHotspotTitle').value = title;
    document.getElementById('rumusHotspotDesc').value = desc;
    document.getElementById('rumusXPosition').value = x;
    document.getElementById('rumusYPosition').value = y;
    showRumusHotspotMarker(x, y);
}

function resetRumusHotspotForm() {
    document.getElementById('rumusHotspotId').value = '';
    document.getElementById('rumusHotspotTitle').value = '';
    document.getElementById('rumusHotspotDesc').value = '';
    document.getElementById('rumusXPosition').value = '';
    document.getElementById('rumusYPosition').value = '';
    document.getElementById('rumusHotspotContainer').innerHTML = '';
}

function deleteRumusHotspot(hotspotId) {
    if (confirm('Yakin hapus hotspot ini?')) {
        document.getElementById('rumusHotspotId').value = hotspotId;
        document.querySelector('#rumusHotspotForm [name="rumus_hotspot_action"]').value = 'delete';
        document.forms[0].submit();
    }
}

function saveRumusHotspot() {
    const title = document.getElementById('rumusHotspotTitle').value;
    const xPos = document.getElementById('rumusXPosition').value;
    const yPos = document.getElementById('rumusYPosition').value;
    
    if (!title || !xPos || !yPos) {
        alert('Judul dan posisi hotspot harus diisi!');
        return;
    }
    
    document.forms[0].submit();
}

// Tampilkan hotspot rumus yang sudah ada
<?php 
$rumus_hotspots_query = mysqli_query($conn, "SELECT * FROM produk_hotspots WHERE kode_produk = '$kode_produk' AND jenis = 'rumus'");
while ($hotspot = mysqli_fetch_assoc($rumus_hotspots_query)): 
?>
showRumusHotspotMarker('<?= $hotspot['x_position'] ?>', '<?= $hotspot['y_position'] ?>');
<?php endwhile; ?>
</script>

<?php 
include 'footer.php'; 
?>