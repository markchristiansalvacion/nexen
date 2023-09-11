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

//print_r_html(strtotime("2021-11-15"));

$db_replen = $db->query('SELECT * FROM tb_replenishment')->fetch_all();
//print_r_html($picklist);

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
                <h4 class="card-title">Replenishment</h4>

              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>


                        <th class="align-middle text-center">Item Code</th>
                        <th class="align-middle text-center">Source Location</th>
                        <th class="align-middle text-center">New Location</th>




                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_replen as $arr_key => $arr_det) { ?>
                        <tr>

                          <td class="align-middle text-center"><?php echo $arr_det['item_code']; ?></td>


                          <td class="align-middle text-center ">
                            <a target="" data-toggle="modal" data-target="#update_details<?php echo $arr_det['id']; ?>" href="" class="btn btn-outline-primary btn-lg "><i class="fas fa-barcode"></i></a>
                          </td>



                          <?php if ($arr_det['scan_stats'] != 'Yes') : ?>
                            <td class="align-middle text-center ">

                              <a target="" data-toggle="modal" data-target="#scan_loc<?php echo $arr_det['id']; ?>" href="" class="btn btn-outline-warning btn-lg disabled"><i class="fas fa-barcode"></i></a>
                            </td>
                          <?php else : ?>
                            <td class="align-middle text-center ">

                              <a target="" data-toggle="modal" data-target="#scan_loc<?php echo $arr_det['id']; ?>" href="" class="btn btn-outline-warning btn-lg"><i class="fas fa-barcode"></i></a>
                            </td>
                          <?php endif; ?>
                          <!-- MODAL PICKING-->
                          <div id="update_details<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Please Scan Barcode</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="scan_location_proc" method="post">
                                    <div class="form-group">


                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>



                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Scan Barcode</label>
                                        <input type="text" class="form-control" name="brcd" placeholder="Scan LPN / Item Code / Batch Code" autofocus>
                                      </div>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-secondary">Cance</button>
                                      <button type="submit" class="btn btn-primary">Scan</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- End Modal -->






                          <!-- MODAL FOR LOCATION-->
                          <div id="scan_loc<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Please Scan Barcode</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="scan_new_location_proc" method="post">
                                    <div class="form-group">


                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>





                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Scan Barcode</label>
                                        <input type="text" class="form-control" name="brcd" id="brcd" value="" placeholder="Enter/Scan Barcode">
                                      </div>


                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-secondary">Cance</button>
                                      <button type="submit" class="btn btn-primary">Scan</button>
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



</body>

</html>