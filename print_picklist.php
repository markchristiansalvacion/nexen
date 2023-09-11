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
  if(!isset($_GET)){
    /**ERROR */
      $_SESSION['msg_heading'] = "Transaction Failed!";
      $_SESSION['msg'] = "Invalid Transaction ID!";
      $_SESSION['msg_type'] = "error";
      redirect("outbound_incoming_dispatch");
  }else{

    $update_to_status = $db->query('UPDATE tb_transfer_order SET status = ? WHERE so_no = ? AND status <> ?','Picking',$_GET['so_no'],"Cancelled");

    if(!$update_to_status->affected_rows()){
      /**ERROR */
      $_SESSION['msg_heading'] = "Printing Failed!";
      $_SESSION['msg'] = "Picklist Printing Can Only Be Done Once!";
      $_SESSION['msg_type'] = "error";
      redirect("outbound_incoming_dispatch");
    }else{

      $get_picklist_details = $db->query('SELECT 
      a.id AS picklist_id,
      a.ref_no,
      a.so_no,
      a.to_id,
      a.allocated_lpn,
      a.allocated_sku_code,
      tb_items.material_description,
      a.allocated_qty,
      a.allocated_expiry,
      a.bin_loc,
      tb_transfer_order.so_date,
      tb_transfer_order.rdd,
      tb_transfer_order.delivering_plant,
      tb_transfer_order.ship_to_code,
      tb_warehouse.warehouse_name,
      tb_warehouse.warehouse_address,
      tb_customer.ship_to_name,
      tb_customer.ship_to_address
      FROM tb_picklist a
      LEFT JOIN tb_items ON tb_items.sap_code = a.allocated_sku_code
      LEFT JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
      LEFT JOIN tb_customer ON tb_transfer_order.ship_to_code = tb_customer.ship_to_code
      LEFT JOIN tb_warehouse ON tb_transfer_order.delivering_plant = tb_warehouse.warehouse_id
      WHERE a.so_no = ?',$_GET['so_no'])->fetch_all();

        $ref_no = "";
        $so_no = "";
        $so_date = "";
        $rdd = "";
        $delivering_plant = "";
        $warehouse_name = "";
        $warehouse_address = "";
        $ship_to_code = "";
        $ship_to_name = "";
        $ship_to_address = "";


        foreach($get_picklist_details as $aux_key => $aux_val){
          $ref_no = $aux_val['ref_no'];
          $so_no = $aux_val['so_no'];
          $so_date = $aux_val['so_date'];
          $rdd = $aux_val['rdd'];
          $delivering_plant = $aux_val['delivering_plant'];
          $warehouse_name = $aux_val['warehouse_name'];
          $warehouse_address = $aux_val['warehouse_address'];
          $ship_to_code = $aux_val['ship_to_code'];
          $ship_to_name = $aux_val['ship_to_name'];
          $ship_to_address = $aux_val['ship_to_address'];
        }
      
      // print_r_html($get_picklist_details);

      $generator = new Picqer\Barcode\BarcodeGeneratorPNG();


      file_put_contents('picklist_barcode_images/to_db_id-' . $_GET['so_no'] . '.png', $generator->getBarcode($_GET['so_no'], $generator::TYPE_CODE_128, 10, 100));

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
      $tb_header->easyCell('Picklist', 'font-size:15; font-style:B; align:C; valign:M');
      $tb_header->printRow();
      $tb_header->endTable(2);  

      $tb_details = new easyTable($pdf, '{60,2,37,37,37,37}');
      $tb_details->rowStyle('border:T;font-size:8; font-style:B');
      $tb_details->easyCell('', 'img:picklist_barcode_images/to_db_id-'.$_GET['so_no'].'.png, w40, h10; align:C; valign:T;rowspan:2');
      $tb_details->easyCell('');
      $tb_details->easyCell('REF No:','valign:B');
      $tb_details->easyCell('SO Date:','valign:B');
      $tb_details->easyCell('SO No:','valign:B');
      $tb_details->easyCell('RDD:','valign:B');
      $tb_details->printRow();
 

      $tb_details->rowStyle('border:0;font-size:7');
      $tb_details->easyCell('','valign:T');
      $tb_details->easyCell('PL-'.$ref_no,'valign:T');
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
      $tb_details_2->easyCell('2. The Zone Keeper shall endorse this to the Forklift Operator ','valign:M');
      $tb_details_2->printRow();

      $tb_details_2->rowStyle('font-size:7');
      $tb_details_2->easyCell($ship_to_code.'-'.$ship_to_name,'valign:M');
      $tb_details_2->easyCell('3. Sweep the item to the Staging Area. Compare the Picklist Data vs Actual','valign:M');
      $tb_details_2->printRow();

      $tb_details_2->rowStyle('font-size:7');
      $tb_details_2->easyCell($ship_to_address,'valign:M; border:B');
      $tb_details_2->easyCell('4. Return this document to the Outbound Team for Validation.','valign:M; border:B');
      $tb_details_2->printRow();
      $tb_details_2->endTable();

      /**
       * Remarks And Signatory
       */

       $table_remarks_sign = new easyTable($pdf, '{30,60,60,60}');

       $table_remarks_sign->rowStyle('font-size:8;valign:M;font-style:B;');
       $table_remarks_sign->easyCell('');
       $table_remarks_sign->easyCell('Prepared By:');
       $table_remarks_sign->easyCell('Picked By:');
       $table_remarks_sign->easyCell('Validated By:');
       $table_remarks_sign->printRow();


       $table_remarks_sign->rowStyle('font-size:8;valign:M;paddingY:3');
       $table_remarks_sign->easyCell('Signature:');
       $table_remarks_sign->easyCell('______________________________');
       $table_remarks_sign->easyCell('______________________________');
       $table_remarks_sign->easyCell('______________________________');
       $table_remarks_sign->printRow();

       
       $table_remarks_sign->rowStyle('font-size:8;valign:M;paddingY:3;align:C');
       $table_remarks_sign->easyCell('Name:');
       $table_remarks_sign->easyCell($_SESSION['name']);
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

      $tb_body = new easyTable($pdf , '{25,60,10,25,25,25,40}');
      $tb_body->rowStyle('font-size:8; font-style:B; paddingY:2; border: TB');
      $tb_body->easyCell('Item Code','valign:T; align:C');
      $tb_body->easyCell('Material Description','valign:T;align:C');
      $tb_body->easyCell('UoM','valign:T;align:C');
      $tb_body->easyCell('Qty','valign:T;align:C');
      $tb_body->easyCell('BBD','valign:T; align:C');
      $tb_body->easyCell('Location','valign:T; align:C');
      $tb_body->easyCell('LPN','valign:T; align:C');
      $tb_body->printRow();
      
      $total_cases = 0;

      foreach($get_picklist_details as $asar_key => $asar_val){
        $tb_body->rowStyle('font-size:8; paddingY:2');
        $tb_body->easyCell($asar_val['allocated_sku_code'],'valign:M; align:C');
        $tb_body->easyCell($asar_val['material_description'],'valign:M;align:C');
        $tb_body->easyCell('Case','valign:M; align:C');
        $tb_body->easyCell(number_format($asar_val['allocated_qty'],2,'.'),'valign:M;align:C');
        $tb_body->easyCell($asar_val['allocated_expiry'],'valign:M; align:C;');
        $tb_body->easyCell($asar_val['bin_loc'],'valign:M; align:C');
        $tb_body->easyCell($asar_val['allocated_lpn'],'valign:M; align:C');
        $tb_body->printRow();

        $total_cases = $total_cases + $asar_val['allocated_qty'];
      }

        $tb_body->rowStyle('font-size:8; paddingY:2');
        $tb_body->easyCell("");
        $tb_body->easyCell("");
        $tb_body->easyCell("");
        $tb_body->easyCell("");
        $tb_body->easyCell("");
        $tb_body->easyCell("Total Cases:",'align:R');
        $tb_body->easyCell(number_format($total_cases,2,'.'),'valign:M;align:C');
        $tb_body->printRow();

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
      


    }
  
  
    }
  

   



?>