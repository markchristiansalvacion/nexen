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
<?php
$get_fullfilled_inbound = $db->query('SELECT a.ref_no,a.document_no,a.item_code,a.batch_no,a.qty_pcs,a.lpn, a.transaction_type,a.fullfilled_by,a.date_time,d.date_created,d.vendor_code,c.vendor_name,e.material_description,a.ref_no as asn_ref
FROM  tb_fullfillment a
INNER JOIN tb_inbound d ON d.document_no = a.document_no
INNER JOIN tb_items e On e.item_code = a.item_code
INNER JOIN tb_vendor c ON c.vendor_id = d.vendor_code where a.document_no=? and a.transaction_type=? GROUP BY batch_no', $_GET['document_no'], "Inbound")->fetch_all();

//print_r_html($get_fullfilled_inbound);
foreach ($get_fullfilled_inbound as $f_key => $f_val) {
  $doc = $f_val['document_no'];
  $ref = $f_val['asn_ref'];
  $vendor = $f_val['vendor_name'];
  $fullfilled = $f_val['fullfilled_by'];
  $date = $f_val['date_created'];
  $datetime = $f_val['date_time'];
}
?>
<?php include 'views/header.php'; ?>
<?php include 'views/nav_header.php'; ?>
<?php include 'views/top_bar.php'; ?>?>




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
                      <label for="transaction_type" class="form-control-label  font-weight-bold "><?php echo "INB -" . $ref; ?></label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                      <label for="source" class="form-control-label  text-primary font-weight-bold ">Vendor/Source:</label>
                      <label for="source" class="form-control-label  font-weight-bold "><?php echo $vendor; ?></label>
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
                        <th class="align-middle text-center  font-weight-bold ">Item Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Item Description</th>
                        <th class="align-middle text-center  font-weight-bold ">Batch No</th>
                        <th class="align-middle text-center  font-weight-bold ">Qty</th>

                      </tr>
                    </thead>
                    <?php foreach ($get_fullfilled_inbound as $arr_key => $arr_val) { ?>
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

        <!--**********************************
					Footer start
				***********************************-->

        <!--**********************************
					Footer end
				***********************************-->
      </div>
    </div>
    <!--**********************************
            Content body end
        ***********************************-->
    <!--**********************************
           Support ticket button start
        ***********************************-->

    <!--**********************************
           Support ticket button end
        ***********************************-->


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