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
            
          $date_today = date('Y-m-d');

          $previous_week = strtotime("-1 week +1 day");

          $start_week_aux = strtotime("last sunday midnight",$previous_week);
          $start_week = date("Y-m-d",$start_week_aux);
          $end_week = date('Y-m-d',strtotime("saturday this week +4 days"));

          // echo $start_week.' '.$end_week ;

          $db_allocated = $db->query('SELECT * FROM tb_transfer_order WHERE rdd BETWEEN ? AND ? AND status = ? ORDER BY rdd DESC',$start_week,$end_week,"Picklist Issuance")->fetch_all();

            // print_r_html($db_allocated);
        ?>
        <!-- row -->
        <div class="row">
          <div class="col-lg-12">
          <form action="reverse_allocated_stocks" method="post">
            <div class="card">
              <div class="card-header">
                <div class="row">
                    <div class="control-group">
                        <button type="submit" class="btn btn-md btn-danger">
                          <i class="fa-solid fa-clock-rotate-left"> Reverse</i>
                        </button>
                      <!-- <input type="submit" class="btn btn-lg btn-primary" value="CONFIRM"/> -->
                    </div>
                </div>
                <div class="row">
                  <h4 class="card-title">Allocated Order</h4>
                </div> 
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <thead>
                      <tr>
                        <th>
                            <div class="form-check custom-checkbox ms-1">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                                <label class="form-check-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th class="align-middle text-center">ID</th>
                        <th class="align-middle text-center">RDD</th>
                        <th class="align-middle text-center">Ship To</th>
                        <th class="align-middle text-center">SO No</th>
                        <!-- <th class="align-middle text-center">SO Item No</th> -->
                        <!-- <th class="align-middle text-center">SKU</th> -->
                        <th class="align-middle text-center">Required Qty</th>
                        <th class="align-middle text-center">Allocated Qty</th>
                        <th class="align-middle text-center">Allocated By</th>
                        <th class="align-middle text-center">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_allocated as $arr_key => $arr_det) { ?>
                        <tr>
                          <!-- <td>
                            <div class="d-flex">
                              <?php if($arr_det['req_qty_case'] == $arr_det['allocated_qty']){ ?>
                                <a data-toggle="modal" data-target="#cancel_order<?php echo $asar_val['id'];?>" class="btn btn-success shadow btn-xs sharp me-1" title="Done"><i class="fa-regular fa-circle-check"></i></a>
                              <?php } ?>
                              
                              <?php if($arr_det['req_qty_case'] > $arr_det['allocated_qty']){ ?>
                                <a data-toggle="modal" data-target="#cancel_order<?php echo $asar_val['id'];?>" class="btn btn-warning shadow btn-xs sharp me-1" title="Reverse"><i class="fa-solid fa-clock-rotate-left"></i></a>
                              <?php } ?>

                              <?php if($arr_det['req_qty_case'] < $arr_det['allocated_qty']){ ?>
                                <a data-toggle="modal" data-target="#cancel_order<?php echo $asar_val['id'];?>" class="btn btn-danger shadow btn-xs sharp me-1" title="Reverse"><i class="fa-solid fa-clock-rotate-left"></i></a>
                              <?php } ?>
                            </div>
												  </td> -->
                          <td>
                            <div class="form-check custom-checkbox ms-1">
                                <input type="checkbox" class="form-check-input" id="<?php echo $arr_det['id']?>" name = "checkbox[]" value ="<?php echo $arr_det['id']?>">
                            </div>
                          </td>
                          <td class="align-middle text-center" ><?php echo $arr_det['id']; ?></td>                          
                          <td class="align-middle text-center" ><?php echo $arr_det['rdd']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['ship_to_code']; ?></td>
                          <td class="align-middle text-center" ><?php echo $arr_det['so_no']; ?></td>
                          <!-- <td class="align-middle text-center" ><?php echo $arr_det['so_item_no']; ?></td> -->
                          <!-- <td class="align-middle text-center" ><?php echo $arr_det['sku_code'].'-'.$arr_det['material_description']; ?></td> -->
                          <td class="align-middle text-center "><?php echo $arr_det['req_qty_case']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['allocated_qty']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_det['allocated_by']; ?></td>
                          <td class = 'align-middle text-center'>
                            <?php if($arr_det['req_qty_case'] == $arr_det['allocated_qty']){ ?>
                              <span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>OK</span>
                            <?php } ?>
                            
                            <?php if($arr_det['req_qty_case'] > $arr_det['allocated_qty']){ ?>
                              <span class='badge badge-sm light badge-warning'> <i class='fa fa-circle text-warning me-1'></i>Insufficient</span>
                            <?php } ?>

                            <?php if($arr_det['req_qty_case'] < $arr_det['allocated_qty']){ ?>
                              <span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Over Allocated</span>
                            <?php } ?>
                          </td>

                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                </div>
              </div>
            </div>
          </form>
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