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

$all_items = $db->query('SELECT * from tb_destination ORDER BY destination_code  DESC')->fetch_all();

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
                <h4 class="card-title">Manage Branch</h4>
              </div>
              <div class="card-body">

                <div class="table-responsive">
                  <table id="example4" class="display">
                    <!-- <table class="table table-bordered table-responsive-sm display" id="view_asn_table"> -->
                    <thead>
                      <tr>
                        <th class="align-middle  text-center  font-weight-bold ">Id</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Code</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Name</th>
                        <th class="align-middle  text-center  font-weight-bold ">Branch Address</th>
                        <th class="align-middle  text-center  font-weight-bold ">Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($all_items as $arr_key => $arr_val) { ?>
                        <tr>

                          <td class="align-middle  text-center "><?php echo $arr_val['id']; ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['destination_code'];
                                                                  ?></td>
                          <td class="align-middle  text-center "><?php echo $arr_val['destination_name'];
                                                                  ?></td>

                          <td class="align-middle  text-center "><?php echo $arr_val['destination_address'];
                                                                  ?></td>
                          <td>
                            <div class="dropdown">
                              <button type="button" class="btn btn-success light sharp" data-bs-toggle="dropdown">
                                <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                  </g>
                                </svg>
                              </button>
                              <div class="dropdown-menu">

                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#edit<?php echo $arr_val['id']; ?>" class="dropdown-item">Edit</a>
                                
                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#delete<?php echo $arr_val['id']; ?>" class="dropdown-item" style="color:red;">Delete</a>
                              </div>
                            </div>
                          </td>
                          <div id="delete<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Deleting...</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="delete_branch" method="post">
                                    <div class="form-group">

                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo
                                                                                            $arr_val['id']; ?>">
                                      </div>

                                      <!-- ATA-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Do you want to delete?
                                          Please Confirm.</label>
                                      </div>


                                      <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-primary">Delete</button>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- End Modal -->
                          <!-- MODAL FOR DELETE-->
                          <div id="edit<?php echo $arr_val['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 id="exampleModalLabel" class="modal-title">Updating...</h4>

                                </div>
                                <div class="modal-body">
                                  <form action="update_branch" method="post" class="needs-validation" novalidate>
                                    <div class="form-group">

                                      <!-- ID-->
                                      <div class="mt-1">
                                        <input type="hidden" name="db_id" id="db_id" value="<?php echo $arr_val['id']; ?>">
                                      </div>
                                      <!-- SOURCE ID-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Vendor Code</label>
                                        <input type="text" name="v_code" id="v_code" class="form-control" value="<?php echo $arr_val['destination_code']; ?>" required>
                                      </div>

                                      <!-- SOURCE NAME-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Vendor Name</label>
                                        <input type="text" name="v_name" id="v_name" class="form-control" value="<?php echo $arr_val['destination_name']; ?>" required>
                                      </div>

                                      <!-- SOURCE ADDRESS-->
                                      <div class="mt-1">
                                        <label for="time" class="form-control-label text-uppercase
                                                  text-primary font-weight-bold">Vendor Address</label>
                                        <input type="text" name="v_add" id="v_add" class="form-control" value="<?php echo $arr_val['destination_address']; ?>" required>
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
                          <!-- End Modal -->

                          <!-- MODAL FOR DELETE-->

                        </tr>
                      <?php
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
  <script>
    $(document).ready(function() {
      $('#view_asn_table').DataTable({
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
</body>

</html>