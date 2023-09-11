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
        <?php

        $db_ir = $db->query('SELECT
        a.id,
        a.ir_ref_no as ref_no,
        a.asn_id,
        a.asn_ref_no,
        a.document_no,
        a.sku_code,
        a.qty_case,
        a.expiry,
        a.ir_status,
        a.reason,
        a.description,
        tb_asn.forwarder,
        tb_asn.driver,
        tb_asn.plate_no,
        tb_items.material_description
        FROM tb_inbound_ir a
        LEFT JOIN tb_items ON tb_items.sap_code = a.sku_code
        LEFT JOIN tb_asn ON tb_asn.id = a.asn_id
        GROUP BY a.asn_id')->fetch_all();


        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Open Inbound IR</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th class="align-middle text-center">Action</th>
                        <!-- <th class="align-middle text-center">Uploading File Name</th> -->
                        <th class="align-middle text-center">IR ID</th>
                        <th class="align-middle text-center">IR REF</th>
                        <th class="align-middle text-center">Document No.</th>
                        <th class="align-middle text-center">Forwarder</th>
                        <th class="align-middle text-center">Driver</th>
                        <th class="align-middle text-center">Plate No.</th>
                        <th class="align-middle text-center">Status</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_ir as $arr_key => $arr_det) { ?>
                        <tr>
                          <td>
                            <div class="d-flex">
                              <a  data-toggle="modal" data-target="#update_details<?php echo $arr_det['asn_id'];?>" class="btn btn-primary shadow btn-xs sharp me-1" title="View/Update"><i class="fas fa-pencil-alt"></i></a>
                              <a target="_blank" href="<?php echo "print_inbound_ir?asn_id={$arr_det['asn_id']}";?>" class="btn btn-warning shadow btn-xs sharp me-1" title="Print IR"><i class="fa-solid fa-print"></i></a>
                            </div>												
                          </td>
                          <td class="align-middle text-center" ><?php echo $arr_det['id']; ?></td>
                          <td class="align-middle text-center" ><?php echo "IR-" . $arr_det['ref_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['document_no']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['forwarder']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['driver']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['plate_no']; ?></td>

                          <td class = 'align-middle text-center'>
                            <?php
                              if (are_strings_equal($arr_det['ir_status'],"Open")) {
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Open</span>";
                              }else{
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-success me-1'></i>Closed</span>";
                              }
                            ?>
                          </td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--**********************************
            Content body end
        ***********************************-->

  </div>
  <!--**********************************
        Main wrapper end
    ***********************************-->

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