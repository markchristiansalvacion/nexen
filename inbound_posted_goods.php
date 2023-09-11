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
    a.transaction_type,
    a.document_no
    FROM tb_inbound a
    
    WHERE status=?', '1')->fetch_all();

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
                <h4 class="card-title">Inbound Posted Goods</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Ref No</th>

                        <th class="align-middle text-center ">Document No</th>
                        <th class="align-middle text-center ">Transaction Type</th>
                        <th class="align-middle text-center ">Created By</th>
                        <th class="align-middle text-center ">Inbound Date</th>
                        <th class="align-middle text-center ">View</th>


                      </tr>
                    </thead>
                    <?php foreach ($all_inbound as $asar_key => $asar_val) {

                      foreach ($asar_val as $arr_val => $arr_det) { ?>
                        <tr>
                          <td class="align-middle text-center " style="font-weight:bold;"><?php echo "#PGR-" . $arr_det['ref_no']; ?></td>

                          <td class="align-middle text-center "><?php echo $arr_det['document_no']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['transaction_type']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['created_by']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['ATA']; ?></td>
                          <!-- <td>
                                            <a target="" href="<?php echo "add_incident_report?asn_ref={$arr_det['ref_no']}&source_doc={$arr_det['document_no']}" ?>" class="btn btn-outline-primary btn-sm" title="Create Incident Report"><i class="fas fa-plus"></i></a>
                                        </td> -->

                          <td class="align-middle text-center ">
                            <a target="_blank" href="<?php echo "inbound_posted_goods_details?document_no={$arr_det['document_no']}&ref_no={$arr_det['ref_no']}" ?>" class="btn btn-outline-primary btn-sm" title="View Items"><i class="fas fa-eye"></i></a>
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