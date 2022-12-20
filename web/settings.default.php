<?php
/* All settings necessary for Alexandria to function properly should be defined below */

/* DB settings */
define("_ALEXANDRIA_DB_HOST", "");
define("_ALEXANDRIA_DB_USER", "");
define("_ALEXANDRIA_DB_PASSWORD", "");
define("_ALEXANDRIA_DB_NAME", "");

define("_ALEXANDRIA_SCIMAGO_DB_HOST", "");
define("_ALEXANDRIA_SCIMAGO_DB_USER", "");
define("_ALEXANDRIA_SCIMAGO_DB_PASSWORD", "");
define("_ALEXANDRIA_SCIMAGO_DB_NAME", "");

/* Scopus keys */
define("_SCOPUS_API_KEY_CORE", "");
define("_SCOPUS_API_KEY_CITATIONS", "");
define("_SCOPUS_API_KEY_SCIVAL", "");
define("_COUNT_MAX", "200");

/* SAML SSO settings */
$emailExt = ""; //e.g. @upatras.gr
$spBaseUrl = ''; // e.g., 'https://alexandria.upatras.gr/uppa_core/saml/';
$serverURL = ''; //e.g., https://alexandria.upatras.gr/
$settingsInfo = array (
    'sp' => array (
        'entityId' => '', // e.g., https://organization.gr/shibboleth
        'assertionConsumerService' => array (
            'url' => $spBaseUrl.'/paperaggregator/index.php?acs',
        ),
        'singleLogoutService' => array (
            'url' => '', // e.g., https://alexandria.upatras.gr/
        ),
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
    ),
    'idp' => array (
        'entityId' => '', //e.g., https://idp.organization.gr/sidp/
        'singleSignOnService' => array (
            'url' => '', //e.g., https://organization/SSO
        ),
        'singleLogoutService' => array (
            'url' => '', //e.g., https://organization/SLS
        ),
        'x509cert' => '', 
    ),
);

$uid_key = 'urn:oid:0.9.2342.19200300.100.1.1';
