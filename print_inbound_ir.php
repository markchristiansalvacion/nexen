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

$db_ir = $db->query('SELECT
  a.id,
  a.ir_ref_no as ref_no,
  a.asn_id,
  a.asn_ref_no,
  a.document_no,
  a.sku_code,
  a.qty_case,
  a.expiry,
  a.ir_status,
  a.reason,
  a.description,
  tb_asn.forwarder,
  tb_asn.driver,
  tb_asn.plate_no,
  tb_asn.eta,
  tb_asn.pull_out_date,
  tb_asn.source_code,
  tb_asn.destination_code,
  tb_source.source_name,
  tb_source.address,
  tb_warehouse.warehouse_name,
  tb_warehouse.warehouse_address,
  tb_items.material_description
  FROM tb_inbound_ir a
  INNER JOIN tb_asn ON tb_asn.id = a.asn_id
  INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
  INNER JOIN tb_warehouse ON tb_warehouse.warehouse_id = tb_asn.destination_code
  INNER JOIN tb_source ON tb_source.source_code = tb_asn.source_code
  WHERE a.asn_id = ?',$_GET['asn_id'])->fetch_all();

  $ir_ref_no = "";
  $document_no = "";
  $pull_out_date = "";
  $eta = "";
  $forwarder = "";
  $driver = "";
  $plate_no = "";
  $source_code = "";
  $source_name = "";
  $source_address = "";
  $destination_code = "";
  $warehouse_name = "";
  $warehouse_address = "";

  foreach($db_ir as $aux_key => $aux_val){
    $ir_ref_no = $aux_val['ref_no'];
    $document_no = $aux_val['document_no'];
    $pull_out_date = $aux_val['pull_out_date'];
    $eta = $aux_val['eta'];
    $forwarder = $aux_val['forwarder'];
    $driver = $aux_val['driver'];
    $plate_no = $aux_val['plate_no'];
    $source_code = $aux_val['source_code'];
    $source_name = $aux_val['source_name'];
    $source_address = $aux_val['address'];
    $destination_code = $aux_val['destination_code'];
    $warehouse_name = $aux_val['warehouse_name'];
    $warehouse_address = $aux_val['warehouse_address'];
  }


$generator = new Picqer\Barcode\BarcodeGeneratorPNG();


file_put_contents('incident_report_barcode/ir-' . $ir_ref_no . '.png', $generator->getBarcode($ir_ref_no, $generator::TYPE_CODE_128, 10, 100));



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
  $tb_header->easyCell('Inbound Incident Report', 'font-size:10; font-style:B; align:C; valign:M');
  $tb_header->printRow();
  $tb_header->endTable(2);  


  $tb_details = new easyTable($pdf, '{40,2,22,27,23,23,26,24,23}');
  $tb_details->rowStyle('border:T;font-size:8; font-style:B');
  $tb_details->easyCell('', 'img:incident_report_barcode/ir-'.$ir_ref_no.'.png, w40, h10; align:C; valign:T;rowspan:2');
  $tb_details->easyCell('');
  $tb_details->easyCell('DOC NO:','valign:B');
  $tb_details->easyCell('PO/STO/DR NO:','valign:B');
  $tb_details->easyCell('ETD:','valign:B');
  $tb_details->easyCell('ETA:','valign:B');
  $tb_details->easyCell('FORWARDER:','valign:B');
  $tb_details->easyCell('DRIVER:','valign:B');
  $tb_details->easyCell('PLATE NO:','valign:B');
  $tb_details->printRow();

  $tb_details->rowStyle('border:0;font-size:7');
  $tb_details->easyCell('');
  $tb_details->easyCell('IR-'.$ir_ref_no,'valign:T');
  $tb_details->easyCell($document_no,'valign:T');
  $tb_details->easyCell($pull_out_date,'valign:T');
  $tb_details->easyCell($eta,'valign:T');
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
  $tb_details_2->easyCell("",'valign:T; rowspan:2');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($source_address,'valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:8');
  $tb_details_2->easyCell('','paddingY: 2');
  $tb_details_2->easyCell('SHIPPING INSTRUCTION/S:','valign:M; font-style:B');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell('','paddingY: 2');
  $tb_details_2->easyCell('1. Present this document to the receiving guard for scanning and validation of transaction.','valign:M');
  $tb_details_2->printRow();


  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell('SHIP TO:','valign:M; font-style:B;font-size:8');
  $tb_details_2->easyCell('2. Ensure the accuracy of the document vs actual stocks.','valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($destination_code.' - '.$warehouse_name,'valign:M');
  $tb_details_2->easyCell('3. Sign and Secure a copy of this document.','valign:M');
  $tb_details_2->printRow();

  $tb_details_2->rowStyle('font-size:7');
  $tb_details_2->easyCell($warehouse_address,'valign:M; border:B');
  $tb_details_2->easyCell('4. Any dispute beyond 24 hours after this document was acknowledge shall not be honored by Arrowgo.','valign:M; border:B');
  $tb_details_2->printRow();
  $tb_details_2->endTable();

  /**
   * Remarks And Signatory
   */

   $table_remarks_sign = new easyTable($pdf, '{30,60,60,60}');

   $table_remarks_sign->rowStyle('font-size:8;valign:M;font-style:B;');
   $table_remarks_sign->easyCell('');
   $table_remarks_sign->easyCell('Inbound Admin:');
   $table_remarks_sign->easyCell('Checker:');
   $table_remarks_sign->easyCell('Trucker:');
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

  $tb_body = new easyTable($pdf , '{25,50,10,20,30,35,40}');
  $tb_body->rowStyle('font-size:8; font-style:B; paddingY:2; valign:M; border:TB');
  $tb_body->easyCell('Item Code','valign:T; align:C');
  $tb_body->easyCell('Material Description','valign:T;align:C');
  $tb_body->easyCell('UoM','valign:T;align:C');
  $tb_body->easyCell('Qty (Case)','valign:T;align:C');
  $tb_body->easyCell('BBD','valign:T; align:C');
  $tb_body->easyCell('Nature of IR','valign:T; align:C');
  $tb_body->easyCell('Remarks','valign:T; align:C');
  $tb_body->printRow();

  foreach($db_ir as $print_key => $print_val){
    $tb_body->rowStyle('font-size:8; paddingY:2');
    $tb_body->easyCell($print_val['sku_code'],'valign:M; align:C');
    $tb_body->easyCell($print_val['material_description'],'valign:M;align:C');
    $tb_body->easyCell('Case','valign:M; align:C');
    $tb_body->easyCell(number_format($print_val['qty_case'],2,'.'),'valign:M;align:C');
    $tb_body->easyCell($print_val['expiry'],'valign:M; align:C;');
    $tb_body->easyCell($print_val['reason'],'valign:M; align:C');
    $tb_body->easyCell($print_val['description'],'valign:M; align:C');
    $tb_body->printRow();
  }
 

  $tb_body->rowStyle('font-size:8;');
  $tb_body->easyCell('','valign:M; align:C; colspan:7 ; paddingY:10');
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