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


$db_inbound = $db->query('SELECT id as in_id ,ref_no,document_no, 
                                item_code,batch_no,qty_pcs,expiry,
                                status,lpn,date_created
                                FROM tb_inbound WHERE ref_no= ? AND document_no=? AND status = ?', $_GET['ref_no'], $_GET['document_no'], '0')->fetch_all();

//print_r_html($db_inbound);

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
        <!-- row -->

        <div class="row">




          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Inbound Fullfillment</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>
                        <th class="align-middle  text-center  font-weight-bold ">Fullfillment Status</th>
                        <th class="align-middle  text-center  font-weight-bold ">Item Code</th>
                        <th class="align-middle  text-center  font-weight-bold ">Batch Code</th>
                        <th class="align-middle  text-center  font-weight-bold ">Qty</th>
                        <th class="align-middle  text-center  font-weight-bold ">LPN</th>
                        <th class="align-middle  text-center  font-weight-bold ">Fulfillment</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_inbound as $asar_key => $asar_val) { ?>



                        <?php if ($asar_val['status'] == 0) { ?>
                          <td class="align-middle text-center"><?php echo "Pending Fulfillment"; ?></td>
                        <?php } ?>
                        <?php if ($asar_val['status'] == 1) { ?>
                          <td class="align-middle text-center"><?php echo "Goods Receipt"; ?></td>
                        <?php } ?>
                        <?php if ($asar_val['status'] == 2) { ?>
                          <td class="align-middle text-center"><?php echo "Received With Issue"; ?></td>
                        <?php } ?>
                        <?php if ($asar_val['status'] == 3) { ?>
                          <td class="align-middle text-center"><?php echo "On Quarantine"; ?></td>
                        <?php } ?>





                        <td class="align-middle text-center"><?php echo $asar_val['item_code']; ?></td>
                        <td class="align-middle text-center"><?php echo $asar_val['batch_no']; ?></td>
                        <td class="align-middle text-center"><?php echo $asar_val['qty_pcs']; ?></td>
                        <td class="align-middle text-center"><?php echo $asar_val['lpn']; ?></td>
                        <td class="align-middle text-center">
                          <a target="" data-toggle="modal" data-target="#fullfill<?php echo $asar_val['in_id']; ?>" href="" class="btn btn-outline-primary btn-md " title="Picking Start"><i class="fas fa-check"></i></a>
                        </td>
                        <!-- MODAL FOR GOODS RECEIPT STATUS-->
                        <div id="fullfill<?php echo $asar_val['in_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 id="exampleModalLabel" class="modal-title">WMS FULFILLMENT</h4>

                              </div>
                              <div class="modal-body">
                                <form action="inbound_fullfillment_proc" method="post">

                                  <div class="form-group">

                                    <!-- ID-->
                                    <div class="mt-1">
                                      <input type="hidden" class="form-control" name="ref_no" id="ref_no" value="<?php echo $asar_val['ref_no']; ?>">
                                    </div>

                                    <!-- URL-->
                                    <div class="mt-1">
                                      <input type="hidden" name="url" id="url" value="<?php echo "inbound_fullfillment_add?ref_no={$_GET['ref_no']}&document_no={$_GET['document_no']}"; ?>">
                                    </div>

                                    <!-- ASN REF NO-->
                                    <div class="mt-1">
                                      <input type="hidden" class="form-control" name="in_id" id="in_id" value="<?php echo $asar_val['in_id']; ?>">
                                    </div>

                                    <!-- document No-->
                                    <div class="mt-1">
                                      <input type="hidden" name="doc" id="doc" value="<?php echo $asar_val['document_no']; ?>">
                                    </div>
                                    <!-- ITEM CODE-->
                                    <div class="mt-1">
                                      <input type="hidden" class="form-control" name="item_code" id="item_code" value="<?php echo $asar_val['item_code']; ?>">
                                    </div>
                                    <!-- BATCH CODE-->
                                    <div class="mt-1">
                                      <input type="hidden" class="form-control" name="b_code" id="b_code" value="<?php echo $asar_val['batch_no']; ?>">
                                    </div>


                                    <!-- STATUS -->
                                    <div class="mt-2">
                                      <label for="time" class="form-control-label text-uppercase  text-primary font-weight-bold">How shall we receive the goods?</label>
                                      <div class="mt-1">
                                        <select class="form-control" name="status">
                                          <option value="">Select</option>
                                          <option value="1">Receive Goods in Complete and Good Condition</option>
                                          <option value="2">Received w/ Issue</option>
                                          <option value="3">On Quarantine</option>
                                        </select>
                                      </div>
                                    </div>
                                    <!-- REQUIRD QTY-->
                                    <div class="mt-1">
                                      <label for="time" class="form-control-label text-uppercase  text-primary font-weight-bold">Please Confirm QTY </label>
                                      <input type="number" class="form-control" name=" required_qty_pcs" id="required_qty_pcs" value="<?php echo $asar_val['qty_pcs']; ?>">
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">CHECK AGAIN</button>
                                    <button type="submit" class="btn btn-primary">CONFIRM</button>
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
      $('#view_fullfillment_table').DataTable({
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