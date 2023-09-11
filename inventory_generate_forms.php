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

  <div id="main-wrapper">
    <?php
    if (isset($_SESSION['msg'])) {
    ?>
      <script>
        swal.fire({

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

    <!--**********************************
            Content body start
        ***********************************-->
    <div class="content-body">
      <?php
      if ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "inventory") {
      ?>
        <div class="container-fluid">
          <!-- row -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Generate Forms</h4>
                </div>
                <div class="card-body">
                  <div class="form-validation">
                    <form action="generate_form_proc" method="post">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-lg-4">

                            <select class="form-control" name="nature">
                              <option value="">Select Nature of Forms</option>
                              <option value="1">Stock Replacement</option>
                              <option value="2">Replenishment</option>
                              <option value="3">Put Away</option>
                              <option value="4">Bin to Bin Transfer</option>
                            </select>
                          </div>

                          <div class="col-lg-4">
                            <button type="submit" class="btn btn-primary">Generate Form</button>
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
      <?php } ?>
      <?php
      // $date = date('Y-m-d');
      $all_count_sheet = $db->query('SELECT * FROM tb_generated_forms')->fetch_all();

      ?>
      <div class="container-fluid">
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Generate Forms</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display">
                    <thead>
                      <tr>

                        <th class="align-middle text-center  font-weight-bold ">Ref No</th>
                        <th class="align-middle text-center  font-weight-bold ">Nature</th>
                        <th class="align-middle text-center  font-weight-bold ">Date Generated</th>
                        <th class="align-middle text-center  font-weight-bold ">Created By</th>
                        <th class="align-middle text-center  font-weight-bold ">Print Stock Replacement</th>
                        <th class="align-middle text-center  font-weight-bold ">Print Replenishment</th>
                        <th class="align-middle text-center  font-weight-bold ">Print Put Away</th>
                        <th class="align-middle text-center  font-weight-bold ">Print Bin Transfer</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_count_sheet as $arr_key => $arr_val) {
                        //print_r_html($db_str);
                      ?>

                        <tr>
                          <td class="align-middle text-center "><?php echo $arr_val['ref_no']; ?></td>
                          <td class="align-middle text-center "><?php
                                                                if ($arr_val['nature'] == '1') {
                                                                  echo "Stock Replacement";
                                                                }
                                                                if ($arr_val['nature'] == '2') {
                                                                  echo "Replenishment";
                                                                }
                                                                if ($arr_val['nature'] == '3') {
                                                                  echo "Put Away";
                                                                }
                                                                if ($arr_val['nature'] == '4') {
                                                                  echo "Bin to Bin Transfer";
                                                                }
                                                                ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['date_generated']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['created_by']; ?></td>

                          <?php if ($arr_val['nature'] != '1') : ?>
                            <td class="align-middle text-center">
                              <a target="_blank" href="<?php echo "print_replacement_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm disabled" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php else : ?>
                            <td class="align-middle text-center">
                              <a target="_blank" href="<?php echo "print_replacement_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php endif; ?>

                          <?php if ($arr_val['nature'] != '2') : ?>
                            <td class="align-middle text-center">
                              <!-- 3rd Cut Count Sheet-->
                              <a target="_blank" href="<?php echo "print_replenishment_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm disabled" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php else : ?>
                            <td class="align-middle text-center">
                              <a target="_blank" href="<?php echo "print_replenishment_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php endif; ?>

                          <?php if ($arr_val['nature'] != '3') : ?>
                            <td class="align-middle text-center">
                              <!-- 3rd Cut Count Sheet-->
                              <a target="_blank" href="<?php echo "print_put_away_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm disabled" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php else : ?>
                            <td class="align-middle text-center">
                              <a target="_blank" href="<?php echo "print_put_away_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php endif; ?>

                          <?php if ($arr_val['nature'] != '4') : ?>
                            <td class="align-middle text-center">
                              <!-- 3rd Cut Count Sheet-->
                              <a target="_blank" href="<?php echo "print_bin_transfer_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm disabled" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php else : ?>
                            <td class="align-middle text-center">
                              <a target="_blank" href="<?php echo "print_bin_transfer_form?ref_no={$arr_val['ref_no']}&nature={$arr_val['nature']}" ?>" class="btn btn-outline-dark btn-sm" title="Edit"><i class="fas fa-print"></i></a>
                            </td>
                          <?php endif; ?>



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
        Main wrapper end
    ***********************************-->

  <!--**********************************
        Scripts
    ***********************************-->
  <!-- Required vendors -->
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
    $(document).ready(function() {
      $('#view_forms_table').DataTable({
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