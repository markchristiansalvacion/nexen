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
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="keywords" content="" />
  <meta name="author" content="" />
  <meta name="robots" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="FinLab : Crypto Trading UI Admin  Bootstrap 5 Template" />
  <meta property="og:title" content="FinLab : Crypto Trading UI Admin  Bootstrap 5 Template" />
  <meta property="og:description" content="FinLab : Crypto Trading UI Admin  Bootstrap 5 Template" />
  <meta property="og:image" content="https://finlab.dexignlab.com/xhtml/social-image.png" />
  <meta name="format-detection" content="telephone=no">

  <!-- PAGE TITLE HERE -->
  <title>Arrowgo Logistics WMS</title>
  <!-- FAVICONS ICON -->
  <link rel="shortcut icon" href="img/Logo ArrowgoL.png">
  <link href="./vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

</head>

<body class="vh-100">
  <div class="authincation h-100">
    <div class="container h-100">
      <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-5">
          <div class="form-input-content text-center error-page">
            <h1 class="error-text fw-bold">400</h1>
            <h4><i class="fa fa-thumbs-down text-danger"></i> Bad Request</h4>
            <p>Your Request resulted in an error</p>
            <div>
              <a class="btn btn-primary" href="index">Back to Home</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--**********************************
	Scripts
***********************************-->
  <!-- Required vendors -->
  <script src="./vendor/global/global.min.js"></script>
  <script src="./js/custom.min.js"></script>
  <script src="./js/dlabnav-init.js"></script>
  <script src="./js/styleSwitcher.js"></script>
</body>

</html>