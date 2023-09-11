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

$date_today = date('Y-m-d');

$db_str = $db->query('SELECT
a.id,
a.ref_no,
a.document_no,
a.document_name,
a.ship_date,
a.transaction_type,
a.truck_type,
a.eta,
a.truck_allocation,
a.status,
a.picking_start,
a.picking_end,
a.checking_start,
a.checking_end,
a.validating_start,
a.validating_end,
a.checker,
a.picker,
a.validator,
b.destination_name,
a.driver,
a.helper,
a.plate_no

FROM tb_outbound a  
INNER JOIN tb_destination b ON b.destination_code = a.destination_code
WHERE document_name =? AND transaction_type=? GROUP BY truck_allocation', $_GET['document_name'], $_GET['transaction_type'])->fetch_all();



//print_r_html($db_str);


// print_r_html($all_db_for_status);



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
                <h4 class="card-title">Incoming Dispatch</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Ref No.</th>
                        <th class="align-middle text-center  font-weight-bold ">Truck Allocation</th>

                        <th class="align-middle text-center  font-weight-bold ">Destination</th>
                        <th class="align-middle text-center  font-weight-bold ">Truck Type</th>
                        <th class="align-middle text-center  font-weight-bold ">Ship Date</th>
                        <th class="align-middle text-center  font-weight-bold ">ETA</th>
                        <th class="align-middle text-center  font-weight-bold ">Status</th>


                        <th class="align-middle text-center  font-weight-bold ">Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_str as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle text-center " style="font-weight:bold;"><?php echo "#LP-" . $arr_val['ref_no']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['truck_allocation']; ?></td>

                          <td class="align-middle text-center "><?php echo $arr_val['destination_name']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['truck_type']; ?></td>
                          <td class="align-middle text-center "><?php echo date('M-d-Y', strtotime($arr_val['ship_date'])); ?></td>
                          <td class="align-middle text-center "><?php echo date('M-d-Y', strtotime($arr_val['eta'])); ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['status']; ?></td>

                          <td>
                            <a target="" data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-warning btn-md" title="Update Details"><i class="fas fa-pen-alt"></i></a>
                          </td>
                          <!-- MODAL FOR UPDATE DISPTACH-->
                          <div id="update_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Details</h4>

                                </div>
                                <div class="modal-body">
                                  <div class="form-validation">
                                    <form action="update_truck_allocation" method="post" class="needs-validation" novalidate>
                                      <div class="form-group">

                                        <div class="mt-1">
                                          <input type="hidden" name="url" id="url" value="<?php echo "transport_dispatch_details?document_name=" . $_GET['document_name'] . "&transaction_type=" . $_GET['transaction_type']; ?>">
                                        </div>

                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                        </div>
                                        <!-- DOC-->
                                        <div class="mt-1">
                                          <input type="hidden" name="doc_no" id="doc_no" value="<?php echo $arr_val['document_no']; ?>">
                                        </div>
                                        <!-- ref-->
                                        <div class="mt-1">
                                          <input type="hidden" name="ref" id="ref" value="<?php echo $arr_val['ref_no']; ?>">
                                        </div>

                                        <div class="mt-1">
                                          <input type="hidden" name="allocation" id="allocation" value="<?php echo $arr_val['truck_allocation']; ?>">
                                        </div>


                                        <!-- ATA-->
                                        <div class="mt-1">
                                          <label for="ata" class="form-control-label text-uppercase text-primary
                                              font-weight-bold">Driver Name</label>
                                          <input type="text" name="d_name" id="d_name" class="form-control" placeholder="Enter Name" Value="<?php echo $arr_val['driver']; ?>" required>
                                        </div>

                                        <!-- PLATE NO-->
                                        <div class="mt-1">
                                          <label for="plate_no" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Helper(s)</label>
                                          <input name="h_name" id="h_name" class="form-control" placeholder="Enter Name(s)" Value="<?php echo $arr_val['helper']; ?>" required>
                                        </div>

                                        <!-- ARRIVIAL-->
                                        <div class="mt-1">
                                          <label for="arrival" class="form-control-label text-uppercase
                                              text-primary font-weight-bold">Plate No.</label>
                                          <input type="text" name="p_no" class="form-control" id="p_no" Placeholder="Enter Plate No" Value="<?php echo $arr_val['plate_no']; ?>" required>
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
  <!--**********************************
            Content body end
        ***********************************-->
  </div>


  <!--**********************************
        Scripts
    ***********************************-->

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <!-- Required vendors -->
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
      $('#view_dispatch_table').DataTable({
        order: [
          [1, "desc"]
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