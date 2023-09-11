<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");
if (isset($_POST["export"])) {
  // $item_code = ;
  $file_name = 'Outbound Summary Report.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Document No", "Item Code", "Item Name", "Batch No", "QTY(PCS)", "Expiry", "Bin 
  Location", "Dispatch Date");

  fputcsv($file, $header);

  $query = " SELECT a.document_no,a.item_code,a.item_description,a.bin_loc,a.batch_no,a.qty_pcs,a.expiry,b.ship_date
                    FROM tb_picklist a
                    INNER JOIN tb_outbound b on b.document_no = a.document_no 
                    WHERE b.ship_date BETWEEN '" . $_POST['start_date'] . "' AND '" . $_POST['end_date'] . "'  GROUP BY batch_no
                  
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();

    $data[] = $row["document_no"];
    $data[] = $row["item_code"];

    $data[] = $row["item_description"];
    $data[] = $row["batch_no"];
    $data[] = $row["qty_pcs"];
    $data[] = $row["expiry"];
    $data[] = $row["bin_loc"];
    $data[] = $row["ship_date"];

    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}
