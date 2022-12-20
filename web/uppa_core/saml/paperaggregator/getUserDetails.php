<?php
require_once("../../functions.php");

function getUserDetails($username){

  $arrUser = array();
  $mysqli = createDatabaseConnection();

  $stmt = $mysqli->prepare('SELECT * FROM `pa_user` WHERE `username` = ?');
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $isVerified = false;
   while ($row = $result->fetch_assoc()) {
       $isVerified = true;
       $role = $row["role"];
       $viewMemberID = $row["viewFacultyMemberID"];
       break;
   }

   if ($isVerified){
     $arrUser["loggedin"] = true;
     $arrUser["username"] = $username;
     $arrUser["role"] = $role;

     if ($role == "fm"){
       $stmt = $mysqli->prepare('SELECT * FROM `faculty_member`, `department` WHERE `id` = ? AND `department`.`dpt_id` = `faculty_member`.`department`');
       $stmt->bind_param('s', $viewMemberID);
       $stmt->execute();

       $result = $stmt->get_result();
       while ($row = $result->fetch_assoc()) {
           $arrUser['last_name'] = $row["last_name"];
           $arrUser['first_name'] = $row["first_name"];
           $arrUser['dpt_school_id'] = $row["dpt_school_id"];
           $arrUser['department'] = $row["department"];
           $arrUser['id'] = $row["id"];
           break;
       }
     }
   } else {
     $arrUser['loggedin'] = 1;
     $arrUser['username'] = 'guest';
     $arrUser['role'] = 'guest';
   }

  $mysqli->close();


return json_encode($arrUser);
}

?>
