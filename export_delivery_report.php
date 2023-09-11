
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

  $header = array("Ref No", "Destination", "Document No", "Branch Received Date",  "Received By", "IR Ref No", "IR Remarks", "RR Ref No", "Truck Arrival From Delivery", "Branch In", "Branch Out", "FDS", "Window Complaince", "In Full");

  fputcsv($file, $header);

  $query = " SELECT 
                    a.source_ref, 
                    a.document_no, 
                    a.branch_received_date, 
                    a.received_by,
                    a.ir_ref_no, 
                    a.ir_remarks, 
                    a.rr_ref_no,
                    a.truck_arrival,
                    a.branch_in,
                    a.branch_out,
                    a.fds_comp,
                    a.window_comp,
                    a.in_full,
                    a.created_by,
                    c.branch_name
                     FROM tb_transport a
                     INNER JOIN tb_outbound b ON b.document_no = a.document_no
                     INNER JOIN tb_branches c ON c.branch_code = b.destination_code WHERE b.document_name= '" . $_POST['doc'] . "' GROUP BY document_no     
  ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    $data = array();
    $data[] = $row["source_ref"];
    $data[] = $row["branch_name"];
    $data[] = $row["document_no"];
    $data[] = $row["branch_received_date"];
    $data[] = $row["received_by"];
    $data[] = $row["ir_ref_no"];
    $data[] = $row["ir_remarks"];
    $data[] = $row["rr_ref_no"];
    $data[] = $row["truck_arrival"];
    $data[] = $row["branch_in"];
    $data[] = $row["branch_out"];
    $data[] = $row["fds_comp"];
    $data[] = $row["window_comp"];
    $data[] = $row["in_full"];


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
