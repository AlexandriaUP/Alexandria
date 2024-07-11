<?php
session_start();
require_once("components/login.php");
require_once("components/language.php");
require_once("../uppa_core/functions.php");
require_once("../uppa_core/settings/components.php");
require_once("../uppa_core/settings/year.php");

set_time_limit(240);

	/* Parameters */
	$report_id = isset($_GET["rid"]) ? intval($_GET["rid"]) : null;
	if (empty($report_id) || !is_numeric($report_id)) { header("Location: error.php?ec=gnr"); exit; }
	$type = "university_report";
	if (isset($_GET['sid'])) $type = "school_report";
	if (isset($_GET['did'])) $type = "department_report";

	$provider_id = "gscholar";
	$isScholarActive = "active";
	$isScopusActive = "";
	$providerLogo = "logoGScholar.png";
	if (isset($_GET['prv']) && ($_GET['prv'] == "scopus") ) {
		$provider_id = "scopus";
		$isScholarActive = "";
		$isScopusActive = "active";
		$providerLogo = "logoScopus.png";
	}


	$role_id = "all";
	$isRoleAllActive = "active"; $isRoleDepActive = ""; $isRoleEdipActive = "";
	if (isset($_GET['rl']) && ($_GET['rl'] == "dep") ) {
		$role_id = "dep";
		$isRoleAllActive = ""; $isRoleDepActive = "active"; $isRoleEdipActive = "";
	}
	if (isset($_GET['rl']) && ($_GET['rl'] == "edip") ) {
		$role_id = "edip";
		$isRoleAllActive = ""; $isRoleDepActive = ""; $isRoleEdipActive = "active";
	}

	/* Create sublinks */
	$fullLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$arrFullLink = parse_url($fullLink);
	parse_str($arrFullLink['query'], $arrFullLinkQuery);

	/* Scholar link */
	$subLinkQuery = $arrFullLinkQuery;
	unset($subLinkQuery['prv']);
	$subLinkQuery['prv'] = "gscholar";
	$scholarLink = http_build_query($subLinkQuery);

	/* Scopus link */
	$subLinkQuery = $arrFullLinkQuery;
	unset($subLinkQuery['prv']);
	$subLinkQuery['prv'] = "scopus";
	$scopusLink = http_build_query($subLinkQuery);

	/* All members link */
	$subLinkQuery = $arrFullLinkQuery;
	unset($subLinkQuery['rl']);
	$subLinkQuery['rl'] = "all";
	$allLink = http_build_query($subLinkQuery);

	/* DEP members link */
	$subLinkQuery = $arrFullLinkQuery;
	unset($subLinkQuery['rl']);
	$subLinkQuery['rl'] = "dep";
	$depLink = http_build_query($subLinkQuery);

	/* EDIP members link */
	$subLinkQuery = $arrFullLinkQuery;
	unset($subLinkQuery['rl']);
	$subLinkQuery['rl'] = "edip";
	$edipLink = http_build_query($subLinkQuery);


	/* Create connection */
	$mysqli = createDatabaseConnection();
	$report = getReport($mysqli, $report_id);

	if ( empty($report) ){ header("Location: error.php?ec=gnr"); exit; }

	$report_year = intval(DateTime::createFromFormat("Y-m-d H:i:s", $report->datetimeCreated)->format("Y"));


	if ($type == "university_report") {
		$unit = new Unit ("university", _UPAT);
		$subUnits = getSchools($mysqli);
		$ranks = getRanks($mysqli);


		$metrics = getFacultyMembersInReport($mysqli, $report_id, $role_id, $provider_id);
		$metricsPerUnit = getAggregateMetricsFromAllFacultyMembers($metrics, "university", NULL, $report_year);
		$metricsPerSubUnit = getAggregateMetricsFromAllFacultyMembers($metrics, "school", $subUnits, $report_year);
		$metricsPerRank = getAggregateMetricsFromAllFacultyMembers($metrics, "rank", $ranks, $report_year);
		$metrics210Profiles_all = getTop210ProfilesCitations($mysqli, $report_id, $provider_id);

	}
	else if ($type == "school_report") {
		$school_id =  $_GET["sid"];
		$school = getSchool($mysqli, $school_id);
		$unit = new Unit ($school->id, $school->name, new Unit ("university", _UPAT));
		$subUnits = getDepartments($mysqli, $report->datetimeCreated);
		$ranks = getRanks($mysqli);

		$metrics = getFacultyMembersInReport($mysqli, $report_id, $role_id, $provider_id);
		$metrics = extractMembersOfSchool($metrics, $school_id);
		$metricsPerUnit = getAggregateMetricsFromAllFacultyMembers($metrics, "university", NULL, $report_year);
		$metricsPerSubUnit = getAggregateMetricsFromAllFacultyMembers($metrics, "department", $subUnits, $report_year);
		$metricsPerRank = getAggregateMetricsFromAllFacultyMembers($metrics, "rank", $ranks, $report_year);
		$metrics210Profiles_all = NULL;

	}
	else if ($type == "department_report") {
		$department_id =  $_GET["did"];
		$department = getDepartment($mysqli, $department_id, $report->datetimeCreated);
		$unit = new Unit ($department->id, $department->name );
		$unit->parentUnit = new Unit($department->school->id, $department->school->name);
		$subUnits = NULL;
		$ranks = getRanks($mysqli);

		$metrics = getFacultyMembersInReport($mysqli, $report_id, $role_id, $provider_id);
		$metrics = extractMembersOfDepartment($metrics, $department_id);
		$metricsPerUnit = getAggregateMetricsFromAllFacultyMembers($metrics, "university", NULL, $report_year);
		$metricsPerSubUnit = NULL;
		$metricsPerRank = getAggregateMetricsFromAllFacultyMembers($metrics, "rank", $ranks, $report_year);
		$metrics210Profiles_all = NULL;

	}


	$mysqli -> close();

	?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'];?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo _ALEXANDRIA." "._VERSION_NUMBER ." | ". $uppa_page_title['view_report']; ?></title>
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
  <?php echo getSideBarMenu($_SESSION["role"], "reports"); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
					<div class="col-sm-6">
            <h1><?php echo $report->title;?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="reports.php"><?php echo _HOME;?></a></li>
			  			<li class="breadcrumb-item"><?php echo $uppa_page_title['view_report']; ?></li>
              <li class="breadcrumb-item active"><?php echo $report->title ?></li>
            </ol>
          </div>


        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
    	<div class="container-fluid">



        <div class="mb-2 mt-4">
			<?php
			$org = _UPAT;
			if ($type == "university_report") echo $unit->name;
			else if ($type == "school_report") echo "<a href='report.php?rid=$report_id'>$org</a> &rarr; $unit->name";
			else if ($type == "department_report") echo "<a href='report.php?rid=$report_id'>$org</a> &rarr; <a href='report.php?rid=$report_id&sid=".$unit->parentUnit->id."'>".$unit->parentUnit->name."</a> &rarr; $unit->name";
			?>
		</div>

			<div class="card card-white" >
				<div class="card-body table-responsive pad">
					<div class="row">
						<div class="col-md-6 text-center">
							<div><?php echo _LABEL_PROVIDER;?></div>
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<a class="btn btn-secondary <?php echo $isScholarActive; ?>" href="?<?php echo $scholarLink;?>"><?php echo _LABEL_SCHOLAR;?></a>
								<a class="btn btn-secondary <?php echo $isScopusActive; ?>" href="?<?php echo $scopusLink;?>"><?php echo _LABEL_SCOPUS;?></a>
							</div>
						</div>
						<div class="col-md-6 text-center">
							<div><?php echo _LABEL_ROLE;?></div>
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<a class="btn btn-secondary <?php echo $isRoleAllActive; ?>" href="?<?php echo $allLink;?>"><?php echo _LABEL_ALL;?></a>
								<a class="btn btn-secondary <?php echo $isRoleDepActive; ?>" href="?<?php echo $depLink;?>"><?php echo _LABEL_DEP;?></a>
								<a class="btn btn-secondary <?php echo $isRoleEdipActive; ?>" href="?<?php echo $edipLink;?>"><?php echo _LABEL_EDIP;?></a>
							</div>
						</div>
					</div>
              </div>
			</div>

		<div class="card card-white" >
			<div class="card-header">
				<h3 class="card-title"><img alt="data provider logo" src="../dist/img/<?php echo $providerLogo;?>" style="height:30px;"></h3>
          		<div class="card-tools">
            		<button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand" style="color: #000;"></i><span class='sr-only'>maximize</span></button>
            		<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus" style="color: #000;"></i><span class='sr-only'>collapse</span></button>
          		</div>
        	</div>
        	<div class="card-body" style="display: block;">
					<?php
						$tbl_provider_id = $provider_id;
						$tbl_role_id = $role_id;
						$tPrefix = $tbl_provider_id."-".$tbl_role_id;
						$fmMetrics = $metrics;
						$metrics_210 = $metrics210Profiles_all;
						include("components/report_table_with_stats.php");
					?>
        	</div><!-- /.card-body -->
      	</div><!-- /.card -->



		<div class="modal fade" id="modal-facultymember-column-info">
    		<div class="modal-dialog">
          		<div class="modal-content bg-secondary">
            		<div class="modal-header">
              			<h4 class="modal-title">Επεξήγηση στηλών</h4>
              			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                		<span aria-hidden="true">&times;</span></button>
            		</div>
            		<div class="modal-body">
              			<table>
							<tbody>
								<tr><td><strong>ΕΚΔ: </strong></td><td>Έτος κτήσης διδακτορικού διπλώματος</td></tr>
								<tr><td><strong><?php echo _TABLE_STATS_METRIC_PUBLICATIONS_ABBREVIATION; ?>: </strong></td><td>Αριθμός δημοσιεύσεων</td></tr>
								<tr><td><strong><?php echo _TABLE_STATS_METRIC_CITATIONS_ABBREVIATION; ?>: </strong></td><td>Αριθμός αναφορών</td></tr>
								<tr><td><strong>h: </strong></td><td>Δείκτης h</td></tr>
								<tr><td><strong>i10: </strong></td><td>Δείκτης i10</td></tr>
								<tr><td><strong>m: </strong></td><td>Δείκτης m</td></tr>
								<tr><td><strong>ΠΑΑ: </strong></td><td>Αριθμός περισσότερων αναφορών σε δημοσίευση</td></tr>
							</tbody>
			  			</table>
            		</div>
            		<div class="modal-footer justify-content-between">
              			<button type="button" class="btn btn-outline-light" data-dismiss="modal">Κλείσιμο</button>
            		</div>
          		</div>
          <!-- /.modal-content -->
        	</div>
        <!-- /.modal-dialog -->
      	</div>
      <!-- /.modal -->

	</div>
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
<!-- Chart -->
<script src="../plugins/chart.js/Chart.min.js"></script>
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
  $(document).ready(function() {

	var <?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active = '<?php echo $tPrefix;?>-unitStats-meanScores-tabContent';
	var <?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active = '<?php echo $tPrefix;?>-subUnitStats-meanScores-tabContent';
	var <?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active = '<?php echo $tPrefix;?>-rankStats-meanScores-tabContent';
	var <?php echo str_replace('-','_',$tPrefix);?>_facultyMemberStats_tab = '<?php echo $tPrefix;?>-facultyMemberStats-tabContent';

	$( "#<?php echo $tPrefix;?>-unitStats-tab" ).click(function() {
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#' + <?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active).css( 'display', 'block' );
	});

	$( "#<?php echo $tPrefix;?>-subUnitStats-tab" ).click(function() {
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#' + <?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active).css( 'display', 'block' );
	});

	$( "#<?php echo $tPrefix;?>-rankStats-tab" ).click(function() {
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#' + <?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active).css( 'display', 'block' );
	});

	$( "#<?php echo $tPrefix;?>-facultyMemberStats-tab" ).click(function() {
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#' + <?php echo str_replace('-','_',$tPrefix);?>_facultyMemberStats_tab_active).css( 'display', 'block' );
	});

	$( "#<?php echo $tPrefix;?>-top210-tab" ).click(function() {
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>UnitTotalScores = $("#tbl<?php echo $tPrefix;?>UnitTotalScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"ordering": false,
		"dom": 'Bt',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print']
	});

	$( "#<?php echo $tPrefix;?>-unitStats-totalScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active = '<?php echo $tPrefix;?>-unitStats-totalScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-unitStats-totalScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>UnitTotalScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>UnitQ3Scores = $("#tbl<?php echo $tPrefix;?>UnitQ3Scores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"ordering": false,
		"dom": 'Bt',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print']
	});

	$( "#<?php echo $tPrefix;?>-unitStats-q3Scores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active = '<?php echo $tPrefix;?>-unitStats-q3Scores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-unitStats-q3Scores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>UnitQ3Scores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>UnitMeanScores = $("#tbl<?php echo $tPrefix;?>UnitMeanScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"ordering": false,
		"dom": 'Bt',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print']
	});

	$( "#<?php echo $tPrefix;?>-unitStats-meanScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active = '<?php echo $tPrefix;?>-unitStats-meanScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-unitStats-meanScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>UnitMeanScores.columns.adjust().draw();
	});

	var tbl<?php str_replace('-','_',$tPrefix);?>UnitMedianScores = $("#tbl<?php echo $tPrefix;?>UnitMedianScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"ordering": false,
		"dom": 'Bt',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print']
	});

	$( "#<?php echo $tPrefix;?>-unitStats-medianScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active = '<?php echo $tPrefix;?>-unitStats-medianScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-unitStats-medianScores-tabContent').css( 'display', 'block' );
		tbl<?php str_replace('-','_',$tPrefix);?>UnitMedianScores.columns.adjust().draw();
	});

	var tbl<?php str_replace('-','_',$tPrefix);?>UnitQ1Scores = $("#tbl<?php echo $tPrefix;?>UnitQ1Scores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"ordering": false,
		"dom": 'Bt',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print']
	});

	$( "#<?php echo $tPrefix;?>-unitStats-q1Scores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_unitStats_tab_active = '<?php echo $tPrefix;?>-unitStats-q1Scores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-unitStats-q1Scores-tabContent').css( 'display', 'block' );
		tbl<?php str_replace('-','_',$tPrefix);?>UnitQ1Scores.columns.adjust().draw();
	});
	
	var tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitMeanScores = $("#tbl<?php echo $tPrefix;?>SubUnitMeanScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-subUnitStats-meanScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active = '<?php echo $tPrefix;?>-subUnitStats-meanScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-subUnitStats-meanScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitMeanScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitMedianScores = $("#tbl<?php echo $tPrefix;?>SubUnitMedianScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-subUnitStats-medianScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active = '<?php echo $tPrefix;?>-subUnitStats-medianScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-subUnitStats-medianScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitMedianScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitQ1Scores = $("#tbl<?php echo $tPrefix;?>SubUnitQ1Scores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-subUnitStats-q1Scores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active = '<?php echo $tPrefix;?>-subUnitStats-q1Scores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-subUnitStats-q1Scores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitQ1Scores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitQ3Scores = $("#tbl<?php echo $tPrefix;?>SubUnitQ3Scores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-subUnitStats-q3Scores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active = '<?php echo $tPrefix;?>-subUnitStats-q3Scores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-subUnitStats-q3Scores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitQ3Scores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitTotalScores = $("#tbl<?php echo $tPrefix;?>SubUnitTotalScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-subUnitStats-totalScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_subUnitStats_tab_active = '<?php echo $tPrefix;?>-subUnitStats-totalScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-subUnitStats-totalScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>SubUnitTotalScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitMeanScores = $("#tbl<?php echo $tPrefix;?>RankStatsSubUnitMeanScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-rankStats-meanScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active = '<?php echo $tPrefix;?>-rankStats-meanScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-rankStats-meanScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitMeanScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitMedianScores = $("#tbl<?php echo $tPrefix;?>RankStatsSubUnitMedianScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-rankStats-medianScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active = '<?php echo $tPrefix;?>-rankStats-medianScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-rankStats-medianScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitMedianScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitQ1Scores = $("#tbl<?php echo $tPrefix;?>RankStatsSubUnitQ1Scores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-rankStats-q1Scores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active = '<?php echo $tPrefix;?>-rankStats-q1Scores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-rankStats-q1Scores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitQ1Scores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitQ3Scores = $("#tbl<?php echo $tPrefix;?>RankStatsSubUnitQ3Scores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-rankStats-q3Scores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active = '<?php echo $tPrefix;?>-rankStats-q3Scores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-rankStats-q3Scores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitQ3Scores.columns.adjust().draw();
	});
	
	var tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitTotalScores = $("#tbl<?php echo $tPrefix;?>RankStatsSubUnitTotalScores").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-rankStats-totalScores-tab" ).click(function() {
		<?php echo str_replace('-','_',$tPrefix);?>_rankStats_tab_active = '<?php echo $tPrefix;?>-rankStats-totalScores-tabContent';
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-rankStats-totalScores-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>RankStatsSubUnitTotalScores.columns.adjust().draw();
	});

	var tbl<?php echo str_replace('-','_',$tPrefix);?>FacultyMembers = $("#tbl<?php echo $tPrefix;?>FacultyMembers").DataTable({
		language: { url: 'https://cdn.datatables.net/plug-ins/1.12.0/i18n/el.json' },
		"responsive": false,
		"autoWidth": true,
		"scrollX": true,
		"dom": 'lfBtip',
		"buttons": ['copy', 'csv', 'excel', { extend:'pdfHtml5', orientation: 'landscape'}, 'print'],
		"columnDefs": [
			{ "type": "string", "targets": 0 },
			{ "type": "num", "targets": "_all" }
		]
	});

	$( "#<?php echo $tPrefix;?>-facultyMemberStats-tab" ).click(function() {
		$('[role|="tabpanel"][style="display: block;"]').css( 'display', 'none' );
		$('#<?php echo $tPrefix;?>-facultyMemberStats-tabContent').css( 'display', 'block' );
		tbl<?php echo str_replace('-','_',$tPrefix);?>FacultyMembers.columns.adjust().draw();
	});

});

</script>

</body>
</html>
