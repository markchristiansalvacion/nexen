<?php
require_once 'includes/load.php';
require_once 'vendor2/autoload.php';


if (isset($_POST)) {
  //print_r_html($_POST);
  $id = $db->escape_string($_POST['id']);
  $item_code = $db->escape_string($_POST['item_code']);
  // $new_loc = $db->escape_string($_POST['new_loc']);


  $available_pf_location = $db->query('SELECT * FROM tb_bin_location_bac WHERE location_type = ? and item_code=?  ', "Pickface", $item_code)->fetch_all();
  foreach ($available_pf_location as $pf_key => $pf_val) {
    $loc = $pf_val['location_code'];
  }



  //print_r_html($available_pf_location);


  if (count($available_pf_location) != 0) {
    $category_code = $db->query('SELECT category_code FROM tb_items WHERE item_code = ?', $item_code)->fetch_all();
    foreach ($category_code as $cat_val => $cat_det) {
      $category = $cat_det['category_code'];
    }
    //print_r_html($category_code);
    if (
      $category == 'PERSONAL CARE' || $category == 'MENS CARE' || $category == 'HOME CARE' || $category == 'BABY CARE' || $category == 'HEALTH CARE' || $category == 'FRAGRANCE'

    ) {
      $db_inbound = $db->query('SELECT
                                      tb_inbound.id as in_id ,
                                      tb_inbound.item_code,tb_inbound.batch_no,tb_inbound.qty_pcs AS available_qty,
                                      TIMESTAMPDIFF(DAY,now(),tb_inbound.expiry) AS AGING,
                                      tb_inbound.expiry as EXP,
                                      tb_inbound.date_received,
                                      tb_inbound.lpn,
                                      tb_inbound.mfg,
                                      tb_inbound.bin_location as source_location
                                      FROM tb_inbound
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code =  tb_inbound.bin_location
                                      WHERE   tb_bin_location_bac.location_type=?
                                      AND tb_inbound.item_code=?

                                      AND tb_inbound.qty_pcs <> 0 ', 'Storage', $item_code);

      //print_r_html($db_inbound);
      if ($db_inbound->num_rows() == 0) {
        $get_min_4_to_6_months = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) < 560
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
        //print_r_html($get_min_4_to_6_months);
        if (!empty($get_min_4_to_6_months)) {

          $insert_into_replen_tb = $db->query(
            'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
            $get_min_4_to_6_months['in_id'],
            $get_min_4_to_6_months['source_location'],

            $item_code
          );
          if ($insert_into_replen_tb->affected_rows()) {

            $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_4_to_6_months['in_id']);
            if ($transfer_location->affected_rows()) {
              $_SESSION['msg_heading'] = "Transaction Successfully Added!";
              $_SESSION['msg'] = "This is to confirm that you have successfully conduct  a replenish in the System!";
              $_SESSION['msg_type'] = "success";
              redirect($_POST['url']);
            } else {

              $get_min_6_to_12_months = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) BETWEEN 181 AND 365
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
              //print_r_html($get_min_6_to_12_months);
              if (!empty($get_min_6_to_12_months)) {
                $insert_into_replen_tb = $db->query(
                  'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
                  $get_min_6_to_12_months['in_id'],
                  $get_min_6_to_12_months['source_location'],

                  $item_code
                );

                if ($insert_into_replen_tb->affected_rows()) {

                  $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_6_to_12_months['in_id']);

                  if ($transfer_location->affected_rows()) {
                    $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                    $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                    $_SESSION['msg_type'] = "success";
                    redirect($_POST['url']);
                  } else {

                    $get_min_12_to_18_months = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) BETWEEN 366 AND 560
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();


                    //print_r_html($get_min_12_to_18_months);
                    if (!empty($get_min_12_to_18_months)) {

                      $insert_into_replen_tb = $db->query(
                        'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
                        $get_min_12_to_18_months['in_id'],
                        $get_min_12_to_18_months['source_location'],

                        $item_code
                      );
                      if ($insert_into_replen_tb->affected_rows()) {
                        $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_12_to_18_months['in_id']);
                        if ($transfer_location->affected_rows()) {
                          $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                          $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                          $_SESSION['msg_type'] = "success";
                          redirect($_POST['url']);
                        } else {
                          $get_min_18_months_up = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) > 560
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
                          //print_r_html($get_min_18_months_up);
                          if (!empty($get_min_18_months_up)) {
                            $insert_into_replen_tb = $db->query(
                              'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
                              $get_min_18_months_up['in_id'],
                              $get_min_18_months_up['source_location'],

                              $item_code
                            );

                            if ($insert_into_replen_tb->affected_rows()) {
                              $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_18_months_up['in_id']);
                              if ($transfer_location->affected_rows()) {

                                $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                                $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                                $_SESSION['msg_type'] = "success";
                                redirect($_POST['url']);
                              } else {
                                $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                                $_SESSION['msg'] = "No Available Pickface Location!";
                                $_SESSION['msg_type'] = "error";
                                redirect($_POST['url']);
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      } else {

        $get_db_storage = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00

                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
        //print_r_html($get_db_storage);
        if (!empty($get_db_storage)) {
          $insert_into_replen_tb = $db->query(
            'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
            $get_db_storage['in_id'],
            $get_db_storage['source_location'],

            $item_code
          );


          if ($insert_into_replen_tb->affected_rows()) {

            $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_db_storage['in_id']);
            if ($transfer_location->affected_rows()) {

              $_SESSION['msg_heading'] = "Transaction Successfully Added!";
              $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
              $_SESSION['msg_type'] = "success";
              redirect($_POST['url']);
            } else {
              $_SESSION['msg_heading'] = "Transaction Successfully Added!";
              $_SESSION['msg'] = "No Available Pickface Location!";
              $_SESSION['msg_type'] = "error";
              redirect($_POST['url']);
            }
          }
        }
      }
    } else {


      $category_code = $db->query('SELECT category_code FROM tb_items WHERE item_code = ?', $item_code)->fetch_array();


      if (
        $category == 'PERSONAL CARE' || $category == 'MENS CARE' || $category == 'HOME CARE' || $category == 'BABY CARE' || $category == 'HEALTH CARE' || $category == 'FRAGRANCE'
      ) {
        $db_inbound = $db->query('SELECT
                                      tb_inbound.id as in_id ,
                                      tb_inbound.item_code,tb_inbound.batch_no,tb_inbound.qty_pcs AS available_qty,
                                      TIMESTAMPDIFF(DAY,now(),tb_inbound.expiry) AS AGING,
                                      tb_inbound.expiry as EXP,
                                      tb_inbound.date_received,
                                      tb_inbound.lpn,
                                      tb_inbound.mfg,
                                      tb_inbound.bin_location as source_location
                                      FROM tb_inbound
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code =  tb_inbound.bin_location
                                      WHERE   tb_bin_location_bac.location_type=?
                                      AND tb_inbound.item_code=?

                                      AND tb_inbound.qty_pcs <> 0 ', 'Storage', $item_code);

        //print_r_html($db_inbound);
        if ($db_inbound->num_rows() == 0) {
          $get_min_4_to_6_months = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) BETWEEN 120 AND 180
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();

          if (!empty($get_min_4_to_6_months)) {
            $insert_into_replen_tb = $db->query(
              'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
              $get_min_4_to_6_months['in_id'],
              $get_min_4_to_6_months['source_location'],

              $item_code
            );


            if ($insert_into_replen_tb->affected_rows()) {
              $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_4_to_6_months['in_id']);
              if ($transfer_location->affected_rows()) {


                $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                $_SESSION['msg_type'] = "success";
                redirect($_POST['url']);
              }
            }
          } else {

            $get_min_6_to_12_months = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) BETWEEN 181 AND 365
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
            if (!empty($get_min_6_to_12_months)) {
              $insert_into_replen_tb = $db->query(
                'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
                $get_min_6_to_12_months['in_id'],
                $get_min_6_to_12_months['source_location'],

                $item_code
              );
              if ($insert_into_replen_tb->affected_rows()) {
                $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_6_to_12_months['in_id']);
                if ($transfer_location->affected_rows()) {

                  $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                  $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                  $_SESSION['msg_type'] = "success";
                  redirect($_POST['url']);
                }
              }
            } else {

              $get_min_12_to_18_months = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) BETWEEN 366 AND 560
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();
              if (!empty($get_min_12_to_18_months)) {
                $insert_into_replen_tb = $db->query(
                  'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
                  $get_min_12_to_18_months['in_id'],
                  $get_min_12_to_18_months['source_location'],

                  $item_code
                );

                if ($insert_into_replen_tb->affected_rows()) {
                  $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_12_to_18_months['in_id']);
                  if ($transfer_location->affected_rows()) {

                    $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                    $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                    $_SESSION['msg_type'] = "success";
                    redirect($_POST['url']);
                  }
                }
              } else {
                $get_min_18_months_up = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) > 560
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();

                if (!empty($get_min_18_months_up)) {
                  $insert_into_replen_tb = $db->query(
                    'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
                    $get_min_18_months_up['in_id'],
                    $get_min_18_months_up['source_location'],

                    $item_code
                  );
                  if ($insert_into_replen_tb->affected_rows()) {
                    $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_min_18_months_up['in_id']);
                    if ($transfer_location->affected_rows()) {

                      $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                      $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                      $_SESSION['msg_type'] = "success";
                      redirect($_POST['url']);
                    }
                  }
                } else {
                  $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                  $_SESSION['msg'] = "No Available Pickface Location!";
                  $_SESSION['msg_type'] = "error";
                  redirect($_POST['url']);
                }
              }
            }
          }
        } else {

          $get_db_storage = $db->query('SELECT a.id as in_id,
                                      a.batch_no,
                                      a.qty_pcs as available_qty,
                                      a.bin_location as source_location,
                                      a.lpn,
                                      a.mfg,
                                      a.expiry as EXP,a.date_received,
                                      TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                      FROM tb_inbound a
                                      INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                      WHERE tb_bin_location_bac.location_type = ?
                                      AND a.item_code = ?
                                      AND a.qty_pcs <> 0
                                      AND a.expiry <> 0000-00-00
                                      AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) 
                                      AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1', 'Storage', $item_code)->fetch_array();

          if (!empty($get_db_storage)) {
            $insert_into_replen_tb = $db->query(
              'INSERT INTO `tb_replenishment`(`in_id`,`source_loc`,`item_code`) VALUES (?,?,?)',
              $get_db_storage['in_id'],
              $get_db_storage['source_location'],

              $item_code
            );


            if ($insert_into_replen_tb->affected_rows()) {

              $transfer_location = $db->query('UPDATE tb_inbound SET bin_location = ? WHERE id = ?', $loc, $get_db_storage['in_id']);
              if ($transfer_location->affected_rows()) {

                $_SESSION['msg_heading'] = "Transaction Successfully Added!";
                $_SESSION['msg'] = "This is to confirm that you have successfully conduct a replenish in the System";
                $_SESSION['msg_type'] = "success";
                redirect($_POST['url']);
              } else {
                $_SESSION['msg_heading'] = "Transaction Failed!";
                $_SESSION['msg'] = "No Available Pickface Location!";
                $_SESSION['msg_type'] = "error";
                redirect($_POST['url']);
              }
            }
          }
        }
      }
    }
  }
} else {
  $_SESSION['msg_heading'] = "Transaction Failed!";
  $_SESSION['msg'] = "This is to inform that there is no available pickface location!";
  $_SESSION['msg_type'] = "error";
  redirect($_POST['url']);
  //print_r_html("No Available Pickface Location");
}
