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

$picklist = $db->query('SELECT
a.id,
a.in_id,
a.out_id,
a.item_code,
a.document_no,
a.item_code,
a.batch_no,
a.bin_loc,
a.qty_pcs,
a.expiry,
c.material_description
FROM tb_picklist a
INNER JOIN tb_items c on c.item_code=a.item_code
INNER JOIN tb_outbound b
ON b.document_no = a.document_no
WHERE a.document_no=? and b.status !=?', $_GET['document_no'], 'For Validating')->fetch_all();
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
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Check List Details</h4>
                <div class="col-lg-2 text-right">
                  <div class="control-group">
                    <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Document #:</label>&nbsp<?php echo $_GET['document_no']; ?>
                  </div>

                </div>
              </div>

              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th>Details></th>
                        <th>Scan To Check</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($picklist as $arr_key => $arr_det) { ?>
                        <tr>

                          <td style="font-size:10px!important"><?php
                                                                echo "<b>Item Code:</b>" . "  " . "  " . $arr_det['item_code'] . "</br>";
                                                                echo "<b>Item Name:</b>" . " " . " " . $arr_det['material_description'] . "</br>";
                                                                echo "<b>Batch Code:</b>" . "  " . "  " . $arr_det['batch_no'] . "</br>";
                                                                echo "<b>Qty:</b>" . "  " . "  " . $arr_det['qty_pcs'] . "</br>";
                                                                ?></td>

                          <td class="align-middle text-center ">

                            <a target="" data-toggle="modal" data-target="#update_details<?php echo $arr_det['id']; ?>" href="" class="btn btn-outline-primary btn-sm"><i class="fas fa-barcode"></i></a>
                          </td>
                          <!-- MODAL PICKING-->
                          <div id="update_details<?php echo $arr_det['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Please Scan Barcode</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="scan_to_check" method="post">
                                    <div class="form-group">


                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_det['id']; ?>">
                                      </div>



                                      <!-- IN ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="in_id" id="in_id" value="<?php echo $arr_det['in_id']; ?>">
                                      </div>
                                      <!-- OUT ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="out_id" id="out_id" value="<?php echo $arr_det['out_id']; ?>">
                                      </div>

                                      <!-- ITEM CODE-->
                                      <div class="mt-1">
                                        <input type="hidden" name="doc" id="doc" value="<?php echo $arr_det['document_no']; ?>">
                                      </div>

                                      <!-- ITEM CODE-->
                                      <div class="mt-1">
                                        <input type="hidden" name="i_code" id="i_code" value="<?php echo $arr_det['item_code']; ?>">
                                      </div>

                                      <!-- ITEM CODE-->
                                      <div class="mt-1">
                                        <input type="hidden" name="i_name" id="i_name" value="<?php echo $arr_det['material_description']; ?>">
                                      </div>


                                      <!-- ITEM CODE-->
                                      <div class="mt-1">
                                        <input type="hidden" name="loc" id="loc" value="<?php echo $arr_det['bin_loc']; ?>">
                                      </div>


                                      <!-- BATCH CODE-->
                                      <div class="mt-1">
                                        <input type="hidden" name="b_code" id="b_code" value="<?php echo $arr_det['batch_no']; ?>">
                                      </div>
                                      <!-- BATCH CODE-->
                                      <div class="mt-1">
                                        <input type="hidden" name="exp" id="exp" value="<?php echo $arr_det['expiry']; ?>">
                                      </div>
                                      <!-- BATCH CODE-->
                                      <!-- <div class="mt-1">
                                        <input type="hidden" name="lpn" id="lpn" value="<?php echo $arr_det['lpn']; ?>">
                                      </div> -->


                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Required QTY</label>
                                        <input type="number" class="form-control" name="qty_pcs" id="qty_pcs" value="<?php echo $arr_det['qty_pcs']; ?>">
                                      </div>

                                      <!-- QTY-->
                                      <div class="mt-1">
                                        <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Scan Barcode</label>
                                        <input type="text" class="form-control" name="brcd_lpn" id="brcd_lpn" placeholder="Scan LPN / Item Code / Batch Code">
                                      </div>


                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
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