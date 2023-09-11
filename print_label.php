<?php
ob_start();
require_once 'includes/load.php';
?>
<?php
/** To Generate Printable PDF */
include_once 'fpdf/fpdf.php'; // fpdf
include_once 'fpdf/easytable/exfpdf.php'; // exfpdf
include_once 'fpdf/easytable/easyTable.php'; // easytable

require 'vendor2/autoload.php'; // Barcode Generator
?>

<?php

$db_pallet = $db->query('SELECT 
  a.id,
  a.ia_ref,
  a.lpn,
  a.sku_code,
  a.qty_case,
  a.expiry,
  a.bin_loc,
  a.putaway_status,
  tb_items.material_description,
  tb_items.weight_per_case,
  tb_asn.last_updated,
  tb_assembly_build.document_no
  FROM tb_inventory_adjustment a
  INNER JOIN tb_assembly_build ON tb_assembly_build.id = a.ab_id 
  INNER JOIN tb_items on tb_items.sap_code = a.sku_code
  INNER JOIN tb_asn ON tb_asn.id = tb_assembly_build.asn_id
  WHERE tb_assembly_build.document_no = ?', $_GET['document_no'])->fetch_all();



/**
 * Start of Printing
 */
$pdf = new exFPDF('P', 'mm', array(105, 148.5));
$pdf->AliasNbPages();
$pdf->SetMargins(1, 1, 1);
$pdf->SetFont('Arial', '', 10);

foreach($db_pallet as $db_key => $arr_val){

  $pdf->AddPage();

  /**GENERATE BARCODE */
  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
  file_put_contents('lpn_barcode_images/pallet_tag' . $arr_val['lpn'] . '.png', $generator->getBarcode($arr_val['lpn'], $generator::TYPE_CODE_128, 10, 400));

  $tb_label = new easyTable($pdf, '{100,48.5}');


  $tb_label->rowStyle('font-size:7; valign:M;');
  $tb_label->easycell('', 'img:lpn_barcode_images/pallet_tag' . $arr_val['lpn'] . '.png,w60,h10,align:C;colspan:2; paddingY:3;');
  $tb_label->printRow();

  $tb_label->rowStyle('font-size:16; font-style:B; valign:T;font-color:black;');
  $tb_label->easycell($arr_val['lpn'], 'align:C;colspan:2;font-color:black;');
  $tb_label->printRow();
  $tb_label->endTable(0);

  $tb_info_det = new easyTable($pdf, '{100,20}');
  $tb_info_det->rowStyle('font-size:10; valign:B; border:LRT; font-color:black;');
  $tb_info_det->easycell('MATERIAL DESCRIPTION', 'align:L; colspan:2;font-color:black;');
  $tb_info_det->printRow();
  $tb_info_det->rowStyle('font-size:15; font-style:B; valign:T; border:LRB;font-color:black; ');
  $tb_info_det->easycell($arr_val['material_description'], 'align:C; colspan:2; paddingY:3;font-color:black;');
  $tb_info_det->printRow();
  $tb_info_det->endTable(0);

  $tb_info_loc = new easyTable($pdf, '{100,20}');
  $tb_info_loc->rowStyle('font-size:10; valign:B; border:LR;font-color:black; ');
  $tb_info_loc->easycell('BIN LOCATION', 'align:L;colspan:2;font-color:black;');
  $tb_info_loc->printRow();

  $tb_info_loc->rowStyle(' valign:T; border:LRB; ');
  $tb_info_loc->rowStyle('font-size:35; font-style:B; valign:T; border:LRB; font-color:black;');
  $tb_info_loc->easycell($arr_val['bin_loc'], 'align:C; font-style:B;paddingY:3;colspan:2;font-color:black;');
  $tb_info_loc->printRow();
  $tb_info_loc->endTable(0);

  $tb_info_date = new easyTable($pdf, '{100,20}');
  $tb_info_date->rowStyle('font-size:10; valign:B; border:LR;font-color:black; ');

  $tb_info_date->easycell('BBD', 'align:L;colspan:2;font-color:black;');

  $tb_info_date->printRow();
  $tb_info_date->rowStyle('font-size:30; valign:M; border:LRB;font-color:black; ');

  $tb_info_date->easycell(date('M-d-Y', strtotime($arr_val['expiry'])), 'align:C;font-style:B;paddingY:3;colspan:2;font-color:black;');
  $tb_info_date->printRow();
  $tb_info_date->endTable(0);

  $tb_last_info = new easyTable($pdf, '{74.25,74.25}');
  $tb_last_info->rowStyle('font-size:10; valign:L; border:LRTB;font-color:black;');
  $tb_last_info->easycell('TOTAL CASES', 'align:C;font-color:black;');
  $tb_last_info->easycell('DOCUMENT NO', 'align:C; colspan:2;font-color:black;');
  $tb_last_info->printRow();
  $tb_last_info->rowStyle('font-size:12; font-style:B; valign:L; border:LRTB;font-color:black;');
  $tb_last_info->easycell(number_format($arr_val['qty_case'], 2), 'align:C;paddingY:3;font-color:black;');
  $tb_last_info->easycell($arr_val['document_no'], 'align:C;paddingY:3;font-color:black;');
  $tb_last_info->printRow();
  $tb_last_info->endTable(0);

  $tb_remarks = new easyTable($pdf,1);
  $tb_remarks->rowStyle('font-size:10; valign:L; border:LRTB;font-color:black;');
  $tb_remarks->easycell('Remarks', 'align:L;font-color:black;');
  $tb_remarks->easycell('', 'align:L;font-color:black;border:LR');
  $tb_remarks->printRow();

  $tb_remarks->easycell('', 'align:L;font-color:black;border:LRB;paddingY:5');
  $tb_remarks->printRow();
  $tb_remarks->endTable(0);

}
/**
 * Output With file name
 */
$pdf->Output(); //To Print and to indicate the filename



?>