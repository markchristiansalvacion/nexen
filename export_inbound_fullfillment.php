
<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");

if (isset($_POST["export"])) {

  $file_name = 'Inbound Fullfillment Report.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Source", "Document No", "Item Code", "Batch No", "Item Name", "QTY(PCS)",  "Date Fullfilled", "Fullfilled By");

  fputcsv($file, $header);

  $query = "SELECT a.document_no,a.item_code,a.batch_no,a.qty_pcs,a.lpn,a.date_time,a.fullfilled_by,c.vendor_name,b.vendor_code,d.material_description
                    FROM tb_fullfillment a
                     INNER JOIN tb_inbound b On b.document_no = a.document_no
                     INNER JOIN tb_vendor c On c.vendor_id = b.vendor_code
                     INNER JOIN tb_items d ON d.item_code = a.item_code
                     WHERE a.document_no =   '" . $_POST['doc'] . "'   GROUP BY a.batch_no    
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["vendor_name"];
    $data[] = $row["document_no"];
    $data[] = $row["item_code"];
    $data[] = $row["batch_no"];
    $data[] = $row["qty_pcs"];
    $data[] = $row["date_time"];
    $data[] = $row["fullfilled_by"];
    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}
