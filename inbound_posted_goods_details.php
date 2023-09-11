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
// $enddate = date("Y-m-d", $offset)

$db_asn = $db->query('SELECT 
   a.id AS recID,
   a.ref_no,
   a.document_no,
   a.item_code,
   a.batch_no,
   a.qty_pcs,
   a.expiry,
   a.lpn,
   a.status,
   a.bin_location,
   tb_items.material_description
       
   FROM tb_inbound a
   INNER JOIN tb_items ON tb_items.item_code = a.item_code
    WHERE  a.document_no = ? AND a.status =? ORDER BY batch_no ASC', $_GET['document_no'], '1')->fetch_all();

//print_r_html($db_asn);
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
                <h4 class="card-title">Inbound Posted Goods</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>


                        <th class="align-middle text-center  font-weight-bold ">Item Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Batch No</th>
                        <th class="align-middle text-center  font-weight-bold ">Item Name</th>
                        <th class="align-middle text-center  font-weight-bold ">Qty (PCS)</th>
                        <th class="align-middle text-center  font-weight-bold ">Expiry</th>

                        <th class="align-middle text-center  font-weight-bold ">LPN</th>

                        <th class="align-middle text-center  font-weight-bold ">Edit</th>

                      </tr>
                    </thead>
                    <?php foreach ($db_asn as $arr_key => $arr_val) { ?>
                      <tr>


                        <td class="align-middle text-center"><?php echo $arr_val['item_code']; ?></td>
                        <td class="align-middle text-center"><?php echo $arr_val['batch_no']; ?></td>
                        <td class="align-middle text-center"><?php echo $arr_val['material_description']; ?></td>


                        <td class="align-middle text-center"><?php echo $arr_val['qty_pcs']; ?></td>

                        <td class="align-middle text-center "><?php echo $arr_val['expiry']; ?></td>

                        <td class="align-middle text-center "><?php echo $arr_val['lpn']; ?></td>

                        <td class="align-middle text-center ">
                          <a target="" data-toggle="modal" data-target="#update_inbound<?php echo $arr_val['recID']; ?>" href="" class="btn btn-outline-warning btn-md" title="Update ATA"><i class="fas fa-pen-alt"></i></a>
                        </td>
                        <div id="update_inbound<?php echo $arr_val['recID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 id="exampleModalLabel" class="modal-title">Update Posted Goods</h4>

                              </div>
                              <div class="modal-body">
                                <form action="update_inbound_details" method="post">
                                  <div class="form-group">


                                    <div class="mt-1">
                                      <input type="hidden" name="url" id="url" value="<?php echo "inbound_posted_goods_details?document_no={$arr_val['document_no']}&ref_no={$arr_val['ref_no']}"; ?>">
                                    </div>


                                    <div class="mt-1">
                                      <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['recID']; ?>">
                                    </div>


                                    <div class="mt-1">
                                      <input type="hidden" name="ref_no" id="ref_no" value="<?php echo $arr_val['ref_no']; ?>">
                                    </div>


                                    <div class="mt-1">
                                      <input type="hidden" name="document_no" id="document_no" value="<?php echo $arr_val['document_no']; ?>">
                                    </div>

                                    <div class="mt-2">
                                      <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">BATCH NO</label>
                                      <input type="text" name="batch" id="batch" placeholder="Enter qty pcs" class="form-control" value="<?php echo $arr_val['batch_no']; ?>">
                                    </div>


                                    <div class="mt-2">
                                      <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">QTY PCS</label>
                                      <input type="number" name="qty_pcs" id="qty_pcs" placeholder="Enter qty pcs" class="form-control" value="<?php echo $arr_val['qty_pcs']; ?>">
                                    </div>


                                    <div class="mt-2">
                                      <label for="mfg" class="form-control-label text-uppercase text-primary font-weight-bold">EXPIRY</label>
                                      <input type="date" name="expiry" id="expiry" placeholder="Enter pack size" class="form-control" value="<?php echo $arr_val['expiry']; ?>">
                                    </div>


                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
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