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

$all_items = $db->query('SELECT id,
ref_no,
source_ref_no,
transaction_type,
source_document,
nature_of_ir,
ir_date,
item_code,
batch_code,
qty,
source,
destination,
description,
created_by,
status,
date_closed,
closed_by,
remarks
 FROM tb_incident_report WHERE status =? and transaction_type=? GROUP BY ref_no', 'Open', 'Outbound')->fetch_all();
//print_r_html($all_items);
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
                <h4 class="card-title">Incident Report</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Ref No</th>
                        <th class="align-middle text-center  font-weight-bold ">Source Ref No</th>
                        <th class="align-middle text-center  font-weight-bold ">Transcation Type</th>
                        <th class="align-middle text-center  font-weight-bold ">Document No</th>
                        <th class="align-middle text-center  font-weight-bold ">IR Date</th>
                        <th class="align-middle text-center  font-weight-bold ">Description</th>
                        <th class="align-middle text-center  font-weight-bold ">Created By</th>
                        <th class="align-middle text-center  font-weight-bold ">Status</th>
                        <th class="align-middle text-center  font-weight-bold ">Action</th>



                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle text-center" style="font-weight:bold;"><?php echo "#IR-" . $arr_val['ref_no']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['source_ref_no']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['transaction_type']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['source_document']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['ir_date']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['description']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['created_by']; ?></td>
                          <td class="align-middle text-center">
                            <span class="badge badge-success"><?php echo $arr_val['status']; ?></span>
                          </td>
                          <td class="align-middle text-center ">
                            <a target="" data-toggle="modal" data-target="#update<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-success btn-sm" title="Update"><i class="fas fa-pen-alt"></i></a>
                            <a href="<?php echo "print_ir?source_ref_no={$arr_val['ref_no']}&ref_no={$arr_val['ref_no']}&document_no={$arr_val['source_document']}" ?>" class="btn btn-outline-info btn-sm" title="Print Forms"><i class=" fas fa-print"></i></a>
                            <a target="" data-toggle="modal" data-target="#delete<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-danger btn-sm" title="Delete"><i class="fas fa-trash"></i></a>

                          </td>

                          <div id="update<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">IR Status</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="update_outbound_ir_status" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>
                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['source_document']; ?>">
                                      </div>
                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Date Closed</label>
                                        <div class="mt-1">
                                          <input type="date" name="receipt_date" id="receipt_date" class="form-control">
                                        </div>
                                      </div>
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Closed By</label>
                                        <div class="mt-1">
                                          <input type="text" name="closed_by" id="closed_by" class="form-control" placeholder="Enter Name">
                                        </div>
                                      </div>
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Remarks</label>
                                        <div class="mt-1">
                                          <input type="text" name="ir_remarks" id="ir_remarks" class="form-control" value="<?php echo $arr_val['remarks']; ?>">
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
                          <!-- End Modal -->
                          <!-- MODAL FOR DELETE-->
                          <div id="delete<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Delete Incident Report</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="" method="post">
                                    <div class="form-group">

                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo  $arr_val['ref_no']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo  $arr_val['source_document']; ?>">
                                      </div>

                                      <!-- ATA-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Do you want to delete INCIDENT REPORT?
                                          Please Confirm.</label>

                                      </div>


                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-primary">Delete</button>
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

</body>

</html>