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
$asn_details = $db->query('SELECT 
    a.ref_no,
    a.transaction_type,
    a.document_no,
    a.eta,
    a.ata,
    a.loading_bay,
    a.time_slot,
    a.plate_no,
    a.truck_type,
    a.created_by,
    a.time_arrived,
 
    a.time_docked,
    a.unloading_start,
    a.unloading_end,
   
    a.time_departed,
    tb_vendor.vendor_name,
    tb_destination.destination_address
    FROM tb_inbound a
    INNER JOIN tb_destination ON tb_destination.destination_code = a.destination_code
    INNER JOIN tb_vendor ON tb_vendor.vendor_id = a.vendor_code
    WHERE a.id = ?', $_GET['id'])->fetch_array();
//print_r_html($asn_details);

$inbound_db_details = $db->query('SELECT * FROM tb_inbound where ref_no = ? ORDER BY batch_no,item_code DESC', $_GET['ref_no'])->fetch_all();
$inbound_ref = $db->query('SELECT DISTINCT ref_no FROM tb_inbound WHERE ref_no = ?', $_GET['ref_no'])->fetch_array();


?>

<?php

$date_today = date('M-d-Y g:i A');

$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$tb_header = new easyTable($pdf, 3);
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w40; align:L; valign:M');
$tb_header->easyCell('INBOUND INSPECTION FORM', 'font-size:15; font-style:B; align:C; valign:M');
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w40; align:R; valign:M');
$tb_header->printRow();
$tb_header->endTable(5);

/**
 * INFO SECTION
 */

$tb_info = new easyTable($pdf, '{25,67.5,5,25,67.5}');
$tb_info->easyCell('System Ref. No:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('AGL-' . $asn_details['ref_no'], 'font-size:8; font-style:B; align:C; valign:M;border:B');

$tb_info->easyCell('');
$tb_info->easyCell('Printed:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($date_today, 'font-size:8; font-style:B; align:C; valign:M; border:B');


$tb_info->printRow();
$tb_info->easyCell('Source Document:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($asn_details['document_no'], 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Arrival:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell(date('M-d-Y g:i A', strtotime($asn_details['time_arrived'])), 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->easyCell('Source/Supplier:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($asn_details['vendor_name'], 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Gate In:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('', 'font-size:8; font-style:B;align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->easyCell('Loading Bay:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell($asn_details['loading_bay'], 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Truck Type:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell(strtoupper($asn_details['truck_type']), 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->easyCell('Staging Lane:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('', 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Plate No:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell(strtoupper($asn_details['plate_no']), 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->easyCell('Unloading Start:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('', 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->easyCell('');
$tb_info->easyCell('Unloading End:', 'font-size:8;align:L; valign:M;');
$tb_info->easyCell('', 'font-size:8; font-style:B; align:C; valign:M;border:B');
$tb_info->printRow();

$tb_info->endTable(5);

/**
 * SIGN AREA
 */

$table_remarks_sign = new easyTable($pdf, '{20,55,60,55');

$table_remarks_sign->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Checker:', 'font-style:B; align:C;border:1;font-color:black');
$table_remarks_sign->easyCell('Inbound Associate/Encoder:', 'font-style:B; align:C;border:1;font-color:black');
$table_remarks_sign->easyCell('Trucker:', 'font-style:B; align:C;border:1;font-color:black');
$table_remarks_sign->printRow();


$table_remarks_sign->easyCell('Signature:', 'font-size:7; align:L; valign:B; paddingX:2;paddingY:5;');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Name:', 'font-size:7; align:L; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Designation:', 'font-size:7; align:L; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1:LR');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Date:', 'font-size:7; align:L; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->easyCell('', 'align:C; valign:B;border:1');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);

/**
 * ITEMS PROPER
 */

$tb_info = new easyTable($pdf, '{35,40,40,40,35}');

$tb_info->rowStyle('border:1; paddingY:2; font-size:7; font-style:B;border-color:#2d3238');
$tb_info->easyCell('Item Code', 'align:C; valign:M;');

$tb_info->easyCell('Batch Code', 'align:C; valign:M;');
$tb_info->easyCell('Qty (Pcs)', 'align:C; valign:M;');
$tb_info->easyCell('Initial Remarks', 'align:C; valign:M;');
$tb_info->easyCell('Other Remarks', 'align:C; valign:M;');

$tb_info->printRow();

$last_inserted_p_tag = "";
$pallet_cluster = 0;

foreach ($inbound_db_details as $db_key => $db_val) {

  if (are_strings_equal($last_inserted_p_tag, $db_val['pallet_tag'])) {

    $cell_color = "#d5d7db";
    $tb_info->rowStyle('border:1; paddingY:2; font-size:8;border-color:#2d3238');


    $tb_info->easyCell($db_val['item_code'], 'align:C; valign:M; font-style:B;');
    $tb_info->easyCell($db_val['batch_no'], 'align:C; valign:M; font-style:B;');
    $tb_info->easyCell($db_val['qty_pcs'], 'align:C; valign:M;');
    $tb_info->easyCell('', 'align:C; valign:M;');
    $tb_info->easyCell('', 'align:C; valign:M;');
    $tb_info->printRow();
    $last_inserted_p_tag = $db_val['pallet_tag'];
  } else {

    $pallet_cluster++;
    $tb_info->rowStyle('border:1; paddingY:2; font-size:8;border-color:#2d3238');
    $tb_info->easyCell($db_val['id'], 'align:C; valign:M;font-style:B;');

    $tb_info->easyCell($db_val['item_code'], 'align:C; valign:M; font-style:B;');
    $tb_info->easyCell($db_val['batch_no'], 'align:C; valign:M; font-style:B;');
    $tb_info->easyCell($db_val['qty_pcs'], 'align:C; valign:M;');
    $tb_info->easyCell('', 'align:C; valign:M;');
    $tb_info->easyCell('', 'align:C; valign:M;');

    $tb_info->printRow();
    $last_inserted_p_tag = $db_val['pallet_tag'];
  }
}
$tb_info->rowStyle('paddingY:2; font-size:8;');
$tb_info->easyCell('**NOTHING FOLLOWS**', 'colspan:7; align:C');
$tb_info->printRow();
$tb_info->endTable(2);

$pdf->Output('', 'Inbound Inspection FORM IIF-' . $asn_details['ref_no'] . '.pdf'); //To Print and to indicate the filename
?>
