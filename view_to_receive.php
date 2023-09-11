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
<?php
// $date_today = date('Y-m-d');

//$all_pallets = $db->query('SELECT * FROM tb_pallets')->fetch_all();
//print_r_html($all_pallets);
// $startdate = date('Y-m-d');
// $offset = strtotime("+30 day");
// $enddate = date("Y-m-d", $offset);




$db_asn = $db->query('SELECT 
tb_inbound.id AS recID,
tb_inbound.ref_no,
   tb_inbound.document_no,
   tb_inbound.item_code,
   tb_inbound.batch_no,
   tb_inbound.qty_pcs,
   tb_inbound.expiry,
   tb_inbound.lpn,
       tb_inbound.status,
   tb_inbound.bin_location,
   tb_items.material_description
       
   FROM tb_inbound
   INNER JOIN tb_items ON tb_items.item_code = tb_inbound.item_code
    WHERE  tb_inbound.document_no = ? AND  tb_inbound.ref_no = ? AND tb_inbound.pg_status=? ORDER BY batch_no ASC,item_code', $_GET['document_no'], $_GET['ref_no'], '0')->fetch_all();


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

    <!--**********************************
            Content body start
        ***********************************-->
    <div class="content-body">
      <div class="container-fluid">
        <!-- row -->

        <div class="row">

          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h6>To Receive</h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>


                        <th class="font-weight-bold " style="font-size: 12px!important;">Details</th>

                        <th class="font-weight-bold " style="font-size: 12px!important;"></th>

                      </tr>
                    </thead>
                    <?php foreach ($db_asn as $arr_key => $arr_val) { ?>
                      <tr>


                        <td style="font-size: 10px!important;">
                          <?php echo "<b> Item Code:  </b>  &nbsp; &nbsp; &nbsp;"  . $arr_val['item_code'] . "<br/>";
                          echo "<b> Batch Code:  </b>  &nbsp; &nbsp;"  . $arr_val['batch_no'] . "<br/>";
                          echo "<b> Item Name:  </b>  &nbsp; &nbsp;"  . $arr_val['material_description'] . "<br/>";
                          echo "<b> Quantity:  </b>  &nbsp; &nbsp; &nbsp; &nbsp;"  . $arr_val['qty_pcs'] . "<br/>"; ?> </td>

                        <td class="align-middle text-center " style="font-size: 10px!important;">
                          <a target="" data-toggle="modal" data-target="#update_inbound<?php echo $arr_val['recID']; ?>" href="" class="btn btn-outline-warning btn-md" title="Scan Barcode"><i class="fas fa-barcode"></i></a>
                        </td>
                        <div id="update_inbound<?php echo $arr_val['recID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 id="exampleModalLabel" class="modal-title">Receive Item</h4>

                              </div>
                              <div class="modal-body">
                                <form action="scan_to_receive" method="post">
                                  <div class="form-group">

                                    <!-- URL-->
                                    <div class="mt-1">
                                      <input type="hidden" name="url" id="url" value="<?php echo "view_to_receive?document_no={$arr_val['document_no']}&ref_no={$arr_val['ref_no']}"; ?>">
                                    </div>


                                    <div class="mt-1">
                                      <input type="hidden" name="doc" id="doc" value="<?php echo $arr_val['document_no']; ?>">
                                    </div>
                                    <!-- ID -->
                                    <div class="mt-1">
                                      <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['recID']; ?>">
                                    </div>

                                    <!--ITEM CODE-->
                                    <div class="mt-1">
                                      <input type="hidden" name="i_code" id="i-code" value="<?php echo $arr_val['item_code']; ?>">
                                    </div>

                                    <!-- BATCH NO -->
                                    <div class="mt-1">
                                      <input type="hidden" name="b_code" id="b_code" value="<?php echo $arr_val['batch_no']; ?>">
                                    </div>

                                    <!-- EXPIRY-->
                                    <div class="mt-1">
                                      <input type="hidden" name="expiry" id="expiry" value="<?php echo $arr_val['expiry']; ?>">
                                    </div>

                                    <!-- REFO NO-->
                                    <div class="mt-1">
                                      <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                    </div>
                                    <div class="mt-2">
                                      <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">ACTUAL QTY PCS</label>
                                      <input type="number" name="qty_pcs" id="qty_pcs" placeholder="Enter qty pcs" class="form-control">
                                    </div>
                                    <div class="mt-2">
                                      <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Scan Barcode</label>
                                      <input type="text" name="s_item" id="s_item" placeholder="Enter/Scan Barcode" class="form-control" autofocus>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Confirm</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>


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
					Footer start
				***********************************-->

    <!--**********************************
					Footer end
				***********************************-->
  </div>
  </div>
  <!--**********************************
            Content body end
        ***********************************-->
  <!--**********************************
           Support ticket button start
        ***********************************-->

  <!--**********************************
           Support ticket button end
        ***********************************-->


  </div>
  <!--**********************************
        Main wrapper end
    ***********************************-->

  <!--**********************************
        Scripts
    ***********************************-->
  <!-- Required vendors -->
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
      $('#view_inbound_table').DataTable({
        order: [
          [5, "desc"]
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