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
if (isset($_POST)) {
  $scan = $_POST['scan_asn'];

  //$inbound_trans = get_bet_inbound($db, $start_date, $end_date);
  $inbound_asn = get_bet_inbound_asn($db, $scan);

  // print_r_html($inbound_asn);
} ?>


<body>
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
          <div class="col-lg-4">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Scan ASN Barcode</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="index_main_guard" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-12">

                          <input placeholder="Scan Here" name="scan_asn" class="form-control" required autofocus>
                        </div>

                        <!-- <div class="col-lg-4">
                          <button type="submit" class="btn btn-primary" name="btn_report">Generate Summary Report</button>
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
      <?php if (!empty($inbound_asn)) { ?>

        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4">
              <?php foreach ($inbound_asn as $inbound_key => $inbound_det) { ?>
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">ASN Details</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-validation">
                      <form class="needs-validation" novalidate method="POST" action="update_asn">
                        <div class="row">
                          <div class="col-xl-12">
                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom05">ASN Ref No.:
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="text" class="form-control" id="ref" name="ref" value="<?php echo "#ASN -" . $inbound_det['ref_no']; ?>" required>

                              </div>
                              <div class="col-lg-8">
                                <input type="hidden" class="form-control" id="ref_no" name="ref_no" value="<?php echo  $inbound_det['ref_no']; ?>" required>

                              </div>
                            </div>

                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom03">Document No.:
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?php echo $inbound_det['document_no']; ?>" required>

                              </div>
                            </div>
                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom03">Call Time:
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="time" class="form-control" id="call_time" name="call_time" value="<?php echo $inbound_det['time_slot']; ?>" required>

                              </div>
                            </div>
                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom03">ATA
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="date" class="form-control" id="ata" name="ata" value="<?php echo $inbound_det['ATA']; ?>" required>

                              </div>
                            </div>


                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom05">Plate No.:
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="text" class="form-control" id="plate" name="plate" value="<?php echo $inbound_det['plate_no']; ?>" required>
                              </div>
                            </div>
                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom03">Docking Bay
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="text" class="form-control" name="dock" id="dock" value="<?php echo $inbound_det['loading_bay']; ?>" required>

                              </div>
                            </div>

                            <div class="mb-3 row">
                              <label class="col-lg-4 col-form-label" for="validationCustom03">Departed Time
                                <span class="text-danger">*</span>
                              </label>
                              <div class="col-lg-8">
                                <input type="time" class="form-control" id="depart" name="depart" value="<?php echo $inbound_det['time_departed']; ?>" required>

                              </div>
                            </div>

                            <div class="mb-3 row">
                              <div class="col-lg-8 ms-auto">
                                <button type="submit" class="btn btn-success">Confirm Transaction</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>

      <?php } else { ?>
        <?php if (!empty($_POST['scan_asn'])  && empty($inbound_scan)) { ?>
          <div class="container-fluid">
            <!-- row -->
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">ASN Transaction</h4>
                  </div>
                  <div class="card-body">
                    <h4>No Transaction</h4>
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