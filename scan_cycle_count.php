<?php

require_once 'includes/load.php';

/**
 * Check each script if login is authenticated or if session is already expired
 */

if (is_login_auth()) {

  if (is_session_expired()) {
    $_SESSION['msg'] = "<b>SESSION EXPIRED:</b> Please Login Again.";
    $_SESSION['msg_type'] = "danger";

    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['user_type']);
    unset($_SESSION['user_status']);
    unset($_SESSION['login_time']);

    redirect("login", false);
  }
} else {
  redirect("login", false);
}

?>

<?php

if (isset($_POST['bin_location'])) {

  if (empty(trim($_POST['bin_location']))) {
    echo "<div class = \"container-fluid\">";
    echo "<div class=\"alert alert-danger\">";
    echo "<strong>Error!</strong> Please Fill all Fields";
    echo "</div>";
    echo "</div>";
  } else {

    /**
     * Start and End Date
     * @var [type]
     */
    $bin_loc = $_POST['bin_location'];
    // $item_code = $_POST['item_batch'];

    $inbound_trans = get_bet_loc($db, $bin_loc);
  }
}

?>
<?php
$all_loc = $db->query('SELECT DISTINCT location_code FROM tb_bin_location_bac')->fetch_all();
?>

<?php include 'views/header.php'; ?>
<?php include 'views/nav_header.php'; ?>
<?php include 'views/top_bar.php'; ?>


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
          <div class="col-lg-4">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Daily Cycle Count</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="scan_cycle_count" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-12">
                          <input type="text" class="form-control" name="bin_location" placeholder="Scan/Enter Barcode">
                          <!-- <datalist id="bin_loc">
                            <?php foreach ($all_loc as $item_key => $arr_val) { ?>
                              <option value="<?php echo $arr_val['location_code']; ?>"><?php echo $arr_val['location_code']; ?></option>
                            <?php } ?>
                          </datalist> -->
                        </div>

                        <!-- <div class="col-lg-3">
                          <button type="submit" class="btn btn-primary" name="btn_report">Generate Cycle Count Sheet</button>
                        </div> -->

                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php if (!empty($inbound_trans)) { ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Cycle Count Details</h4>

                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="example4" class="display">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead>
                        <tr>

                          <th style="font-size: 10px!important;">Bin Location</th>
                          <th style="font-size: 10px!important;">Bin Details</th>
                          <!-- <th class=" text-center  font-weight-bold ">Batch No</th>
                          <th class=" text-center  font-weight-bold ">Qty (PCS) IN</th>
                          <th class=" text-center  font-weight-bold ">Qty (PCS) OUT</th>
                          <th class=" text-center  font-weight-bold ">Available (PCS)</th>
                          <th class=" text-center  font-weight-bold ">LPN</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($inbound_trans as $inbound_key => $inbound_det) { ?>
                          <tr>
                            <?php if (array_key_exists('bin_location', $inbound_det)) { ?>
                              <td style="font-size: 10px!important;">
                                <?php echo $inbound_det['bin_location']; ?></th>
                              <?php } else { ?>
                              <td class=" text-center ">Empty</th>
                              <?php } ?>

                              <?php if (array_key_exists('item_code', $inbound_det) && array_key_exists('batch_no', $inbound_det)) { ?>
                              <td style="font-size: 10px!important;">
                                <?php
                                echo "<b>Item Code:</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" . $inbound_det['item_code'] . "</br>";

                                echo "<b>Batch Code:</b> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;" . $inbound_det['batch_no'] . "</br>";

                                echo "<b>Qty Pcs (IN):</b> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $inbound_det['in_qty'] . "</br>";

                                echo "<b>Qty Pcs (OUT):</b> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $inbound_det['dispatch_qty'] . "</br>";

                                echo "<b>Available:</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " . $inbound_det['in_qty'] - $inbound_det['dispatch_qty'] . "</br>";

                                echo "<b>LPN:</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" . $inbound_det['lpn'] . "</br>"; ?></th>
                              <?php } else { ?>
                              <td style="font-size: 10px!important;">Empty</th>
                              <?php } ?>
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
        <?php if (!empty($_POST['doc'])  && isset($_POST['btn_report']) && empty($outbound_fullfillment)) { ?>
          <div class="container-fluid">
            <div class="col-12 h-container">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover table-md" id="stock_in_tbl">
                      <h4>No Outbound Transaction</h4>
                    </table>
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
  <<!-- Required vendors -->
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