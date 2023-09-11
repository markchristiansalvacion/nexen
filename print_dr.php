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

$date = date('Y-m-d');
$picklist_details = array();
$db_picklist = $db->query('SELECT id,document_no,item_code,item_description,batch_no,qty_pcs,expiry,bin_loc FROM tb_validated WHERE document_no=? ', $_GET['document_no'])->fetch_all();

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();

foreach ($db_picklist as $asar_key => $asar_val) {
  file_put_contents('barcode_images/dr-' . $asar_val['document_no'] . '.png', $generator->getBarcode($asar_val['document_no'], $generator::TYPE_CODE_128, 10, 400));
}

foreach ($db_picklist as $arr_key => $arr_det) {
  $aux_id = $arr_det['id'];
  $aux_item_code = $arr_det['item_code'];
  $aux_document_no = $arr_det['document_no'];
  $aux_batch_no = $arr_det['batch_no'];
  $aux_qty = $arr_det['qty_pcs'];
  $aux_expiry = $arr_det['expiry'];
  $aux_bin = $arr_det['bin_loc'];
  $aux_desc = $arr_det['item_description'];


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

  $aux_count = count($db_picklist);
  //echo $aux_count;
  $total = 0;
  $array_count = count($picklist_details);
  // echo $array_count;


  if (empty($get_db_outbound)) {
    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "Print form failed. Please ask to invetory to allocate this document no {$_GET['document_no']}";
    $_SESSION['msg_type'] = "error";
    redirect("view_str", false);
  } else {
    foreach ($get_db_outbound as $asar_key => $db_val) {
      $ref = $db_val['ref_no'];
      $code = $db_val['truck_allocation'];
      // $date = date('m-d-Y g:i:s a', $db_val['date']);
      $created_by = $db_val['created_by'];
      $document_no = $db_val['document_no'];

      $source = $db_val['warehouse_name'];
      $destination = $db_val['destination_name'] . " - " .
        $db_val['destination_address'];
      $dispatch_date = $db_val['ship_date'];
      $rdd = $db_val['eta'];
      $picker = $db_val['picker'];
      $checker = $db_val['checker'];
      $validator = $db_val['validator'];
      $total += floatval($aux_qty);

      if (array_key_exists($aux_document_no, $picklist_details)) {
        $picklist_details[$aux_document_no][$db_val['document_no']] = $db_val;
        $picklist_details[$aux_document_no][$db_val['document_no']]['item_code'] = $aux_item_code;
        $picklist_details[$aux_document_no][$db_val['document_no']]['batch_no'] = $aux_batch_no;
        $picklist_details[$aux_document_no][$db_val['document_no']]['qty_pcs'] = $aux_qty;
        $picklist_details[$aux_document_no][$db_val['document_no']]['expiry'] = $aux_expiry;

        $picklist_details[$aux_document_no][$db_val['document_no']]['bin_loc'] = $aux_bin;

        $picklist_details[$aux_document_no][$db_val['document_no']]['item_description'] = $aux_desc;
      } else {
        $picklist_details[$aux_document_no][$db_val['document_no']] = $db_val;
        $picklist_details[$aux_document_no][$db_val['document_no']]['item_code'] = $aux_item_code;
        $picklist_details[$aux_document_no][$db_val['document_no']]['batch_no'] = $aux_batch_no;
        $picklist_details[$aux_document_no][$db_val['document_no']]['qty_pcs'] = $aux_qty;
        $picklist_details[$aux_document_no][$db_val['document_no']]['expiry'] = $aux_expiry;

        $picklist_details[$aux_document_no][$db_val['document_no']]['bin_loc'] = $aux_bin;

        $picklist_details[$aux_document_no][$db_val['document_no']]['item_description'] = $aux_desc;
      }
    }
  }
  // print_r_html($picklist_details);
}

$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('', 'img:img/personal_collection_logo.png, w70; align:L; valign:M; rowspan:3; ');
$tb_header->printRow();


$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w30; align:R;');
$tb_header->printRow();

$tb_header->easyCell('DELIVERY RECEIPT', 'font-size:15; font-style:B; align:R; valign:M');

$tb_header->printRow();


//  $tb_header->easyCell('DELIVERY RECEIPT', 'font-size:15; font-style:B; align:C; valign:M;border:1');
//  $tb_header->printRow();

$tb_header->endTable(2);
$tb_label = new easyTable($pdf, '{80,180}');

$tb_label->rowStyle('font-size:7;');
$tb_label->easycell('', 'img:barcode_images/dr-' . $document_no . '.png,w60,h15,');
$tb_label->printRow();

$tb_label->endTable(2);
$table = new easyTable($pdf, '{120,500,200}');

$table->easyCell('<b>SOURCE DOCUMENT:</b>', 'font-size:8; align:L; valign:M');
$table->easyCell($document_no, 'border-color:#afb5bf; font-style:B; valign:M; align:L');
$table->easyCell('Arrowgo-Logistics Inc. Warehouse Complex' . "\n" . '11M, Villarica Road,' . "\n" . 'Brgy Patubig, Bulacan 3019' . "\n" . '<b>agli.support@arrowgologistics.com</b>', 'align:R; font-size:8; rowspan:2');
$table->printRow();


$table->rowStyle('font-size:8');
$table->easyCell('<b>DR #:</b>', 'align:L; valign:M');
$table->easyCell("DR-" . $ref . "-" . $code, 'border-color:#afb5bf; align:L;font-style:B; valign:M');
$table->easyCell("");
$table->printRow();
$table->endTable(5);


$delivery_details = new easyTable($pdf, '{150,500,200,190}');

$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>SOURCE:</b>', 'font-size:8');
$delivery_details->easyCell($source, 'border-color:#afb5bf; font-size:8; align:L');
$delivery_details->easyCell('<b>DISPATCH DATE:</b>', 'font-size:8; align:R');
$delivery_details->easyCell($dispatch_date, 'border-color:#afb5bf; font-size:8; align:C');
$delivery_details->printRow();

$delivery_details->rowStyle('font-size:8; valign:M;');
$delivery_details->easyCell('<b>DESTINATION:</b>', 'font-size:8');
$delivery_details->easyCell($destination, 'border-color:#afb5bf; font-size:8; align:L');
$delivery_details->easyCell('<b>DELIVERY DATE:</b>', 'font-size:8; align:R');
$delivery_details->easyCell($rdd, 'border-color:#afb5bf; font-size:8; align:C');
$delivery_details->printRow();

$delivery_details->rowStyle('font-size:8; valign:M');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:8');
$delivery_details->easyCell($_SESSION['name'], 'border-color:#afb5bf; align:L; font-size:8');
$delivery_details->easyCell('<b>DATE GENERATED:</b>', 'font-size:8;align:R');
$delivery_details->easyCell($date, 'border-color:#afb5bf; align:C; font-size:8');
$delivery_details->printRow();


$delivery_details->endTable(2);


/**
 * REMARKS
 */

// $table_remarks = new easyTable($pdf, 1);
// $table_remarks->rowStyle('font-size:9;border-color:#2d3238;valign:M');
// $table_remarks->easyCell('REMARKS', 'font-style:B; align:C; bgcolor:#33E838;border:1;font-color:#ffffff');
// $table_remarks->printRow();

// $table_remarks->easyCell('', 'border:LRB; paddingY:20');
// $table_remarks->printRow();

// $table_remarks->endTable(2);

/**
 * Signatory
 */

$table_remarks_sign = new easyTable($pdf, '{100,100,100');

$table_remarks_sign->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_remarks_sign->easyCell('');

$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C;border:1;font-color:black');
$table_remarks_sign->easyCell('Received By:', 'font-style:B; align:C; ;border:1;font-color:black');
$table_remarks_sign->printRow();


$table_remarks_sign->easyCell('Signature:', 'font-size:9; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('_______________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('_______________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Name:', 'font-size:9; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('_______________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('_______________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Date:', 'font-size:9; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('_______________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('_______________________', 'align:C; valign:B;border:L');
$table_remarks_sign->printRow();


$table_remarks_sign->endTable(5);


/**
 * ITEM LIST
 */

$table_item_list = new easyTable($pdf, '{35,100,40,40,40}');

$table_item_list->rowStyle('font-size:9;border:1;border-color:#2d3238; font-color:black;valign:M');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('MATERIAL DESCRIPTION', 'font-style:B; align:C');
$table_item_list->easyCell('BATCH CODE', 'font-style:B; align:C');
$table_item_list->easyCell('QTY (PCS)', 'font-style:B; align:C');
$table_item_list->easyCell('EXPIRY DATE', 'font-style:B; align:C');



$table_item_list->printRow();


$i = 1;
foreach ($db_picklist  as $arr_key => $arr_item_det) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#fff;';
  }

  $table_item_list->rowStyle('border:LRBT;font-size:9; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);

  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['item_description']);
  $table_item_list->easyCell($arr_item_det['batch_no']);
  $table_item_list->easyCell($arr_item_det['qty_pcs']);
  $table_item_list->easyCell($arr_item_det['expiry']);


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


$tableD = new easyTable($pdf, '{100,60,20,20,10,10,25,5,20}', 'split-row:true');

$tableD->rowStyle('font-size:7; split-row:false');
$tableD->easyCell('', 'colspan:8; paddingY:2');
$tableD->printRow();

$tableD->rowStyle('font-size:9');
$tableD->easyCell('REMARKS', 'font-style:B;colspan:5; bgcolor:#ffffff; border:1; border-color:black; font-color:black;');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:8">TOTAL QTY</s>', 'align:L;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:9; border:TLB; border-color:black');
$tableD->easyCell($total, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();



$tableD->easyCell('', 'font-style:B;colspan:5; border:LRB; border-color:#2d3238; rowspan:5');
$tableD->easyCell('');
$tableD->easyCell('<s "font-size:9"><b>TOTAL SKU</b></s>', 'align:L;');
$tableD->easyCell('<b><s "align:L"></s></b>', 'font-size:9; border:TLB; border-color:black');
$tableD->easyCell($aux_count, 'align:R; font-size:8;border: TRB;  border-color:black');
$tableD->printRow();

$tableD->rowStyle('paddingY:2');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();

$tableD->rowStyle('paddingY:2');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();

$tableD->rowStyle('paddingY:2');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();

$tableD->rowStyle('paddingY:2');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->easyCell('');
$tableD->printRow();
$tableD->endTable(5);

//  $num_rows = count($db_inbound);
//  $maxRow = 12;
//  $initRow=0;
//  $i=1;
//  if($num_rows >= $maxRow){

//    if($num_rows >= 15 && $num_rows <=20){

//       $addRow = 8;

//       foreach($db_inbound as $arr_key =>$arr_item_det){
//          $bgcolor='';
//          if($i%2){
//             $bgcolor='bgcolor:#ebeef2;';
//          }

//          $table_item_list->rowStyle('border:LRB;font-size:9; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;'.$bgcolor);

//           $table_item_list->easyCell($arr_item_det['item_code']);
//           $table_item_list->easyCell($arr_item_det['material_description']);
//           $table_item_list->easyCell($arr_item_det['batch_code']);
//           $table_item_list->easyCell($arr_item_det['pack_size']);
//           $table_item_list->easyCell($arr_item_det['qty']);
//           $table_item_list->easyCell($arr_item_det['boxes']);
//           $table_item_list->easyCell($arr_item_det['expiry_date']);
//           $table_item_list->easyCell($arr_item_det['production_date']);
//          $table_item_list->printRow();
//          $i++;  
//      }

//       /**
//        * Nothing Follows
//        */
//        $table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
//        $table_item_list->easyCell('*** Nothing Follows ***','colspan:8');
//        $table_item_list->printRow();

//       while($addRow!=0){

//          if($i%2){
//                $bgcolor='bgcolor:#afb5bf;';
//          }
//             $table_item_list->rowStyle('border:LRB;font-size:9; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->printRow();
//             $i++;
//             $addRow--;
//       }

//    }else{

//       foreach($db_inbound as $arr_key =>$arr_item_det){
//          $bgcolor='';
//             if($i%2){
//                $bgcolor='bgcolor:#ebeef2;';
//             }

//             $table_item_list->rowStyle('border:LRB;font-size:9; align:C{CCCCCCCCC}; paddingY:3; valign:M ; split-row:true;'.$bgcolor);
//             $table_item_list->easyCell($arr_item_det['item_code']);
//             $table_item_list->easyCell($arr_item_det['material_description']);
//             $table_item_list->easyCell($arr_item_det['batch_code']);
//             $table_item_list->easyCell($arr_item_det['pack_size']);
//             $table_item_list->easyCell($arr_item_det['qty']);
//             $table_item_list->easyCell($arr_item_det['boxes']);
//             $table_item_list->easyCell($arr_item_det['expiry_date']);
//             $table_item_list->easyCell($arr_item_det['production_date']);
//          $table_item_list->printRow();

//             $i++;
//             $maxRow--;
//        }

//        /**
//        * Nothing Follows
//        */
//       $table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
//       $table_item_list->easyCell('*** Nothing Follows ***','colspan:8');
//       $table_item_list->printRow();

//    }

//  }else{

//       foreach($db_inbound as $arr_key =>$arr_item_det){
//          $bgcolor='';
//          if($i%2){
//             $bgcolor='bgcolor:#ebeef2;';
//          }

//          $table_item_list->rowStyle('border:LRB;font-size:9; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;'.$bgcolor);
//          $table_item_list->easyCell($arr_item_det['item_code']);
//           $table_item_list->easyCell($arr_item_det['material_description']);
//           $table_item_list->easyCell($arr_item_det['batch_code']);
//           $table_item_list->easyCell($arr_item_det['pack_size']);
//           $table_item_list->easyCell($arr_item_det['qty']);
//           $table_item_list->easyCell($arr_item_det['boxes']);
//           $table_item_list->easyCell($arr_item_det['expiry_date']);
//           $table_item_list->easyCell($arr_item_det['production_date']);
//          $table_item_list->printRow();
//          $i++;
//          $maxRow--;

//      }

//       /**
//        * Nothing Follows
//        */
//       $table_item_list->rowStyle('font-size:9; align:C{CCCCCCCC}; paddingY:3; split-row:true');
//       $table_item_list->easyCell('*** Nothing Follows ***','colspan:8');
//       $table_item_list->printRow();

//       while($maxRow!=0){

//          if($i%2){
//                $bgcolor='bgcolor:#afb5bf;';
//          }
//             $table_item_list->rowStyle('font-size:9; align:C{CCCCCCCCC}; paddingY:3; font-color:#ffffff;valign:M; split-row:true');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->easyCell('-');
//             $table_item_list->printRow();
//             $i++;
//             $maxRow--;
//       }
//    }

// $table_item_list->endTable(2);

/**
 * Output With file name
 */


$pdf->Output('', "DR-" . $_GET['ref_no'] . "-" . $document_no . ".pdf"); //To Print and to indicate the filename



?>