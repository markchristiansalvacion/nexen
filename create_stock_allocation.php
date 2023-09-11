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

        $transfer_order = $db->query('SELECT
                          a.id,
                          a.so_no,
                          a.sku_code,
                          a.ship_to_code,
                          tb_items.material_description,
                          tb_customer.req_shelf_life,
                          a.req_qty_case,
                          a.allocated_qty
                          FROM tb_transfer_order a
                          LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
                          LEFT JOIN tb_customer ON tb_customer.ship_to_code = a.ship_to_code
                          WHERE a.status = ?
                          AND a.sku_code = ?
                          AND a.ship_to_code =?
                          AND a.rdd = ?',"Allocation",$_GET['sku_code'],$_GET['customer_code'],$_GET['rdd'])->fetch_all();

                          // print_r_html($transfer_order);
        ?>
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h6 class="card-title"><a href="check_stock"><i class="fa-solid fa-square-caret-left"></i> Back</a> / Stock Allocation List</h6>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Action</th>
                        <th class="align-middle text-center  font-weight-bold ">TO ID</th>
                        <th class="align-middle text-center  font-weight-bold ">SO #</th>
                        <th class="align-middle text-center  font-weight-bold ">SKU Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Material Description</th>
                        <th class="align-middle text-center  font-weight-bold ">Order Qty</th>
                        <th class="align-middle text-center  font-weight-bold ">Picked</th>
                        <th class="align-middle text-center  font-weight-bold ">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($transfer_order  as $asar_key => $asar_val) {?>
                        <tr>
                          <td> 
                            <div class="d-flex">
                              <?php if(are_strings_equal($_GET['alloc_status'],"GOOD")){ ?>
                                      <a data-toggle="modal" data-target="#allocate_stock<?php echo $asar_val['id'];?>"  class="btn btn-success shadow btn-xs sharp me-1" title="Auto Allocation"><i class="fa-solid fa-dolly"></i></a>
                                      <a data-toggle="modal" data-target="#cancel_order<?php echo $asar_val['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Cancel Order"><i class="fa-solid fa-trash-can"></i></a>
                              <?php }else{ ?>
                                      <?php if(are_strings_equal($_GET['alloc_status'],"SHORT")){ ?>
                                            <a data-toggle="modal" data-target="#allocate_stock<?php echo $asar_val['id'];?>"  class="btn btn-success shadow btn-xs sharp me-1" title="Auto Allocation (Short)"><i class="fa-solid fa-dolly"></i></a>
                                            <a data-toggle="modal" data-target="#cancel_order<?php echo $asar_val['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Cancel Order"><i class="fa-solid fa-trash-can"></i></a>
                                      <?php }else{ ?>
                                            <a data-toggle="modal" data-target="#cancel_order<?php echo $asar_val['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Cancel Order"><i class="fa-solid fa-trash-can"></i></a>
                                      <?php } ?>
                              <?php } ?>
                            </div>												
												  </td>
                          <td class="align-middle text-center "><?php echo $asar_val['id'];?></td>
                          <td class="align-middle text-center "><?php echo $asar_val['so_no']; ?></td>
                          <td class="align-middle text-center "><?php echo $asar_val['sku_code']; ?></td>
                          <td class="align-middle text-center "><?php echo $asar_val['material_description']; ?></td>
                          <td class="align-middle text-center "><?php echo number_format($asar_val['req_qty_case'], 2, ".", ",");?></td>
                          <td class="align-middle text-center "><?php echo number_format($asar_val['allocated_qty'], 2, ".", ",");?></td>
                          <td class="align-middle text-center "><?php
                            if ($asar_val['req_qty_case'] != $asar_val['allocated_qty']) {
                              echo '<span class="badge badge-warning">Pending Allocation</span>';
                            } else {
                              echo '<span class="badge badge-success">Done Allocation</span>';
                            }
                          ?></td>

                        <!-- MODAL FOR STOCK ALLOCATION-->
                        <div id="allocate_stock<?php echo $asar_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Auto Stock Allocation</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="create_stock_allocation_proc" method="post">
                                    <div class="form-group">

                                      <h4>Do you wish to proceed allocation of the following order?</h4>
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="to_db_id" id="to_db_id" value="<?php echo $asar_val['id']; ?>">
                                      </div>

                                      <!-- Customer Code-->
                                      <div class="mt-1">
                                        <input type="hidden" name="customer_code" id="customer_code" value="<?php echo $_GET['customer_code']; ?>">
                                      </div>

                                      <!-- Allocation Status -->
                                      <div class="mt-1">
                                        <input type="hidden" name="alloc_status" id="alloc_status" value="<?php echo $_GET['alloc_status']; ?>">
                                      </div>

                                      <!-- RDD-->
                                      <div class="mt-1">
                                        <input type="hidden" name="rdd" id="rdd" value="<?php echo $_GET['rdd']; ?>">
                                      </div>

                                      <!-- SO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="so_no" id="so_no" value="<?php echo $asar_val['so_no']; ?>">
                                      </div>
                                      
                                      <!-- SKU-->
                                      <div class="mt-1">
                                        <input type="hidden" name="sku_code" id="sku_code" value="<?php echo $asar_val['sku_code']; ?>">
                                      </div>

                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <input type="hidden" name="req_qty" id="req_qty" value="<?php echo $asar_val['req_qty_case']; ?>">
                                      </div>

                                       <!-- Shelf Life-->
                                       <div class="mt-1">
                                        <input type="hidden" name="req_shelf_life" id="req_shelf_life" value="<?php echo $asar_val['req_shelf_life']; ?>">
                                      </div>

                                      <!-- Document -->
                                      <div class="mt-1">
                                        <label for="source_doc" class="form-control-label text-uppercase text-primary font-weight-bold">SO NO.</label>
                                        <input type="text" step="1" class="form-control" id="source_doc" value="<?php echo $asar_val['so_no']?>" disabled>
                                      </div>

                                      <!-- SKU -->
                                      <div class="mt-1">
                                        <label for="sku" class="form-control-label text-uppercase text-primary font-weight-bold">SKU</label>
                                        <input type="text" step="1" class="form-control" id="sku" value="<?php echo $asar_val['sku_code'].'-'.$asar_val['material_description'];  ?>" disabled>
                                      </div>

                                      <!-- Qty -->
                                      <div class="mt-1">
                                        <label for="req_qty_case" class="form-control-label text-uppercase text-primary font-weight-bold">Required Quantity (Cases)</label>
                                        <input type="number" step="1" class="form-control" id="req_qty_case" value="<?php echo $asar_val['req_qty_case']?>" disabled>
                                      </div>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
                                      <button type="submit" class="btn btn-primary">Proceed</button>
                                    </div>

                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- End Modal -->
                          

                          <!-- MODAL FOR CANCEL ORDER-->
                        <div id="cancel_order<?php echo $asar_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Cancel Order</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="cancel_order" method="post">
                                    <div class="form-group">

                                    <h4>Do you wish to cancel the following order?</h4>

                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="to_db_id" id="to_db_id" value="<?php echo $asar_val['id']; ?>">
                                      </div>

                                      <!-- URL-->
                                      <div class="mt-1">
                                        <input type="hidden" name="url" id="url" value="<?php echo "create_stock_allocation?sku_code={$_GET['sku_code']}&customer_code={$_GET['customer_code']}&rdd={$_GET['rdd']}&alloc_status={$_GET['alloc_status']}"; ?>">
                                      </div>

                                      <!-- Customer Code-->
                                      <div class="mt-1">
                                        <input type="hidden" name="customer_code" id="customer_code" value="<?php echo $_GET['customer_code']; ?>">
                                      </div>

                                      <!-- RDD-->
                                      <div class="mt-1">
                                        <input type="hidden" name="rdd" id="rdd" value="<?php echo $_GET['rdd']; ?>">
                                      </div>

                                      <!-- SO-->
                                      <div class="mt-1">
                                        <input type="hidden" name="so_no" id="so_no" value="<?php echo $asar_val['so_no']; ?>">
                                      </div>
                                      
                                      <!-- SKU-->
                                      <div class="mt-1">
                                        <input type="hidden" name="sku_code" id="sku_code" value="<?php echo $asar_val['sku_code']; ?>">
                                      </div>

                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <input type="hidden" name="req_qty" id="req_qty" value="<?php echo $asar_val['req_qty_case']; ?>">
                                      </div>

                                       <!-- Shelf Life-->
                                       <div class="mt-1">
                                        <input type="hidden" name="req_shelf_life" id="req_shelf_life" value="<?php echo $asar_val['req_shelf_life']; ?>">
                                      </div>

                                      <!-- Document -->
                                      <div class="mt-1">
                                        <label for="source_doc" class="form-control-label text-uppercase text-primary font-weight-bold">SO NO.</label>
                                        <input type="text" step="1" class="form-control" id="source_doc" value="<?php echo $asar_val['so_no']?>" disabled>
                                      </div>

                                      <!-- SKU -->
                                      <div class="mt-1">
                                        <label for="sku" class="form-control-label text-uppercase text-primary font-weight-bold">SKU</label>
                                        <input type="text" step="1" class="form-control" id="sku" value="<?php echo $asar_val['sku_code'].'-'.$asar_val['material_description'];  ?>" disabled>
                                      </div>

                                      <!-- Qty -->
                                      <div class="mt-1">
                                        <label for="req_qty_case" class="form-control-label text-uppercase text-primary font-weight-bold">Required Quantity (Cases)</label>
                                        <input type="number" step="1" class="form-control" id="req_qty_case" value="<?php echo $asar_val['req_qty_case']?>" disabled>
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