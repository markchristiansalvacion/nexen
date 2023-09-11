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
$i = 0;
$db_location_details = $db->query('SELECT 
id,
aisle,
warehouse,
layer,
location_code,
status,
location_type,
item_code,
category FROM tb_bin_location_bac WHERE aisle=?', $_GET['aisle'])
  ->fetch_all();
foreach ($db_location_details as $key_val => $stat_det) {
  $status = $stat_det['status'];
}
?>
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

    <div class="content-body">
      <div class="container-fluid">
        <!-- row -->

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Manage Location</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display" style="min-width: 845px">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="example4"> -->
                    <thead>
                      <tr>
                        <th class="align-middle text-center  font-weight-bold ">Id</th>
                        <th class="align-middle text-center  font-weight-bold ">Item Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Category</th>
                        <th class="align-middle text-center  font-weight-bold ">Location Code</th>
                        <th class="align-middle text-center  font-weight-bold ">Status</th>
                        <th class="align-middle text-center  font-weight-bold ">Update Status</th>
                        <th class="align-middle text-center  font-weight-bold ">Update Details</th>



                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($db_location_details as $arr_key => $arr_val) { ?>
                        <tr>


                          <td class="align-middle text-center "><?php echo $arr_val['id']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['item_code']; ?></td>
                          <td class="align-middle text-center "><?php echo $arr_val['category']; ?></td>
                          <td class="align-middle text-center"><?php echo $arr_val['location_code']; ?></td>
                          <td class="align-middle text-center ">
                            <?php

                              if (are_strings_equal($arr_val['status'],"Available")) {
                                echo "<span class='badge badge-sm light badge-success'> <i class='fa fa-circle text-success me-1'></i>Available</span>";
                              }else{
                                echo "<span class='badge badge-sm light badge-danger'> <i class='fa fa-circle text-danger me-1'></i>Occupied</span>";
                              }
                             
                              ?>
                          </td>

                          
                            <?php if (are_strings_equal($arr_val['status'],"Available")) { ?>
                              <td class="align-middle text-center "><a target="" href="<?php echo "update_location_status?db_id={$arr_val['id']}&aisle={$_GET['aisle']}&curr_status={$arr_val['status']}";?>" class="btn btn-outline-danger btn-md" title="Update Details"><i class="fa-solid fa-power-off"></i></a></td>
                            <?php }else{ ?>
                              <td class="align-middle text-center "><a target="" href="<?php echo "update_location_status?db_id={$arr_val['id']}&aisle={$_GET['aisle']}&curr_status={$arr_val['status']}";?>" class="btn btn-outline-success btn-md" title="Update Details"><i class="fa-solid fa-power-off"></i></a></td>
                            <?php } ?>
                             
                        
                          
                          <td class="align-middle text-center "><a target="" data-toggle="modal" data-target="#edit<?php echo $arr_val['id']; ?>" href="" class="btn btn-outline-primary btn-md" title="Update Details"><i class="fas fa-pen"></i></a></td>

                          <!-- MODAL FOR DELETE-->
                          <div id="edit<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Updating...</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="update_loc_details" method="post">
                                    <div class="form-group">
                                      <!-- URL-->
                                      <div class="mt-1">
                                        <input type="hidden" name="url" id="url" value="<?php echo "view_location_list?aisle={$arr_val['aisle']}"; ?>">
                                      </div>
                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>
                                      <!-- SOURCE ADDRESS-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Aisle</label>
                                        <input type="text" name="aisle" id="aisle" class="form-control" value="<?php echo $arr_val['aisle']; ?>">
                                      </div>
                                      <!-- SOURCE ID-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Item Code</label>
                                        <input type="text" name="i_code" id="i_code" class="form-control" value="<?php echo $arr_val['item_code']; ?>">
                                      </div>

                                      <!-- SOURCE NAME-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Category</label>
                                        <input type="text" name="cat" id="cat" class="form-control" value="<?php echo $arr_val['category']; ?>">
                                      </div>

                                      <!-- SOURCE ADDRESS-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Location Code</label>
                                        <input type="text" name="loc" id="loc" class="form-control" value="<?php echo $arr_val['location_code']; ?>">
                                      </div>
                                      <!-- SOURCE ADDRESS-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Warehouse #</label>
                                        <input type="text" name="whse" id="whse" class="form-control" value="<?php echo $arr_val['warehouse']; ?>">
                                      </div>
                                      <!-- SOURCE ADDRESS-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Layer/Column</label>
                                        <input type="text" name="layer" id="layer" class="form-control" value="<?php echo $arr_val['layer']; ?>">
                                      </div>
                                      <!-- SOURCE ADDRESS-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Location Type</label>
                                        <input type="text" name="l_type" id="l_type" class="form-control" value="<?php echo $arr_val['location_type']; ?>">
                                      </div>




                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>


                        </tr>

                      <?php
                        ++$i;
                      }
                      ?>
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
  </div>

  </div>


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

</body>

</html>