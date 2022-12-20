<?php
require_once("version.php");

$uppa_page_title = array(
	"members" => _PAGE_MEMBERS,
	"edit_member" => _PAGE_EDIT_MEMBER,
	"new_report" => _PAGE_NEW_REPORT,
	"update_report" => _PAGE_UPDATE_REPORT,
	"view_reports" => _PAGE_VIEW_REPORTS,
	"view_report" => _PAGE_VIEW_REPORT,
	"login" => _PAGE_LOGIN,
	"tutorial" => _PAGE_TUTORIAL,
	"source_code" => _PAGE_SOURCE_CODE
);


function getSideBarMenu($role, $pageKey){
	$sidebarmenu = "<aside class='main-sidebar sidebar-dark-primary elevation-4'>";
	/* Logo and id */
	$sidebarmenu .=  "<div class='brand-link'><img src='../dist/img/logoAlexandriaSquare.png' alt='Alexandria Logo' class='brand-image img-circle elevation-3' style='opacity: 1.0'> <div class='brand-text font-weight-light'>"._ALEXANDRIA." <span class='menu-version'>"._VERSION_NUMBER."</span></div></div>";

	/* role */
	$sidebarmenu .= "<div class='sidebar'><div class='user-panel mt-3 pb-3 mb-3 d-flex'><div class='info user-side-info'>";
	if ($role == "fm") $sidebarmenu .= $_SESSION["member_lastname"].", ".$_SESSION["member_firstname"];
	else if ($role == "admin") $sidebarmenu .= _USERNAME_ADMIN;
	else if ($role == "guest") $sidebarmenu .= _USERNAME_VISITOR;

	$sidebarmenu .= "</div></div>";

	/* menu items */
	$sidebarmenu .= getSideBarMenuItems($pageKey);
	$sidebarmenu .= "</div></aside>";
	return $sidebarmenu;
}


function getSideBarMenuItems($key){

	$activeViewMembers = "";
	$activeViewReports = "";
	$activeNewReport = "";
	$activeSystemInfo = "";
	$activeChangelog = "";
	$activeTutorial = "";
	$activeSourceCode = "";

	if ($key == "members") $activeViewMembers = "active";
	else if ($key == "new_report") $activeNewReport = "active";
	else if ($key == "view_reports") $activeViewReports = "active";
	else if ($key == "tutorial") $activeTutorial = "active";
	else if ($key == "source_code") $activeSourceCode = "active";

	$sidebar = "<nav class='mt-2'>";
	$sidebar .= "<ul class='nav nav-pills nav-sidebar flex-column' data-widget='treeview' data-accordion='false'>";
	$sidebar .= "<li class='nav-header'>"._MENU_REPORTS."</li>";
	$sidebar .= "<li class='nav-item'><a href='reports.php' class='nav-link $activeViewReports'><i class='nav-icon fas fa-file'></i><p>"._MENU_VIEW_REPORTS."</p></a></li>";
	if ($_SESSION["role"] == "admin"){
		$sidebar .= "<li class='nav-item'><a href='createReport.php' class='nav-link $activeNewReport'><i class='nav-icon fas fa-file-medical'></i><p>"._MENU_NEW_REPORT."</p></a></li>";
	}
	if ($_SESSION["role"] == "fm"){
		$member_id = $_SESSION["member_id"];
		$sidebar .= "<li class='nav-header'>"._MENU_MEMBERS."</li>";
		$sidebar .= "<li class='nav-item'><a href='editMember.php?fmid=$member_id' class='nav-link $activeViewMembers'><i class='nav-icon fa fa-user'></i><p>"._MENU_VIEW_MY_PROFILE."</p></a></li>";
	} else if ($_SESSION["role"] == "admin"){
		$sidebar .= "<li class='nav-header'>ΜΕΛΗ ΔΕΠ</li>";
		$sidebar .= "<li class='nav-item'><a href='members.php' class='nav-link $activeViewMembers'><i class='nav-icon fa fa-users'></i><p>"._MENU_VIEW_MEMBERS."</p></a></li>";

	}
//$sidebar .= "<li class='nav-header'>ΔΙΑΧΕΙΡΙΣΗ</li>";
	//$sidebar .= "<li class='nav-item'><a href='#' class='nav-link $activeSystemInfo'><i class='nav-icon fas fa-info'></i><p>Πληροφορίες</p></a></li>";
	//$sidebar .= "<li class='nav-item'><a href='#' class='nav-link $activeChangelog'><i class='nav-icon fas fa-tasks'></i><p>Εξέλιξη</p></a></li>";
	if ($_SESSION["role"] != "guest"){
		$sidebar .= "<li class='nav-header'>"._MENU_LOGOUT."</li>";
		$sidebar .= "<li class='nav-item'><a href='../uppa_core/saml/paperaggregator/index.php?slo' class='nav-link'><i class='nav-icon fa fa-sign-out-alt'></i><p>"._MENU_USER_LOGOUT."</p></a></li>";
	} else {
		$sidebar .= "<li class='nav-header'>"._MENU_LOGIN."</li>";
		$sidebar .= "<li class='nav-item'><a href='login.php' class='nav-link'><i class='nav-icon fa fa-sign-in-alt'></i><p>"._MENU_USER_LOGIN."</p></a></li>";
	}

	/* Tutorial */
	$sidebar .= "<li class='nav-header'>"._MENU_HELP."</li>";
	$sidebar .= "<li class='nav-item'><a href='tutorial.php' class='nav-link $activeTutorial'><i class='nav-icon fa fa-question-circle'></i><p>"._MENU_TUTORIAL."</p></a></li>";


	/* Source code */
	$sidebar .= "<li class='nav-header'>"._MENU_CODE."</li>";
	$sidebar .= "<li class='nav-item'><a href='sourcecode.php' class='nav-link $activeSourceCode'><i class='nav-icon fa fa-light fa-code'></i><p>"._MENU_SOURCE_CODE."</p></a></li>";


	/* Language */
	/*
	$query = $_GET;
	$query_result = http_build_query($query);
	$sidebar .= "<li class='nav-header'>"._MENU_LANGUAGE."</li>";
	$sidebar .= "<li class='nav-item'><a href='".$_SERVER['PHP_SELF']."?".$query_result."&lang=el' class='nav-link'><i class='nav-icon far fa-users'></i><p>"._MENU_LANGUAGE_EL."</p></a></li>";
	$sidebar .= "<li class='nav-item'><a href='".$_SERVER['PHP_SELF']."?".$query_result."&lang=en' class='nav-link'><i class='nav-icon far fa-sign-in'></i><p>"._MENU_LANGUAGE_EN."</p></a></li>";
	*/

	$sidebar .= "</ul>";
	$sidebar .= "</nav>";


	return $sidebar;

}

function getTopNavBar($lang){
	/* Get language */
	$elClassLanguageSelected = $enClassLanguageSelected = $elLink = $enLink = "";
	$query = $_GET;
	$query_result = http_build_query($query);
	if ($lang == "el") {
		$elClassLanguageSelected = "language-selected";
		$enLink = "href='".$_SERVER['PHP_SELF']."?".$query_result."&lang=en'";
	} else if ($lang == "en") {
		$enClassLanguageSelected = "language-selected";
		$elLink = "href='".$_SERVER['PHP_SELF']."?".$query_result."&lang=el'";
	}
	/* Build navigation bar */
	$topNavBar = "";
	$topNavBar .= "<nav class='main-header navbar navbar-expand navbar-white navbar-light'>";
	$topNavBar .= "<ul class='navbar-nav'><li class='nav-item'><a class='nav-link' data-widget='pushmenu' href='#' role='button'><i class='fas fa-bars'></i><span class='sr-only'>Toggle Menu</span></a></li></ul>";
	$topNavBar .= "<ul class='navbar-nav ml-auto'>";
	$topNavBar .= "<li class='nav-item'><a class='nav-link $elClassLanguageSelected' $elLink>ΕΛ</a></li>";
	$topNavBar .= "<li class='nav-item'><a class='nav-link $enClassLanguageSelected' $enLink>EN</a></li>";
	$topNavBar .= "</ul></nav>";
	return $topNavBar;
}

function getFavicon(){
	$favicon = "<link rel='apple-touch-icon' sizes='180x1801' href='../dist/img/favicon/apple-touch-icon.png'>";
	$favicon .= "<link rel='icon' type='image/png' sizes='32x32' href='../dist/img/favicon/favicon-32x32.png'>";
	$favicon .= "<link rel='icon' type='image/png' sizes='16x16' href='../dist/img/favicon/favicon-16x16.png'>";
	$favicon .= "<link rel='manifest' href='../dist/img/favicon/site.webmanifest'>";
	return $favicon;
}

function getFooter(){
	$footer = "<footer class='main-footer'>
				<div class='row'>
					<div class='col-md-3'>
						"._FOOTER_COPYRIGHT."<br><a href='mailto:"._FOOTER_EMAIL."'>"._FOOTER_EMAIL."</a>
					</div>
					<div class='col-md-6'>
						<img alt='eu funding logo' src='../dist/img/epanadvm_footer_2.jpg' style='width:100%;'>
					</div>
					<div class='colm-md-3 ml-auto'>
						"._FOOTER_VERSION." "._VERSION_NUMBER."
					</div>
				</div>
			</footer>";
	return $footer;
}

?>
