<?php
session_start();

require_once("../uppa_core/functions.php");

if (!isset($_REQUEST['fmid']) OR !isset($_REQUEST['orcidid'])) {
    exit;
}
if (!isset($_SESSION['role'])) {
    exit;
} elseif ($_SESSION['role'] == 'guest') {
    exit;
} elseif ($_SESSION['role'] == 'fm' && $_REQUEST['fmid'] != $_SESSION['member_id']) {
    exit;
} elseif (!validate_orcid($_REQUEST['orcidid'])) {
    exit;
}

$mysqli = createDatabaseConnection();
$sql = "UPDATE faculty_member SET orcid_id = ? WHERE id = ?";
$stmt= $mysqli->prepare($sql);
$stmt->execute([$_REQUEST['orcidid'], $_REQUEST['fmid']]);
echo "OK";
