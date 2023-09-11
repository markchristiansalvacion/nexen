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
$inbound_report = $db->query('SELECT
                                        tb_inbound.item_code,tb_inbound.batch_no,tb_inbound.qty_pcs,tb_inbound.qty_pcs,
                                        tb_inbound.expiry,tb_inbound.date_created,tb_inbound.bin_location,
                                        tb_inbound.lpn,
                                        tb_inbound.created_by,
                                        tb_items.material_description
                                         FROM tb_inbound
                                            INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                                            WHERE tb_inbound.date_created BETWEEN ? AND ?
                                            ORDER BY item_code DESC', $_GET['start_date'], $_GET['end_date'])->fetch_all();
/**
 * Header
 */

$pdf = new exFPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('INBOUND SUMMARY REPORT', 'font-size:18; font-style:B; align:L; valign:M');
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');
$tb_header->printRow();
$tb_header->endTable(2);

$table = new easyTable($pdf, '{50,75,25,82.5,82.5}');

$table->rowStyle('font-size:8');
$table->easyCell('<b>PRINTED BY:</b>', 'font-size:8; align:L');
$table->easyCell($_SESSION['name'], 'border-color:#0339fc; align:L;font-size:10');
$table->easyCell("");
$table->easyCell('Prepared By:', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->easyCell('Received By (Trucker/Delivery Staff):', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('<b>PRINT DATE:</b>', 'font-size:8');
$table->easyCell($today, ' border-color:#0339fc; align:L; font-size:7; font-style:B');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8');
$table->easyCell('', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell('Signature:', 'font-size:6; align:R; valign:B; paddingX:2');
$table->easyCell('_______________________________', 'align:C; valign:B;');
$table->easyCell('_______________________________', 'align:C; valign:B;');
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

$table->rowStyle('font-size:8;');
$table->easyCell('', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();


$table->rowStyle('font-size:8;');
$table->easyCell('', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8;');
$table->easyCell('', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:8;');
$table->easyCell('', 'font-size:8');
$table->easyCell('', ' border-color:#0339fc; align:L; font-size:7');
$table->easyCell("");
$table->easyCell("");
$table->easyCell("");
$table->printRow();

$table->endTable(5);

/**
 * Remarks And Signatory
 */


/**
 * ITEM LIST
 */

$table_item_list = new easyTable($pdf, '{25,25,80,20,35,65,45}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:#2d3238; bgcolor:#0339fc; font-color:#ffffff;valign:M; paddingY:2');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY', 'font-style:B; align:C');

$table_item_list->easyCell('BIN LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('LPN', 'font-style:B; align:C');
$table_item_list->easyCell('DATE RECEIVED', 'font-style:B; align:C');
$table_item_list->printRow();

$i = 1;
foreach ($inbound_report as $db_in_key => $db_in_val) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#ebeef2;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);

  $table_item_list->easyCell($db_in_val['item_code']);
  $table_item_list->easyCell($db_in_val['batch_no']);
  $table_item_list->easyCell($db_in_val['material_description']);
  $table_item_list->easyCell($db_in_val['expiry']);

  $table_item_list->easyCell($db_in_val['bin_location']);
  $table_item_list->easyCell($db_in_val['lpn']);
  $table_item_list->easyCell($db_in_val['date_created']);


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

$pdf->Output('', "INBOUND SUMMARY REPORT-" . $_GET['start_date'] . "-" . $_GET['end_date'] . ".pdf"); //To Print and to indicate the filename




?>