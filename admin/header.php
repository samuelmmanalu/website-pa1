<?php 
session_start();
include '../koneksi/koneksi.php';
if(!isset($_SESSION['admin'])){
    header('location:index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Informasi Ulos Admin</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-theme.css">
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <!-- Add Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for better icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Custom styles for admin header */
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .navbar {
            background: linear-gradient(to right, #ff8680, #e6736d);
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 62px;
            border-radius: 0;
            margin-bottom: 25px;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 20px;
            color: white !important;
            padding: 15px;
            height: 62px;
            display: flex;
            align-items: center;
        }
        .navbar-brand i {
            margin-right: 8px;
            font-size: 24px;
        }
        .navbar-nav > li > a {
            color: white !important;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            padding-top: 20px;
            padding-bottom: 20px;
            transition: all 0.3s ease;
        }
        .navbar-nav > li > a:hover, 
        .navbar-nav > li > a:focus {
            background-color: rgba(255, 255, 255, 0.15) !important;
        }
        .dropdown-menu {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
            padding: 10px 0;
        }
        .dropdown-menu > li > a {
            padding: 8px 20px;
            font-family: 'Montserrat', sans-serif;
            color: #333;
            transition: all 0.2s ease;
        }
        .dropdown-menu > li > a:hover {
            background-color: #ffd6d4;
            color: #e6736d;
        }
        .navbar-toggle {
            margin-top: 14px;
        }
        /* Accent line at the bottom of navbar */
        .accent-line {
            height: 3px;
            background: linear-gradient(to right, #ffd6d4, #ff8680, #e6736d, #ffd6d4);
            width: 100%;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="halaman_utama.php"><i class="fas fa-gem"></i> Ulos Admin</a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-left">
                    
                    <li><a href="http://localhost/Informasi-Ulos/admin/tm_produk.php"><i class="fas fa-plus-circle"></i> Tambah Ulos</a></li>

                    <li><a href="http://localhost/Informasi-Ulos/index.php" target="_blank"><i class="fas fa-globe"></i> View Website</a></li>
                    
                    <li><a href="halaman_utama.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cog"></i> Pemeliharaan <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="../DATABASE/backup.php"><i class="fas fa-database"></i> Backup Database</a></li>
                            <li><a href="../DATABASE/retrieve.php"><i class="fas fa-redo"></i> Retrieve Database</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-shield"></i> Admin <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="proses/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <div class="accent-line"></div>