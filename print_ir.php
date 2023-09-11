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
$ir_details = $db->query('SELECT 
    ref_no,
    source_ref_no,
    transaction_type,
    source_document,
    nature_of_ir,
    ir_date,
    item_code,
    batch_code,
    qty,
    source,
    status,
    destination,
    description FROM tb_incident_report where source_document=?', $_GET['document_no'])->fetch_all();
if (empty($ir_details)) {
  $_SESSION['msg_heading'] = "Transaction Error!";
  $_SESSION['msg'] = "There's no item/sku to pick! Please Check your Stock First";
  $_SESSION['msg_type'] = "error";
  redirect("view_asn", false);
} else {
  foreach ($ir_details as $ir_det => $ir_val) {
    $ref_no = $ir_val['ref_no'];
    $source_ref = $ir_val['source_ref_no'];
    $t_type = $ir_val['transaction_type'];
    $document = $ir_val['source_document'];
    $nature = $ir_val['nature_of_ir'];
    $ir_date = $ir_val['ir_date'];
    $item = $ir_val['item_code'];
    $batch = $ir_val['batch_code'];
    $qty = $ir_val['qty'];
    $source = $ir_val['source'];
    $dest = $ir_val['destination'];
    $desc = $ir_val['description'];
    $stats = $ir_val['status'];
  }
}
?>

<?php

$date_today = date('M-d-Y g:i A');

$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$tb_header = new easyTable($pdf, 3);
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w40; align:L; valign:M');
$tb_header->easyCell('INCIDENT REPORT FORM', 'font-size:15; font-style:B; align:C; valign:M');
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w40; align:R; valign:M');
$tb_header->printRow();
$tb_header->endTable(5);

/**
 * INFO SECTION
 */

$tb_info = new easyTable($pdf, '{25,67.5,5,25,67.5}');
$tb_info->easyCell('System Ref. No:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('IR-' . $ref_no, 'font-size:8; font-style:B; align:C; valign:M;border:B');

$tb_info->easyCell('');
$tb_info->easyCell('Printed:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($date_today, 'font-size:8; font-style:B; align:C; valign:M; border:B');


$tb_info->printRow();
$tb_info->easyCell('Source Document:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($document, 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Transaction Type:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($t_type, 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->easyCell('Source/Supplier:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($source, 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Destination:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($dest, 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->easyCell('IR Date:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($ir_date, 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Printed By:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($_SESSION['name'], 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();


$tb_info->endTable(5);

$table_sign = new easyTable($pdf, '{30,90,90');

$table_sign->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_sign->easyCell('');
$table_sign->easyCell('Prepared By:', 'font-style:B; align:C;border:1;font-color:black');
$table_sign->easyCell('Confirm By:', 'font-style:B; align:C;border:1;font-color:black');
$table_sign->printRow();


$table_sign->easyCell('Printed Name Over Signature:', 'font-size:7; align:L; valign:B; paddingX:2;paddingY:5;');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->printRow();


$table_sign->easyCell('Designation:', 'font-size:7; align:L; valign:B;paddingX:2;paddingY:3');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->easyCell('', 'align:C; valign:B;border:1:LR');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->printRow();

$table_sign->easyCell('Date:', 'font-size:7; align:L; valign:B;paddingX:2;paddingY:3');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->easyCell('', 'align:C; valign:B;border:1');
$table_sign->printRow();
$table_sign->endTable(5);

/**
 * DESC
 */

$table_remarks_sign = new easyTable($pdf, '{30,190');

$table_remarks_sign->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('REMARKS', 'font-style:B; align:L;border:1;font-color:black');
$table_remarks_sign->easyCell('', 'font-style:B; align:L;border:1;font-color:black');
$table_remarks_sign->printRow();


$table_remarks_sign->easyCell('', 'font-size:7; align:L; valign:B; paddingX:2;paddingY:10;');
$table_remarks_sign->easyCell($desc, 'align:L; valign:T;border:1');
$table_remarks_sign->easyCell('', 'align:L; valign:T;border:1');
$table_remarks_sign->easyCell('');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);

/**
 * ITEMS PROPER
 */

$tb_info = new easyTable($pdf, '{60,60,40,45}');

$tb_info->rowStyle('border:1; paddingY:2; font-size:7; font-style:B;border-color:#2d3238');
$tb_info->easyCell('ITEM CODE', 'align:C; valign:M;');
$tb_info->easyCell('BATCH CODE', 'align:C; valign:M;');
$tb_info->easyCell('QTY(PCS)', 'align:C; valign:M;');
$tb_info->easyCell('NATURE OF IR', 'align:C; valign:M;');
$tb_info->printRow();



foreach ($ir_details as $db_key => $db_val) {


  $cell_color = "#d5d7db";
  $tb_info->rowStyle('border:1; paddingY:2; font-size:8;border-color:#2d3238');
  $tb_info->easyCell($db_val['item_code'], 'align:C; valign:M;font-style:B;b');
  $tb_info->easyCell($db_val['batch_code'], 'align:C; valign:M;');
  $tb_info->easyCell($db_val['qty'], 'align:C; valign:M; font-style:B;');
  $tb_info->easyCell($db_val['nature_of_ir'], 'align:C; valign:M; font-style:B;');

  $tb_info->printRow();
}
$tb_info->rowStyle('paddingY:2; font-size:8;');
$tb_info->easyCell('**NOTHING FOLLOWS**', 'colspan:7; align:C');
$tb_info->printRow();
$tb_info->endTable(2);

$pdf->Output('', 'Incident Report-' . $ref_no . '.pdf'); //To Print and to indicate the filename
?>
