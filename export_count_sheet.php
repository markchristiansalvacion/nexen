<?php

require_once 'includes/load.php';


if (isset($_POST)) {
  $date_now = date('d-M-Y');
  $bin_loc = $_GET['aisle'];
  $aisle_details = get_bet_bin_loc($db, $bin_loc);

  /** Before Printing Ensure ALL INFO ARE OK - FOR FUTURE UPDATE */
  $bin_count = count($aisle_details);



  //print_r_html($aisle_details);

  $file_name = 'IRIS Cycle Count '.$date_now.'.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Bin",  "Material Desc.",  "LPN",  "BBD", "Qty (Case)",);

  fputcsv($file, $header);

  foreach ($aisle_details as $aisle_det => $aisle_val) {
    // print_r_html(($aisle_val));
    // if(array_key_exists('bin_location',$aisle_val)){
    //   $data = array($aisle_val['bin_location'], '', '', '', '', '', '');
    //   fputcsv($file, $data);
    // }
    $data = array();
    if (array_key_exists('actual_bin_loc', $aisle_val)) {
      $data = array($aisle_val['actual_bin_loc'],  $aisle_val['sku_code'].'-'.$aisle_val['material_description'], $aisle_val['lpn'], $aisle_val['expiry'], $aisle_val['qty_case'] - $aisle_val['allocated_qty']);
      fputcsv($file, $data);
    } else {
      $data = array($aisle_val['bin_location'], '', '', '', '');
      fputcsv($file, $data);
    }
  }

  fclose($file);
  exit;
}
