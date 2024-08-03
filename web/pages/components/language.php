<?php
if ( !isset($_SESSION['lang']) ){
  $_SESSION['lang'] = 'el';
}

if( isset($_GET['lang']) && !empty($_GET['lang']) && ($_GET['lang']=='el' || $_GET['lang']=='en') ) {
  $_SESSION['lang'] = $_GET['lang'];
}

if ($_SESSION['lang'] == "en") {
  include_once("../uppa_core/language/lang.en.php");
} else {
  include_once("../uppa_core/language/lang.el.php");
}
?>
