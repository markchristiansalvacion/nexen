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

if (isset($_POST['btn_report'])) {

  if (empty(trim($_POST['sku_code']))) {
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
    $sku_code = $_POST['sku_code'];
    // $item_code = $_POST['item_batch'];

    $db_sku_loc = get_sku_location($db, $sku_code);

    // print_r_html($db_sku_loc);
  }
}

?>


<?php include 'views/header.php'; ?>
<?php include 'views/nav_header.php'; ?>
<?php include 'views/top_bar.php'; ?>
<?php
    $tb_items = $db->query('SELECT * FROM tb_items')->fetch_all();
    // print_r_html($tb_items);
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
                <h4 class="card-title">Cycle Count (Per SKU)</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="inventory_daily_cycle_count_per_sku" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-12">
                          <label for="sku_code" class="form-control-label text-uppercase text-primary font-weight-bold">Select SKU</label>
                          <input list="sku_list" class="form-control" name="sku_code" id="sku_code">
                          <datalist id="sku_list">
                            <?php foreach ($tb_items as $item_key => $arr_val) { ?>
                              <option value="<?php echo $arr_val['sap_code']; ?>"><?php echo $arr_val['sap_code'].'-'.$arr_val['material_description']; ?></option>
                            <?php } ?>
                          </datalist>
                        </div>
                      </div>
                      <div class="row mt-3">
                        <div class="col-lg-12">
                          <button type="submit" class="btn btn-lg btn-block btn-primary" name="btn_report">GENERATE SKU LOCATIONS</button>
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
      <?php if (!empty($db_sku_loc)) { ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <div class="col-lg-11 text-right">
                    <h4 class="card-title">Cycle Count Details (per SKU)</h4>
                  </div>
                  <div class="col-lg-1 text-center">
                      <a target="_blank" href="<?php echo "export_count_sheet_per_sku?sku_code={$sku_code}"; ?>"><i class="fa-solid fa-file-csv fa-3x link-success"></i></a>
                        <a target="_blank" href="<?php echo "print_daily_cycle_report_per_sku?sku_code={$sku_code}"; ?>"><i class="fa-solid fa-file-pdf fa-3x link-danger"></i></a>
                  </div>
                </div>

                <div class="card-body">

                  <div class="table-responsive">
                    <table id="example4" class="display">
                      <!-- <table class="table table-bordered table-responsive-sm" id="view_asn_table"> -->
                      <thead>
                        <tr>

                          <th class=" text-center  font-weight-bold ">Location</th>
                          <th class=" text-center  font-weight-bold ">Material Description</th>
                          <th class=" text-center  font-weight-bold ">LPN</th>
                          <th class=" text-center  font-weight-bold ">BBD</th>
                          <th class=" text-center  font-weight-bold ">Qty (PCS) IN</th>
                          <th class=" text-center  font-weight-bold ">Qty (PCS) OUT</th>
                          <th class=" text-center  font-weight-bold ">Available (PCS)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($db_sku_loc as $arr_key => $arr_val) { ?>
                          <tr>
                            <td class="text-center "><?php echo $arr_val['actual_bin_loc']; ?></th>
                            <td class="text-center "><?php echo $arr_val['sku_code'].'-'.$arr_val['material_description']; ?></th>
                            <td class="text-center "><?php echo $arr_val['lpn']; ?></th>
                            <td class="text-center "><?php echo $arr_val['expiry']; ?></th>
                            <td class="text-center "><?php echo $arr_val['qty_case']; ?></th>
                            <td class="text-center "><?php echo $arr_val['allocated_qty']; ?></th>
                            <td class="text-center "><?php echo $arr_val['available_qty']; ?></th>
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