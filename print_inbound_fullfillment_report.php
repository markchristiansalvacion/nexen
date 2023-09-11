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
$inbound_report = $db->query('SELECT a.document_no,a.item_code,a.batch_no,a.qty_pcs,a.lpn,a.date_time,a.fullfilled_by,c.vendor_name,b.vendor_code,d.material_description
                    FROM tb_fullfillment a
                     INNER JOIN tb_inbound b On b.document_no = a.document_no
                     INNER JOIN tb_vendor c On c.vendor_id = b.vendor_code
                     INNER JOIN tb_items d ON d.item_code = a.item_code
                     WHERE a.document_no =?', $_GET['document_no'])->fetch_all();
foreach ($inbound_report as $arr_key => $arr_val) {
  $doc = $arr_val['document_no'];
  $source = $arr_val['vendor_name'];
  $date = $arr_val['date_time'];
  $fullfilled = $arr_val['fullfilled_by'];
}
/**
 * Header
 */

$pdf = new exFPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('INBOUND FULLFILLMENT REPORT', 'font-size:18; font-style:B; align:L; valign:M');
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');
$tb_header->printRow();
$tb_header->endTable(2);

$table = new easyTable($pdf, '{50,75,25,82.5,82.5}');

$table->rowStyle('font-size:9');
$table->easyCell('<b>DOCUMENT NO:</b>', 'font-size:9; align:L');
$table->easyCell($doc, 'border-color:#0339fc; align:L;font-size:9');
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:9');
$table->easyCell('<b>SOURCE:</b>', 'font-size:9');
$table->easyCell($source, ' border-color:#0339fc; align:L; font-size:9;');
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:9');
$table->easyCell('<b>DATE FULLFILLED:</b>', 'font-size:9');
$table->easyCell($date, ' border-color:#0339fc; align:L; font-size:9;');
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:9');
$table->easyCell('<b>FULLFILLED BY</b>', 'font-size:9');
$table->easyCell($fullfilled, ' border-color:#0339fc; align:L; font-size:9;');
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:9');
$table->easyCell('<b>PRINTED BY</b>', 'font-size:9');
$table->easyCell($_SESSION['name'], ' border-color:#0339fc; align:L; font-size:9;');
$table->easyCell("");
$table->printRow();

$table->rowStyle('font-size:9');
$table->easyCell('<b>DATE PRINTED</b>', 'font-size:9');
$table->easyCell($today, ' border-color:#0339fc; align:L; font-size:9;');
$table->easyCell("");
$table->rowStyle('font-size:9');
$table->easyCell('Prepared By:', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
$table->easyCell('Confirmed By:', 'font-style:B; align:C; bgcolor:#0339fc;border:1;font-color:#ffffff');
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

$table_item_list = new easyTable($pdf, '{35,35,160,50}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:#2d3238; bgcolor:#0339fc; font-color:#ffffff;valign:M; paddingY:2');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('QTY(PCS)', 'font-style:B; align:C');

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
  $table_item_list->easyCell($db_in_val['qty_pcs']);




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

$pdf->Output('', "INBOUND FULLFILLMENT REPORT-" . $_GET['document_no'] .  ".pdf"); //To Print and to indicate the filename




?>