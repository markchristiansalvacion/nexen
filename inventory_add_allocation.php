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

$db_str = $db->query('SELECT DISTINCT
        
        tb_outbound.ref_no , 
        tb_outbound.id, 
        tb_outbound.date,
        tb_outbound.document_name,
        tb_outbound.transaction_type,
        tb_outbound.ship_date,
        tb_outbound.eta
        FROM tb_outbound
        INNER JOIN tb_destination ON tb_destination.destination_code = tb_outbound.destination_code
        INNER JOIN tb_warehouse on tb_warehouse.warehouse_id = tb_outbound.source_code 
        WHERE  tb_outbound.ship_date >= ?
        GROUP BY tb_outbound.document_name', $date_today)->fetch_all();

$db_for_status = $db->query('SELECT *FROM tb_outbound ')->fetch_all();

//print_r_html($db_for_status);

$all_db_for_status = array();

foreach ($db_for_status as $arr_key => $arr_val) {

  $all_db_for_status[$arr_val['document_no']][$arr_val['id']] = $arr_val['status'];
}

// print_r_html($all_db_for_status);



?>
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
                <h4 class="card-title">Load Plan</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center font-weight-bold ">Ref No.</th>
                        <th class="align-middle text-center font-weight-bold ">Upload Date</th>
                        <th class="align-middle text-center font-weight-bold ">Upload File Name</th>
                        <th class="align-middle text-center font-weight-bold ">Ship Date</th>
                        <th class="align-middle text-center font-weight-bold ">ETA</th>
                        <th class="align-middle text-center font-weight-bold "></th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_str as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle text-center "><?php echo "#LP-" . $arr_val['ref_no']; ?></td>
                          <td class="align-middle text-center "><?php echo  date('Y-m-d', $arr_val['date']);
                                                                ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['document_name']; ?></td>
                          <td class="align-middle text-center "><?php echo date('M-d-Y', strtotime($arr_val['ship_date'])); ?></td>
                          <td class="align-middle text-center "><?php echo date('M-d-Y', strtotime($arr_val['eta'])); ?></td>

                          <td class="align-middle text-center">
                            <a target="" href="<?php echo "inventory_allocation_proc?document_name={$arr_val['document_name']}&transaction_type={$arr_val['transaction_type']}" ?>" class="btn
                btn-outline-primary btn-md" title="Update">Enter Load Plan List</i></a>
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

</body>

</html>