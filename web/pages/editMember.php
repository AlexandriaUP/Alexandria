<?php
/* Session */
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("../uppa_core/functions.php");
require_once("../uppa_core/settings/components.php");

/* Check if the GET parameters exist */
if ( !isset($_GET['fmid']) ){ header("Location: error.php?ec=gnr"); exit; }

/* Check access rights */
if ($_SESSION["role"] == "fm"){ if ($_GET["fmid"] != $_SESSION["member_id"]){ header("Location: error.php?ec=ad"); exit; } }
if ( !isset($_SESSION["role"]) || $_SESSION["role"] == "guest" ){ header("Location: error.php?ec=ad"); exit; }

$facultyMemberID = $_GET["fmid"];
$mysqli = createDatabaseConnection();
$facultyMember = getFacultyMemberById($mysqli, $facultyMemberID);
$ranks = getRanks($mysqli);
$departments = getDepartments($mysqli);
$mysqli -> close();

if (empty($facultyMember)) { header("Location: error.php?ec=gnr"); exit; }
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['edit_member']; ?></title>
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
         <?php echo getSideBarMenu($_SESSION["role"], "members"); ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo $uppa_page_title['edit_member']; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME;?></a></li>
                           <li class="breadcrumb-item"><?php echo $uppa_page_title['edit_member']; ?></li>
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
                        <div class="card card-gray-dark">
                           <div class="card-header">
                              <h3 class="card-title"><?php echo $uppa_page_title['edit_member']." | ".$facultyMember->last_name." ".$facultyMember->first_name; ?></h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i><span class="sr-only">maximize</span></button>
                              </div>
                           </div>
                           <!-- /.card-header -->
                           <form role="form">
                              <div class="card-body" style="display: block;">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="inpFacultyMemberID">ID</label>
                                          <input type="text" class="form-control" id="inpFacultyMemberID" placeholder="<?php echo $facultyMember->id; ?>" disabled>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="inpFacultyMemberLastName"><?php echo _LABEL_LASTNAME; ?></label>
                                          <input type="text" class="form-control is-required" id="inpFacultyMemberLastName" placeholder="<?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_LASTNAME;?>" value="<?php echo $facultyMember->last_name; ?>">
                                          <span id="spnFaculyMemberLastNameErrorMessage" class="error invalid-feedback"><?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_LASTNAME; ?></span>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="inpFacultyMemberFirstName"><?php echo _LABEL_FIRSTNAME; ?></label>
                                          <input type="text" class="form-control is-required" id="inpFacultyMemberFirstName" placeholder="<?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_FIRSTNAME;?>" value="<?php echo $facultyMember->first_name; ?>">
                                          <span id="spnFaculyMemberFirstNameErrorMessage" class="error invalid-feedback"><?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_FIRSTNAME;?></span>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="inpFacultyMemberPhD"><?php echo _LABEL_PHD_YEAR;?></label>
                                          <input type="number" maxlength="4" class="form-control" id="inpFacultyMemberPhD" placeholder="<?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_PHD;?>" value="<?php echo $facultyMember->phd_year; ?>">
                                          <span id="spnFaculyMemberPhDYearErrorMessage" class="error invalid-feedback"><?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_PHD;?></span>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label><?php echo _LABEL_RANK; ?></label>
                                          <select class="form-control" id="selFacultyMemberRank">
                                          <?php
                                             foreach ($ranks as $rank){
                                             	$selected = "";
                                             	if ($facultyMember->rank->id == $rank->id) $selected="selected";
                                             	echo "<option value='$rank->id' $selected>$rank->full_title</option>";
                                             }
                                             ?>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label><?php echo _LABEL_DEPARTMENT; ?></label>
                                          <select class="form-control" id="selFacultyMemberDepartment">
                                          <?php
                                             foreach ($departments as $department){
                                             	$selected = "";
                                             	if ($facultyMember->department->id == $department->id) $selected="selected";
                                               echo "<option value='$department->id' $selected>$department->name</option>";
                                             }
                                              ?>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="inpFacultyMemberScholarID"><?php echo _LABEL_SCHOLAR_ID; ?></label>
                                          <input type="text" class="form-control" id="inpFacultyMemberScholarID" placeholder="<?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_SCHOLAR; ?>" value="<?php echo $facultyMember->scholar_id; ?>">
                                          <span><small><?php echo _EDIT_MEMBER_FORM_CHECK_SCHOLAR_PROFILE; ?> <a id='linkScholarProfile' href="#" onclick="return false"><?php echo _LABEL_HERE; ?></a></small></span>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="inpFacultyMemberScopusID"><?php echo _LABEL_SCOPUS_ID; ?></label>
                                          <input type="text" class="form-control" id="inpFacultyMemberScopusID" placeholder="<?php echo _EDIT_MEMBER_FORM_PLACEHOLDER_SCOPUS; ?>" value="<?php echo $facultyMember->scopus_id; ?>">
                                          <span><small><?php echo _EDIT_MEMBER_FORM_CHECK_SCOPUS_PROFILE; ?> <a id='linkScopusProfile' href="#" onclick="return false"><?php echo _LABEL_HERE; ?></a></small></span>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label><?php echo _EDIT_MEMBER_FORM_LABEL_VALID_DATA; ?></label>
                                          <select class="form-control" id="selValidData">
                                             <option value="0"><?php echo _EDIT_MEMBER_FORM_LABEL_VALID_DATA_NO; ?></option>
                                             <option value="1"><?php echo _EDIT_MEMBER_FORM_LABEL_VALID_DATA_YES; ?></option>
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- /.row -->
                              </div>
                           </form>
                           <div class="card-footer">
                              <button  class="btn btn-primary float-right" id="btnUpdateMember"><?php echo _EDIT_MEMBER_FORM_UPDATE_BUTTON;?></button>
                           </div>
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
      <!-- Sweet alert -->
      <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
      <script src="//cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
      <script type="text/javascript">
         $( document ).ready(function() {
             $("#selValidData").val("<?php echo $facultyMember->isValidated; ?>");
         });

         $( "#btnUpdateMember" ).click(function() {
         	if (areAllRequiredFieldsFilled()){
         		updateFacultyMember();
         	}
         });

         $( "#linkScopusProfile" ).click(function() {
         	window.open("https://www.scopus.com/authid/detail.uri?authorId="+$("#inpFacultyMemberScopusID").val());
         });

         $( "#linkScholarProfile" ).click(function() {
         	window.open("https://scholar.google.gr/citations?user="+$("#inpFacultyMemberScholarID").val());
         });

         function areAllRequiredFieldsFilled(){
         	var areAllRequiredFIeldsFilled = true;
         	$( ".is-required" ).each(function( index ) {
         		if (!$(this).val()) {
         			$(this).addClass("is-invalid")
         			areAllRequiredFIeldsFilled = false;
         		}
         		else {
         			$(this).removeClass("is-invalid")
         		}
         	});
         	return areAllRequiredFIeldsFilled
         }

         function updateFacultyMember(){
          var facultyMember = {
                    id: $("#inpFacultyMemberID").attr('placeholder'),
                    last_name: $("#inpFacultyMemberLastName").val(),
                    first_name: $("#inpFacultyMemberFirstName").val(),
         					rank_id: $("#selFacultyMemberRank").find(":selected").val(),
         					department_id: $("#selFacultyMemberDepartment").find(":selected").val(),
         					scholar_id: $("#inpFacultyMemberScholarID").val(),
         					scopus_id: $("#inpFacultyMemberScopusID").val(),
         					phd_year: $("#inpFacultyMemberPhD").val(),
         					is_valid: $("#selValidData").find(":selected").val()
                }

         	$.ajax({
         	   url: '../uppa_core/updateFacultyMember.php',
         	   data: {facultyMember},
         	   type: 'post',
         		 success: function(response) {
         			 if (JSON.parse(response).type == "success"){
         				Swal.fire({
         					icon: 'success',
         					text: JSON.parse(response).content,
         				})
         			 } else if (JSON.parse(response).type == "error"){
         				 Swal.fire({
         						icon: 'error',
         						text: JSON.parse(response).content,
         					})
         			 }

         	    },
         	    error: function(response) {
         	        console.log('Error: ' + response);
         	    }
         	});
         }
      </script>
   </body>
</html>
