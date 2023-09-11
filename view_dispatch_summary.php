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

          $allocated_truck = $db->query('SELECT 
          a.id,
          a.ref_no,
          a.to_id,
          a.system_do_no,
          a.system_shipment_no,
          a.so_date,
          a.rdd,
          a.so_item_no,
          a.so_no,
          a.delivering_plant,
          a.ship_to_code,
          a.sku_code,
          a.material_description,
          a.qty,
          a.total_weight,
          a.total_cbm,
          a.total_pallets,
          a.truck_type,
          a.plate_no,
          a.driver,
          a.helper,
          a.date_created,
          a.allocated_by,
          tb_customer.ship_to_name,
          tb_customer.ship_to_address
          FROM tb_transport_allocation a
          LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
          WHERE a.rdd BETWEEN ? AND ? AND a.do_no = ? AND a.shipment_no = ? 
          GROUP BY a.system_shipment_no
          ORDER BY a.rdd DESC ',$start_week,$end_week,"","")->fetch_all();
          
        // print_r_html($allocated_truck);


        ?>
        <!-- row -->
        <div class="row">
          <!-- <div class="col-lg-4">
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
          </div> -->
          
          <div class="col-lg-12">
            
            <div class="card">
              <div class="card-header">
                <div class="col-lg-12">
                  <h4 class="card-title">Allocated Trucks</h4>
                </div>
                <!-- <?php if(!empty($allocated_truck)){?> -->
                <!-- <div class="col-lg-1 text-center">
                    <div class="control-group">
                      <a href="download_sap_f2_integration_file" class="btn btn-success" >F2 File</a>
                    </div>
                </div> -->
                <!-- <?php }?> -->
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" name = "example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center small">Action</th>
                        <th class="align-middle text-center small">Ref No.</th>
                        <th class="align-middle text-center small">RDD</th>
                        <!-- <th class="align-middle text-center small">SO No.</th> -->
                        <!-- <th class="align-middle text-center">Delivering Plant</th> -->
                        <!-- <th class="align-middle text-center small">Sys DO No.</th> -->
                        <!-- <th class="align-middle text-center small">SO No.</th> -->
                        <th class="align-middle text-center small">Shipment No.</th>
                        <!-- <th class="align-middle text-center">Ship To</th> -->
                        <th class="align-middle text-center small">Truck Type</th>
                        <th class="align-middle text-center small">Plate No.</th>
                        <th class="align-middle text-center small">Driver</th>
                        <th class="align-middle text-center small">Helper</th>
                        <th class="align-middle text-center small">Status</th>
                        <!-- <th class="align-middle text-center">Truck Allocation</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($allocated_truck as $arr_key => $arr_det) { ?>
                        <tr>
                          <td>
                            <div class="d-flex">
                              <a  data-toggle="modal" data-target="#update_details<?php echo $arr_det['id'];?>" class="btn btn-primary shadow btn-xs sharp me-1" title="View/Update"><i class="fas fa-pencil-alt"></i></a>
                            </div>												
                          </td>
                          <!-- <td><input type="checkbox" class="form-check-input" id="check-<?php echo $arr_det['id']?>" name = "check-<?php echo $arr_det['id']?>" value ="<?php echo $arr_det['id']?>" data-amount="<?php echo $arr_det['picked_qty']*-1; ?>" ></td> -->
                          <td class="align-middle text-center small" ><?php echo $arr_det['ref_no']; ?></td>	
                          <!-- <td class="align-middle text-center small" ><?php echo $arr_det['so_date']; ?></td> -->
                          <td class="align-middle text-center small" ><?php echo $arr_det['rdd']; ?></td>
                          <!-- <td class="align-middle text-center small" ><?php echo $arr_det['system_do_no']; ?></td>	 -->
                          <!-- <td class="align-middle text-center small" ><?php echo $arr_det['so_no']; ?></td> -->
                          <td class="align-middle text-center small" ><?php echo "SHPTNO-".$arr_det['system_shipment_no']; ?></td>
                          <!-- <td class="align-middle text-center small" ><?php echo $arr_det['ship_to_name'].'-'.$arr_det['ship_to_address']; ?></td> -->
                          <!-- <td class="align-middle text-center" ><?php echo $arr_det['delivering_plant']; ?></td> -->
                          <!-- <?php if(is_null($arr_det['ship_to_name'])){?>
                            <td class="align-middle text-center small" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                          <?php }else{?>
                              <td class="align-middle text-center small" ><?php echo $arr_det['ship_to_name']; ?></td>
                          <?php } ?> -->
                          <!-- <?php if(is_null($arr_det['ship_to_name'])){?>
                            <td class="align-middle text-center" >
                               <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Customer Not Registered</span>
                            </td>
                          <?php }else{?>
                            <td class="align-middle text-center" ><?php echo $arr_det['ship_to_address']; ?></td>
                          <?php } ?> -->

                          <!-- TRUCK TYPE -->
                          <?php if(are_strings_equal($arr_det['truck_type'],"ZL")){?>
                            <td class="align-middle text-center small">AUV/Pick-up</td>
                          <?php }?>

                          <?php if(are_strings_equal($arr_det['truck_type'],"ZH")){?>
                            <td class="align-middle text-center small">3 ton/4W Closed V</td>
                          <?php }?>

                          <?php if(are_strings_equal($arr_det['truck_type'],"ZD")){?>
                            <td class="align-middle text-center small">4 ton/6W Closed V</td>
                          <?php }?>
                          
                          <?php if(are_strings_equal($arr_det['truck_type'],"Y1")){?>
                            <td class="align-middle text-center small">26 Pallets - Trai</td>
                          <?php }?>

                          <?php if(are_strings_equal($arr_det['truck_type'],"Z6")){?>
                            <td class="align-middle text-center small">13 ton/10W Clsd V</td>
                          <?php }?>

                          <?php if(are_strings_equal($arr_det['truck_type'],"Z5")){?>
                            <td class="align-middle text-center small">13 ton/10W Wing V</td>
                          <?php }?>

                          <?php if(are_strings_equal($arr_det['truck_type'],"")){?>
                            <td class="align-middle text-center small">-</td>
                          <?php }?>
                        
                        
                          <td class="align-middle text-center small" ><?php echo $arr_det['plate_no']; ?></td>
                          <td class="align-middle text-center small" ><?php echo $arr_det['driver']; ?></td>
                          <td class="align-middle text-center small" ><?php echo $arr_det['helper']; ?></td>
                          
                          <!-- STATUS -->
                          <td class = 'align-middle text-center'>
                            <?php
                              if (are_strings_equal($arr_det['truck_type'],EmptyString) || are_strings_equal($arr_det['plate_no'],EmptyString) || are_strings_equal($arr_det['driver'],EmptyString) || are_strings_equal($arr_det['helper'],EmptyString)) {
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1 small'></i>Pending Truck Details</span>";
                              }else{
                                echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1 small'></i>For Dispatch</span>";
                              }
                            ?>
                          </td>

                          <!-- MODAL FOR UPDATE ASN DETAILS-->
                          <div id="update_details<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Truck Details</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="update_allocated_truck_details" method="post">
                                    <div class="form-group">
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_ref" id="db_ref" value="<?php echo $arr_det['ref_no']; ?>">
                                      </div>

                                      <!-- Truck Type -->
                                      <div class="mt-1">
                                        <label for="truck_type" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Picked SKU</label>
                                        <select id="truck_type" name="truck_type" class="form-control">
                                          <option value="">Select Truck Type </option>
                                          <option value="ZL">AUV/Pick-up</option>
                                          <option value="ZH">3 ton/4W Closed V</option>
                                          <option value="ZD">4 ton/6W Closed V</option>
                                          <option value="Y1">26 Pallets - Trai</option>
                                          <option value="Z6">13 ton/10W Clsd V</option>
                                          <option value="Z5">13 ton/10W Wing V</option>
                                        </select>
                                      </div>

                                      <!-- Plate No. -->
                                      <div class="mt-1">
                                        <label for="plate_no" class="form-control-label text-uppercase text-primary font-weight-bold">Plate No.</label>
                                        <input type="text" step="1" class="form-control" id="plate_no" name="plate_no" value="<?php echo $arr_det['plate_no'];  ?>">
                                      </div>

                                       <!-- Driver -->
                                       <div class="mt-1">
                                        <label for="driver" class="form-control-label text-uppercase text-primary font-weight-bold">Driver</label>
                                        <input type="text" step="1" class="form-control" id="driver" name="driver" value="<?php echo $arr_det['driver'];  ?>">
                                       </div>

                                       <!-- Helper -->
                                       <div class="mt-1">
                                        <label for="helper" class="form-control-label text-uppercase text-primary font-weight-bold">Helper</label>
                                        <input type="text" step="1" class="form-control" id="helper" name="helper" value="<?php echo $arr_det['helper'];  ?>">
                                      </div>
                                                    
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                                      <button type="submit" class="btn btn-primary">Save changes</button>
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