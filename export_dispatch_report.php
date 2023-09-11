
<?php

$connect = new PDO("mysql:host=localhost;dbname=u478425112_new_wms", "u478425112_new_wms", "Newsys2022");
//$connect = new PDO("mysql:host=localhost;dbname=siri_mdc_db2", "root", "");
if (isset($_POST["export"])) {
  // $item_code = ;
  $file_name = 'Delivery Monitoring Report.csv';
  header("Content-Description: File Transfer");
  header("Content-Disposition: attachment; filename=$file_name");
  header("Content-Type: text/csv;");

  $file = fopen('php://output', 'w');

  $header = array("Ref No", "Destination", "Document No", "Truck_type",  "Trucker", "Actual Dispatched", "Call Time", "Truck Arrival", "Truck Departed Time", "Loading Start", "Loading End");

  fputcsv($file, $header);

  $query = " SELECT
                    a.ref_no,
                    a.document_no,
                    a.driver,
                    a.helper,
                    a.plate_no,
                    a.truck_type,
                    a.trucker,
                    a.call_time,
                    a.arrival_time,
                    a.departed_time,
                    a.actual_dispatch,
                     a.loading_start,
                     a.loading_end,
                     
                      b.branch_name
                      FROM tb_outbound a 
                      INNER JOIN tb_branches b ON b.branch_code = a.destination_code
                      WHERE a.document_name= '" . $_POST['doc'] . "' GROUP BY document_no     
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["ref_no"];
    $data[] = $row["branch_name"];
    $data[] = $row["document_no"];
    $data[] = $row["truck_type"];
    $data[] = $row["trucker"];
    $data[] = $row["call_time"];
    $data[] = $row["actual_dispatch"];
    $data[] = $row["arrival_time"];
    $data[] = $row["departed_time"];
    $data[] = $row["loading_start"];
    $data[] = $row["loading_end"];



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
