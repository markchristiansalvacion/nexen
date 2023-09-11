
<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");

if (isset($_POST["export"])) {

  $file_name = 'Outbound Fullfillment Report.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");
  $file = fopen('php://output', 'w');
  $header = array("Ref No", "Document No", "Branch", "Item Code",  "Item Name", "Batch No", "QTY(PCS)",  "Status", "Date Fullfilled", "Fullfilled By");
  fputcsv($file, $header);
  $query = "SELECT a.ref_no,a.document_no,a.item_code,a.batch_no,a.qty_pcs,a.lpn,a.date_time,a.fullfilled_by,b.ship_date,c.branch_name,d.material_description,e.status
                    FROM tb_fullfillment a
                    INNER JOIN tb_outbound b ON b.document_no = a.document_no
                    INNER JOIN tb_branches c ON c.branch_code = b.destination_code
                    INNER JOIN tb_items d ON d.item_code = a.item_code
                    INNER JOIN tb_picklist e On e.document_no = a.document_no
                     WHERE a.transaction_type='Outbound' AND b.document_name=   '" . $_POST['doc'] . "' GROUP BY a.batch_no   
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["ref_no"];
    $data[] = $row["document_no"];
    $data[] = $row["branch_name"];
    $data[] = $row["item_code"];
    $data[] = $row["material_description"];
    $data[] = $row["batch_no"];
    $data[] = $row["qty_pcs"];

    $data[] = $row["status"];
    $data[] = $row["date_time"];
    $data[] = $row["fullfilled_by"];
    fputcsv($file, $data);
  }
  fclose($file);
  exit;
}
