<?php
/* Session */
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("../uppa_core/settings/components.php");

$error_message = _ERROR_CODE_GENERIC;
if(isset($_GET['ec'])){
  if ($_GET['ec'] == "gnr") $error_message = _ERROR_CODE_GENERIC;
  if ($_GET['ec'] == "ad") $error_message = _ERROR_CODE_ACCESS_DENIED;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". _ERROR_HEADER; ?></title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="../dist/css/adminlte.min.css">
      <!-- Alexandria style -->
      <link rel="stylesheet" href="../dist/css/alexandria.css">
      <!-- Google Font: Source Sans Pro -->
      <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
      <?php echo getFavicon(); ?>
   </head>
   <body class="hold-transition sidebar-mini">
      <div class="wrapper">
         <?php echo getTopNavBar($_SESSION['lang']); ?>
         <?php echo getSideBarMenu($_SESSION["role"], ""); ?>
         <div class="content-wrapper">
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo _ERROR_HEADER; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME; ?></a></li>
                           <li class="breadcrumb-item"><?php echo _ERROR_HEADER; ?></li>
                        </ol>
                     </div>
                  </div>
               </div>
            </section>
            <section class="content">
               <div class="container-fluid">
                  <div class="row">
                    <div class="col-md-12">
                        <div class="info-box bg-danger">
                          <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                          <div class="info-box-content"><?php echo $error_message; ?></div>
                        </div>
                      </div>
                  </div>
               </div>
            </section>
            <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
            <i class="fas fa-chevron-up"></i>
            </a>
         </div>
         <?php echo getFooter(); ?>
      </div>
      <script src="../plugins/jquery/jquery.min.js"></script>
      <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="../dist/js/adminlte.min.js"></script>
   </body>
</html>
