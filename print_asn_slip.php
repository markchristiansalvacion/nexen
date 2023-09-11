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
a.transaction_type,
a.pull_out_request_no,
a.date_requested,
a.pull_out_date,
a.eta,
a.source_code,
a.destination_code,
tb_source.source_name,
tb_warehouse.warehouse_name,
tb_warehouse.warehouse_address,
tb_source.address,
a.forwarder,
a.truck_type,
a.driver,
a.plate_no,
a.sku_code,
tb_items.material_description,
a.qty_case,
a.document_no,
a.remarks,
a.date_created
from tb_asn a
INNER JOIN tb_source ON tb_source.source_code = a.source_code
INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
INNER JOIN tb_warehouse ON tb_warehouse.warehouse_id = a.destination_code WHERE a.id = ? ', $_GET['db_id'])->fetch_array();

// print_r_html($db_asn);

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();


file_put_contents('asn_barcode_images/asn-db_id-' . $db_asn['id'] . '.png', $generator->getBarcode($db_asn['id'], $generator::TYPE_CODE_128, 10, 100));



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
$tb_header->easyCell('Advance Shipment Notice', 'font-size:10; font-style:B; align:C; valign:M');
$tb_header->printRow();
$tb_header->endTable(2);  


$tb_details = new easyTable($pdf, '{40,2,22,27,23,23,26,24,23}');
$tb_details->rowStyle('border:T;font-size:8; font-style:B');
$tb_details->easyCell('', 'img:asn_barcode_images/asn-db_id-'.$db_asn['id'].'.png, w40, h10; align:C; valign:T;rowspan:2');
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
$tb_details->easyCell('ASN-'.$db_asn['ref_no'],'valign:T');
$tb_details->easyCell($db_asn['document_no'],'valign:T');
$tb_details->easyCell($db_asn['pull_out_date'],'valign:T');
$tb_details->easyCell($db_asn['eta'],'valign:T');
$tb_details->easyCell($db_asn['forwarder'],'valign:T');
$tb_details->easyCell($db_asn['driver'],'valign:T');
$tb_details->easyCell($db_asn['plate_no'],'valign:T');
$tb_details->printRow();
$tb_details->endTable(3);

$tb_details_2 = new easyTable($pdf , '{90,120}', 'border:0');
$tb_details_2->rowStyle('font-size:8; font-style:B');
$tb_details_2->easyCell('SHIP FROM:','valign:M');
$tb_details_2->easyCell('REMARKS:','valign:M;');
$tb_details_2->printRow();


$tb_details_2->rowStyle('font-size:7');
$tb_details_2->easyCell($db_asn['source_code'].' - '.$db_asn['source_name'],'valign:M');
$tb_details_2->easyCell($db_asn['remarks'],'valign:T; rowspan:2');
$tb_details_2->printRow();

$tb_details_2->rowStyle('font-size:7');
$tb_details_2->easyCell($db_asn['address'],'valign:M');
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
$tb_details_2->easyCell('2. Prior Delivery Kindly Prepare PPE, Company ID, Vaccine Card, Oil Pan,and Tire Chocks','valign:M');
$tb_details_2->printRow();

$tb_details_2->rowStyle('font-size:7');
$tb_details_2->easyCell($db_asn['destination_code'].' - '.$db_asn['warehouse_name'],'valign:M');
$tb_details_2->easyCell('3. Submit this form to the Inbound Window for queuing.','valign:M');
$tb_details_2->printRow();

$tb_details_2->rowStyle('font-size:7');
$tb_details_2->easyCell($db_asn['warehouse_address'],'valign:M; border:B');
$tb_details_2->easyCell('4. After your shipment is received surrender this form and wait for releasing of the Goods Receipt Form.','valign:M; border:B');
$tb_details_2->printRow();
$tb_details_2->endTable();

$tb_body = new easyTable($pdf , '{30,60,15,35,35,35}');
$tb_body->rowStyle('font-size:8; font-style:B; paddingY:2');
$tb_body->easyCell('Item Code','valign:T; align:C');
$tb_body->easyCell('Material Description','valign:T;align:C');
$tb_body->easyCell('UoM','valign:T;align:C');
$tb_body->easyCell('Planned (Quantity)','valign:T;align:C');
$tb_body->easyCell('Actual','valign:T; align:C');
$tb_body->easyCell('Remarks','valign:T; align:C');
$tb_body->printRow();

$tb_body->rowStyle('font-size:8; paddingY:2');
$tb_body->easyCell($db_asn['sku_code'],'valign:M; align:C');
$tb_body->easyCell($db_asn['material_description'],'valign:M;align:C');
$tb_body->easyCell('Case','valign:M; align:C');
$tb_body->easyCell(number_format($db_asn['qty_case'],2,'.'),'valign:M;align:C');
$tb_body->easyCell('','valign:M; align:C; border:B');
$tb_body->easyCell('','valign:M; align:C');
$tb_body->printRow();

$tb_body->rowStyle('font-size:8;');
$tb_body->easyCell('','valign:M; align:C; colspan:6 ; paddingY:10');
$tb_body->printRow();

$tb_body->rowStyle('font-size:8;');
$tb_body->easyCell('-Nothing Follows-','valign:M; align:C; colspan:6');
$tb_body->printRow();
$tb_body->endTable(120);


/**
 * Remarks And Signatory
 */

  $table_remarks_sign = new easyTable($pdf, '{30,60,60,60}');

  $table_remarks_sign->rowStyle('font-size:8;valign:M;font-style:B;');
  $table_remarks_sign->easyCell('');
  $table_remarks_sign->easyCell('Driver:');
  $table_remarks_sign->easyCell('Checked By:');
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

/**
 * Output With file name
 */
$pdf->Output(); //To Print and to indicate the filename


?>