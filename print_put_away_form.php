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

//print_r_html($_GET);

$db_for_putaway = $db->query('SELECT 
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
  WHERE tb_assembly_build.document_no = ? AND a.transaction_type = ?', $_GET['document_no'],"INB")->fetch_all();

 // print_r_html($db_for_putaway);

  $ref_no = $_GET['document_no'];
  $so_no = "";
  $so_date = "";
  $rdd = "";
  $delivering_plant = "";
  $warehouse_name = "";
  $warehouse_address = "";
  $ship_to_code = "";
  $ship_to_name = "";
  $ship_to_address = "";


  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();


  file_put_contents('putaway_form_barcode/document_no-' . $_GET['document_no'] . '.png', $generator->getBarcode($_GET['document_no'], $generator::TYPE_CODE_128, 10, 100));

  /**
   * Start of Printing
   */
  $pdf = new exFPDF('P', 'mm', 'A4');
  $pdf->AliasNbPages();
  $pdf->SetMargins(3,3,3);
  $pdf->SetFont('Arial', '', 10);
  $pdf->AddPage();

  // Header

  $tb_header=new easyTable($pdf, 3);
  $tb_header->rowStyle('border:0');
  $tb_header->easyCell('', 'img:img/agl_logo.png, w35; align:C; valign:M ;rowspan:3');
  $tb_header->easyCell('Arrowgo-Logistics Inc.', 'font-size:10; font-style:BI; align:C; valign:B');
  $tb_header->easyCell('', ' align:C; valign:M ;rowspan:3');
  $tb_header->printRow();

  $tb_header->rowStyle('border:0');
  $tb_header->easyCell('Arrowgo-Logistics Inc. Warehouse Complex'."\n".'11M Villarica Road,Brgy. Patubig, Marilao Bulacan', 'font-size:7; font-style:I; align:C; valign:T');
  $tb_header->printRow();

  $tb_header->rowStyle('border:0');
  $tb_header->easyCell('Putaway Form', 'font-size:15; font-style:B; align:C; valign:M');
  $tb_header->printRow();
  $tb_header->endTable(2);  

  $tb_details = new easyTable($pdf, '{60,2,37,37,37,37}');
  $tb_details->rowStyle('border:T;font-size:8; font-style:B');
  $tb_details->easyCell('', 'img:putaway_form_barcode/document_no-'.$_GET['document_no'].'.png, w40, h10; align:C; valign:T;rowspan:2');
  $tb_details->easyCell('');
  $tb_details->easyCell('REF No:','valign:B');
  $tb_details->easyCell('SO Date:','valign:B');
  $tb_details->easyCell('SO No:','valign:B');
  $tb_details->easyCell('RDD:','valign:B');
  $tb_details->printRow();


  $tb_details->rowStyle('border:0;font-size:7');
  $tb_details->easyCell('','valign:T');
  $tb_details->easyCell('PAF-'.$ref_no,'valign:T');
  $tb_details->easyCell($so_date,'valign:T');
  $tb_details->easyCell($so_no,'valign:T');
  $tb_details->easyCell($rdd,'valign:T');
  $tb_details->printRow();
  $tb_details->endTable(3);

  $tb_details_2 = new easyTable($pdf , '{90,120}', 'border:0');
  $tb_details_2->rowStyle('font-size:8; font-style:B');
  $tb_details_2->easyCell('SHIP FROM:','valign:M');
  $tb_details_2->easyCell('REMARKS:','valign:M;');
  $tb_details_2->printRow();


  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($delivering_plant.'-'.$warehouse_name,'valign:M');
  $tb_details_2->easyCell("REMARKS",'valign:T; rowspan:2');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($warehouse_address,'valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:8');
  $tb_details_2->easyCell('','paddingY: 2');
  $tb_details_2->easyCell('PICKING INSTRUCTION/S:','valign:M; font-style:B');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell('','paddingY: 2');
  $tb_details_2->easyCell('1. Endorse This Document to The Zone Keeper.','valign:M');
  $tb_details_2->printRow();


  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell('SHIP TO:','valign:M; font-style:B;font-size:8');
  $tb_details_2->easyCell('2. The Zone Keeper shall work with the Operator','valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($ship_to_code.'-'.$ship_to_name,'valign:M');
  $tb_details_2->easyCell('3. Compare the Putaway Form Data vs Actual','valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($ship_to_address,'valign:M; border:B');
  $tb_details_2->easyCell('4. Return this document to the Inventory Team for Validation.','valign:M; border:B');
  $tb_details_2->printRow();
  $tb_details_2->endTable();

  /**
   * Remarks And Signatory
   */

   $table_remarks_sign = new easyTable($pdf, '{30,60,60,60}');

   $table_remarks_sign->rowStyle('font-size:8;valign:M;font-style:B;');
   $table_remarks_sign->easyCell('');
   $table_remarks_sign->easyCell('Inventory Analyst:');
   $table_remarks_sign->easyCell('Zone Keeper:');
   $table_remarks_sign->easyCell('Operator:');
   $table_remarks_sign->printRow();


   $table_remarks_sign->rowStyle('font-size:8;valign:M;paddingY:3');
   $table_remarks_sign->easyCell('Signature:');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->printRow();

   
   $table_remarks_sign->rowStyle('font-size:8;valign:M;paddingY:3');
   $table_remarks_sign->easyCell('Name:');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->printRow();


   $table_remarks_sign->rowStyle('font-size:8;valign:M;paddingY:3;');
   $table_remarks_sign->easyCell('Date:');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->easyCell('______________________________');
   $table_remarks_sign->printRow();
   $table_remarks_sign->endTable(3);

  $tb_body = new easyTable($pdf , '{10,75,10,25,25,25,40}');
  $tb_body->rowStyle('font-size:8; font-style:B; paddingY:2; border: TB');
  $tb_body->easyCell('No.','valign:T; align:C');
  $tb_body->easyCell('Material Description','valign:T;align:C');
  $tb_body->easyCell('UoM','valign:T;align:C');
  $tb_body->easyCell('Qty','valign:T;align:C');
  $tb_body->easyCell('BBD','valign:T; align:C');
  $tb_body->easyCell('Location','valign:T; align:C');
  $tb_body->easyCell('LPN','valign:T; align:C');
  $tb_body->printRow();

  $pallet_count = 1;

  foreach($db_for_putaway as $asar_key => $asar_val){
    $tb_body->rowStyle('font-size:8; paddingY:2');
    $tb_body->easyCell($pallet_count,'valign:M; align:C');
    $tb_body->easyCell($asar_val['sku_code'].'-'.$asar_val['material_description'],'valign:M;align:C');
    $tb_body->easyCell('Case','valign:M; align:C');
    $tb_body->easyCell(number_format($asar_val['qty_case'],2,'.'),'valign:M;align:C');
    $tb_body->easyCell($asar_val['expiry'],'valign:M; align:C;');
    $tb_body->easyCell('______________','valign:M; align:C');
    $tb_body->easyCell($asar_val['lpn'],'valign:M; align:C');
    $tb_body->printRow();

    $pallet_count++;
  }

  $tb_body->rowStyle('font-size:8;');
  $tb_body->easyCell('','valign:M; align:C; colspan:6 ; paddingY:10');
  $tb_body->printRow();

  $tb_body->rowStyle('font-size:8;');
  $tb_body->easyCell('-Nothing Follows-','valign:M; align:C; colspan:7');
  $tb_body->printRow();
  $tb_body->endTable(120);


  /**
   * Output With file name
   */
  $pdf->Output(); //To Print and to indicate the filename
  





?>