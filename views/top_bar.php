<div class="dlabnav">
  <div class="dlabnav-scroll">
    <!-- ADMIN ACCESS -->
    <?php
    if ($_SESSION['user_type'] == "admin") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li><a class="has-arrow " aria-expanded="false">
            <i class="material-icons">pallet</i>
            <span class="nav-text">Inbound</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="upload_asn">Upload / Advance Shipment Notice</a></li>
            <li><a href="view_asn">Incoming Shipment</a></li>
            <li><a href="view_incident_report">View Incident Report</a></li>
            <li><a href="javascript:void(0);" aria-expanded="false">Fulfillment</a>
              <ul aria-expanded="false">
                <li><a href="inbound_fullfillment">Pending Fulfillment</a></li>
                <!-- <li><a href="view_inbound_fulfillment">Fulfilled Transactions</a></li> -->
              </ul>
            </li>
            <!-- <li><a href="javascript:void(0);" aria-expanded="false">Incident Report</a>
              <ul aria-expanded="false">
                <li><a href="./inbound_incident_report">Create Incident Report</a></li>
                <li><a href="./inbound_view_incident_report">View Incident Report</a></li>
              </ul>
            </li>
            <li><a href="inbound_posted_goods">Posted Goods</a></li>
            <li><a href="inbound_quarantine_items">Quarantine Items</a></li> -->
            <li><a href="javascript:void(0);" aria-expanded="false">Putaway</a>
              <ul aria-expanded="false">
                <li><a href="inbound_putaway">Pending Putaway</a></li>
                <!-- <li><a href="print_movement_form">Print Putaway Form</a></li> -->
              </ul>
            </li>
            <!-- <li><a href="view_incoming">Receive Stock</a></li> -->
          </ul>
        </li>
        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
            <i class="material-icons"> outbound </i>
            <span class="nav-text">Outbound</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="outbound_upload_rdc" aria-expanded="false">Upload Order</a>
              <!-- <ul aria-expanded="false">
                <li><a href="./outbound_upload_rdc">RDC </a></li>
                <li><a href="./outbound_upload_rdd">RDD </a></li>
              </ul> -->
            </li>
            <li><a href="outbound_incoming_dispatch">Order Summary</a></li>
            <li><a href="view_dispatch_summary">Dispatch Summary</a></li>
            <!-- <li><a href="javascript:void(0);" aria-expanded="false">Fullfillment</a>
              <ul aria-expanded="false">
                <li><a href="outbound_fullfillment">Pending Fullfillment</a></li>
                <li><a href="outbound_fullfilled_transaction">Fullfilled Transaction</a></li>
              </ul>
            </li> -->
            <!-- <li><a href="javascript:void(0);" aria-expanded="false">Incident Report</a>
              <ul aria-expanded="false">
                <li><a href="./outbound_ir">Add IR</a></li>
                <li><a href="./outbound_ir_list">View IR</a></li>
              </ul>
            </li>
            <li><a href="view_picklist">Pick Stock</a></li>
            <li><a href="view_checklist">Check Stock</a></li>
            <li><a href="view_validation">Validate Stock</a></li> -->
          </ul>
        </li>
        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
            <i class="material-icons"> inventory </i>
            <span class="nav-text">Inventory</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="check_stock">Inventory Level</a></li>
            <!-- <li><a href="inventory_allocated_items">View Allocated Load Plan</a></li> -->

            <!-- <li><a href="javascript:void(0);" aria-expanded="false">Allocation</a>

              <ul aria-expanded="false">
                <li><a href="inventory_add_allocation">Allocate Load Plan</a></li>
              </ul>
            </li> -->
            <li><a href="putaway_confirmation">Putaway Confirmation</a></li>
            <li><a href="view_allocated_stocks">View Allocated Stocks</a></li>
            <li><a href="lock_items">Lock Items</a></li>
            <li><a href="view_locked_items">View Locked Items</a></li>
            <!-- <li><a href="inventory_upload_soh">Upload SOH</a></li> -->
            <!-- <li><a href="inventory_stock_monitoring">Stock Monitoring</a></li> -->
            <li><a href="inventory_update_bin_loc">Bin to Bin</a></li>
            <!-- <li><a href="inventory_generate_forms">Generate Forms</a></li> -->
            <!-- <li><a href="inventory_replenishment">View Replenishment</a></li> -->
            <!-- <li><a href="inventory_staging_area">Items On Staging Area</a></li> -->
            <!-- <li><a href="cycle_count">Cycle Count</a></li> -->
            <li><a href="inventory_daily_cycle_count">Cycle Count (per Aisle)</a></li>
            <li><a href="inventory_daily_cycle_count_per_sku">Cycle Count (per SKU)</a></li>
          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
            <i class="material-icons"> history </i>
            <span class="nav-text">Returns</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="create_returns">Create Returns</a></li>
          </ul>
        </li>
        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> local_shipping </i>
            <span class="nav-text">Transport</span>
          </a>
          <ul aria-expanded="false">

            <li><a href="transport_truck_allocation">Truck Allocation</a></li>
            <li><a href="view_allocated_trucks">View Allocated Trucks</a></li>
            <!-- <li><a href="transport_dispatch_monitoring">Dispatch Monitoring</a></li>
            <li><a href="transport_delivery_monitoring">Delivery Monitoring</a></li> -->

          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> receipt_long </i>
            <span class="nav-text">Reports</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="inventory_stock_report">Daily Inventory Report</a></li>
            <li><a href="assembly_build_report">Assembly Build</a></li>
            <li><a href="transfer_order_register">Transfer Order Register</a></li>
            <li><a href="detailed_inventory_adjustment">Detailed Inventory Adjustment</a></li>
            <li><a href="incident_report_registers">Incident Report Registers</a></li>
            <!-- <li><a href="inventory_aging_report">Aging Report</a></li> -->
            <!-- <li><a href="inventory_daily_cycle_count">Daily Cycle Count</a></li>
            <li><a href="inventory_item_summary">Item Summary Report</a></li>
            <li><a href="inbound_report">Inbound Summary Report</a></li>
            <li><a href="outbound_report">Outbound Summary Report</a></li>
            <li><a href="inbound_fullfillment_report">Inbound Fullfillment Report</a></li>
            <li><a href="outbound_fullfillment_report">Outbound Fullfillment Report</a></li>
            <li><a href="transport_dispatch_report">Dispatch Report</a></li>
            <li><a href="transport_delivery_report">Delivery Report</a></li> -->
            <li><a href="stock_verification_report">Stock Verification Report</a></li>
            <li><a href="view_sap_f2">SAP Integration File (F2)</a></li>
          </ul>
        </li>
        
        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

          <i class="material-icons"> visibility </i>
          <span class="nav-text">Viewer</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="assembly_build_report">Assembly Build</a></li>
            <li><a href="transfer_order_register">Transfer Order Register</a></li>
            <li><a href="inventory_stock_report">Inventory Report</a></li>
            <li><a href="incident_report_registers">Incident Report Registers</a></li>
          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> visibility </i>
            <span class="nav-text">Operator</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="putaway">Put Away</a></li>
            <li><a href="replenishment">Replenishment</a></li>
            <!--<li><a href="outbound_report">Stock Transfer</a></li>-->


          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
            <i class="material-icons"> admin_panel_settings </i>
            <span class="nav-text">Administrator</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="javascript:void(0);" aria-expanded="false">Item Master list</a>
              <ul aria-expanded="false">
                <li><a href="admin_add_items">Add Items</a></li>
                <li><a href="admin_manage_items">Manage Items</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Customer Master</a>
              <ul aria-expanded="false">
                <li><a href="upload_customer_master">Bulk Upload - Customer</a></li>
                <li><a href="admin_add_items">Add Customer</a></li>
                <li><a href="admin_manage_items">Manage Customer</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Account Master list</a>
              <ul aria-expanded="false">
                <li><a href="admin_add_user">Add User</a></li>
                <li><a href="admin_manage_user">Manage User</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Location Masterlist</a>
              <ul aria-expanded="false">
                <li><a href="admin_add_location">Add Location</a></li>
                <li><a href="admin_manage_location">Manage Location</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Branch Masterlist</a>
              <ul aria-expanded="false">
                <li><a href="admin_add_branch">Add Branch</a></li>
                <li><a href="admin_manage_branch">Manage Branch</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Vendor/Source Masterlist</a>
              <ul aria-expanded="false">
                <li><a href="admin_add_vendor">Add Vendor</a></li>
                <li><a href="admin_manage_vendor">Manage Vendor</a></li>
              </ul>
            </li>
          </ul>
        </li>
      </ul>

    <?php
    }
    ?>




    <!-- INBOUND ACCESS -->
    <?php
    if ($_SESSION['user_type'] == "inbound") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        <li><a class="has-arrow " aria-expanded="false">
            <i class="material-icons">pallet</i>
            <span class="nav-text">Inbound</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="upload_asn">Upload / Advance Shipment Notice</a></li>
            <li><a href="view_asn">Incoming Shipment</a></li>
            <li><a href="javascript:void(0);" aria-expanded="false">Fullfillment</a>
              <ul aria-expanded="false">
                <li><a href="inbound_fullfillment">Add Fullfillment</a></li>
                <li><a href="inbound_fullfilled_transaction">Fullfilled Transaction</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Incident Report</a>
              <ul aria-expanded="false">
                <li><a href="./inbound_incident_report">Create Incident Report</a></li>
                <li><a href="./inbound_view_incident_report">View Incident Report</a></li>
              </ul>
            </li>
            <li><a href="inbound_posted_goods">Posted Goods</a></li>
            <li><a href="inbound_quarantine_items">Quarantine Items</a></li>
            <li><a href="inbound_putaway">Putaway</a></li>
            <li><a href="view_incoming">Receive Stock</a></li>
          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> receipt_long </i>
            <span class="nav-text">Reports</span>
          </a>
          <ul aria-expanded="false">

            <li><a href="inbound_report">Inbound Summary Report</a></li>
            <li><a href="inbound_fullfillment_report">Inbound Fullfillment Report</a></li>


          </ul>
        </li>
      </ul>

    <?php
    }
    ?>





    <!-- OUTBOUND ACCESS -->
    <?php
    if ($_SESSION['user_type'] == "outbound") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
            <i class="material-icons"> outbound </i>
            <span class="nav-text">Outbound</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="outbound_upload_rdc" aria-expanded="false">Upload Work List</a>
              <!-- <ul aria-expanded="false">
                <li><a href="./outbound_upload_rdc">RDC </a></li>
                <li><a href="./outbound_upload_rdd">RDD </a></li>
              </ul> -->
            </li>
            <li><a href="outbound_incoming_dispatch">Transfer Order</a></li>
            <li><a href="javascript:void(0);" aria-expanded="false">Fullfillment</a>
              <ul aria-expanded="false">
                <li><a href="outbound_fullfillment">Add Fullfillment</a></li>
                <li><a href="outbound_fullfilled_transaction">Fullfilled Transaction</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0);" aria-expanded="false">Incident Report</a>
              <ul aria-expanded="false">
                <li><a href="./outbound_ir">Add IR</a></li>
                <li><a href="./outbound_ir_list">View IR</a></li>
              </ul>
            </li>
            <li><a href="view_picklist">Pick Stock</a></li>
            <li><a href="view_checklist">Check Stock</a></li>
            <li><a href="view_validation">Validate Stock</a></li>
          </ul>
        </li>


        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> receipt_long </i>
            <span class="nav-text">Reports</span>
          </a>
          <ul aria-expanded="false">

            <li><a href="outbound_report">Outbound Summary Report</a></li>
            <li><a href="outbound_fullfillment_report">Outbound Fullfillment Report</a></li>

          </ul>
        </li>
      </ul>

    <?php
    }
    ?>





    <!-- INVENTORY ACCESS -->
    <?php
    if ($_SESSION['user_type'] == "inventory") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>


        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
            <i class="material-icons"> inventory </i>
            <span class="nav-text">Inventory</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="check_stock">Inventory Level</a></li>
            <li><a href="inventory_allocated_items">View Allocated Load Plan</a></li>
            <!-- <li><a href="javascript:void(0);" aria-expanded="false">Allocation</a>

              <ul aria-expanded="false">
                <li><a href="inventory_add_allocation">Allocate Load Plan</a></li>

               
              </ul>
            </li> -->
            <li><a href="inventory_upload_soh">Upload SOH</a></li>
            <!-- <li><a href="inventory_stock_monitoring">Stock Monitoring</a></li> -->
            <li><a href="inventory_update_bin_loc">Update Bin Location</a></li>
            <li><a href="inventory_generate_forms">Generate Forms</a></li>
            <li><a href="inventory_replenishment">View Replenishment</a></li>
            <li><a href="inventory_staging_area">Items On Staging Area</a></li>
            <li><a href="cycle_count">Cycle Count</a></li>
          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> receipt_long </i>
            <span class="nav-text">Reports</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="inventory_stock_report">Daily Inventory Report</a></li>
            <li><a href="inventory_aging_report">Aging Report</a></li>
            <li><a href="inventory_daily_cycle_count">Daily Cycle Count</a></li>
            <li><a href="inventory_item_summary">Item Summary Report</a></li>
            <li><a href="inbound_report">Inbound Summary Report</a></li>
            <li><a href="outbound_report">Outbound Summary Report</a></li>
            <li><a href="inbound_fullfillment_report">Inbound Fullfillment Report</a></li>
            <li><a href="outbound_fullfillment_report">Outbound Fullfillment Report</a></li>
            <li><a href="transport_dispatch_report">Dispatch Report</a></li>
            <li><a href="transport_delivery_report">Delivery Report</a></li>
          </ul>
        </li>

      </ul>

    <?php
    }
    ?>


    <!-- TRANSPORT ACCESS -->
    <?php
    if ($_SESSION['user_type'] == "transport") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>



        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> local_shipping </i>
            <span class="nav-text">Transport</span>
          </a>
          <ul aria-expanded="false">

            <li><a href="transport_truck_allocation">Truck Allocation</a></li>
            <li><a href="view_allocated_trucks">View Allocated Trucks</a></li>
            <li><a href="transport_dispatch_monitoring">Dispatch Monitoring</a></li>
            <li><a href="transport_delivery_monitoring">Delivery Monitoring</a></li>

          </ul>
        </li>
        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

            <i class="material-icons"> receipt_long </i>
            <span class="nav-text">Reports</span>
          </a>
          <ul aria-expanded="false">

            <li><a href="transport_dispatch_report">Dispatch Report</a></li>
            <li><a href="transport_delivery_report">Delivery Report</a></li>
          </ul>
        </li>
      </ul>

    <?php
    }
    ?>



    <!-- VIEWER ACCESS -->
    <?php
    if ($_SESSION['user_type'] == "viewer") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>


        <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">

          <i class="material-icons"> visibility </i>
          <span class="nav-text">Viewer</span>
          </a>
          <ul aria-expanded="false">
            <li><a href="assembly_build_report">Assembly Build</a></li>
            <li><a href="transfer_order_register">Transfer Order Register</a></li>
            <li><a href="inventory_stock_report">Inventory Report</a></li>
            <li><a href="incident_report_registers">Incident Report Registers</a></li>
          </ul>
        </li>




      </ul>

    <?php
    }
    ?>
    <?php
    if ($_SESSION['user_type'] == "inbound checker" || $_SESSION['user_type'] == "picker" || $_SESSION['user_type'] == "outbound checker" || $_SESSION['user_type'] == "validator" || $_SESSION['user_type'] == "operator") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index_user" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

      </ul>

    <?php
    }
    ?>

    <?php
    if ($_SESSION['user_type'] == "main guard") {
    ?>
      <ul class="metismenu" id="menu">
        <li><a href="index_main_guard" aria-expanded="false">
            <i class="material-icons-outlined">dashboard</i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

      </ul>

    <?php
    }
    ?>

  </div>
</div>