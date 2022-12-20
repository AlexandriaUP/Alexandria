<?php
require_once 'functions.php';
require_once 'classes/AuthorProfile.php';
require_once 'classes/Publication.php';
require_once 'classes/Message.php';
require_once 'settings/year.php';

set_time_limit(1000);

// 1. Get GET parameters
$member_id = $_GET["member_id"];
$scopus_id = $_GET["scopus_id"];
$phd_year = $_GET["phd_year"];
$report_id = $_GET["report_id"];
$current_year = intval(_CURRENT_YEAR);




/* Scopus API keys */
$api_key_core = _SCOPUS_API_KEY_CORE;
$api_key_citations = _SCOPUS_API_KEY_CITATIONS;
$api_key_scival = _SCOPUS_API_KEY_SCIVAL;


/* Initial declaration of metrics (0) */
$publications_total = $citations_total = $hindex = $i10index = 0;			//total
$publications_5y = $citations_5y = $hindex_5y = $i10index_5y = 0;			//5 yrs
$mindex = $most_paper_citations = 0;																	//misc
$citations_per_year = $publications_per_year = array();
$citations_per_year[$current_year-5] =
		$citations_per_year[$current_year-4] =
		$citations_per_year[$current_year-3] =
		$citations_per_year[$current_year-2] =
		$citations_per_year[$current_year-1] = 0;
$msg = new Message("error", "");




/* Step 1: Get core information */
$url = "https://api.elsevier.com/content/author/author_id/$scopus_id?apiKey=$api_key_core&view=metrics";
$resp = json_decode(executeCURL($url), true);
/* The core data is found */
if ( isset($resp['author-retrieval-response']) ){
	$hindex = $resp['author-retrieval-response'][0]['h-index'];
	$publications_total = $resp['author-retrieval-response'][0]['coredata']['document-count'];
	$citations_total = $resp['author-retrieval-response'][0]['coredata']['citation-count'];
	if ( $phd_year != 0 ) $mindex = round( $hindex / ($current_year - $phd_year), 3 );
} else {
	$msg->type = "error";
	$msg->content = "Προέκυψε κάποιο πρόβλημα με τη συλλογή των βασικών δεδομένων";
	$msg->member_id = $member_id;
	echo json_encode($msg); //It is returned to AJAX call
	exit();
}


$count_max = intval(_COUNT_MAX);
$publications_remaining = $publications_total;
$reps = $publications_total/$count_max;

/* Open database with Alexandria Scimago DB to get Q1-4 for publication */
$scimago_mysqli = connectToAlexandriaScimagoDatabase();
$publications = array();

for ($i=0; $i<= $reps; $i++){
	$startpoint = $publications_total - $publications_remaining;
	if ($publications_remaining >= $count_max) $count = $count_max;
	else $count = $publications_remaining;
	$publications_remaining -= $count_max;

	$url = "https://api.elsevier.com/content/search/scopus?query=AU-ID($scopus_id)&apiKey=$api_key_core&count=$count&start=$startpoint";
	$resp = json_decode(executeCURL($url), true);

	/* The query returned results */
	if ( isset($resp["search-results"]) ){
		$resp_entries = $resp["search-results"]["entry"];
		// $publications = array();
		foreach ($resp_entries as $p){
			$publication = new Publication();
			$publication->facultymember_id = $member_id;
			$publication->report_id = $report_id;
			$publication->provider_id = "scopus";
			if (isset($p["dc:title"])) $publication->pub_title = $p["dc:title"]; else $publication->pub_title = "";
			if (isset($p["dc:creator"])) $publication->pub_authors = $p["dc:creator"]; else $publication->pub_authors = "";
			if (isset($p["prism:publicationName"])) $publication->pub_venue = $p["prism:publicationName"]; else $publication->pub_venue = "";
			if (isset($p["prism:coverDate"])) $publication->pub_date = $p["prism:coverDate"]; else $publication->pub_date = "";
			if (isset($p["prism:doi"])) $publication->pub_doi = $p["prism:doi"]; else $publication->pub_doi = "";
			if (isset($p["citedby-count"])) $publication->pub_citedby = $p["citedby-count"]; else $publication->pub_citedby = "";
			if (isset($p["prism:issn"])) $publication->pub_issn = $p["prism:issn"]; else $publication->pub_issn = "";
			if (isset($p["prism:eIssn"])) $publication->pub_eissn = $p["prism:eIssn"]; else $publication->pub_eissn = "";
			if (isset($p["prism:aggregationType"])) $publication->pub_type = $p["prism:aggregationType"]; else $publication->pub_type = "";
			if (isset($p["subtype"])) $publication->pub_subtype = $p["subtype"]; else $publication->pub_subtype = "";
			if (isset($p["subtypeDescription"])) $publication->pub_subtype_description = $p["subtypeDescription"]; else $publication->pub_subtype_description = "";
			if (isset($p["source-id"])) $publication->pub_source_id = $p["source-id"]; else $publication->pub_source_id = "";
			if (isset($p["dc:identifier"])) $publication->pub_provider_id = $p["dc:identifier"]; else $publication->pub_provider_id = "";
			if (isset($p["prism:coverDate"]))	$publication->year = substr($publication->pub_date, 0, 4);

			if ( isset($p["prism:issn"]) && isset($p["prism:coverDate"]) && $publication->pub_type == "Journal" )
					$publication->q = getScimagoQforPublication($scimago_mysqli, $publication->pub_issn, $publication->year);
			else $publication->q = null;
			array_push($publications, $publication);
		}
	} else {
		$msg->type = "error";
		$msg->content = "Προέκυψε κάποιο πρόβλημα με τη συλλογή των δημοσιεύσεων";
		$msg->member_id = $member_id;
		echo json_encode($msg); //It is returned to AJAX call
		exit();
	}


	//echo $url."</br>";
}
$scimago_mysqli -> close();




/* Step 3: Calculate publications_per_year, i10 index, and publications last 5 years*/
foreach ($publications as $p){
	array_push($publications_per_year, intval($p->year));
	if ( intval($p->pub_citedby) >= 10) $i10index++; // total
	if ( (intval($p->pub_citedby) >= 10) && (intval($p->year) >= ($current_year - 5)) ) $i10index_5y++;
	if ( intval($p->year) > ($current_year - 5) ) $publications_5y++;
	if ( intval($p->pub_citedby) > $most_paper_citations) $most_paper_citations = intval($p->pub_citedby);
}



/* Step 4: Calculate citations last 5 years and per year citations */
//foreach ($publication as $p){
//	$pub_scopus_id = str_replace("SCOPUS_ID:", "", $p->pub_provider_id);		//Get scopus ID
//}

$citations_per_publication_last_5_years = array();
$h5_start_year = $current_year-5;
$h5_end_year = $current_year-1;
foreach ($publications as $p){
	$pub_scopus_id = str_replace("SCOPUS_ID:", "", $p->pub_provider_id);		//Get scopus ID
	$url ="https://api.elsevier.com/content/abstract/citations?scopus_id=$pub_scopus_id&apiKey=$api_key_citations&date=".$h5_start_year."-".$h5_end_year;
	$resp = json_decode(executeCURL($url), true);
	if ( isset($resp["abstract-citations-response"]) ){
		$resp_entries = $resp["abstract-citations-response"]["citeColumnTotalXML"]["citeCountHeader"]["columnTotal"];
		$citations_per_year[$current_year-5] += intval($resp_entries[0]["$"]);
		$citations_per_year[$current_year-4] += intval($resp_entries[1]["$"]);
		$citations_per_year[$current_year-3] += intval($resp_entries[2]["$"]);
		$citations_per_year[$current_year-2] += intval($resp_entries[3]["$"]);
		$citations_per_year[$current_year-1] += intval($resp_entries[4]["$"]);

		$p->citations_5yrs = intval($resp_entries[0]["$"]) + intval($resp_entries[1]["$"]) + intval($resp_entries[2]["$"]) + intval($resp_entries[3]["$"]) + intval($resp_entries[4]["$"]);
		array_push($citations_per_publication_last_5_years, intval($p->citations_5yrs));
	}
	//usleep( 200 * 1000 );
}

/* Calculate citations last 5 years */
foreach ($citations_per_year as $cpy){
	$citations_5y += intval($cpy);
}


/* Calculate h-index last 5 years */
rsort($citations_per_publication_last_5_years);
for ($i=0; $i<count($citations_per_publication_last_5_years); $i++){
	if ($i == $citations_per_publication_last_5_years[$i]) {
		$hindex_5y = $i;
		break;
	}
	else if ($i > $citations_per_publication_last_5_years[$i]) {
		$hindex_5y = $i - 1;
		break;
	}

}





//We can also use https://opencitations.net/index/coci/api/v1/citations/10.1007/s10639-019-09869-4

/* Step 5: Formating the last */
$basic_info = array(
	"author_id" => $member_id,
	"provider_id" => "scopus",
	"provider_author_id" => $scopus_id
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

asort($publications_per_year);
asort($citations_per_year);

$jsonAuthorData = array(
	"basic_info" => $basic_info,
	"metrics_total" => $metrics_total,
	"metrics_5y" => $metrics_5y,
	"metrics_misc" => $metrics_misc,
	"citations_per_year" => $citations_per_year,
	"publications_per_year" => array_count_values($publications_per_year)
);

$metrics_metadata = json_encode($jsonAuthorData);
//print_r( $metrics_metadata );


/* Step 6: Update database */
$mysqli = createDatabaseConnection();
$sql = "UPDATE `faculty_member_in_report`
				SET `metrics_metadata`='$metrics_metadata'
				WHERE `report_id`='$report_id' AND `facultymember_id`='$member_id' AND `provider_id`='scopus'";

if ($mysqli->query($sql) === TRUE) {
	// Get publications
	$stmt = $mysqli->prepare ("INSERT INTO `publication_of_faculty_member_in_report`(`facultymember_id`, `report_id`, `provider_id`, `pub_title`, `pub_authors`, `pub_venue`, `pub_date`, `pub_doi`, `pub_citedby`, `pub_issn`, `pub_type`, `pub_subtype`, `pub_subtype_description`, `pub_source_id`, `pub_provider_id`, `scimagojr_q`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	foreach ($publications as $p){
		$stmt->bind_param("iissssssisssssss",
			$p->facultymember_id,
			$p->report_id,
			$p->provider_id,
			$p->pub_title,
			$p->pub_authors,
			$p->pub_venue,
			$p->pub_date,
			$p->pub_doi,
			$p->pub_citedby,
			$p->pub_issn,
			$p->pub_type,
			$p->pub_subtype,
			$p->pub_subtype_description,
			$p->pub_source_id,
			$p->pub_provider_id,
			$p->q
		 );
		 $stmt->execute();
	}

	$msg->type = "success";
	$msg->content = $member_id;
	$msg->member_id = $member_id;


} else {
	echo "Error: " . $sql . "<br>" . $mysqli->error;
}
$mysqli -> close();


echo json_encode($msg);


?>
