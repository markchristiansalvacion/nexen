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

$db_inbound = $db->query('SELECT 
    a.id,
    a.ref_no,
    a.created_by,
    a.ATA,
    a.date_created,
    a.plate_no,
    a.transaction_type,
    a.document_no,
    a.date_created
 
    FROM tb_inbound a
    
    WHERE status <> 0 and a.checker_name=?', $_SESSION['name'])->fetch_all();

// print_r_html($db_inbound);

$all_inbound = array();

foreach ($db_inbound as $db_key => $db_val) {
  // print_r_html($db_val);
  // $all_asn[$db_val['ref_no']][$db_val['str_no']][$db_key]= $db_val;
  $all_inbound[$db_val['ref_no']][$db_val['document_no']] = $db_val;
}

//print_r_html($all_inbound);

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
                <h6>Incoming Shipment</h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>
                        <th style="font-size: 12px!important;">Details</th>

                        <th class="text-center" style="font-size: 10px!important;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_inbound as $asar_key => $asar_val) {

                        foreach ($asar_val as $arr_val => $arr_det) { ?>
                          <tr>

                            <td style="font-size: 12px!important;">

                              <?php echo "<b>Document No.: </b>  &nbsp; &nbsp; &nbsp; &nbsp; " .  $arr_det['document_no'] . "</br>";
                              echo "<b>Transaction Type: </b>   &nbsp; " .   $arr_det['transaction_type'] . "</br>";
                              echo "<b>Plate No.: </b>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" .   $arr_det['plate_no'] . "</br>"; ?>
                            </td>


                            <td class="text-center" style="font-size: 10px!important;">

                              <a target="_blank" href="<?php echo "view_to_receive?document_no={$arr_det['document_no']}&ref_no={$arr_det['ref_no']}" ?>" class="btn btn-outline-primary btn-sm" style="font-size: 10px!important;"><i class="fa fa-eye"></i></a>
                            </td>

                          </tr>
                      <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
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
      $('#view_inbound_table').DataTable({
        order: [
          [5, "desc"]
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