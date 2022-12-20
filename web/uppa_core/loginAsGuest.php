<?php
session_start();
$_SESSION["loggedin"] = true;
$_SESSION["username"] = "guest";
$_SESSION["role"] = "guest";
$_SESSION["samlUserdata"] = "";
$_SESSION["lang"] = "el";
header("location: ../pages/reports.php");
?>
