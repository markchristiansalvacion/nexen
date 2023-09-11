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
            INNER JOIN tb_items on tb_items.sap_code = a.sku_code
            INNER JOIN tb_asn ON tb_asn.id = tb_assembly_build.asn_id
            WHERE a.putaway_status = "Pending"
            AND a.transaction_type = "INB"
            GROUP BY tb_assembly_build.document_no')->fetch_all();

       // print_r_html($db_for_put_away);


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">For Putaway</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Action</th>
                        <th class="align-middle text-center">Date Received</th>
                        <th class="align-middle text-center">Source Document</th>
                        <!-- <th class="align-middle text-center">LPN</th>
                        <th class="align-middle text-center">Suggested Location</th>
                        <th class="align-middle text-center">SKU</th>
                        <th class="align-middle text-center">Qty Case</th>
                        <th class="align-middle text-center">BBD</th> -->
                        <th class="align-middle text-center">Putaway Status</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_for_put_away as $arr_key => $arr_det) { ?>
                        <tr>

                        <!-- ACTION ITEMS -->
                          <!-- <td>
                            <div class="dropdown ms-auto text-end">
                              <div class="btn sharp btn-warning tp-btn ms-auto" data-bs-toggle="dropdown">
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M12.2835 13C12.8354 13 13.2828 12.5523 13.2828 12C13.2828 11.4477 12.8354 11 12.2835 11C11.7316 11 11.2842 11.4477 11.2842 12C11.2842 12.5523 11.7316 13 12.2835 13Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M12.2835 6C12.8354 6 13.2828 5.55228 13.2828 5C13.2828 4.44772 12.8354 4 12.2835 4C11.7316 4 11.2842 4.44772 11.2842 5C11.2842 5.55228 11.7316 6 12.2835 6Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M12.2835 20C12.8354 20 13.2828 19.5523 13.2828 19C13.2828 18.4477 12.8354 18 12.2835 18C11.7316 18 11.2842 18.4477 11.2842 19C11.2842 19.5523 11.7316 20 12.2835 20Z" stroke="#342E59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                              </div>
                              <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" target="_blank" href="<?php echo "print_label?document_no={$arr_det['document_no']}";?>">Print Pallet Tag</a>
                                <a class="dropdown-item" target="_blank" href="<?php echo "print_put_away_form?document_no={$arr_det['document_no']}";?>">Print Putaway Form</a>
                            </div>
                          </td>		 -->
                          <td>
                            <div class="d-flex">
                              <a target="_blank" href="<?php echo "print_label?document_no={$arr_det['document_no']}";?>" class="btn btn-warning shadow btn-xs sharp me-1" title="Print Pallet Tag"><i class="fa-solid fa-print"></i></a>
                              <a target="_blank" href="<?php echo "print_put_away_form?document_no={$arr_det['document_no']}";?>" class="btn btn-success shadow btn-xs sharp me-1" title="Print Putaway Form"><i class="fa-solid fa-print"></i></a>
                            </div>												
												  </td>
                          <td class="align-middle text-center" ><?php echo $arr_det['last_updated']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['document_no']; ?></td>
                          <!-- <td class="align-middle text-center" ><?php echo $arr_det['lpn']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['bin_loc']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['sku_code'].'-'.$arr_det['material_description']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['qty_case']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['expiry']; ?></td> -->
                          <td class="align-middle text-center" ><?php echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>{$arr_det['putaway_status']}</span>"; ?></td>

                         
                         
                        
    
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