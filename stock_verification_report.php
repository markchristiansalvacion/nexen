<?php
require_once 'includes/load.php';

if (is_login_auth()) {

    /** SESSION BASE TO TIME TODAY */
  
    if (is_session_expired()) {
      $_SESSION['msg'] = "<b>SESSION EXPIRED:</b> Please Login Again.";
      $_SESSION['msg_type'] = "danger";
  
      unset($_SESSION['user_id']);
      unset($_SESSION['name']);
      unset($_SESSION['user_type']);
      unset($_SESSION['user_status']);
  
      unset($_SESSION['login_time']);
  
      /**TIME TO DAY + 315360000 THAT EQUIVALENT TO 10 YEARS*/
  
      redirect("login", false);
    }
} else {
redirect("login", false);
}

        $stock_allocation = array();

        // $transfer_order = $db->query('SELECT sku_code,SUM(req_qty_case) as required_qty FROM tb_transfer_order WHERE status = ? GROUP BY sku_code',"Allocation")->fetch_all();

        $transfer_order = $db->query('SELECT
       a.ship_to_code,
       a.sku_code,
       a.rdd,
       tb_items.sap_code AS item_master_code,
       tb_items.material_description,
       tb_customer.ship_to_code AS customer_master_code,
       tb_customer.ship_to_name,
       tb_customer.req_shelf_life,
       SUM(a.req_qty_case) AS order_qty
       FROM tb_transfer_order a
       LEFT OUTER JOIN tb_items ON tb_items.sap_code = a.sku_code
       LEFT OUTER JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
       WHERE a.status = ?
       GROUP BY a.rdd,tb_customer.req_shelf_life,a.sku_code,customer_master_code
       ORDER BY a.rdd DESC',"Allocation")->fetch_all();

     

      foreach($transfer_order as $asar_key => $asar_val){

        // if($asar_val['req_shelf_life'] <= .5){

        // }

        // if($asar_val['req_shelf_life'] > .5 AND $asar_val['req_shelf_life'] > .5 ){

        // }

        
     
        if(are_strings_equal($asar_val['item_master_code'],EmptyString)){
          /** ERROR */
          $transfer_order[$asar_key]['available_qty'] = 0;
         
        }else{
          $get_total_available_stocks = $db->query('SELECT
          a.sku_code,
          tb_items.material_description,
          SUM(a.qty_case - a.allocated_qty) AS available_qty
          FROM tb_inventory_adjustment a 
          INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
          WHERE DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) >= ?
          AND a.transaction_type = ?
          AND a.qty_case - a.allocated_qty <> 0
          AND a.sku_code = ?
          AND a.putaway_status <> ?
          AND a.lpn_status = ?',$asar_val['req_shelf_life'],"INB",$asar_val['item_master_code'],"Pending","Active")->fetch_array();

         if(is_array_has_empty_input($get_total_available_stocks)){
          $transfer_order[$asar_key]['available_qty'] = 0;
         }else{
          $transfer_order[$asar_key]['available_qty'] = $get_total_available_stocks['available_qty'];
         }
        }
     }

if(!empty($transfer_order)){

    $file_name = "Stock Verification Report ".date('Y-M-d').".csv";
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Type: text/csv;");
  
    $file = fopen('php://output', 'w');
  
    $header = array("Ship_to_code","Ship To","RDD","Mat. #","Material Description","Req Shelf Life", "Total Order Qty", "Available Qty", "Remarks");
    fputcsv($file, $header);

    foreach ($transfer_order as $asar_key => $row) {
        $data = array();

        $data[] = $row['customer_master_code'];
        $data[] = $row['ship_to_name'];
        $data[] = $row['rdd'];
        $data[] = $row["sku_code"];

        if(is_null($row['item_master_code'])){
          $data[] = "Item Not Registered";
        }else{
          $data[] = $row["material_description"];
        }
        
        $data[] = $row["req_shelf_life"];
        $data[] = number_format($row["order_qty"]);
        $data[] = number_format($row["available_qty"]);
        
        if(is_null($row['req_shelf_life']) || is_null($row['item_master_code'])){
          $data[] = "Inaccurate Calculation. Update the Masterfile";
        }else{
          if($row['available_qty'] > 0){
            if ($row['available_qty'] < $row['order_qty']) {
              $data[] = "Shortage";
            } else {
              $data[] = "In Full";
            }
          }else{
            $data[] = "Out of Stock";
          }
        }
      
    
        fputcsv($file, $data);
      }
      fclose($file);
      exit;

}else{
  $_SESSION['msg_heading'] = "Download Failed!";
  $_SESSION['msg'] = "No Available Data for Stocks Verification.";
  $_SESSION['msg_type'] = "warning";
  redirect("index");
}





?>