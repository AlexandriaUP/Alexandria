<?php
   session_start();
   require_once("components/login.php");
   require_once("components/language.php");
   require_once("../uppa_core/functions.php");
   require_once("../uppa_core/settings/components.php");

   /* Check access rights */
   if ( !isset($_SESSION["role"]) || $_SESSION["role"] != "admin" ){ header("Location: error.php?ec=ad"); exit; }

  $updateReportStatus = false;
  if ( !isset($_GET['type']) ){ header("Location: error.php?ec=gnr"); exit; }
  else { if ( $_GET['type'] == "nr" ){
      if ( !isset($_GET['title']) ){ header("Location: error.php?ec=gnr"); exit; }
      else {
        $mysqli = createDatabaseConnection();
   		  $report_id = createNewReport($mysqli, $_GET["title"])->id;
   		  $mysqli -> close();
        $updateReportStatus = true;
      }
    } else if ( $_GET['type'] == "er" ){
      if ( !isset($_GET['rid']) ){ header("Location: error.php?ec=gnr"); exit; }
      else {
        $report_id = $_GET["rid"];
        $updateReportStatus = true;
      }
    }
    else { header("Location: error.php?ec=gnr"); exit; }
  }

	if ($updateReportStatus == true){
		$mysqli = createDatabaseConnection();
		$facultyMembers = getReportStatusOfFacultyMembers($mysqli, $report_id);
		$report = getReport($mysqli, $report_id);
		$mysqli -> close();
	} else {
		exit();
	}

?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['update_report']; ?></title>
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
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <div class="container-fluid">
                  <div class="row mb-2">
                     <div class="col-sm-6">
                        <h1><?php echo $uppa_page_title['update_report']; ?></h1>
                     </div>
                     <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME;?></a></li>
                           <li class="breadcrumb-item"><?php echo $uppa_page_title['update_report']; ?></li>
                        </ol>
                     </div>
                  </div>
               </div>
               <!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="container-fluid">
                  <!-- Warning message -->
                  <div class="row">
                     <div class="col-md-12">
                        <div class="callout callout-warning">
                           <h5><?php echo _UPDATE_REPORT_WARNING_HEADER; ?></h5>
                           <p><?php echo _UPDATE_REPORT_WARNING_MESSAGE; ?></p>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="card card-dark">
                           <div class="card-header">
                              <h3 class="card-title" data-reportID="<?php echo $report_id;?>" id="report-title"><?php echo _UPDATE_REPORT_CARD_HEADER; ?></h3>
                           </div>
                           <div class="card-body">
                              <table id="tblFacultyMembers" class="table table-bordered table-hover">
                                 <thead>
                                    <tr>
                                       <th scope="col"><?php echo _UPDATE_REPORT_CARD_TABLE_COLUMN_FACULTY_MEMBER;?></th>
                                       <th scope="col"><?php echo _UPDATE_REPORT_CARD_TABLE_COLUMN_SCHOLAR;?></th>
                                       <th scope="col"><?php echo _UPDATE_REPORT_CARD_TABLE_COLUMN_SCOPUS;?></th>
                                    </tr>
                                    <tr>
                                       <td scope="col"></td>
                                       <th scope="col">
                                          <div class="progress">
                                             <div id='pbarScholar' class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                <span id='spanPbarScholar'>00%</span>
                                             </div>
                                          </div>
                                       </th>
                                       <!-- /scholar progress bar -->
                                       <th scope="col">
                                          <div class="progress">
                                             <div id='pbarScopus' class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                <span id='spanPbarScopus'>00%</span>
                                             </div>
                                          </div>
                                       </th>
                                       <!-- /scopus progress bar -->
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       foreach ($facultyMembers as $fm){

                                       	/* Print table data */
                                       	echo "<tr id='tr-$fm->id' class='fm-table-tr' data-fmID='$fm->id' data-lastName='$fm->last_name' data-firstName='$fm->first_name' data-scholarID='$fm->scholar_id' data-scopusID='$fm->scopus_id' data-scholarPubs='$fm->scholar_metadata' data-scopusPubs='$fm->scopus_metadata' data-phdYear='$fm->phd_year' data-scholarStatus='$fm->report_scholar_status' data-ScopusStatus='$fm->report_scopus_status'>";
                                       	/* Col #1: Name */
                                       	echo "<td>$fm->last_name, $fm->first_name</td>";

                                       	/* Col #2: Scholar */
                                       	if ($fm->report_scholar_status == "no id")
                                       		echo "<td id='td-scholar-$fm->id' class='alert alert-warning alert-dismissible'><i class='icon fas fa-exclamation-triangle'></i> Δεν συλλέχθηκαν δεδομένα - Το μέλος ΔΕΠ δε διαθέτει προφίλ Google Scholar</td>";
                                        	else if ($fm->report_scholar_status == "completed")
                                        		echo "<td id='td-scholar-$fm->id' class='alert alert-success alert-dismissible'><i class='icon fas fa-check'></i> Τα δεδομένα από το Google Scholar έχουν αποθηκευτεί</td>";
                                       	else if ($fm->report_scholar_status == "error")
                                       		echo "<td id='td-scholar-$fm->id' class='alert alert-danger alert-dismissible'><i class='icon fas fa-times'></i> Δεν συλλέχθηκαν δεδομένα - Υπάρχει κάποιο πρόβλημα: επικοινωνήστε με τον διαχειριστή</td>";
                                         else
                                       		echo "<td id='td-scholar-$fm->id' class=''></td>";

                                       	/* Col #3: Scopus */
                                       	if ($fm->report_scopus_status == "no id")
                                       		echo "<td id='td-scopus-$fm->id' class='alert alert-warning alert-dismissible'><i class='icon fas fa-exclamation-triangle'></i> Δεν συλλέχθηκαν δεδομένα - Το μέλος ΔΕΠ δε διαθέτει προφίλ Scopus</td>";
                                        	else if ($fm->report_scopus_status == "completed")
                                        		echo "<td id='td-scopus-$fm->id' class='alert alert-success alert-dismissible'><i class='icon fas fa-check'></i> Τα δεδομένα από το Scopus έχουν αποθηκευτεί</td>";
                                       	else if ($fm->report_scopus_status == "error")
                                       		echo "<td id='td-scopus-$fm->id' class='alert alert-danger alert-dismissible'><i class='icon fas fa-times'></i> Δεν συλλέχθηκαν δεδομένα - Υπάρχει κάποιο πρόβλημα: επικοινωνήστε με τον διαχειριστή</td>";
                                       	else
                                       		echo "<td id='td-scopus-$fm->id' class='alert'></td>";

                                       	echo "</tr>";
                                       }
                                       ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <!-- /.card-dark -->
                     </div>
                  </div>
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
         class FacultyMember {
           constructor(id, last_name, first_name, scholar_id, scopus_id, scholar_pubs, scopus_pubs, phd_year, scholar_metrics_status, scopus_metrics_status) {
             this.id = id;
             this.last_name = last_name;
         		this.first_name = first_name;
         		this.scholar_id = scholar_id;
         		this.scopus_id = scopus_id;
         		this.scholar_pubs = scholar_pubs;
         		this.scopus_pubs = scopus_pubs;
         		this.phd_year = phd_year;
         		this.scholar_metrics_status = scholar_metrics_status;
         		this.scopus_metrics_status = scopus_metrics_status;
           }
         }


           $( document ).ready(function() {
         	/* 1. Get the report ID */
         	var report_id = $('#report-title').attr("data-reportID");

         	/* 2. Get the faculty members as an array */
         	var facultyMembers = [];
         	$( ".fm-table-tr" ).each(function( index ) {
         		let fm = new FacultyMember(
               $(this).attr("data-fmID"),
               $(this).attr("data-lastName"),
               $(this).attr("data-firstName"),
               $(this).attr("data-scholarID"),
               $(this).attr("data-scopusID"),
               $(this).attr("data-scholarPubs"),
               $(this).attr("data-scopusPubs"),
               $(this).attr("data-phdYear"),
               $(this).attr("data-scholarStatus"),
               $(this).attr("data-scopusStatus"));
         		facultyMembers.push(fm);
         	});

           /* 3. Create an array with the 'open' Scholar or Scopus status only */
           var facultyMembersWithOpenStatus = [];
           for (i=0; i<facultyMembers.length; i++){
             if ( (facultyMembers[i].scholar_metrics_status == "open") || facultyMembers[i].scopus_metrics_status == "open" ){
               facultyMembersWithOpenStatus.push( facultyMembers[i] );
             }
           }


           /* 4. Get data for Scholar and Scopus*/
           var index = 0;
           function loopScholarDataCollector() {
         		setTimeout(function() {
         			if (index < facultyMembersWithOpenStatus.length){
         				loopScholarDataCollector();
                 if (facultyMembersWithOpenStatus[index].scholar_metrics_status == "open") {
                   updateGoogleScholar(facultyMembersWithOpenStatus[index]);
                 }
                 if (facultyMembersWithOpenStatus[index].scopus_metrics_status == "open") {
                   updateScopus(facultyMembersWithOpenStatus[index]);
                 }
         			}
         			index++;
         		}, 3000)
         	}
         	loopScholarDataCollector();


         	function setScholarProgressBar(value){
         		$("#pbarScholar").width(value);
         		$("#spanPbarScholar").text(value)
         	}

         	function setScopusProgressBar(value){
         		$("#pbarScopus").width(value);
         		$("#spanPbarScopus").text(value)
         	}


         	/* Initialize Scholar Progress bar */
         	var countCompletedScholarStatus = 0;
         	for (var i=0; i<facultyMembers.length; i++){
         		if (facultyMembers[i].scholar_metrics_status != "open") {
         			countCompletedScholarStatus++;
         		}
         	}
         	setScholarProgressBar(parseInt(100*countCompletedScholarStatus/facultyMembers.length) + "%");


         	/* Initialize Scopus Progress bar */
         	var countCompletedScopusStatus = 0;
         				for (i=0; i<facultyMembers.length; i++){
         					if (facultyMembers[i].scopus_metrics_status != "open") {
         						countCompletedScopusStatus++;
         					}
         				}
         	setScopusProgressBar(parseInt(100*countCompletedScopusStatus/facultyMembers.length) + "%");

           /* Update Google Scholar */
         	function updateGoogleScholar(author){
         		$.ajax({
         			url: "../uppa_core/getGoogleScholarMetricsForAuthorFunction.php",
         			data: {
         				member_id: author.id,
         				scholar_id: author.scholar_id,
         				report_id: report_id,
         				phd_year: author.phd_year
         			},
         			success: function( result ) { // result should be the member_id
         				msg = JSON.parse(result);
         				var elementID = "td-scholar-" + msg.member_id;
         				var element = document.getElementById(elementID);
         				element.classList.add("alert");
         				element.classList.add("alert-dismissible");

         				if (msg.type == "success"){
         					element.classList.add("alert-success");
         					element.innerHTML  = "<i class='icon fas fa-check'></i> Τα δεδομένα από το Google Scholar έχουν αποθηκευτεί";
         				} else if (msg.type == "error"){
         					element.classList.add("alert-danger");
         					element.innerHTML  = "<i class='icon fas fa-times'></i> " + msg.content;
         				}


         				/* Update the status of the selected member */
         				for (i = 0; i < facultyMembers.length; i++){
         					if (facultyMembers[i].id == msg.member_id){
         						facultyMembers[i].scholar_metrics_status = "completed";
         					}
         				}

         				/* Update the number of the completed scholar reports */
         				var countCompletedScholarStatus = 0;
         				for (i=0; i<facultyMembers.length; i++){
         					if (facultyMembers[i].scholar_metrics_status != "open") {
         						countCompletedScholarStatus++;
         					}
         				}

         				/* Update the scholar progress bar */
         				setScholarProgressBar(parseInt(100*countCompletedScholarStatus/facultyMembers.length) + "%");
         			}
         		});
         	}

         	function updateScopus(author){
         		$.ajax({
         			url: "../uppa_core/getScopusMetricsForAuthorFunctionV2.php",
         			data: {
         				member_id: author.id,
         				scopus_id: author.scopus_id,
         				report_id: report_id,
         				phd_year: author.phd_year
         			},
         			success: function( result ) {
         				msg = JSON.parse(result);
         				var elementID = "td-scopus-" + msg.member_id;
         				var element = document.getElementById(elementID);
         				element.classList.add("alert");
         				element.classList.add("alert-dismissible");

         				if (msg.type == "success"){
         					element.classList.add("alert-success");
         					element.innerHTML  = "<i class='icon fas fa-check'></i> Τα δεδομένα από το Scopus έχουν αποθηκευτεί";
         				} else if (msg.type == "error"){
         					element.classList.add("alert-danger");
         					element.innerHTML  = "<i class='icon fas fa-times'></i> " + msg.content;
         				}

         				for (i=0; i<facultyMembers.length; i++){
         					if (facultyMembers[i].id == msg.member_id){
         						facultyMembers[i].scopus_metrics_status = "completed";
         					}
         				}

         				var countCompletedScopusStatus = 0;
         				for (i=0; i<facultyMembers.length; i++){
         					if (facultyMembers[i].scopus_metrics_status != "open") {
         						countCompletedScopusStatus++;
         					}
         				}

         				setScopusProgressBar(parseInt(100*countCompletedScopusStatus/facultyMembers.length) + "%");
         			}
         		});
         	}

         	});
      </script>
   </body>
</html>
