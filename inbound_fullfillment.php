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

        $db_items = $db->query('SELECT sap_code,material_description FROM tb_items')->fetch_all();
  
        $get_ab = $db->query('SELECT 
                              tb_assembly_build.id,
                              tb_assembly_build.ab_ref_no,
                              tb_assembly_build.asn_id,
                              tb_assembly_build.asn_ref_no,
                              tb_assembly_build.document_no,
                              tb_assembly_build.sku_code,
                              tb_assembly_build.qty_case,
                              tb_assembly_build.expiry,
                              tb_items.material_description,
                              tb_assembly_build.fulfillment_status,
                              tb_asn.ata
                              FROM tb_assembly_build 
                              INNER JOIN tb_asn ON tb_asn.id = tb_assembly_build.asn_id
                              INNER JOIN tb_items ON tb_items.sap_code = tb_assembly_build.sku_code
                              WHERE fulfillment_status = ?', "Pending")->fetch_all();

        //print_r_html($get_ab);
      ?>

        <!-- row -->

        <div class="row">

          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Pending Fulfillment</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table id="view_fullfillment_table" class="display" style="min-width: 845px"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center ">Action</th>
                        <th class="align-middle text-center ">ID</th>
                        <th class="align-middle text-center ">AB No.</th>
                        <th class="align-middle text-center ">Document No</th>
                        <th class="align-middle text-center ">Date Received</th>
                        <th class="align-middle text-center ">Material Description</th>
                        <th class="align-middle text-center ">Qty Received</th>
                        <th class="align-middle text-center ">BBD</th>
                        <th class="align-middle text-center ">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($get_ab as $arr_key => $arr_val) { ?>
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
                                <a class="dropdown-item" data-toggle="modal" data-target="#update_fulfillment<?php echo $arr_val['id'];?>">Fulfillment Confirmation</a>
                              </div>
                            </div>
                          </td>		 -->
                          <td class="align-middle text-center ">
                            <div class="d-flex">
                              <a data-toggle="modal" data-target="#edit_details<?php echo $arr_val['id'];?>" class="btn btn-warning shadow btn-xs sharp me-1" title ="Update"><i class="fas fa-pencil-alt"></i></a>
                              <a data-toggle="modal" data-target="#update_fulfillment<?php echo $arr_val['id'];?>" class="btn btn-success shadow btn-xs sharp me-1" title ="Fulfillment Confirmation"><i class="fa-solid fa-circle-check"></i></a>
                            </div>
                          </td>
                          <td class="align-middle text-center "><?php echo $arr_val['id']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['ab_ref_no']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['document_no']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['ata']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['sku_code'].'-'.$arr_val['material_description']; ?></td>
                          <td class="align-middle text-center "><?php echo number_format($arr_val['qty_case'],2,".",","); ?></td>
                          <td class="align-middle text-center "><?php echo date('d-M-Y',strtotime($arr_val['expiry'])); ?></td>
                          <td class="align-middle text-center" ><?php echo "<span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>{$arr_val['fulfillment_status']}</span>"; ?></td>

                        <!-- MODAL FOR UPDATE ASN DETAILS-->
                        <div id="update_fulfillment<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Fulfillment Confirmation</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="update_fulfillment_proc" method="post">
                                    <div class="form-group">
                                      <h4>Do you wish to proceed the Fulfillment of the following stocks?</h4>

                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>

                                      <!-- ASN REF-->
                                      <div class="mt-1">
                                        <input type="hidden" name="asn_ref" id="asn_ref" value="<?php echo $arr_val['asn_ref_no']; ?>">
                                      </div>

                                      <!-- Doc-->
                                      <div class="mt-1">
                                        <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>
                                      
                                      <!-- SKU-->
                                      <div class="mt-1">
                                        <input type="hidden" name="sku_code" id="sku_code" value="<?php echo $arr_val['sku_code']; ?>">
                                      </div>

                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <input type="hidden" name="qty_case" id="qty_case" value="<?php echo $arr_val['qty_case']; ?>">
                                      </div>

                                      <!-- EXPIRATION-->
                                      <div class="mt-1">
                                        <input type="hidden" name="expiration_date" id="expiration_date" value="<?php echo $arr_val['expiry']; ?>">
                                      </div>

                                      <!-- Document -->
                                      <div class="mt-1">
                                        <label for="source_doc" class="form-control-label text-uppercase text-primary font-weight-bold">Document Ref.</label>
                                        <input type="text" step="1" class="form-control" id="source_doc" value="<?php echo $arr_val['document_no']?>" disabled>
                                      </div>

                                      <!-- SKU -->
                                      <div class="mt-1">
                                        <label for="sku" class="form-control-label text-uppercase text-primary font-weight-bold">SKU</label>
                                        <input type="text" step="1" class="form-control" id="sku" value="<?php echo $arr_val['sku_code'].'-'.$arr_val['material_description'];  ?>" disabled>
                                      </div>

                                      <!-- Qty -->
                                      <div class="mt-1">
                                        <label for="qty_case" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Received Quantity (Cases)</label>
                                        <input type="number" step="1" class="form-control" id="qty_case" value="<?php echo $arr_val['qty_case']?>" disabled>
                                      </div>

                                      <!-- Expiration Date -->
                                      <div class="mt-1">
                                        <label for="expiration_date" class="form-control-label text-uppercase text-primary font-weight-bold">Expiration Date/Best Before Date (BBD)</label>
                                        <input type="date" class="form-control" id="expiration_date" value="<?php echo $arr_val['expiry'];?>" disabled>
                                      </div>

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
                          <!-- End Modal -->

                        <!-- MODAL FOR EDIT AB DETAILS-->
                        <div id="edit_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Details</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="update_assembly_build_details" method="post">
                                    <div class="form-group">
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>

                                      <!-- ASN REF-->
                                      <div class="mt-1">
                                        <input type="hidden" name="asn_ref" id="asn_ref" value="<?php echo $arr_val['asn_ref_no']; ?>">
                                      </div>

                                      <!-- Document -->
                                      <div class="mt-1">
                                        <label for="source_doc" class="form-control-label text-uppercase text-primary font-weight-bold">Document Ref.</label>
                                        <input type="text" step="1" class="form-control" id="source_doc" name="source_doc" value="<?php echo $arr_val['document_no']; ?>">
                                      </div>

                                      <!-- SKU -->
                                      <div class="mt-1">
                                        <label for="actual_sku" class="form-control-label text-uppercase text-primary font-weight-bold">Actual Received SKU</label>
                                        <input list="actual_sku" class="form-control" name="actual_sku" value="<?php echo $arr_val['sku_code']; ?>">
                                        <datalist id="actual_sku">
                                          <?php foreach($db_items as $arr_key => $db_val){  ?>
                                            <option value="<?php echo $db_val['sap_code']; ?>">
                                              <?php echo $db_val['sap_code'].'-'.$db_val['material_description']; ?>
                                            </option>
                                          <?php } ?>
                                        </datalist>
                                      </div>

                                      <!-- Qty -->
                                      <div class="mt-1">
                                        <label for="qty_case" class="form-control-label text-uppercase text-primary font-weight-bold">
                                          Actual Received Quantity (Cases)
                                        </label>
                                        <input type="number" step = "1" class="form-control" id="qty_case" name="qty_case" value="<?php echo $arr_val['qty_case']; ?>"></input>
                                      </div>

                                      <!-- Expiration Date -->
                                      <div class="mt-1">
                                        <label for="expiration_date" class="form-control-label text-uppercase text-primary font-weight-bold">Expiration Date/Best Before Date (BBD)</label>
                                        <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="<?php echo $arr_val['expiry']; ?>">
                                      </div>

                                    </div>

                                    <div class="d-grid gap-1 mt-3">
                                        <button type="button" data-dismiss="modal" class="btn btn-danger">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
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
                  </table>
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