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
$get_inbound = $db->query('SELECT a.document_no, a.transaction_type,d.date_received,b.vendor_code,c.vendor_name
FROM  tb_fullfillment a
INNER JOIN tb_asn b ON b.document_no = a.document_no
INNER JOIN tb_inbound d ON d.document_no = a.document_no
INNER JOIN tb_vendor c ON c.vendor_id = b.vendor_code where a.transaction_type=?', "Inbound")->fetch_all();
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
  <!--*******************
        Preloader end
    ********************-->


  <!--**********************************
        Main wrapper start
    ***********************************-->
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
                <h4 class="card-title">Inbound Fullfillment Details</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="view_fullfillment_table" class="display">
                    <thead>
                      <tr>
                        <th class="align-middle text-center ">Document No</th>
                        <th class="align-middle text-center ">Source</th>
                        <th class="align-middle text-center ">Date Received</th>

                        <th class="align-middle text-center ">Action</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_inbound as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle text-center "><?php echo $arr_val['document_no']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['vendor_name']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['date_received']; ?></td>
                          <td class="align-middle text-center ">
                            <a target="" href="<?php echo "inbound_fullfillment_add?ref_no={$arr_val['ref_no']}&document_no={$arr_val['document_no']}" ?>" class="btn  btn-outline-primary btn-md" title="Update"><i class="fas
                              fa-plus"></i></a>
                            <a target="" href="<?php echo "fullfillment_details?document_no={$arr_val['document_no']}" ?>" class="btn  btn-outline-secondary btn-md" title="Update"><i class="fas
                              fa-eye"></i></a>


                          </td>
                        </tr>
                      <?php
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
      $('#view_fullfillment_table').DataTable({
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