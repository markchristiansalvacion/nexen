<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';

if (isset($_POST)) {
  //print_r_html($_POST);
  if (
    empty(trim($_POST['item_category'])) || empty(trim($_POST['item_code'])) || empty(trim($_POST['item_name']))  || empty(trim($_POST['p_size'])) || empty(trim($_POST['w_box'])) || empty(trim($_POST['cbm'])) || empty(trim($_POST['c_pallet'])) || empty(trim($_POST['p_pallet'])) || empty(trim($_POST['c_tier'])) || empty(trim($_POST['layer']) || empty(trim($_POST['s_life'])))
  ) {

    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "<b>Error:</b> Failed. All fields are required.";
    $_SESSION['msg_type'] = "danger";
    redirect("admin_add_items");
  } else {
    $item_category = remove_junk($_POST['item_category']);
    $ptr_no = remove_junk($_POST['item_code']);
    $item_name = remove_junk($_POST['item_name']);

    $p_size = remove_junk($_POST['p_size']);
    $w_box = remove_junk($_POST['w_box']);
    $cbm = remove_junk($_POST['cbm']);

    $c_pallet = remove_junk($_POST['c_pallet']);
    $p_pallet = remove_junk($_POST['p_pallet']);
    $c_tier = remove_junk($_POST['c_tier']);
    $layer = remove_junk($_POST['layer']);
    $s_life = $_POST['s_life'];

    $sql = "INSERT INTO tb_items (`item_code`, `material_description`,  `category_code`, `pack_size`, `weight_per_box`, `cbm_per_box`,`case_per_pallet`,`pcs_per_pallet`, `case_per_tier`, `layer_high`,`shelf_life`)
        VALUES('$ptr_no','$item_name','$item_category','$p_size','$w_box','$cbm','$c_pallet','$p_pallet','$c_tier','$layer','$s_life')";

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
