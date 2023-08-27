<?php
/* Required files */
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("../uppa_core/functions.php");
require_once("../uppa_core/settings/components.php");

/* Check if the GET parameters exist */
if ( !isset($_GET['fmid']) ){ header("Location: error.php?ec=gnr"); exit; }
if ( !isset($_GET['rid']) ){ header("Location: error.php?ec=gnr"); exit; }

/* Check access rights */
if ($_SESSION["role"] == "fm"){ if ($_GET["fmid"] != $_SESSION["member_id"]){ header("Location: error.php?ec=ad"); exit; } }
if ( !isset($_SESSION["role"]) || $_SESSION["role"] == "guest" ){ header("Location: error.php?ec=ad"); exit; }



	$report_id = $_GET["rid"];
	$member_id = $_GET["fmid"];

  /* Get the required data */
	$mysqli = createDatabaseConnection();

  /* Basic information and metrics */
  $facultyMember_scholar =  getFacultyMemberInReport($mysqli, $member_id, $report_id, "gscholar");
  $facultyMember_scopus = getFacultyMemberInReport($mysqli, $member_id, $report_id, "scopus");

  /* Publications */
	$gscholar_publications = getPublicationsOfFacultyMember($mysqli, $member_id, $report_id, "gscholar");
	$scopus_publications = getPublicationsOfFacultyMember($mysqli, $member_id, $report_id, "scopus");

  //if ( empty($scopus_publications) ) $facultyMember_scopus->q_metric = new MetricQ("n","n","n","n");
  $facultyMember_scopus->q_metric = getQMetricsForFacultyMember($facultyMember_scopus, $scopus_publications);

	$mysqli -> close();

	if ( empty($facultyMember_scholar) || empty($facultyMember_scopus) ){ header("Location: error.php?ec=gnr"); exit; }
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
  <?php echo getSideBarMenu($_SESSION["role"], "view_reports"); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo _MEMBER_IN_REPORT_TITLE;?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME; ?></a></li>
              <li class="breadcrumb-item"><a href="report.php?rid=<?php echo $report_id;?>"><?php echo _LABEL_REPORT; ?></a></li>
			  			<li class="breadcrumb-item"><?php echo $facultyMember_scholar->last_name.", ".$facultyMember_scholar->first_name; ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

				<div class="row">
					<div class="col-md-12">
						<div class="card card-dark">
							<div class="card-header">
								<h3 class="card-title"><?php echo _MEMBER_IN_REPORT_CARD_INFO_HEADER; ?></h3>
							</div><!-- /.card-header -->
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<dl>
											<dt><?php echo _LABEL_FULLNAME; ?></dt>
											<dd><?php echo $facultyMember_scholar->last_name.", ".$facultyMember_scholar->first_name; ?></dd>
											<dt><?php echo _LABEL_ROLE." ("._LABEL_RANK.")"; ?></dt>
											<dd><?php echo $facultyMember_scholar->role->name." (".$facultyMember_scholar->rank->full_title.")"; ?></dd>
											<dt><?php echo _LABEL_DEPARTMENT." ("._LABEL_SCHOOL.")"; ?></dt>
											<dd><?php echo $facultyMember_scholar->department->name." (".$facultyMember_scholar->department->school->name.")"; ?></dd>
										</dl>
									</div>
									<div class="col-md-4">
										<table class="table table-bordered table-hover">
											<thead>
												<tr>
												  <th scope="col"><?php echo _LABEL_METRIC; ?></th>
													<th scope="col"><?php echo _LABEL_SCHOLAR; ?></th>
													<th scope="col"><?php echo _LABEL_SCOPUS; ?></th>
												</tr>
												</thead>
											<tbody>
												<tr>
													<td><b><?php echo _LABEL_PUBLICATIONS; ?></b></td>
													<td><?php if (isset($facultyMember_scholar->metrics_metadata)) echo $facultyMember_scholar->author_profile->metric_total->publications; else echo _LABEL_NOT_AVAILABLE;?></td>
													<td><?php if (isset($facultyMember_scopus->metrics_metadata)) echo $facultyMember_scopus->author_profile->metric_total->publications; else echo _LABEL_NOT_AVAILABLE;?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_CITATIONS; ?></b></td>
                          <td><?php if (isset($facultyMember_scholar->metrics_metadata)) echo $facultyMember_scholar->author_profile->metric_total->citations; else echo _LABEL_NOT_AVAILABLE;?></td>
													<td><?php if (isset($facultyMember_scopus->metrics_metadata)) echo $facultyMember_scopus->author_profile->metric_total->citations; else echo _LABEL_NOT_AVAILABLE;?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_HINDEX; ?></b></td>
													<td><?php if (isset($facultyMember_scholar->metrics_metadata)) echo $facultyMember_scholar->author_profile->metric_total->hindex; else echo _LABEL_NOT_AVAILABLE;?></td>
													<td><?php if (isset($facultyMember_scopus->metrics_metadata)) echo $facultyMember_scopus->author_profile->metric_total->hindex; else echo _LABEL_NOT_AVAILABLE;?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_I10INDEX; ?></b></td>
                          <td><?php if (isset($facultyMember_scholar->metrics_metadata)) echo $facultyMember_scholar->author_profile->metric_total->i10index; else echo _LABEL_NOT_AVAILABLE;?></td>
													<td><?php if (isset($facultyMember_scopus->metrics_metadata)) echo $facultyMember_scopus->author_profile->metric_total->i10index; else echo _LABEL_NOT_AVAILABLE;?></td>
												</tr>
											</tbody>
										</table>
									</div>

									<div class="col-md-4">
										<table class="table table-bordered table-hover">
											<thead>
												<tr>
												  <th scope="col"><?php echo _LABEL_JOURNAL_Q; ?></th>
													<th scope="col"><?php echo _LABEL_NUMBER_OF_PUBLICATIONS; ?></th>
												</tr>
												</thead>
											<tbody>
												<tr>
													<td><b><?php echo _LABEL_Q1;?></b></td>
													<td><?php if (empty($scopus_publications)) echo _LABEL_NOT_AVAILABLE; else echo $facultyMember_scopus->q_metric->q1; ?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_Q2;?></b></td>
													<td><?php if (empty($scopus_publications)) echo _LABEL_NOT_AVAILABLE; else echo $facultyMember_scopus->q_metric->q2; ?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_Q3;?></b></td>
													<td><?php if (empty($scopus_publications)) echo _LABEL_NOT_AVAILABLE; else echo $facultyMember_scopus->q_metric->q3; ?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_Q4;?></b></td>
													<td><?php if (empty($scopus_publications)) echo _LABEL_NOT_AVAILABLE; else echo $facultyMember_scopus->q_metric->q4; ?></td>
												</tr>
												<tr>
													<td><b><?php echo _LABEL_Q1_Q2;?></b></td>
													<td><?php if (empty($scopus_publications)) echo _LABEL_NOT_AVAILABLE; else echo $facultyMember_scopus->q_metric->q1 + $facultyMember_scopus->q_metric->q2; ?></td>
												</tr>

											</tbody>
										</table>
									</div>
								</div>
                <!-- Info message -->
                <div class="row">
                   <div class="col-md-12">
                      <div class="callout callout-warning">
                         <h5><?php echo _MEMBER_IN_REPORT_WARNING_HEADER; ?></h5>
                         <p><?php echo _MEMBER_IN_REPORT_WARNING_MESSAGE; ?></p>
                      </div>
                   </div>
                </div>
							</div><!-- /.card-body -->
						</div><!-- /.card -->
					</div><!-- /.col-md-12 -->
				</div><!-- /.row -->

				<div class="row">
					<div class="col-md-12">
						<div class="card card-primary">
							<div class="card-header">
								<h3 class="card-title"><?php echo _MEMBER_IN_REPORT_CARD_SCHOLAR_HEADER; ?></h3>
								<div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i><span class='sr-only'>maximize</span></button>
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i><span class='sr-only'>collapse</span></button>
                </div><!-- /.card-tools -->
							</div><!-- /.card-header -->
							<div class="card-body">
								<table id="tblScholarPublications" class="table table-bordered table-hover">
									<thead>
										<tr>
										  <th scope="col"><?php echo _LABEL_PUBLICATION; ?></th>
											<th scope="col"><?php echo _LABEL_YEAR; ?></th>
											<th scope="col"><?php echo _LABEL_CITATIONS; ?></th>
										</tr>
                  </thead>
									<tbody>
										<?php
										foreach ($gscholar_publications as $p){
											if ($p->pub_year == "0000") $p->pub_year = "n/a";
											echo "<tr>
													  	<td>".$p->pub_authors.". (".$p->pub_year."). ".$p->pub_title.". <i>".$p->pub_venue."</i></td>
															<td>".$p->pub_year."</td>
															<td>".intval($p->pub_citedby)."</td>
														</tr>";
											}
										?>
									</tbody>
								</table>
							</div><!-- /.card-body -->
						</div><!-- /.card -->
					</div><!-- /.col-md-12 -->
				</div><!-- /.row -->





        <!-- =========================================================== -->
        <div class="row">



		  <div class="col-md-12">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title"><?php echo _MEMBER_IN_REPORT_CARD_SCOPUS_HEADER; ?></h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">




                <table id="tblScopusPublications" class="table table-bordered table-hover">
                  <thead>
					<tr>
					  <th scope="col"><?php echo _LABEL_PUBLICATION;?></th>
            <th scope="col"><?php echo _LABEL_TYPE;?></th>
						<th scope="col"><?php echo _LABEL_JOURNAL_Q;?></th>
            <th scope="col"><?php echo _LABEL_YEAR;?></th>
						<th scope="col"><?php echo _LABEL_CITATIONS;?></th>
					</tr>
                  </thead>
					<tbody>
						<?php
						foreach ($scopus_publications as $p){
							if (($p->pub_q == '0') || ($p->pub_q == '-')) $p->pub_q = "";
              //if ( $p->pub_type == "Journal" ) {
              echo "<tr><td>$p->pub_authors ($p->pub_year).$p->pub_title. <i>$p->pub_venue</i>.</td><td>$p->pub_type</td><td>$p->pub_q</td><td>$p->pub_year</td><td>$p->pub_citedby</td></tr>";
              //}
							//echo "<tr><td>$p->pub_title</td><td>$p->pub_year</td><td>$p->pub_type</td><td>$p->pub_venue</td><td>$p->pub_q</td><td>$p->pub_citedby</td></tr>";
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


      </div><!-- /.container-fluid -->
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
    $("#tblScopusPublications").DataTable({
      <?php if ($_SESSION["lang"] == "el"){ ?>
   		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
      <?php } ?>
      "responsive": true,
      "autoWidth": false,
	  "lengthChange": true,
      "dom": 'lfBtip',
      "buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print']
    });

	$("#tblScholarPublications").DataTable({
      <?php if ($_SESSION["lang"] == "el"){ ?>
        language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
      <?php } ?>
      "responsive": true,
      "autoWidth": false,
      "dom": 'lfBtip',
      "buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
      "columnDefs": [{ "type": "num", "targets": 2 }]
    });


  });


</script>

</body>
</html>
