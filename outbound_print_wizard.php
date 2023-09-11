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

$date_today = date('Y-m-d');

$db_str = $db->query('SELECT
a.id,
a.ref_no,
a.document_no,
a.document_name,
a.ship_date,
a.transaction_type,
a.eta,
a.truck_allocation,
a.status,
a.picking_start,
a.picking_end,
a.checking_start,
a.checking_end,
a.validating_start,
a.validating_end,
a.checker,
a.picker,
a.validator,
b.destination_name

FROM tb_outbound a  
INNER JOIN tb_destination b ON b.destination_code = a.destination_code
WHERE document_no =? AND ref_no=? GROUP BY document_no', $_GET['document_no'], $_GET['ref'])->fetch_all();
?>




<body>

  <!--*******************
        Preloader start
    ********************-->

  <div id="preloader">
    <div class="loader"></div>
  </div>

  <div id="main-wrapper">

    <div class="content-body">
      <div class="container-fluid">
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Print Wizard</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="view_asn_table" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="view_asn_table"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center ">Print Forms</th>
                        <th class="align-middle text-center ">Print Picking Tag</th>
                        <th class="align-middle text-center ">Print Delivery Receipt</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_str as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="  text-center align-middle">
                            <a target="_blank" href="<?php echo "print_picklist?truck_allocation={$arr_val['truck_allocation']}&document_no={$arr_val['document_no']}&ref_no={$arr_val['ref_no']}" ?>" class="btn
                btn-outline-primary btn-lg" title="Edit"><i class="fas fa-print"></i></a>
                          </td>
                          <td class="  text-center align-middle">
                            <a target="_blank" href="<?php echo "print_picking_tag?document_no={$arr_val['document_no']}&ref_no={$arr_val['ref_no']}&transaction_type={$arr_val['transaction_type']}&destination={$arr_val['destination_name']}" ?>" class="btn
                btn-outline-primary btn-lg" title="Edit"><i class="fas fa-print"></i></a>
                          </td>
                          <td class="  text-center align-middle">
                            <a target="_blank" href="<?php echo "print_dr?truck_allocation={$arr_val['truck_allocation']}&document_no={$arr_val['document_no']}&ref_no={$arr_val['ref_no']}" ?>" class="btn
                btn-outline-primary btn-lg" title="Edit"><i class="fas fa-print"></i></a>
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
  </div>
  </div>

  </div>


  <!--**********************************
        Scripts
    ***********************************-->

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
      $('#view_asn_table').DataTable({
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