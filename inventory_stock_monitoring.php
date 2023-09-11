<?php
require_once 'includes/load.php';

/**
 * Check each script if login is authenticated or if session is already expired
 */



// either new or old, it should live at most for another hour

if (is_login_auth()) {

  /** SESSION BASE TO TIME TODAY */

  if (is_session_expired()) {
    $_SESSION['msg'] = "<b>SESSION EXPIRED:</b> Please Login Again.";
    $_SESSION['msg_type'] = "danger";

    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['user_type']);
    unset($_SESSION['user_status']);

    unset($_SESSION['login_time']);

    /**TIME TO DAY + 315360000 THAT EQUIVALENT TO 10 YEARS*/

    redirect("login", false);
  }
} else {
  redirect("login", false);
}

?>

<?php include 'views/header.php'; ?>
<?php include 'views/nav_header.php'; ?>
<?php include 'views/top_bar.php'; ?>
<?php
$arr_inv_lvl = array();
$all_item_code_qty = array();
$table_data = array();
$date_today = date('Y-m-d');

$db_str = $db->query('SELECT
       tb_outbound.id,
      
        tb_outbound.ref_no , 
        tb_outbound.truck_allocation, 
       
        tb_outbound.item_code,
       
          tb_outbound.allocation,
        
      
        tb_outbound.qty_pcs as required_qty
       
        FROM tb_outbound
       
     
        WHERE   tb_outbound.document_name=? GROUP BY  tb_outbound.document_no', $_GET['document_name'])->fetch_all();

foreach ($db_str as $arr_key => $out_db_line) {
  $item_code = $out_db_line['item_code'];
  $order_qty = $out_db_line['required_qty'];
  /*
   * Get Inbound SUM
   */
  $inbound_sum = $db->query(
    'SELECT 
                            tb_inbound.id as recID,
                            tb_inbound.item_code, 
                             tb_inbound.bin_location as source_loc,
                            SUM(tb_inbound.qty_pcs) as SOH,
                            tb_items.category_code,
                               tb_bin_location_bac.warehouse
                            FROM tb_inbound 
                            INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
                              INNER JOIN tb_bin_location_bac  ON tb_bin_location_bac.location_code = tb_inbound.bin_location
                            WHERE tb_inbound.item_code = ?
                            AND tb_bin_location_bac.location_type = ? ',
    $item_code,
    "Pickface"
  )->fetch_array();
  if (is_array_has_empty_input($inbound_sum)) {
    $all_item_code_qty[$item_code]['recID'] = $inbound_sum['recID'];
    $all_item_code_qty[$item_code]['category_code'] = $inbound_sum['category_code'];
    $all_item_code_qty[$item_code]['warehouse'] = $inbound_sum['warehouse'];
    $all_item_code_qty[$item_code]['source_loc'] = $inbound_sum['source_loc'];
    $all_item_code_qty[$item_code]['SOH'] = "Not in inbound";
    $all_item_code_qty[$item_code]['allocation_status'] = "Item Not in Masterlist/Not in Inbound Records";
  } else {

    if ($inbound_sum['SOH'] < $order_qty) {
      $all_item_code_qty[$item_code]['recID'] = $inbound_sum['recID'];
      $all_item_code_qty[$item_code]['category_code'] = $inbound_sum['category_code'];
      $all_item_code_qty[$item_code]['warehouse'] = $inbound_sum['warehouse'];
      $all_item_code_qty[$item_code]['source_loc'] = $inbound_sum['source_loc'];
      $all_item_code_qty[$item_code]['SOH'] = $inbound_sum['SOH'];
      $all_item_code_qty[$item_code]['allocation_status'] = "Replenish";
    } else {
      $all_item_code_qty[$item_code]['recID'] = $inbound_sum['recID'];
      $all_item_code_qty[$item_code]['category_code'] = $inbound_sum['category_code'];
      $all_item_code_qty[$item_code]['warehouse'] = $inbound_sum['warehouse'];
      $all_item_code_qty[$item_code]['source_loc'] = $inbound_sum['source_loc'];
      $all_item_code_qty[$item_code]['SOH'] = $inbound_sum['SOH'];
      $all_item_code_qty[$item_code]['allocation_status'] = "Good";
    }
  }
  //print_r_html($all_item_code_qty);
}
?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <?php
    if (isset($_SESSION['msg'])) {
    ?>
      <script>
        swal({

          title: "<?php echo $_SESSION['msg_heading']; ?>",
          text: "<?php echo $_SESSION['msg']; ?>",
          icon: "<?php echo $_SESSION['msg_type']; ?>",
          button: "Close",

        });
      </script>

    <?php

      unset($_SESSION['msg']);
      unset($_SESSION['msg_type']);
      unset($_SESSION['msg_heading']);
    }
    ?>
  </div>
</div>

<body>

  <!--*******************
        Preloader start
    ********************-->

  <div id="preloader">
    <div class="loader"></div>
  </div>

  <div id="main-wrapper">

    <div class="content-body">
      <div class="container-fluid">
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Triggered Replenishment</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Item Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Required</th>
                        <th class="align-middle text-center  font-weight-bold ">Remarks</th>
                        <th class="align-middle text-center  font-weight-bold "></th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_item_code_qty as $arr_item_code => $arr_details) {
                        if (
                          are_strings_equal($arr_details['allocation_status'], "Item Not in Masterlist/Not in Inbound Records")
                          || are_strings_equal($arr_details['allocation_status'], "Replenish")
                        ) {
                      ?>
                          <tr>


                            <td class="align-middle text-center "><?php echo  $arr_item_code;
                                                                  ?></td>
                            <td class="align-middle text-center">
                              <?php
                              if (are_strings_equal($arr_details['allocation_status'], "Item Not in Masterlist/Not in Inbound Records")) {
                                echo '<a href="add_item">Item Not in Masterlist/Not in Inbound Records';
                              } else {
                                echo number_format($arr_details['SOH'] - $order_qty, 2, ".", ",");
                              }

                              ?>
                            </td>
                            <td class="align-middle text-center "><?php
                                                                  if (are_strings_equal($arr_details['allocation_status'], "Item Not in Masterlist/Not in Inbound Records")) {
                                                                    echo '<p>Item Not in Masterlist/Not in Inbound Records</p>';
                                                                  } else {
                                                                    echo '<p>Replenish</p>';
                                                                  }

                                                                  ?></td>

                            <?php if ($arr_details['allocation_status'] == 'Item Not in Masterlist/Not in Inbound Records') : ?>
                              <td class="align-middle text-center"> <a target="" data-toggle="modal" data-target="#status<?php echo $arr_details['recID']; ?>" href="" class="btn btn-outline-primary btn-md disabled" title="Replenish">Replenish</a> </td>
                            <?php else : ?>
                              <td class="align-middle text-center"> <a target="" data-toggle="modal" data-target="#status<?php echo $arr_details['recID']; ?>" href="" class="btn btn-outline-primary btn-md " title="Replenish">Replenish</a> </td>
                            <?php endif; ?>
                            <!-- MODAL FOR PICKING START-->
                            <div id="status<?php echo $arr_details['recID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                              <div role="document" class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 id="exampleModalLabel" class="modal-title">Hey There!</h4>

                                  </div>
                                  <div class="modal-body">
                                    <form method="post" action="replenishment_proc">
                                      <div class="form-group">

                                        <!--  URL-->
                                        <div class="mt-1">
                                          <input type="hidden" name="url" id="url" value="<?php echo "view_stock?document_name={$_GET['document_name']}&transaction_type={$_GET['transaction_type']}"; ?>">
                                        </div>
                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" class="form-control" name="id" id="id" value="<?php echo
                                                                                                              $arr_details['recID']; ?>">
                                        </div>

                                        <!-- ITEM CODE-->
                                        <div class="mt-1">
                                          <input type="hidden" class="form-control" name="item_code" id="item_code" value="<?php echo
                                                                                                                            $arr_item_code; ?>">
                                        </div>


                                        <div class="mt-1">
                                          <input type="hidden" class="form-control" name="category" id="category" value="<?php echo
                                                                                                                          $arr_details['category_code']; ?>">
                                        </div>

                                        <div class="mt-1">
                                          <input type="hidden" class="form-control" name="wshe" id="wshe" value="<?php echo
                                                                                                                  $arr_details['warehouse']; ?>">
                                        </div>
                                        <!-- SOURCE LOC-->
                                        <div class="mt-1">

                                          <input type="hidden" class="form-control" name="s_loc" id="s_loc" value="<?php echo
                                                                                                                    $arr_details['source_loc']; ?>">
                                        </div>

                                        <!--TRANSACTION TYPE-->
                                        <div class="mt-1">

                                          <input type="hidden" class="form-control" name="transaction_type" id="transaction_type" value="<?php echo $_GET['transaction_type']; ?>">
                                        </div>


                                        <div class="mt-2">
                                          <label for="time" class="form-control-label text-uppercase text-primary
                            font-weight-bold">Do you want to Replenish this item ?</label>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Replenish</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- End Modal -->
                          </tr>
                      <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Allocate Load Plan</h4>
              </div>
              <div class="card-body">
                <?php
                $get_branch = $db->query('SELECT 
                  a.truck_allocation,
                  a.document_name,
                  a.transaction_type,
                  a.allocation,
                  a.status,
                  b.branch_name FROM tb_outbound a
                  INNER JOIN tb_branches b ON b.branch_code = a.destination_code
                  WHERE document_name=? AND transaction_type=? GROUP BY truck_allocation', $_GET['document_name'], $_GET['transaction_type'])->fetch_all();
                ?>
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Branch Name</th>
                        <th class="align-middle text-center  font-weight-bold ">Truck Allocation</th>
                        <th class="align-middle text-center  font-weight-bold ">Transaction Type</th>

                        <th class="align-middle text-center  font-weight-bold ">Status</th>
                        <th class="align-middle text-center  font-weight-bold ">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_branch as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle text-center "><?php echo  $arr_val['branch_name']; ?></td>
                          <td class="align-middle text-center "><?php echo  $arr_val['truck_allocation']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['transaction_type']; ?></td>

                          <td class="  text-center align-middle">
                            <?php
                            if ($arr_val['allocation'] == 'YES') {
                              echo "Allocated";
                            }
                            if ($arr_val['allocation'] == 'NO') {
                              echo "For Allocation";
                            }

                            ?></td>
                          <td class="align-middle text-center">

                            <a target="" href="<?php echo "inventory_coload_allocation?truck_allocation={$arr_val['truck_allocation']}&transaction_type={$arr_val['transaction_type']}&branch={$arr_val['branch_name']}" ?>" class="btn  btn-outline-primary btn-md " title="Update">Enter to Allocate</i></a>

                          </td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  </div>

  <!--**********************************
        Scripts
    ***********************************-->

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="./vendor/global/global.min.js"></script>
  <script src="./vendor/chart.js/Chart.bundle.min.js"></script>
  <script src="./vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
  <!-- Apex Chart -->
  <script src="./vendor/apexchart/apexchart.js"></script>

  <!-- Datatable -->
  <script src="./vendor/datatables/js/jquery.dataTables.min.js"></script>
  <script src="./js/plugins-init/datatables.init.js"></script>

  <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

  <script src="./js/custom.min.js"></script>
  <script src="./js/dlabnav-init.js"></script>
  <script src="./js/demo.js"></script>
  <script src="./js/styleSwitcher.js"></script>

</body>

</html>