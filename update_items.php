<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {

  // /**
  //  * Adding of items Process Logic
  //     *1. Add the item details in the Items Table in Database that is the "items" table
  //     *2. Check the item status if status = 301 add the item details in the available_items database table
  //     *2.1 if status = 300 add to lock_items database table
  // */

  if (are_fields_filled($_POST)) {

    //print_r_html($_POST);
    $id = remove_junk($_POST['db_id']);
    $item_code = remove_junk($_POST['item_code']);
    $cat = $_POST['cat'];
    $description = remove_junk($_POST['descrip']);
    $p_size = remove_junk($_POST['p_size']);
    $c_pallet = remove_junk($_POST['c_pallet']);
    $p_pallet = remove_junk($_POST['p_pallet']);
    $c_tier = remove_junk($_POST['c_tier']);
    $layer = remove_junk($_POST['layer']);
    $shelf_life = remove_junk($_POST['shelf_life']);
    $weight_per_case = remove_junk($_POST['weight_per_case']);
    $cbm_per_case = remove_junk($_POST['cbm_per_case']);

    $update_item_details = $db->query('UPDATE tb_items set sap_code = ?, category=?,material_description = ?, pack_size = ?, case_per_pallet = ? , pcs_per_pallet=?, case_per_tier=?, layer=?, shelf_life = ?, weight_per_case = ?, cbm_per_case = ? WHERE id = ?', $item_code, $cat, $description, $p_size, $c_pallet, $p_pallet, $c_tier, $layer, $shelf_life, $weight_per_case, $cbm_per_case, $id);

    if ($update_item_details->affected_rows()) {
      $_SESSION['msg_heading'] = "Well Done!";
      $_SESSION['msg'] = "{$item_code}-{$description} Master List Details is now UPDATED!";
      $_SESSION['msg_type'] = "success";
      redirect("admin_manage_items", false);
    } else {


      $_SESSION['msg_heading'] = "Transaction Error!";
      $_SESSION['msg'] = "Update Failed. If this persist please Contact your System Administrator.";
      $_SESSION['msg_type'] = "error";
      redirect("admin_manage_items", false);
    }
  } else {


    $_SESSION['msg_heading'] = "Transaction Error!";
    $_SESSION['msg'] = "In Order to Confirm the Transaction. Please Fill all Fields.";
    $_SESSION['msg_type'] = "error";
    redirect("admin_manage_items", false);
  }
}
