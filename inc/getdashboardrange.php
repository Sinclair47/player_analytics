<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

//Database Info
include 'config.php';

// Include database class
include 'database.class.php';

if (!isset($_GET['id'])) {
	$_GET['id'] = date("Y-m-d", strtotime('-7 day')).",".date("Y-m-d");
}

$date = explode(",", $_GET['id']);

// Instantiate database.
$database = new Database();

if ($date[0] == $date[1]) {
	$database->query('SELECT `connect_date` AS d, DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%l:00 %p") AS time, COUNT(`auth`) AS total FROM `player_analytics` WHERE `connect_date` BETWEEN  :start AND :end GROUP BY DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%H")');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$connections = $database->resultset();
}
else {
	$database->query('SELECT `connect_date` AS d, `connect_date` AS time, COUNT(`auth`) AS total FROM `player_analytics` WHERE `connect_date` BETWEEN  :start AND :end GROUP BY `connect_date`');
	$database->bind(':start', $date[0]);
	$database->bind(':end', $date[1]);
	$connections = $database->resultset();
}

print_r(json_encode($connections));

?>
