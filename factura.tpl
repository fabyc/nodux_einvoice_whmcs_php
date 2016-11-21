<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="{$charset}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$companyname} - {$pagetitle}</title>

    <!-- Bootstrap -->
    <link href="{$BASE_PATH_CSS}/bootstrap.min.css" rel="stylesheet">
    <link href="{$BASE_PATH_CSS}/font-awesome.min.css" rel="stylesheet">

    <!-- Styling -->
    <link href="templates/{$template}/css/overrides.css" rel="stylesheet">
    <link href="templates/{$template}/css/styles.css" rel="stylesheet">
    <link href="templates/{$template}/css/invoice.css" rel="stylesheet">

</head>
		
<div class="table-responsive">
<table id="tableInvoicesListClient" class="table table-condensed">
  <thead>
    <tr>
      <th class="text-center"> Tipo </th>
      <th class="text-center"> Fecha </th>
      <th class="text-center"> Numero </th>
      <th class="text-center"> Total </th>
      <th class="text-center"> Descargar </th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$resulthtml key=num item=row}
    <tr>
      {if $row[0] eq "e_invoice"} <td class="text-center">Factura </td>
      {elseif $row[0] eq "e_credit_note"} <td class="text-center">Nota de Credito</td>
      {/if}
      <td class="text-center"> {$row[1]} </td>
      <td class="text-center"> {$row[2]} </td>
      <td class="text-center"> {$row[4]} </td>
      <td class="text-center">
        <div class="btnfact">
          <a class="btn btn-success" href="descargar.php?formato=pdf&claveacceso={$row[3]}">
             PDF</a>  
          <a class="btn btn-success" href="descargar.php?formato=xml&claveacceso={$row[3]}">
            XML</a>
        </div>
      </td>
    </tr>   
  {/foreach}
  </tbody>
</table>

</div>

<p>* INFORMACION ADICIONAL....</p>
