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
<?php include 'views/top_bar.php'; ?>>
<?php

$all_items = $db->query('SELECT ref_no,id,
document_no,
item_code,
batch_no,
qty_pcs,
expiry,
lpn,
date_created FROM tb_inbound 
WHERE status=?', "3")->fetch_all();

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
  <!--*******************
        Preloader end
    ********************-->


  <div id="main-wrapper">

    <!--**********************************
            Content body start
        ***********************************-->
    <div class="content-body">
      <div class="container-fluid">
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Inbound Quarantine Items</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm" id="view_quarantin_table"> -->
                    <thead>
                      <tr>
                        <th>Source Re #</th>
                        <th>Document No</th>
                        <th>Item Code</th>
                        <th>Batch Code</th>
                        <th>Qty</th>
                        <th>Expiry</th>
                        <th>LPN</th>
                        <th>Date Received</th>
                        <th>Status</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>
                          <td><?php echo $arr_val['ref_no']; ?></td>
                          <td><?php echo $arr_val['document_no']; ?></td>
                          <td><?php echo $arr_val['item_code']; ?></td>
                          <td><?php echo $arr_val['batch_no']; ?></td>
                          <td><?php echo $arr_val['qty_pcs']; ?></td>
                          <td><?php echo $arr_val['expiry']; ?></td>
                          <td><?php echo $arr_val['lpn']; ?></td>
                          <td><?php echo $arr_val['date_created']; ?></td>
                          <td class="small text-center">

                            <a target="" data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-danger btn-md" title="UpdateDetails"><input type='hidden' name='stats' value='<?php echo $arr_val['status']; ?>'><i class="fas fa-unlock"></i></a>

                          </td>
                          <div id="update_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Details</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="update_quarantine" method="post">
                                    <div class="form-group">


                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>
                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['asn_ref_no']; ?>">
                                      </div>
                                      <!-- LPN-->
                                      <div class="mt-1">
                                        <input type="hidden" name="lpn" id="lpn" value="<?php echo $arr_val['lpn']; ?>">
                                      </div>
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase  text-primary font-weight-bold">Unlock Option</label>
                                        <div class="mt-1">
                                          <select class="form-control" name="status">
                                            <option value="">Select</option>
                                            <option value="1">Receive Goods in Complete and Good Condition</option>


                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                      <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>



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

    <!--**********************************
					Footer start
				***********************************-->

    <!--**********************************
					Footer end
				***********************************-->
  </div>
  </div>
  <!--**********************************
            Content body end
        ***********************************-->
  <!--**********************************
           Support ticket button start
        ***********************************-->

  <!--**********************************
           Support ticket button end
        ***********************************-->


  </div>
  <!--**********************************
        Main wrapper end
    ***********************************-->

  <!--**********************************
        Scripts
    ***********************************-->
  <!-- Required vendors -->
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
      $('#view_quarantin_table').DataTable({
        order: [
          [6, "desc"]
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