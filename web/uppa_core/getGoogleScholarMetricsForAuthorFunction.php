<?php

/* Required files */
require_once 'scraping_plugin/simple_html_dom/simple_html_dom.php';
require_once "settings/year.php";
require_once "classes/AuthorProfile.php";
require_once "classes/Publication.php";
require_once "classes/Message.php";
require_once 'functions.php';


/* Set some time constraints to avoid bot check from Google */
set_time_limit(0);
$sleeptime = rand(1,11);
sleep($sleeptime);



/* Get parameters for faculty member */
if ( !isset($_GET["member_id"]) &&
		 !isset($_GET["scholar_id"]) &&
		 !isset($_GET["phd_year"]) &&
		 !isset($_GET["report_id"])
	 ){
		 return False;
	 }
$member_id = $_GET["member_id"];
$scholar_id = $_GET["scholar_id"];
$phd_year = intval($_GET["phd_year"]);
$report_id = $_GET["report_id"];
$current_year = intval(_CURRENT_YEAR);


$msg = new Message("error", "");


if (($scholar_id != "") OR ($scholar_id != NULL)) {

	/* Create SQL connection */
	$mysqli = createDatabaseConnection();

	/* Get author profile (from Scholar) */
	$author = getMetricsForAuthor($member_id, $report_id, $scholar_id, $phd_year, $current_year);

	if ($author === null){
		$msg->type = "error";
		$msg->content = "Το scholar ID δεν είναι σωστό";
		$msg->member_id = $member_id;
	} else {
		/* SQL for updating report entry */
		$sql = "UPDATE `faculty_member_in_report`
		 			  SET `metrics_metadata`='$author->metrics_metadata'
						WHERE `report_id`='$report_id' AND `facultymember_id`='$member_id' AND `provider_id`='gscholar'";

		/* Execute SQL command */
		if ($mysqli->query($sql) === TRUE) {
			$msg->type = "success";
			$msg->content = $member_id;
			$msg->member_id = $member_id;
		} else {
			$msg->type = "error";
			$msg->content = "Υπήρξε πρόβλημα με την εγγραφή στην ΒΔ: ".$mysqli->error;
			$msg->member_id = $member_id;
		  //echo "Error: " . $sql . "<br>" . $mysqli->error;
		}

		/* SQL for publications */
		$stmt = $mysqli->prepare ("INSERT INTO `publication_of_faculty_member_in_report`(`facultymember_id`, `report_id`, `provider_id`, `pub_title`, `pub_authors`, `pub_venue`, `pub_date`, `pub_citedby`,`pub_provider_id`) VALUES (?,?,?,?,?,?,?,?,?)");
		foreach ($author->publications_list as $p){
			$uniqid = uniqid();
			$stmt->bind_param("iisssssis",
				$p['faculty_member_id'],
				$p['report_id'],
				$p['provider_id'],
				$p['title'],
				$p['authors'],
				$p['venue'],
				$p['date'],
				$p['citedBy'],
				$uniqid
			 );
			 $stmt->execute();
		}

		/* Close SQL connection */
		$mysqli -> close();
	}
}

/* What is returned to AJAX call */
echo json_encode($msg);



function getMetricsForAuthor($member_id, $report_id, $scholar_id, $phd_year, $current_year){
	$author = new AuthorProfile();

	/* Basic information */
	$author->id = $member_id;
	$author->provider_id = "gscholar";
	$author->provider_author_id = $scholar_id;

	/* Initial declaration of metrics (0) */
	$publications_total = $citations_total = $hindex = $i10index = 0;			//total
	$publications_5y = $citations_5y = $hindex_5y = $i10index_5y = 0;			//5 yrs
	$mindex = $most_paper_citations = 0;																	//misc
	$publications_per_year = array();


	/* Get the scholar page */
	$gscholarProfilePage = 'https://scholar.google.com/citations?user='.$scholar_id.'&hl=en';

	/* Return null if the author page doesn't exist */
	$array = get_headers($gscholarProfilePage);
	$string = $array[0];
	$pos = strpos($string, "200");
	if ($pos === false) return null;

	/* Get the page content */
  $html = file_get_html($gscholarProfilePage);

	/* Get table data */
	if ( null !== $html->find('#gsc_rsb_st', 0) ){
		$authorTable = $html->find('#gsc_rsb_st', 0);
		$citations_table =  $authorTable->find(".gsc_rsb_std");
	}

	/* Get total citations metrics from the table */
	if ( isset($citations_table[0]) ) $citations_total = $citations_table[0]->plaintext;
	if ( isset($citations_table[2]) ) $hindex = $citations_table[2]->plaintext;
	if ( isset($citations_table[4]) ) $i10index = $citations_table[4]->plaintext;

	/* Get 5-years citations metrics from the table */
	if ( isset($citations_table[1]) ) $citations_5y = $citations_table[1]->plaintext;
	if ( isset($citations_table[3]) ) $hindex_5y = $citations_table[3]->plaintext;
	if ( isset($citations_table[5]) ) $i10index_5y = $citations_table[5]->plaintext;

	/* Get plot data */
	$citations_per_year = array();
	if ( null !== $html->find(".gsc_g_a") ){
		$labels = $html->find(".gsc_g_t");									// get labels
		$plots = array_fill(0, sizeof($labels), 0);					// create a plots array
		$plots_filled = $html->find(".gsc_g_a");						// plots by Scholar
		foreach ($plots_filled as $plot){
			$style = $plot->style;
			$zindexPos = strpos($style, "z-index");
			$style = substr($style, $zindexPos, (strlen($style)-$zindexPos));
			$zindex = intval( str_replace("z-index:", "", $style) );

			$index = intval(sizeof($plots) - $zindex);
			$plots[$index] = $plot->find(".gsc_g_al", 0)->plaintext;
		}


		for ($i=0; $i<sizeof($labels); $i++){
			$label = trim( $labels[$i]->plaintext );
			$plot = trim( $plots[$i] );
			$citations_per_year[$label] = $plot;
		}
	}

	/* Get m-index */
	if ($phd_year != 0){
		$mindex = round($hindex/($current_year-$phd_year), 3);
	}



	$publicationsList = array();


	$page = 1;
	$finalPage = false;
	while (!$finalPage) {
		$offset = ($page - 1)* 100;
		$cStart = 0+$offset;
		$profile = 'https://scholar.google.com/citations?user='.$scholar_id.'&hl=en&cstart='.$cStart.'&view_op=list_works&pagesize=100';
		$html = file_get_html($profile);
		if(is_object($html)){
			$empty = $html->find('td.gsc_a_e',0);
			if($empty){
				$finalPage = true;
				unset($html);
			}
			else{
				$urlArray[] = $profile;
				$page++;
			}
		}
		else{
			$response['success'] = 0;
			$response['message'] = "Invalid URL";
		}

	}

	$most_paper_citations = 0;

	if($finalPage){
		foreach ($urlArray as $urlPublikasi) {
			$html = file_get_html($urlPublikasi);
			$table = $html->find('#gsc_a_t',0);
			$rowData = array();
			if($table){
				foreach($table->find('tr.gsc_a_tr') as $row){
					$paper['faculty_member_id'] = $member_id;
					$paper['report_id'] = $report_id;
					$paper['provider_id'] = 'gscholar';

					/* Paper title */
					$paperTitle = $row->find('td.gsc_a_t a', 0)->plaintext;
					$paper['title'] = str_replace('"', "'", $paperTitle);



					/* Paper citations */
					$citedBy = $row->find('td.gsc_a_c', 0)->plaintext;
					if($citedBy === ''){
						$citedBy = 0;
					}
					$citedBy = preg_replace('/[\*]+/', '', $citedBy);
					$paper['citedBy'] = trim($citedBy);



					/* Authors */
					$paper['authors'] = $row->find('td.gsc_a_t .gs_gray', 0)->plaintext;

					/* Venue */
					$paperVenue = $row->find('td.gsc_a_t .gs_gray', 1)->plaintext;
					$paper['venue'] = str_replace('"', "'", $paperVenue);
					if($paper['venue'] === ''){
						$paper['venue'] = 'na';
					}

					/* Publication year */
					$paper['year']   = $row->find('td.gsc_a_y', 0)->plaintext;
					if($paper['year'] === ' '){
						$paper['year'] = '0000';
						$paper['date'] = '0000-00-00';
					}
					if (intval($paper['year']) > 0){
						array_push( $publications_per_year, intval($paper['year']) );
						$paper['date'] = intval($paper['year'])."-01-01";
					}




					$rowData[] = $paper;

					$publications_total++;
					$pubYear = intval($paper['year']);
					$min5y = $current_year - 5;
					if ($pubYear >= $min5y) $publications_5y++;
					if ($paper['citedBy'] > $most_paper_citations) $most_paper_citations = $paper['citedBy'];



					array_push($publicationsList, $paper);
				}

			}
			else{
				$response['success'] = 0;
				$response['message'] = "Publications table not found";
			}

		}


	}
	else{
		$response['success'] = 0;
		$response['message'] = "Failed to find";
	}





	asort($publications_per_year);



	$basic_info = array(
		"author_id" => $author->id,
		"provider_id" => $author->provider_id,
		"provider_author_id" => $author->provider_author_id
	);

	$metrics_total = array(
		"publications" => $publications_total,
		"citations" => $citations_total,
		"hindex" => $hindex,
		"i10index" => $i10index
	);

	$metrics_5y = array(
		"publications" => $publications_5y,
		"citations" => $citations_5y,
		"hindex" => $hindex_5y,
		"i10index" => $i10index_5y
	);


	$metrics_misc = array(
		"mindex" => $mindex,
		"most_paper_citations" => $most_paper_citations
	);




	$jsonAuthorData = array(
		"basic_info" => $basic_info,
		"metrics_total" => $metrics_total,
		"metrics_5y" => $metrics_5y,
		"metrics_misc" => $metrics_misc,
		"citations_per_year" => $citations_per_year,
		"publications_per_year" => array_count_values($publications_per_year)
	);


	$author->metrics_metadata = json_encode($jsonAuthorData);
	$author->publications_list = $publicationsList;





	return $author;
}





?>
