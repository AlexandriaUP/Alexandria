<?php
/**
 *  SAML Handler
 */
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;
session_start();

require_once dirname(__DIR__).'/_toolkit_loader.php';
require_once dirname(__DIR__).'/extlib/xmlseclibs/xmlseclibs.php';
require_once 'getUserDetails.php';

$auth = new Auth($settingsInfo);

if (isset($_GET['sso'])) {
    $auth->login();
} else if (isset($_GET['sso2'])) {
    $returnTo = $serverURL.'/pages/reports.php';
    //echo $spBaseUrl;
    $auth->login($returnTo);
} else if (isset($_GET['slo'])) {
    $returnTo = null;
    $parameters = array();
    $nameId = null;
    $sessionIndex = null;
    $nameIdFormat = null;
    $samlNameIdNameQualifier = null;
    $samlNameIdSPNameQualifier = null;

    if (isset($_SESSION['samlNameId'])) {
        $nameId = $_SESSION['samlNameId'];
    }
    if (isset($_SESSION['samlNameIdFormat'])) {
        $nameIdFormat = $_SESSION['samlNameIdFormat'];
    }
    if (isset($_SESSION['samlNameIdNameQualifier'])) {
        $samlNameIdNameQualifier = $_SESSION['samlNameIdNameQualifier'];
    }
    if (isset($_SESSION['samlNameIdSPNameQualifier'])) {
        $samlNameIdSPNameQualifier = $_SESSION['samlNameIdSPNameQualifier'];
    }
    if (isset($_SESSION['samlSessionIndex'])) {
        $sessionIndex = $_SESSION['samlSessionIndex'];
    }

    $auth->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $samlNameIdNameQualifier, $samlNameIdSPNameQualifier);


} else if (isset($_GET['acs'])) {
    if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
        $requestID = $_SESSION['AuthNRequestID'];
    } else {
        $requestID = null;
    }

    $auth->processResponse($requestID);

    $errors = $auth->getErrors();

    if (!empty($errors)) {
        echo '<p>',implode(', ', $errors),'</p>';
        if ($auth->getSettings()->isDebugActive()) {
            echo '<p>'.htmlentities($auth->getLastErrorReason()).'</p>';
        }
    }

    if (!$auth->isAuthenticated()) {
        echo "<p>Not authenticated</p>";
        exit();
    }


    $_SESSION['samlUserdata'] = $auth->getAttributes();
    $_SESSION['samlNameId'] = $auth->getNameId();
    $_SESSION['samlNameIdFormat'] = $auth->getNameIdFormat();
    $_SESSION['samlNameIdNameQualifier'] = $auth->getNameIdNameQualifier();
    $_SESSION['samlNameIdSPNameQualifier'] = $auth->getNameIdSPNameQualifier();
    $_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
    unset($_SESSION['AuthNRequestID']);
    if (isset($_POST['RelayState']) && Utils::getSelfURL() != $_POST['RelayState']) {
        // To avoid 'Open Redirect' attacks, before execute the
        // redirection confirm the value of $_POST['RelayState'] is a // trusted URL.
        $auth->redirectTo($_POST['RelayState']);
    }




} else if (isset($_GET['sls'])) {
    if (isset($_SESSION) && isset($_SESSION['LogoutRequestID'])) {
        $requestID = $_SESSION['LogoutRequestID'];
    } else {
        $requestID = null;
    }

    $auth->processSLO(false, $requestID);
    $errors = $auth->getErrors();
    if (empty($errors)) {
        echo '<p>Sucessfully logged out</p>';
        //header("location: ../../pages/login.php");
    } else {
        echo '<p>', htmlentities(implode(', ', $errors)), '</p>';
        if ($auth->getSettings()->isDebugActive()) {
            echo '<p>'.htmlentities($auth->getLastErrorReason()).'</p>';
        }
    }
}



if (isset($_SESSION['samlUserdata'])) {
    $username = $_SESSION['samlUserdata'][$uid_key][0];    // Get the session username
    $username = strtoupper($username."".$emailExt);       // Convert username to USERNAME@ORG.GR (as in table `pa_user` of Alexandria db)
    fillUserData($username);
    header("location: $serverURL/pages/reports.php");

    if (!empty($_SESSION['samlUserdata'])) {
        $attributes = $_SESSION['samlUserdata'];
        echo 'You have the following attributes:<br>';
        echo '<table><thead><th>Name</th><th>Values</th></thead><tbody>';
        foreach ($attributes as $attributeName => $attributeValues) {
            echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
            foreach ($attributeValues as $attributeValue) {
                echo '<li>' . htmlentities($attributeValue) . '</li>';
            }
            echo '</ul></td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo "<p>You don't have any attribute</p>";
    }

    echo '<p><a href="?slo" >Logout</a></p>';
} else {
    echo '<p><a href="?sso" >Login</a></p>';
    echo '<p><a href="?sso2" >Login and access to attrs.php page</a></p>';
    header("location: $serverURL/pages/login.php");
}



function fillUserData($username){
  $result = getUserDetails($username);
  $jsonResult = json_decode(str_replace ('\"','"', $result), true);

  $_SESSION["loggedin"] = $jsonResult["loggedin"];
  $_SESSION["username"] = $jsonResult["username"];
  $_SESSION["role"] = $jsonResult["role"];
  if ($_SESSION["role"] == "fm"){
    $_SESSION['member_lastname'] = $jsonResult["last_name"];
    $_SESSION['member_firstname'] = $jsonResult["first_name"];
    $_SESSION['member_school'] = $jsonResult["dpt_school_id"];
    $_SESSION['member_dept'] = $jsonResult["department"];
    $_SESSION['member_id'] = $jsonResult["id"];
  }
}
