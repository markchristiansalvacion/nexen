<?php
ob_start();
require_once 'includes/load.php';
?>
<?php
/** To Generate Printable PDF */
include_once 'fpdf/fpdf.php'; // fpdf
include_once 'fpdf/easytable/exfpdf.php'; // exfpdf
include_once 'fpdf/easytable/easyTable.php'; // easytable
?>

<?php

$transaction_type = $_GET['transaction_type'];

$date_today = date('Y-m-d');
if ($transaction_type == 'STR') {

  $get_all_outbound = $db->query('SELECT  a.document_no, a.ref_no, a.picker,b.destination_name FROM tb_outbound a INNER JOIN tb_destination b ON b.destination_code = a.destination_code')->fetch_all();


  $pdf = new exFPDF('L', 'mm',  array(105, 148));
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->SetFont('arial', '', 10);

  $max_page = 0;
  while ($max_page  != 10) {




    $tb_header = new easyTable($pdf, 2);
    $tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w40; align:L; ');
    $tb_header->easyCell('PICKING TAG', 'font-size:18; font-style:B;  valign:M');

    $tb_header->printRow();
    $tb_header->endTable(2);



    $delivery_details = new easyTable($pdf, '{150,500,200,190}');

    $delivery_details->rowStyle('font-size:9; valign:M;');
    $delivery_details->easyCell('<b>Ref #:</b>', 'font-size:9');
    $delivery_details->easyCell($_GET['document_no'] . "-0000" . $max_page, 'border-color:#afb5bf; font-size:10; align:L;font-style:B;');
    $delivery_details->easyCell('<b>Date:</b>', 'font-size:9; align:R');
    $delivery_details->easyCell($date_today, 'border-color:#afb5bf; font-size:9; align:C');
    $delivery_details->printRow();

    $delivery_details->rowStyle('font-size:9; valign:M;');
    $delivery_details->easyCell('', 'font-size:10');
    $delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:L');
    $delivery_details->easyCell('<b>Shift:</b>', 'font-size:9; align:R');
    $delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:C');
    $delivery_details->printRow();

    $delivery_details->endTable(2);

    $tb_info = new easyTable($pdf, '{30,100}');
    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Document No:</b>', 'align:L;');
    $tb_info->easycell($_GET['document_no'], 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Branch:</b>', 'align:L;');
    $tb_info->easycell($_GET['destination'], 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Picker:</b>', 'align:L;');
    $tb_info->easycell('', 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>No. of Box:</b>', 'align:L;');
    $tb_info->easycell('', 'align:C;');
    $tb_info->printRow();
    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Pallet No:</b>', 'align:L;');
    $tb_info->easycell('__________ of _________', 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:7; valign:L;');
    $tb_info->easycell('<b>REMINDERS:</b>', 'align:L;');
    $tb_info->easycell('I check ng maigi na tama ang ITEM CODE Tignang maigi ang EXPIRATION (dapat 6 months beyond) Kumpletuhin lahat ng blangko ', 'align:L;font-size:8');
    $tb_info->printRow();
    $tb_info->endTable(2);

    $max_page++;
  }
} else {
  $get_all_outbound = $db->query('SELECT  a.document_no, a.ref_no, a.picker,b.destination_name FROM tb_outbound a INNER JOIN tb_destination b ON b.destination_code = a.destination_code')->fetch_all();


  $pdf = new exFPDF('L', 'mm',  array(105, 148));
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->SetFont('arial', '', 10);

  $max_page = 0;
  while ($max_page  != 40) {




    $tb_header = new easyTable($pdf, 2);
    $tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w40; align:L; ');
    $tb_header->easyCell('PICKING TAG', 'font-size:18; font-style:B;  valign:M');

    $tb_header->printRow();
    $tb_header->endTable(2);



    $delivery_details = new easyTable($pdf, '{150,500,200,190}');

    $delivery_details->rowStyle('font-size:9; valign:M;');
    $delivery_details->easyCell('<b>Ref #:</b>', 'font-size:9');
    $delivery_details->easyCell($_GET['document_no'] . "-0000" . $max_page, 'border-color:#afb5bf; font-size:10; align:L;font-style:B;');
    $delivery_details->easyCell('<b>Date:</b>', 'font-size:9; align:R');
    $delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:C');
    $delivery_details->printRow();

    $delivery_details->rowStyle('font-size:9; valign:M;');
    $delivery_details->easyCell('', 'font-size:10');
    $delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:L');
    $delivery_details->easyCell('<b>Shift:</b>', 'font-size:9; align:R');
    $delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:C');
    $delivery_details->printRow();

    $delivery_details->endTable(2);

    $tb_info = new easyTable($pdf, '{30,100}');
    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Document No:</b>', 'align:L;');
    $tb_info->easycell($_GET['document_no'], 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Branch:</b>', 'align:L;');
    $tb_info->easycell($_GET['destination'], 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Picker:</b>', 'align:L;');
    $tb_info->easycell('', 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>No. of Box:</b>', 'align:L;');
    $tb_info->easycell('', 'align:C;');
    $tb_info->printRow();
    $tb_info->rowStyle('font-size:8; valign:L; border:LRTB');
    $tb_info->easycell('<b>Pallet No:</b>', 'align:L;');
    $tb_info->easycell('__________ of _________', 'align:C;');
    $tb_info->printRow();

    $tb_info->rowStyle('font-size:7; valign:L;');
    $tb_info->easycell('<b>REMINDERS:</b>', 'align:L;');
    $tb_info->easycell('I check ng maigi na tama ang ITEM CODE Tignang maigi ang EXPIRATION (dapat 6 months beyond) Kumpletuhin lahat ng blangko ', 'align:L;font-size:8');
    $tb_info->printRow();
    $tb_info->endTable(2);

    $max_page++;
  }
}


/**
 * Output With file name
 */


$pdf->Output('', "DR-" . $_GET['ref_no'] . "-" .  $_GET['document_no'] . ".pdf"); //To Print and to indicate the filename




?>