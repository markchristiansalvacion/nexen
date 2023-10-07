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
<?php

$all_items = $db->query('SELECT * FROM tb_items ORDER BY id DESC')->fetch_all();

?>


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
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Manage Items</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="view_asn_table"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">SKU</th>
                        <th class="align-middle text-center  font-weight-bold ">Mat. Desc.</th>
                        <th class="align-middle text-center  font-weight-bold ">Category</th>
                        <th class="align-middle text-center  font-weight-bold ">Size</th>
                        <!-- <th class="align-middle text-center  font-weight-bold ">Manufacturer</th> -->
                         <!-- <th class="align-middle text-center  font-weight-bold ">Weight Per Box</th> -->
                        <!-- <th class="align-middle text-center  font-weight-bold ">CBM Per Box</th> -->
                        <th class="align-middle text-center  font-weight-bold ">Load Index</th>
                        <th class="align-middle text-center  font-weight-bold ">Speed Rating</th>
                        <th class="align-middle text-center  font-weight-bold ">Ply Rating</th>
                        <!-- <th class="align-middle text-center  font-weight-bold ">Origin</th> -->
                        <!-- <th class="align-middle text-center  font-weight-bold ">Action</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>
                          <td class="align-middle text-center"><?php echo $arr_val['sku_code']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['material_description']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['category']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['tire_size']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['load_index']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['speed_rating']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['ply_rating']; ?></td>
                          <!-- <td> -->
                            <!-- <div class="d-flex"> -->
                              <!-- <a data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" class="btn btn-info shadow btn-xs sharp" title="Update"><i class="fa-solid fa-pen-to-square"></i></a> -->
                              <!-- <a target="_blank" href="<?php echo "print_put_away_form?document_no={$arr_det['document_no']}";?>" class="btn btn-success shadow btn-xs sharp me-1" title="Print Putaway Form"><i class="fa-solid fa-print"></i></a> -->
                            <!-- </div>												 -->
												  <!-- </td> -->
                          <!-- <td>
                            <div class="dropdown">
                              <button type="button" class="btn btn-success light sharp" data-bs-toggle="dropdown">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                  </g>
                                </svg>
                              </button>
                              <div class="dropdown-menu">
                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#view_details<?php echo $arr_val['id']; ?>" class=" dropdown-item" title="View Details">View Details</a>

                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#update_details<?php echo $arr_val['id']; ?>" class=" dropdown-item" title="Update Details">Update</a>

                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#delete<?php echo $arr_val['id']; ?>" class="dropdown-item" style="color:red;">Delete</a>
                              </div>
                            </div>
                          </td> -->
                          <!-- MODAL FOR PICKING START-->
                          <div id="update_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Update Item Details</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="update_items" method="post">
                                    <div class="form-group">
                                      <div class="mt-1">
                                        <input type="hidden" class="form-control" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>

                                      <!-- ITEM CODE-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Item Code</label>
                                        <input type="text" class="form-control" name="item_code" id="item_code" value="<?php echo $arr_val['sap_code']; ?>">
                                      </div>

                                      <!--CAT CODE-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase text-primary font-weight-bold">Category</label>
                                        <input type="text" class="form-control" name="cat" id="cat" value="<?php echo $arr_val['category']; ?>">
                                      </div>

                                      <!-- Description-->
                                      <div class="mt-1">
                                        <label for="qty_pcs" class="form-control-label text-uppercase text-primary font-weight-bold">Item Description</label>
                                        <input type="text" class="form-control" name="descrip" id="descrip" value="<?php echo $arr_val['material_description']; ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="weight_per_case" class="form-control-label
															text-uppercase text-primary font-weight-bold">Weight Per Box</label>
                                        <input type="text" class="form-control" name="weight_per_case" id="weight_per_case" value="<?php echo $arr_val['weight_per_case'];
                                                                                                                                  ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="cbm_per_case" class="form-control-label
															text-uppercase text-primary font-weight-bold">CBM per Box</label>
                                        <input type="text" class="form-control" name="cbm_per_case" id="cbm_per_case" value="<?php echo $arr_val['cbm_per_case'];
                                                                                                                            ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="qty_pcs" class="form-control-label
															text-uppercase text-primary font-weight-bold">Pack
                                          Size</label>
                                        <input type="text" class="form-control" name="p_size" id="p_size" value="<?php echo $arr_val['pack_size'];
                                                                                                                  ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="qty_pcs" class="form-control-label
															text-uppercase text-primary font-weight-bold">Case
                                          Per Pallet</label>
                                        <input type="text" class="form-control" name="c_pallet" id="c_pallet" value="<?php echo $arr_val['case_per_pallet']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <label for="running_bal" class="form-control-label
                                                            text-uppercase text-primary
                                                            font-weight-bold">PCS PER PALLET</label>
                                        <input type="text" class="form-control" name="p_pallet" id="p_pallet" value="<?php
                                                                                                                      echo $arr_val['pcs_per_pallet']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <label for="running_bal" class="form-control-label
                                                            text-uppercase text-primary
                                                            font-weight-bold">CASE PER TIER</label>
                                        <input type="text" class="form-control" name="c_tier" id="c_tier" value="<?php
                                                                                                                  echo $arr_val['case_per_tier']; ?>">
                                      </div>
                                      <div class="mt-1">
                                        <label for="running_bal" class="form-control-label
                                                            text-uppercase text-primary
                                                            font-weight-bold">LAYER/HIGH</label>
                                        <input type="text" class="form-control" name="layer" id="layer" value="<?php
                                                                                                                echo $arr_val['layer']; ?>">
                                      </div>

                                      <div class="mt-1">
                                        <label for="running_bal" class="form-control-label
                                                            text-uppercase text-primary
                                                            font-weight-bold">SHELF LIFE (MONTHS)</label>
                                        <input type="text" class="form-control" name="shelf_life" id="shelf_life" value="<?php
                                                                                                                          echo $arr_val['shelf_life']; ?>">
                                      </div>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="submit" class="btn btn-success btn-block btn-lg">Update Details</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- End Modal -->
                          <!-- MODAL FOR UPDATE ASN DETAILS-->
                          <div id="view_details<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Item Details</h4>
                                </div>
                                <div class="modal-body">
                                  <form action="" method="post">
                                    <div class="form-group">
                                      <div class="col-xl-6">
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom05">Weight Per Box:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['weight_per_case']; ?>

                                            </label>
                                          </div>
                                        </div>
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom02">CBM Per Box:
                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['cbm_per_case']; ?>

                                            </label>

                                          </div>
                                        </div>
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom03">Pack Size:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['pack_size']; ?>

                                            </label>
                                          </div>
                                        </div>
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom03">Case Per Pallet:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['case_per_pallet']; ?>

                                            </label>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-xl-6">
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom03">Stacking Height:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['layer']; ?>

                                            </label>
                                          </div>
                                        </div>
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom03">Case Per Tier:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['case_per_tier']; ?>

                                            </label>
                                          </div>
                                        </div>
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom05">Pcs Per Pallet:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['pcs_per_pallet']; ?>

                                            </label>
                                          </div>
                                        </div>
                                        <div class="mb-3 row">
                                          <label class="col-lg-6 col-form-label" for="validationCustom03">Shelf Life:

                                          </label>
                                          <div class="col-lg-6">
                                            <label class="col-lg-12 col-form-label" for="validationCustom03"><?php echo $arr_val['shelf_life']; ?>

                                            </label>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- End Modal -->




                          <!-- MODAL FOR DELETE-->
                          <div id="delete<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Delete ASN</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="inbound_delete_posted_asn" method="post">
                                    <div class="form-group">

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
  <script>
    $(document).ready(function() {
      $('#view_asn_table').DataTable({
        order: [
          [0, "desc"]
        ],
        lengthMenu: [
          [5],
          [5]
        ]
      });
    });
  </script>
</body>

</html>