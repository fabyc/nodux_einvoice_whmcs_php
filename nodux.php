<?php
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpcs.inc' ;
require_once 'phpxmlrpc-4.0.0/lib/xmlrpc_wrappers.inc' ;


$factura = $_POST['id_invoice'];
$notaCredito = $_POST['id_credito'];
$ride = $_POST['ride'];

$url = 'http://162.248.52.245:3470/';
$db = 'nodux_nodored';
$user = 'whmcs';
$pass = 'whmcs12354';
$formato = 'pdf';


if ( ($_SERVER['HTTP_HOST']=="nodored.com" || $_SERVER['HTTP_HOST']=="www.nodored.com") && $_SERVER['SERVER_ADDR']=="67.225.176.145" ) {

    if ($ride != "") {
    	function getPathAdm($url, $user, $pass, $ride) {
	    $server = new xmlrpc_client($url);
	    $message = new xmlrpcmsg('model.einvoice.einvoice.get_path_adm',
	    		array(new xmlrpcval($ride, 'string'),
	                    new xmlrpcval([], 'struct')));

	    $server->setCredentials($user, $pass);
	    $server->return_type = 'phpvals';
	    $result=$server->send($message);
	    $result=$result->value();
	    return $result;
	  }

	  $resulthtml = getPathAdm($url . $db, $user, $pass, $ride);
	  header('Content-Type: application/octet-stream');
	  header('Content-Disposition: attachment; filename=' . 'comp_elect_'.$ride .'.' . $formato);
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
    if ($ride == "") {

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
      $result_invoice = mysql_query('SELECT id, date, duedate, subtotal, tax FROM tblinvoices WHERE id='. $id_cbte);
      $result_id = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid = 11 and relid=' . $client);
      $result_razon_social = mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid = 47 and relid=' . $client);
      $result_items = mysql_query('SELECT description, amount FROM tblinvoiceitems WHERE invoiceid='. $id_cbte);
      $result_clients = mysql_query('SELECT firstname, lastname, email, address1, city, state, country, phonenumber FROM tblclients WHERE id='. $client);
      $data = mysql_fetch_array($result_invoice);
      $data_id = mysql_fetch_array($result_id);
      $data_razon_social = mysql_fetch_array($result_razon_social);
      $data_clients = mysql_fetch_array($result_clients);

      $id = utf8_decode($data[0]);
      $date = utf8_decode($data[1]);
      $duedate = utf8_decode($data[2]);
      $subtotal = utf8_decode($data[3]);
      $total = utf8_decode($data[4]);

      $identificacion = utf8_decode($data_id[0]);

      $razon_social = utf8_decode($data_razon_social[0]);

      $firstname = utf8_decode($data_clients[0]);
      $lastname = utf8_decode($data_clients[1]);
      $email = utf8_decode($data_clients[2]);
      $address = utf8_decode($data_clients[3]);
      $city = utf8_decode($data_clients[4]);
      $state= utf8_decode($data_clients[5]);
      $country = utf8_decode($data_clients[6]);
      $phonenumber = utf8_decode($data_clients[7]);

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
                        new xmlrpcval($razon_social, 'string'),
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

    $resultado = sendInvoice($url . $db, $user, $pass, $tipo, $id, $date, $duedate, $subtotal, $total, $identificacion, $datos, $razon_social, $firstname, $lastname, $email, $address, $city, $state, $country,$phonenumber);
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
}
}else{
    $origen= $_SERVER["HTTP_REFERER"];

    echo "<script language='JavaScript'>alert('No puede ejecutar WHMCS-Nodux porque su servidor no está autorizado.');</script>";

    echo '<script languaje="JavaScript">
          var origen="'.$origen.'";
        </script>';

    echo "<script language='javascript'>function redireccionar(){window.location=origen;}</script>";
    echo "<script language='JavaScript'>setTimeout ('redireccionar()', 1);</script>";

}
