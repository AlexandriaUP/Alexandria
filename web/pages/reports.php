<?php
/* Session */
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("../uppa_core/functions.php");
require_once("../uppa_core/settings/components.php");



/* Get reports */
$mysqli = createDatabaseConnection();
$reports = getReports($mysqli);
$mysqli -> close();

?>
<!DOCTYPE html>
<html lang="el">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['view_reports']; ?></title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
      <!-- DataTables -->
      <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
      <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
      <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
         <?php echo getSideBarMenu($_SESSION["role"], "view_reports"); ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo _VIEW_REPORTS; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="#"><?php echo _HOME; ?></a></li>
                           <li class="breadcrumb-item"><?php echo $uppa_page_title['view_reports']; ?></li>
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
                              <h3 class="card-title"><?php echo _AVAILABLE_REPORTS; ?></h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i><span class="sr-only">maximize</span></button>
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i><span class="sr-only">collapse</span></button>
                              </div>
                              <!-- /.card-tools -->
                           </div>
                           <!-- /.card-header -->
                           <div class="card-body">
                              <table id="tblReports" class="table table-bordered table-hover datatable">
                                 <thead>
                                    <tr>
                                       <th scope="col"><?php echo _CREATION_DATE; ?></th>
                                       <th scope="col"><?php echo _TITLE; ?></th>
                                       <th scope="col"><?php echo _ACTION; ?></th>
                                       <?php
                                       if ($_SESSION["role"] == "admin") echo "<th scope='col'>"._STATUS."</th>";
                                       ?>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       foreach ($reports as $report){
                                       	echo "<tr><td>$report->datetimeCreated</td><td>$report->title</td><td><a href='report.php?rid=$report->id'>"._VIEW_DETAILS."</a></td>";
                                          if ($_SESSION["role"] == "admin") {
                                             echo "<td>".$report->progress."%";
                                             if($report->progress != 100.00) {
                                                echo " - <a href='updateReport.php?type=er&rid=".$report->id."'>"._CONTINUE_UPDATE."</a>";
                                             }
                                             echo "</td>";
                                          } 
                                        echo "</tr>";
                                       }
                                       ?>
                                 </tbody>
                              </table>
                           </div>
                           <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
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
      <!-- DataTables -->
      <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
      <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
      <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
      <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
      <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
      <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
      <script src="../plugins/jszip/jszip.min.js"></script>
      <script src="../plugins/pdfmake/pdfmake.min.js"></script>
      <script src="../plugins/pdfmake/vfs_fonts.js"></script>
      <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
      <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
      <!-- AdminLTE App -->
      <script src="../dist/js/adminlte.min.js"></script>
      <script>
         $(function () {
            $("#tblReports").DataTable({
               <?php if ($_SESSION["lang"] == "el"){ ?>
         	      language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
               <?php } ?>
               "responsive": true,
               "lengthChange": true,
               "autoWidth": false,
               "dom": 'lfBtip',
               "buttons": ["copy", "csv", "excel", "pdf", "print"]
            });
         });
      </script>
   </body>
</html>
