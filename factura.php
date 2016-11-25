<?php
define('CLIENTAREA', true);

require('../init.php');
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpcs.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc_wrappers.inc' ;

$ca = new WHMCS_ClientArea();
$ca->setPageTitle('Comprobantes Electronicos');
$ca->initPage();
$ca->requireLogin(); // Uncomment this line to require a login to access this page

if ($ca->isLoggedIn()) {

  $url = 'http://162.248.52.245:3470/';
  $db = 'nodux_nodored';
  $user = 'whmcs';
  $pass = 'whmcs12354';

  #yamburara
  #$result = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid = 2 and relid=' . $ca->getUserID());

  $result = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid = 11 and relid=' . $ca->getUserID());
  $data = mysql_fetch_array($result);
  $identificacion = $data[0];

  function getInvoice($url, $user, $pass, $identificacion) {
    $server = new xmlrpc_client($url);
    $message = new xmlrpcmsg('model.einvoice.einvoice.get_invoice',
    		array(new xmlrpcval($identificacion, 'string'),
                    new xmlrpcval([], 'struct')));

    $server->setCredentials($user, $pass);
    $server->return_type = 'phpvals';
    $resultado=$server->send($message);
    $resultado=$resultado->value();
    return $resultado;
  }

$ca->assign('resulthtml', getInvoice($url . $db, $user, $pass, $identificacion));


}

$ca->setTemplate('factura');
$ca->output();
