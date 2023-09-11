<?php
require_once "includes/load.php";

/**
 * Check each script if login is authenticated or if session is already expired
 */

// either new or old, it should live at most for another hour

if (is_login_auth()) {
  /** SESSION BASE TO TIME TODAY */

  if (is_session_expired()) {
    $_SESSION["msg"] = "<b>SESSION EXPIRED:</b> Please Login Again.";
    $_SESSION["msg_type"] = "danger";

    unset($_SESSION["user_id"]);
    unset($_SESSION["name"]);
    unset($_SESSION["user_type"]);
    unset($_SESSION["user_status"]);

    unset($_SESSION["login_time"]);

    /**TIME TO DAY + 315360000 THAT EQUIVALENT TO 10 YEARS*/

    redirect("login", false);
  }
} else {
  redirect("login", false);
}
?>

<?php include "views/header.php"; ?>
<?php include "views/nav_header.php"; ?>
<?php include "views/top_bar.php"; ?>


<?php
$picklist_array = [];
$get_data = $db
  ->query(
    'SELECT
a.id AS out_db_id,
a.ref_no,
a.transaction_type,
a.item_code,
a.document_no,
a.truck_allocation,
a.qty_pcs as required_qty,
b.category_code,
b.material_description
FROM tb_outbound a 
INNER JOIN tb_items b  ON b.item_code=a.item_code
WHERE a.document_no=?  AND a.qty_pcs <> 0  GROUP BY a.item_code',
    $_GET["document_no"]
  )
  ->fetch_all(); //print_r_html($get_data); // /print_r_html($get_data);
foreach ($get_data as $arr_key => $arr_val) {
  $aux_item_id = $arr_val["out_db_id"];
  $aux_item_ref = $arr_val["ref_no"];
  $aux_item_code = $arr_val["item_code"];
  $aux_required_qty = $arr_val["required_qty"];
  $aux_truck_allocation = $arr_val["truck_allocation"];
  $aux_document_no = $arr_val["document_no"];
  $item_category = $arr_val["category_code"];
  $item_desc = $arr_val["material_description"];
  $get_items_from_inbound = $db
    ->query(
      "SELECT item_code FROM tb_inbound WHERE item_code=?",
      $aux_item_code
    )
    ->fetch_all(); //print_r_html($get_items_from_inbound);
  if (empty($get_items_from_inbound)) {
    $picklist_array[$aux_item_id][$aux_item_id]["id"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["batch_no"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["available_qty"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["bin_location"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["EXP"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["aging"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["item_code"] = $aux_item_code;
    $picklist_array[$aux_item_id][$aux_item_id]["out_req_qty"] =
      "Not in inbound records.";
    $picklist_array[$aux_item_id][$aux_item_id]["out_document_no"] =
      "Not in inbound records."; //print_r_html($picklist_array); // echo "Not in inbound records.";
  } else {
    /***************/ /****FOR FG*****/ /***************/ if (
      $item_category == "PERSONAL CARE" ||
      $item_category == "MENS CARE" ||
      $item_category == "FRAGRANCE" ||
      $item_category == "HOME CARE" ||
      $item_category == "BABY CARE" ||
      $item_category == "HEALTH CARE"
    ) {
      /*
             * FEFO QUERY and  QTY Query and PICKFACE
             * GET FIRST THE QTY_PCS FROM INBOUND THAT IS LOWER THAN THE REQUIRED QTY
             * IF NO RESULT
             * GET THE LARGER QTY BUT THE SMALLEST QTY OF ALL THE LARGEST
             */
      $db_inbound = $db
        ->query(
          'SELECT
                                    tb_inbound.id ,
                                    tb_inbound.item_code,tb_inbound.batch_no,tb_inbound.qty_pcs AS available_qty,
                                    TIMESTAMPDIFF(DAY,now(),tb_inbound.expiry) AS AGING,
                                    tb_inbound.expiry as EXP,
                                    tb_inbound.date_created,
                                     tb_inbound.lpn,
                                    tb_inbound.bin_location
                                    FROM tb_inbound
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code =  tb_inbound.bin_location
                                    WHERE   tb_bin_location_bac.location_type=?
                                    AND tb_inbound.item_code=?
                                    
                                     AND tb_inbound.expiry <> 0000-00-00
                                    AND tb_inbound.qty_pcs <> 0 ',
          "Pickface",
          $aux_item_code
        )
        ->fetch_all(); //print_r_html($db_inbound);
      if (!empty($db_inbound)) {
        $get_less_than_6_months = $db
          ->query(
            'SELECT
                                    a.id,
                                    a.batch_no,
                                    a.qty_pcs as available_qty,
                                    a.bin_location,
                                    a.lpn,
                                    a.expiry as EXP,
                                    TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                    FROM tb_inbound a
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                    WHERE tb_bin_location_bac.location_type = ?
                                    AND a.item_code = ?
                                    AND a.qty_pcs <> 0
                                    AND a.expiry <> 0000-00-00
                                    AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) < 180
                                    AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1',
            "Pickface",
            $aux_item_code
          )

          ->fetch_all(); //print_r_html($get_less_than_6_months);
        if (!empty($get_less_than_6_months)) {
          foreach ($get_less_than_6_months
            as $in_arr_key => $in_arr_val) {
            if (array_key_exists($aux_item_code, $picklist_array)) {
              $picklist_array[$aux_item_id][$in_arr_val["id"]] = $in_arr_val;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_db_id"] = $aux_item_id;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["item_code"] = $aux_item_code;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_req_qty"] = $aux_required_qty;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_document_no"] = $aux_document_no;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["description"] = $item_desc;
            } else {
              $picklist_array[$aux_item_id][$in_arr_val["id"]] = $in_arr_val;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_db_id"] = $aux_item_id;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["item_code"] = $aux_item_code;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_req_qty"] = $aux_required_qty;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_document_no"] = $aux_document_no;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["description"] = $item_desc;
            }
          } //print_r_html($picklist_array);
        } else {
          // echo "empty array";
          // echo $aux_item_code;
          $get_greater_than_6_months = $db
            ->query(
              'SELECT
                                    a.id,
                                    a.batch_no,
                                    a.qty_pcs as available_qty,
                                    a.bin_location,
                                    a.lpn,
                                    a.expiry as EXP,
                                    TIMESTAMPDIFF(DAY,NOW(),a.expiry) AS Ageing
                                    FROM tb_inbound a
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                    WHERE tb_bin_location_bac.location_type = ?
                                    AND a.item_code = ?
                                    AND a.qty_pcs <> 0
                                    AND a.expiry <> 0000-00-00
                                    AND TIMESTAMPDIFF(DAY,NOW(),a.expiry) >=180
                                    AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1',
              "Pickface",
              $aux_item_code
            )
            ->fetch_all(); //print_r_html($get_greater_than_6_months);
          if (!empty($get_greater_than_6_months)) {
            foreach ($get_greater_than_6_months
              as $in_arr_key => $in_arr_val) {
              if (
                array_key_exists(
                  $aux_item_code,
                  $picklist_array
                )
              ) {
                $picklist_array[$aux_item_id][$in_arr_val["id"]] = $in_arr_val;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_db_id"] = $aux_item_id;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["item_code"] = $aux_item_code;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_req_qty"] = $aux_required_qty;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_document_no"] = $aux_document_no;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["description"] = $item_desc;
              } else {
                $picklist_array[$aux_item_id][$in_arr_val["id"]] = $in_arr_val;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_db_id"] = $aux_item_id;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["item_code"] = $aux_item_code;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_req_qty"] = $aux_required_qty;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_document_no"] = $aux_document_no;
                $picklist_array[$aux_item_id][$in_arr_val["id"]]["description"] = $item_desc;
              }
            }
            // print_r_html($picklist_array);
          }
        }
      }
    } else {
      /****NON FG */ $db_inbound = $db
        ->query(
          'SELECT
                                    tb_inbound.id,
                                   
                                     tb_inbound.batch_no,
                                    tb_inbound.qty_pcs AS available_qty,
                                     tb_inbound.lpn,
                                    tb_inbound.expiry as EXP,
                                    tb_inbound.date_created,
                                    tb_inbound.bin_location
                                    FROM tb_inbound
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code =  tb_inbound.bin_location
                                    WHERE   tb_bin_location_bac.location_type=?
                                    AND tb_inbound.item_code=?
                                  
                                    AND tb_inbound.qty_pcs <> 0 ',
          "Pickface",
          $aux_item_code
        )
        ->fetch_all();
      if (!empty($db_inbound)) {
        $non_fg = $db
          ->query(
            'SELECT
                                    a.id,
                                    a.batch_no,
                                    a.qty_pcs as available_qty,
                                    a.bin_location,
                                     a.lpn,
                                    a.expiry as EXP,a.date_created,
                                    TIMESTAMPDIFF(DAY,NOW(),a.date_created) AS Ageing
                                    FROM tb_inbound a
                                    INNER JOIN tb_bin_location_bac ON tb_bin_location_bac.location_code = a.bin_location
                                    WHERE tb_bin_location_bac.location_type = ?
                                    AND a.item_code = ?
                                    AND a.qty_pcs <> 0
                                    AND a.qty_pcs = (SELECT MIN(a.qty_pcs) FROM tb_inbound) ORDER BY a.qty_pcs ASC LIMIT 1',
            "Pickface",
            $aux_item_code
          )
          ->fetch_all();
        if (!empty($non_fg)) {
          foreach ($non_fg as $in_arr_key => $in_arr_val) {
            if (array_key_exists($aux_item_code, $picklist_array)) {
              $picklist_array[$aux_item_id][$in_arr_val["id"]] = $in_arr_val;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_db_id"] = $aux_item_id;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["item_code"] = $aux_item_code;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_req_qty"] = $aux_required_qty;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_document_no"] = $aux_document_no;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["description"] = $item_desc;
            } else {
              $picklist_array[$aux_item_id][$in_arr_val["id"]] = $in_arr_val;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_db_id"] = $aux_item_id;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["item_code"] = $aux_item_code;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_req_qty"] = $aux_required_qty;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["out_document_no"] = $aux_document_no;
              $picklist_array[$aux_item_id][$in_arr_val["id"]]["description"] = $item_desc;
            }
          }
        }
      }
    }
  }
}
?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <?php if (isset($_SESSION["msg"])) { ?>
      <script>
        swal({

          title: "<?php echo $_SESSION["msg_heading"]; ?>",
          text: "<?php echo $_SESSION["msg"]; ?>",
          icon: "<?php echo $_SESSION["msg_type"]; ?>",
          button: "Close",

        });
      </script>

    <?php
      unset($_SESSION["msg"]);
      unset($_SESSION["msg_type"]);
      unset($_SESSION["msg_heading"]);
    } ?>
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
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Picklist Details</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th>Picklist Details</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($picklist_array
                        as $pick_arr_key => $pick_arr_val) {
                        foreach ($pick_arr_val
                          as $pick_arr_val_key => $pick_arr_det) { ?>
                          <tr>
                            <td style="font-size: 10px!important;"><?php
                                                                    echo "<b>Item Code:</b>" . "  " . "  " .  "  " . "  " . $pick_arr_det["item_code"] . "</br>";
                                                                    echo "<b>Batch No.:</b>" . "  " . "  " .  "  " . "  " . $pick_arr_det["batch_no"] . "</br>";
                                                                    echo "<b>Location:</b>" . "  " . "  " .  "  " . "  " . $pick_arr_det["bin_location"] . "</br>";
                                                                    echo "<b>Required QTY:</b>" . "  " . "  " .  "  " . "  " . $pick_arr_det["out_req_qty"] . "</br>";
                                                                    echo "<b>Available QTY:</b>" . "  " . "  " .  "  " . "  " . $pick_arr_det["available_qty"] . "</br>";
                                                                    echo "<b>Expiration:</b>" . "  " . "  " .  "  " . "  " . $pick_arr_det["EXP"] . "</br>";
                                                                    ?></td>
                            <td class=" align-middle text-center"> <a target="" data-toggle="modal" data-target="#status<?php echo $pick_arr_det["id"]; ?>" href="" class="btn btn-outline-primary btn-md" title="Picking Start">Scan</a> </td>
                            <!-- MODAL FOR PICKING START-->
                            <div id="status<?php echo $pick_arr_det["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                              <div role="document" class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 id="exampleModalLabel" class="modal-title">Hey There!</h4>

                                  </div>
                                  <div class="modal-body">
                                    <form method="post" action="allocate_proc">
                                      <div class="form-group">

                                        <!-- URL-->
                                        <div class="mt-1">
                                          <input type="hidden" name="url" id="url" value="<?php echo "picklist?document_no={$_GET["document_no"]}"; ?>">
                                        </div>
                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="db_id" id="db_id" value="<?php echo $pick_arr_det["id"]; ?>">
                                        </div>

                                        <!-- IN ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="in_id" id="in_id" value="<?php echo $pick_arr_det["id"]; ?>">
                                        </div>
                                        <!-- OUT ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="out_id" id="out_id" value="<?php echo $pick_arr_det["out_db_id"]; ?>">
                                        </div>

                                        <!-- ITEM CODE-->
                                        <div class="mt-1">
                                          <input type="hidden" name="document_no" id="document_no" value="<?php echo $pick_arr_det["out_document_no"]; ?>">
                                        </div>

                                        <!-- ITEM CODE-->
                                        <div class="mt-1">
                                          <input type="hidden" name="item_code" id="item_code" value="<?php echo $pick_arr_det["item_code"]; ?>">
                                        </div>

                                        <!-- ITEM NAME-->
                                        <div class="mt-1">
                                          <input type="hidden" name="i_desc" id="i_desc" value="<?php echo $pick_arr_det["description"]; ?>">
                                        </div>

                                        <!-- BATCH CODE-->
                                        <div class="mt-1">
                                          <input type="hidden" name="batch_no" id="batch_no" value="<?php echo $pick_arr_det["batch_no"]; ?>">
                                        </div>

                                        <!-- BIN LOC-->
                                        <div class="mt-1">
                                          <input type="hidden" name="loc" id="loc" value="<?php echo $pick_arr_det["bin_location"]; ?>">
                                        </div>

                                        <!-- OUT QTY-->
                                        <div class="mt-1">
                                          <input type="hidden" name="required_qty_pcs" id="required_qty_pcs" value="<?php echo $pick_arr_det["out_req_qty"]; ?>">
                                        </div>

                                        <!-- IN QTY-->
                                        <div class="mt-1">
                                          <input type="hidden" name="qty_pcs" id="qty_pcs" value="<?php echo $pick_arr_det["available_qty"]; ?>">
                                        </div>
                                        <!-- BIN LOC-->
                                        <div class="mt-1">
                                          <input type="hidden" name="exp" id="exp" value="<?php echo $pick_arr_det["EXP"]; ?>">
                                        </div>

                                        <!-- ID-->
                                        <div class="mt-1">
                                          <input type="hidden" name="lpn" id="lpn" value="<?php echo $pick_arr_det["lpn"]; ?>">
                                        </div>





                                        <!-- QTY-->
                                        <div class="mt-1">
                                          <label for="expiry" class="form-control-label text-uppercase text-primary font-weight-bold">Scan Barcode</label>
                                          <input type="text" class="form-control" name="brcd_lpn" placeholder="Scan Barcode" autofocus>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">No Thanks</button>
                                        <button type="submit" class="btn btn-primary">Yes, i want</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-- End Modal -->



                          </tr>

                          <!--                 --><?php }
                                                //                        }
                                              } ?>
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