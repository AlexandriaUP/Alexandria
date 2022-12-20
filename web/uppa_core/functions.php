<?php
/* Settings */
require_once(dirname(__DIR__)."/settings.php");

/* Classes */
require_once("classes/Department.php");
require_once("classes/FacultyMember.php");
require_once("classes/Metric.php");
require_once("classes/MetricArray.php");
require_once("classes/MetricQ.php");
require_once("classes/Rank.php");
require_once("classes/Role.php");
require_once("classes/Report.php");
require_once("classes/School.php");
require_once("classes/Message.php");
require_once("classes/Unit.php");
require_once("classes/Publication.php");
require_once("classes/AuthorProfile.php");


/**
	Get all reports from Alexandria DB
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@return: array of Report ($reports) - An array with the matched reports
**/
function getReports($mysqli){
	$reports = array();
	$sql = "SELECT *,
					(SELECT COUNT(*) FROM `faculty_member_in_report` AS `FMiR` WHERE `FMiR`.`report_id`=`report`.`report_id` AND `FMiR`.`provider_id`='gscholar') AS scholar_entries,
					(SELECT COUNT(*) FROM `faculty_member_in_report` AS `FMiR` WHERE `FMiR`.`report_id`=`report`.`report_id` AND `FMiR`.`provider_id`='gscholar' AND `FMiR`.`metrics_metadata` IS NULL) AS scholar_entries_empty,
					(SELECT COUNT(*) FROM `faculty_member_in_report` AS `FMiR` WHERE `FMiR`.`report_id`=`report`.`report_id` AND `FMiR`.`provider_id`='gscholar' AND (`FMiR`.`info_metadata` LIKE '%\"scholar_id\":\"\"%' OR `FMiR`.`info_metadata` LIKE '%\"scholar_id\":null%')) AS members_with_no_scholar,
					(SELECT COUNT(*) FROM `faculty_member_in_report` AS `FMiR` WHERE `FMiR`.`report_id`=`report`.`report_id`AND `FMiR`.`provider_id`='scopus') AS scopus_entries,
					(SELECT COUNT(*) FROM `faculty_member_in_report` AS `FMiR` WHERE `FMiR`.`report_id`=`report`.`report_id`AND `FMiR`.`provider_id`='scopus' AND `FMiR`.`metrics_metadata` IS NULL) AS scopus_entries_empty,
					(SELECT COUNT(*) FROM `faculty_member_in_report` AS `FMiR` WHERE `FMiR`.`report_id`=`report`.`report_id` AND `FMiR`.`provider_id`='scopus' AND (`FMiR`.`info_metadata` LIKE '%\"scopus_id\":\"\"%' OR `FMiR`.`info_metadata` LIKE '%\"scopus_id\":null%')) AS members_with_no_scopus
					FROM `report`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$report = new Report($row["report_id"], $row["report_title"], $row["report_datetime_created"]);
			$progress_scholar = (intval($row["scholar_entries"]) - intval($row["scholar_entries_empty"]) + intval($row["members_with_no_scholar"]))*100/intval($row["scholar_entries"]);
			$progress_scopus  = (intval($row["scopus_entries"])  - intval($row["scopus_entries_empty"])  + intval($row["members_with_no_scopus"])) *100/intval($row["scopus_entries"]);
			$progress_agg 		= ($progress_scholar + $progress_scopus)/2;
			$report->progress = round($progress_agg, 2);
			array_push($reports, $report);
		}
		$result -> free_result();
	}
	return $reports;
}



/**
	Gets a specific report
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@param: string ($report_id) - The ID of the selected report
	@return: Report - The selected report
**/
function getReport($mysqli, $report_id){
	$sql = "SELECT * FROM `report` WHERE `report`.`report_id`='$report_id'";
	$report = null;
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$report = new Report($row["report_id"], $row["report_title"], $row["report_datetime_created"]);
		}
		$result -> free_result();
	}
	return $report;
}

/**
	Creates a new report
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@param: string ($report_title) - The title of the report
	@return: Report - The selected report
**/
function createNewReport($mysqli, $report_title){
	/* Get all faculty members and current date and time */
	$facultyMembers = getFacultyMembers($mysqli);
	$datetime = new DateTime("now", new DateTimeZone('Europe/Athens'));
	$datetime = $datetime->format("Y-m-d H:i:s");

	/* Create the entry in the Report table */
	$mysqli -> query("INSERT INTO `report`(`report_title`,`report_datetime_created`) VALUES ('$report_title', '$datetime')");
	$report_id =  $mysqli-> insert_id;

	/* Fill the Faculty-member-in-report table for Google Scholar */
	$query = "INSERT INTO `faculty_member_in_report` (	`report_id`, `provider_id`, `facultymember_id`, `info_metadata`) VALUES ($report_id, 'gscholar', ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt ->bind_param("ss", $fmid, $info_metadata);
	$mysqli->query("START TRANSACTION");
	foreach ($facultyMembers as $fm) {
		$fmid = $fm->id;
		$info_metadata = json_encode($fm, JSON_UNESCAPED_UNICODE);
	  $stmt->execute();
	}
	$stmt->close();
	$mysqli->query("COMMIT");

	/* Do the same for Scopus */
	$query = "INSERT INTO `faculty_member_in_report` (	`report_id`, `provider_id`, `facultymember_id`, `info_metadata`) VALUES ($report_id, 'scopus', ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt ->bind_param("ss", $fmid, $info_metadata);
	$mysqli->query("START TRANSACTION");
	foreach ($facultyMembers as $fm) {
		$fmid = $fm->id;
		$info_metadata = json_encode($fm, JSON_UNESCAPED_UNICODE);
	  $stmt->execute();
	}
	$stmt->close();
	$mysqli->query("COMMIT");

	/* Return the report*/
	$report = new Report($report_id, $report_title, $datetime);
	return $report;
}



/**
	Get all faculty members
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@return: array of FacultyMember ($facultyMembers) - An array with the matched faculty members
**/
function getFacultyMembers($mysqli){
	$sql = "SELECT `FM`.*, `DPT`.*, `DPT_INFO`.*, `RNK`.*, `SCH`.*, `ROLE`.*
				  FROM `faculty_member` AS `FM`, `department` AS `DPT`, `department_info` AS `DPT_INFO`, `rank` AS `RNK`, `school` AS `SCH`, `role` AS `ROLE`
					WHERE `FM`.`rank` = `RNK`.`rank_id` AND `FM`.`department` = `DPT`.`dpt_id` AND `DPT`.`dpt_id` = `DPT_INFO`.`dptid` AND `DPT_INFO`.`valid_until` IS NULL AND `DPT`.`dpt_school_id` = `SCH`.`school_id` AND `ROLE`.`role_id` = `FM`.`role` AND `FM`.`isActive` = 1
					ORDER BY `FM`.`last_name` ASC";
	$facultyMembers = array();
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$facultyMember = new FacultyMember(
				$row["id"],
				$row["first_name"],
				$row["last_name"],
				$row["google_scholar_id"],
				$row["scopus_id"],
				new Department($row["department"], $row["dpt_full_name"], new School($row["dpt_school_id"], $row["school_name"])),
				new Rank($row["rank_id"], $row["rank_full_title"], $row["rank_short_title"], $row["rank_order_id"]),
				new Role($row["role_id"], $row["role_name"], $row["role_order_id"]),
				$row["phd_year"],
				$row["isValidated"]
				);
			array_push($facultyMembers, $facultyMember);
		}
		$result -> free_result();
	}
	return $facultyMembers;
}

function getFacultyMemberById($mysqli, $fmid){
	$sql = "SELECT `FM`.*, `DPT`.*, `DPT_INFO`.*, `RNK`.*, `SCH`.*, `ROLE`.*
				  FROM `faculty_member` AS `FM`, `department` AS `DPT`, `department_info` AS `DPT_INFO`, `rank` AS `RNK`, `school` AS `SCH`, `role` AS `ROLE`
					WHERE `FM`.`rank` = `RNK`.`rank_id` AND `FM`.`department` = `DPT`.`dpt_id` AND `DPT`.`dpt_id` = `DPT_INFO`.`dptid` AND `DPT_INFO`.`valid_until` IS NULL AND `DPT`.`dpt_school_id` = `SCH`.`school_id` AND `ROLE`.`role_id` = `FM`.`role`
					AND `FM`.`id` = '$fmid'";
	$facultyMember = null;
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$facultyMember = new FacultyMember(
				$row["id"],
				$row["first_name"],
				$row["last_name"],
				$row["google_scholar_id"],
				$row["scopus_id"],
				new Department($row["department"], $row["dpt_full_name"], new School($row["dpt_school_id"], $row["school_name"])),
				new Rank($row["rank_id"], $row["rank_full_title"], $row["rank_short_title"], $row["rank_order_id"]),
				new Role($row["role_id"], $row["role_name"], $row["role_order_id"]),
				$row["phd_year"],
				$row["isValidated"]
				);
		}
		$result -> free_result();
	}
	return $facultyMember;
}

/**
	Get faculty member if in report
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@param: string ($member_id) - The ID of the member
	@param: string ($report_id) - The ID of the report
	@param: string ($provider_id) - The ID of the provider (e.g., gscholar, scopus)
	@return: Faculty Member ($fm) - The matched faculty member
**/
function getFacultyMemberInReport($mysqli, $member_id, $report_id, $provider_id){
	$sql = "SELECT * FROM `faculty_member_in_report`
					WHERE `facultymember_id`='$member_id' AND `report_id`='$report_id' AND `provider_id`='$provider_id'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$jsonInfoMetadata = json_decode($row["info_metadata"]);
			$facultyMember = new FacultyMember(
				$jsonInfoMetadata->id,
				$jsonInfoMetadata->first_name,
				$jsonInfoMetadata->last_name,
				$jsonInfoMetadata->scholar_id,
				$jsonInfoMetadata->scopus_id,
				new Department($jsonInfoMetadata->department->id,
											 $jsonInfoMetadata->department->name,
											 new School($jsonInfoMetadata->department->school->id,
											 					  $jsonInfoMetadata->department->school->name)),
				new Rank($jsonInfoMetadata->rank->id,
							   $jsonInfoMetadata->rank->full_title,
								 $jsonInfoMetadata->rank->shorter_title,
								 $jsonInfoMetadata->rank->order_id),
				new Role($jsonInfoMetadata->role->id,
								 $jsonInfoMetadata->role->name,
								 $jsonInfoMetadata->role->order_id),
				$jsonInfoMetadata->phd_year,
				$jsonInfoMetadata->isValidated
				);

			//print_r($row["info_metadata"]);
			$facultyMember->info_metadata = $row["info_metadata"];
			$facultyMember->metrics_metadata = $row["metrics_metadata"];
			$authorProfile = new AuthorProfile();
			if ( $row["metrics_metadata"]!= NULL && $row["metrics_metadata"]!= "" ){
				$jsonMetricsMetadata = json_decode($row["metrics_metadata"]);
				//print_r($jsonMetricsMetadata);
				$metric_total = new Metric($jsonMetricsMetadata->metrics_total->publications,
																	 $jsonMetricsMetadata->metrics_total->citations,
																	 $jsonMetricsMetadata->metrics_total->hindex,
																	 $jsonMetricsMetadata->metrics_total->i10index);
				$metric_total->mindex = $jsonMetricsMetadata->metrics_misc->mindex;
				$metric_total->most_paper_citations = $jsonMetricsMetadata->metrics_misc->most_paper_citations;

				$authorProfile->metric_total = $metric_total;
			}


			/* Assign the author profile to the member */
			$facultyMember->author_profile = $authorProfile;
			break;
		}
	}
	return $facultyMember;
}

function getFacultyMembersInReport($mysqli, $report_id, $role_id, $provider_id){
	$roleParameter = "";
	if ($role_id == "dep") $roleParameter = "AND `info_metadata` LIKE '%\"dep\"%'";
	else if ($role_id == "edip") $roleParameter = "AND `info_metadata` LIKE '%\"edip\"%'";
	$facultyMembers = array();
	$sql = "SELECT * FROM `faculty_member_in_report` WHERE `report_id`='$report_id' AND `provider_id`='$provider_id' $roleParameter";
	//echo $sql;
	//exit;
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			/* Create the Faculty Member */
			$jsonInfoMetadata = json_decode($row["info_metadata"]);
			$facultyMember = new FacultyMember(
				$jsonInfoMetadata->id,
				$jsonInfoMetadata->first_name,
				$jsonInfoMetadata->last_name,
				$jsonInfoMetadata->scholar_id,
				$jsonInfoMetadata->scopus_id,
				new Department($jsonInfoMetadata->department->id,
											 $jsonInfoMetadata->department->name,
											 new School($jsonInfoMetadata->department->school->id,
											 					  $jsonInfoMetadata->department->school->name)),
				new Rank($jsonInfoMetadata->rank->id,
							   $jsonInfoMetadata->rank->full_title,
								 $jsonInfoMetadata->rank->shorter_title,
								 $jsonInfoMetadata->rank->order_id),
				new Role($jsonInfoMetadata->role->id,
								 $jsonInfoMetadata->role->name,
								 $jsonInfoMetadata->role->order_id),
				$jsonInfoMetadata->phd_year,
				$jsonInfoMetadata->isValidated
				);

				$facultyMember->info_metadata = $row["info_metadata"];
				$facultyMember->metrics_metadata = $row["metrics_metadata"];

				if ( $row["metrics_metadata"]!= NULL && $row["metrics_metadata"]!= "" ){
					$authorProfile = new AuthorProfile();
					$jsonMetricsMetadata = json_decode($row["metrics_metadata"]);

					/* Get total metric */
					$metric_total = new Metric($jsonMetricsMetadata->metrics_total->publications,
																		 $jsonMetricsMetadata->metrics_total->citations,
																		 $jsonMetricsMetadata->metrics_total->hindex,
																		 $jsonMetricsMetadata->metrics_total->i10index);
					$metric_total->mindex = $jsonMetricsMetadata->metrics_misc->mindex;
					$metric_total->most_paper_citations = $jsonMetricsMetadata->metrics_misc->most_paper_citations;
					$authorProfile->metric_total = $metric_total;

					/* Get 5y metric */
					$metric_5yrs = new Metric($jsonMetricsMetadata->metrics_5y->publications,
																		$jsonMetricsMetadata->metrics_5y->citations,
																		$jsonMetricsMetadata->metrics_5y->hindex,
																		$jsonMetricsMetadata->metrics_5y->i10index);
					$authorProfile->metric_5y = $metric_5yrs;

					/* Get publications per year */
					$authorProfile->publications_per_year = $jsonMetricsMetadata->publications_per_year;

					if ($provider_id == "scopus"){
						$qs = getScimagoQForFacultyMemberInReport($mysqli, $report_id, $facultyMember->id);
						//$pubs = getPublicationsOfFacultyMember($mysqli, $facultyMember->id, $report_id, $provider_id);

						$authorProfile->pubsQ1Q2 = intval($qs[0]) + intval($qs[1]);
						$authorProfile->pubsJournals = $qs[4];
						//break;
					}

					/* Assign author profile */
					$facultyMember->author_profile = $authorProfile;
				}


			array_push($facultyMembers, $facultyMember);
		}
	}

	return $facultyMembers;
}


function getScimagoQForFacultyMemberInReport($mysqli, $report_id, $fm_id){
	$qs = array(0, 0, 0, 0, 0);

	$sql = "SELECT `scimagojr_q` FROM `publication_of_faculty_member_in_report` WHERE `pub_subtype` = 'ar' AND `report_id`='$report_id' AND `facultymember_id`='$fm_id' AND `provider_id`='scopus'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			if ($row['scimagojr_q'] == "Q1") $qs[0]++;
			else if ($row['scimagojr_q'] == "Q2") $qs[1]++;
			else if ($row['scimagojr_q'] == "Q3") $qs[2]++;
			else if ($row['scimagojr_q'] == "Q4") $qs[3]++;
			else $qs[4]++;
		}
	}
	/*
	$sql = "SELECT
	  			(SELECT COUNT(*) FROM `publication_of_faculty_member_in_report`  WHERE `scimagojr_q` = 'Q1' AND `report_id`='$report_id' AND `facultymember_id`='$fm_id' AND `provider_id`='scopus') AS `q1`,
					(SELECT COUNT(*) FROM `publication_of_faculty_member_in_report`  WHERE `scimagojr_q` = 'Q2' AND `report_id`='$report_id' AND `facultymember_id`='$fm_id' AND `provider_id`='scopus') AS `q2`,
					(SELECT COUNT(*) FROM `publication_of_faculty_member_in_report`  WHERE `scimagojr_q` = 'Q3' AND `report_id`='$report_id' AND `facultymember_id`='$fm_id' AND `provider_id`='scopus') AS `q3`,
					(SELECT COUNT(*) FROM `publication_of_faculty_member_in_report`  WHERE `scimagojr_q` = 'Q4' AND `report_id`='$report_id' AND `facultymember_id`='$fm_id' AND `provider_id`='scopus') AS `q4`,
					(SELECT COUNT(*) FROM `publication_of_faculty_member_in_report`  WHERE `pub_subtype` = 'ar' AND `report_id`='$report_id' AND `facultymember_id`='$fm_id' AND `provider_id`='scopus') AS `all`
					";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$qs[0] = intval($row['q1']);
			$qs[1] = intval($row['q2']);
			$qs[2] = intval($row['q3']);
			$qs[3] = intval($row['q4']);
			$qs[4] = intval($row['all']);
		}
	}
	*/
	return $qs;

}

/**
 For each faculty member, it returns the status of each scholar provider
 1 = the faculty member has no Provider ID
 2 = the faculty member (with valid Provider ID) has not been updated yet
 3 = the faculty memebr (with valid Provider ID) has been updated
 4 = there was a problem with that faculty member
**/
function getReportStatusOfFacultyMembers($mysqli, $report_id){
		// sql query
		$sql = "SELECT DISTINCT fmir.facultymember_id AS id, fm.last_name, fm.first_name, fm.google_scholar_id, fm.scopus_id, fm.phd_year,
		(SELECT metrics_metadata FROM faculty_member_in_report AS fmirScholar WHERE fmir.facultymember_id = fmirScholar.facultymember_id AND provider_id='gscholar' AND report_id='$report_id') As `scholar_metadata`,
		(SELECT metrics_metadata FROM faculty_member_in_report AS fmirScopus WHERE fmir.facultymember_id = fmirScopus.facultymember_id AND provider_id='scopus' AND report_id='$report_id') As `scopus_metadata`
		FROM faculty_member_in_report AS fmir, faculty_member as fm
		WHERE fmir.facultymember_id = fm.id AND report_id='$report_id' ORDER BY fm.last_name, fm.first_name";

		$facultyMembersWithMetrics = array();
		if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$fm = new FacultyMember(
				$row["id"],
				$row["first_name"],
				$row["last_name"],
				$row["google_scholar_id"],
				$row["scopus_id"],
				NULL,
				NULL,
				NULL,
				$row["phd_year"],
				NULL
			);


			$fm->scholar_metadata = $row['scholar_metadata'];
			$fm->scopus_metadata = $row['scopus_metadata'];
			$fm->report_id = $report_id;

			if ( ($fm->scholar_id == "") OR  ($fm->scholar_id == NULL) ){
				$fm->report_scholar_status = 'no id';
			} else if ($fm->scholar_metadata == NULL){
				$fm->report_scholar_status = 'open';
			} else if ($fm->scholar_metadata != ""){
				$fm->report_scholar_status = 'completed';
			} else {
				$fm->report_scholar_status = 'error';
			}

			if ( ($fm->scopus_id == "") OR  ($fm->scopus_id == NULL) ){
				$fm->report_scopus_status = 'no id';
			} else if ($fm->scopus_metadata == NULL){
				$fm->report_scopus_status = 'open';
			} else if ($fm->scopus_metadata != ""){
				$fm->report_scopus_status = 'completed';
			} else {
				$fm->report_scopus_status = 'error';
			}
			array_push($facultyMembersWithMetrics, $fm);
		}
		$result -> free_result();
	}
	return $facultyMembersWithMetrics;
}

function getQMetricsForFacultyMember($facultyMember, $publications){
	$q_metric = new MetricQ(0,0,0,0);
	foreach ($publications as $p){
		if ($p->pub_q == "Q1") $q_metric->q1++;
		else if ($p->pub_q == "Q2") $q_metric->q2++;
		else if ($p->pub_q == "Q3") $q_metric->q3++;
		else if ($p->pub_q == "Q4") $q_metric->q4++;
	}
	return $q_metric;
}

/**
	Get all ranks (e.g., professor, assistant_professor)
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@return: array of Rank ($ranks) - An array with the matched ranks
**/
function getRanks($mysqli){
	$ranks = array();
	$sql = "SELECT `rank`.* FROM `rank` ORDER BY `rank_order_id` ASC";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$rank = new Rank($row["rank_id"], $row["rank_full_title"], $row["rank_short_title"], $row["rank_order_id"]);
			array_push($ranks, $rank);
		}
		$result -> free_result();
	}
	return $ranks;
}

/**
	Get all departments of the organization
	@param: mysqli ($mysqli) - The connection with the Alexandria database
	@return: array of Department ($departments) - An array with the matched departments
**/
function getDepartments($mysqli, $reference_time = null){
	if(is_null($reference_time)) {
		$q = "`department_info`.`valid_until` IS NULL";
	} else {
		$q = "( (`department_info`.`valid_from` <= STR_TO_DATE('".$reference_time."', '%Y-%m-%d %H:%i:%s') AND `department_info`.`valid_until` IS NULL) OR (`department_info`.`valid_from` <= STR_TO_DATE('".$reference_time."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$reference_time."', '%Y-%m-%d %H:%i:%s') <= `department_info`.`valid_until`) )";
	}

	$sql = "SELECT * FROM `department`, `department_info`, `school` WHERE `department`.`dpt_school_id` = `school`.`school_id` AND `department`.`dpt_id` = `department_info`.`dptid` AND ".$q;
	$departments = array();
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$department = new Department($row["dpt_id"], $row["dpt_full_name"], new School($row["school_id"], $row["school_name"]));
			array_push($departments, $department);
		}
		$result -> free_result();
	}
	return $departments;
}



/**
  * Redirect with POST data.
  *
  * @param string $url URL.
  * @param array $post_data POST data. Example: ['foo' => 'var', 'id' => 123]
  * @param array $headers Optional. Extra headers to send.
  */

function redirect_post($url, array $data, array $headers = null) {
  $params = [
    'http' => [
      'method' => 'POST',
      'content' => http_build_query($data)
    ]
  ];

  if (!is_null($headers)) {
    $params['http']['header'] = '';
    foreach ($headers as $k => $v) {
      $params['http']['header'] .= "$k: $v\n";
    }
  }

  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);

  if ($fp) {
    echo @stream_get_contents($fp);
    die();
  } else {
    // Error
    throw new Exception("Error loading '$url', $php_errormsg");
  }
}




















class MetricsList {
	public $publications, $publications_5y, $citations, $citations_5y, $hindex, $hindex_5y, $i10index, $i10index_5y, $mindex, $publications_current_year, $citations_current_year, $most_paper_citations;
	function __construct($publications, $publications_5y, $citations, $citations_5y, $hindex, $hindex_5y, $i10index, $i10index_5y, $mindex, $publications_current_year, $citations_current_year, $most_paper_citations) {
		$this->publications = $publications;
		$this->publications_5y = $publications_5y;
		$this->citations = $citations;
		$this->citations_5y = $citations_5y;
		$this->hindex = $hindex;
		$this->hindex_5y = $hindex_5y;
		$this->i10index = $i10index;
		$this->i10index_5y = $i10index_5y;
		$this->mindex = $mindex;
		$this->publications_current_year = $publications_current_year;
		$this->citations_current_year = $citations_current_year;
		$this->most_paper_citations = $most_paper_citations;
	}
}



class DepartmentProgress { public $department_id, $department_full_name, $total_faculty_members, $faculty_members_with_metrics; }
class AggregateMetrics {
	public $unit_id, $unit_name;
	public $citations = array();
	public $citations_5y = array();
	public $publications = array();
	public $publications_5y = array();
	public $hindex = array();
	public $hindex_5y = array();
	public $i10index = array();
	public $i10index_5y = array();
	public $publications_previous_year = array();
	public $publications_2_years_before = array();
	public $publications_3_years_before = array();
	public $citations_previous_year = array();
	public $citations_2_years_before = array();
	public $citations_3_years_before = array();
	public $pubs_q12 = array();
	public $pubs_q1234 = array();
	}









/**
	Creates a connection with the Alexandria database;
	@params: none
	@return: mysqli connection with the database
**/
function createDatabaseConnection(){
	$mysqli = new mysqli(_ALEXANDRIA_DB_HOST, _ALEXANDRIA_DB_USER, _ALEXANDRIA_DB_PASSWORD, _ALEXANDRIA_DB_NAME);
	$mysqli -> set_charset("utf8");
	if ($mysqli -> connect_errno) {
		echo "Αποτυχία σύνδεσης MySQL: " . $mysqli -> connect_error;
		exit();
	}
	return $mysqli;
}

/**
	Creates a connection with the Alexandria Scimago database;
	@params: none
	@return: mysqli connection with the database
**/
function connectToAlexandriaScimagoDatabase(){
	$mysqli = new mysqli(_ALEXANDRIA_SCIMAGO_DB_HOST,
										   _ALEXANDRIA_SCIMAGO_DB_USER,
											 _ALEXANDRIA_SCIMAGO_DB_PASSWORD,
											 _ALEXANDRIA_SCIMAGO_DB_NAME);
	$mysqli -> set_charset("utf8");
	if ($mysqli -> connect_errno) {
		echo "Αποτυχία σύνδεσης MySQL: " . $mysqli -> connect_error;
		exit();
	}
	return $mysqli;
}








function getFacultyMembersWithMetrics($mysqli, $report_id, $unit_type, $unit_id, $provider){
	$facultyMembersWithMetrics = array();
	if ($unit_type == "university"){
		$sql = "SELECT * FROM `faculty_member_in_report`,`faculty_member`, `department`, `rank`, `school` WHERE `faculty_member_in_report`.`report_id` = $report_id AND `faculty_member`.`id` = `faculty_member_in_report`.`facultymember_id` AND `faculty_member_in_report`.`provider_id` = '$provider' AND `rank`.`rank_id` = `faculty_member`.`rank` AND `department`.`dpt_id` = `faculty_member`.`department` AND `department`.`dpt_school_id` = `school`.`school_id` ORDER BY `faculty_member`.`last_name` ASC";
	} else if ($unit_type == "school"){
		$sql = "SELECT * FROM `faculty_member_in_report`,`faculty_member`, `department`, `rank`, `school` WHERE `school`.`school_id` = '$unit_id' AND `faculty_member_in_report`.`report_id` = $report_id AND `faculty_member`.`id` = `faculty_member_in_report`.`facultymember_id` AND `faculty_member_in_report`.`provider_id` = '$provider' AND `rank`.`rank_id` = `faculty_member`.`rank` AND `department`.`dpt_id` = `faculty_member`.`department` AND `department`.`dpt_school_id` = `school`.`school_id` ORDER BY `faculty_member`.`last_name` ASC";
	} else if ($unit_type == "department"){
		$sql = "SELECT * FROM `faculty_member_in_report`,`faculty_member`, `department`, `rank`, `school` WHERE `department`.`dpt_id` = '$unit_id' AND `faculty_member_in_report`.`report_id` = $report_id AND `faculty_member`.`id` = `faculty_member_in_report`.`facultymember_id` AND `faculty_member_in_report`.`provider_id` = '$provider' AND `rank`.`rank_id` = `faculty_member`.`rank` AND `department`.`dpt_id` = `faculty_member`.`department` AND `department`.`dpt_school_id` = `school`.`school_id` ORDER BY `faculty_member`.`last_name` ASC";
	}
	//echo $sql;


	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {

			$fm = new FacultyMember(
				$row["facultymember_id"],
				$row["first_name"],
				$row["last_name"],
				$row["google_scholar_id"],
				$row["scopus_id"],
				new Department($row["dpt_id"], $row["dpt_full_name"], $row["dpt_short_name"], $row["dpt_school_id"]),
				new School($row["school_id"], $row["school_name"]),
				new Rank($row["rank_id"], $row["rank_full_title"], null, null),
				$row["phd_year"],
				0
			);

			$authorProfile = new AuthorProfile();
			$authorProfile->metrics_metadata = $row["metrics_metadata"];
			//$authorProfile->publications_list = $row["publications_list"];

			$fm->author_profile = $authorProfile;


			array_push($facultyMembersWithMetrics, $fm);
		}
		$result -> free_result();
	}

	return $facultyMembersWithMetrics;
}




function getMetricsForFacultyMembers($mysqli, $report_id, $unit_type, $unit_id, $provider){
	$facultyMembersWithMetrics = array();
	if ($unit_type == "university"){
		$sql = "SELECT * FROM `faculty_member_in_report`,`faculty_member`, `department`, `rank`, `school` WHERE `faculty_member_in_report`.`report_id` = $report_id AND `faculty_member`.`id` = `faculty_member_in_report`.`facultymember_id` AND `faculty_member_in_report`.`provider_id` = '$provider' AND `rank`.`rank_id` = `faculty_member`.`rank` AND `department`.`dpt_id` = `faculty_member`.`department` AND `department`.`dpt_school_id` = `school`.`school_id` AND pubs_total IS NOT NULL ORDER BY `faculty_member`.`last_name` ASC";
	}
	else if ($unit_type == "school"){
		$sql = "SELECT * FROM `faculty_member_in_report`,`faculty_member`, `department`, `rank`, `school` WHERE `school`.`school_id` = '$unit_id' AND `faculty_member_in_report`.`report_id` = $report_id AND `faculty_member`.`id` = `faculty_member_in_report`.`facultymember_id` AND `faculty_member_in_report`.`provider_id` = '$provider' AND `rank`.`rank_id` = `faculty_member`.`rank` AND `department`.`dpt_id` = `faculty_member`.`department` AND `department`.`dpt_school_id` = `school`.`school_id` AND pubs_total IS NOT NULL ORDER BY `faculty_member`.`last_name` ASC";
	}else if ($unit_type == "department"){
		$sql = "SELECT * FROM `faculty_member_in_report`,`faculty_member`, `department`, `rank`, `school` WHERE `department`.`dpt_id` = '$unit_id' AND `faculty_member_in_report`.`report_id` = $report_id AND `faculty_member`.`id` = `faculty_member_in_report`.`facultymember_id` AND `faculty_member_in_report`.`provider_id` = '$provider' AND `rank`.`rank_id` = `faculty_member`.`rank` AND `department`.`dpt_id` = `faculty_member`.`department` AND `department`.`dpt_school_id` = `school`.`school_id` AND pubs_total IS NOT NULL ORDER BY `faculty_member`.`last_name` ASC";
	}
	//echo $sql;
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$fm = new FacultyMember();
			$fm->publications = $row["pubs_total"];
			$fm->publications_5y = $row["pubs_5y"];
			$fm->citations = $row["citations_total"];
			$fm->citations_5y = $row["citations_5y"];
			$fm->hindex = $row["h_index_total"];
			$fm->hindex_5y = $row["h_index_5y"];
			$fm->i10index = $row["i10_index_total"];
			$fm->i10index_5y = $row["i10_index_5y"];
			$fm->mindex = $row["m_index"];
			$fm->publications_current_year = $row["publications_current_year"];
			$fm->citations_current_year = $row["citations_current_year"];
			$fm->most_paper_citations = $row["most_paper_citations"];
			$fm->first_name = $row["first_name"];
			$fm->last_name = $row["last_name"];
			$fm->rank = new Rank($row["rank_id"], $row["rank_full_title"], null, null);
			$fm->phd_year = $row["phd_year"];
			$fm->scholar_id = $row["google_scholar_id"];
			$fm->scopus_id = $row["scopus_id"];
			$fm->department = new Department($row["dpt_id"], $row["dpt_full_name"], $row["dpt_short_name"], $row["dpt_school_id"]);
			$fm->school = new School($row["school_id"], $row["school_name"]);
			array_push($facultyMembersWithMetrics, $fm);
		}
		$result -> free_result();
	}
	return $facultyMembersWithMetrics;
}




function getDepartment($mysqli, $department_id, $reference_time){
	$sql = "SELECT * FROM `department`, `department_info`, `school` WHERE `department`.`dpt_school_id` = `school`.`school_id` AND `department`.`dpt_id`='$department_id' AND `department`.`dpt_id` = `department_info`.`dptid` 
	AND ( (`department_info`.`valid_from` <= STR_TO_DATE('".$reference_time."', '%Y-%m-%d %H:%i:%s') AND `department_info`.`valid_until` IS NULL) OR (`department_info`.`valid_from` <= STR_TO_DATE('".$reference_time."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('".$reference_time."', '%Y-%m-%d %H:%i:%s') <= `department_info`.`valid_until`) )";
	$department = null;
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$department = new Department($row["dpt_id"], $row["dpt_full_name"], $row["dpt_school_id"]);
			$department->school = new School($row["school_id"], $row["school_name"]);
		}
		$result -> free_result();
	}
	return $department;
}


function getSchools($mysqli){
	$schools = array();
	$sql = "SELECT * FROM `school`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$school = new School($row["school_id"], $row["school_name"]);
			array_push($schools, $school);
		}
		$result -> free_result();
	}
	return $schools;
}

function getSchool($mysqli, $school_id){
	$sql = "SELECT * FROM `school` WHERE `school`.`school_id`='$school_id'";
	$school = null;
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$school = new School($row["school_id"], $row["school_name"]);
		}
		$result -> free_result();
	}
	return $school;
}




function getAggregateMetricsFromAllFacultyMembers($facultyMembersWithMetrics, $unit_type, $units, $report_year){
	$metricsArray = array();
	if ($unit_type == "university"){
		$metrics = new AggregateMetrics();
		foreach ($facultyMembersWithMetrics as $fm){
			if ( isset($fm->metrics_metadata) ){
				array_push($metrics->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
				array_push($metrics->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
				array_push($metrics->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
				array_push($metrics->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
				array_push($metrics->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
				array_push($metrics->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
				array_push($metrics->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
				array_push($metrics->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

				array_push($metrics->publications_previous_year,getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
				array_push($metrics->publications_2_years_before,getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
				array_push($metrics->publications_3_years_before,getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
				array_push($metrics->citations_previous_year,getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
				array_push($metrics->citations_2_years_before,getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
				array_push($metrics->citations_3_years_before,getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));


				if ( !empty($fm->author_profile->pubsQ1Q2) && $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metrics->pubs_q12, $fm->author_profile->pubsQ1Q2);
				if ( !empty($fm->author_profile->pubsJournals) && $fm->author_profile->pubsJournals != NULL ) array_push($metrics->pubs_q1234, $fm->author_profile->pubsJournals);

			}
		}
		array_push($metricsArray, $metrics);
		//print_r($metricsArray[0]->publications_2021);
	}
	else if ($unit_type == "school"){
		foreach ($units as $school){
			$metrics = new AggregateMetrics();
			$metrics->unit = new Unit ($school->id, $school->name);
			array_push($metricsArray, $metrics);
		}


		foreach ($facultyMembersWithMetrics as $fm){
			foreach ($metricsArray as $metricsPerSchool){
				if (isset($fm->info_metadata)){
					if ($metricsPerSchool->unit->id == json_decode($fm->info_metadata)->department->school->id){
						if ( isset($fm->metrics_metadata) ){
							array_push($metricsPerSchool->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
							array_push($metricsPerSchool->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
							array_push($metricsPerSchool->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
							array_push($metricsPerSchool->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
							array_push($metricsPerSchool->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
							array_push($metricsPerSchool->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
							array_push($metricsPerSchool->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
							array_push($metricsPerSchool->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

							array_push($metricsPerSchool->publications_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
							array_push($metricsPerSchool->publications_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
							array_push($metricsPerSchool->publications_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
							array_push($metricsPerSchool->citations_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
							array_push($metricsPerSchool->citations_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
							array_push($metricsPerSchool->citations_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));

							if ( !empty($fm->author_profile->pubsQ1Q2) && $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metricsPerSchool->pubs_q12, $fm->author_profile->pubsQ1Q2);
							if ( !empty($fm->author_profile->pubsJournals) && $fm->author_profile->pubsJournals != NULL ) array_push($metricsPerSchool->pubs_q1234, $fm->author_profile->pubsJournals);
							break;
						}
					}
				}
			}

		}

	}
	else if ($unit_type == "department"){
		foreach ($units as $department){
			$metrics = new AggregateMetrics();
			//print_r($department);
			$metrics->unit = new Unit ($department->id, $department->name);
			array_push($metricsArray, $metrics);
		}


		foreach ($facultyMembersWithMetrics as $fm){
			foreach ($metricsArray as $metricsPerDepartment){
				if (isset($fm->info_metadata)){
					if ($metricsPerDepartment->unit->id == json_decode($fm->info_metadata)->department->id){
						if (!is_null($fm->metrics_metadata)){
							//$metricsPerDepartment->unit->name = json_decode($fm->info_metadata)->department->name;
							array_push($metricsPerDepartment->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
							array_push($metricsPerDepartment->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
							array_push($metricsPerDepartment->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
							array_push($metricsPerDepartment->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
							array_push($metricsPerDepartment->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
							array_push($metricsPerDepartment->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
							array_push($metricsPerDepartment->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
							array_push($metricsPerDepartment->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

							array_push($metricsPerDepartment->publications_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
							array_push($metricsPerDepartment->publications_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
							array_push($metricsPerDepartment->publications_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
							array_push($metricsPerDepartment->citations_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
							array_push($metricsPerDepartment->citations_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
							array_push($metricsPerDepartment->citations_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));

							if ( !empty($fm->author_profile->pubsQ1Q2) && $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metricsPerDepartment->pubs_q12, $fm->author_profile->pubsQ1Q2);
							if ( !empty($fm->author_profile->pubsJournals) && $fm->author_profile->pubsJournals != NULL ) array_push($metricsPerDepartment->pubs_q1234, $fm->author_profile->pubsJournals);
							break;
						}
					}
				}
			}

		}

	}
	else if ($unit_type == "rank"){
		foreach ($units as $rank){
			$metrics = new AggregateMetrics();
			$metrics->unit = new Unit ($rank->id, $rank->full_title);
			array_push($metricsArray, $metrics);
		}

		foreach ($facultyMembersWithMetrics as $fm){
			foreach ($metricsArray as $metricsPerRank){
				if (isset($fm->info_metadata)){
					if ($metricsPerRank->unit->id == json_decode($fm->info_metadata)->rank->id){
						if (!is_null($fm->metrics_metadata)){
							array_push($metricsPerRank->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
							array_push($metricsPerRank->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
							array_push($metricsPerRank->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
							array_push($metricsPerRank->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
							array_push($metricsPerRank->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
							array_push($metricsPerRank->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
							array_push($metricsPerRank->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
							array_push($metricsPerRank->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

							array_push($metricsPerRank->publications_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
							array_push($metricsPerRank->publications_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
							array_push($metricsPerRank->publications_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
							array_push($metricsPerRank->citations_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
							array_push($metricsPerRank->citations_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
							array_push($metricsPerRank->citations_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));

							if ( !empty($fm->author_profile->pubsQ1Q2) && $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metricsPerRank->pubs_q12, $fm->author_profile->pubsQ1Q2);
							if ( !empty($fm->author_profile->pubsJournals) && $fm->author_profile->pubsJournals != NULL ) array_push($metricsPerRank->pubs_q1234, $fm->author_profile->pubsJournals);


							break;
						}
					}
				}
			}

		}

	}

	return $metricsArray;

}




function getAggregateMetricsFromAllFacultyMembers_NEW_NEED_WORK($facultyMembersWithMetrics, $unit_type, $units, $report_year){
	if ($unit_type == "university"){
		foreach ($facultyMembersWithMetrics as $fm){
			if ( !empty($fm->author_profile) ){
				/* Total */
				$metrics_total = new MetricArray();
				if ( !empty($metrics_total->publications) ) array_push($metrics_total->publications_per_member, $fm->author_profile->metric_total->publications);
				if ( !empty($metrics_total->citations) ) array_push($metrics_total->citations_per_member, $fm->author_profile->metric_total->citations);
				if ( !empty($metrics_total->hindex) ) array_push($metrics_total->hindex_per_member, $fm->author_profile->metric_total->hindex);
				if ( !empty($metrics_total->i10index) ) array_push($metrics_total->i10index_per_member, $fm->author_profile->metric_total->i10index);

				/* 5 years */
				$metrics_5yrs = new MetricArray();
				if ( !empty($metrics_5yrs->publications) ) array_push($metrics_5yrs->publications_per_member, $fm->author_profile->metrics_5y->publications);
				if ( !empty($metrics_5yrs->citations) ) array_push($metrics_5yrs->citations_per_member, $fm->author_profile->metrics_5y->citations);
				if ( !empty($metrics_5yrs->hindex) ) array_push($metrics_5yrs->hindex_per_member, $fm->author_profile->metrics_5y->hindex);
				if ( !empty($metrics_5yrs->i10index) ) array_push($metrics_5yrs->i10index_per_member, $fm->author_profile->metrics_5y->i10index);

				//array_push($metrics->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
				///array_push($metrics->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
				//array_push($metrics->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
				//array_push($metrics->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
				//array_push($metrics->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
				//array_push($metrics->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
				//array_push($metrics->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
				//array_push($metrics->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

				//array_push($metrics->publications_2021,getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, 2021));
				//array_push($metrics->publications_2020,getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, 2020));
				//array_push($metrics->publications_2019,getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, 2019));
				//array_push($metrics->citations_2021,getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, 2021));
				//array_push($metrics->citations_2020,getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, 2020));
				//array_push($metrics->citations_2019,getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, 2019));


				//if ( $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metrics->pubs_q12, $fm->author_profile->pubsQ1Q2);
				//if ( $fm->author_profile->pubsJournals != NULL ) array_push($metrics->pubs_q1234, $fm->author_profile->pubsJournals);
				$metricsArray = array($metrics_total, $metrics_5yrs);

			}
		}
		//array_push($metricsArray, $metrics);
		//print_r($metricsArray[0]->publications_2021);
	}
	else if ($unit_type == "school"){
		foreach ($units as $school){
			$metrics = new AggregateMetrics();
			$metrics->unit = new Unit ($school->id, $school->name);
			array_push($metricsArray, $metrics);
		}


		foreach ($facultyMembersWithMetrics as $fm){
			foreach ($metricsArray as $metricsPerSchool){
				if (isset($fm->info_metadata)){
					if ($metricsPerSchool->unit->id == json_decode($fm->info_metadata)->school->id){
						if ( isset($fm->metrics_metadata) ){
							array_push($metricsPerSchool->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
							array_push($metricsPerSchool->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
							array_push($metricsPerSchool->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
							array_push($metricsPerSchool->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
							array_push($metricsPerSchool->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
							array_push($metricsPerSchool->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
							array_push($metricsPerSchool->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
							array_push($metricsPerSchool->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

							array_push($metricsPerSchool->publications_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
							array_push($metricsPerSchool->publications_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
							array_push($metricsPerSchool->publications_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
							array_push($metricsPerSchool->citations_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
							array_push($metricsPerSchool->citations_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
							array_push($metricsPerSchool->citations_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));

							if ( $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metricsPerSchool->pubs_q12, $fm->author_profile->pubsQ1Q2);
							if ( $fm->author_profile->pubsJournals != NULL ) array_push($metricsPerSchool->pubs_q1234, $fm->author_profile->pubsJournals);
							break;
						}
					}
				}
			}

		}

	}
	else if ($unit_type == "department"){
		foreach ($units as $department){
			$metrics = new AggregateMetrics();
			//print_r($department);
			$metrics->unit = new Unit ($department->id, $department->full_name);
			array_push($metricsArray, $metrics);
		}


		foreach ($facultyMembersWithMetrics as $fm){
			foreach ($metricsArray as $metricsPerDepartment){
				if (isset($fm->info_metadata)){
					if ($metricsPerDepartment->unit->id == json_decode($fm->info_metadata)->department->id){
						if (json_decode($fm->metrics_metadata) !== NULL){
							array_push($metricsPerDepartment->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
							array_push($metricsPerDepartment->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
							array_push($metricsPerDepartment->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
							array_push($metricsPerDepartment->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
							array_push($metricsPerDepartment->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
							array_push($metricsPerDepartment->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
							array_push($metricsPerDepartment->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
							array_push($metricsPerDepartment->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

							array_push($metricsPerDepartment->publications_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
							array_push($metricsPerDepartment->publications_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
							array_push($metricsPerDepartment->publications_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
							array_push($metricsPerDepartment->citations_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
							array_push($metricsPerDepartment->citations_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
							array_push($metricsPerDepartment->citations_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));

							if ( $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metricsPerDepartment->pubs_q12, $fm->author_profile->pubsQ1Q2);
							if ( $fm->author_profile->pubsJournals != NULL ) array_push($metricsPerDepartment->pubs_q1234, $fm->author_profile->pubsJournals);
							break;
						}
					}
				}
			}

		}

	}
	else if ($unit_type == "rank"){
		foreach ($units as $rank){
			$metrics = new AggregateMetrics();
			$metrics->unit = new Unit ($rank->id, $rank->full_title);
			array_push($metricsArray, $metrics);
		}

		foreach ($facultyMembersWithMetrics as $fm){
			foreach ($metricsArray as $metricsPerRank){
				if (isset($fm->info_metadata)){
					if ($metricsPerRank->unit->id == json_decode($fm->info_metadata)->rank->id){
						if (json_decode($fm->metrics_metadata) !== NULL){
							array_push($metricsPerRank->publications, json_decode($fm->metrics_metadata)->metrics_total->publications);
							array_push($metricsPerRank->publications_5y, json_decode($fm->metrics_metadata)->metrics_5y->publications);
							array_push($metricsPerRank->citations, json_decode($fm->metrics_metadata)->metrics_total->citations);
							array_push($metricsPerRank->citations_5y, json_decode($fm->metrics_metadata)->metrics_5y->citations);
							array_push($metricsPerRank->hindex, json_decode($fm->metrics_metadata)->metrics_total->hindex);
							array_push($metricsPerRank->hindex_5y, json_decode($fm->metrics_metadata)->metrics_5y->hindex);
							array_push($metricsPerRank->i10index, json_decode($fm->metrics_metadata)->metrics_total->i10index);
							array_push($metricsPerRank->i10index_5y, json_decode($fm->metrics_metadata)->metrics_5y->i10index);

							array_push($metricsPerRank->publications_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-1));
							array_push($metricsPerRank->publications_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-2));
							array_push($metricsPerRank->publications_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->publications_per_year, $report_year-3));
							array_push($metricsPerRank->citations_previous_year, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-1));
							array_push($metricsPerRank->citations_2_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-2));
							array_push($metricsPerRank->citations_3_years_before, getElementFromKey(json_decode($fm->metrics_metadata)->citations_per_year, $report_year-3));

							if ( $fm->author_profile->pubsQ1Q2 != NULL ) array_push($metricsPerRank->pubs_q12, $fm->author_profile->pubsQ1Q2);
							if ( $fm->author_profile->pubsJournals != NULL ) array_push($metricsPerRank->pubs_q1234, $fm->author_profile->pubsJournals);
							break;
						}
					}
				}
			}

		}

	}

	return $metricsArray;

}

















function getPublicationsOfFacultyMember($mysqli, $member_id, $report_id, $provider_id){

	$sql = "SELECT * FROM `publication_of_faculty_member_in_report` WHERE `facultymember_id`='$member_id' AND `report_id`='$report_id' AND `provider_id`='$provider_id'";
	//echo $sql;
	$publications = array();
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$publication = new Publication();
			$publication->facultymember_id = $row["facultymember_id"];
			$publication->report_id = $row["report_id"];
			$publication->provider_id = $row["provider_id"];
			$publication->pub_title = $row["pub_title"];
			$publication->pub_authors = $row["pub_authors"];
			$publication->pub_venue = $row["pub_venue"];
			$publication->pub_date = $row["pub_date"];
			$publication->pub_citedby= $row["pub_citedby"];
			$publication->pub_doi = $row["pub_doi"];
			$publication->pub_issn = $row["pub_issn"];
			$publication->pub_type = $row["pub_type"];
			$publication->pub_subtype = $row["pub_subtype"];
			$publication->pub_subtype_description = $row["pub_subtype_description"];
			$publication->pub_source_id = $row["pub_source_id"];
			$publication->pub_provider_id = $row["pub_provider_id"];


			$publication->pub_year = substr( $row["pub_date"], 0, 4);
			$publication->pub_q = $row["scimagojr_q"];

			array_push($publications, $publication);
		}
		$result -> free_result();
	}
	return $publications;
}


function getFacultyMembersWithNoMetricsForGivenReport($mysqli, $report_id){
	$facultyMembers = array();
	//if ( ($department == "all") && ($rank == "all") ){
		$sql = "SELECT facultymember_id, first_name, last_name, rank, phd_year, google_scholar_id, department FROM faculty_member, `faculty_member_in_report` WHERE provider_id='gscholar' AND pubs_total IS NULL AND report_id='$report_id' AND faculty_member.id = faculty_member_in_report.facultymember_id AND google_scholar_id <> ''";
		if ($result = $mysqli -> query($sql)) {
			while ($row = $result -> fetch_row()) {
				$fm = new FacultyMember();
				$fm->id = $row[0];
				$fm->first_name = $row[1];
				$fm->last_name = $row[2];
				$fm->rank = $row[3];
				$fm->phd_year = $row[4];
				$fm->scholar_id = $row[5];
				$fm->department = $row[6];
				array_push($facultyMembers, $fm);
			}
			$result -> free_result();
		}
		return $facultyMembers;
	//}
}





function getMetricsForEachDepartment($mysqli, $report_id, $provider){
	$metricsForEachDeparment = array();
	$sql = "SELECT `department`.`full_name` AS `departmentName`, COUNT(facultymember_id) AS totalMembers, SUM(`pubs_total`) AS subPublications, SUM(`pubs_5y`) AS subPublications5y, SUM(`citations_total`) AS sumCitations,  SUM(`citations_5y`) AS sumCitations_5y, SUM(`h_index_total`) AS sumHIndex, SUM(`h_index_5y`) AS sumHIndex_5y, SUM(`i10_index_total`) AS sumI10Index, SUM(`i10_index_5y`) AS sumI10Index_5y FROM `faculty_member_in_report`, `faculty_member`, `department` WHERE `report_id`='$report_id' AND provider_id='$provider'  AND `faculty_member`.`id`=`faculty_member_in_report`.`facultymember_id` AND `department`.`id`=`faculty_member`.`department` GROUP BY `department`.`full_name` ORDER BY `department`.`full_name`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_row()) {
			$depMetric = new Department();
			$depMetric->full_name = $row[0];
			$depMetric->totalMembers = $row[1];
			$depMetric->subPublications = $row[2];
			$depMetric->subPublications5y = $row[3];
			$depMetric->sumCitations = $row[4];
			$depMetric->sumCitations_5y = $row[5];
			$depMetric->sumHIndex = $row[6];
			$depMetric->sumHIndex_5y = $row[7];
			$depMetric->sumI10Index = $row[8];
			$depMetric->sumI10Index_5y = $row[9];
			array_push($metricsForEachDeparment, $depMetric);
		}
		$result -> free_result();
	}
	return $metricsForEachDeparment;
}




function getStatusForEachDepartmentReport($mysqli, $provider){
	$arrStatus = array();
}







function getActiveMetrics($mysqli, $report_id, $provider){
	$progressForEachDepartment = array();
	$sql = "SELECT `department`.`id`, `department`.`full_name`, COUNT(`faculty_member`.`department`) AS total FROM `department` LEFT JOIN `faculty_member` on `department`.`id` = `faculty_member`.`department` GROUP BY 1";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_row()) {
			$dp = new DepartmentProgress();
			$dp->department_id = $row[0];
			$dp->department_full_name = $row[1];
			$dp->total_faculty_members = $row[2];
			array_push($progressForEachDepartment, $dp);
		}
		$result -> free_result();
	}


	$sql = "SELECT department.id, count(department.id) FROM department, faculty_member, faculty_member_in_report WHERE department.id = faculty_member.department AND faculty_member_in_report.facultymember_id = faculty_member.id AND faculty_member_in_report.provider_id = '$provider' AND faculty_member_in_report.report_id = '$report_id' AND faculty_member_in_report.pubs_total > 0 GROUP BY department.id";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_row()) {
			$department_id = $row[0];
			foreach ($progressForEachDepartment as $dp){
				if ($department_id == $dp->department_id){
					$dp->faculty_members_with_metrics = $row[1];
					break;
				}
			}
		}
		$result -> free_result();
	}

	return $progressForEachDepartment;
}


function getAllFacultyMembers($mysqli){
	$facultyMembers = array();
	$sql = "SELECT `fm`.`id`, `fm`.`last_name`, `fm`.`first_name`, `rank`.`short_title`, `fm`.`phd_year`, `fm`.`google_scholar_id`, `fm`.`scopus_id`, `d`.`full_name` FROM `faculty_member` AS `fm`, `department` AS `d`, `rank` WHERE `d`.`id` = `fm`.`department` AND `rank`.`id` = `fm`.`rank` ORDER BY `d`.`full_name`, `fm`.`last_name`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_row()) {
			$fm = new FacultyMember();
			$fm->id = $row[0];
			$fm->last_name = $row[1];
			$fm->first_name = $row[2];
			$fm->rank = $row[3];
			$fm->phd_year = $row[4];
			$fm->gscholar = $row[5];
			$fm->scopus = $row[6];
			$fm->department = $row[7];
			array_push($facultyMembers, $fm);
		}
		$result -> free_result();
	}

	return $facultyMembers;
}

function getTop210ProfilesCitations($mysqli, $report_id, $provider){
	$total_citations = 0;
	$citationsArray = array();
	$sql = "SELECT `metrics_metadata` FROM `faculty_member_in_report` WHERE `report_id` = $report_id AND `provider_id` = '$provider'";


	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_row()) {
			if(!is_null($row[0])) {
				$metrics_metadata = json_decode($row[0]);
				if (isset ($metrics_metadata->metrics_total) ){
					array_push($citationsArray, intval($metrics_metadata->metrics_total->citations));
				}
			}
		}
		$result -> free_result();
	}


	rsort($citationsArray);


	$count = 1;
	foreach ($citationsArray as $c){
		if ($count >= 20 && $count <= 210)
			$total_citations += intval($c);
			$count++;
	}



	return $total_citations;
}


function getMeanScore($arr){
	if (empty($arr)) return 0;
	$totalScore = 0;
	foreach ($arr as $element){
		$totalScore += $element;
	}
	return $totalScore/sizeof($arr);
}

function getMedian($arr) {
	if (empty($arr)) return 0;
	sort($arr);
	$arrLength = sizeof($arr);
	$median =0;
	if($arrLength % 2 == 0){
		$median = ($arr[$arrLength/2 - 1] + $arr[$arrLength/2])/2;
	} else {
		$median = $arr[($arrLength)/2];
	}
	return $median;
}

function getQ3Score($arr){
	if (empty($arr)) return 0;
	sort($arr);
	$arrLength = sizeof($arr);
	$q1 =0;
	if($arrLength % 2 == 0){
		$q1 = ($arr[$arrLength/4 - 1] + $arr[$arrLength/4])/2;
	} else {
		$q1 = $arr[($arrLength)/4];
	}
	return $q1;
}

function getQ1Score($arr){
	if (empty($arr)) return 0;
	sort($arr);
	$arrLength = sizeof($arr);
	$q3 =0;
	if($arrLength % 2 == 0){
		$q3 = ($arr[3*$arrLength/4 - 1] + $arr[3*$arrLength/4])/2;
	} else {
		$q3 = $arr[($arrLength)*0.75];
	}
	return $q3;
}

function getTotal($arr){
	$totalScore = 0;
	foreach ($arr as $element){
		$totalScore += $element;
	}
	return $totalScore;
}





function extractMembersOfSchool($facultyMembers, $school_id){
	foreach ($facultyMembers as $fmKey => $fm){
		if ( json_decode($fm->info_metadata)->department->school->id != $school_id){
			unset($facultyMembers[$fmKey]);
		}
	}
	return $facultyMembers;
}

function extractMembersOfDepartment($facultyMembers, $department_id){
	foreach ($facultyMembers as $fmKey => $fm){
		if ( json_decode($fm->info_metadata)->department->id != $department_id){
			unset($facultyMembers[$fmKey]);
		}
	}
	return $facultyMembers;
}

function getElementFromKey($givenArray, $givenKey){
	$elementValue = 0;
	foreach( $givenArray as $key => $value){
		if ($key == $givenKey){
			$elementValue = $value;
			break;
		}
	}
	return $elementValue;
}



function executeCURL($url){
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$headers = array("Accept: application/json",);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //for debug only!
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //for debug only!
	$resp = curl_exec($curl);
	curl_close($curl);
	return $resp;
}






function getScimagoQforPublication($mysqli, $issn, $year){
	if ( ( intval($year) < 1999 ) || ( intval($year) > 2020 ) ) return null;

	$q = null;
	$scimago_table = "scimagojr".$year;
	$sql = "SELECT `sjr_best_q` FROM `$scimago_table` WHERE `issn` LIKE '%$issn%'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$q = $row["sjr_best_q"];
		}
		$result -> free_result();
	}
	return $q;

}



class ModipMetric {
	public $unit, $code, $title, $count;
	function __construct($unit, $code, $title, $count) {
		$this->unit = $unit;
		$this->code = $code;
		$this->title = $title;
		$this->count = $count;
	}
}
function getModipDataForUpatras($mysqli, $report_id, $year){
	$pubs_in_scopus_journals = 0;
	$pubs_in_scopus_journas_year = 0;
	$citations_scopus = 0;
	$citations_scopus_year = 0;
	$citations_scholar = 0;
	$modip_metrics = array();


	/* Εργασίες σε επιστημονικά περιοδικά με κριτές (σωρευτικά) */
	$sql = "SELECT DISTINCT COUNT(`pub_provider_id`) AS `scopus_pubs` FROM `publication_of_faculty_member_in_report` WHERE `provider_id` = 'scopus' AND `pub_type` = 'Journal' AND `report_id` = '$report_id'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( "Πανεπιστήμιο Πατρών", "M1.116", "Εργασίες σε επιστημονικά περιοδικά με κριτές (σωρευτικά)", $row["scopus_pubs"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	/* Εργασίες σε επιστημονικά περιοδικά με κριτές (έτος αναφοράς) */
	$sql = "SELECT DISTINCT COUNT(`pub_provider_id`) AS `scopus_pubs` FROM `publication_of_faculty_member_in_report` WHERE `provider_id` = 'scopus' AND `pub_type` = 'Journal' AND `report_id` = '$report_id' AND `pub_date` >= '$year-01-01'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( "Πανεπιστήμιο Πατρών", "M1.227", "Εργασίες σε επιστημονικά περιοδικά με κριτές (έτος αναφοράς)", $row["scopus_pubs"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	/* Εργασίες σε επιστημονικά περιοδικά με κριτές (έτος αναφοράς) */
	$sql = "SELECT DISTINCT `pub_provider_id`, SUM(`pub_citedby`) AS `scopus_citations` FROM `publication_of_faculty_member_in_report` WHERE `provider_id` = 'scopus'  AND `report_id` = '$report_id'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( "Πανεπιστήμιο Πατρών", "M1.232", "Ετεροαναφορές Scopus (σωρευτικά)", $row["scopus_citations"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	/* Αναφορές (σωρευτικά) */
	$sql = "SELECT DISTINCT `pub_provider_id`, SUM(`pub_citedby`) AS `scholar_citations` FROM `publication_of_faculty_member_in_report` WHERE `provider_id` = 'gscholar'  AND `report_id` = '$report_id'";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( "Πανεπιστήμιο Πατρών", "M1.232", "Ετεροαναφορές Scopus (σωρευτικά)", $row["scholar_citations"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}


	/* Για κάθε τμήμα */
	/* Εργασίες σε επιστημονικά περιοδικά με κριτές (σωρευτικά) */
	$sql = "SELECT DISTINCT `dpt_full_name`, `pub_provider_id`, COUNT(`pub_provider_id`) AS `scopus_pubs` FROM `publication_of_faculty_member_in_report`, `faculty_member`, `department` WHERE `publication_of_faculty_member_in_report`.`facultymember_id`=`faculty_member`.`id` AND `faculty_member`.`department`=`department`.`dpt_id` AND `provider_id` = 'scopus' AND `pub_type` = 'Journal' AND `report_id` = 1 GROUP BY `dpt_full_name`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( $row["dpt_full_name"], "M3.117", "Εργασίες σε επιστημονικά περιοδικά με κριτές (σωρευτικά)", $row["scopus_pubs"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	/* Εργασίες σε επιστημονικά περιοδικά με κριτές (έτος αναφοράς) */
	$sql = "SELECT DISTINCT `dpt_full_name`, `pub_provider_id`, COUNT(`pub_provider_id`) AS `scopus_pubs` FROM `publication_of_faculty_member_in_report`, `faculty_member`, `department` WHERE `publication_of_faculty_member_in_report`.`facultymember_id`=`faculty_member`.`id` AND `faculty_member`.`department`=`department`.`dpt_id` AND `provider_id` = 'scopus' AND `pub_date` >= '$year-01-01' AND `pub_type` = 'Journal' AND `report_id` = 1  GROUP BY `dpt_full_name`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( $row["dpt_full_name"], "Μ3.177", "Εργασίες σε επιστημονικά περιοδικά με κριτές (έτος αναφοράς)", $row["scopus_pubs"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	/* Ανακοινώσεις σε πρακτικά συνεδρίων με κριτές (σωρευτικά) */
	$sql = "SELECT DISTINCT `dpt_full_name`, `pub_provider_id`, COUNT(`pub_provider_id`) AS `scopus_pubs` FROM `publication_of_faculty_member_in_report`, `faculty_member`, `department` WHERE `publication_of_faculty_member_in_report`.`facultymember_id`=`faculty_member`.`id` AND `faculty_member`.`department`=`department`.`dpt_id` AND `provider_id` = 'scopus' AND `pub_type` = 'Conference Proceeding' AND `report_id` = 1 GROUP BY `dpt_full_name`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( $row["dpt_full_name"], "M3.120", "Ανακοινώσεις σε πρακτικά συνεδρίων με κριτές (σωρευτικά)", $row["scopus_pubs"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	/* Ανακοινώσεις σε πρακτικά συνεδρίων με κριτές (έτος αναφοράς) */
	$sql = "SELECT DISTINCT `dpt_full_name`, `pub_provider_id`, COUNT(`pub_provider_id`) AS `scopus_pubs` FROM `publication_of_faculty_member_in_report`, `faculty_member`, `department` WHERE `publication_of_faculty_member_in_report`.`facultymember_id`=`faculty_member`.`id` AND `faculty_member`.`department`=`department`.`dpt_id` AND `provider_id` = 'scopus' AND `pub_date` >= '$year-01-01' AND `pub_type` = 'Conference Proceeding' AND `report_id` = 1  GROUP BY `dpt_full_name`";
	if ($result = $mysqli -> query($sql)) {
		while ($row = $result -> fetch_assoc()) {
			$mm = new ModipMetric( $row["dpt_full_name"], "Μ3.180", "Ανακοινώσεις σε πρακτικά συνεδρίων με κριτές (έτος αναφοράς)", $row["scopus_pubs"] );
			array_push($modip_metrics, $mm);
		}
		$result -> free_result();
	}

	return $modip_metrics;

}

?>
