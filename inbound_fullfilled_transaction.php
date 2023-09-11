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

$get_inbound = $db->query('SELECT 
a.document_no,
a.transaction_type,
b.date_created,
b.vendor_code,
c.vendor_name
FROM tb_fullfillment a 
INNER JOIN tb_inbound b on b.document_no = a.document_no
INNER JOIN tb_vendor c on c.vendor_id = b.vendor_code
WHERE a.transaction_type=? GROUP BY a.document_no', "Inbound")->fetch_all();
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
                <h4 class="card-title">Inbound Fullfilled Transaction</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Document No</th>
                        <th class="align-middle text-center  font-weight-bold ">Transaction Type</th>
                        <th class="align-middle text-center  font-weight-bold ">Source</th>
                        <th class="align-middle text-center  font-weight-bold ">Date Received</th>

                        <th class="align-middle text-center  font-weight-bold ">View</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_inbound as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle text-center "><?php echo $arr_val['document_no']; ?></td>

                          <td class="align-middle text-center "><?php echo $arr_val['transaction_type']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['vendor_name']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['date_created']; ?></td>


                          <td class="align-middle text-center ">

                            <a target="" href="<?php echo "inbound_fullfilled_details?document_no={$arr_val['document_no']}" ?>" class="btn  btn-outline-primary btn-md" title="Update"><i class="fas
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