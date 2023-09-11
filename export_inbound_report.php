
<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");
if (isset($_POST["export"])) {
  // $item_code = ;
  $file_name = 'Inbound Summary Report.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');
  $header = array("Item Code", "Batch No", "Item Name", "QTY(PCS)",  "Expiry", "Location", "Date Received");

  fputcsv($file, $header);

  $query = " SELECT
                    tb_inbound.item_code,
                    tb_inbound.batch_no,
                      tb_items.material_description,
                    tb_inbound.qty_pcs,
                    tb_inbound.expiry,
                    tb_inbound.bin_location,
                    tb_inbound.date_created
                     FROM tb_inbound
                        INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                        WHERE tb_inbound.date_created BETWEEN '" . $_POST['start_date'] . "' AND '" . $_POST['end_date'] . "' GROUP BY batch_no
                        ORDER BY ref_no                
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["item_code"];
    $data[] = $row["batch_no"];
    $data[] = $row["material_description"];
    $data[] = $row["qty_pcs"];
    $data[] = $row["expiry"];

    $data[] = $row["bin_location"];
    $data[] = $row["date_created"];

    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}


// $query = "
// SELECT * FROM tb_inbound 
// ORDER BY item_code DESC;
// ";

// $statement = $connect->prepare($query);
// $statement->execute();
// $result = $statement->fetchAll();
