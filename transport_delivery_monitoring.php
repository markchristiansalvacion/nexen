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

$all_items = $db->query('SELECT a.id,
a.branch_received_date,
a.received_by,
a.ir_ref_no,
a.ir_remarks,
a.rr_ref_no,
a.truck_arrival,
a.branch_in,
a.branch_out,
a.fds_comp,
a.window_comp,
a.in_full,
a.source_ref,
a.document_no,
c.destination_name

FROM tb_transport a
INNER JOIN tb_outbound b ON b.document_no = a.document_no
INNER JOIN tb_destination c ON c.destination_code = b.destination_code GROUP BY b.destination_code')->fetch_all();

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
                <h4 class="card-title">Delivery Monitoring</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle  text-center  font-weight-bold ">Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Destination</th>

                        <!-- <th class="align-middle  text-center  font-weight-bold ">Branch Receipt Date</th>
                        <th class="align-middle  text-center  font-weight-bold ">Received By</th>
                        <th class="align-middle  text-center  font-weight-bold ">I.R Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">I.R Remarks</th>
                        <th class="align-middle  text-center  font-weight-bold ">R.R Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Truck Arrival From Delivery</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Time In</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Time Out</th>
                        <th class="align-middle  text-center  font-weight-bold ">FDS Complaince</th>
                        <th class="align-middle  text-center  font-weight-bold ">Window Time Complaince</th>
                        <th class="align-middle  text-center  font-weight-bold ">In Full</th> -->
                        <th class="align-middle  text-center  font-weight-bold ">Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle  text-center "><?php echo "#DIS-" . $arr_val['source_ref']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['destination_name']; ?></td>

                          <td class="align-middle  text-center "><a target="" data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-warning btn-md" title="Update Details"><i class="fas fa-pen-alt"></i></a></td>
                          <!-- MODAL FOR UPDATE DISPTACH-->
                          <div id="update_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Details</h4>

                                </div>
                                <div class="modal-body">
                                  <div class="form-validation">
                                    <form action="update_transport_delivery" method="post" class="needs-validation" novalidate>
                                      <div class="form-group">


                                        <div class="mt-1">
                                          <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                        </div>


                                        <!-- ATA-->
                                        <div class="mt-1">
                                          <label for="ata" class="form-control-label text-uppercase text-primary
                                              font-weight-bold">Branch Received Date</label>
                                          <input type="date" name="b_date" id="b_date" class="form-control" placeholder="Enter Name" value="<?php echo $arr_val['branch_received_date']; ?>" required>
                                        </div>

                                        <!-- PLATE NO-->
                                        <div class="mt-1">
                                          <label for="plate_no" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Received By</label>
                                          <input type="text" name="received_by" id="received_by" class="form-control" placeholder="Enter Name" value="<?php echo $arr_val['received_by']; ?>" required>
                                        </div>

                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">IR Ref No</label>
                                          <input type="text" name="ir_ref" class="form-control" id="ir_ref" Placeholder="Enter IR Ref" value="<?php echo $arr_val['ir_ref_no']; ?>" required>
                                        </div>
                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">IR Remarks</label>
                                          <input type="text" name="ir_remarks" class="form-control" id="ir_remarks" Placeholder="Enter IR Remarks" value="<?php echo $arr_val['ir_remarks']; ?>" required>
                                        </div>
                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">RR Ref No</label>
                                          <input type="text" name="rr_ref" class="form-control" id="rr_ref" Placeholder="Enter RR Ref No" value="<?php echo $arr_val['rr_ref_no']; ?>" required>
                                        </div>
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Truck Arrival From Delivery</label>
                                          <input type="time" name="arrive" class="form-control" id="arrive" Placeholder="Enter Plate No" value="<?php echo $arr_val['truck_arrival']; ?>" required>
                                        </div>
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Branch Time In</label>
                                          <input type="time" name="in" class="form-control" id="in" Placeholder="Enter Plate No" value="<?php echo $arr_val['branch_in']; ?>" required>
                                        </div>
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Branch Time Out</label>
                                          <input type="time" name="out" class="form-control" id="out" Placeholder="Enter Plate No" value="<?php echo $arr_val['branch_out']; ?>" required>
                                        </div>
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">FDS Complaince</label>
                                          <input type="text" name="fds" class="form-control" id="fds" Placeholder="Enter FDS" value="<?php echo $arr_val['fds_comp']; ?>" required>
                                        </div>
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Window Time Complaince</label>
                                          <input type="text" name="window" class="form-control" id="window" Placeholder="Enter Window Complaince" value="<?php echo $arr_val['window_comp']; ?>" required>
                                        </div>
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">In Full</label>
                                          <input type="text" name="in_full" class="form-control" id="in_full" Placeholder="Enter In Full" value="<?php echo $arr_val['in_full']; ?>" required>
                                        </div>

                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                      </div>
                                    </form>
                                  </div>
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