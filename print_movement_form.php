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

$db_destination = $db->query('SELECT id, destination_code, destination_name, destination_address FROM tb_destination ORDER BY destination_code')->fetch_all();

$all_destination = array();

foreach ($db_destination as $db_key => $asar_det) {
  $all_destination[$asar_det['destination_code']] = $asar_det;
}

$db_vendor = $db->query('SELECT id, vendor_name, vendor_id, address FROM tb_vendor')->fetch_all();

$all_vendor = array();

foreach ($db_vendor as $db_vendor_key => $db_vendor_det) {
  $all_vendor[$db_vendor_det['vendor_id']] = $db_vendor_det;
}

$db_items = $db->query('SELECT id, item_code,material_description, pack_size FROM tb_items')->fetch_all();

$all_items = array();

foreach ($db_items as $db_item_key => $db_items_det) {

  $all_items[$db_items_det['item_code']] = $db_items_det;
}



$db_inbound = $db->query('SELECT 
    a.ref_no,
    a.transaction_type,
    a.document_no,
    a.item_code,
    a.batch_no,
    a.qty_pcs,
    a.lpn,
    a.bin_location,
    a.date_created,
    a.vendor_code,
    a.destination_code,
    a.expiry
  
    FROM 
     tb_inbound a
    WHERE a.ref_no = ?', $_GET['asn_ref'])->fetch_all();

// print_r_html($db_inbound);


$date_today = date('m-d-Y');

foreach ($db_inbound as $db_key => $db_val) {


  $inbound_date = $db_val['date_created'];
  $system_ref_no = $db_val['ref_no'];
  $transaction = $db_val['transaction_type'];
  $document_no = $db_val['document_no'];
  $print_date = $date_today;
  $printed_by = $_SESSION['name'];
  $consignee = $all_destination[$db_val['destination_code']]['destination_name'];
  // $source = $all_vendor[$db_val['vendor_code']]['vendor_name'];

}

/**
 * Header
 */

$pdf = new exFPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell(' PUT AWAY FORM- ' . $system_ref_no, 'font-size:18; font-style:B; align:L; valign:M');
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');
$tb_header->printRow();
$tb_header->endTable(2);

$table = new easyTable($pdf, '{50,75,25,82.5,82.5}');

$table->rowStyle('font-size:8');
$table->easyCell('<b>SOURCE DOCUMENT NO.:</b>', 'font-size:8; align:L');
$table->easyCell($document_no, 'border-color:#0339fc; align:L;font-size:10');
$table->easyCell("");
$table->easyCell('Prepared By:', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->easyCell('Received By (Trucker/Delivery Staff):', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>PRINT DATE:</b>', 'font-size:8');
$table->easyCell($print_date, ' border-color:#0339fc; align:L; font-size:7; font-style:B');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$table->easyCell($printed_by, ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell('Signature:', 'font-size:6; align:R; valign:B; paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->printRow();



$table->rowStyle('font-size:7');
$table->easyCell('<b>CONSIGNEE:</b>', 'font-size:8');
$table->easyCell($consignee, 'border-color:#0339fc; align:L; font-size:7');
$table->easyCell('Name:', 'font-size:6; align:R; valign:B;paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>GOODS RECEIPT DATE:</b>', 'font-size:8');
$table->easyCell($inbound_date, 'border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('ASN REF. NO:', 'font-style:B');
$table->easyCell('ASN-' . $system_ref_no);
$table->easyCell('Designation:', 'font-size:6; align:R; valign:B;paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('');
$table->easyCell('');
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:9');
$table->easyCell('POST GOODS REF. NO:', 'font-style:B');
$table->easyCell('PGR-' . $system_ref_no, 'font-style:B; font-color:#fc0335; font-size:11');
$table->easyCell('Date:', 'font-size:6; align:R; valign:B;paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('');
$table->easyCell('');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8;');
$table->easyCell('<b>RT UNIT:</b>', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();


$table->rowStyle('font-size:8;');
$table->easyCell('<b>OPERATOR NAME:</b>', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8;');
$table->easyCell('<b>MOVE TIME:</b>', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8;');
$table->easyCell('<b>MOVE DATE:</b>', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->endTable(5);

/**
 * Remarks And Signatory
 */

// $table_remarks = new easyTable($pdf,1);

// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;valign:M');
// $table_remarks->easyCell('REMARKS', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
// $table_remarks->printRow();

// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
// $table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
// $table_remarks->printRow();

// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
// $table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
// $table_remarks->printRow();

// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
// $table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
// $table_remarks->printRow();

// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
// $table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
// $table_remarks->printRow();

// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
// $table_remarks->easyCell("", 'font-style:B; align:C;border:LRB');
// $table_remarks->printRow();

// $table_remarks->endTable(5);

/**
 * ITEM LIST
 */

$table_item_list = new easyTable($pdf, '{25,80,40,20,25,25,60}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:#2d3238; bgcolor:#0339fc; font-color:#ffffff;valign:M; paddingY:2');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');

$table_item_list->easyCell('SOURCE LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('BIN LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('LPN', 'font-style:B; align:C');

$table_item_list->printRow();

$i = 1;
foreach ($db_inbound as $db_in_key => $db_in_val) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#ebeef2;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);

  $table_item_list->easyCell($db_in_val['item_code']);
  $table_item_list->easyCell($all_items[$db_in_val['item_code']]['material_description']);
  $table_item_list->easyCell($db_in_val['batch_no']);
  $table_item_list->easyCell(number_format($db_in_val['qty_pcs'], 2, ".", ","));


  $table_item_list->easyCell($db_in_val['bin_location']);
  $table_item_list->easyCell("");
  $table_item_list->easyCell($db_in_val['lpn']);

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCCC}; paddingY:4; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:9');
$table_item_list->printRow();

/**
 * Output With file name
 */

$pdf->Output('', "Movement Form-" . $document_no . ".pdf"); //To Print and to indicate the filename




?>