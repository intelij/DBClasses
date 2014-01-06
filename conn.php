<?php
require('constants.php');  
//database connection
$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!isset($connection)) {
	echo "Database connection error" . mysql_error();
}
$db_select = mysql_select_db(DB_NAME, $connection);

?>