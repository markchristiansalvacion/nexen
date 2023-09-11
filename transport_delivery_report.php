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

if (isset($_POST['btn_report'])) {
  //print_r_html($_POST);

  if (empty(trim($_POST['doc']))) {
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
    $doc = $_POST['doc'];


    //$inbound_trans = get_bet_inbound($db, $start_date, $end_date);
    //$inbound_fullfillment = get_fullfillment($db, $doc);
    $dispatch_report = get_delivery($db, $doc);
  }
}
?>
<?php $get_doc = $db->query('SELECT document_name FROM tb_outbound GROUP BY document_name')->fetch_all();
?>


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
                <h4 class="card-title">Delivery Monitoring Report</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="transport_delivery_report" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">
                          <input placeholder="Search Load Plan" list="doc_no" class="form-control" name="doc" required>
                          <datalist id="doc_no">

                            <?php foreach ($get_doc as $asar_key => $asar_val) { ?>
                              <option value="<?php echo $asar_val['document_name']; ?>"><?php echo $asar_val['document_name']; ?></option>
                            <?php } ?>
                          </datalist>
                        </div>

                        <div class="col-lg-4">
                          <button type="submit" class="btn btn-primary" name="btn_report">Generate Report</button>
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
      <?php if (!empty($dispatch_report)) { ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Delivery Monitoring Report</h4>
                  <div class="col-lg-1 text-right">
                    <form action="export_delivery_report" method="post" name="export_excel">

                      <div class="control-group">

                        <input type='hidden' name="doc" id="doc_no" value="<?php echo $doc; ?>" />
                        <input type="submit" name="export" value="Export Data" class="btn btn-info" />

                      </div>

                    </form>
                  </div>
                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="view_report_table" class="display">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead class="fixed_header" id="fixed_header">
                        <tr>

                          <th class="align-middle  text-center  font-weight-bold ">Ref No</th>
                          <th class="align-middle  text-center  font-weight-bold ">Destination</th>
                          <th class="align-middle  text-center  font-weight-bold ">Document No</th>
                          <th class="align-middle  text-center  font-weight-bold ">Branch Receved Date</th>
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
                        <?php foreach ($dispatch_report  as $outbound_key => $outbound_det) {
                        ?>
                          <tr>
                            <td class=" text-center" style="font-weight:bold;"><?php echo "#DIF-" . $outbound_det['source_ref']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['branch_name']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['document_no']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['branch_received_date']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['received_by']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['ir_ref_no']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['ir_remarks']; ?></td>
                            <td class=" text-center "><?php echo $outbound_det['rr_ref_no']; ?></td>
                            <td class=" text-center "><?php echo $outbound_det['truck_arrival']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['branch_in']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['branch_out']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['fds_comp']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['window_comp']; ?></td>
                            <td class=" text-center"><?php echo $outbound_det['in_full']; ?></td>

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
        <?php if (!empty($_POST['doc'])  && isset($_POST['btn_report']) && empty($dispatch_report)) { ?>
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