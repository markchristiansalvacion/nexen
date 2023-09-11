<?php

require_once 'includes/load.php';


if (isset($_POST)) {
  $date_now = date('d-M-Y');
  $sku_code = $_GET['sku_code'];
  // $item_code = $_POST['item_batch'];

  $db_sku_loc = get_sku_location($db, $sku_code);
  /** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */
  $bin_count = count($db_sku_loc);


//   print_r_html($db_sku_loc);

  $file_name = 'IRIS Per SKU Cycle Count'.$date_now.'.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Bin",  "Material Desc.",  "LPN",  "BBD", "Qty (Case)",);

  fputcsv($file, $header);

  $data [] = array();
  foreach ($db_sku_loc as $aisle_det => $row) {
    
    $data = array($row['actual_bin_loc'],$row['sku_code'].'-'.$row['material_description'],$row['lpn'],$row['expiry'],$row['available_qty']);
   
    fputcsv($file, $data); 
  }

  fclose($file);
  exit;
}
