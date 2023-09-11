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

/**
 * DAILY INVENTORY REPORT
 */

$get_all_items = $db->query('SELECT * FROM tb_items')->fetch_all();

// /print_r_html($get_all_items);

$dir_arr = array();


foreach ($get_all_items as $arr_key => $arr_val) {

  $get_records = $db->query('SELECT 
  tb_inbound.item_code, 
  SUM(tb_inbound.in_qty) as received,
  SUM(tb_inbound.allocated_qty) as allocated, SUM(tb_inbound.dispatch_qty) as dispatched, 
  tb_items.material_description 
  FROM tb_inbound 
  INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
  WHERE tb_inbound.item_code = ? ', $arr_val['item_code'])->fetch_array();

  //print_r_html($get_records);

  if (!is_array_has_empty_input($get_records)) {

    $dir_arr[$arr_val['item_code']] = $get_records;
  } else {

    $dir_arr[$arr_val['item_code']]['item_code'] = $arr_val['item_code'];
    $dir_arr[$arr_val['item_code']]['received'] = 0;
    $dir_arr[$arr_val['item_code']]['allocated'] = 0;
    $dir_arr[$arr_val['item_code']]['dispatched'] = 0;
    $dir_arr[$arr_val['item_code']]['material_description'] = $arr_val['material_description'];
  }
}
//print_r_html($bet_outbound);


/** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */

$today = date('Y-m-d');
$pdf = new exFPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetFont('arial', '', 10);

/**
 * INBOUND COPY
 */

$pdf->AddPage();

$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell(' STOCK REPORT ', 'font-size:20; font-style:B; align:L; valign:M');
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');
$tb_header->printRow();
$tb_header->endTable(2);

$table = new easyTable($pdf, '{50,75,25,82.5,82.5}');

$table->rowStyle('font-size:8');
$table->easyCell('', 'font-size:8; align:L');
$table->easyCell('', 'border-color:#0339fc; align:L;font-size:10');
$table->easyCell("");
$table->easyCell('Prepared By:', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->easyCell('Received By (Trucker/Delivery Staff):', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>PRINT DATE:</b>', 'font-size:12');
$table->easyCell($today, ' border-color:#0339fc; align:L; font-size:12; font-style:B');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>PRINTED BY:</b>', 'font-size:12');
$table->easyCell($_SESSION['name'], ' border-color:#0339fc; align:L; font-size:12');
$table->easyCell('Signature:', 'font-size:6; align:R; valign:B; paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->printRow();

$table->rowStyle('font-size:8;');
$table->easyCell('', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:7');
$table->easyCell('', 'font-size:8');
$table->easyCell('', 'border-color:#0339fc; align:L; font-size:7');
$table->easyCell('Name:', 'font-size:6; align:R; valign:B;paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('', 'font-size:8');
$table->easyCell('', 'border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('', 'font-style:B');
$table->easyCell('');
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
$table->easyCell('', 'font-style:B');
$table->easyCell('', 'font-style:B; font-color:#fc0335; font-size:11');
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

$table->endTable(5);

/**
 * Remarks And Signatory
 */

$table_remarks = new easyTable($pdf, 1);

$table_remarks->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_remarks->easyCell('REMARKS', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table_remarks->printRow();

$table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
$table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
$table_remarks->printRow();

$table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
$table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
$table_remarks->printRow();

$table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
$table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
$table_remarks->printRow();

$table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
$table_remarks->easyCell("", 'font-style:B; align:C;border:LR');
$table_remarks->printRow();

$table_remarks->rowStyle('font-size:9;border-color:#2d3238;paddingY:3');
$table_remarks->easyCell("", 'font-style:B; align:C;border:LRB');
$table_remarks->printRow();

$table_remarks->endTable(5);

/**
 * ITEM LIST
 */

$table_item_list = new easyTable($pdf, '{50,75,40,40,40,40}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:#2d3238; bgcolor:#0339fc; font-color:#ffffff;valign:M; paddingY:2');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('OPENING QTY', 'font-style:B; align:C');
$table_item_list->easyCell('QTY(PCS)IN', 'font-style:B; align:C');
$table_item_list->easyCell('QTY(PCS)OUT', 'font-style:B; align:C');
$table_item_list->easyCell('ENDING QTY', 'font-style:B; align:C');

$table_item_list->printRow();

$i = 1;
foreach ($dir_arr as $db_in_key => $db_in_val) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#ebeef2;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:7; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);

  $table_item_list->easyCell($db_in_val['item_code']);
  $table_item_list->easyCell($db_in_val['material_description']);
  $table_item_list->easyCell(number_format($db_in_val['received']));
  $table_item_list->easyCell(number_format($db_in_val['allocated']));
  $table_item_list->easyCell(number_format($db_in_val['dispatched']));
  $table_item_list->easyCell(number_format($db_in_val['received'] - ($db_in_val['allocated'] + $db_in_val['dispatched'])));

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();








/**
 * Output With file name
 */


$pdf->Output('', "STOCK REPORT-"  . $today . ".pdf"); //To Print and to indicate the filename



?>