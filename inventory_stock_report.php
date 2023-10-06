  <?php

require_once 'includes/load.php';

/**
 * Check each script if login is authenticated or if session is already expired
 */

if (is_login_auth()) {

  if (is_session_expired()) {
    $_SESSION['msg'] = "<b>SESSION EXPIRED:</b> Please Login Again.";
    $_SESSION['msg_type'] = "danger";

    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['user_type']);
    unset($_SESSION['user_status']);
    unset($_SESSION['login_time']);

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

      <?php

        /**
         * DAILY INVENTORY REPORT
         */

        // $get_all_items = $db->query('SELECT * FROM tb_items')->fetch_all();

        // // /print_r_html($get_all_items);

        // $dir_arr = array();


        // foreach ($get_all_items as $arr_key => $arr_val) {

        //   $get_records = $db->query('SELECT 
        //   tb_inbound.item_code, 
        //   SUM(tb_inbound.in_qty) as received,
        //   SUM(tb_inbound.allocated_qty) as allocated, 
        //   SUM(tb_inbound.dispatch_qty) as dispatched, 
        //   tb_items.material_description 
        //   FROM tb_inbound 
        //   INNER JOIN tb_items ON tb_items.sap_code = tb_inbound.item_code
        //   WHERE tb_inbound.item_code = ? ', $arr_val['item_code'])->fetch_array();

        //   print_r_html($get_records);

        //   if (!is_array_has_empty_input($get_records)) {

        //     $dir_arr[$arr_val['item_code']] = $get_records;
        //   } else {

        //     $dir_arr[$arr_val['item_code']]['item_code'] = $arr_val['item_code'];
        //     $dir_arr[$arr_val['item_code']]['received'] = 0;
        //     $dir_arr[$arr_val['item_code']]['allocated'] = 0;
        //     $dir_arr[$arr_val['item_code']]['dispatched'] = 0;
        //     $dir_arr[$arr_val['item_code']]['material_description'] = $arr_val['material_description'];
        //   }
        // }

        $db_inventory_report = $db->query('SELECT
        a.sku_code,
        tb_items.material_description,
        SUM(a.qty_case - a.allocated_qty) AS SOH,
        a.expiry AS exp_date
        -- tb_items.shelf_life,
        -- DATEDIFF(a.expiry,CURDATE()) AS days_to_expiry,
        -- DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) AS shelf_life_percentage
        FROM tb_inventory_adjustment a 
        INNER JOIN tb_items ON tb_items.sku_code = a.sku_code
        WHERE a.transaction_type = "INB"
        GROUP BY a.sku_code
        ORDER BY SOH ASC')->fetch_all();

        // print_r_html($db_inventory_report);                  


      ?>
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="col-lg-11 text-right">
                    <h4 class="card-title">Daily Inventory Report</h4>
                </div>
                <div class="col-lg-1 text-center">
                       <a target="_blank" href="<?php echo "export_inventory_stock?export_dir=1"; ?>"><i class="fa-solid fa-file-csv fa-3x link-success"></i></a>
                      <a target="_blank" href="<?php echo "print_daily_inventory_report"; ?>" style="pointer-events: none"><i class="fa-solid fa-file-pdf fa-3x link-danger"></i></a>
                </div>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="view_asn_table"> -->
                    <thead>
                      <tr>
                        <th class=" text-center  font-weight-bold ">SKU Code</th>
                        <th class=" text-center  font-weight-bold ">Material Description</th>
                        <th class=" text-center  font-weight-bold ">SOH</th>
                        <!-- <th class=" text-center  font-weight-bold ">Serial</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_inventory_report as $asar_key => $db_det) { ?>
                        <tr>
                          <tD class=" text-center"><?php echo $db_det['sku_code']; ?></th>
                          <td class=" text-center"><?php echo $db_det['material_description']; ?></td>
                          <td class=" text-center"><?php echo number_format($db_det['SOH']); ?></td>
                          <!-- <td class=" text-center"><?php echo $db_det['exp_date']; ?></td> -->
                        </tr>
                      <?php } ?>
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
      $('#view_dir_table').DataTable({
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