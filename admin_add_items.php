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
$all_items = $db->query('SELECT * FROM tb_items')->fetch_all();
$all_unit = $db->query('SELECT * FROM tb_units')->fetch_all();
$all_sloc = $db->query('SELECT * FROM tb_sloc')->fetch_all();

$all_category = $db->query('SELECT * FROM tb_category')->fetch_all();

//print_r_html($all_unit);
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
                  <form action="admin_add_items_proc" method="post" class="needs-validation" novalidate>
                    <div class="row">
                      <div class="col-xl-6">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom05">Item Category
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="item_category" id="item_cat" class="form-control" required>
                              <option value="">Item Category</option>
                              <?php foreach ($all_category as $cat_arr_id => $cat_arr_val) { ?>
                                <option value="<?php echo $cat_arr_val['category_description']; ?>"><?php echo $cat_arr_val['category_code'] . " - " . $cat_arr_val['category_description']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom02">Item Code <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">

                            <input type="text" class="form-control" placeholder="Enter Item Code" name="item_code" id="item_code" required />

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Item Description
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Enter Item Name" required>

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Pack Size
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="p_size" id="p_size" placeholder="Enter Pack Size" required>

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Case Per Tier
                            
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="c_tier" id="c_tier" placeholder="Enter Case Per Tier" >

                          </div>
                        </div>
                      </div>
                      <div class="col-xl-6">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Weight Per Box
                           
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="w_box" id="w_box" placeholder="Enter Weight Per Box" >

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom05">CBM Per Box
                            
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" placeholder="Enter CBM Per Box" name="cbm" id="cbm"  />


                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">CASE Per Pallet
                            >
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="c_pallet" id="c_pallet" placeholder="Enter CASE Per Pallet" >

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">PCS Per Pallet
                            
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="p_pallet" id="p_pallet" placeholder="Enter PCS Per Pallet" >

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Stocking High
                        
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="layer" id="layer" placeholder="Enter Stocking High" >

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Shelf Life
                            
                          </label>
                          <div class="col-lg-6">
                            <input type="number" class="form-control" name="s_life" id="s_life" placeholder="Enter Shelf Life" >

                          </div>
                        </div>


                        <div class="mb-3 row">
                          <div class="col-lg-8 ms-auto">
                            <button type="submit" class="btn btn-primary">Confirm Transaction</button>
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
</body>

</html>