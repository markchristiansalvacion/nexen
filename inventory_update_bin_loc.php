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
$get_lpn = $db->query('SELECT DISTINCT lpn FROM tb_inventory_adjustment')->fetch_all();
$all_location = $db->query('SELECT id, location_code FROM tb_bin_location_bac  ORDER BY location_code ASC ')->fetch_all();
?>
<?php

if (isset($_POST['btn_report'])) {
  $lpn = $_POST['s_lpn'];
  $inbound_lpn = get_bet_lpn($db, $lpn);

  
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
                <h4 class="card-title">Update Bin Location</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="inventory_update_bin_loc" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">
                          <input list="search" placeholder="Select/Enter LPN" name="s_lpn" class="form-control" title="Start Date" required>
                          <datalist id="search">
                            <?php foreach ($get_lpn as $db_id => $db_det) { ?>
                              <option value="<?php echo $db_det['lpn']; ?>"> <?php echo $db_det['lpn']; ?> </option>
                            <?php } ?>
                          </datalist>
                        </div>

                        <div class="col-lg-4">
                          <button type="submit" class="btn btn-primary" name="btn_report">Search LPN</button>
                        </div>
                      </div>

                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php  if(!empty($inbound_lpn)) { ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Update Bin Location</h4>

                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="example4" class="display">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead class="fixed_header" id="fixed_header">
                        <tr>

                          <th class=" text-center font-weight-bold ">Id</th>
                          <th class=" text-center font-weight-bold ">LPN</th>
                          <th class=" text-center font-weight-bold ">Mat. #</th>
                          <th class=" text-center font-weight-bold ">Expiry</th>
                          <th class=" text-center font-weight-bold ">Qty Case</th>
                          <th class=" text-center font-weight-bold ">Allocated Case</th>
                          <th class=" text-center font-weight-bold ">Bin Location</th>
                          <th class=" text-center font-weight-bold "></th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($inbound_lpn as $inbound_key => $lpn_det) {
                        ?>
                          <tr>


                            <td class="text-center "><?php echo $lpn_det['id']; ?></td>
                            <td class="text-center "><?php echo $lpn_det['lpn']; ?></td>
                            <td class="text-center "><?php echo $lpn_det['sku_code']; ?></td>
                            <td class="text-center "><?php echo $lpn_det['expiry']; ?></td>
                            <td class="text-center "><?php echo $lpn_det['qty_case']; ?></td>
                            <td class="text-center "><?php echo $lpn_det['allocated_qty']; ?></td>
                            <td class="text-center "><?php echo $lpn_det['actual_bin_loc']; ?></td>
                            <td class="align-middle text-center"> <a target="" data-toggle="modal" data-target="#update_details<?php echo $lpn_det['id']; ?>" href="" class="btn btn-outline-primary btn-md" title="Update Details">Update</a></td>
                            <!-- MODAL FOR TIME RECEIVED-->
                            <div id="update_details<?php echo $lpn_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                              <div role="document" class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 id="exampleModalLabel" class="modal-title">Transfer Location</h4>

                                  </div>
                                  <div class="modal-body">
                                    <form action="update_bin_loc_proc" method="post">
                                      <div class="form-group">
                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="lpn" id="lpn" value="<?php echo $lpn_det['lpn']; ?>">
                                        </div>

                                        <!-- CURRENT LOCATION DISABLED -->
                                        <div class="mt-1">
                                          <label for="old_loc" class="form-control-label text-uppercase text-primary font-weight-bold">CURRENT LOCATION</label>
                                          <input type="text" name="old_loc" id="old_loc" class="form-control" value="<?php echo $lpn_det['bin_loc']; ?>">
                                        </div>

                                        <!-- CURRENT LOCATION HIDDEN -->
                                        <div class="mt-1">
                                          <input type="hidden" name="old_location" id="old_location" value="<?php echo $lpn_det['bin_loc']; ?>">
                                        </div>

                                        <!-- NEW LOCATION -->
                                        <div class="mt-1">
                                          <label for="new_location" class="form-control-label text-uppercase text-primary font-weight-bold">SELECT NEW LOCATION</label>

                                          <input list="new_location_id" name="new_location_id" class="form-control" placeholder="Enter/Select Location">
                                          <datalist id="new_location_id">
                                            <option value="">Select New Location</option>
                                            <?php foreach ($all_location as $db_id => $db_det) { ?>
                                              <option value="<?php echo $db_det['location_code']; ?>"> <?php echo $db_det['location_code']; ?> </option>
                                            <?php } ?>
                                          </datalist>
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
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <?php if (!empty($_POST['doc'])  && isset($_POST['btn_report']) && empty($inbound_fullfillment)) { ?>
          <div class="container-fluid">
            <!-- row -->
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Inbound Fullfillment Report</h4>
                  </div>
                  <div class="card-body">
                    <h4>No Inbound Transaction</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
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
      $('#view_report_table').DataTable({
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
  <script>
    (function() {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function(form) {
          form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })
    })()
  </script>
</body>

</html>