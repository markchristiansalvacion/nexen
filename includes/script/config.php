<?php


/**
 * Database Name:
 * pcdsi_wms
 */
 if ($_SERVER['SERVER_ADDR'] == '194.163.32.64') { //HOSTINGER 194.163.32.64
	define('DB_HOST', 'localhost');          // Set database host
	define('DB_USER', 'u478425112_mcbs_nexen');   // Set database user
	define('DB_PASS', 'AGLsmark689');         // Set database password
	define('DB_NAME', 'u478425112_nexen'); // Set database name
} else {

	define('DB_HOST', 'localhost');          // Set database host
	define('DB_USER', 'root');               // Set database user
	define('DB_PASS', '');                   // Set database password
	define('DB_NAME', 'local_nexen');        // Set database name
}


