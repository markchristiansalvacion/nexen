<?php


require_once 'includes/load.php';
require_once 'vendor2/autoload.php';

if (isset($_POST)) {
//print_r_html($_POST);


     $aisle= remove_junk($_POST['aisle']);
     $loc_name = remove_junk($_POST['loc_name']);
     $loc_type = remove_junk($_POST['loc_type']);
     $i_code = remove_junk($_POST['i_code']);
     $whs= remove_junk($_POST['whs']);
     $i_cat = remove_junk($_POST['i_cat']);
     $s_level= remove_junk($_POST['s_level']);

     $sql = "INSERT INTO tb_bin_location_bac (`aisle`, `location_code`,`location_type`,`item_code`,`warehouse`,`category`,`layer`)
         VALUES('$aisle','$loc_name','$loc_type','$i_code','$whs','$i_cat',$s_level)";



     if ($db->query($sql)) {
       $_SESSION['msg_heading'] = "Success!";
       $_SESSION['msg'] = "You have successfully added new item(s) to our system!";
       $_SESSION['msg_type'] = "success";
       redirect("admin_add_location");
     } else {
       $_SESSION['msg_heading'] = "Error!";
       $_SESSION['msg'] = "<b>Error: Failed. Please try again.</b>";
       $_SESSION['msg_type'] = "error";
       redirect("admin_add_location");
     }
   } 


