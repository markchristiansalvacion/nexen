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
$all_destination = $db->query('SELECT * FROM tb_destination')->fetch_all();
$all_source = $db->query('SELECT * FROM tb_source')->fetch_all();

$all_item = $db->query('SELECT * FROM tb_items')->fetch_all();
$db_inbound = $db->query('SELECT * FROM tb_inbound WHERE status = ? GROUP BY document_no', "2")->fetch_all();
foreach ($db_inbound as $key_val => $arr_val) {
  $ref_no = $arr_val['ref_no'];
}
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

    <div class="content-body">
      <div class="container-fluid">
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Inbound Incident Report</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form class="needs-validation" novalidate method="POST" action="inbound_ir_proc">
                    <input type="hidden" id="ref_no" name="ref_no" value="<?php echo $_GET['ref_no'] ?>">
                    <input type="hidden" id="doc_no" name="doc_no" value="<?php echo $_GET['document_no'] ?>">
                    <div class="row">
                      <div class="col-xl-6">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom05">Select/Enter Item Code
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input list="data_list" id="scan_field" placeholder="Enter Item Code" class="form-control medium" autofocus>
                            <datalist id="data_list">
                              <?php foreach ($all_item as $item_key => $arr_val) { ?>
                                <option value="<?php echo $arr_val['item_code']; ?>"><?php echo $arr_val['item_code'] . '-' . $arr_val['material_description']; ?></option>
                              <?php } ?>
                            </datalist>

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom02">Select Source Warehouse <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input list="source_list" name="source" id="source" class="form-control" placeholder="Enter/Select Source/Supplier Name or Code">
                            <datalist id="source_list">
                              <?php foreach ($all_source as $s_id => $s_det) { ?>
                                <option value="<?php echo $s_det['source_code']; ?>"><?php echo $s_det['source_name']; ?></option>
                              <?php } ?>
                            </datalist>
                          </div>
                        </div>


                      </div>
                      <div class="col-xl-6">

                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom05">Select Branch/Destination
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input list="branch" class="form-control" placeholder="Enter/SelectWarehouse/Supplier" name="destination" required />
                            <datalist id="branch">
                              <option value="">Select Destination/Branch</option>
                              <?php foreach ($all_destination as $asar_key => $asar_val) { ?>
                                <option value="<?php echo $asar_val['destination_code']; ?>"><?php echo $asar_val['destination_name']; ?></option>
                              <?php } ?>
                            </datalist>

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom02">Description <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">

                            <textarea class="form-control" placeholder="Please Select" name="desc" required></textarea>

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <div class="col-lg-8 ms-auto">
                            <a href="javascript:void(0);" class="add_item_button btn btn-primary">Add Item</a>
                            <button type="submit" class="btn btn-success">Confirm Transaction</button>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class='row'>
                      <div class="col-lg-12">
                        <div class="card">
                          <div class="card-header">
                            <h4 class="card-title">Item Details</h4>
                          </div>
                          <div class="card-body card-body1">
                            <div class="form-group">

                              <div class="table-responsive">
                                <div id="table-scroll">
                                  <table class="table table-bordered table-responsive-sm" id="view_asn_table">
                                    <thead>
                                      <tr>
                                        <th class="align-middle text-center ">Seq.</th>
                                        <th class="align-middle text-center ">Nature of Incident</th>
                                        <th class="align-middle text-center ">Item</th>
                                        <th class="align-middle text-center ">Batch#</th>
                                        <th class="align-middle text-center ">Qty (PCS)</th>
                                        <th class="align-middle text-center ">Remarks</th>
                                        <th class="align-middle text-center ">Action</th>
                                      </tr>
                                    </thead>
                                    <tbody class="item_info_field">
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--**********************************
        Main wrapper end
    ***********************************-->

  <!--**********************************
        Scripts
    ***********************************-->
  <!-- Required vendors -->
  <script src="./vendor/global/global.min.js"></script>
  <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
  <script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
  <script src="./js/custom.min.js"></script>
  <script src="./js/dlabnav-init.js"></script>
  <script src="./js/demo.js"></script>
  <script src="./js/styleSwitcher.js"></script>



  <script type="text/javascript">
    $(document).ready(function() {
      var maxField = 1000; //Input fields increment limitation
      var addButton = $('.add_item_button'); //Add button selector
      var wrapper = $('.item_info_field'); //Input field wrapper


      var x = 1; //Initial field counter is 1
      var form_id_count = 0;
      //Once add button is clicked
      $(addButton).click(function() {



        var scan_field = document.getElementById("scan_field");
        // var selectedText = scan_field.options[scan_field.selectedIndex].text;
        // var selectedValue = scan_field.options[scan_field.selectedIndex].value;
        var selectedValue = scan_field.value;

        //console.log(selectedText);
        //console.log(selectedValue);
        var form_id = "add-form-" + form_id_count;
        console.log(form_id);

        //Check maximum number of input fields

        if (x < maxField) {
          var fieldHTML = '<tr id="' + form_id + '"><td class="align-middle text-center px-3 py-2">' + x + '</td><td class="aling-middle text-center px-3 py-2"><select name="nature[]" class="form-control"><option value="">Select Nature of Incident</option><option value="expired">Expired Goods</option><option value="damaged_box">Damaged Box/Packaging</option><option value="spillage_leakage">Spillage/Leakage</option><option value="damaged_item">Damaged Item</option><option value="near_expiry">Near Expiry</option><option value="lacking_missing">Lacking/Missing</option><option value="wrong_batch">Wrong Batch</option><option value="tampered_seal">Tampered Seal</option><option value="label_issue">Product Label Issue</option><option value="others">Others (Please Indicate)</option></select></td><td class="align-middle text-center px-3 py-2"><select name="f_item_id[]" class="form-control "><option value="' + selectedValue + '">' + selectedValue + '</option></select></td> <td class="align-middle  text-center px-3 py-2"><input type="text" name="batch[]" placeholder="Enter batch no." class="form-control"></td> <td class="align-middle  text-center px-3 py-2"><input type="number" name="f_qty[]" placeholder="Enter Quantity" class="form-control"></td><td class="align-middle  text-center px-3 py-2"><textarea name="f_remarks[]" placeholder="Enter remarks" class="form-control"></textarea></td> <td class="align-middle  text-center px-3 py-2"><a href="javascript:void(0);" class="remove_item_button btn btn-danger " onclick=remove_item("' + form_id + '") title="Remove Item">-</a></td></tr>';
          x++; //Increment field counter
          form_id_count++;
          $(wrapper).append(fieldHTML); //Add field html

        }

      });
    });

    function remove_item($id, $seq) {
      document.getElementById($id).remove(); //Remove field html
    }
    /**
     This will remove the issues when an Account Coordinator Will Press Enter and the Form will submit even though the input is not yet Done
     */
    $(document).on("keydown", "form", function(event) {
      return event.key != "Enter";
    });
  </script>
</body>

</html>