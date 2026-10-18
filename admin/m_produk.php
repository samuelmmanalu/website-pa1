<?php 
include 'header.php';
?>

<div class="container">
    <h2 style="width: 100%; border-bottom: 4px solid gray"><b>Master Produk</b></h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Kode Produk</th>
                <th scope="col">Nama Produk</th>
                <th scope="col">Kategori</th>
                <th scope="col">Image</th>
                <th scope="col">Harga</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $result = mysqli_query($conn, "SELECT * FROM produk ORDER BY kode_produk ASC");
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                // Hitung jumlah hotspot untuk produk ini
                $hotspot_count = 0;
                $hotspot_query = mysqli_query($conn, "SELECT * FROM produk_hotspots WHERE kode_produk = '".$row['kode_produk']."'");
                $hotspot_count = mysqli_num_rows($hotspot_query);
                $hotspots = [];
                while($h = mysqli_fetch_assoc($hotspot_query)) {
                    $hotspots[] = $h;
                }
            ?>
                <tr>
                    <td><?= $no; ?></td>
                    <td><?= $row['kode_produk']; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['kategori']; ?></td>
                    <td>
                        <img src="../image/produk/<?= $row['image']; ?>" width="100">
                    </td>
                    <td>Rp.<?= number_format($row['harga']); ?></td>
                    <td>
                        <a href="edit_produk.php?kode=<?= $row['kode_produk']; ?>" class="btn btn-warning" title="Edit Produk">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a> 
                        <a href="proses/del_produk.php?kode=<?= $row['kode_produk']; ?>" class="btn btn-danger" onclick="return confirm('Yakin Ingin Menghapus Data ?')" title="Hapus Produk">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a> 
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#hotspotModal<?= $no ?>" title="Lihat Hotspot Produk">
                            <i class="glyphicon glyphicon-picture"></i> Hotspot (<?= $hotspot_count ?>)
                        </button>
                        
                        <!-- Hotspot Modal -->
                        <div class="modal fade" id="hotspotModal<?= $no ?>" tabindex="-1" role="dialog" aria-labelledby="hotspotModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="hotspotModalLabel">Hotspot: <?= $row['nama'] ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php if ($hotspot_count > 0): ?>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Judul</th>
                                                        <th>Posisi</th>
                                                        <th>Deskripsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($hotspots as $index => $hotspot): ?>
                                                    <tr>
                                                        <td><?= ($index + 1) ?></td>
                                                        <td><?= htmlspecialchars($hotspot['judul']) ?></td>
                                                        <td><?= htmlspecialchars($hotspot['x_position']) ?>, <?= htmlspecialchars($hotspot['y_position']) ?></td>
                                                        <td><?= nl2br(htmlspecialchars($hotspot['deskripsi'])) ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                Belum ada hotspot untuk produk ini.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php
                $no++; 
            }
            ?>
        </tbody>
    </table>

    <a href="tm_produk.php" class="btn btn-success">
        <i class="glyphicon glyphicon-plus-sign"></i> Tambah Produk
    </a>
</div>

<!-- Spasi untuk footer -->
<br><br><br><br><br><br><br><br><br><br><br><br>

<?php 
include 'footer.php';
?>