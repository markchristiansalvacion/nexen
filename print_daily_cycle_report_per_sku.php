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

$sku_code = $_GET['sku_code'];
$db_sku_loc = get_sku_location($db, $sku_code);
/** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */
$bin_count = count($db_sku_loc);


/** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */
$bin_count = count($db_sku_loc);
/**
 * Start of Printing
 */
$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins(2,2,2,2);
$pdf->SetFont('Arial', '', 10);
$pdf->AddPage();


// Header

$tb_header=new easyTable($pdf, 3);
$tb_header->rowStyle('border:0');
$tb_header->easyCell('', 'img:img/agl_logo.png, w30; align:C; valign:M ;rowspan:3');
$tb_header->easyCell('Arrowgo-Logistics Inc.', 'font-size:10; font-style:BI; align:C; valign:B');
$tb_header->easyCell('', 'img:img/pepsi_logo.png, w15; align:C; valign:M ;rowspan:3');
$tb_header->printRow();

$tb_header->rowStyle('border:0');
$tb_header->easyCell('12th Floor Avecshares Center'."\n".'1132 University Parkway North, Bonifacio Triangle,'."\n".'Bonifacio Global City, Taguig 1634', 'font-size:7; font-style:I; align:C; valign:T');
$tb_header->printRow();

$tb_header->rowStyle('border:0');
$tb_header->easyCell('Cycle Count Sheet', 'font-size:10; font-style:B; align:C; valign:M');
$tb_header->printRow();
$tb_header->endTable(7);  

$table = new easyTable($pdf, 4);

$table->rowStyle('font-size:8');
$table->easyCell('<b>SKU:</b>','align:L');
$table->easyCell($sku_code, 'border-color:#0339fc; align:L');
$table->easyCell('<b>Print Date:</b>');
$table->easyCell($today, ' border-color:#0339fc; align:L;font-style:B');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>Printed By:</b>');
$table->easyCell($_SESSION['name'], ' border-color:#0339fc; align:L;font-style:B');
$table->easyCell('<b>Total Bin:</b>');
$table->easyCell($bin_count . " Bin(s)", ' border-color:#0339fc; align:L');
$table->printRow();
$table->endTable(5);

$tb_signatory = new easyTable($pdf, '{20,47.5,47.5,47.5,47.5}');

$tb_signatory->rowStyle('font-size:8;valign:M;font-style:B;border:T');
$tb_signatory->easyCell('');
$tb_signatory->easyCell('Inventory Analyst:');
$tb_signatory->easyCell('Zone Keeper:');
$tb_signatory->easyCell('Supervisor:');
$tb_signatory->easyCell('Operator:');
$tb_signatory->printRow();


$tb_signatory->rowStyle('font-size:8;valign:M;paddingY:3');
$tb_signatory->easyCell('Signature:');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->printRow();


$tb_signatory->rowStyle('font-size:8;valign:M;paddingY:3');
$tb_signatory->easyCell('Name:');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->printRow();


$tb_signatory->rowStyle('font-size:8;valign:M;paddingY:3;');
$tb_signatory->easyCell('Date:');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->easyCell('________________________');
$tb_signatory->printRow();
$tb_signatory->endTable();
/**
 * Remarks And Signatory
 */

$table_remarks = new easyTable($pdf, 1);

$table_remarks->rowStyle('font-size:8;border-color:#2d3238;valign:M');
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

$table_item_list = new easyTable($pdf, '{25,70,55,40,20}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:#2d3238; bgcolor:#0339fc; font-color:#ffffff;valign:M;');
$table_item_list->easyCell('Bin', 'font-style:B; align:C');
$table_item_list->easyCell('Material Desc.', 'font-style:B; align:C');
$table_item_list->easyCell('LPN', 'font-style:B; align:C');
$table_item_list->easyCell('BBD', 'font-style:B; align:C');
$table_item_list->easyCell('Qty (Case)', 'font-style:B; align:C');
$table_item_list->printRow();

$i = 1;
foreach ($db_sku_loc as $db_in_key => $db_in_val) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#ebeef2;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:2; valign:M; split-row:true;' . $bgcolor);

  
    $table_item_list->easyCell($db_in_val['actual_bin_loc']);
    $table_item_list->easyCell($db_in_val['sku_code'].'-'.$db_in_val['material_description']);
    $table_item_list->easyCell($db_in_val['lpn']);
    $table_item_list->easyCell($db_in_val['expiry']);
    $table_item_list->easyCell(number_format($db_in_val['available_qty'], 2, ".", ","));
 
 
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


$pdf->Output('', "DAILY CYCLE COUNT" . $today.' '.$_GET['sku_code'] . ".pdf"); //To Print and to indicate the filename



?>