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
          <div class="col-xl-3 col-lg-4">
            <div class="clearfix">
              <div class="card card-bx profile-card author-profile m-b30">
                <div class="card-body">
                  <div class="p-5">
                    <div class="author-profile">
                      <div class="author-media">
                        <img src="images/user.jpg" alt="">
                        <div class="upload-link" title="" data-toggle="tooltip" data-placement="right" data-original-title="update">
                          <input type="file" class="update-flie">
                          <i class="fa fa-camera"></i>
                        </div>
                      </div>
                      <div class="author-info">
                        <h6 class="title">Nella Vita</h6>
                        <span>Developer</span>
                      </div>
                    </div>
                  </div>
                  <div class="info-list">
                    <ul>
                      <li><a href="app-profile.html">Models</a><span>36</span></li>
                      <li><a href="uc-lightgallery.html">Gallery</a><span>3</span></li>
                      <li><a href="app-profile.html">Lessons</a><span>1</span></li>
                    </ul>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="input-group mb-3">
                    <div class="form-control rounded text-center bg-white">Portfolio</div>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-9 col-lg-8">
            <div class="card profile-card card-bx m-b30">
              <div class="card-header">
                <h6 class="title">Profile</h6>
              </div>
              <form class="profile-form">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-6 m-b30">
                      <label class="form-label">Fullname</label>
                      <input type="text" class="form-control" value="John">
                    </div>
                    <div class="col-sm-6 m-b30">
                      <label class="form-label">Email</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col-sm-6 m-b30">
                      <label class="form-label">New Password</label>
                      <input type="password" class="form-control">
                    </div>

                    <div class="col-sm-6 m-b30">
                      <label class="form-label">Phone</label>
                      <input type="text" class="form-control" value="+123456789">
                    </div>

                  </div>
                </div>
                <div class="card-footer">
                  <button class="btn btn-primary">UPDATE</button>

                </div>
              </form>
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





        <!-- Daterangepicker -->
        <!-- momment js is must -->
        <script src="./vendor/moment/moment.min.js"></script>
        <script src="./vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- clockpicker -->
        <script src="./vendor/clockpicker/js/bootstrap-clockpicker.min.js"></script>
        <!-- asColorPicker -->
        <script src="./vendor/jquery-asColor/jquery-asColor.min.js"></script>
        <script src="./vendor/jquery-asGradient/jquery-asGradient.min.js"></script>
        <script src="./vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js"></script>
        <!-- Material color picker -->
        <script src="./vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
        <!-- pickdate -->
        <script src="./vendor/pickadate/picker.js"></script>
        <script src="./vendor/pickadate/picker.time.js"></script>
        <script src="./vendor/pickadate/picker.date.js"></script>



        <!-- Daterangepicker -->
        <script src="./js/plugins-init/bs-daterange-picker-init.js"></script>
        <!-- Clockpicker init -->
        <script src="./js/plugins-init/clock-picker-init.js"></script>
        <!-- asColorPicker init -->
        <script src="./js/plugins-init/jquery-asColorPicker.init.js"></script>
        <!-- Material color picker init -->
        <script src="./js/plugins-init/material-date-picker-init.js"></script>
        <!-- Pickdate -->
        <script src="./js/plugins-init/pickadate-init.js"></script>
        <script src="./vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
        <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
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