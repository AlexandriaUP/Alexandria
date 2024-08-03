<?php
session_start();
session_destroy();
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: pages/reports.php");
  exit;
}
/* Version */
require_once("uppa_core/settings/version.php");

/* Language config */
$_SESSION['lang'] = "el";
if(isset($_GET['lang']) && !empty($_GET['lang']) && ($_GET['lang']=='el' || $_GET['lang']=='en')) $_SESSION['lang'] = $_GET['lang'];
if ($_SESSION['lang'] == "en") require_once("uppa_core/language/lang.en.php");
else require_once("uppa_core/language/lang.el.php");
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo _ALEXANDRIA; ?> | <?php echo _PAGE_LOGIN; ?></title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="dist/css/alexandria.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <!-- Favicon -->
  <link rel='apple-touch-icon' sizes='180x180' href='dist/img/favicon/apple-touch-icon.png'>
  <link rel='apple-touch-icon' sizes='180x180' href='dist/img/favicon/apple-touch-icon.png'>
	<link rel='icon' type='image/png' sizes='32x32' href='dist/img/favicon/favicon-32x32.png'>
	<link rel='icon' type='image/png' sizes='16x16' href='dist/img/favicon/favicon-16x16.png'>
	<link rel='manifest' href='dist/img/favicon/site.webmanifest'>
</head>
<body style="background-color: #fcfcfc;">
<!-- Automatic element centering -->
  <div class="container-fluid">
    <div class="row" style="margin:30px 0px;">
      <div class="col-md-4"></div>
      <div class="col-md-4"><img center alt="logo" src="dist/img/logoAlexandria.png" width="100%"></div>
      <div class="col-md-4"></div>
    </div>
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4">
        <div class="col-12 text-center">
     	    <a href='uppa_core/saml/paperaggregator/index.php?sso' class="btn btn-primary btn-lg"><img alt="upatras logo" src="dist/img/logoUpatras.png" width='30px'> <?php echo _LOGIN_FACULTY_MEMBER; ?></a>
        </div>
        <div class="col-12 text-center">
      	<a href='uppa_core/loginAsGuest.php'><?php echo _LOGIN_VISITOR; ?></a>
        </div>
      </div>
      <div class="col-md-4"></div>
    </div>

  <!-- Footer -->
  <footer class="main-footer fixed-bottom" style="margin:0px;">
    <div class='row'>
      <div class='col-md-4'>Copyright &copy; 2021 <?php echo _UPAT;?><br><a href="mailto:modipsecr@upatras.gr">modipsecr@upatras.gr</a></div>
      <div class='col-md-4'><img alt="eu funding logo" src='dist/img/epanadvm_footer_2.jpg' width='100%'></div>
      <div class='colm-md-3 ml-auto'><?php echo _FOOTER_VERSION;?> <?php echo _VERSION_NUMBER; ?></div>
    </div>
  </footer>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
