<?php 

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
    die();
}

require_once 'app.php';

// if (!isset($_GET['id'])) {
// 	$_GET['id'] = date("Y-m-d", strtotime('-7 day')).",".date("Y-m-d");
// }

$dates = Util::getCookieJson("dates");
#pr($dates); die;


if ($dates['start'] == $dates['end']) {
	$database->query('SELECT `connect_date` AS d, DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%l:00 %p") AS time, COUNT(*) AS total FROM `'.DB_TABLE_PA.'` '.getIpDatesSql($include_where = true).' GROUP BY DATE_FORMAT(FROM_UNIXTIME(`connect_time`), "%H")');
	$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
	$connections = FileSystemCache::retrieve($key);
	if($connections === false) {
		$connections = $database->resultset();
		FileSystemCache::store($key, $connections, 1000);
	}
	
}
else {
	$database->query('SELECT `connect_date` AS d, `connect_date` AS time, COUNT(*) AS total FROM `'.DB_TABLE_PA.'` '.getIpDatesSql($include_where = true).' GROUP BY `connect_date`');
	$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
	$connections = FileSystemCache::retrieve($key);
	if($connections === false) {
		$connections = $database->resultset();
		FileSystemCache::store($key, $connections, 1000);
	}
}

print_r(json_encode($connections));
