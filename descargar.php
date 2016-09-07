<?php
define('CLIENTAREA', true);

require('../init.php');
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpcs.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc_wrappers.inc' ;


$ca = new WHMCS_ClientArea();
$ca->requireLogin();

if ($ca->isLoggedIn()) {
  $url = 'http://162.248.52.245:3470/';
  $db = 'nodux';
  $user = 'nodux';
  $pass = 'nodux';

  $clave = $_REQUEST['claveacceso'];
  $id = $_REQUEST['id'];
  $formato = $_REQUEST['formato'];
  if (!in_array($formato, array('pdf', 'xml'))) {
    $formato = 'xml';
  }

  function getPath($url, $user, $pass, $formato, $clave, $id) {
    $server = new xmlrpc_client($url);
    $message = new xmlrpcmsg('model.einvoice.einvoice.get_path',
    		array(new xmlrpcval($formato, 'string'),
    		    new xmlrpcval($clave, 'string'),
    		    new xmlrpcval($id, 'string'),
                    new xmlrpcval([], 'struct')));

    $server->setCredentials($user, $pass);
    $server->return_type = 'phpvals';
    $result=$server->send($message);
    $result=$result->value();
    return $result;
  }

  $resulthtml = getPath($url . $db, $user, $pass, $formato, $clave, $id);
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename=' . 'comp_elect_'.$claveacceso . $id .'.' . $formato);
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . strlen($resulthtml));
  ob_clean();
  flush();

  echo $resulthtml;

  exit();
}
