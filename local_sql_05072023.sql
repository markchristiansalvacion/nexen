-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.28-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for siri_mdc_db2
DROP DATABASE IF EXISTS `siri_mdc_db2`;
CREATE DATABASE IF NOT EXISTS `siri_mdc_db2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `siri_mdc_db2`;

-- Dumping structure for table siri_mdc_db2.tb_activity_logs
DROP TABLE IF EXISTS `tb_activity_logs`;
CREATE TABLE IF NOT EXISTS `tb_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) NOT NULL,
  `account` varchar(255) NOT NULL,
  `access_type` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_asn
DROP TABLE IF EXISTS `tb_asn`;
CREATE TABLE IF NOT EXISTS `tb_asn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `uploading_file_name` varchar(255) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `pull_out_request_no` varchar(255) NOT NULL,
  `date_requested` date NOT NULL,
  `pull_out_date` date NOT NULL,
  `eta` date NOT NULL,
  `ata` date DEFAULT NULL,
  `source_code` varchar(255) DEFAULT NULL,
  `destination_code` varchar(255) NOT NULL,
  `forwarder` varchar(255) NOT NULL,
  `truck_type` varchar(255) NOT NULL,
  `driver` varchar(255) NOT NULL,
  `plate_no` varchar(255) DEFAULT NULL,
  `sku_code` varchar(255) DEFAULT NULL,
  `actual_sku` varchar(255) DEFAULT NULL,
  `qty_case` int(11) DEFAULT NULL,
  `actual_qty` int(11) DEFAULT NULL,
  `document_no` varchar(255) NOT NULL,
  `bay_location` varchar(255) NOT NULL,
  `checker` varchar(255) NOT NULL,
  `time_arrived` time DEFAULT NULL,
  `unloading_start` time DEFAULT NULL,
  `unloading_end` time DEFAULT NULL,
  `time_departed` time DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` varchar(255) NOT NULL,
  `date_created` date NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_assembly_build
DROP TABLE IF EXISTS `tb_assembly_build`;
CREATE TABLE IF NOT EXISTS `tb_assembly_build` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ab_ref_no` varchar(255) DEFAULT NULL,
  `asn_id` int(11) DEFAULT NULL,
  `asn_ref_no` varchar(50) DEFAULT NULL,
  `document_no` varchar(50) DEFAULT NULL,
  `sku_code` varchar(255) DEFAULT NULL,
  `qty_case` int(11) DEFAULT NULL,
  `expiry` date NOT NULL,
  `fulfillment_status` enum('Done','Pending') NOT NULL DEFAULT 'Pending',
  `created_by` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_beg_bal
DROP TABLE IF EXISTS `tb_beg_bal`;
CREATE TABLE IF NOT EXISTS `tb_beg_bal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(255) NOT NULL,
  `in_summ` decimal(10,2) NOT NULL,
  `out_summ` decimal(10,2) NOT NULL,
  `beg_bal` decimal(10,2) NOT NULL,
  `remarks` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=906 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_bin_location_bac
DROP TABLE IF EXISTS `tb_bin_location_bac`;
CREATE TABLE IF NOT EXISTS `tb_bin_location_bac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aisle` varchar(255) NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `location_type` enum('Pickface','Storage','Virtual Location') NOT NULL,
  `status` enum('AVAILABLE','OCCUPIED','VIRTUAL LOCATION') NOT NULL DEFAULT 'AVAILABLE',
  `item_code` varchar(255) NOT NULL,
  `warehouse` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `layer` varchar(50) NOT NULL DEFAULT '0',
  `columns` varchar(100) NOT NULL,
  `high` varchar(100) NOT NULL,
  `deep` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9775 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_bin_transfer_logs
DROP TABLE IF EXISTS `tb_bin_transfer_logs`;
CREATE TABLE IF NOT EXISTS `tb_bin_transfer_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lpn` varchar(255) NOT NULL,
  `old_location` varchar(255) NOT NULL,
  `new_location` varchar(255) NOT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_type` varchar(255) NOT NULL,
  `created_by` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_branches
DROP TABLE IF EXISTS `tb_branches`;
CREATE TABLE IF NOT EXISTS `tb_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL,
  `fds_1` varchar(255) NOT NULL,
  `fds_2` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_category
DROP TABLE IF EXISTS `tb_category`;
CREATE TABLE IF NOT EXISTS `tb_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_code` varchar(255) DEFAULT NULL,
  `category_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_checklist
DROP TABLE IF EXISTS `tb_checklist`;
CREATE TABLE IF NOT EXISTS `tb_checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pick_id` varchar(255) NOT NULL,
  `in_id` varchar(255) NOT NULL,
  `out_id` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `expiry` date NOT NULL,
  `bin_loc` varchar(255) NOT NULL,
  `lpn` varchar(255) NOT NULL,
  `status` enum('For Checking','Checked') NOT NULL DEFAULT 'For Checking',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_cluster
DROP TABLE IF EXISTS `tb_cluster`;
CREATE TABLE IF NOT EXISTS `tb_cluster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `new_clustering` varchar(255) NOT NULL,
  `fds_1st_cyc` varchar(255) NOT NULL,
  `fds_2nd_cyc` varchar(255) NOT NULL,
  `complete_add` varchar(255) NOT NULL,
  `code` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_counted_sheet
DROP TABLE IF EXISTS `tb_counted_sheet`;
CREATE TABLE IF NOT EXISTS `tb_counted_sheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `aisle` varchar(150) NOT NULL,
  `layer` varchar(255) NOT NULL,
  `counter_name` varchar(255) NOT NULL,
  `analyst_name` varchar(255) NOT NULL,
  `time_start` time NOT NULL,
  `time_finished` time NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `encoded_by` varchar(255) NOT NULL,
  `date_created` date NOT NULL,
  `transaction_time` varchar(255) NOT NULL,
  `lpn` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=772 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_count_sheet
DROP TABLE IF EXISTS `tb_count_sheet`;
CREATE TABLE IF NOT EXISTS `tb_count_sheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `aisle` varchar(255) NOT NULL,
  `layer` varchar(255) NOT NULL,
  `warehouse` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('Not Issued','Issued','Pre','First','Second','Validated','Third') NOT NULL DEFAULT 'Not Issued',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_destination
DROP TABLE IF EXISTS `tb_destination`;
CREATE TABLE IF NOT EXISTS `tb_destination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destination_code` varchar(255) NOT NULL,
  `destination_name` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL,
  `fds_1` varchar(255) NOT NULL,
  `fds_2` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `destination_address` varchar(255) NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_encoded
DROP TABLE IF EXISTS `tb_encoded`;
CREATE TABLE IF NOT EXISTS `tb_encoded` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(250) NOT NULL,
  `aisle` varchar(150) NOT NULL,
  `layer` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `date` varchar(250) NOT NULL,
  `created_by` varchar(250) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_form_details
DROP TABLE IF EXISTS `tb_form_details`;
CREATE TABLE IF NOT EXISTS `tb_form_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `source_loc` varchar(255) NOT NULL,
  `destination_loc` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `nature` enum('1','2','3','4') NOT NULL,
  `lpn` varchar(255) NOT NULL,
  `transaction_date` date NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_fullfillment
DROP TABLE IF EXISTS `tb_fullfillment`;
CREATE TABLE IF NOT EXISTS `tb_fullfillment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `lpn` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `fullfilled_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_generated_forms
DROP TABLE IF EXISTS `tb_generated_forms`;
CREATE TABLE IF NOT EXISTS `tb_generated_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `nature` enum('1','2','3','4') NOT NULL,
  `date_generated` date NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_inbound
DROP TABLE IF EXISTS `tb_inbound`;
CREATE TABLE IF NOT EXISTS `tb_inbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `transaction_type` varchar(225) NOT NULL,
  `vendor_code` varchar(255) NOT NULL,
  `destination_code` varchar(255) NOT NULL,
  `ETA` date NOT NULL,
  `truck_type` varchar(255) NOT NULL,
  `plate_no` varchar(255) NOT NULL,
  `loading_bay` varchar(255) NOT NULL,
  `time_slot` time NOT NULL,
  `time_arrived` time NOT NULL,
  `time_docked` time NOT NULL,
  `unloading_start` time NOT NULL,
  `unloading_end` time NOT NULL,
  `time_departed` time NOT NULL,
  `ATA` date NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `in_qty` decimal(10,2) NOT NULL,
  `qty_pcs` decimal(20,2) NOT NULL DEFAULT 0.00,
  `allocated_qty` decimal(10,2) NOT NULL,
  `dispatch_qty` decimal(20,2) NOT NULL DEFAULT 0.00,
  `expiry` date NOT NULL,
  `mfg` date NOT NULL,
  `lpn` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `bin_location` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `pg_status` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('0','1','2','3') NOT NULL,
  `system_transaction_date` varchar(255) NOT NULL DEFAULT '',
  `system_last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pallet_tag` varchar(255) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `initial_remarks` varchar(255) NOT NULL,
  `staging_lane` varchar(50) DEFAULT NULL,
  `ir_status` enum('No IR','With IR') NOT NULL DEFAULT 'No IR',
  `fullfillment_status` enum('Pending','Fullfilled') NOT NULL DEFAULT 'Pending',
  `checker_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_incident_report
DROP TABLE IF EXISTS `tb_incident_report`;
CREATE TABLE IF NOT EXISTS `tb_incident_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `source_ref_no` varchar(255) NOT NULL,
  `transaction_type` enum('inbound','outbound','transport','inventory','warehouse') NOT NULL,
  `source_document` varchar(100) DEFAULT NULL,
  `nature_of_ir` varchar(100) DEFAULT NULL,
  `ir_date` date DEFAULT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `batch_code` varchar(100) DEFAULT NULL,
  `qty` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `system_transaction` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `date_closed` date NOT NULL,
  `closed_by` varchar(50) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_inventory_adjustment
DROP TABLE IF EXISTS `tb_inventory_adjustment`;
CREATE TABLE IF NOT EXISTS `tb_inventory_adjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ia_ref` int(25) NOT NULL,
  `ab_id` varchar(255) DEFAULT NULL,
  `to_id` varchar(50) NOT NULL DEFAULT '',
  `lpn` varchar(50) NOT NULL,
  `sku_code` varchar(255) DEFAULT NULL,
  `qty_case` int(11) NOT NULL,
  `expiry` date NOT NULL,
  `bin_loc` varchar(100) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_issued
DROP TABLE IF EXISTS `tb_issued`;
CREATE TABLE IF NOT EXISTS `tb_issued` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `aisle` varchar(150) NOT NULL,
  `layer` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `date` varchar(250) NOT NULL,
  `created_by` varchar(250) NOT NULL,
  `status` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_items
DROP TABLE IF EXISTS `tb_items`;
CREATE TABLE IF NOT EXISTS `tb_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku_code` varchar(255) DEFAULT NULL,
  `sap_code` varchar(255) NOT NULL,
  `material_description` varchar(255) NOT NULL,
  `ranking` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `pack_size` varchar(255) NOT NULL,
  `weight_per_case` varchar(255) DEFAULT NULL,
  `cbm_per_case` varchar(255) DEFAULT NULL,
  `cold_storage` enum('yes','no') NOT NULL,
  `cp_value` varchar(255) NOT NULL,
  `case_per_tier` varchar(255) NOT NULL,
  `layer` varchar(255) NOT NULL,
  `top_load` varchar(255) NOT NULL,
  `case_per_pallet` varchar(255) NOT NULL,
  `pcs_per_pallet` varchar(255) NOT NULL,
  `shelf_life` int(255) DEFAULT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_lock_items
DROP TABLE IF EXISTS `tb_lock_items`;
CREATE TABLE IF NOT EXISTS `tb_lock_items` (
  `lock_id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_no` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `production_date` date NOT NULL,
  `unit` varchar(255) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `date_created` date NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_login_logs
DROP TABLE IF EXISTS `tb_login_logs`;
CREATE TABLE IF NOT EXISTS `tb_login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_outbound
DROP TABLE IF EXISTS `tb_outbound`;
CREATE TABLE IF NOT EXISTS `tb_outbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `destination_code` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `pack_size` decimal(20,2) NOT NULL DEFAULT 0.00,
  `qty_pcs` decimal(20,2) NOT NULL DEFAULT 0.00,
  `qty_box` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_weight` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_cbm` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `declared_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `truck_type` varchar(255) NOT NULL,
  `ship_date` date NOT NULL,
  `eta` date NOT NULL,
  `call_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `departed_time` time NOT NULL,
  `actual_dispatch` date NOT NULL,
  `picking_start` time NOT NULL,
  `picking_end` time NOT NULL,
  `checking_start` time NOT NULL,
  `checking_end` time NOT NULL,
  `validating_start` time NOT NULL,
  `validating_end` time NOT NULL,
  `loading_start` time NOT NULL,
  `loading_end` time NOT NULL,
  `picker` varchar(255) NOT NULL,
  `checker` varchar(255) NOT NULL,
  `validator` varchar(255) NOT NULL,
  `source_code` enum('9146','9143','9152','9149','9155','9161','9140','9167','9174','9171') NOT NULL DEFAULT '9167',
  `in_picklist` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `picklist_status` enum('NOT PRINTED') NOT NULL DEFAULT 'NOT PRINTED',
  `dr_status` enum('NOT PRINTED') NOT NULL DEFAULT 'NOT PRINTED',
  `staging_lane` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `trucker` varchar(255) NOT NULL,
  `status` enum('For Allocation and Pick','For Checking','For Validating','For DR printing') NOT NULL DEFAULT 'For Allocation and Pick',
  `system_last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `truck_allocation` varchar(255) NOT NULL,
  `plate_no` varchar(255) NOT NULL DEFAULT 'Please Update',
  `driver` varchar(255) NOT NULL DEFAULT 'Please Update',
  `helper` varchar(255) NOT NULL DEFAULT 'Please Update',
  `loading_status` varchar(255) NOT NULL DEFAULT 'Please Update',
  `allocation` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_outboun_incident_report
DROP TABLE IF EXISTS `tb_outboun_incident_report`;
CREATE TABLE IF NOT EXISTS `tb_outboun_incident_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_ref_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_type` enum('inbound','outbound','transport','inventory','warehouse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_document` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nature_of_ir` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ir_date` date DEFAULT NULL,
  `item_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `system_transaction` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Open','Closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Open',
  `date_closed` date NOT NULL,
  `closed_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_pallets
DROP TABLE IF EXISTS `tb_pallets`;
CREATE TABLE IF NOT EXISTS `tb_pallets` (
  `pallet_id` int(11) NOT NULL AUTO_INCREMENT,
  `pallet_code` varchar(100) NOT NULL,
  `status` enum('Available','In Used') NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`pallet_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100000 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_personnel
DROP TABLE IF EXISTS `tb_personnel`;
CREATE TABLE IF NOT EXISTS `tb_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `posistion` enum('SELECT','CHECKER','PICKER','VALIDATOR') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_picklist
DROP TABLE IF EXISTS `tb_picklist`;
CREATE TABLE IF NOT EXISTS `tb_picklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_id` varchar(255) NOT NULL,
  `out_id` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `expiry` date NOT NULL,
  `bin_loc` varchar(255) NOT NULL,
  `lp_status` enum('Picking','Checking','Validating','For DR') NOT NULL DEFAULT 'Picking',
  `date_created` date NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `status` enum('Fullfilled','Pending','Partially Fullfilled') NOT NULL DEFAULT 'Pending',
  `loc_status` enum('1','2') NOT NULL DEFAULT '1',
  `barcode` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_quarantine_items
DROP TABLE IF EXISTS `tb_quarantine_items`;
CREATE TABLE IF NOT EXISTS `tb_quarantine_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_id` varchar(255) NOT NULL,
  `source_ref_no` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `batch_code` varchar(255) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `date_receieved` date NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_receive
DROP TABLE IF EXISTS `tb_receive`;
CREATE TABLE IF NOT EXISTS `tb_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_id` varchar(255) NOT NULL,
  `ref_no` varchar(250) NOT NULL,
  `document_no` varchar(250) NOT NULL,
  `item_code` varchar(250) NOT NULL,
  `batch_no` varchar(250) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `expiry` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_replenishment
DROP TABLE IF EXISTS `tb_replenishment`;
CREATE TABLE IF NOT EXISTS `tb_replenishment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_id` varchar(255) NOT NULL,
  `source_loc` varchar(255) NOT NULL,
  `new_loc` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `replen_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_created` date NOT NULL,
  `scan_stats` enum('Yes','No') NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_return
DROP TABLE IF EXISTS `tb_return`;
CREATE TABLE IF NOT EXISTS `tb_return` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `dispatch_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `created_by` date NOT NULL,
  PRIMARY KEY (`return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_sloc
DROP TABLE IF EXISTS `tb_sloc`;
CREATE TABLE IF NOT EXISTS `tb_sloc` (
  `sloc_id` int(11) NOT NULL AUTO_INCREMENT,
  `sloc_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `status` enum('GOOD','LOCKED','QUARANTINE') DEFAULT NULL,
  `designation` enum('PF','ST') NOT NULL DEFAULT 'PF',
  PRIMARY KEY (`sloc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_source
DROP TABLE IF EXISTS `tb_source`;
CREATE TABLE IF NOT EXISTS `tb_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_code` varchar(255) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_email` varchar(50) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_transport
DROP TABLE IF EXISTS `tb_transport`;
CREATE TABLE IF NOT EXISTS `tb_transport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_ref` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `branch_received_date` date NOT NULL,
  `received_by` varchar(255) NOT NULL,
  `ir_ref_no` varchar(255) NOT NULL,
  `ir_remarks` varchar(255) NOT NULL,
  `rr_ref_no` varchar(255) NOT NULL,
  `truck_arrival` time NOT NULL,
  `branch_in` time NOT NULL,
  `branch_out` time NOT NULL,
  `fds_comp` varchar(255) NOT NULL,
  `window_comp` varchar(255) NOT NULL,
  `in_full` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_transport_allocation
DROP TABLE IF EXISTS `tb_transport_allocation`;
CREATE TABLE IF NOT EXISTS `tb_transport_allocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `ship_date` date NOT NULL,
  `eta` date NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `box` decimal(10,2) NOT NULL,
  `total_wieght` decimal(10,2) NOT NULL,
  `total_cbm` decimal(10,2) NOT NULL,
  `declared_value` decimal(10,2) NOT NULL,
  `co_load_ref_no` varchar(255) NOT NULL DEFAULT 'Please Update',
  `truck_type_loadplan` varchar(255) NOT NULL,
  `truck_type_actual` varchar(255) NOT NULL DEFAULT 'Please Update',
  `revised_rdd` date NOT NULL,
  `driver` varchar(255) NOT NULL DEFAULT 'Please Update',
  `courier` varchar(255) NOT NULL DEFAULT 'Please Update',
  `hauler` varchar(255) NOT NULL DEFAULT 'Please Update',
  `plate_no` varchar(255) NOT NULL DEFAULT 'Please Update',
  `call_time` time NOT NULL,
  `actual_truck_arrival` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `Status` varchar(255) DEFAULT 'Please Update',
  `loading_start` time NOT NULL,
  `loading_end` time NOT NULL,
  `time_of_dispatch` time NOT NULL,
  `validated_by` varchar(255) NOT NULL DEFAULT 'Please Update',
  `dispatch_by` varchar(255) NOT NULL DEFAULT 'Please Update',
  `branch_receipt_date` date NOT NULL,
  `Received_by` varchar(255) NOT NULL DEFAULT 'Please Update',
  `ir_ref_no` varchar(255) NOT NULL DEFAULT 'Please Update',
  `ir_remarks` varchar(255) NOT NULL DEFAULT 'Please Update',
  `rr_ref_no` varchar(255) NOT NULL DEFAULT 'Please Update',
  `truck_arrival` time NOT NULL,
  `branch_in` time NOT NULL,
  `branch_out` time NOT NULL,
  `fds_comp` varchar(255) NOT NULL DEFAULT 'Please Update',
  `window_comp` varchar(255) NOT NULL DEFAULT 'Please Update',
  `in_full` varchar(255) NOT NULL DEFAULT 'Please Update',
  `helper_2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_units
DROP TABLE IF EXISTS `tb_units`;
CREATE TABLE IF NOT EXISTS `tb_units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_code` varchar(255) DEFAULT NULL,
  `unit_short_name` varchar(100) NOT NULL,
  `unit_name` varchar(255) NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_users
DROP TABLE IF EXISTS `tb_users`;
CREATE TABLE IF NOT EXISTS `tb_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(200) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `user_type` enum('admin','inbound','outbound','inventory','transport','viewer','encoder','picker','inbound checker','outbound checker','validator','main guard','operator') NOT NULL,
  `user_status` enum('0','1','2') NOT NULL,
  `user_category` enum('0','1','2') NOT NULL,
  `name` varchar(200) NOT NULL,
  `user_contact_no` varchar(200) NOT NULL,
  `photo` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_validated
DROP TABLE IF EXISTS `tb_validated`;
CREATE TABLE IF NOT EXISTS `tb_validated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_id` int(200) NOT NULL,
  `pick_id` varchar(200) NOT NULL,
  `in_id` varchar(200) NOT NULL,
  `out_id` varchar(200) NOT NULL,
  `document_no` varchar(250) NOT NULL,
  `item_code` varchar(250) NOT NULL,
  `batch_no` varchar(250) NOT NULL,
  `item_description` varchar(250) NOT NULL,
  `qty_pcs` decimal(10,2) NOT NULL,
  `expiry` date NOT NULL,
  `bin_loc` varchar(250) NOT NULL,
  `lpn` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table siri_mdc_db2.tb_warehouse
DROP TABLE IF EXISTS `tb_warehouse`;
CREATE TABLE IF NOT EXISTS `tb_warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` varchar(255) NOT NULL,
  `warehouse_name` varchar(255) NOT NULL,
  `warehouse_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
