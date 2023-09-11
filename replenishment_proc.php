<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);

  $date = date('Y-m-d');
  $in_id = $db->escape_string($_POST['id']);
  $item_code = $db->escape_string($_POST['item_code']);

  $source_loc = $db->escape_string($_POST['s_loc']);
  // $category = $db->escape_string($_POST['category']);
  // $warehouse = $db->escape_string($_POST['wshe']);


  $available_pf_location = $db->query('SELECT * FROM tb_bin_location_bac WHERE location_type = ? ', "Pickface",)->fetch_all();


  // print_r_html($available_pf_location);

  if (count($available_pf_location) != 0) {



    $category = $db->query('SELECT category_code FROM tb_items WHERE item_code = ?', $item_code)->fetch_all();

    if (
      $category == 'PERSONAL CARE' || $category == 'MENS CARE' || $category == 'HOME CARE' || $category == 'BABY CARE' || $category == 'HEALTH CARE' || $category == 'FRAGRANCE'

    ) {

      $get_min_4_to_6_months = $db->query('SELECT
                                    a.id,
                                    a.batch_no,
                                    a.qty_pcs as available_qty,
                                    a.bin_location,
                                    a.expiry as EXP,
                                    TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                    FROM tb_inbound a
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                    WHERE tb_bin_location_bac.location_type = ?
                                    AND a.item_code = ?
                                    AND a.qty_pcs <> 0
                                    AND a.expiry <> 0000-00-00
                                    AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) < 180
                                    AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
      if (!empty($get_min_4_to_6_months)) {

        $insert_into_replen_tb = $db->query(
          'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`new_loc`,`item_code`,`date_created`) VALUES (?,?,?,?,?)',
          $in_id,
          $source_loc,
          $get_min_4_to_6_months['old_loc'],
          $item_code,
          $date
        );
        if ($insert_into_replen_tb->affected_rows()) {

          $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $source_loc, $get_min_4_to_6_months['id']);
          if ($transfer_location->affected_rows()) {
            $_SESSION['msg_heading'] = "Transaction Successfully Added!";
            $_SESSION['msg'] = "This is to confirm that you have successfully created a picklist in the System!";
            $_SESSION['msg_type'] = "success";
            redirect("check_stock");
          } else {
            $_SESSION['msg_heading'] = "Transaction Successfully Added!";
            $_SESSION['msg'] = "No Available Pickface Location!";
            $_SESSION['msg_type'] = "error";
            redirect("check_stock");
          }
        }
      } else {
        $get_min_18_months_up = $db->query('SELECT
                                    a.id,
                                    a.batch_no,
                                    a.qty_pcs as available_qty,
                                    a.bin_location as old_loc,
                                    a.expiry as EXP,
                                    TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                    FROM tb_inbound a
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                    WHERE tb_bin_location_bac.location_type = ?
                                    AND a.item_code = ?
                                    AND a.qty_pcs <> 0
                                    AND a.expiry <> 0000-00-00
                                    AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) >=180
                                    AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();

        if (!empty($get_min_18_months_up)) {
          $insert_into_replen_tb = $db->query(
            'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`new_loc`,`item_code`,`date_created`) VALUES (?,?,?,?,?)',
            $in_id,
            $source_loc,
            $get_min_18_months_up['old_loc'],
            $item_code,
            $date
          );

          if ($insert_into_replen_tb->affected_rows()) {
            $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $source_loc, $get_min_18_months_up['id']);
            if ($transfer_location->affected_rows()) {

              $_SESSION['msg_heading'] = "Transaction Successfully Added!";
              $_SESSION['msg'] = "This is to confirm that you have successfully created a picklist in the System!";
              $_SESSION['msg_type'] = "success";
              redirect("check_stock");
            } else {
              $_SESSION['msg_heading'] = "Transaction Successfully Added!";
              $_SESSION['msg'] = "No Available Pickface Location!";
              $_SESSION['msg_type'] = "error";
              redirect("check_stock");
            }
          }
        }
      }
    } else {
      $get_db_storage = $db->query('SELECT
                                              a.id,
                                              a.qty_pcs as available_qty,
                                              a.bin_location AS old_loc,
                                              a.expiry as EXP,
                                              TIMESTAMPDIFF(DAY,NOW(),a.date_created) AS Ageing
                                              FROM tb_inbound a
                                              INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                              WHERE tb_bin_location_bac.location_type = ?
                                              AND a.item_code = ?
                                              AND a.qty_pcs <> 0

                                              AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();

      if (!empty($get_db_storage)) {
        $insert_into_replen_tb = $db->query(
          'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`new_loc`,`item_code`,`date_created`) VALUES (?,?,?,?,?)',
          $in_id,
          $source_loc,
          $get_db_storage['old_loc'],
          $item_code,
          $date
        );


        if ($insert_into_replen_tb->affected_rows()) {

          $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE tb_inbound.id = ?', $source_loc, $get_db_storage['id']);
          if ($transfer_location->affected_rows()) {

            $_SESSION['msg_heading'] = "Transaction Successfully Added!";
            $_SESSION['msg'] = "This is to confirm that you have successfully created a picklist in the System!";
            $_SESSION['msg_type'] = "success";
            redirect("check_stock");
          } else {
            $_SESSION['msg_heading'] = "Transaction Successfully Added!";
            $_SESSION['msg'] = "No Available Pickface Location!";
            $_SESSION['msg_type'] = "error";
            redirect("check_stock");
          }
        }
      } else {
        $_SESSION['msg_heading'] = "Transaction Successfully Added!";
        $_SESSION['msg'] = "No Available Pickface Location!";
        $_SESSION['msg_type'] = "error";
        redirect("check_stock");
      }
    }
  }
}
