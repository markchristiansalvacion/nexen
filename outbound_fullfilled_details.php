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
$get_fullfilled_outbound = $db->query('SELECT 
a.id,
a.document_no,
a.item_code,
a.batch_no,
a.qty_pcs,
a.date_time,
a.fullfilled_by,
b.destination_code,
b.eta,
c.branch_name,
d.material_description
 From tb_fullfillment a 
INNER JOIN tb_outbound b  ON b.document_no = a.document_no
INNER JOIN tb_branches c ON c.branch_code = b.destination_code
INNER JOIN tb_items d ON d.item_code = a.item_code
WHERE a.transaction_type=? and a.document_no=? GROUP BY batch_no', "Outbound", $_GET['document_no'])->fetch_all();

//print_r_html($get_fullfilled_outbound);
foreach ($get_fullfilled_outbound as $arr_key => $arr_val) {
  $doc = $arr_val['document_no'];
  $branch = $arr_val['branch_name'];
  $fullfilled = $arr_val['fullfilled_by'];
  $date = $arr_val['eta'];
  $datetime = $arr_val['date_time'];
  $masked_doc =  substr($doc, 10, 20);
}
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
                <h4 class="card-title">Fullfilled Details</h4>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="transaction_type" class="form-control-label  text-primary font-weight-bold ">Document No:</label>
                      <label for="transaction_type" class="form-control-label  font-weight-bold "><?php echo $doc; ?></label>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="transaction_type" class="form-control-label  text-primary font-weight-bold ">Reference #:</label>
                      <label for="transaction_type" class="form-control-label  font-weight-bold "><?php echo  "#OUT000-" . $masked_doc; ?></label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="source" class="form-control-label  text-primary font-weight-bold ">Vendor/Source:</label>
                      <label for="source" class="form-control-label  font-weight-bold "><?php echo $branch; ?></label>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="source" class="form-control-label  text-primary font-weight-bold ">Fullfilled By:</label>
                      <label for="source" class="form-control-label  font-weight-bold "><?php echo $fullfilled; ?></label>
                    </div>
                  </div>

                  <div class="row">
                    <!-- ETA -->
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="eta" class="form-control-label text-primary font-weight-bold ">Date Received: </label>
                      <label for="eta" class="form-control-label font-weight-bold "><?php echo $date; ?></label>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="eta" class="form-control-label text-primary font-weight-bold ">Date and Time Fullfilled: </label>
                      <label for="eta" class="form-control-label font-weight-bold "><?php echo $datetime; ?> </label>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Item Details</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>
                        <th class="align-middle text-center font-weight-bold ">Item Code</th>
                        <th class="align-middle text-center font-weight-bold ">Item Description</th>
                        <th class="align-middle text-center font-weight-bold ">Batch No</th>
                        <th class="align-middle text-center font-weight-bold ">Qty</th>

                      </tr>
                    </thead>
                    <?php foreach ($get_fullfilled_outbound as $arr_key => $arr_val) { ?>
                      <tr>

                        <td class="align-middle text-center"><?php echo $arr_val['item_code']; ?></td>
                        <td class="align-middle text-center"><?php echo $arr_val['material_description']; ?></td>
                        <td class="align-middle text-center"><?php echo $arr_val['batch_no']; ?></td>
                        <td class="align-middle text-center"><?php echo $arr_val['qty_pcs']; ?></td>


                      </tr>
                    <?php
                    }
                    ?>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
      $('#view_fullfilled_table').DataTable({
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
</body>

</html>