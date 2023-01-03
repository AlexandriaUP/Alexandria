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

$SSO_protocol = 'CAS'; //SAML or CAS

$emailExt = ""; //e.g. @upatras.gr
$serverURL = ''; //e.g., https://alexandria.upatras.gr/

if ($SSO_protocol == 'SAML') {
    /* SAML SSO settings */
    $spBaseUrl = ''; // e.g., 'https://alexandria.upatras.gr/uppa_core/saml/';
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
} elseif ($SSO_protocol == 'CAS') {
    ///////////////////////////////////////
    // Basic Config of the phpCAS client //
    ///////////////////////////////////////

    // Full Hostname of your CAS Server
    $cas_host = '';

    // Context of the CAS Server
    $cas_context = '/cas';

    // Port of your CAS server. Normally for a https server it's 443
    $cas_port = 8443;

    //the base url of the CAS client service, e.g. https://alexandria.upatras.gr/. Usually the same as $serverURL, but not if the software is installed as a subdirectory.
    $cas_service_base_url = '';

    // Path to the ca chain that issued the cas server certificate
    $cas_server_ca_cert_path = '';

    $cas_url = 'https://' . $cas_host;
    if ($cas_port != '443') {
        $cas_url = $cas_url . ':' . $cas_port;
    }
    $cas_url = $cas_url . $cas_context;
}
