<?php
   session_start();
   require_once("components/login.php");
   require_once("components/language.php");
   require_once("../uppa_core/functions.php");
   require_once("../uppa_core/settings/components.php");

   if ( !isset($_SESSION["role"]) || $_SESSION["role"] != "admin" ){ header("Location: error.php?ec=ad"); exit; }
   ?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['new_report']; ?></title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
      <!-- DataTables -->
      <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
      <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
         <!-- Top navigation bar -->
         <?php echo getTopNavBar($_SESSION['lang']); ?>
         <!-- Side bar -->
         <?php echo getSideBarMenu($_SESSION["role"], "new_report"); ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo $uppa_page_title['new_report']; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME;?></a></li>
                           <li class="breadcrumb-item"><?php echo $uppa_page_title['new_report']; ?></li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="card card-dark">
                           <div class="card-header">
                              <h3 class="card-title"><?php echo _NEW_REPORT_FORM_HEADER; ?></h3>
                           </div>
                           <!-- /.card-header -->
                           <!-- form start -->
                           <form role="form">
                              <div class="card-body">
                                 <div class="form-group">
                                    <label for="inpReportTitle"><?php echo _NEW_REPORT_FORM_LABEL_TITLE; ?></label>
                                    <input type="text" class="form-control" id="inpReportTitle" placeholder="<?php echo _NEW_REPORT_FORM_PLACEHOLDER_TITLE; ?>">
                                    <span id="spnReportTitleErrorMessage" class="error invalid-feedback"><?php echo _NEW_REPORT_FORM_ERROR;?></span>
                                 </div>
                              </div>
                              <!-- /.card-body -->
                              <div class="card-footer">
                                 <div class="btn btn-primary float-right" id="btnReportCreation"><?php echo _NEW_REPORT_FORM_BUTTON_CREATE; ?></div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
                  <!-- /.row -->
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
            <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
            <i class="fas fa-chevron-up"></i>
            </a>
         </div>
         <!-- /.content-wrapper -->
         <?php echo getFooter(); ?>
      </div>
      <!-- ./wrapper -->
      <!-- jQuery -->
      <script src="../plugins/jquery/jquery.min.js"></script>
      <!-- Bootstrap 4 -->
      <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- AdminLTE App -->
      <script src="../dist/js/adminlte.min.js"></script>
      <script>
         $('#btnReportCreation').click(function () {
          if ( $("#inpReportTitle").val() == "") {
          $("#spnReportTitleErrorMessage").show();
          }
          else {
            $("#spnReportTitleErrorMessage").hide();
         var url = 'updateReport.php?type=nr&title='+$("#inpReportTitle").val();
            window.open(url);
         }
         });
      </script>
   </body>
</html>
