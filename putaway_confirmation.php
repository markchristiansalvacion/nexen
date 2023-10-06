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
        <?php

      
        $db_for_put_away = $db->query('SELECT 
            a.id,
            a.ia_ref,
            a.lpn,
            a.sku_code,
            a.qty_case,
            a.expiry,
            a.bin_loc,
            a.putaway_status,
            tb_items.material_description,
            tb_asn.last_updated,
            tb_assembly_build.document_no
            FROM tb_inventory_adjustment a
            INNER JOIN tb_assembly_build ON tb_assembly_build.id = a.ab_id 
            INNER JOIN tb_items on tb_items.sku_code = a.sku_code
            INNER JOIN tb_asn ON tb_asn.id = tb_assembly_build.asn_id
            WHERE a.putaway_status = "Pending"
            AND a.transaction_type = "INB" ')->fetch_all();

       // print_r_html($db_for_put_away);


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <form action="putaway_confirmation_proc" method="post">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <h4 class="card-title">Putaway Confirmation</h4>
                </div>
                <div class="row">
                    <div class="control-group">
                      <input type="submit" class="btn btn-lg btn-primary" value="CONFIRM"/>
                    </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">ID</th>
                        <th class="align-middle text-center">Date Received</th>
                        <th class="align-middle text-center">Source Document</th>
                        <th class="align-middle text-center">SKU</th>
                        <th class="align-middle text-center">Qty Case</th>
                        <th class="align-middle text-center">Serial</th>
                        <th class="align-middle text-center">Suggested Location</th>
                        <th class="align-middle text-center">LPN</th>
                        <th class="align-middle text-center">Putaway Status</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_for_put_away as $arr_key => $arr_det) { ?>
                        <tr>

                        <!-- ACTION ITEMS -->
                          <td>
                            <input type="checkbox" class="form-check-input" id="<?php echo $arr_det['id']?>" name = "checkbox[]" value ="<?php echo $arr_det['id']?>">
                          </td>		
                          <td class="align-middle text-center" ><?php echo $arr_det['last_updated']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['document_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['sku_code'].'-'.$arr_det['material_description']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['qty_case']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['expiry']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['bin_loc']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['lpn']; ?></td>
                          <td class="align-middle text-center" ><?php echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>{$arr_det['putaway_status']}</span>"; ?></td>

                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                </div>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--**********************************
            Content body end
        ***********************************-->

  </div>
  <!--**********************************
        Main wrapper end
    ***********************************-->

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