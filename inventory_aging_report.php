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
$get_aging_fefo = $db->query('SELECT tb_inbound.item_code,
                      tb_inbound.batch_no,  
                      tb_items.material_description,
                      tb_inbound.qty_pcs,
                      tb_inbound.expiry,
                     TIMESTAMPDIFF(MONTH,now(), tb_inbound.expiry) as Shelf,
                    DATEDIFF(tb_inbound.expiry,now()) as freshness
                    FROM tb_inbound
                    INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code 
                    WHERE  tb_inbound.expiry <> 0000-00-00 ORDER BY ref_no ')->fetch_all();
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
                <h4 class="card-title">Aging Report</h4>
                <form action="export_aging_report" method="post" name="export_excel">
                  <div class="control-group col-lg-12 text-right">
                    <input type="submit" name="export" value="Export Data" class="btn btn-info" />
                  </div>

                </form>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="view_asn_table"> -->
                    <thead>
                      <tr>

                        <th class=" text-center  font-weight-bold ">Item Code</th>
                        <th class=" text-center  font-weight-bold ">Batch No</th>
                        <th class=" text-center  font-weight-bold ">Item Name</th>
                        <th class=" text-center  font-weight-bold ">Qty</th>
                        <th class=" text-center  font-weight-bold ">Expiry Date </th>

                        <th class=" text-center  font-weight-bold ">Remaining Shelf Life</th>
                        <th class=" text-center  font-weight-bold ">Shelf Freshness</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_aging_fefo as $key_val => $arr_val) { ?>
                        <tr>

                          <td class=" text-center"><?php echo $arr_val['item_code']; ?></td>
                          <td class=" text-center"><?php echo $arr_val['batch_no']; ?></td>
                          <td class=" text-center"><?php echo $arr_val['material_description']; ?></td>
                          <td class=" text-center"><?php echo $arr_val['qty_pcs']; ?></td>

                          <td class=" text-center "><?php echo $arr_val['expiry']; ?></td>

                          <td class=" text-center ">
                            <?php
                            if ($arr_val['Shelf'] > 100) {
                              echo "<p style='color:green; font-weight:bold!important;'>" . $arr_val['Shelf'] . " Months(s)</p>";
                            }
                            if ($arr_val['Shelf'] < 100) {
                              echo "<p style='color:red;'>" . $arr_val['Shelf'] .  " Months(s)</p>";
                            }
                            ?>
                          </td>
                          <td class=" text-center ">
                            <?php
                            if ($arr_val['freshness'] > 100) {
                              echo "<p style='color:green; font-weight:bold!important;'>" . $arr_val['freshness'] . " Days(s)</p>";
                            }
                            if ($arr_val['freshness'] < 100) {
                              echo "<p style='color:red;'>" . $arr_val['freshness'] .  " Days(s)</p>";
                            }
                            ?>
                          </td>
                        </tr>

                      <?php }
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