<?php

require_once 'includes/load.php';
require_once 'vendor2/autoload.php';

if (isset($_POST)) {
//print_r_html($_POST);

  $vend_code = remove_junk($_POST['vend_code']);
  $vend_name = remove_junk($_POST['vend_name']);
  $add_name = remove_junk($_POST['add_name']);

  $sql = "INSERT INTO tb_source (`source_code`, `source_name`, `address`)
      VALUES('$vend_code','$vend_name','$add_name')";

  if ($db->query($sql)) {
    $sqls = "INSERT INTO tb_vendor(`vendor_upload_name`, `vendor_id`, `vendor_name`, `address`)
          VALUES('$vend_name','$vend_code','$vend_name','$add_name')";

    if ($db->query($sqls)) {
      $_SESSION['msg_heading'] = "Success!";
      $_SESSION['msg'] = "You have successfully added new item(s) to our system!";
      $_SESSION['msg_type'] = "success";
      redirect("admin_add_vendor");
    } else {
      $_SESSION['msg_heading'] = "Error!";
      $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
      $_SESSION['msg_type'] = "error";
      redirect("admin_add_vendor");
    }
  } else {
    $_SESSION['msg_heading'] = "Error!";
    $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
    $_SESSION['msg_type'] = "error";
    redirect("admin_add_vendor");
  }
} else {
  $_SESSION['msg_heading'] = "Error!";
  $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
  $_SESSION['msg_type'] = "error";
  redirect("admin_add_vendor");
}
