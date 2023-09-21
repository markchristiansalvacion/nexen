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
                      <div class="col-lg-12">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom02">SKU <span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter SKU Code" name="item_code" id="item_code" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="item_name">Item Name <span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Item Name" name="item_name" id="item_name" required />
                            </div>
                        </div>
                          <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom05">Item Category
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="item_category" id="item_cat" class="form-control" required>
                              <option value="">Select Category</option>
                              <option value="passenger">Passenger</option>
                              <option value="suv">SUV</option>
                              <option value="light_truck">Light Truck</option>
                              <option value="high_performance">High Performance</option>
                            </select>
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="unit">Unit
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="unit" id="unit" class="form-control" required>
                              <option value="">Select Unit</option>
                              <option value="pcs">Pc</option>
                              <option value="case">Case</option>
                            </select>
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="manufacturer">Manufacturer <span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Manufacturer" name="manufacturer" id="manufacturer" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="dot">DOT<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="DOT eg. 2023" name="dot" id="dot" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="tire_brand">Tire Brand<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Tire Brand" name="tire_brand" id="tire_brand" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="tire_size">Tire Size<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Tire Size eg. 305/45 R22" name="tire_size" id="tire_size" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="tire_design">Tire Design<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Tire Design" name="tire_design" id="tire_design" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="rim_size">Rim Size<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="number" min="1" step=".1" class="form-control" placeholder="Enter Rim Size" name="rim_size" id="rim_size" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="load_index">Load Index<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="number" min="1" step=".1" class="form-control" placeholder="Enter Load Index" name="load_index" id="load_index" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="speed_rating">Speed Rating<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Speed Rating" name="speed_rating" id="speed_rating" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="ply_rating">Ply Rating<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="number" min="1" step=".1" class="form-control" placeholder="Enter Ply Rating" name="ply_rating" id="ply_rating" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="origin">Origin<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control" placeholder="Enter Origin" name="origin" id="origin" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="regulation">Regulation
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="regulation" id="regulation" class="form-control" required>
                              <option value="">Regulation Classification</option>
                              <option value="regulated">Regulated</option>
                              <option value="non-regulated">Non-Regulated</option>
                            </select>
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="cif">CIF<span class="text-danger">*</span>
                          </label>
                            <div class="col-lg-6">
                              <input type="number" min="1" step=".1" class="form-control" placeholder="Enter CIF" name="cif" id="cif" required />
                            </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="type">Type
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="type" id="type" class="form-control" required>
                              <option value="">Select Type</option>
                              <option value="regulated">Tire</option>
                            </select>
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