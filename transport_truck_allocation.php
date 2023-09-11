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

          $date_today = date('Y-m-d');

          $db_for_dispatch = $db->query('SELECT 
          a.id,
          a.to_no,
          a.transaction_type,
          a.uploading_file_name,
          a.delivery_order_no,
          a.pcppi_shipment_no,
          a.so_date,
          a.rdd,
          a.so_no,
          a.delivering_plant,
          a.ship_to_code,
          a.sku_code,
          a.material_description,
          a.req_qty_case,
          a.allocated_qty,
          a.picked_qty,
          a.allocated_by,
          a.created_by,
          a.`status`,
          a.truck_allocation_status,
          a.fill_rate_status,
          a.upload_date,
          tb_items.material_description,
          tb_items.weight_per_case,
          tb_items.cbm_per_case,
          tb_customer.ship_to_name,
          tb_customer.ship_to_address
          FROM tb_transfer_order a
          LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
          LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
          -- WHERE a.truck_allocation_status = ? AND a.rdd >= ? AND a.allocated_qty <> ?
          WHERE a.truck_allocation_status = ? AND a.allocated_qty <> ?
          ORDER BY a.so_no',"Pending",0)->fetch_all();
          
          // print_r_html($db_for_dispatch);


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-4">
              <div class="card">
                  <div class="card-header">
                        <div class="col-lg-10 col-md-10 col-sm-10">
                          <h4 class="card-title">Truck Allocation</h4>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-10 text-right">
                          <form action="add_truck_allocation" method="post">
                            <div class="control-group">
                              <input type="submit" class="btn btn-primary" value="Allocate"/>
                            </div>
                          </form>
                        </div>
                  </div>
                  <div class="card-body">
                      <div id="runningTotal"></div>
                  </div>
              </div>
          </div>
          
          <div class="col-lg-8">
            <form action="add_truck_allocation" method="post">
            <div class="card">
              <div class="card-header">
                <div class="col-lg-11">
                  <h4 class="card-title">List of Shipment</h4>
                </div>
                <div class="col-lg-1 text-right">
                    <div class="control-group">
                      <input type="submit" class="btn btn-primary" value="Allocate"/>
                    </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" name = "example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th>
                            <div class="form-check custom-checkbox ms-2">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                                <label class="form-check-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th class="align-middle text-center small">ID</th>
                        <th class="align-middle text-center small">SO Date</th>
                        <th class="align-middle text-center small">RDD</th>
                        <th class="align-middle text-center small">SO No.</th>
                        <!-- <th class="align-middle text-center">Delivering Plant</th> -->
                        <th class="align-middle text-center small">Ship To</th>
                        <!-- <th class="align-middle text-center">Address</th> -->
                        <th class="align-middle text-center small">SKU</th>
                        <th class="align-middle text-center small">Allocated</th>
                        <th class="align-middle text-center small">Total Weight</th>
                        <th class="align-middle text-center small">Total CBM</th>
                        <!-- <th class="align-middle text-center">Truck Allocation</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_for_dispatch as $arr_key => $arr_det) { ?>
                        <tr>
                        <!-- ACTION ITEMS -->
                          <!-- <td> -->
                            <!-- <div class="dropdown ms-auto text-end">
                              <div class="btn sharp btn-warning tp-btn ms-auto" data-bs-toggle="dropdown">
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M12.2835 13C12.8354 13 13.2828 12.5523 13.2828 12C13.2828 11.4477 12.8354 11 12.2835 11C11.7316 11 11.2842 11.4477 11.2842 12C11.2842 12.5523 11.7316 13 12.2835 13Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M12.2835 6C12.8354 6 13.2828 5.55228 13.2828 5C13.2828 4.44772 12.8354 4 12.2835 4C11.7316 4 11.2842 4.44772 11.2842 5C11.2842 5.55228 11.7316 6 12.2835 6Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M12.2835 20C12.8354 20 13.2828 19.5523 13.2828 19C13.2828 18.4477 12.8354 18 12.2835 18C11.7316 18 11.2842 18.4477 11.2842 19C11.2842 19.5523 11.7316 20 12.2835 20Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                              </div> -->
                              <!-- <div class="dropdown-menu dropdown-menu-end"> -->
                                <!-- <?php if($arr_det['status'] == "Picking"){ ?>
                                  <a class="dropdown-item" data-toggle="modal" data-target="#update_details<?php echo $arr_det['id'];?>">View/Update Details</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo "print_asn_slip?db_id={$arr_det['id']}";?>">Print Picklist</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo "print_asn_slip?db_id={$arr_det['id']}";?>">Print Checklist</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo "print_asn_slip?db_id={$arr_det['id']}";?>">Print Delivery Order</a>
                                <?php }else{  ?>
                                  <a class="dropdown-item" data-toggle="modal" data-target="#goods_receipt<?php echo $arr_det['id'];?> style="pointer-events: none">Goods Issuance</a>
                                <?php } ?> -->
                                <!-- <?php if(is_null($arr_det['ship_to_name']) || is_null($arr_det['material_description'])){?>
                                  <a class="dropdown-item" target="_blank" style="pointer-events: none" href="<?php echo "print_picklist?to_id={$arr_det['id']}";?>">Print Picklist (Disabled)</a>
                                  <a class="dropdown-item" target="_blank" style="pointer-events: none" href="<?php echo "validate_picking?transfer_order_id={$arr_det['id']}";?>">Validate Picking (Disabled)</a>
                                  <a class="dropdown-item" target="_blank" style="pointer-events: none" href="<?php echo "print_delivery_order?transfer_order_id={$arr_det['id']}";?>">Print Delivery Order (Disabled)</a>
                                <?php }else{?>
                                  <a class="dropdown-item" target="_blank" href="<?php echo "print_picklist?to_id={$arr_det['id']}";?>">Print Picklist </a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo "validate_picking?transfer_order_id={$arr_det['id']}";?>">Validate Picking </a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo "print_delivery_order?transfer_order_id={$arr_det['id']}";?>">Print Delivery Order </a>
                                <?php } ?> -->
                              <!-- </div> -->
                          <!-- </td>	 -->
                          <td><input type="checkbox" class="form-check-input" id="check-<?php echo $arr_det['id']?>" name = "check-<?php echo $arr_det['id']?>" value ="<?php echo $arr_det['id']?>" data-amount="<?php echo $arr_det['allocated_qty']; ?>" ></td>
                          <td class="align-middle text-center small" ><?php echo $arr_det['id']; ?></td>	
                          <td class="align-middle text-center small" ><?php echo $arr_det['so_date']; ?></td>
                          <td class="align-middle text-center small" ><?php echo $arr_det['rdd']; ?></td>
                          <td class="align-middle text-center small" ><?php echo $arr_det['so_no']; ?></td>
                          <!-- <td class="align-middle text-center" ><?php echo $arr_det['delivering_plant']; ?></td> -->
                          <?php if(is_null($arr_det['ship_to_name'])){?>
                            <td class="align-middle text-center small" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                           
                          <?php }else{?>
                            
                              <td class="align-middle text-center small" ><?php echo $arr_det['ship_to_name']; ?></td>

                          <?php } ?>

                          <!-- <?php if(is_null($arr_det['ship_to_name'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                          <?php }else{?>
                            <td class="align-middle text-center" ><?php echo $arr_det['ship_to_address']; ?></td>
                          <?php } ?> -->

                          <?php if(is_null($arr_det['material_description'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger small'> <i class='fa fa-circle text-danger me-1'></i>Item Not Registered</span>
                            </td>
                          <?php }else{?>
                            <td class="align-middle text-center small" ><?php echo $arr_det['material_description'];?></td>
                          <?php } ?>
                        
                          <td class="align-middle text-center "><?php echo number_format($arr_det['allocated_qty'],2); ?></td>

                          <?php if($arr_det['weight_per_case'] == "TBD" || is_null($arr_det['weight_per_case'])){?>
                            <td class="align-middle text-center small"><span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Cannot Calculate Update Masterlist</span></td>
                          <?php }else{?>
                            <td class="align-middle text-center small"><?php echo number_format(($arr_det['allocated_qty'])*$arr_det['weight_per_case'],2)." KG"; ?></td>
                          <?php }?>

                          <?php if($arr_det['cbm_per_case'] == "TBD" || is_null($arr_det['cbm_per_case'])){?>
                            <td class="align-middle text-center small"><span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Cannot Calculate Update Masterlist</span></td>
                          <?php }else{?>
                            <td class="align-middle text-center small"><?php echo number_format(($arr_det['allocated_qty'])*$arr_det['cbm_per_case'],2); ?></td>
                          <?php }?>
                        
                          <!-- STATUS -->
                          <!-- <td class = 'align-middle text-center'>
                            <?php

                              if ($arr_det['truck_allocation_status'] == "Pending") {
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1 small'></i>Pending</span>";
                              }else{
                                echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1 small'></i>Done</span>";
                              }
                                                
                              ?>
                          </td> -->

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
  <script>
    (function() {
      'use strict'
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')
      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function(form) {
          form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })
    })()

    const checkboxes = document.querySelectorAll("input[type=checkbox]");      
      example4.addEventListener('click', () => {
          var total = 0;
          for (const {checked, dataset} of checkboxes) {
              total += checked ? Number(dataset.amount) : 0;
          }
          runningTotal.textContent = total;
      });
  </script>
</body>

</html>