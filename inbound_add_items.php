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


<?php $all_item = $db->query('SELECT * FROM tb_items')->fetch_all(); ?>
<?php $all_loc = $db->query('SELECT * FROM tb_bin_location_bac')->fetch_all(); ?>

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
                <h4 class="card-title">Add Items</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form class="needs-validation" novalidate action="inbound_add_item_proc" method="post">
                    <div class="row">
                      <div class="col-xl-6">
                        <div class="mb-3 row">
                          <label class="col-lg-3 col-form-label" for="validationCustom05">Select Item Code
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input list="data_list" id="scan_field" placeholder="Enter Item Code" class="form-control medium" autofocus>
                            <datalist id="data_list">
                              <?php foreach ($all_item as $item_key => $arr_val) { ?>
                                <option value="<?php echo $arr_val['item_code']; ?>"><?php echo $arr_val['item_code'] . " - " . $arr_val['material_description']; ?></option>
                              <?php } ?>
                            </datalist>

                          </div>
                        </div>
                        <!-- REF NO-->
                        <div class="mt-1">
                          <input type="hidden" name="asn_ref" id="asn_ref" value="<?php echo $_GET['asn_ref']; ?>">
                        </div>

                        <!-- DOC NO-->
                        <input type="hidden" name="source_doc" id="source_doc" value="<?php echo $_GET['source_doc']; ?>">
                        <div class="mt-1">
                        </div>
                      </div>
                      <div class="mb-3 row">
                        <div class="col-lg-12 ms-auto">
                          <a href="javascript:void(0);" class="add_item_button btn btn-primary">Add Item</a>
                          <button type="submit" class="btn btn-success">Confirm Transaction</button>
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
                                  <table class="table table-bordered table-responsive-sm" id="item_list_table">
                                    <thead>
                                      <tr>
                                        <th class="align-middle text-center px-3 py-2 font-weight-bold ">Seq.</th>
                                        <th class="align-middle text-center px-3 py-2 font-weight-bold ">Item</th>
                                        <th class="align-middle text-center px-3 py-2 font-weight-bold ">Batch#</th>
                                        <th class="align-middle text-center px-3 py-2 font-weight-bold ">Qty (PCS)</th>
                                        <th class="align-middle text-center px-3 py-2 font-weight-bold ">Remarks</th>
                                        <th class="align-middle text-center px-3 py-2 font-weight-bold ">Action</th>
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
                </div>
                </form>
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
  <!--  vendors -->
  <script src="./vendor/global/global.min.js"></script>
  <script src="./vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
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
          var fieldHTML = '<tr id="' + form_id + '"><td class="align-middle text-center px-3 py-2">' + x + '</td><td class="align-middle text-center px-3 py-2"><select name="f_item_id[]" class="form-control small"><option value="' + selectedValue + '">' + selectedValue + '</option></select></td> <td class="align-middle small text-center px-3 py-2"><input type="text" name="batch[]" placeholder="Enter batch no." class="form-control"></td> <td class="align-middle small text-center px-3 py-2"><input type="number" name="f_qty[]" placeholder="Enter Quantity" class="form-control"></td><td class="align-middle small text-center px-3 py-2"><textarea name="f_remarks[]" placeholder="Enter remarks" class="form-control"></textarea></td> <td class="align-middle  text-center px-3 py-2"><a href="javascript:void(0);" class="remove_item_button btn btn-danger" onclick=remove_item("' + form_id + '") title="Remove Item">-</a></td></tr>';
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