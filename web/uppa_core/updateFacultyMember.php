<?php
   session_start();
   require_once("classes/Message.php");
   require_once("functions.php");

   /* Language config */
   $_SESSION['lang'] = "el";
   if(isset($_GET['lang']) && !empty($_GET['lang'])) $_SESSION['lang'] = $_GET['lang'];
   if ($_SESSION['lang'] == "en") require_once("language/lang.en.php");
   else require_once("language/lang.el.php");
   /* End of language config */

   $facultyMember = $_POST["facultyMember"];

  if (empty($facultyMember["id"]) || empty($facultyMember["last_name"]) || empty($facultyMember["first_name"]) || empty($facultyMember["rank_id"])
   || empty($facultyMember["department_id"]) || ($facultyMember["is_valid"] != 0 AND $facultyMember["is_valid"] != 1)) {
    $msg = new Message("error", _EDIT_MEMBER_FORM_UPDATE_INFO_ERROR);
  } else if (!isset($_SESSION['role'])) {
    $msg = new Message("error", _EDIT_MEMBER_FORM_UPDATE_INFO_ERROR);
  } else if ($_SESSION['role'] != 'admin' && ($_SESSION['role']) == 'fm' && $_SESSION['member_id'] != $facultyMember["id"]) {
    $msg = new Message("error", _EDIT_MEMBER_FORM_UPDATE_INFO_ERROR);
   } else {

    $mysqli = createDatabaseConnection();

    if($facultyMember["phd_year"] == '') {
      $facultyMember["phd_year"] = 'NULL';
    }

    //only admin can change orcid id using a form
    //users get the orcid id after authenticating
    if ($_SESSION['role'] == 'admin' && isset($facultyMember['orcid_id'])) {
      if (!empty($facultyMember['orcid_id'])) {
        if (validate_orcid($facultyMember['orcid_id'])) {
          $orcid_sql = "`orcid_id`='".$facultyMember["orcid_id"]."',";
        } else { //wrong orcid id
          $orcid_error = true;
          $orcid_sql = '';
        }
      } elseif (empty($facultyMember['orcid_id'])) {
        $orcid_sql = "`orcid_id`=NULL,";
      }
    } else {
      $orcid_sql = '';
    }
    
    $sql = "UPDATE `faculty_member` SET
      `last_name`='".$facultyMember["last_name"]."',
      `first_name`='".$facultyMember["first_name"]."',
      `rank`='".$facultyMember["rank_id"]."',
      `phd_year`= ".$facultyMember["phd_year"].",
      `department`='".$facultyMember["department_id"]."',
      `google_scholar_id`='".$facultyMember["scholar_id"]."',
      `scopus_id`='".$facultyMember["scopus_id"]."',
      ".$orcid_sql."
      `isValidated`='".$facultyMember["is_valid"]."'
      WHERE `id`=".$facultyMember["id"]."";

    if ($mysqli->query($sql) === TRUE && !$orcid_error) {
      $msg = new Message("success",_EDIT_MEMBER_FORM_UPDATE_INFO_SUCCESS);
    } else {
      $msg = new Message("error", _EDIT_MEMBER_FORM_UPDATE_INFO_ERROR);
    }
   }
   echo json_encode($msg, JSON_UNESCAPED_UNICODE);
