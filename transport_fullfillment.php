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

$all_items = $db->query('SELECT branch_receipt_date,
received_by,
ir_ref_no,
ir_remarks,
rr_ref_no,
truck_arrival,
branch_in,
branch_out,
fds_comp,
window_comp,
in_full,
ref_no,
document_no,
destination
FROM tb_transport_allocation')->fetch_all();

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
                <h4 class="card-title">Dispatch Fullfillment</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle  text-center  font-weight-bold ">Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Destination</th>
                        <th class="align-middle  text-center  font-weight-bold ">Document No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Receipt Date</th>
                        <th class="align-middle  text-center  font-weight-bold ">Received By</th>
                        <th class="align-middle  text-center  font-weight-bold ">I.R Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">I.R Remarks</th>
                        <th class="align-middle  text-center  font-weight-bold ">R.R Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Truck Arrival From Delivery</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Time In</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Time Out</th>
                        <th class="align-middle  text-center  font-weight-bold ">FDS Complaince</th>
                        <th class="align-middle  text-center  font-weight-bold ">Window Time Complaince</th>
                        <th class="align-middle  text-center  font-weight-bold ">In Full</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle  text-center "><?php echo $arr_val['ref_no']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['destination']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['document_no']; ?></td>
                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#Picking_End<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['branch_receipt_date']; ?></a>
                          </td>
                          <!-- MODAL FOR RECEIPT DATE-->
                          <div id="Picking_End<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Date</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_branch_date" method="post">
                                    <div class="form-group">



                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter/Select Date</label>
                                        <div class="mt-1">
                                          <input type="date" name="receipt_date" id="receipt_date" class="form-control">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#received<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['received_by']; ?></a>
                          </td>
                          <!-- MODAL FOR RECEIVED BY-->
                          <div id="received<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Received By</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_received_by" method="post">
                                    <div class="form-group">


                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Receiver Name</label>
                                        <div class="mt-1">
                                          <input type="text" name="r_name" id="r_name" class="form-control" placeholder="Enter Receiver Name">
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


                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#ir_ref<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['ir_ref_no']; ?></a>
                          </td>
                          <!-- MODAL FOR IR REF-->
                          <div id="ir_ref<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update IR Ref No</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_ir_no" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter IR #</label>
                                        <div class="mt-1">
                                          <input type="text" name="ir_ref" id="ir_ref" class="form-control" placeholder="Enter IR Ref #">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#ir_remarks<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['ir_remarks']; ?></a>
                          </td>
                          <!-- MODAL FOR IR REMARKS-->
                          <div id="ir_remarks<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update IR Remarks</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_ir_remarks" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter IR Remarks</label>
                                        <div class="mt-1">
                                          <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Enter remarks">
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


                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#rr_ref<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['rr_ref_no']; ?></a>
                          </td>
                          <!-- MODAL FOR RER REF-->
                          <div id="rr_ref<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update RR Ref #</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_rr_ref" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter RR Ref #</label>
                                        <div class="mt-1">
                                          <input type="text" name="rr_ref" id="rr_ref" class="form-control" placeholder="Enter RR ">
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


                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#truck<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['truck_arrival']; ?></a>
                          </td>
                          <!-- MODAL FOR TRUCK ARRIVAL-->
                          <div id="truck<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Time of Arrival</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_actual_arrival" method="post">
                                    <div class="form-group">
                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter/Select Time of Arrival</label>
                                        <div class="mt-1">
                                          <input type="time" name="arrv" id="arrv" class="form-control">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#in<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['branch_in']; ?></a>
                          </td>
                          <!-- MODAL FOR BRANCH IN-->
                          <div id="in<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Time In</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_branch_in" method="post">
                                    <div class="form-group">


                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Time In</label>
                                        <div class="mt-1">
                                          <input type="time" name="time_in" id="time_in" class="form-control">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#out<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['branch_out']; ?></a>
                          </td>
                          <!-- MODAL FOR TIME OUT-->
                          <div id="out<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Time Out</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_branch_out" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Time Out</label>
                                        <div class="mt-1">
                                          <input type="time" name="time_out" id="time_out" class="form-control">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#fds_comp<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['fds_comp']; ?></a>
                          </td>
                          <!-- MODAL FOR FDS-->
                          <div id="fds_comp<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update FDS Compliance</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_fds" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter/Select FDS</label>
                                        <div class="mt-1">
                                          <input type="text" name="fds" id="fds" class="form-control">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#window_comp<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['window_comp']; ?></a>
                          </td>
                          <!-- MODAL FOR WINDOW-->
                          <div id="window_comp<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Window Complaince</h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_window" method="post">
                                    <div class="form-group">


                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter/Select Window Complaince</label>
                                        <div class="mt-1">
                                          <input type="text" name="window" id="window" class="form-control">
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

                          <td class="align-middle text-center">
                            <a target="" data-toggle="modal" data-target="#full<?php echo $arr_val['ref_no']; ?>" href="" class="align-middle text-center" title="Picking End"><?php echo $arr_val['in_full']; ?></a>
                          </td>
                          <!-- MODAL FOR RECEIVED BY-->
                          <div id="full<?php echo $arr_val['ref_no']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update </h4>
                                  <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                  <form action="update_in_full" method="post">
                                    <div class="form-group">

                                      <!-- REF NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                      </div>

                                      <!-- DOC NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- TIME UNLOADING START -->
                                      <div class="mt-2">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Enter/Select</label>
                                        <div class="mt-1">
                                          <input type="text" name="in_full" id="in_full" class="form-control">
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