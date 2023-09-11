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
$date_today = date('Y-m-d');
$db_replen = $db->query('SELECT  
    a.id,
       a.in_id,
       a.item_code,
       a.source_loc,
        a.new_loc,
        a.replen_date,
       b.bin_location
        FROM tb_replenishment a
        INNER JOIN tb_inbound b ON b.id = a.in_id
        INNER JOIN tb_bin_location_bac c ON c.location_code =  b.bin_location
        WHERE a.id = ? AND a.item_code = ? AND a.source_loc=? AND c.location_type=?', $_GET['id'], $_GET['item_code'], $_GET['source_loc'], 'Pickface')->fetch_all();
//print_r_html($db_replen);
//$date_today = date('m-d-Y');

foreach ($db_replen as $db_key => $db_val) {
  $in_id = $db_val['in_id'];
  $item_code = $db_val['item_code'];
  $source_loc = $db_val['source_loc'];
  $date = $db_val['replen_date'];
  $new_location = $db_val['new_loc'];
}



//print_r_html($db_inbound);

/** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */

/**
 * Header
 */

$pdf = new exFPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('arial', '', 10);


$tb_header = new easyTable($pdf, 2);
$tb_header->easyCell('REPLENISHMENT FORM ', 'font-size:18; font-style:B; align:L; valign:M');
$tb_header->easyCell('', 'img:img/Logo Arrowgo.png, w50; align:R;');
$tb_header->printRow();
$tb_header->endTable(2);



$table = new easyTable($pdf, '{190,500,200}');
$table->easyCell('<b>REPLENSHIMENT REF. NO:</b>', 'font-size:9; align:L; valign:M');
$table->easyCell("RPLNH - 00000" . $in_id, 'border-color:#afb5bf; font-style:B; valign:M; align:L');
$table->printRow();
$table->endTable(5);


$delivery_details = new easyTable($pdf, '{190,500,200,190}');

$delivery_details->rowStyle('font-size:9; valign:M;');
$delivery_details->easyCell('<b>RT Unit:</b>', 'font-size:10');
$delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:L');
$delivery_details->easyCell('<b>Operator Name:</b>', 'font-size:9; align:R');
$delivery_details->easyCell('', 'border-color:#afb5bf; font-size:9; align:C');
$delivery_details->printRow();

$delivery_details->rowStyle('font-size:9; valign:M;');
$delivery_details->easyCell('<b>DATE AND TIME OF REPLENISHMENT:</b>', 'font-size:9');
$delivery_details->easyCell($date, 'border-color:#afb5bf; font-size:9; align:L');
$delivery_details->easyCell('<b>PRINTED DATE:</b>', 'font-size:9; align:R');
$delivery_details->easyCell($date_today, 'border-color:#afb5bf; font-size:9; align:C');
$delivery_details->printRow();

$delivery_details->rowStyle('font-size:9; valign:M');
$delivery_details->easyCell('<b>PRINTED BY:</b>', 'font-size:9');
$delivery_details->easyCell($_SESSION['name'], 'border-color:#afb5bf; align:L; font-size:9');
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

$table_remarks_sign = new easyTable($pdf, '{110,110,110,120');

$table_remarks_sign->rowStyle('font-size:9;border-color:#2d3238;valign:M');
$table_remarks_sign->easyCell('');
$table_remarks_sign->easyCell('Prepared By:', 'font-style:B; align:C;;border:1;font-color:black');
$table_remarks_sign->easyCell('Confirmed By:', 'font-style:B; align:C;border:1;font-color:black');
$table_remarks_sign->easyCell('Received By:', 'font-style:B; align:C; ;border:1;font-color:black');
$table_remarks_sign->printRow();


$table_remarks_sign->easyCell('Signature:', 'font-size:9; align:R; valign:B; paddingX:2;paddingY:5');
$table_remarks_sign->easyCell('____________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('____________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('____________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Name:', 'font-size:9; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('_____________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('_____________________', 'align:C; valign:B;border:LR');
$table_remarks_sign->easyCell('_____________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Signature:', 'font-size:9; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('______________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('______________________', 'align:C; valign:B;border:LR');
$table_remarks_sign->easyCell('______________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();

$table_remarks_sign->easyCell('Name:', 'font-size:9; align:R; valign:B;paddingX:2;paddingY:3');
$table_remarks_sign->easyCell('______________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('______________________', 'align:C; valign:B;');
$table_remarks_sign->easyCell('______________________', 'align:C; valign:B;');
$table_remarks_sign->printRow();
$table_remarks_sign->endTable(5);


/**
 * ITEM LIST
 */

$table_item_list = new easyTable($pdf, '{100,100,100}');

$table_item_list->rowStyle('font-size:9;border:1;border-color:#2d3238; font-color:black;valign:M');
$table_item_list->easyCell('ITEM CODE', 'font-style:B; align:C');
$table_item_list->easyCell('SOURCE LOCATION', 'font-style:B; align:C');
$table_item_list->easyCell('NEW LOCATION', 'font-style:B; align:C');

$table_item_list->printRow();


$i = 1;
foreach ($db_replen as $arr_key => $arr_item_det) {
  $bgcolor = '';
  if ($i % 2) {
    $bgcolor = 'bgcolor:#fff;';
  }

  $table_item_list->rowStyle('border:LRBT;font-size:9; align:C{CCCCCCCCC}; paddingY:3; valign:M; split-row:true;' . $bgcolor);

  $table_item_list->easyCell($arr_item_det['item_code']);
  $table_item_list->easyCell($arr_item_det['source_loc']);
  $table_item_list->easyCell($arr_item_det['bin_location']);
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


$pdf->Output('', "REPLENISHMENT -" . $_GET['id'] . ".pdf"); //To Print and to indicate the filename



?>