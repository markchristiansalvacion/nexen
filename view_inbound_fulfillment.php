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

        //print_r_html(strtotime("2021-11-15"));

        $date_today = date('Y-m-d');
        $week_start = date('Y-m-d',strtotime("sunday last week"));
        $week_end = date('Y-m-d',strtotime("saturday this week"));

        $db_inbound = array();

        $db_checker = $db->query('SELECT * FROM tb_users where user_type = ?','inbound checker')->fetch_all();

        $db_items = $db->query('SELECT sap_code,material_description FROM tb_items')->fetch_all();

        $db_asn = $db->query('SELECT 
            a.id,
            a.ref_no,
            a.uploading_file_name,
            a.transaction_type,
            a.pull_out_request_no,
            a.date_requested,
            a.pull_out_date,
            a.eta,
            a.ata,
            tb_source.source_name,
            a.forwarder,
            a.truck_type,
            a.driver,
            a.plate_no,
            a.sku_code,
            a.actual_sku,
            tb_items.material_description,
            a.qty_case,
            a.actual_qty,
            a.document_no,
            a.bay_location,
            a.checker,
            a.time_arrived,
            a.unloading_start,
            a.unloading_end,
            a.time_departed,
            a.remarks
            FROM tb_asn a
            INNER JOIN tb_source ON tb_source.source_code = a.source_code 
            INNER JOIN tb_items on tb_items.sap_code = a.sku_code
          WHERE a.eta BETWEEN ? AND ?
          ORDER BY a.eta DESC ', $week_start,$week_end)->fetch_all();

        print_r_html($db_asn);


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Incoming Shipment</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Action</th>
                        <!-- <th class="align-middle text-center">Uploading File Name</th> -->
                        <th class="align-middle text-center">Req. No</th>
                        <th class="align-middle text-center">PRF No.</th>
                        <th class="align-middle text-center">TO Ref. No.</th>
                        <th class="align-middle text-center">SKU</th>
                        <th class="align-middle text-center">Source</th>
                        <th class="align-middle text-center">Truck Type</th>
                        <th class="align-middle text-center">ETA</th>
                        <th class="align-middle text-center">Bay Allocation</th>
                        <th class="align-middle text-center">Action</th>
                        <th class="align-middle text-center">Goods Receipt</th>
                        <th class="align-middle text-center">Status</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_asn as $arr_key => $arr_det) { ?>
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
                                <a class="dropdown-item" data-toggle="modal" data-target="#update_details<?php echo $arr_det['id'];?>">View/Update Details</a>
                                <a class="dropdown-item" target="_blank" href="<?php echo "print_asn_slip?db_id={$arr_det['id']}";?>">Print ASN Slip</a>
                                <a class="dropdown-item" data-toggle="modal" data-target="#goods_receipt<?php echo $arr_det['id'];?>">Post Goods Receipt</a>
                                <?php if(!empty($arr_det['actual_qty']) || $arr_det['actual_qty'] != null){ ?>
                                  <a class="dropdown-item" data-toggle="modal" data-target="#goods_receipt<?php echo $arr_det['id'];?>">Print Goods Receipt</a>
                                <?php } ?>
                              </div>
                            </div>
                          </td>		
                          <!-- <td class="align-middle text-center" ><?php echo $arr_det['uploading_file_name']; ?></td> -->
                          <td class="align-middle text-center" ><?php echo $arr_det['pull_out_request_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo "PRF-" . $arr_det['ref_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['document_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['sku_code'].'-'.$arr_det['material_description']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['source_name']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['truck_type']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['eta']; ?></td>

                          <td class="align-middle text-center ">
                            <?php 
                              if(empty($arr_det['bay_location'])){
                                echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Unassigned</span>";
                              }else{
                                echo $arr_det['bay_location'];
                              }
                            ?>
                          </td>

            
                          <td class = 'align-middle text-center'>
                            <?php

                              if (empty($arr_det['actual_qty']) || $arr_det['actual_qty'] == null) {
                                echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Waiting</span>";
                              }else{

                                if($arr_det['qty_case'] != $arr_det['actual_qty']){
                                  echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Issue Incident Report</span>";
                                }else{
                                  echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Received In Full</span>";
                                }

                              }
                             
                              ?>
                          </td>

                          <td class = 'align-middle text-center'>
                            <?php

                              if (empty($arr_det['actual_qty']) || $arr_det['actual_qty'] == null) {
                                echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Pending</span>";
                              }else{

                                echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Done</span>";
                                

                              }
                             
                              ?>
                          </td>

                        
                          <td class = 'align-middle text-center'>
                            <?php
                              if ($arr_det['ata'] == NULL && $arr_det['time_arrived'] == NULL && $arr_det['unloading_start'] == NULL && $arr_det['unloading_end'] == NULL && $arr_det['time_departed'] == NULL) {
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>In Transit</span>";
                              }

                              if ($arr_det['ata'] != NULL) {

                                if($arr_det['time_arrived'] != NULL && $arr_det['unloading_start'] == NULL && $arr_det['unloading_end'] == NULL &&  $arr_det['time_departed'] == NULL){
                                  echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Unload Pending</span>";
                                }

                                if($arr_det['time_arrived'] != NULL && $arr_det['unloading_start'] != NULL && $arr_det['unloading_end'] == NULL &&  $arr_det['time_departed'] == NULL){
                                  echo "<span class='badge badge-sm light badge-primary'> <i class='fa fa-circle text-primary me-1'></i>Ongoing</span>";
                                }

                                
                                if($arr_det['time_arrived'] != NULL && $arr_det['unloading_start'] != NULL && $arr_det['unloading_end'] != NULL &&  $arr_det['time_departed'] == NULL){
                                  echo "<span class='badge badge-sm light badge-info'> <i class='fa fa-circle text-info me-1'></i>Waiting Documents</span>";
                                }

                                if($arr_det['time_arrived'] != NULL && $arr_det['unloading_start'] != NULL && $arr_det['unloading_end'] != NULL &&  $arr_det['time_departed'] != NULL){
                                  echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Dispatched</span>";
                                }
                               
                              }

                             
                              ?>
                          </td>
                         
                        
    
                          <!-- MODAL FOR UPDATE ASN DETAILS-->
                          <div id="update_details<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update ASN Details</h4>

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
                                      <button type="submit" class="btn btn-primary">Save changes</button>
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
                                      <button type="submit" class="btn btn-primary">Goods Receipt</button>

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