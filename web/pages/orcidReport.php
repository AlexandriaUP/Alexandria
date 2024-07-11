<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'fm') { header("Location: error.php?ec=ad"); exit; }

require_once("../uppa_core/functions.php");
require_once("components/language.php");
require_once("../uppa_core/settings/components.php");
require_once("../uppa_core/settings/year.php");

//echo "<pre>";
//print_r($_SESSION);

$mysqli = createDatabaseConnection();
$sql = "SELECT fm.orcid_id FROM faculty_member as fm WHERE fm.id = '".$_SESSION['member_id']."'";

$result = $mysqli->query($sql);
if( $result->num_rows > 0 ) {
    $orcid_id = $result->fetch_row()[0];
} else {
    header("Location: error.php?ec=norc"); exit;
}

if (!isset($_POST['year']) || !is_numeric($_POST['year'])) {
   $year = _CURRENT_YEAR;
} else {
   $year = $_POST['year'];
}
?>

<!DOCTYPE html>
<html  lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | "._MENU_VIEW_ORCID_DATA ?></title>
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
         <!-- Top navigation bar -->
         <?php echo getTopNavBar($_SESSION['lang']); ?>
         <!-- Side bar -->
         <?php echo getSideBarMenu($_SESSION["role"], "ORCID"); ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo _MENU_VIEW_ORCID_DATA; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME;?></a></li>
                           <li class="breadcrumb-item"><?php echo _MENU_VIEW_ORCID_DATA; ?></li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- /.container-fluid -->
            </section>
            <section class="content">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="card card-gray-dark">
                           <div class="card-header">
                              <h3 class="card-title"><?php echo _LABEL_PUBLICATIONS;?></h3>
                           </div>
                           <div class="card-body" style="display: block;">
                              <div class="ribbon-wrapper ribbon-lg">
                                 <div class="ribbon bg-primary text-lg">
                                    <?php echo $year;?>
                                 </div>
                              </div>
                              <div class="mb-2">
                                 <form method="post">
                                    <div class="row">
                                       <div class="col-sm-2">
                                          <select name="year" class="form-control" aria-label="select year">
                                          <?php
                                             for ($k = _CURRENT_YEAR; $k >= 1980; $k--) {
                                                if ($k == $year) {
                                                   $selected = "selected";
                                                } else {
                                                   $selected = "";
                                                }
                                                ?>
                                                   <option value="<?php echo $k;?>"<?php echo $selected;?>><?php echo $k;?></option>
                                                <?php
                                             }
                                          ?>
                                          </select>
                                       </div>
                                       <div class="col-sm-10">
                                          <button type="submit" class="btn btn-dark"><?php echo _SELECT;?></button>
                                       </div>
                                    </div>
                                 </form>
                              </div>
                                 <?php
                                    $pubs = orcidCitations($orcid_id, $year);
                                    if (sizeof($pubs) > 0) {
                                       ?>
                                          <table class="table">
                                             <thead>
                                                <tr>
                                                   <th scope="col">#</th>
                                                   <th scope="col"><?php echo _LABEL_PUBLICATION;?></th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php
                                                   $i = 1;
                                                   foreach ($pubs as $pub) {
                                                      ?>
                                                         <tr>
                                                            <td><?php echo $i;?></td>
                                                            <td><?php echo $pub;?></td>
                                                         </tr>
                                                      <?php
                                                      $i++;
                                                   }
                                                ?>
                                             </tbody>
                                          </table>
                                       <?php
                                    } else {
                                       ?>
                                       <div class="alert alert-warning" role="alert">
                                          <?php echo _NO_ORCID_PUBS;?>
                                       </div>
                                       <?php
                                    }
                                 ?>
                           </div>
                        </div>
                     </div>
                  </div>
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
      <!-- AdminLTE App -->
      <script src="../dist/js/adminlte.min.js"></script>
   </body>
</html>

