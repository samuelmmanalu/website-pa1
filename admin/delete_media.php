<?php
include 'header.php';

if (isset($_GET['id']) && isset($_GET['kode'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $kode = mysqli_real_escape_string($conn, $_GET['kode']);
    
    // Get media info before deleting
    $query = mysqli_query($conn, "SELECT * FROM produk_media WHERE id = '$id'");
    $media = mysqli_fetch_assoc($query);
    
    if ($media) {
        // Delete file from server
        if ($media['tipe'] == 'video') {
            $file_path = "../video/" . $media['nama_file'];
        } else {
            $file_path = "../image/materials/" . $media['nama_file'];
        }
        
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $delete_query = "DELETE FROM produk_media WHERE id = '$id'";
        if (mysqli_query($conn, $delete_query)) {
            echo "<script>alert('Media berhasil dihapus!'); window.location='edit_produk.php?kode=$kode';</script>";
        } else {
            echo "<script>alert('Gagal menghapus media: " . mysqli_error($conn) . "'); window.location='edit_produk.php?kode=$kode';</script>";
        }
    } else {
        echo "<script>alert('Media tidak ditemukan!'); window.location='edit_produk.php?kode=$kode';</script>";
    }
} else {
    echo "<script>alert('Parameter tidak valid!'); window.location='m_produk.php';</script>";
}

include 'footer.php';
?> 