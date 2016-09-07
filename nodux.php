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
  $factura = $_REQUEST['factura'];
  $tipo = $_REQUEST['tipo'];
  $url = 'http://162.248.52.245:3470/';
  $db = 'nodux';
  $user = 'nodux';
  $pass = 'nodux';

  $result_invoice = mysql_query('SELECT id, date, duedate, subtotal, total FROM tblinvoices WHERE id='. $factura);
  $result_id = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE relid=' . $ca->getUserID());
  $result_items = mysql_query('SELECT description, amount FROM tblinvoiceitems WHERE invoiceid='. $factura);
  $result_clients = mysql_query('SELECT firstname, lastname, email, address1, city, state, country, phonenumber FROM tblclients WHERE id='. $ca->getUserID());
  $data = mysql_fetch_array($result_invoice);
  $data_id = mysql_fetch_array($result_id);
  $data_clients = mysql_fetch_array($result_clients);

  $id = $data[0];
  $date = $data[1];
  $duedate = $data[2];
  $subtotal = $data[3];
  $total = $data[4];

  $identificacion = $data_id[0];

  $firstname = $data_clients[0];
  $lastname = $data_clients[1];
  $email = $data_clients[2];
  $address = $data_clients[3];
  $city = $data_clients[4];
  $state= $data_clients[5];
  $country = $data_clients[6];
  $phonenumber = $data_clients[7];

  while ($row = mysql_fetch_array($result_items)){
    $datos[]= $row[0]." -- ".$row[1];
    }


  function sendInvoice($url, $user, $pass, $tipo, $id, $date, $duedate, $subtotal, $total, $identificacion, $datos, $firstname, $lastname, $email, $address, $city, $state, $country,$phonenumber) {

    $server = new xmlrpc_client($url);
    $message = new xmlrpcmsg('model.einvoice.einvoice.save_invoice',
    		array(new xmlrpcval($tipo, 'string'),
    		    new xmlrpcval($id, 'int'),
                    new xmlrpcval($date, 'string'),
                    new xmlrpcval($duedate, 'string'),
                    new xmlrpcval($subtotal, 'string'),
                    new xmlrpcval($total, 'string'),
                    new xmlrpcval($identificacion, 'string'),
                    new xmlrpcval(xmlrpc_encode(array($datos))),
                    new xmlrpcval($firstname, 'string'),
                    new xmlrpcval($lastname, 'string'),
                    new xmlrpcval($email, 'string'),
                    new xmlrpcval($address, 'string'),
                    new xmlrpcval($city, 'string'),
                    new xmlrpcval($state, 'string'),
                    new xmlrpcval($country, 'string'),
                    new xmlrpcval($phonenumber, 'string'),
                    new xmlrpcval([], 'struct')));

    $server->setCredentials($user, $pass);
    $server->return_type = 'phpvals';
    $result=$server->send($message);
    return $result;
   }

$resultado = sendInvoice($url . $db, $user, $pass, $tipo, $id, $date, $duedate, $subtotal, $total, $identificacion, $datos, $firstname, $lastname, $email, $address, $city, $state, $country,$phonenumber);
$ca->assign('resulthtml', $resultado);

}

$ca->setTemplate('nodux');

$ca->output();
