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
        $week_end = date('Y-m-d',strtotime("saturday this week +4 days"));

        $db_inbound = array();

        $db_checker = $db->query('SELECT * FROM tb_users where user_type = ?','checker')->fetch_all();

        $db_items = $db->query('SELECT sku_code,material_description FROM tb_items')->fetch_all();

        $db_asn = $db->query('SELECT 
            a.id,
            a.ref_no,
            a.uploading_file_name,
            a.eta,
            a.ata,
            a.source_code,
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
            LEFT JOIN tb_items on tb_items.sku_code = a.sku_code
          WHERE a.eta BETWEEN ? AND ?
          ORDER BY a.eta DESC,a.id ASC ', $week_start,$week_end)->fetch_all();

        //print_r_html($db_asn);


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
                        <th class="align-middle text-center">ID</th>
                        <th class="align-middle text-center">Uploading File Name</th>
                        <th class="align-middle text-center">PRF</th>
                        <th class="align-middle text-center">Source Doc.</th>
                        <th class="align-middle text-center">ETA</th>
                        <th class="align-middle text-center">Source</th>
                        <th class="align-middle text-center">SKU</th>
                        <th class="align-middle text-center">Truck Type</th>
                        <th class="align-middle text-center">Receipt Status</th>
                        <th class="align-middle text-center">Action</th>
                        <th class="align-middle text-center">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_asn as $arr_key => $arr_det) { ?>
                        <tr>
                          <td>
                            <div class="d-flex">
                              <?php if(is_null($arr_det['material_description'])){?>
                                <a  data-toggle="modal" data-target="#update_details<?php echo $arr_det['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Disabled" style="pointer-events: none"><i class="fas fa-pencil-alt"></i></a>
                              <?php }else{ ?>
                                <a  data-toggle="modal" data-target="#update_details<?php echo $arr_det['id'];?>" class="btn btn-primary shadow btn-xs sharp me-1" title="View/Update"><i class="fas fa-pencil-alt"></i></a>
                              <?php } ?>
                              <!-- <a  data-toggle="modal" data-target="#update_details<?php echo $arr_det['id'];?>" class="btn btn-primary shadow btn-xs sharp me-1" title="View/Update"><i class="fas fa-pencil-alt"></i></a> -->

                              <?php if(is_null($arr_det['material_description'])){?>
                                <a target="_blank" href="<?php echo "print_asn_slip?db_id={$arr_det['id']}";?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Disabled" style="pointer-events: none"><i class="fa-solid fa-print"></i></a>
                              <?php }else{ ?>
                                <a target="_blank" href="<?php echo "print_asn_slip?db_id={$arr_det['id']}";?>" class="btn btn-warning shadow btn-xs sharp me-1" title="ASN Slip"><i class="fa-solid fa-print"></i></a>
                              <?php } ?>

                              <?php if(is_null($arr_det['material_description'])){?>
                                <a data-toggle="modal" data-target="#goods_receipt<?php echo $arr_det['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Disabled" style="pointer-events: none"><i class="fa-solid fa-box"></i></a>
                              <?php }else{ ?>
                                <a data-toggle="modal" data-target="#goods_receipt<?php echo $arr_det['id'];?>" class="btn btn-info shadow btn-xs sharp me-1" title="Post Goods"><i class="fa-solid fa-box"></i></a>
                              <?php } ?>

                              
                              <?php if($arr_det['actual_qty'] != 0){ ?>
                                <a target="_blank" href="<?php echo "print_goods_receipt?doc_no={$arr_det['document_no']}&asn_id={$arr_det['id']}";?>" class="btn btn-success shadow btn-xs sharp me-1" title="Print Receipt"><i class="fa-solid fa-print"></i></a>
                                <?php if($arr_det['qty_case'] != $arr_det['actual_qty'] || $arr_det['sku_code'] != $arr_det['actual_sku']){ ?>
                                  <a  data-toggle="modal" data-target="#incident_report<?php echo $arr_det['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Incident Report"><i class="fa-solid fa-triangle-exclamation"></i></a>
                                <?php } ?>
                              <?php } ?>
                            </div>												
												  </td>
                          <td class="align-middle text-center" ><?php echo $arr_det['id']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['uploading_file_name']; ?></td>
                          <td class="align-middle text-center" ><?php echo "PRF-" . $arr_det['ref_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['document_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['eta']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['source_code']; ?></td>
                          <?php if(is_null($arr_det['material_description'])){ ?>
                            <td>
                              <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Item Not in Masterlist</span>
                            </td>
                          <?php }else{?>
                            <td class="align-middle text-center "><?php echo $arr_det['sku_code']; ?></td>
                          <?php } ?>
                          <td class="align-middle text-center "><?php echo $arr_det['truck_type']; ?></td>
                          
            
                          <td class = 'align-middle text-center'>
                            <?php

                              if (empty($arr_det['actual_qty']) || $arr_det['actual_qty'] == 0) {
                                echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Pending</span>";
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

                              if (empty($arr_det['actual_qty']) || $arr_det['actual_qty'] == 0) {
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

                          <!-- INCIDENT REPORT MODAL-->
                          <div id="incident_report<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Create Incident Report</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="create_inbound_ir" method="post">
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

                                      <!-- NATURE OF DAMAGE-->
                                      <div class="mt-1">
                                        <label for="reason" class="form-control-label text-uppercase text-primary font-weight-bold">Nature of Incident</label>
                                        <select name="reason" id="reason" class="form-control">
                                            <option value="">Select Nature of Incident</option>
                                            <option value="Leakers">Leakers</option>
                                            <option value="Lacking/Missing">Lacking/Missing</option>
                                            <option value="Broken Packaging">Broken Packaging</option>
                                            <option value="Others">Others</option>
                                            <option value="Late Truck Arrival">Late Truck Arrival</option>
                                            <option value="Planned vs Actual Not Match">Planned vs Actual Not Match</option>
                                        </select>
                                      </div>

                                      <div class="mt-1">
                                        <label for="actual_sku" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Affected SKU</label>
                                        <input list="actual_sku" class="form-control" name="actual_sku">
                                        <datalist id="actual_sku">
                                          <?php foreach($db_items as $arr_key => $arr_val){  ?>
                                            <option value="<?php echo $arr_val['sku_code']; ?>"><?php echo $arr_val['sku_code'].'-'.$arr_val['material_description']; ?></option>
                                          <?php } ?>
                                        </datalist>
                                      </div>

                                      <div class="mt-1">
                                        <label for="actual_qty" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Affected Quantity (Cases)</label>
                                        <input type="number" step="1" class="form-control" id="actual_qty" name="actual_qty">
                                      </div>

                                      <div class="mt-1">
                                        <label for="expiration_date" class="form-control-label text-uppercase text-primary font-weight-bold">Expiration Date/Best Before Date (BBD)</label>
                                        <input type="date" class="form-control" id="expiration_date" name="expiration_date">
                                      </div>

                                      <div class="mt-1">
                                        <label for="description" class="form-control-label text-uppercase text-primary font-weight-bold">Description</label>
                                        <textarea class="form-control" name="description" id="description" rows="4"></textarea>
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

                                        <input list="actual_sku" class="form-control" name="actual_sku">
                                        <datalist id="actual_sku">
                                          <?php foreach($db_items as $arr_key => $arr_val){  ?>
                                            <option value="<?php echo $arr_val['sku_code']; ?>"><?php echo $arr_val['sku_code'].'-'.$arr_val['material_description']; ?></option>
                                          <?php } ?>
                                        </datalist>
                                      </div>

                                      <div class="mt-1">
                                        <label for="actual_qty" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Received Quantity (Pc)</label>
                                        <input type="number" step="1" class="form-control" id="actual_qty" name="actual_qty">
                                      </div>

                                      <div class="mt-1">
                                        <label for="expiration_date" class="form-control-label text-uppercase text-primary font-weight-bold">Serial No.</label>
                                        <input type="text" class="form-control" id="serial_no" name="serial_no">
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