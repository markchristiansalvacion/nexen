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

      <?php
        
        $date_today = date('Y-m-d');
       
        $stock_allocation = array();

        $transfer_order = $db->query('SELECT sku_code,SUM(req_qty_case) as required_qty FROM tb_transfer_order WHERE status = ? GROUP BY sku_code',"Allocation")->fetch_all();

       // print_r_html($transfer_order);

       $check_inventory_levels = $db->query('SELECT
       a.ship_to_code,
       a.sku_code,
       a.rdd,
       tb_items.sap_code AS item_master_code,
       tb_items.material_description,
       tb_customer.ship_to_code AS customer_master_code,
       tb_customer.ship_to_name,
       tb_customer.req_shelf_life,
       SUM(a.req_qty_case) AS order_qty
       FROM tb_transfer_order a
       LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
       LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
       WHERE a.status = ?
       GROUP BY a.rdd,tb_customer.req_shelf_life,a.sku_code,customer_master_code
       ORDER BY a.rdd DESC',"Allocation")->fetch_all();

      // print_r_html($check_inventory_levels);

       foreach($check_inventory_levels as $asar_key => $asar_val){

          if(are_strings_equal($asar_val['item_master_code'],EmptyString) || are_strings_equal($asar_val['customer_master_code'],EmptyString)){
            /** ERROR */
            $check_inventory_levels[$asar_key]['available_qty'] = 0;
           
          }else{
            
            $get_total_available_stocks = $db->query('SELECT
            a.sku_code,
            tb_items.material_description,
            SUM(a.qty_case - a.allocated_qty) AS available_qty
            FROM tb_inventory_adjustment a 
            INNER JOIN tb_items ON tb_items.sap_code = a.sku_code
            WHERE DATEDIFF(a.expiry,CURDATE())/(tb_items.shelf_life*30) >= ?
            AND a.transaction_type = ?
            AND a.qty_case - a.allocated_qty <> 0 
            AND a.sku_code = ?
            AND a.putaway_status <> ?
            AND a.lpn_status = ?',$asar_val['req_shelf_life'],"INB",$asar_val['item_master_code'],"Pending","Active")->fetch_array();

           if(is_array_has_empty_input($get_total_available_stocks)){
            $check_inventory_levels[$asar_key]['available_qty'] = 0;
           }else{
            $check_inventory_levels[$asar_key]['available_qty'] = $get_total_available_stocks['available_qty'];
           }

          }
       }
        
        ?>
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Inventory Level</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Action</th>
                        <th class="align-middle text-center  font-weight-bold ">RDD</th>
                        <th class="align-middle text-center  font-weight-bold ">Ship To</th>
                        <th class="align-middle text-center  font-weight-bold ">Required Shelf Life</th>
                        <th class="align-middle text-center  font-weight-bold ">SKU Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Material Description</th>
                        <th class="align-middle text-center  font-weight-bold ">Order Qty</th>
                        <th class="align-middle text-center  font-weight-bold ">SOH</th>
                        <th class="align-middle text-center  font-weight-bold ">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($check_inventory_levels  as $asar_key => $asar_val) {?>
                        <tr>
                          <td> 
                            <div class="d-flex">
                                <?php if(is_null($asar_val['customer_master_code']) || is_null($asar_val['item_master_code'])){ ?>
                                      <!-- Missing Items on Database  -->
                                      <a href="<?php echo "create_stock_allocation?sku_code={$asar_val['sku_code']}&customer_code={$asar_val['customer_master_code']}&rdd={$asar_val['rdd']}&alloc_status=MD";?>"  class="btn btn-success shadow btn-xs sharp me-1" title="View (MD)"><i class="fa-solid fa-eye"></i></a>
                                <?php }else{ ?>
                                      <?php if($asar_val['available_qty'] > 0){ ?>

                                        <?php if ($asar_val['available_qty'] < $asar_val['order_qty']) { ?>
                                              <!-- Need Replen/Shortage -->
                                              <a href="<?php echo "create_stock_allocation?sku_code={$asar_val['sku_code']}&customer_code={$asar_val['customer_master_code']}&rdd={$asar_val['rdd']}&alloc_status=SHORT";?>"  class="btn btn-warning shadow btn-xs sharp me-1" title="View (Short)"><i class="fa-solid fa-eye"></i></a>
                                        <?php }else{ ?>
                                              <!-- Good For Allocation -->
                                              <a href="<?php echo "create_stock_allocation?sku_code={$asar_val['sku_code']}&customer_code={$asar_val['customer_master_code']}&rdd={$asar_val['rdd']}&alloc_status=GOOD";?>"  class="btn btn-success shadow btn-xs sharp me-1" title="View (Good)"><i class="fa-solid fa-eye"></i></a>
                                        <?php } ?>

                                      <?php }else{ ?>
                                            <!-- Out of Stock -->
                                            <a href="<?php echo "create_stock_allocation?sku_code={$asar_val['sku_code']}&customer_code={$asar_val['customer_master_code']}&rdd={$asar_val['rdd']}&alloc_status=OS";?>"  class="btn btn-danger shadow btn-xs sharp me-1" title="View"><i class="fa-solid fa-eye"></i></a>
                                      <?php } ?>
                                <?php }?>
                            </div>												
												  </td>
                          <td class="align-middle text-center" ><?php echo $asar_val['rdd']; ?></td>
                          <?php if(is_null($asar_val['customer_master_code'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                          <?php }else{?>
                            
                              <td class="align-middle text-center" ><?php echo $asar_val['ship_to_name']; ?></td>

                          <?php } ?>

                          <?php if(is_null($asar_val['customer_master_code'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                          <?php }else{?>
                            
                            <td class="align-middle text-center "><?php echo $asar_val['req_shelf_life']*100;?>%</td>

                          <?php } ?>
                          

                          <?php if(is_null($asar_val['item_master_code'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Item Not Registered</span>
                            </td>
                           
                          <?php }else{?>
                            
                              <td class="align-middle text-center" ><?php echo $asar_val['item_master_code']; ?></td>

                          <?php } ?>

                          <?php if(is_null($asar_val['item_master_code'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Item Not Registered</span>
                            </td>
                           
                          <?php }else{?>
                            
                              <td class="align-middle text-center" ><?php echo $asar_val['material_description']; ?></td>

                          <?php } ?>
                          
                        
                          <td class="align-middle text-center "><?php echo number_format($asar_val['order_qty'], 2, ".", ",");?></td>
                          <td class="align-middle text-center "><?php echo number_format($asar_val['available_qty'], 2, ".", ",");?></td>
                        
                          <td class="align-middle text-center ">
                            <?php
                              if(is_null($asar_val['customer_master_code']) || is_null($asar_val['item_master_code'])){
                                  echo '<span class="badge badge-danger">Inaccurate Calculation. Update the Master File</span>';
                              }else{
                                if($asar_val['available_qty'] > 0){
                                  if ($asar_val['available_qty'] < $asar_val['order_qty']) {
                                    echo '<span class="badge badge-warning">Needs Replenishment</span>';
                                  } else {
                                    echo '<span class="badge badge-success">Good for Allocation</span>';
                                  }
                                }else{
                                  echo '<span class="badge badge-danger">Out of Stock</span>';
                                }
                              }
                            ?>
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
        < </div>
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