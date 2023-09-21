<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';

if (isset($_POST)) {
  // print_r_html($_POST);
  if (
    empty(trim($_POST['item_code'])) || empty(trim($_POST['item_name'])) || empty(trim($_POST['item_category']))  || empty(trim($_POST['unit'])) || empty(trim($_POST['manufacturer'])) || empty(trim($_POST['dot'])) || empty(trim($_POST['tire_brand'])) || empty(trim($_POST['tire_size'])) || empty(trim($_POST['tire_design'])) || empty(trim($_POST['rim_size']) || empty(trim($_POST['load_index'])) || empty(trim($_POST['load_index'])) || empty(trim($_POST['speed_rating'])) || empty(trim($_POST['ply_rating'])) || empty(trim($_POST['origin'])) || empty(trim($_POST['regulation'])) || empty(trim($_POST['cif'])) || empty(trim($_POST['type'])))
  ) {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "<b>Error:</b> Failed. All fields are required.";
    $_SESSION['msg_type'] = "danger";
    redirect("admin_add_items");
  } else {
    $item_code = remove_junk($_POST['item_code']);
    $item_name = remove_junk($_POST['item_name']);
    $item_category = remove_junk($_POST['item_category']);
    $unit = remove_junk($_POST['unit']);
    $manufacturer = remove_junk($_POST['manufacturer']);
    $dot = remove_junk($_POST['dot']);
    $tire_brand = remove_junk($_POST['tire_brand']);
    $tire_size = remove_junk($_POST['tire_size']);
    $tire_design = remove_junk($_POST['tire_design']);
    $rim_size = remove_junk($_POST['rim_size']);
    $load_index = remove_junk($_POST['load_index']);
    $speed_rating = remove_junk($_POST['speed_rating']);
    $ply_rating = remove_junk($_POST['ply_rating']);
    $origin = remove_junk($_POST['origin']);
    $regulation = remove_junk($_POST['regulation']);
    $cif = remove_junk($_POST['cif']);
    $type = remove_junk($_POST['type']);
    $created_by = $_SESSION['name'];
    

    $sql = "INSERT INTO tb_items (`sku_code`, `material_description`,  `category`, `unit`, `manufacturer`, `dot`,`tire_brand`,`tire_size`, `tire_design`, `rim_size`,`load_index`, `speed_rating`, `ply_rating`, `origin`, `regulation`, `cif`, `type`,`created_by`) VALUES('$item_code','$item_name','$item_category','$unit','$manufacturer','$dot','$tire_brand','$tire_size','$tire_design','$rim_size','$load_index','$speed_rating','$ply_rating','$origin','$regulation','$cif','$type','$created_by')";

    if ($db->query($sql)) {
      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "You have successfully added new item(s) to our system!";
      $_SESSION['msg_type'] = "success";
      redirect("admin_add_items");
    }
  }
} else {
  $_SESSION['msg_heading'] = "Error!";
  $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
  $_SESSION['msg_type'] = "error";
  redirect("admin_add_items");
}
