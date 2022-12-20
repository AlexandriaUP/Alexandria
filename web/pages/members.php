<?php
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("components/facultyMembersCard.php");
require_once("../uppa_core/functions.php");
require_once("../uppa_core/settings/components.php");


/* Check access rights */
if ( !isset($_SESSION["role"]) || $_SESSION["role"] != "admin" ){ header("Location: error.php?ec=ad"); exit; }




$mysqli = createDatabaseConnection();
$facultyMembers = getFacultyMembers($mysqli);
$mysqli -> close();
$facultyMembersPerRole = array( array(), array());
foreach ($facultyMembers as $fm){
 if ($fm->role->id == 'dep') array_push( $facultyMembersPerRole[0], $fm );
 else if ($fm->role->id == 'edip') array_push( $facultyMembersPerRole[1], $fm );
}
?>
<!DOCTYPE html>
<html  lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['members']; ?></title>
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
      <!-- Ionicons -->
      <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
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
         <?php echo getSideBarMenu($_SESSION["role"], "members"); ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo $uppa_page_title['members']; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME;?></a></li>
                           <li class="breadcrumb-item"><?php echo $uppa_page_title['members']; ?></li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- /.container-fluid -->
            </section>
            <section class="content">
               <div class="container-fluid">
                  <?php foreach ($facultyMembersPerRole as $fmpr) echo viewFacultyMembersCard($fmpr); ?>
               </div>
            </section>
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
      <script type="text/javascript" language="javascript" src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
      <script type="text/javascript" language="javascript" src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
      <script type="text/javascript" language="javascript" src="../plugins/jszip/jszip.min.js"></script>
      <script type="text/javascript" language="javascript" src="../plugins/pdfmake/pdfmake.min.js"></script>
      <script type="text/javascript" language="javascript" src="../plugins/pdfmake/vfs_fonts.js"></script>
      <script type="text/javascript" language="javascript" src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
      <script type="text/javascript" language="javascript" src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
      <!-- AdminLTE App -->
      <script src="../dist/js/adminlte.min.js"></script>
      <script>
         $(function () {
           $("#tblFacultyMembers_dep").DataTable({
               <?php if ($_SESSION["lang"] == "el"){ ?>
                  language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
               <?php } ?>
               "responsive": true,
               "autoWidth": false,
               "lengthChange": true,
               "dom": 'lfBtip',
               "buttons": ["copy", "csv", "excel", { extend:'pdfHtml5', orientation: 'landscape'}, "print"]
           });
         });

         $(function () {
           $("#tblFacultyMembers_edip").DataTable({
               <?php if ($_SESSION["lang"] == "el"){ ?>
                  language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
               <?php } ?>
               "responsive": true,
               "autoWidth": false,
               "lengthChange": true,
               "dom": 'lfBtip',
               "buttons": ["copy", "csv", "excel", { extend:'pdfHtml5', orientation: 'landscape'}, "print"]
           });
         });

      </script>
   </body>
</html>
