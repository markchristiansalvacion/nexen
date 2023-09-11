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

$get_allocated_items = $db->query('SELECT * FROM tb_picklist where document_no=?', $_GET['document_no'])->fetch_all();
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
                <h4 class="card-title">Allocated Details</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="view_asn_table"> -->
                    <thead>
                      <tr>

                        <th class="align-middle text-center font-weight-bold $arr_val">Item Code</th>
                        <th class="align-middle text-center font-weight-bold $arr_val">Batch No</th>
                        <th class="align-middle text-center font-weight-bold $arr_val">Item Name</th>
                        <th class="align-middle text-center font-weight-bold $arr_val">Qty Pcs</th>
                        <th class="align-middle text-center font-weight-bold $arr_val">Location</th>

                        <th class="align-middle text-center font-weight-bold $arr_val"></th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_allocated_items  as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle text-center "><?php echo $arr_val['item_code']; ?></td>
                          <td class="align-middle text-center "><?php echo  $arr_val['batch_no']; ?></td>
                          <td class="align-middle text-center "><?php echo  $arr_val['item_description']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['qty_pcs']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['bin_loc']; ?></td>

                          <td class="align-middle text-center ">
                            <a target="" data-toggle="modal" data-target="#status<?php echo
                                                                                  $arr_val['id'];
                                                                                  ?>" href="" class="btn btn-outline-primary btn-md" title="Return Stock">Return</a>
                          </td>
                          <!-- MODAL FOR PICKING START-->
                          <div id="status<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Warning</h4>

                                </div>
                                <div class="modal-body">
                                  <form method="post" action="inventory_return_stock">
                                    <div class="form-group">
                                      <div class="mt-1">
                                        <input type="hidden" name="url" id="url" value="<?php echo "inventory_allocated_details?document_no={$arr_val['document_no']}"; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <input type="hidden" name="in_id" id="db_id" value="<?php echo $arr_val['in_id']; ?>">
                                      </div>

                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <input type="hidden" name="item_code" id="item_code" value="<?php echo $arr_val['item_code']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <input type="hidden" class="form-control" name="batch_no" id="batch_no" value="<?php echo $arr_val['batch_no']; ?>">
                                      </div>

                                      <div class="mt-1">
                                        <input type="hidden" class="form-control" name=" qty_pcs" id="qty_cs" value="<?php echo $arr_val['qty_pcs']; ?>">
                                      </div>
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Do you want to return this item ?<label>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-secondary">No Thanks</button>
                                      <button type="submit" class="btn btn-primary">Save Changes</button>
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
  <script>
    $(document).ready(function() {
      $('#view_asn_table').DataTable({
        order: [
          [0, "desc"]
        ],
        lengthMenu: [
          [5],
          [5]
        ]
      });
    });
  </script>
</body>

</html>