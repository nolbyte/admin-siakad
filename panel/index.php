<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
define("RESMI", "OK");

if (!isset($_SESSION['idMHS'])) {
    header('Location: ../index.php');
}

//konfigurasi
require('../config/database.php');
require('../config/fungsi.php');
require('../vendor/autoload.php');
require('../config/csrf-token.php');

//token
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

if (isset($_GET['mod'])) {
    $mod = sanitasi($_GET['mod']);
    $hal = sanitasi($_GET['hal']);
}

$sql = $db->prepare("SELECT * FROM us_mhs WHERE mhs_uid = :idna");
$sql->execute(array(':idna' => $_SESSION['idMHS']));
$mhs = $sql->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Akademik</title>
    <link rel="shortcut icon" type="image/jpg" href="../assets/img/logo-akfar.png"/>
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="../assets/style/dashboard.css" rel="stylesheet">
</head>
<body>
<header class="navbar navbar-dark bg-primary sticky-top flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-4" href="#">
        <img src="../assets/img/logo-akfar.png" style="width: 35px" alt="Akfar Mahadhika" class="d-inline-block"> SIAKAD
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</header>
<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">
                            <span data-feather="home"></span>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a class="nav-link" href="#"><i data-feather="user"></i> Account <i data-feather="chevron-down"></i> </a>
                        <ul class="submenu collapse">
                            <li><a class="nav-link" href="#"><i data-feather="chevron-right"></i>Profile </a></li>
                            <li><a class="nav-link" href="logout.php"><i data-feather="chevron-right"></i>Logout </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php
            if (isset($_GET['mod'])) {
                include('modul/' . $mod . '/' . $hal . '.php');
            } else {
                include('dashboard.php');
            }
            ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"
        integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"
        integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha"
        crossorigin="anonymous"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
