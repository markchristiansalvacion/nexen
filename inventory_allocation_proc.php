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
                <h4 class="card-title">Allocate Load Plan</h4>
              </div>
              <div class="card-body">
                <?php
                $get_branch = $db->query('SELECT 
                  a.truck_allocation,
                  a.document_no,
                  a.document_name,
                  a.transaction_type,
                  a.allocation,
                  a.status,
                  b.branch_name FROM tb_outbound a
                  INNER JOIN tb_branches b ON b.branch_code = a.destination_code
                  WHERE document_name=? AND transaction_type=? GROUP BY a.truck_allocation ', $_GET['document_name'], $_GET['transaction_type'])->fetch_all();
                ?>
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Branch Name</th>
                        <th class="align-middle text-center  font-weight-bold ">Truck Allocation</th>
                        <th class="align-middle text-center  font-weight-bold ">Transaction Type</th>

                        <th class="align-middle text-center  font-weight-bold ">Status</th>
                        <th class="align-middle text-center  font-weight-bold ">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_branch as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle text-center "><?php echo  $arr_val['branch_name']; ?></td>
                          <td class="align-middle text-center "><?php echo  $arr_val['truck_allocation']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['transaction_type']; ?></td>

                          <td class="  text-center align-middle">
                            <?php
                            if ($arr_val['allocation'] == 'YES') {
                              echo "Allocated";
                            }
                            if ($arr_val['allocation'] == 'NO') {
                              echo "For Allocation";
                            }

                            ?></td>
                          <!-- <td>
                            <div class="dropdown">
                              <button type="button" class="btn btn-success light sharp" data-bs-toggle="dropdown">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                  </g>
                                </svg>
                              </button>
                              <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo "check_stock?document_no={$arr_val['document_no']}" ?>">Check Stock</a>
                                <a class="dropdown-item" href="<?php echo "inventory_coload_allocation?truck_allocation={$arr_val['truck_allocation']}&transaction_type={$arr_val['transaction_type']}&branch={$arr_val['branch_name']}" ?>">Go to Allocation</a>


                              </div>
                            </div>
                          </td> -->
                          <td class="align-middle text-center">

                            <a target="" href="<?php echo "inventory_coload_allocation?truck_allocation={$arr_val['truck_allocation']}&transaction_type={$arr_val['transaction_type']}&branch={$arr_val['branch_name']}" ?>" class="btn  btn-outline-primary btn-md " title="Update">Enter to Allocate</i></a>

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