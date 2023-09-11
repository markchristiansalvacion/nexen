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

//print_r_html(strtotime("2021-11-15"));

$date_today = date('Y-m-d');

$db_inbound = array();

$db_asn = $db->query('SELECT 
    tb_asn.id as asn_id,
    tb_asn.ref_no,
    tb_asn.transaction_type,
    tb_asn.document_no,
    tb_asn.eta,
    tb_asn.ata,
    tb_asn.bay_location,
    tb_asn.time_slot,
    tb_asn.created_by,
    tb_asn.truck_type,
    tb_asn.truck_plate_no,
    tb_asn.time_arrived,
    tb_asn.time_received,
    tb_asn.time_docked,
    tb_asn.unloading_start,
    tb_asn.unloading_end,
    tb_asn.time_departed,
    tb_asn.date_created,
    tb_destination.destination_address,
    tb_source.source_name,
    tb_destination.destination_name
    FROM tb_asn
    INNER JOIN tb_destination ON tb_destination.destination_code = tb_asn.destination_code
    INNER JOIN tb_source ON tb_source.source_code = tb_asn.vendor_code 
     WHERE tb_asn.eta >= ?
   GROUP BY tb_asn.document_no ', $date_today)->fetch_all();


foreach ($db_asn as $db_key => $db_val) {
  $get_inbound_details = $db->query('SELECT id,status FROM tb_inbound where asn_ref_no = ?', $db_val['ref_no'])->fetch_all();
  if (!empty($get_inbound_details)) {
    $db_inbound[$db_val['ref_no']] = $get_inbound_details;
  } else {
    continue;
  }
}
//print_r_html($get_inbound_details);

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
                <h4 class="card-title">Incoming Shipment</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Reference #</th>
                        <th class="align-middle text-center">Document #</th>
                        <th class="align-middle text-center">Status</th>
                        <th class="align-middle text-center">IR Status</th>
                        <th class="align-middle text-center">ETA</th>
                        <th class="align-middle text-center">Transaction Type</th>
                        <th class="align-middle text-center">Source</th>
                        <th class="align-middle text-center">Truck Type</th>
                        <th class="align-middle text-center">Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_asn as $arr_key => $arr_det) { ?>
                        <tr>
                          <td class="align-middle text-center" style="font-weight:bold;"><?php echo "#ASN-" . $arr_det['ref_no']; ?></td>
                          <td><?php echo $arr_det['document_no']; ?></td>
                          <?php if (!empty($db_inbound)) { ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 0) { ?>
                              <td class="align-middle text-center ">
                                <span class="badge badge-warning"><?php echo "Not Posted"; ?></span>
                              </td>
                            <?php } ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 1) { ?>
                              <td class="align-middle text-center ">
                                <span class="badge badge-success"><?php echo "Posted"; ?></span>
                              </td>
                            <?php } ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 2) { ?>
                              <td class="align-middle text-center ">
                                <span class="badge badge-danger"><?php echo "Posted w/ Issue"; ?></span>
                              </td>
                            <?php } ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 3) { ?>
                              <td class="align-middle text-center ">
                                <span class="badge badge-info light"><?php echo "On Quarantine"; ?></span>
                              </td>
                            <?php } ?>
                          <?php } else { ?>
                            <td class="align-middle text-center "><span class="badge badge-warning"><?php echo "Not Posted"; ?></span></td>
                          <?php } ?>
                          <?php if (!empty($db_inbound)) { ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 0) { ?>
                              <td class="align-middle text-center "> <span class="badge badge-warning"><?php echo "Waiting to post"; ?></span></td>
                            <?php } ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 1) { ?>
                              <td class="align-middle text-center "> <span class="badge badge-success"><?php echo "No IR "; ?></span></td>
                            <?php } ?>
                            <?php if (get_asn_status($db_inbound, $arr_det['ref_no']) == 2) { ?>
                              <td class="align-middle text-center"> <span class="badge badge-danger"><?php echo "With IR please Print IR Form"; ?></span></td>
                            <?php } ?>
                          <?php } else { ?>
                            <span class="badge badge-warning">
                              <td class="align-middle text-center "><span class="badge badge-warning"><?php echo "Waiting to post"; ?></span></td>
                              </td>
                            <?php } ?>
                            <td><?php echo date('M-d-Y', strtotime($arr_det['eta'])); ?></td>
                            <td><?php echo $arr_det['transaction_type']; ?></td>
                            <td><?php echo $arr_det['source_name']; ?></td>

                            <td><?php echo $arr_det['truck_type']; ?></td>

                            <td>
                              <div class="dropdown">
                                <button type="button" class="btn btn-success light sharp" data-bs-toggle="dropdown">
                                  <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                      <rect x="0" y="0" width="24" height="24" />
                                      <circle fill="#000000" cx="5" cy="12" r="2" />
                                      <circle fill="#000000" cx="12" cy="12" r="2" />
                                      <circle fill="#000000" cx="19" cy="12" r="2" />
                                    </g>
                                  </svg>
                                </button>
                                <div class="dropdown-menu">
                                  <a class="dropdown-item" href="<?php echo "inbound_add_items?asn_ref={$arr_det['ref_no']}&source_doc={$arr_det['document_no']}" ?>">Add Items</a>
                                  <a class="dropdown-item" href="" data-toggle="modal" data-target="#update_details<?php echo $arr_det['asn_id']; ?>" class=" dropdown-item" title="Update Details">Update</a>

                                  <a class="dropdown-item" href="<?php echo "inbound_print_wizard?document_no={$arr_det['document_no']}&asn_ref={$arr_det['ref_no']}" ?>" class="dropdown-item">Print Forms</a>
                                  <a class="dropdown-item" href="" data-toggle="modal" data-target="#delete<?php echo $arr_det['asn_id']; ?>" class="dropdown-item" style="color:red;">Delete</a>
                                </div>
                              </div>
                            </td>


                            <!-- MODAL FOR UPDATE ASN DETAILS-->
                            <div id="update_details<?php echo $arr_det['asn_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                              <div role="document" class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 id="exampleModalLabel" class="modal-title">Update ASN Details</h4>

                                  </div>
                                  <div class="modal-body">
                                    <form action="inbound_update_asn_proc" method="post">
                                      <div class="form-group">


                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_det['asn_id']; ?>">
                                        </div>

                                        <!-- ATA-->
                                        <div class="mt-1">
                                          <label for="ata" class="form-control-label text-uppercase text-primary font-weight-bold">Enter ATA.</label>
                                          <input type="date" name="ata" id="ata" class="form-control" value="<?php echo $arr_det['ata']; ?>">
                                        </div>

                                        <!-- PLATE NO-->
                                        <div class="mt-1">
                                          <label for="plate_no" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Plate #.</label>
                                          <input type="text" name="plate_no" id="plate_no" class="form-control" value="<?php echo $arr_det['truck_plate_no']; ?>">
                                        </div>

                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Arrival Time</label>
                                          <input type="time" name="arrival" class="form-control" id="arrival" value="<?php echo $arr_det['time_arrived']; ?>">
                                        </div>

                                        <!-- DOCKED-->
                                        <label for="docked" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Docked Time.</label>
                                        <div class="mt-1">
                                          <input type="time" name="docked" class="form-control" id="docked" value="<?php echo $arr_det['time_docked']; ?>">
                                        </div>

                                        <!-- UNLOADING-->
                                        <label for="unloading_start" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Unloading Start.</label>
                                        <div class="mt-1">
                                          <input type="time" name="unloading_start" class="form-control" id="unloading_start" value="<?php echo $arr_det['unloading_start']; ?>">
                                        </div>

                                        <!-- UNLOADING END-->
                                        <label for="unloading_end" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Unloading End.</label>
                                        <div class="mt-1">
                                          <input type="time" name="unloading_end" class="form-control" id="unloading_end" value="<?php echo $arr_det['unloading_end']; ?>">
                                        </div>

                                        <!-- DEPARTED-->
                                        <label for="departed" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Time Departed.</label>
                                        <div class="mt-1">
                                          <input type="time" name="departed" class="form-control" id="departed" value="<?php echo $arr_det['time_departed']; ?>">
                                        </div>
                                        <!-- ASN ITEM STATUS -->

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
                            <div id="delete<?php echo $arr_det['asn_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                              <div role="document" class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 id="exampleModalLabel" class="modal-title">Delete ASN</h4>

                                  </div>
                                  <div class="modal-body">
                                    <form action="inbound_delete_posted_asn" method="post">
                                      <div class="form-group">

                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="ref_no" id="ref_no" value="<?php echo  $arr_det['ref_no']; ?>">
                                        </div>
                                        <div class="mt-1">
                                          <input type="hidden" name="document_no" id="document_no" value="<?php echo  $arr_det['document_no']; ?>">
                                        </div>

                                        <!-- ATA-->
                                        <div class="mt-1">
                                          <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Do you want to delete POSTED ASN?
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