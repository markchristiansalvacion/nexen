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

$db_bin_label = $db->query('SELECT * FROM tb_bin_location_bac WHERE aisle=? GROUP BY id ORDER BY location_code DESC', $_GET['aisle'])->fetch_all();

// print_r_html($db_bin_label);
// $bin_label = array();

// foreach ($db_bin_label as $db_key => $arr_val) {

//   $bin_label[$arr_val['location_code']][$db_key] = $arr_val;
//   // $bin_label[$arr_val['pallet_tag']][$db_key]['total_weight'] = (float)$arr_val['weight_per_box'] * $arr_val['qty_box'];
//   // $bin_label[$arr_val['pallet_tag']][$db_key]['total_cbm'] = (float)$arr_val['cbm_per_box'] * $arr_val['qty_box'];
// }




// $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();


foreach ($db_bin_label as $asar_key => $asar_val) {

  // print_r_html($asar_val);
  // file_put_contents('bin_location_barcodes/bin_label' . $asar_val['location_code'] . '.png', $generator->getBarcode($asar_val['location_code'], $generator::TYPE_CODE_128, 10, 400));

  // echobr($generator->getBarcode($asar_val['location_code'], $generator::TYPE_CODE_128, 10, 400));

  file_put_contents('bin_location_barcodes/bin_label-' . $asar_val['location_code'] . '.png', $generator->getBarcode($asar_val['location_code'], $generator::TYPE_CODE_128, 10, 400));
}

/**
 * Start of Printing
 */
$pdf = new exFPDF('P', 'mm', array(105, 148));
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetFont('Arial', '', 10);
$pdf->AddPage();
$loc_count = 0;


foreach ($db_bin_label as $arr_key => $arr_val) {


  $tb_info = new easyTable($pdf, '{15,20,50}');
  $tb_info->rowStyle('font-size:9; valign:M; border:1;font-color:black; font-style:B');
  $tb_info->easycell('Level', 'align:C;');
  $tb_info->easycell('Location', 'align:C;');
  $tb_info->easycell('Code', 'align:C;');
  $tb_info->printRow();
  // $tb_info->rowStyle('font-size:14; valign:L; border:LRT;font-color:black;');

  $tb_info->rowStyle('font-size:10; valign:M; border:LRB;font-color:black;font-style:B');
  $tb_info->easycell($arr_val['high'], 'align:C; font-size:15');
  $tb_info->easycell($arr_val['location_code'], 'align:C;font-style:B;valign:M; border:LRB ');
  $tb_info->easycell('', 'img:bin_location_barcodes/bin_label-' . $arr_val['location_code'] . '.png,w40,h15,align:C; valign:M');
  $tb_info->printRow();
  $tb_info->endTable(1);
}



/**
 * Output With file name
 */
$pdf->Output('', "Location tag-" . $_GET['aisle'] . ".pdf");

?>