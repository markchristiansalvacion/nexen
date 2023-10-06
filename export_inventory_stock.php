<?php

require_once 'includes/load.php';



if (isset($_GET["export_dir"])) {

      // print_r_html($_POST);
      $date_today = date('Y-m-d');

      $db_inventory_report = $db->query('SELECT
      a.sku_code,
      tb_items.material_description,
      SUM(a.qty_case) AS SOH,
      a.expiry AS exp_date
      FROM tb_inventory_adjustment a 
      INNER JOIN tb_items ON tb_items.sku_code = a.sku_code
      GROUP BY exp_date,a.sku_code
      ORDER BY SOH ASC')->fetch_all();

      if(!empty($db_inventory_report)){
        $file_name = 'Daily Inventory Report '.$date_today.'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Type: text/csv;");
      
        $file = fopen('php://output', 'w');
      
        $header = array("SKU Code",  "Material Description",  "SOH", "Serial");
      
        fputcsv($file, $header);
      
      
        foreach ($db_inventory_report as $asar_key => $asar_det) {
          $data = array();
      
          $data[] = $asar_det["sku_code"];
          $data[] = $asar_det["material_description"];
          $data[] = number_format($asar_det["SOH"]);
          $data[] = $asar_det["exp_date"];
          fputcsv($file, $data);
        }
    
        fclose($file);
        exit;

      }else{

        $_SESSION['msg_heading'] = "Download Failed!";
        $_SESSION['msg'] = "No Available Data for SAP Integration File (F2).";
        $_SESSION['msg_type'] = "warning";
        redirect("index");

      }
  
}else{
  $_SESSION['msg_heading'] = "Transaction Error!";
  $_SESSION['msg'] = "Unauthorized Access!";
  $_SESSION['msg_type'] = "error";
  redirect("index");

}
