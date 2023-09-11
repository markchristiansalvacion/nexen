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

<body>
  <?php
  if (isset($_POST['btn_report'])) {
    $brcd = $_POST['scan_barcode'];

    //$inbound_trans = get_bet_inbound($db, $brcd, $end_date);
    $get_loc = get_bet_get_loc($db, $brcd);
  } ?>

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
                <h4 class="card-title">Details</h4>

              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="view_bin_location" method="post" class="needs-validation" novalidate>
                    <div class="row">
                      <div class="col-xl-4">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom02">Select Bin Location <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-8">
                            <input type="text" class="form-control" placeholder="Scan here" id="scan_barcode" name="scan_barcode" required autofocus />

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <div class="col-lg-8 ms-auto">

                            <button type="submit" class="btn btn-success" name="btn_report">Confirm Transaction</button>
                          </div>
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
      <?php if (!empty($get_loc)) {
      ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Location Details</h4>

                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="example4" class="display">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead>
                        <tr>
                          <th class="align-middle text-center ">Details</th>
                          <th class="align-middle text-center "></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($get_loc as $inbound_key => $inbound_det) {
                        ?>
                          <tr>
                            <td><?php
                                echo "<b>Item Code:</b>" . "     " . $inbound_det['item_code'] . "</br>";
                                echo "<b>Batch No.:</b>" . "     " . $inbound_det['batch_no'] . "</br>";
                                echo "<b>Qty:</b>" . "     " . $inbound_det['qty_pcs'] . "</br>";
                                echo "<b>Location:</b>" . "     " . $inbound_det['bin_location'] . "</br>";
                                echo "<b>Expiry:</b>" . "     " . $inbound_det['expiry'] . "</br>";
                                echo "<b>LPN:</b>" . "     " . $inbound_det['lpn'] . "</br>";   ?></td>
                            <td>
                              <a target="" data-toggle="modal" data-target="#update_inbound<?php echo $inbound_det['id']; ?>" href="" class="btn btn-outline-warning btn-md" title="Scan Barcode"><i class="fas fa-barcode"></i></a>
                            </td>
                            <div id="update_inbound<?php echo $inbound_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                              <div role="document" class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 id="exampleModalLabel" class="modal-title">Scan Location</h4>

                                  </div>
                                  <div class="modal-body">
                                    <form action="scan_to_new_location" method="post">
                                      <div class="form-group">

                                        <!-- URL-->
                                        <div class="mt-1">
                                          <input type="hidden" name="url" id="url" value="<?php echo "view_bin_location?id={$inbound_det['id']}"; ?>">
                                        </div>

                                        <!-- ID -->
                                        <div class="mt-1">
                                          <input type="hidden" name="db_id" id="db_id" value="<?php echo $inbound_det['id']; ?>">
                                        </div>
                                        <!-- ID -->
                                        <div class="mt-1">
                                          <input type="hidden" name="lpn" id="lpn" value="<?php echo $inbound_det['lpn']; ?>">
                                        </div>
                                        <!-- ID -->
                                        <div class="mt-1">
                                          <input type="hidden" name="loc" id="loc" value="<?php echo $inbound_det['bin_location']; ?>">
                                        </div>

                                        <!-- BATCH NO -->

                                        <div class="mt-2">
                                          <label class="col-lg-4 col-form-label" for="validationCustom05">Select Transactio Type
                                            <span class="text-danger">*</span>
                                          </label>
                                          <div class="col-lg-6">
                                            <select class="default-select wide form-control" id="trans_type" name="trans_type" required>
                                              <option data-display="Select">Please Select</option>
                                              <option value="Stock Transfer">Stock Transfer</option>
                                              <option value="Replenishment">Replenishment</option>


                                            </select>
                                          </div>
                                          <div class="mt-2">
                                            <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Scan New Location</label>
                                            <input type="text" name="s_new_location" id="s_new_location" placeholder="Enter/Scan Barcode" class="form-control" autofocus>
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                                          <button type="submit" class="btn btn-primary">Confirm</button>
                                        </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>



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
        <?php if (!empty($_POST['brcd']) && empty($get_loc)) { ?>
          <div class="container-fluid">
            <!-- row -->
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Inbound Summary Report</h4>
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