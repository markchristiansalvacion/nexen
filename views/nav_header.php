<div class="nav-header">
  <?php
  if ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == 'inbound' || $_SESSION['user_type'] == 'outbound' || $_SESSION['user_type'] == 'inventory' || $_SESSION['user_type'] == 'transport' || $_SESSION['user_type'] == 'viewer') {
  ?>
    <a href="index" class="brand-logo">
      <img src="img/Logo ArrowgoL.png" width="60" height="60">

      <div class="brand-title">
        <h1>IRIS<h1>
      </div>
    </a>

    <div class="nav-control">

    </div>

  <?php } ?>
  <?php
  if ($_SESSION['user_type'] == "inbound checker" || $_SESSION['user_type'] == "outbound checker" || $_SESSION['user_type'] == "picker" || $_SESSION['user_type'] == "validator" || $_SESSION['user_type'] == "operator" || $_SESSION['user_type'] == "main guard") {
  ?>
    <a href="index_user" class="brand-logo">
      <img src="img/Logo ArrowgoL.png" width="60" height="60">

      <div class="brand-title">
        <h1>WMS<h1>
      </div>
    </a>
    <div class="nav-control">

    </div>

  <?php } ?>
</div>