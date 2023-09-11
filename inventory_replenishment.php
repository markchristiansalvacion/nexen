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
$db_str = $db->query('SELECT * FROM tb_replenishment')->fetch_all();
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
                <h4 class="card-title">Replenishment List</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table id="view_fullfillment_table" class="display" style="min-width: 845px"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center ">In ID</th>
                        <th class="align-middle text-center ">Item Code</th>

                        <th class="align-middle text-center ">New Location</th>

                        <th class="align-middle text-center ">Replenishment Date and Time</th>
                        <th class="align-middle text-center ">Print Form</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_str as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle text-center "><?php echo "LP-" . $arr_val['in_id'];
                                                                ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['item_code'];

                                                                ?></td>

                          <td class="align-middle text-center "><?php echo $arr_val['source_loc']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['replen_date'];
                                                                ?></td>
                          <td class="align-middle text-center">
                            <a target="_blank" href="<?php echo "print_replenishment?id={$arr_val['id']}&item_code={$arr_val['item_code']}&source_loc={$arr_val['source_loc']}" ?>" class="btn btn-outline-secondary btn-md" title="Print Replenpishment Form"><i class="fas fa-print"></i></a>
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