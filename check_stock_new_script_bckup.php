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

$all_item_code_qty = array();
$date_today = date('Y-m-d');

$for_allocation = $db->query('SELECT item_code, SUM(qty_pcs) as required FROM tb_outbound WHERE status = ?', 'FOR ALLOCATION')->fetch_all();

foreach ($for_allocation as $arr_val => $arr_det) {
  $item_code = $arr_det['item_code'];
  $required = $arr_det['required'];

  $aux = $db->query('SELECT
              A1.item_code,
              A1.required,
              V1.available,
              V1.recID,
              V1.source_loc
              FROM
              (SELECT
              item_code,
              qty_pcs AS required
              FROM tb_outbound WHERE STATUS="FOR ALLOCATION"  GROUP BY item_code ORDER BY required ) A1
              INNER JOIN
              (SELECT
              d.id AS recID,
                d.item_code, 
                SUM(d.qty_pcs) AS available,
                d.bin_location  as source_loc
                FROM tb_inbound d 
                INNER JOIN tb_bin_location_bac e ON e.location_code = d.bin_location 
                WHERE e.location_type=? GROUP BY item_code ORDER BY available) V1 ON V1.item_code = A1.item_code  ', 'Pickface')->fetch_array();

  if (is_array_has_empty_input($aux)) {
    $all_item_code_qty[$item_code]['recID'] = $inbound_sum['recID'];
    $all_item_code_qty[$item_code]['source_loc'] = $inbound_sum['source_loc'];
    $all_item_code_qty[$item_code]['available'] = "Not in inbound";
    $all_item_code_qty[$item_code]['allocation_status'] = "Item Not in Masterlist/Not in Inbound Records";
  } else {

    if ($aux['available'] < $arr_det['required']) {
      $all_item_code_qty[$item_code]['recID'] = $aux['recID'];
      $all_item_code_qty[$item_code]['item_code'] = $aux['item_code'];
      $all_item_code_qty[$item_code]['required'] = $required;
      $all_item_code_qty[$item_code]['available'] = $aux['available'];
      $all_item_code_qty[$item_code]['source_loc'] = $aux['source_loc'];
      $all_item_code_qty[$item_code]['allocation_status'] = "Replenish";
    } else {
      $all_item_code_qty[$item_code]['recID'] = $aux['recID'];
      $all_item_code_qty[$item_code]['item_code'] = $aux['item_code'];
      $all_item_code_qty[$item_code]['required'] = $required;
      $all_item_code_qty[$item_code]['source_loc'] = $aux['source_loc'];
      $all_item_code_qty[$item_code]['available'] = $aux['available'];
      $all_item_code_qty[$item_code]['allocation_status'] = "Good";
    }
  }
}
//print_r_html($all_item_code_qty);



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
                            <td class="align-middle text-center "><?php echo  $arr_details['item_code']; ?></td>
                            <td class="align-middle text-center">
                              <?php
                              if (are_strings_equal($arr_details['allocation_status'], "Item Not in Masterlist/Not in Inbound Records")) {
                                echo '<a href="add_item">Item Not in Masterlist/Not in Inbound Records';
                              } else {
                                echo number_format($arr_details['available'] - $required, 2, ".", ",");
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
                                        <!-- SOURCE LOC-->
                                        <div class="mt-1">

                                          <input type="text" class="form-control" name="s_loc" id="s_loc" value="<?php echo
                                                                                                                  $arr_details['source_loc']; ?>">
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

  <!-- <script>
    $(document).ready(function() {

      $('#example4').DataTable({
        order: [
          [1, "desc"],
        ],
        lengthMenu: [
          [5],
          [5]
        ]
      })

    });
  </script> -->


</body>

</html>