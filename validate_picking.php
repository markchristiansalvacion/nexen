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

            $db_items = $db->query('SELECT * FROM tb_items')->fetch_all();

            $all_lpn = $db->query('SELECT DISTINCT lpn FROM tb_inventory_adjustment')->fetch_all();
           

            $picklist_details = $db->query('SELECT 
                                            a.id,
                                            a.ref_no,
                                            a.ia_id,
                                            a.to_id,
                                            a.allocated_lpn,
                                            a.picked_lpn,
                                            a.allocated_sku_code,
                                            a.picked_sku_code,
                                            a.allocated_qty,
                                            a.picked_qty,
                                            a.allocated_expiry,
                                            a.picked_expiry,
                                            a.bin_loc,
                                            a.picked_loc,
                                            a.status,
                                            a.fulfillment_status,
                                            tb_items.material_description,
                                            tb_transfer_order.req_qty_case,
                                            tb_transfer_order.so_no
                                            FROM tb_picklist a
                                            INNER JOIN tb_items ON tb_items.sap_code = a.allocated_sku_code
                                            INNER JOIN tb_transfer_order ON tb_transfer_order.id = a.to_id
                                            WHERE a.so_no = ?
                                            AND a.fulfillment_status = ? 
                                            ORDER BY a.id ASC
                                            LIMIT 20',$_GET['so_no'],"Pending")->fetch_all();
          
            // print_r_html($picklist_details);


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Picking Validation</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Action</th>
                        <th class="align-middle text-center">ID</th>
                        <th class="align-middle text-center">Picklist Ref.</th>
                        <th class="align-middle text-center">SO No.</th>
                        <th class="align-middle text-center">SKU</th>
                        <th class="align-middle text-center">Allocated</th>
                        <th class="align-middle text-center">Allocated BBD</th>
                        <th class="align-middle text-center">Bin Location</th>
                        <th class="align-middle text-center">Status</th>
                        <th class="align-middle text-center">Fulfillment Status</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($picklist_details as $arr_key => $arr_det) { ?>
                        <tr>

                        <!-- ACTION ITEMS -->
                          <td>
                            <div class="dropdown ms-auto text-end">
                              <div class="btn sharp btn-warning tp-btn ms-auto" data-bs-toggle="dropdown">
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M12.2835 13C12.8354 13 13.2828 12.5523 13.2828 12C13.2828 11.4477 12.8354 11 12.2835 11C11.7316 11 11.2842 11.4477 11.2842 12C11.2842 12.5523 11.7316 13 12.2835 13Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M12.2835 6C12.8354 6 13.2828 5.55228 13.2828 5C13.2828 4.44772 12.8354 4 12.2835 4C11.7316 4 11.2842 4.44772 11.2842 5C11.2842 5.55228 11.7316 6 12.2835 6Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M12.2835 20C12.8354 20 13.2828 19.5523 13.2828 19C13.2828 18.4477 12.8354 18 12.2835 18C11.7316 18 11.2842 18.4477 11.2842 19C11.2842 19.5523 11.7316 20 12.2835 20Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                              </div>
                              <div class="dropdown-menu dropdown-menu-end">
                                <?php if ($arr_det['status'] == "For Validation") {?>
                                    <a class="dropdown-item" data-toggle="modal" data-target="#validate_picking<?php echo $arr_det['id'];?>">Picking Confirmation</a>
                                <?php } ?>

                                <?php if ($arr_det['status'] == "Validated") {
                                    if ($arr_det['allocated_lpn'] == $arr_det['picked_lpn'] AND  $arr_det['allocated_sku_code'] == $arr_det['picked_sku_code'] AND  $arr_det['allocated_qty'] == $arr_det['picked_qty'] AND  $arr_det['allocated_expiry'] == $arr_det['picked_expiry'] AND  $arr_det['bin_loc'] == $arr_det['picked_loc']) {?>
                                    <a class="dropdown-item" target="_blank" href="<?php echo "print_rtl_tag?picklist_id={$arr_det['id']}&so_no={$_GET['so_no']}";?>">Print Ready to Load Tag</a>
                                <?php }else{ ?>
                                    <a class="dropdown-item" data-toggle="modal" data-target="#validate_picking<?php echo $arr_det['id'];?>">Picking Confirmation</a>
                               <?php  } } ?>

                               <?php if ($arr_det['status'] == "Ready to Load") {?>
                                    <a class="dropdown-item" data-toggle="modal" data-target="#outbound_fulfillment_modal<?php echo $arr_det['id'];?>">Outbound Fulfillment</a>
                                <?php } ?>

                              </div>
                            </div>
                          </td>	
                          <td class="align-middle text-center" ><?php echo $arr_det['id']; ?></td>	
                          <td class="align-middle text-center" ><?php echo $arr_det['ref_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['so_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['allocated_sku_code'] .'-'.$arr_det['material_description'];?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['allocated_qty']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['allocated_expiry']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['bin_loc']; ?></td>
                          <!-- STATUS -->
                          <td class = 'align-middle text-center'>
                            <?php

                              if ($arr_det['status'] == "For Validation") {
                                echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>For System Validation</span>";
                              }

                              
                              if($arr_det['status'] == "Validated"){
                                if ($arr_det['allocated_lpn'] == $arr_det['picked_lpn'] AND  $arr_det['allocated_sku_code'] == $arr_det['picked_sku_code'] AND  $arr_det['allocated_qty'] == $arr_det['picked_qty'] AND  $arr_det['allocated_expiry'] == $arr_det['picked_expiry'] AND  $arr_det['bin_loc'] == $arr_det['picked_loc']) {
                                    echo "<span class='badge badge-sm light badge-info'> <i class='fa fa-circle text-info me-1'></i>Print RTL Tag</span>";
                                }else{
                                    echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Needs Revalidation</span>";
                                }
                              }

                              if ($arr_det['status'] == "Ready to Load") {
                                echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Ready to Load</span>";
                              }
                            
                            ?>
                          </td>

                          <!--FULFILLMENT STATUS -->
                          <td class = 'align-middle text-center'>
                            <?php

                              if ($arr_det['fulfillment_status'] == "Pending") {

                                if($arr_det['status'] == "Validated"){
                                   
                                    echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Awaiting Fulfillment</span>";
                                    
                                }

                                if ($arr_det['status'] == "Ready to Load") {
                                    echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Awaiting Fulfillment</span>";
                                }

                                if($arr_det['status'] == "For Validation"){
                                   
                                    echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Pending Fulfillment</span>";
                                    
                                }

                                
                              }

                              if ($arr_det['fulfillment_status'] == "Fulfilled") {
                                echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Fulfilled</span>";
                              }

                              if ($arr_det['fulfillment_status'] == "Partially Fulfilled") {
                                echo "<span class='badge badge-sm light badge-info'> <i class='fa fa-circle text-info me-1'></i>Partially Fulfilled</span>";
                              }
                             
                              ?>
                          </td>
                          
                          <!-- MODAL FOR PICKING VALIDATION-->
                          <div id="validate_picking<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Picklist Validation</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="validate_picking_proc" method="post">
                                    <div class="form-group">
                                    
                                      <!-- PICKLIST ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="picklist_id" id="picklist_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>

                                      <!-- TO ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="to_id" id="to_id" value="<?php echo $_GET['so_no']; ?>">
                                      </div>

                                      <!-- PICKING REF-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref" id="ref" value="<?php echo $arr_det['ref_no']; ?>">
                                      </div>

                                      <!-- Checker-->
                                      <!-- <div class="mt-1">
                                        <label for="checker" class="form-control-label text-uppercase text-primary font-weight-bold">Select Checker</label>
                                        <select name="checker" id="checker" class="form-control">
                                            <option value="<?php echo $arr_det['checker']; ?>"><?php echo $arr_det['checker']; ?></option>
                                            <?php foreach($db_checker as $arr_key => $arr_val){?>
                                                <option value="<?php echo $arr_val['name']?>"><?php echo $arr_val['name']?></option>
                                            <?php } ?>
                                        </select>
                                      </div> -->

                                      <div class="mt-1">
                                        <label for="actual_sku" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Picked SKU</label>
                                        <!-- <select id="actual_sku" name="actual_sku" class="form-control">
                                          <option value="">Select Actual SKU</option>
                                          <?php
                                            foreach($db_items as $arr_key => $arr_val){ 
                                          ?>
                                              <option value="<?php echo $arr_val['sap_code']; ?>"><?php echo $arr_val['sap_code'].'-'.$arr_val['material_description']; ?></option>
                                          <?php
                                            }
                                          ?>
                                          
                                        </select> -->
                                        <input list="actual_sku" class="form-control" name="actual_sku" value="<?php echo $arr_det['allocated_sku_code']; ?>">
                                        <datalist id="actual_sku">
                                          <?php foreach($db_items as $arr_key => $arr_val){  ?>
                                            <option value="<?php echo $arr_val['sap_code']; ?>"><?php echo $arr_val['sap_code'].'-'.$arr_val['material_description']; ?></option>
                                          <?php } ?>
                                        </datalist>
                                      </div>

                                      <div class="mt-1">
                                        <label for="actual_qty" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Picked(Cases)</label>
                                        <input type="number" step="1" class="form-control" id="actual_qty" name="actual_qty" value="<?php echo $arr_det['allocated_qty']; ?>">
                                      </div>

                                      <!-- ACTUAL PICKED BBD-->
                                      <div class="mt-1">
                                      <label for="actual_bbd" class="form-control-label text-uppercase text-primary font-weight-bold">Picked BBD</label>
                                        <input type="date" name="actual_bbd" class="form-control" id="actual_bbd" value="<?php echo $arr_det['allocated_expiry']; ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="actual_bin_loc" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Location</label>
                                        <input type="text" step="1" class="form-control" id="actual_bin_loc" name="actual_bin_loc" value="<?php echo $arr_det['bin_loc']; ?>">
                                      </div>

                                      
                                      <div class="mt-1">
                                        <label for="picked_lpn" class="form-control-label text-uppercase text-primary font-weight-bold">Actual LPN</label>
                                        <!-- <input type="text" step="1" class="form-control" id="picked_lpn" name="picked_lpn"> -->
                                        <input list="picked_lpn" class="form-control" name="picked_lpn" value="<?php echo $arr_det['allocated_lpn']; ?>">
                                        <datalist id="picked_lpn">
                                          <?php foreach($all_lpn as $arr_key => $arr_val){  ?>
                                            <option value="<?php echo $arr_val['lpn']; ?>"><?php echo $arr_val['lpn']; ?></option>
                                          <?php } ?>
                                        </datalist>
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

                           <!-- MODAL FOR FULFILLMENT-->
                           <div id="outbound_fulfillment_modal<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Fulfillment Confirmation</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="outbound_fulfillment_proc" method="post">
                                    <div class="form-group">
                                    
                                      <!-- PICKLIST ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="picklist_id" id="picklist_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>

                                      <!-- TO ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="to_id" id="to_id" value="<?php echo $arr_det['to_id']; ?>">
                                      </div>

                                      <!-- IA SOURCE ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ia_id" id="ia_id" value="<?php echo $arr_det['ia_id']; ?>">
                                      </div>

                                      <!-- PICKING REF-->
                                      <div class="mt-1">
                                        <input type="hidden" name="ref" id="ref" value="<?php echo $arr_det['ref_no']; ?>">
                                      </div>

                                      <!-- SO NO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="so_no" id="so_no" value="<?php echo $arr_det['so_no']; ?>">
                                      </div>

                                      <!-- SKU -->
                                      <div class="mt-1">
                                        <label for="sku" class="form-control-label text-uppercase text-primary font-weight-bold">SKU Details</label>
                                        <input type="text" class="form-control" id="sku" value="<?php echo $arr_det['picked_sku_code'].'-'.$arr_det['material_description'];  ?>" disabled>
                                      </div>

                                      <!-- SKU -->
                                      <div class="mt-1">
                                        <input type="hidden" id="sku" name="sku" value="<?php echo $arr_det['picked_sku_code']?>">
                                      </div>
                                        
                                      <!-- PICKED QTY -->
                                      <div class="mt-1">
                                        <label for="actual_qty" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Picked(Cases)</label>
                                        <input type="number" step="1" class="form-control" id="actual_qty" name="actual_qty" value="<?php echo $arr_det['picked_qty']; ?>" disabled>
                                      </div>

                                      <div class="mt-1">
                                        <input type="hidden" id="qty" name="qty" value="<?php echo $arr_det['picked_qty']?>">
                                      </div>
                                        

                                      <!-- ACTUAL PICKED BBD-->
                                      <div class="mt-1">
                                      <label for="actual_bbd" class="form-control-label text-uppercase text-primary font-weight-bold">Picked BBD</label>
                                        <input type="date" name="actual_bbd" class="form-control" id="actual_bbd" value="<?php echo $arr_det['picked_expiry']; ?>" disabled>
                                      </div>

                                      <div class="mt-1">
                                        <input type="hidden" id="bbd" name="bbd" value="<?php echo $arr_det['picked_expiry']?>">
                                      </div>
                                        

                                      <div class="mt-1">
                                        <label for="actual_bin_loc" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Location</label>
                                        <input type="text" step="1" class="form-control" id="actual_bin_loc" name="actual_bin_loc" value="<?php echo $arr_det['picked_loc']; ?>" disabled>
                                      </div>

                                      <div class="mt-1">
                                        <input type="hidden" id="bin_loc" name="bin_loc" value="<?php echo $arr_det['picked_loc']?>">
                                      </div>
                                        
                                    
                                      <div class="mt-1">
                                        <label for="picked_lpn" class="form-control-label text-uppercase text-primary font-weight-bold">Actual LPN</label>
                                        <input type="text" step="1" class="form-control" id="picked_lpn" name="picked_lpn" value="<?php echo $arr_det['picked_lpn']; ?>" disabled>
                                      </div>

                                      <div class="mt-1">
                                        <input type="hidden" id="lpn" name="lpn" value="<?php echo $arr_det['picked_lpn']?>">
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