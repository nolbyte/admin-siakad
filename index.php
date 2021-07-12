<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
define("RESMI", "OK");

//konfigurasi
require('config/database.php');
require('config/csrf-token.php');
require('config/fungsi.php');
//require('config/gump.class.php');
require('vendor/autoload.php');
//token
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Akademik</title>
    <link rel="shortcut icon" type="image/jpg" href="assets/img/logo-akfar.png"/>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.18/sweetalert2.all.min.js" integrity="sha512-kW/Di7T8diljfKY9/VU2ybQZSQrbClTiUuk13fK/TIvlEB1XqEdhlUp9D+BHGYuEoS9ZQTd3D8fr9iE74LvCkA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.18/sweetalert2.min.css" integrity="sha512-riZwnB8ebhwOVAUlYoILfran/fH0deyunXyJZ+yJGDyU0Y8gsDGtPHn1eh276aNADKgFERecHecJgkzcE9J3Lg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="assets/style/style.css" rel="stylesheet">
</head>
<body>
<?php
if($csrf->check_valid('post')){
    $gump     = new GUMP();
    $npm      = $_POST['npm'];
    $password = $_POST['password'];
    $_POST = array(
        'npm'      => $npm,
        'password' => $password
    );
    $_POST = $gump->sanitize($_POST);
    $gump->validation_rules(array(
        'npm'      => 'required|numeric',
        'password' => 'required'
    ));
    $gump->filter_rules(array(
            'npm' => 'trim|sanitize_numbers',
    ));
    $ok = $gump->run($_POST);
    if($ok === false){
        $error[] = $gump->get_readable_errors(true);
    }else{
        $sql = $db->prepare("SELECT mhs_uid, mhs_nim, mhs_password, mhs_nama FROM us_mhs WHERE mhs_nim = :nimna");
        $sql->execute(array(':nimna' => $npm));
        $log = $sql->fetch(PDO::FETCH_ASSOC);
        if($log){
            if(password_verify($password, $log['mhs_password'])){
                $_SESSION['idMHS'] = $log['mhs_uid'];
                $_SESSION['nmMHS'] = $log['mhs_nama'];
                header('Location: panel/index.php');
            }else{
                ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'NIM/Password yang anda masukkan tidak sesuai',
                    showConfirmButton: false,
                    timer: 1700
                })
            </script>
            <?php
            }
        }else{
            ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'NIM/Password yang anda masukkan tidak cocok',
                    showConfirmButton: false,
                    timer: 1700
                })
            </script>
            <?php
        }
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand col-md-6 col-lg-3 me-0 px-4" href="#">
            <img src="assets/img/logo-akfar.png" style="width: 35px" alt="Akfar Mahadhika" class="d-inline-block">
            SIAKAD AKFAR MAHADHIKA
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="#">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#info">Pengumuman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Perwalian-Dosen</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Hero Banner Section -->
<section class="hero-banner py-5">
    <div class="container">
        <div class="row row align-items-center">
            <div class="col-lg-5 offset-lg-1 order-lg-1">
                <img src="assets/img/hero-image.jpg" class="img-fluid" alt="Web Development">
            </div>
            <div class="col-lg-6">
                <h2 class="mt-3">Sistem Informasi Akademik<br>Akademi Farmasi Mahadhika</h2>
                <p class="lead text-secondary my-5">Media Digital Layanan Bimbingan, Monitoring, dan Penilaian Akademik</p>
                <a href="#mhs" class="btn btn-outline-primary">KRS-Mahasiswa</a>&nbsp;<a class="btn btn-outline-success">Perwalian-Dosen</a>
            </div>
        </div>
    </div>
</section>
<main class="container py-4">
    <div class="row g-5">
        <div id="info" class="col-md-8">
            <div class="card">
                <div class="card-header bg-success bg-gradient text-white">
                    Informasi Akademik
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div id="mhs" class="card-header bg-success bg-gradient text-white">
                    Login SIAKAD
                </div>
                <div class="card-body">
                    <?php
                    if(isset($error)){
                        foreach($error as $salah){
                            ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <div>
                                    <?= $salah.'<br>'; ?>
                                    <meta http-equiv="refresh" content="3">
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="npm" class="form-label">NIM</label>
                            <input type="hidden" name="<?=$token_id;?>" value="<?=$token_value;?>">
                            <input type="number" name="npm" class="form-control" id="npm" aria-describedby="npmhelp">
                            <div id="npmhelp" class="form-text">Nomor Induk Mahasiswa</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password">
                        </div>
                        <button type="submit" class="btn btn-primary"><i data-feather="unlock"></i> Log-in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="footer mt-auto py-3 bg-primary">
    <div class="container">
        <span class="text-white">2021 Akademi Farmasi Mahadhika</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"
        integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE"
        crossorigin="anonymous"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
