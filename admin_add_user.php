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
$all_source = $db->query('SELECT * FROM tb_source')->fetch_all();
$all_destination = $db->query('SELECT * FROM tb_destination')->fetch_all();
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
                <h4 class="card-title">Create Account</h4>
              </div>
              <div class="card-body">
                <div class="form-validation">
                  <form action="admin_add_user_proc" method="post" class="needs-validation" novalidate>
                    <div class="row">
                      <div class="col-xl-6">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom05">Fullname
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input type="text" class="form-control" placeholder="Enter Fullname" name="name" id="name" required />
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom02">Username <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">

                            <input type="text" class="form-control" placeholder="Enter Username" name="user_name" id="user_name" required />

                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Password
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <input type="password" class="form-control" placeholder="Enter Password" name="user_pass" id="user_pass" required />
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">User Type
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="user_type" id="user_type" class="form-control" required>
                              <option data-display=" Select">Please select</option>
                              <option value="admin">Admin</option>
                              <option value="inbound">Inbound</option>
                              <option value="outbound">Outbound</option>
                              <option value="inventory">Inventory</option>
                              <option value="transport">Transport</option>
                              <option value="viewer">Viewer</option>
                              <option value="picker">Picker</option>
                              <option value="checker">Checker</option>
                              <option value="validator">Validator</option>
                            </select>

                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xl-6">
                        <div class="mb-3 row">
                          <label class="col-lg-4 col-form-label" for="validationCustom03">Status
                            <span class="text-danger">*</span>
                          </label>
                          <div class="col-lg-6">
                            <select name="user_status" id="user_status" class="form-control" required>
                              <option value="">Select Status</option>
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
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