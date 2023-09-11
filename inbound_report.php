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
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  $inbound_trans = get_bet_inbound($db, $start_date, $end_date);
} ?>

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
                <h4 class="card-title">Inbound Report</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="inbound_report" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">

                          <input placeholder="Select Date Start" name="start_date" class="form-control" onmouseover="(this.type='date')" data-toggle="tooltip" data-placement="top" title="Start Date" required>
                        </div>
                        <div class="col-lg-4">
                          <input placeholder="Select Date End" name="end_date" class="form-control" onmouseover="(this.type='date')" data-toggle="tooltip" data-placement="top" title="End Date" required>
                        </div>
                        <div class="col-lg-4">
                          <button type="submit" class="btn btn-primary" name="btn_report">Generate Summary Report</button>
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
      <?php if (!empty($inbound_trans)) { ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Inbound Summary Report</h4>
                  <div class="col-lg-2 text-right">
                    <form action="export_inbound_report" method="post" name="export_excel">

                      <div class="control-group">

                        <input type='hidden' name="start_date" id="start_date" value="<?php echo $start_date; ?>" />
                        <input type='hidden' name="end_date" id="end_date" value="<?php echo $end_date; ?>" />
                        <input type="submit" name="export" value="Export Data" class="btn btn-info" />
                        <a target="_blank" href="<?php echo "print_inbound_report?start_date={$start_date}&end_date={$end_date}"; ?>" class="btn btn-info">PDF</a>
                      </div>

                    </form>
                  </div>
                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="example4" class="display" style="min-width: 845px">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead>
                        <tr>

                          <th class="align-middle text-center ">Document No</th>
                          <th class="align-middle text-center ">Item Code</th>
                          <th class="align-middle text-center ">Batch No</th>
                          <th class="align-middle text-center ">Item Name</th>
                          <th class="align-middle text-center ">Qty(PCS)</th>
                          <th class="align-middle text-center ">Expiration</th>
                          <th class="align-middle text-center ">Location</th>
                          <th class="align-middle text-center ">Date Received</th>


                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($inbound_trans as $inbound_key => $inbound_det) { ?>
                          <tr>

                            <td class=" text-center"><?php echo $inbound_det['document_no']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['item_code']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['batch_no']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['material_description']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['qty_pcs']; ?></td>
                            <td class=" text-center "><?php echo $inbound_det['expiry']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['bin_location']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['date_created']; ?></td>

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
        <?php if (!empty($_POST['start_date']) && !empty($_POST['end_date']) && isset($_POST['btn_report']) && empty($inbound_trans)) { ?>
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