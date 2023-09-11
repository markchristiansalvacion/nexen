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
    $inbound_fullfillment = get_fullfillment($db, $doc);
  }
}
?>
<?php $get_doc = $db->query('SELECT document_no FROM tb_inbound GROUP BY document_no')->fetch_all();
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
                <h4 class="card-title">Fullfillment Report</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="inbound_fullfillment_report" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">
                          <input placeholder="Document No" list="doc_no" class="form-control" name="doc" required>
                          <datalist id="doc_no">

                            <?php foreach ($get_doc as $asar_key => $asar_val) { ?>
                              <option value="<?php echo $asar_val['document_no']; ?>"><?php echo $asar_val['document_no']; ?></option>
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
      <?php if (!empty($inbound_fullfillment)) { ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Inbound Fullfillment Report</h4>
                  <div class="col-lg-2 text-right">
                    <form action="export_inbound_fullfillment" method="post" name="export_excel">

                      <div class="control-group">

                        <input type='hidden' name="doc" id="doc_no" value="<?php echo $doc; ?>" />

                        <input type="submit" name="export" value="Export Data" class="btn btn-info" />
                        <a target="_blank" href="<?php echo "print_inbound_fullfillment_report?document_no={$doc}"; ?>" class="btn btn-info">PDF</a>
                      </div>

                    </form>
                  </div>
                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="example4" class="display">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead class="fixed_header" id="fixed_header">
                        <tr>

                          <th class=" text-center px-3 py-2 font-weight-bold ">Source</th>
                          <th class=" text-center px-3 py-2 font-weight-bold ">Document No.</th>
                          <th class=" text-center px-3 py-2 font-weight-bold ">Item Code</th>
                          <th class=" text-center px-3 py-2 font-weight-bold ">Batch No</th>
                          <th class=" text-center px-3 py-2 font-weight-bold ">Item Name</th>
                          <th class=" text-center px-3 py-2 font-weight-bold ">Qty</th>

                          <th class=" text-center px-3 py-2 font-weight-bold ">Date Fullfilled</th>
                          <th class=" text-center px-3 py-2 font-weight-bold ">Fullfilled By</th>


                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($inbound_fullfillment as $inbound_key => $inbound_det) {
                        ?>
                          <tr>
                            <td class=" text-center"><?php echo $inbound_det['vendor_name']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['document_no']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['item_code']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['batch_no']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['material_description']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['qty_pcs']; ?></td>

                            <td class=" text-center"><?php echo $inbound_det['date_time']; ?></td>
                            <td class=" text-center"><?php echo $inbound_det['fullfilled_by']; ?></td>


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