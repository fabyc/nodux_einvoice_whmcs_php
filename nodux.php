<?php
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpcs.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc_wrappers.inc' ;

#yamburara
#id = 2
#razon = 9

$factura = $_POST['id_invoice'];
$notaCredito = $_POST['id_credito'];

$url = 'http://162.248.52.245:3470/';
$db = 'nodux_nodored';
$user = 'whmcs';
$pass = 'whmcs12354';

if ( ($_SERVER['HTTP_HOST']=="nodored.com" || $_SERVER['HTTP_HOST']=="www.nodored.com") && $_SERVER['SERVER_ADDR']=="67.225.176.145" ) {

    if ($factura != "") {
       $tipo = "factura";
       $id_cbte = $factura;
    }

    if ($notaCredito != "") {
       $tipo = "credito";
       $id_cbte = $notaCredito;
    }

    $conn = mysql_connect("localhost","joomfast_82015","u58Uea(E2oW]");
    mysql_select_db("joomfast_82015",$conn);
    $query = ("SELECT * FROM wp_comprobantes WHERE cedula = '$user'");
    $result = mysql_query($query, $conn);

    $result_verificacion = mysql_query('SELECT id FROM tblinvoices WHERE id='. $id_cbte);

    if (mysql_fetch_array($result_verificacion)){
      $result_client = mysql_query('SELECT userid FROM tblinvoices WHERE id='. $id_cbte);
      $data_client = mysql_fetch_array($result_client);
      $client = $data_client[0];
      $result_invoice = mysql_query('SELECT id, date, duedate, subtotal, total FROM tblinvoices WHERE id='. $id_cbte);
      $result_id = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid = 11 and relid=' . $client);
      $result_razon_social = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid = 47 and relid=' . $client);
      $result_items = mysql_query('SELECT description, amount FROM tblinvoiceitems WHERE invoiceid='. $id_cbte);
      $result_clients = mysql_query('SELECT firstname, lastname, email, address1, city, state, country, phonenumber FROM tblclients WHERE id='. $client);
      $data = mysql_fetch_array($result_invoice);
      $data_id = mysql_fetch_array($result_id);
      $data_razon_social = mysql_fetch_array($result_razon_social);
      $data_clients = mysql_fetch_array($result_clients);

    $id = $data[0];
    $date = $data[1];
    $duedate = $data[2];
    $subtotal = $data[3];
    $total = $data[4];

    $identificacion = $data_id[0];

    $razon_social = $data_razon_social[0];

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

    function sendInvoice($url, $user, $pass, $tipo, $id, $date, $duedate, $subtotal, $total, $identificacion, $datos, $razon_social, $firstname, $lastname, $email, $address, $city, $state, $country,$phonenumber) {

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
    $origen= $_SERVER["HTTP_REFERER"];

    if ($resultado) {
      if ($resultado->value()) {
        $val=$resultado->value();
        $origen= $_SERVER["HTTP_REFERER"];
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='{$charset}'/>";
        echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
        echo "<title>FACTURACION ELECTRONICA</title>";
        echo "<link href='styles.css' rel='stylesheet'>";
        echo "<link href='overrides.css' rel='stylesheet'>";

        echo "</head>";

        echo '<script languaje="JavaScript">
          var val="'.$val.'";
          alert(val);
        </script>';


      }else {
        $origen= $_SERVER["HTTP_REFERER"];
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='{$charset}'/>";
        echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
        echo "<title>FACTURACION ELECTRONICA</title>";
        echo "<link href='styles.css' rel='stylesheet'>";
        echo "<link href='overrides.css' rel='stylesheet'>";


        echo "</head>";

        echo "<script language='JavaScript'>alert('Se ha producido un error al enviar el Comprobante, contacte con el Administrador');</script>";

      }
    } else {
      $origen= $_SERVER["HTTP_REFERER"];
      echo "<html lang='es'>";
      echo "<head>";
      echo "<meta charset='{$charset}'/>";
      echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
      echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
      echo "<title>FACTURACION ELECTRONICA</title>";
      echo "<link href='styles.css' rel='stylesheet'>";
      echo "<link href='overrides.css' rel='stylesheet'>";


      echo "</head>";

      echo "<script language='JavaScript'>alert('Se ha producido un error al enviar el Comprobante, contacte con el Administrador');</script>";

    }

    }else{
      $origen= $_SERVER["HTTP_REFERER"];
      echo "<html lang='es'>";
      echo "<head>";
      echo "<meta charset='{$charset}'/>";
      echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";
      echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
      echo "<title>FACTURACION ELECTRONICA</title>";
      echo "<link href='styles.css' rel='stylesheet'>";
      echo "<link href='overrides.css' rel='stylesheet'>";

      echo "</head>";

      echo "<script language='JavaScript'>alert('Factura no existe verifique el número');</script>";


    }
    echo '<script languaje="JavaScript">
          var origen="'.$origen.'";
        </script>';

    echo "<script language='javascript'>function redireccionar(){window.location=origen;}</script>";
    echo "<script language='JavaScript'>setTimeout ('redireccionar()', 1);</script>";

}else{
    $origen= $_SERVER["HTTP_REFERER"];

    echo "<script language='JavaScript'>alert('No puede ejecutar WHMCS-Nodux porque su servidor no está autorizado.');</script>";

    echo '<script languaje="JavaScript">
          var origen="'.$origen.'";
        </script>';

    echo "<script language='javascript'>function redireccionar(){window.location=origen;}</script>";
    echo "<script language='JavaScript'>setTimeout ('redireccionar()', 1);</script>";

}
