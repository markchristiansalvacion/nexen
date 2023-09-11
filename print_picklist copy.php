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

$picklist_count = 0;
$checklist_count = 0;
$validators_count = 0;
$dispatcher_count = 0;
$trucker_count = 0;
$total = 0;
$date = date('Y-m-d');
$picklist_details = array();
$get_db_picklist = $db->query('SELECT out_id,document_no,item_code,item_description,batch_no,qty_pcs,expiry,bin_loc FROM tb_picklist WHERE document_no=? ', $_GET['document_no'])->fetch_all();

//print_r_html($get_db_picklist);
foreach ($get_db_picklist as $arr_key => $arr_det) {
  $aux_id = $arr_det['out_id'];
  $aux_item_code = $arr_det['item_code'];
  $aux_document_no = $arr_det['document_no'];
  $aux_batch_no = $arr_det['batch_no'];
  $aux_qty = $arr_det['qty_pcs'];
  $aux_expiry = $arr_det['expiry'];

  $aux_bin = $arr_det['bin_loc'];

  $aux_desc = $arr_det['item_description'];
  $total += floatval($aux_qty);

  $get_db_outbound = $db->query('SELECT a.ref_no,
  a.source_code, 
   a.destination_code, 
   a.document_no,
   a.document_name,
   a.item_code,
   a.ship_date,
   a.eta,
   a.picker,
   a.truck_allocation,
   a.checker,
   a.created_by,
   a.validator,
   b.destination_name,
   b.destination_address,
   d.warehouse_name,
   c.material_description 
   FROM tb_outbound a 
   INNER JOIN tb_destination b ON b.destination_code = a.destination_code
   INNER JOIN tb_items c ON c.item_code = a.item_code
    INNER JOIN tb_warehouse d ON d.warehouse_id = a.source_code
    WHERE a.document_no=? ORDER BY item_code',  $aux_document_no)->fetch_all();

  $aux_count = count($get_db_picklist);
  // echo $aux_count;

  $array_count = count($picklist_details);
  // echo $array_count;

  // if (empty($get_db_outbound)) {
  //   $_SESSION['msg_heading'] = "Transaction Error!";
  //   $_SESSION['msg'] = "Print form failed. Please ask to invetory to allocate this document no {$_GET['document_no']}";
  //   $_SESSION['msg_type'] = "error";
  //   redirect($url);
  // } else {
  foreach ($get_db_outbound as $asar_key => $asar_val) {
    $ref =  $asar_val['ref_no'];
    $code =  $asar_val['truck_allocation'];
    $date =  $asar_val['ship_date'];
    $created_by =  $asar_val['created_by'];
    $document_no =  $asar_val['document_no'];
    $source =  $asar_val['warehouse_name'];
    $destination =  $asar_val['destination_name'];
    $dispatch_date =  $asar_val['ship_date'];
    $rdd =  $asar_val['eta'];
    $picker =  $asar_val['picker'];
    $checker =  $asar_val['checker'];
    $validator =  $asar_val['validator'];
    $dispatcher =  $asar_val['eta'];


    if (array_key_exists($aux_document_no, $picklist_details)) {
      $picklist_details[$aux_document_no][$asar_val['document_no']] = $asar_val;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['item_code'] = $aux_item_code;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['batch_no'] = $aux_batch_no;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['qty_pcs'] = $aux_qty;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['expiry'] = $aux_expiry;

      $picklist_details[$aux_document_no][$asar_val['document_no']]['bin_loc'] = $aux_bin;

      $picklist_details[$aux_document_no][$asar_val['document_no']]['item_description'] = $aux_desc;
    } else {
      $picklist_details[$aux_document_no][$asar_val['document_no']] = $asar_val;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['item_code'] = $aux_item_code;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['batch_no'] = $aux_batch_no;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['qty_pcs'] = $aux_qty;
      $picklist_details[$aux_document_no][$asar_val['document_no']]['expiry'] = $aux_expiry;

      $picklist_details[$aux_document_no][$asar_val['document_no']]['bin_loc'] = $aux_bin;

      $picklist_details[$aux_document_no][$asar_val['document_no']]['item_description'] = $aux_desc;
    }
  }
}
//print_r_html($picklist_details);

/** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */

/**
 * ITEMS FOR PICKLIST
 */





$pdf = new exFPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetFont('arial', '', 10);

/**
 * Picklist
 */
$pdf->AddPage();

$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w70; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');

$tb_header->printRow();
$tb_header->easyCell('PICKLIST', 'font-size:15; font-style:B; align:R;');
$tb_header->printRow();

$tb_header->endTable(2);

$picklist = new easyTable($pdf, '{100,500,200}');
$picklist->easyCell('', 'font-size:10; align:L; valign:M');
$picklist->easyCell('', 'border-color:#afb5bf; font-style:B;font-size:10; valign:M; align:L');
$picklist->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:9; rowspan:2');
$picklist->printRow();

$picklist->rowStyle('font-size:10');
$picklist->easyCell('<b>Reference #:</b>', 'align:L; valign:M');
$picklist->easyCell("#000" . $aux_id . "-" . $code, 'border-color:#afb5bf; align:L;font-style:B; valign:M; font-size:10');
$picklist->easyCell("");
$picklist->printRow();
$picklist->endTable(5);


$delivery_details = new easyTable($pdf, '{120,300,100,100');
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>DOCUMENT NO:</b>', 'font-size:8');
$delivery_details->easyCell('<b>' . $document_no . '</b>', 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>PICKING START:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; font-size:10; align:L');

$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>BRANCH NAME:</b>', 'font-size:8');
$delivery_details->easyCell($destination, 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>PICKING END:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>DATE PICKED:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M');
$delivery_details->easyCell('<b>DATE GENERATED:</b>', 'font-size:8');
$delivery_details->easyCell($date, 'border-color:black; align:L; font-size:8');
$delivery_details->easyCell('<b>PICKER:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{110,80,80');
$table_remarks_sign->rowStyle('font-size:8;border-color:black;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Picked By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Signature:', 'font-size:8; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Date:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);
/**
 * ITEM LIST
 */
$tableD = new easyTable($pdf, '{60,60,60,60,10,10,25,5,20}', 'split-row:true');
$tableD->rowStyle('font-size:7; split-row:false');
$tableD->easyCell('', 'colspan:8; paddingY:2');
$tableD->printRow();
$tableD->rowStyle('font-size:8');
$tableD->easyCell('REMARKS', 'font-style:B;colspan:5;;border:1; border-color:black; font-color:black;align:L;');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL QTY</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($total, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->easyCell('', 'font-style:B;colspan:5; border:LRB; border-color:#afb5bf; rowspan:5');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL SKU</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($aux_count, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->rowStyle('paddingY:1;');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->endTable(5);
$table_item_list = new easyTable($pdf, '{25,30,90,35,20,25,60}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:black; font-color:black;valign:M');

$table_item_list->easyCell('LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY DATE', 'font-style:B; align:C');
$table_item_list->easyCell('PICKING TAG', 'font-style:B; align:C');

$table_item_list->printRow();


$i = 1;
foreach ($get_db_picklist as $arr_key => $arr_item_det) {

  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:white;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);
  $table_item_list->easyCell($arr_item_det['bin_loc']);
  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell($arr_item_det['batch_no']);
  $table_item_list->easyCell($arr_item_det['qty_pcs']);
  $table_item_list->easyCell($arr_item_det['expiry']);

  $table_item_list->easyCell('');

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();
$table_item_list->endTable(2);

$picklist_count++;


/***CHECKLIST */

$pdf->AddPage();
$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w70; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');

$tb_header->printRow();
$tb_header->easyCell('CHECKLIST', 'font-size:15; font-style:B; align:R;');
$tb_header->printRow();

$tb_header->endTable(2);

$picklist = new easyTable($pdf, '{100,500,200}');
$picklist->easyCell('', 'font-size:10; align:L; valign:M');
$picklist->easyCell('', 'border-color:#afb5bf; font-style:B;font-size:10; valign:M; align:L');
$picklist->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:9; rowspan:2');
$picklist->printRow();

$picklist->rowStyle('font-size:10');
$picklist->easyCell('<b>Reference #:</b>', 'align:L; valign:M');
$picklist->easyCell("#000" . $aux_id . "-" . $code, 'border-color:#afb5bf; align:L;font-style:B; valign:M; font-size:10');
$picklist->easyCell("");
$picklist->printRow();
$picklist->endTable(5);


$delivery_details = new easyTable($pdf, '{120,300,100,100');
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>DOCUMENT NO:</b>', 'font-size:8');
$delivery_details->easyCell('<b>' . $document_no . '</b>', 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>CHECKING START:</b>', 'font-size:8');
$delivery_details->easyCell('', 'border-color:black; font-size:8; align:L');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>BRANCH NAME:</b>', 'font-size:8');
$delivery_details->easyCell($destination, 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>CHECKING END:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:10');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>DATE CHECKED:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:10');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M');
$delivery_details->easyCell('<b>DATE GENERATED:</b>', 'font-size:8');
$delivery_details->easyCell($date, 'border-color:black; align:L; font-size:8');
$delivery_details->easyCell('<b>CHECKER:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{110,80,80');
$table_remarks_sign->rowStyle('font-size:8;border-color:black;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Checked By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Signature:', 'font-size:8; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Date:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);
/**
 * ITEM LIST
 */
$tableD = new easyTable($pdf, '{60,60,60,60,10,10,25,5,20}', 'split-row:true');
$tableD->rowStyle('font-size:7; split-row:false');
$tableD->easyCell('', 'colspan:8; paddingY:2');
$tableD->printRow();
$tableD->rowStyle('font-size:8');
$tableD->easyCell('REMARKS', 'font-style:B;colspan:5;;border:1; border-color:black; font-color:black;align:L;');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL QTY</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($total, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->easyCell('', 'font-style:B;colspan:5; border:LRB; border-color:#afb5bf; rowspan:5');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL SKU</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($aux_count, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->rowStyle('paddingY:1;');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->endTable(5);
$table_item_list = new easyTable($pdf, '{25,30,90,35,20,25,60}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:black; font-color:black;valign:M');

$table_item_list->easyCell('LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY DATE', 'font-style:B; align:C');
$table_item_list->easyCell('PICKING TAG', 'font-style:B; align:C');

$table_item_list->printRow();


$i = 1;
foreach ($get_db_picklist as $arr_key => $arr_item_det) {

  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:white;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);
  $table_item_list->easyCell($arr_item_det['bin_loc']);
  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell('');
  $table_item_list->easyCell('');
  $table_item_list->easyCell('');

  $table_item_list->easyCell('');

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();
$table_item_list->endTable(2);


/****VALIDATOR */
$pdf->AddPage();
$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w70; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');

$tb_header->printRow();
$tb_header->easyCell('VALIDATOR', 'font-size:15; font-style:B; align:R;');
$tb_header->printRow();

$tb_header->endTable(2);

$picklist = new easyTable($pdf, '{100,500,200}');
$picklist->easyCell('', 'font-size:10; align:L; valign:M');
$picklist->easyCell('', 'border-color:#afb5bf; font-style:B;font-size:10; valign:M; align:L');
$picklist->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:9; rowspan:2');
$picklist->printRow();

$picklist->rowStyle('font-size:10');
$picklist->easyCell('<b>Reference #:</b>', 'align:L; valign:M');
$picklist->easyCell("#000" . $aux_id . "-" . $code, 'border-color:#afb5bf; align:L;font-style:B; valign:M; font-size:10');
$picklist->easyCell("");
$picklist->printRow();
$picklist->endTable(5);


$delivery_details = new easyTable($pdf, '{120,300,100,100');
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>DOCUMENT NO:</b>', 'font-size:8');
$delivery_details->easyCell('<b>' . $document_no . '</b>', 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>VALIDATION START:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; font-size:10; align:L');

$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>BRANCH NAME:</b>', 'font-size:8');
$delivery_details->easyCell($destination, 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>VALIDATION END:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>DATE VALIDATED:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M');
$delivery_details->easyCell('<b>DATE GENERATED:</b>', 'font-size:8');
$delivery_details->easyCell($date, 'border-color:black; align:L; font-size:8');
$delivery_details->easyCell('<b>VALIDATOR:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{110,80,80');
$table_remarks_sign->rowStyle('font-size:8;border-color:black;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Validated By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Signature:', 'font-size:8; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Date:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);
/**
 * ITEM LIST
 */
$tableD = new easyTable($pdf, '{60,60,60,60,10,10,25,5,20}', 'split-row:true');
$tableD->rowStyle('font-size:7; split-row:false');
$tableD->easyCell('', 'colspan:8; paddingY:2');
$tableD->printRow();
$tableD->rowStyle('font-size:8');
$tableD->easyCell('REMARKS', 'font-style:B;colspan:5;;border:1; border-color:black; font-color:black;align:L;');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL QTY</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($total, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->easyCell('', 'font-style:B;colspan:5; border:LRB; border-color:#afb5bf; rowspan:5');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL SKU</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($aux_count, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->rowStyle('paddingY:1;');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->endTable(5);
$table_item_list = new easyTable($pdf, '{25,30,90,35,20,25,60}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:black; font-color:black;valign:M');

$table_item_list->easyCell('LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY DATE', 'font-style:B; align:C');
$table_item_list->easyCell('PICKING TAG', 'font-style:B; align:C');

$table_item_list->printRow();


$i = 1;
foreach ($get_db_picklist as $arr_key => $arr_item_det) {

  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:white;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);
  $table_item_list->easyCell($arr_item_det['bin_loc']);
  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell($arr_item_det['batch_no']);
  $table_item_list->easyCell($arr_item_det['qty_pcs']);
  $table_item_list->easyCell($arr_item_det['expiry']);
  $table_item_list->easyCell('');

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();
$table_item_list->endTable(2);


/**DISPATCHER */
$pdf->AddPage();
$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w70; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');

$tb_header->printRow();
$tb_header->easyCell('DISPATCHER', 'font-size:15; font-style:B; align:R;');
$tb_header->printRow();

$tb_header->endTable(2);

$picklist = new easyTable($pdf, '{100,500,200}');
$picklist->easyCell('', 'font-size:10; align:L; valign:M');
$picklist->easyCell('', 'border-color:#afb5bf; font-style:B;font-size:10; valign:M; align:L');
$picklist->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:9; rowspan:2');
$picklist->printRow();

$picklist->rowStyle('font-size:10');
$picklist->easyCell('<b>Reference #:</b>', 'align:L; valign:M');
$picklist->easyCell("#000" . $aux_id . "-" . $code, 'border-color:#afb5bf; align:L;font-style:B; valign:M; font-size:10');
$picklist->easyCell("");
$picklist->printRow();
$picklist->endTable(5);


$delivery_details = new easyTable($pdf, '{120,300,100,100');
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>DOCUMENT NO:</b>', 'font-size:8');
$delivery_details->easyCell('<b>' . $document_no . '</b>', 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>DISPATCH DATE:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; font-size:10; align:L');

$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>BRANCH NAME:</b>', 'font-size:8');
$delivery_details->easyCell($destination, 'border-color:black; font-size:8; align:L');
$delivery_details->easyCell('<b>DISPATCHER:</b>', 'font-size:8;align:L');
$delivery_details->easyCell('', 'border-color:black; align:C; font-size:8');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:black; font-size:8; align:L');

$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M');
$delivery_details->easyCell('<b>DATE GENERATED:</b>', 'font-size:8');
$delivery_details->easyCell($date, 'border-color:black; align:L; font-size:8');

$delivery_details->printRow();
$delivery_details->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{110,80,80');
$table_remarks_sign->rowStyle('font-size:8;border-color:black;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Dipatched By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Signature:', 'font-size:8; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Date:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);
/**
 * ITEM LIST
 */
$tableD = new easyTable($pdf, '{60,60,60,60,10,10,25,5,20}', 'split-row:true');
$tableD->rowStyle('font-size:7; split-row:false');
$tableD->easyCell('', 'colspan:8; paddingY:2');
$tableD->printRow();
$tableD->rowStyle('font-size:8');
$tableD->easyCell('REMARKS', 'font-style:B;colspan:5;;border:1; border-color:black; font-color:black;align:L;');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL QTY</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($total, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->easyCell('', 'font-style:B;colspan:5; border:LRB; border-color:#afb5bf; rowspan:5');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL SKU</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($aux_count, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->rowStyle('paddingY:1;');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->endTable(5);

$table_item_list = new easyTable($pdf, '{25,30,90,35,20,25,60}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:black; font-color:black;valign:M');

$table_item_list->easyCell('LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY DATE', 'font-style:B; align:C');
$table_item_list->easyCell('PICKING TAG', 'font-style:B; align:C');

$table_item_list->printRow();


$i = 1;
foreach ($get_db_picklist as $arr_key => $arr_item_det) {

  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:white;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);
  $table_item_list->easyCell($arr_item_det['bin_loc']);
  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell($arr_item_det['batch_no']);
  $table_item_list->easyCell($arr_item_det['qty_pcs']);
  $table_item_list->easyCell($arr_item_det['expiry']);

  $table_item_list->easyCell('');

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();
$table_item_list->endTable(2);

/**TRUCKER */
$pdf->AddPage();
$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w70; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');

$tb_header->printRow();
$tb_header->easyCell('TRUCKER', 'font-size:15; font-style:B; align:R;');
$tb_header->printRow();

$tb_header->endTable(2);

$picklist = new easyTable($pdf, '{100,500,200}');
$picklist->easyCell('', 'font-size:10; align:L; valign:M');
$picklist->easyCell('', 'border-color:#afb5bf; font-style:B;font-size:10; valign:M; align:L');
$picklist->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:9; rowspan:2');
$picklist->printRow();

$picklist->rowStyle('font-size:10');
$picklist->easyCell('<b>Reference #:</b>', 'align:L; valign:M');
$picklist->easyCell("#000" . $aux_id . "-" . $code, 'border-color:#afb5bf; align:L;font-style:B; valign:M; font-size:10');
$picklist->easyCell("");
$picklist->printRow();
$picklist->endTable(5);


$delivery_details = new easyTable($pdf, '{120,300,100,100');
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>DOCUMENT NO:</b>', 'font-size:8');
$delivery_details->easyCell('<b>' . $document_no . '</b>', 'border-color:black; font-size:8; align:L');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>BRANCH NAME:</b>', 'font-size:8');
$delivery_details->easyCell($destination, 'border-color:black; font-size:8; align:L');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:black; font-size:8; align:L');
$delivery_details->printRow();
$delivery_details->rowStyle('font-size:8; valign:M');
$delivery_details->easyCell('<b>DATE GENERATED:</b>', 'font-size:8');
$delivery_details->easyCell($date, 'border-color:black; align:L; font-size:8');

$delivery_details->printRow();
$delivery_details->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{110,80,80');
$table_remarks_sign->rowStyle('font-size:8;border-color:black;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Trucker:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C; border:1;font-color:black');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Signature:', 'font-size:8; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Name:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();
$table_remarks_sign->printRow();
$table_remarks_sign->easyCell('Date:', 'font-size:8; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('__________________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);
/**
 * ITEM LIST
 */
$tableD = new easyTable($pdf, '{60,60,60,60,10,10,25,5,20}', 'split-row:true');
$tableD->rowStyle('font-size:7; split-row:false');
$tableD->easyCell('', 'colspan:8; paddingY:2');
$tableD->printRow();
$tableD->rowStyle('font-size:8');
$tableD->easyCell('REMARKS', 'font-style:B;colspan:5;;border:1; border-color:black; font-color:black;align:L;');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL QTY</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($total, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->easyCell('', 'font-style:B;colspan:5; border:LRB; border-color:#afb5bf; rowspan:5');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8"><b>TOTAL SKU</b></s>', 'align:R;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:8; border:TLB; border-color:black');
$tableD->easyCell($aux_count, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();
$tableD->rowStyle('paddingY:1;');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->rowStyle('paddingY:1');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->endTable(5);
$table_item_list = new easyTable($pdf, '{25,30,90,35,20,25,60}');

$table_item_list->rowStyle('font-size:8;border:1;border-color:black; font-color:black;valign:M');

$table_item_list->easyCell('LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY DATE', 'font-style:B; align:C');
$table_item_list->easyCell('PICKING TAG', 'font-style:B; align:C');

$table_item_list->printRow();


$i = 1;
foreach ($get_db_picklist as $arr_key => $arr_item_det) {

  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:white;';
  }

  $table_item_list->rowStyle('border:LRB;font-size:8; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);
  $table_item_list->easyCell($arr_item_det['bin_loc']);
  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell('');
  $table_item_list->easyCell('');
  $table_item_list->easyCell('');

  $table_item_list->easyCell('');

  $table_item_list->printRow();
  $i++;
}

/**
 * Nothing Follows
 */
$table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
$table_item_list->easyCell('*** Nothing Follows ***', 'colspan:8');
$table_item_list->printRow();
$table_item_list->endTable(2);


/**
 * Output With file name
 */

$pdf->Output('', "FORMS-" . $_GET['ref_no'] . "-" . $document_no . ".pdf");
//To Print and to indicate the filename




?>