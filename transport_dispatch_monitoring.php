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

$all_items = $db->query('SELECT 
a.id,
a.ref_no,
a.document_no,
a.driver,
a.helper,
a.plate_no,
a.truck_type,
a.call_time,
a.actual_dispatch,
a.arrival_time,
a.departed_time,
a.loading_start,
a.loading_end,
a.destination_code,

b.destination_name

 FROM tb_outbound a
 INNER JOIN tb_destination b ON b.destination_code = a.destination_code GROUP BY a.destination_code')->fetch_all();


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
                <h4 class="card-title">Dispatch Monitoring</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle  text-center  font-weight-bold ">Ref No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Destination</th>

                        <th class="align-middle  text-center  font-weight-bold ">Driver</th>
                        <th class="align-middle  text-center  font-weight-bold ">Helper(s)</th>
                        <th class="align-middle  text-center  font-weight-bold ">Plate No</th>
                        <th class="align-middle  text-center  font-weight-bold ">Truck Type</th>

                        <th class="align-middle  text-center  font-weight-bold ">Status</th>
                        <!-- <th class="align-middle  text-center  font-weight-bold ">Truck Departed Time</th>
                        <th class="align-middle  text-center  font-weight-bold ">Actual Dispatch Date</th>
                        <th class="align-middle  text-center  font-weight-bold ">Loading Start</th>
                        <th class="align-middle  text-center  font-weight-bold ">Loading End</th> -->
                        <th class="align-middle  text-center  font-weight-bold ">Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle  text-center "><?php echo $arr_val['ref_no']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['destination_name']; ?></td>

                          <td class="align-middle  text-center "><?php echo $arr_val['driver']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['helper']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['plate_no']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['truck_type']; ?></td>

                          <td class="align-middle  text-center ">
                            <?php
                            if ($arr_val['arrival_time'] > $arr_val['call_time']) {
                              echo "MISSED";
                            } elseif ($arr_val['arrival_time'] <= $arr_val['call_time']) {
                              echo "HIT";
                            } else {
                              echo "No Show";
                            }
                            ?>
                          </td>
                          <td class="align-middle text-center ">
                            <a href="<?php echo "transport_doc_no?destination={$arr_val['destination_name']}" ?>" class="btn btn-outline-info btn-sm"><i class=" fas fa-eye"></i></a>

                            <a target="" data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-success btn-sm" title="Update"><i class="fas fa-pen"></i></a>

                          </td>
                          <!-- <td class="align-middle  text-center ">
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
                                <a class="dropdown-item" href="<?php echo "transport_doc_no?destination={$arr_val['destination_name']}" ?>">View Details</a>
                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" class=" dropdown-item" title="Update Details">Update</a>
                              </div>
                            </div>
                          </td> -->
                          <!-- MODAL FOR UPDATE DISPTACH-->
                          <div id="update_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Details</h4>

                                </div>
                                <div class="modal-body">
                                  <div class="form-validation">
                                    <form action="update_transport_dispatch" method="post" class="needs-validation" novalidate>
                                      <div class="form-group">



                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="dest" id="dest" value="<?php echo $arr_val['destination_code']; ?>">
                                        </div>


                                        <!-- ATA-->
                                        <div class="mt-1">
                                          <label for="ata" class="form-control-label text-uppercase text-primary
                                              font-weight-bold">Call Time</label>
                                          <input type="time" name="c_time" id="c_time" class="form-control" placeholder="Enter Name" value="<?php echo $arr_val['call_time']; ?>" required>
                                        </div>

                                        <!-- PLATE NO-->
                                        <div class="mt-1">
                                          <label for="plate_no" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Truck Arrival Time</label>
                                          <input type="time" name="arrival" id="arrival" class="form-control" placeholder="Enter Name(s)" value="<?php echo $arr_val['arrival_time']; ?>" required>
                                        </div>

                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Truck Departure Time</label>
                                          <input type="time" name="depart" class="form-control" id="depart" Placeholder="Enter Plate No" value="<?php echo $arr_val['departed_time']; ?>" required>
                                        </div>
                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Actual Dispatch </label>
                                          <input type="date" name="act_dispatch" class="form-control" id="act_dispatch" Placeholder="Enter Plate No" value="<?php echo $arr_val['actual_dispatch']; ?>" required>
                                        </div>
                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Loading Start</label>
                                          <input type="time" name="l_start" class="form-control" id="l_start" Placeholder="Enter Plate No" value="<?php echo $arr_val['loading_start']; ?>" required>
                                        </div>
                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Loading End</label>
                                          <input type="time" name="l_end" class="form-control" id="l_end" Placeholder="Enter Plate No" value="<?php echo $arr_val['loading_end']; ?>" required>
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