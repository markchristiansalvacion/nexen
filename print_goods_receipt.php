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

 // print_r_html($_GET);

  $db_asn = $db->query('SELECT 
  a.id,
  a.ref_no,
  a.uploading_file_name,
  a.transaction_type,
  a.pull_out_request_no,
  a.pull_out_date,
  a.ata,
  a.last_updated AS date_received,
  tb_source.source_name,
  tb_warehouse.warehouse_name,
  tb_warehouse.warehouse_address,
  tb_source.address,
  a.source_code,
  a.destination_code,
  a.forwarder,
  a.truck_type,
  a.driver,
  a.plate_no,
  a.bay_location,
  a.checker,
  a.time_arrived,
  a.unloading_start,
  a.unloading_end,
  a.time_departed,
  tb_assembly_build.ab_ref_no,
  tb_assembly_build.sku_code,
  tb_assembly_build.document_no,
  tb_assembly_build.qty_case,
  tb_assembly_build.expiry,
  tb_items.material_description,
  a.remarks
  FROM tb_asn a
  INNER JOIN tb_source ON tb_source.source_code = a.source_code 
  INNER JOIN tb_assembly_build ON tb_assembly_build.asn_id = a.id
  INNER JOIN tb_items on tb_items.sap_code = tb_assembly_build.sku_code
  INNER JOIN tb_warehouse ON tb_warehouse.warehouse_id = a.destination_code
  WHERE a.document_no = ?', $_GET['doc_no'])->fetch_all();


  //print_r_html($db_asn);
  $ab_ref_no = "";
  $pull_out_date = "";
  $ata = "";
  $forwarder = "";
  $driver ="";
  $plate_no = "";
  $source_code ="";
  $document_no = "";
  $source_name = "";
  $source_address = "";
  $destination_code = "";
  $warehouse_name = "";
  $warehouse_address = "";
  $time_arrived = "";
  $unloading_start = "";
  $unloading_end = "";
  $time_departed = "";
  $bay_location = "";

  foreach($db_asn as $asar_key =>$asar_val){
    //print_r_html($asar_val);
    $ab_ref_no = $asar_val['ab_ref_no'];
    $pull_out_date = $asar_val['pull_out_date'];
    $ata = $asar_val['ata'];
    $forwarder = $asar_val['forwarder'];
    $driver = $asar_val['driver'];
    $plate_no = $asar_val['plate_no'];
    $source_code = $asar_val['source_code'];
    $document_no = $asar_val['document_no'];
    $source_name = $asar_val['source_name'];
    $source_address = $asar_val['address'];
    $destination_code = $asar_val['destination_code'];
    $warehouse_name = $asar_val['warehouse_name'];
    $warehouse_address = $asar_val['warehouse_address'];
    $time_arrived = $asar_val['time_arrived'];
    $unloading_start = $asar_val['unloading_start'];
    $unloading_end = $asar_val['unloading_end'];
    $time_departed = $asar_val['time_departed'];
    $bay_location = $asar_val['bay_location'];
  }
  

  

  $generator = new Picqer\Barcode\BarcodeGeneratorPNG();


  file_put_contents('asn_barcode_images/asn-db_id-' . $_GET['asn_id'] . '.png', $generator->getBarcode($_GET['asn_id'], $generator::TYPE_CODE_128, 10, 100));


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
  $tb_header->easyCell('', 'img:img/pepsi_logo.png, w15; align:C; valign:M ;rowspan:3');
  $tb_header->easyCell('Arrowgo-Logistics Inc.', 'font-size:10; font-style:BI; align:C; valign:B');
  $tb_header->easyCell('', ' align:C; valign:M ;rowspan:3');
  $tb_header->printRow();

  $tb_header->rowStyle('border:0');
  $tb_header->easyCell('12th Floor Avecshares Center'."\n".'1132 University Parkway North, Bonifacio Triangle,'."\n".'Bonifacio Global City, Taguig 1634', 'font-size:7; font-style:I; align:C; valign:T');
  $tb_header->printRow();

  $tb_header->rowStyle('border:0');
  $tb_header->easyCell('Goods Receipt', 'font-size:10; font-style:B; align:C; valign:M');
  $tb_header->printRow();
  $tb_header->endTable(2);  


  $tb_details = new easyTable($pdf, '{40,2,22,27,23,23,26,24,23}');
  $tb_details->rowStyle('border:T;font-size:8; font-style:B');
  $tb_details->easyCell('', 'img:asn_barcode_images/asn-db_id-'.$_GET['asn_id'].'.png, w40, h10; align:C; valign:T;rowspan:2');
  $tb_details->easyCell('');
  $tb_details->easyCell('DOC NO:','valign:B');
  $tb_details->easyCell('PO/STO/DR NO:','valign:B');
  $tb_details->easyCell('ETD:','valign:B');
  $tb_details->easyCell('ATA:','valign:B');
  $tb_details->easyCell('FORWARDER:','valign:B');
  $tb_details->easyCell('DRIVER:','valign:B');
  $tb_details->easyCell('PLATE NO:','valign:B');
  $tb_details->printRow();

  $tb_details->rowStyle('border:0;font-size:7');
  $tb_details->easyCell('');
  $tb_details->easyCell('PGR-'.$ab_ref_no,'valign:T');
  $tb_details->easyCell($document_no,'valign:T');
  $tb_details->easyCell($pull_out_date,'valign:T');
  $tb_details->easyCell($ata,'valign:T');
  $tb_details->easyCell($forwarder,'valign:T');
  $tb_details->easyCell($driver,'valign:T');
  $tb_details->easyCell($plate_no,'valign:T');
  $tb_details->printRow();
  $tb_details->endTable(3);

  $tb_details_2 = new easyTable($pdf , '{90,120}', 'border:0');
  $tb_details_2->rowStyle('font-size:8; font-style:B');
  $tb_details_2->easyCell('SHIP FROM:','valign:M');
  $tb_details_2->easyCell('REMARKS:','valign:M;');
  $tb_details_2->printRow();


  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($source_code.' - '.$source_name,'valign:M');
  $tb_details_2->easyCell('Arrival: '.date('h:i:s A',strtotime($time_arrived)).' / Unloading Start: '.date('h:i:s A',strtotime($unloading_start)).' / Unloading End: '.date('h:i:s A',strtotime($unloading_end)).' / Bay: '.$bay_location,'valign:T; rowspan:2');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($source_address,'valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:8');
  $tb_details_2->easyCell('','paddingY: 2');
  $tb_details_2->easyCell('OTHER INSTRUCTION/S:','valign:M; font-style:B');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell('','paddingY: 2');
  $tb_details_2->easyCell('1. Inbound Team/Checker shall issue this to the forwarder.','valign:M');
  $tb_details_2->printRow();


  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell('SHIP TO:','valign:M; font-style:B;font-size:8');
  $tb_details_2->easyCell('2. The forwarder shall review the information prior signing.','valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($destination_code.' - '.$warehouse_name,'valign:M');
  $tb_details_2->easyCell('3. Arrowgo shall secure a signed copy of the document and issue the same to the forwarder.','valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($warehouse_address,'valign:M; border:B');
  $tb_details_2->easyCell('4. Prior exit to the premise the driver shall show this to the Guard.','valign:M; border:B');
  $tb_details_2->printRow();
  $tb_details_2->endTable();

  
  /**
   * Remarks And Signatory
   */

   $table_remarks_sign = new easyTable($pdf, '{30,60,60,60}');

   $table_remarks_sign->rowStyle('font-size:8;valign:M;font-style:B;');
   $table_remarks_sign->easyCell('');
   $table_remarks_sign->easyCell('Checker:');
   $table_remarks_sign->easyCell('Driver:');
   $table_remarks_sign->easyCell('Released/Approved By:');
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
   $table_remarks_sign->endTable();


  $tb_body = new easyTable($pdf , '{30,60,15,35,35,35}');
  $tb_body->rowStyle('font-size:8; font-style:B; paddingY:2; border:TB');
  $tb_body->easyCell('Item Code','valign:T; align:C');
  $tb_body->easyCell('Material Description','valign:T;align:C');
  $tb_body->easyCell('UoM','valign:T;align:C');
  $tb_body->easyCell('Actual Qty','valign:T;align:C');
  $tb_body->easyCell('BBD','valign:T; align:C');
  $tb_body->easyCell('Remarks','valign:T; align:C');
  $tb_body->printRow();

  foreach($db_asn as $db_key => $db_val){
    $tb_body->rowStyle('font-size:8; paddingY:2');
    $tb_body->easyCell($db_val['sku_code'],'valign:M; align:C');
    $tb_body->easyCell($db_val['material_description'],'valign:M;align:C');
    $tb_body->easyCell('Case','valign:M; align:C');
    $tb_body->easyCell(number_format($db_val['qty_case'],2,'.'),'valign:M;align:C');
    $tb_body->easyCell(date('d-M-Y',strtotime($db_val['expiry'])),'valign:M; align:C;');
    $tb_body->easyCell('','valign:M; align:C');
    $tb_body->printRow();
  }
  

  $tb_body->rowStyle('font-size:8;');
  $tb_body->easyCell('','valign:M; align:C; colspan:6 ; paddingY:10');
  $tb_body->printRow();

  $tb_body->rowStyle('font-size:8;');
  $tb_body->easyCell('-Nothing Follows-','valign:M; align:C; colspan:6');
  $tb_body->printRow();
  $tb_body->endTable(120);


  /**
   * Output With file name
   */
  $pdf->Output(); //To Print and to indicate the filename


?>