<?php
   require_once("classes/Message.php");
   require_once("functions.php");

   /* Language config */
   $_SESSION['lang'] = "el";
   if(isset($_GET['lang']) && !empty($_GET['lang'])) $_SESSION['lang'] = $_GET['lang'];
   if ($_SESSION['lang'] == "en") require_once("language/lang.en.php");
   else require_once("language/lang.el.php");
   /* End of language config */

   $facultyMember = $_POST["facultyMember"];
   $mysqli = createDatabaseConnection();

  if($facultyMember["phd_year"] == '') {
    $facultyMember["phd_year"] = 'NULL';
  }

   $sql = "UPDATE `faculty_member` SET
     `last_name`='".$facultyMember["last_name"]."',
     `first_name`='".$facultyMember["first_name"]."',
     `rank`='".$facultyMember["rank_id"]."',
     `phd_year`= ".$facultyMember["phd_year"].",
     `department`='".$facultyMember["department_id"]."',
     `google_scholar_id`='".$facultyMember["scholar_id"]."',
     `scopus_id`='".$facultyMember["scopus_id"]."',
     `isValidated`='".$facultyMember["is_valid"]."'
     WHERE `id`=".$facultyMember["id"]."";

   if ($mysqli->query($sql) === TRUE) {
     $msg = new Message("success",_EDIT_MEMBER_FORM_UPDATE_INFO_SUCCESS);
   } else {
     $msg = new Message("error", _EDIT_MEMBER_FORM_UPDATE_INFO_ERROR);
   }

   echo json_encode($msg, JSON_UNESCAPED_UNICODE);
   $mysqli -> close();

   ?>
