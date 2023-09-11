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

$today = date('Y-m-d');
$outbound_report = $db->query('SELECT a.document_no,a.item_code,a.item_description,a.bin_loc,a.batch_no,a.qty_pcs,a.expiry,b.ship_date,c.branch_name,b.ref_no
                    FROM tb_picklist a
                    INNER JOIN tb_outbound b on b.document_no = a.document_no 
                    INNER JOIN tb_branches c ON c.branch_code = b.destination_code
                    WHERE b.ship_date BETWEEN ? and ?
                        ORDER BY ref_no DESC', $_GET['start_date'], $_GET['end_date'])->fetch_all();

foreach ($outbound_report as $arr_val => $r_val) {
  $ref = $r_val['ref_no'];
}
/** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */

/**
 * Header
 */

$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w150; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();


$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w30; align:R;');
$tb_header->printRow();

$tb_header->easyCell('OUTBOUND SUMMARY REPORT', 'font-size:15; font-style:B; align:R; valign:M');
$tb_header->printRow();


//  $tb_header->easyCell('DELIVERY RECEIPT', 'font-size:15; font-style:B; align:C; valign:M;border:1');
//  $tb_header->printRow();

$tb_header->endTable(2);

$table = new easyTable($pdf, '{20,100,70}');
$table->easyCell('', 'font-size:9; align:L; valign:M');
$table->easyCell('', 'border-color:#afb5bf; font-style:B; valign:M; align:L');
$table->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:8; ');
$table->printRow();


$table->endTable(5);


$delivery_details = new easyTable($pdf, '{30,80,30,50}');

$delivery_details->rowStyle('font-size:9');
$delivery_details->easyCell('REF #:', 'align:L; valign:M;font-style:B;');
$delivery_details->easyCell("DR-" . $ref, 'border-color:#afb5bf; align:L;font-style:B; valign:M');
$delivery_details->easyCell("");
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:9; valign:M;');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:#afb5bf; font-size:7; align:L');
$delivery_details->easyCell('<b>START DATE:</b>', 'font-size:8; align:R');
$delivery_details->easyCell($_GET['start_date'], 'border-color:#afb5bf; font-size:7; align:C');
$delivery_details->printRow();

$delivery_details->rowStyle('font-size:9; valign:M');
$delivery_details->easyCell('<b>PRINTED DATE:</b>', 'font-size:8');
$delivery_details->easyCell($today, 'border-color:#afb5bf; align:L; font-size:7');
$delivery_details->easyCell('<b>END DATE:</b>', 'font-size:8; align:R');
$delivery_details->easyCell($_GET['end_date'], 'border-color:#afb5bf; font-size:7; align:C');
$delivery_details->printRow();

$delivery_details->rowStyle('font-size:9; valign:M');
$delivery_details->easyCell('', 'font-size:8');
$delivery_details->easyCell('', 'border-color:#afb5bf; align:L; font-size:7');
$delivery_details->easyCell('', 'font-size:8;align:R');
$delivery_details->easyCell('', 'border-color:#afb5bf; align:C; font-size:7');
$delivery_details->printRow();



$delivery_details->endTable(2);


/**
 * REMARKS
 */

// $table_remarks = new easyTable($pdf, 1);
// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;valign:M');
// $table_remarks->easyCell('REMARKS', 'font-style:B; align:C; bgcolor:#33E838;border:1;font-color:#ffffff');
// $table_remarks->printRow();

// $table_remarks->easyCell('', 'border:LRB; paddingY:20');
// $table_remarks->printRow();

// $table_remarks->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{25,70,70');

$table_remarks_sign->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Prepared By:', 'font-style:B; bgcolor:#0066FF;align:C;;border:1;font-color:#fff');
$table_remarks_sign->easyCell('Confirmed By / Validated By:', 'font-style:B;bgcolor:#0066FF; align:C;border:1;font-color:#fff');
$table_remarks_sign->printRow();


$table_remarks_sign->easyCell('Signature:', 'font-size:6; align:L; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:6; align:L; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Signature:', 'font-size:6; align:L; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:6; align:L; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');

$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);


/**
 * ITEM LIST
 */

$table_item_list = new easyTable($pdf, '{40,30,30,30,60,30,30,30}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:#2d3238; bgcolor:#0066FF;; font-color:#fff;valign:M');
$table_item_list->easyCell('DOCUMENT NO', 'font-style:B; align:C');
$table_item_list->easyCell('BRANCH', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM NAME', 'font-style:B; align:C');
$table_item_list->easyCell('QTY PCS', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY', 'font-style:B; align:C');

$table_item_list->easyCell('LOCATION', 'font-style:B; align:C');


$table_item_list->printRow();


$i = 1;
foreach ($outbound_report as $arr_key => $arr_item_det) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#fff;';
  }

  $table_item_list->rowStyle('border:LRBT;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);

  $table_item_list->easyCell($arr_item_det['document_no']);
  $table_item_list->easyCell($arr_item_det['branch_name']);
  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['batch_no']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell($arr_item_det['qty_pcs']);
  $table_item_list->easyCell($arr_item_det['expiry']);

  $table_item_list->easyCell($arr_item_det['bin_loc']);

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();
$table_item_list->endTable(2);



/**
 * Output With file name
 */


$pdf->Output('', "OUTBOUND SUMMARY REPORT-" . $_GET['start_date'] . "-" . $_GET['end_date'] . ".pdf"); //To Print and to indicate the filename



?>