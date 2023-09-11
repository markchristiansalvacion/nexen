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

      <div class="container-fluid">

        <?php

            $db_items = $db->query('SELECT sap_code,material_description FROM tb_items')->fetch_all();

        ?>
        <!-- row -->
       
        <div class="card text-center">
          <div class="card-header">
            IRIS a Product of Arrowgo-Logistics Inc.
          </div>
          <div class="card-body">
            <h5 class="card-title">Lock Specific Line Items</h5>
            <p class="card-text">Kindly Select the Material No. and BBD of Items to be Locked</p>
            <div class="col-lg-12 text-center">
                  <div class="form-validation">
                    <form action="lock_items_proc" method="post" class="needs-validation" novalidate>
                      <div class="form-group">
                          <div class="row mb-2">
                            <div class="col-lg-6">
                                <input list="actual_sku" class="form-control" name="actual_sku" placeholder="Select SKU" required>
                                <datalist id="actual_sku">
                                    <?php foreach($db_items as $arr_key => $arr_val){  ?>
                                    <option value="<?php echo $arr_val['sap_code']; ?>"><?php echo $arr_val['sap_code'].'-'.$arr_val['material_description']; ?></option>
                                    <?php } ?>
                                </datalist>
                            </div>
                            <div class="col-lg-6">
                                <input placeholder="Select BBD" name="bbd" class="form-control" onmouseover="(this.type='date')" data-toggle="tooltip" data-placement="top" title="BBD" required>
                            </div>
                          </div>
                          <div class="row">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa-solid fa-lock"> Lock Item</i>
                                </button>
                            </div>
                          </div>
                      </div>
                    </form>
                  </div>
            </div>
            
          </div>
          <div class="card-footer text-muted">
            <?php echo date('Y-M-d');?>
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
  <!-- Required vendors -->
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
      $('#view_report_table').DataTable({
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