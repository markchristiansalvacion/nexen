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

          $previous_week = strtotime("-1 week +1 day");

          $start_week_aux = strtotime("last sunday midnight",$previous_week);
          $start_week = date("Y-m-d",$start_week_aux);
          $end_week = date('Y-m-d',strtotime("saturday this week +4 days"));

          // echo $start_week.' '.$end_week ;

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
          -- a.req_qty_case,
          SUM(a.req_qty_case) as total_req_case,
          -- a.allocated_qty,
          SUM(a.allocated_qty) AS total_allocated_qty,
          SUM(a.picked_qty*-1) AS total_picked_qty,
          a.created_by,
          a.upload_date,
          a.truck_allocation_status,
          tb_warehouse.warehouse_name,
          tb_customer.ship_to_name,
          tb_customer.ship_to_address
          FROM tb_transfer_order a
          LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
          LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
          LEFT JOIN tb_warehouse ON tb_warehouse.warehouse_id = a.delivering_plant
          WHERE a.`status` <> ?
          AND a.status <> ?
          AND a.rdd BETWEEN ? AND ?
          GROUP BY a.so_no ORDER BY a.rdd DESC',"Dispatch","Cancelled",$start_week,$end_week)->fetch_all();
          
          // print_r_html($db_for_dispatch);


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Incoming Dispatch</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Action</th>
                        <th class="align-middle text-center">SO Date</th>
                        <th class="align-middle text-center">RDD</th>
                        <th class="align-middle text-center">SO No.</th>
                        <th class="align-middle text-center">Ship To</th>
                        <th class="align-middle text-center">Address</th>
                        <th class="align-middle text-center">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_for_dispatch as $arr_key => $arr_det) { ?>
                        <tr>
                          <td>
                            <div class="d-flex">
                              <?php
                                if($arr_det['total_allocated_qty'] != 0){
                              ?>

                                  <a target="_blank" href="<?php echo "print_picklist?so_no={$arr_det['so_no']}";?>" class="btn btn-warning shadow btn-xs sharp me-1" title="Print Picklist"><i class="fa-solid fa-print"></i></a>

                                  <a target="_blank" href="<?php echo "validate_picking?so_no={$arr_det['so_no']}";?>" class="btn btn-primary shadow btn-xs sharp me-1" title="Validate Picking"><i class="fa-solid fa-box"></i></a>

                                  <?php if(are_strings_equal($arr_det['truck_allocation_status'],"Done")){ ?>
                                    <a target="_blank" href="<?php echo "print_delivery_order?so_no={$arr_det['so_no']}";?>" class="btn btn-success shadow btn-xs sharp me-1" title="Print DO"><i class="fa-solid fa-print"></i></a>
                                  <?php } ?>

                                  <!-- <?php if($arr_det['total_picked_qty'] == 0){?>
                                    <a target="_blank" href="<?php echo "print_picklist?so_no={$arr_det['so_no']}";?>" class="btn btn-warning shadow btn-xs sharp me-1" title="Print Picklist"><i class="fa-solid fa-print"></i></a>
                                    <a target="_blank" href="<?php echo "validate_picking?so_no={$arr_det['so_no']}";?>" class="btn btn-primary shadow btn-xs sharp me-1" title="Validate Picking"><i class="fa-solid fa-box"></i></a>
                                  <?php }else{ ?>
                                    
                                  <?php if($arr_det['total_picked_qty'] < $arr_det['total_allocated_qty']){ ?>
                                    <a target="_blank" href="<?php echo "validate_picking?so_no={$arr_det['so_no']}";?>" class="btn btn-primary shadow btn-xs sharp me-1" title="Validate Picking"><i class="fa-solid fa-box"></i></a>
                                  <?php } ?>

                                  <?php if($arr_det['total_picked_qty'] > $arr_det['total_allocated_qty']){ ?>
                                    <a target="_blank" href="<?php echo "validate_picking?so_no={$arr_det['so_no']}";?>" class="btn btn-primary shadow btn-xs sharp me-1" title="Validate Picking"><i class="fa-solid fa-box"></i></a>
                                  <?php } ?>

                                    <?php if($arr_det['total_picked_qty'] == $arr_det['total_allocated_qty']){ ?>
                                     <?php if(are_strings_equal($arr_det['truck_allocation_status'],"Done")){ ?>
                                      <a target="_blank" href="<?php echo "print_delivery_order?so_no={$arr_det['so_no']}";?>" class="btn btn-success shadow btn-xs sharp me-1" title="Print DO"><i class="fa-solid fa-print"></i></a>
                                     <?php }else{ ?>
                                      <a target="_blank" style="pointer-events: none" href="<?php echo "print_delivery_order?so_no={$arr_det['so_no']}";?>" class="btn btn-success shadow btn-xs sharp me-1" title="Print DO"><i class="fa-solid fa-print"></i></a>
                                     <?php } ?>
                                    
                                <?php } ?>

                                <?php } ?> -->
                                    
                                <?php }else{ ?>
                                  <a target="_blank" style="pointer-events: none" href="<?php echo "print_picklist?so_no={$arr_det['so_no']}";?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Disabled"><i class="fa-solid fa-print"></i></a>
                                  <a target="_blank" style="pointer-events: none" href="<?php echo "validate_picking?so_no={$arr_det['so_no']}";?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Validate Picking"><i class="fa-solid fa-box"></i></a>
                              <?php } ?>                   
                              
                           
                            </div>												
												  </td>
                          <td class="align-middle text-center" ><?php echo $arr_det['so_date']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['rdd']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['so_no']; ?></td>
                          <?php if(is_null($arr_det['ship_to_name'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                           
                          <?php }else{?>
                            
                              <td class="align-middle text-center" ><?php echo $arr_det['ship_to_name']; ?></td>

                          <?php } ?>
                          
                          <?php if(is_null($arr_det['ship_to_name'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                           
                          <?php }else{?>
                            
                              <td class="align-middle text-center" ><?php echo $arr_det['ship_to_address']; ?></td>

                          <?php } ?>


                          <!-- STATUS -->
                          <td class = 'align-middle text-center'>
                            <?php
                              if($arr_det['total_allocated_qty'] != 0){

                                if($arr_det['total_picked_qty'] == 0){
                                  echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Ongoing Picking</span>";
                                }else{
                                  
                                  if($arr_det['total_picked_qty'] < $arr_det['total_allocated_qty']){
                                    echo "<span class='badge badge-sm light badge-info'> <i class='fa fa-circle text-info me-1'></i>Ongoing Validation</span>";
                                  }

                                  if($arr_det['total_picked_qty'] > $arr_det['total_allocated_qty']){
                                    echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Needs Revalidation</span>";
                                  }

                                  if($arr_det['total_picked_qty'] == $arr_det['total_allocated_qty']){
                                      echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Order Complete</span>";
                                  }

                                }
                                  
                              }else{
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>For Allocation</span>";
                              }                   
                              ?>
                          </td>

    
                          <!-- MODAL FOR UPDATE ASN DETAILS-->
                          <div id="validate_picking<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Picklist Validation</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="update_asn" method="post">
                                    <div class="form-group">
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>

                                      <!-- ASN REF-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref" id="ref" value="<?php echo $arr_det['ref_no']; ?>">
                                      </div>

                                      <!-- Checker-->
                                      <div class="mt-1">
                                        <label for="checker" class="form-control-label text-uppercase text-primary font-weight-bold">Select Checker</label>
                                        <select name="checker" id="checker" class="form-control">
                                            <option value="<?php echo $arr_det['checker']; ?>"><?php echo $arr_det['checker']; ?></option>
                                            <?php foreach($db_checker as $arr_key => $arr_val){?>
                                                <option value="<?php echo $arr_val['name']?>"><?php echo $arr_val['name']?></option>
                                            <?php } ?>
                                        </select>
                                      </div>

                                      <!-- BAY LOCATION-->
                                      <div class="mt-1">
                                      <label for="bay_location" class="form-control-label text-uppercase text-primary font-weight-bold">Bay Location</label>
                                        <select name="bay_location" id="bay_location" class="form-control">
                                            <option value="<?php echo $arr_det['bay_location']; ?>"><?php echo $arr_det['bay_location']; ?></option>
                                            <option value="1A">1A</option>
                                            <option value="1B">1B</option>
                                            <option value="2A">2A</option>
                                            <option value="2B">2B</option>
                                            <option value="3A">3A</option>
                                            <option value="3B">3B</option>
                                            <option value="4A">4A</option>
                                            <option value="4B">4B</option>
                                            <option value="5A">5A</option>
                                            <option value="5B">5B</option>
                                            <option value="6A">6A</option>
                                            <option value="6B">6B</option>
                                            <option value="7A">7A</option>
                                            <option value="7B">7B</option>
                                            <option value="8A">8A</option>
                                            <option value="8B">8B</option>
                                            <option value="9A">9A</option>
                                            <option value="9B">9B</option>
                                            <option value="10A">10A</option>
                                            <option value="10B">10B</option>
                                            <option value="11A">11A</option>
                                            <option value="11B">11B</option>
                                            <option value="12A">12A</option>
                                            <option value="12B">12B</option>
                                        </select>
                                      </div>

                                      <!-- ACTUAL DATE ARRIVED-->
                                      <div class="mt-1">
                                      <label for="ata" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Actual Date of Arrival</label>
                                        <input type="date" name="ata" class="form-control" id="ata" value="<?php echo $arr_det['ata']; ?>">
                                      </div>


                                      <!-- TIME ARRIVED-->
                                      <div class="mt-1">
                                      <label for="time_arrived" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Time of Arrival</label>
                                        <input type="time" name="time_arrived" class="form-control" id="time_arrived" value="<?php echo $arr_det['time_arrived']; ?>">
                                      </div>
        
                                      <!-- UNLOADING START-->
                                      <div class="mt-1">
                                      <label for="unloading_start" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Unloading Start.</label>
                                        <input type="time" name="unloading_start" class="form-control" id="unloading_start" value="<?php echo $arr_det['unloading_start']; ?>">
                                      </div>

                                      <!-- UNLOADING END-->
                                      <div class="mt-1">
                                      <label for="unloading_end" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Unloading End</label>
                                        <input type="time" name="unloading_end" class="form-control" id="unloading_end" value="<?php echo $arr_det['unloading_end']; ?>">
                                      </div>

                                      <!-- TIME DEPARTED-->
                                      <div class="mt-1">
                                      <label for="time_departed" class="form-control-label text-uppercase text-primary font-weight-bold">Enter Time of Truck Departure</label>
                                        <input type="time" name="time_departed" class="form-control" id="time_departed" value="<?php echo $arr_det['time_departed']; ?>">
                                      </div>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                                      <button type="submit" class="btn btn-primary">Confirm Transaction</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- End Modal -->

                           <!-- Goods Receipt Modal-->
                           <div id="goods_receipt<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Goods Receipt</h4>
                                </div>

                                <div class="modal-body">
                                  <form action="add_goods_receipt" method="post">
                                    <div class="form-group">
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>

                                      <!-- ASN REF-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref" id="ref" value="<?php echo $arr_det['ref_no']; ?>">
                                      </div>

                                      <!-- Document No. -->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_det['document_no']; ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="planned_sku" class="form-control-label text-uppercase text-primary font-weight-bold">Planned SKU</label>
                                        <input type="text" step="1" class="form-control" id="planned_sku" value="<?php echo $arr_det['sku_code'].'-'.$arr_det['material_description'];  ?>" disabled>
                                      </div>

                                      <div class="mt-1">
                                        <label for="planned_qty" class="form-control-label text-uppercase text-primary font-weight-bold">Planned Quantity (Cases)</label>
                                        <input type="number" step="1" class="form-control" id="planned_qty" value="<?php echo $arr_det['qty_case'];  ?>" disabled>
                                      </div>
                                       
                                      <div class="mt-1">
                                        <label for="actual_sku" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Received SKU</label>
                                        <select id="actual_sku" name="actual_sku" class="form-control">
                                          <option value="">Select Actual SKU</option>
                                          <?php
                                            foreach($db_items as $arr_key => $arr_val){ 
                                          ?>
                                              <option value="<?php echo $arr_val['sap_code']; ?>"><?php echo $arr_val['sap_code'].'-'.$arr_val['material_description']; ?></option>
                                          <?php
                                            }
                                          ?>
                                          
                                        </select>
                                      </div>

                                      <div class="mt-1">
                                        <label for="actual_qty" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Received Quantity (Cases)</label>
                                        <input type="number" step="1" class="form-control" id="actual_qty" name="actual_qty">
                                      </div>

                                      <div class="mt-1">
                                        <label for="expiration_date" class="form-control-label text-uppercase text-primary font-weight-bold">Expiration Date/Best Before Date (BBD)</label>
                                        <input type="date" class="form-control" id="expiration_date" name="expiration_date">
                                      </div>

                                    <div class="modal-footer">

                                      <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                                      <button type="submit" class="btn btn-primary">Confirm</button>

                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Goods Receipt Modal -->


                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                </div>
              </div>
            </div>
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
  </script>

</body>

</html>