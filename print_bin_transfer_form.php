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

$date = date('Y-m-d');


$db_count_sheet = $db->query('SELECT 
    *
FROM tb_generated_forms
WHERE ref_no = ? AND nature = ?', $_GET['ref_no'], $_GET['nature'])->fetch_array();

// print_r_html($aisle_details);

/**
 * Header
 */

$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 3);
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w40; align:L; valign:M;');
$tb_header->easyCell('BIN TRANSFER FORM', 'font-size:15; font-style:B; align:C; valign:M; font-color:#00AF50;');
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w40; align:R; valign:M;');
$tb_header->printRow();
$tb_header->endTable(5);

$tb_info = new easyTable($pdf, '{25,67.5,5,25,67.5}');
$tb_info->easyCell('Ref No.:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('BTB -' . $db_count_sheet['ref_no'], 'font-size:8; font-style:B; align:C; valign:M; border:B');
$tb_info->easyCell('');
$tb_info->printRow();

$tb_info->easyCell('Aisle/Layer:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('', 'font-size:8; font-style:B;align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->printRow();

$tb_info->easyCell('Transfer Date:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('', 'font-size:8; font-style:B;align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->printRow();


$tb_info->easyCell('Generated  By:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($_SESSION['name'], 'font-size:8; font-style:B;align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->printRow();

$tb_info->easyCell('Date Generated:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($db_count_sheet['date_generated'], 'font-size:8; font-style:B;align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->printRow();

$tb_info->endTable(5);


$tb_header = new easyTable($pdf, '{40,40,40,40,40,40}');

$tb_header->rowStyle('border:1; paddingY:1; font-size:8; font-style:B');
$tb_header->easyCell('Source Location', 'align:C; valign:M');
$tb_header->easyCell('Destination Location', 'align:C; valign:M');
$tb_header->easyCell('Item Code', 'align:C; valign:M');
$tb_header->easyCell('Lot No/Batch', 'align:C; valign:M');
$tb_header->easyCell('Qty(PCS)', 'align:C; valign:M');
$tb_header->easyCell('LPN', 'align:C; valign:M');
$tb_header->printRow();



foreach ($db_count_sheet as $aisle_key => $aisle_val) {


  $tb_header->rowStyle('border:1; paddingY:4; font-size:8');
  $tb_header->easyCell('');
  $tb_header->easyCell('');
  $tb_header->easyCell('');
  $tb_header->easyCell('');
  $tb_header->easyCell('');
  $tb_header->easyCell('');

  $tb_header->printRow();
}

$tb_header->easyCell('****Nothing Follows****', 'align:C;font-size:8; colspan:12');
$tb_header->printRow();

$tb_header->endTable(2);

/**
 * Output With file name
 */

$pdf->Output('', "AGLI_BIN_TRANSFER_FORM.pdf"); //To Print and to indicate the filename




?>