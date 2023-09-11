<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  $url = $_POST['url'];
  $id = $_POST['db_id'];
  $aisle = $db->escape_string($_POST['aisle']);
  $i_code = $db->escape_string($_POST['i_code']);
  $cat = $db->escape_string($_POST['cat']);
  $loc = $db->escape_string($_POST['loc']);
  $whse = $db->escape_string($_POST['whse']);
  $layer = $db->escape_string($_POST['layer']);
  $l_type = $db->escape_string($_POST['l_type']);

  $update_to_db = $db->query('UPDATE tb_bin_location_bac SET aisle = ?,item_code=?, category= ?,location_code=?,warehouse=?,layer=? WHERE id=?', $aisle, $i_code, $cat, $loc, $whse, $layer, $id,);

  if ($update_to_db->affected_rows()) {

    $_SESSION['msg_heading'] = "Transaction Successfully Added!";
    $_SESSION['msg'] = "Location Detials updated!";
    $_SESSION['msg_type'] = "success";
    redirect($url, false);
  }
} else {

  $_SESSION['msg_heading'] = "Transaction FAILED Added!";
  $_SESSION['msg'] = "Location Detials failed to update!";
  $_SESSION['msg_type'] = "error";
  redirect($url, false);
}
