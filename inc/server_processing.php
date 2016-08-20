<?php

/*@license MIT - http://datatables.net/license_mit/
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	header("Location: ../index.php?error=".urlencode("Direct access not allowed."));
	die();
}

require_once 'app.php';


$current_con = $db->getCurrentDb();
$db_conn = array(
	'user' => $current_con['username'],
	'pass' => $current_con['password'],
	'db'   => $current_con['database_name'],
	'host' => $current_con['host'],
	'port' => $current_con['port']
);

$table = DB_TABLE_PA;

$where = getIpDatesSql(); # "global where" for all if's in here

# Total DB count from cache
$records = 0;
$database->query('SELECT COUNT(*) as count FROM `'.DB_TABLE_PA.'`');
$key = FileSystemCache::generateCacheKey(sha1(serialize(array($database->stmt(), $db))), 'SQL');
$records = FileSystemCache::retrieve($key);
if($records === false) {
	$records = $database->single();
	FileSystemCache::store($key, $records, 2000);
}
if(!empty($records)) {
	$records = $records['count'];
}

if (isset($_GET['type']) && $_GET['type'] == 'getconnections') {

	$primaryKey = 'id';

	$columns = array(
		array(
			'db'        => 'id',
			'dt'        => 'id'
		),
		array(
			'db'        => 'name',
			'dt'        => 'name',
			'formatter' => function( $d, $row ) {
				return htmlentities($d);
			}
		),
		array(
			'db'        => 'auth',
			'dt'        => 'auth'
		),
		array(
			'db'        => 'connect_time',
			'dt'        => 'connect_time',
			'formatter' => function( $d, $row ) {
				return date('h:i:s a m/d/Y',$d);
			}
		),
		array(
			'db'        => 'connect_method',
			'dt'        => 'connect_method',
			'formatter' => function( $d, $row ) {
				return ConnMethod($d);
			}
		),
		array(
			'db'        => 'duration',
			'dt'        => 'duration',
			'formatter' => function( $d, $row ) {
				return PlaytimeCon($d);
			}
		),
		array(
			'db'        => 'country',
			'dt'        => 'country'
		),
		array(
			'db'        => 'premium',
			'dt'        => 'premium'
		),
		array(
			'db'        => 'html_motd_disabled',
			'dt'        => 'html_motd_disabled'
		),
		array(
			'db'        => 'os',
			'dt'        => 'os'
		),
		array(
			'db'        => 'server_ip',
			'dt'        => 'server_ip'
		)
	);


	require('ssp.class.php');

	echo json_encode(
		SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition = '', $groupBy = '', $where, $records)
	);
}

if (isset($_GET['type']) && ($_GET['type'] == 'getplayers' || $_GET['type'] == 'getstaff')) {

	$primaryKey = 'id';
	#print_r($_GET); die;
	$columns = array(
		array(
			'db'        => 'id',
			'dt'        => 'id'
		),
		array(
			'db'        => 'name',
			'dt'        => 'name',
			'formatter' => function( $d, $row ) {
				return htmlentities($d);
			}
		),
		array(
			'db'        => 'auth',
			'dt'        => 'auth'
		),
		array(
			'db'        => 'SUM(duration)',
			'dt'        => 'duration',
			'formatter' => function( $d, $row ) {
				return PlaytimeCon($d);
			}
		),
		array(
			'db'        => 'COUNT(auth)',
			'dt'        => 'total',
			'as'        => 'total',
			'formatter' => function( $d, $row ) {
				return number_format($d);
			}
		),
		array(
			'db'        => 'MAX(connect_time)',
			'dt'        => 'connect_time',
			'as'        => 'connect_time',
			'formatter' => function( $d, $row ) {
				return date('h:i:s a m/d/Y',$d);
			}
		),
		array(
			'db'        => 'country',
			'dt'        => 'country'
		),
		array(
			'db'        => 'premium',
			'dt'        => 'premium'
		),
		array(
			'db'        => 'html_motd_disabled',
			'dt'        => 'html_motd_disabled'
		),
		array(
			'db'        => 'os',
			'dt'        => 'os'
		),
		array(
			'db'        => 'flags',
			'dt'        => 'flags',
			'formatter' => function( $d, $row ) use (&$staff_group_names) {
				return FlagToName($d, $staff_group_names);
			}
		),
	);

	$groupBy = "GROUP BY auth";


	if($_GET['type'] == 'getstaff') { # AND flags != "" AND (flags = 'z' OR flags = 'xy')
		if(!empty($where)) {
			$where .= " AND ";
		}
		$where .= 'flags != "" ';
		if(!empty($staff_whitelist)) {
			$where .= " AND (";
			foreach($staff_whitelist as $flag) {
				$where .= "flags = '".$flag."' OR ";
			}
			$where = rtrim(rtrim($where, " "), "OR");
			$where .= ")";
		}
		if(!empty($staff_blacklist)) {
			$where .= " AND (";
			foreach($staff_blacklist as $flag) {
				$where .= "flags != '".$flag."' AND ";
			}
			$where = rtrim(rtrim($where, " "), "AND");
			$where .= ")";
		}
		$groupBy = "GROUP BY auth, flags";
	}

	require('ssp.class.php');

	echo json_encode(
		SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition = '', $groupBy, $where, $records)
	);
}

if (isset($_GET['type']) && $_GET['type'] == 'getcountryinfo') {

	$primaryKey = 'id';

	$columns = array(
		array(
			'db'        => 'id',
			'dt'        => 'id'
		),
		array(
			'db'        => 'name',
			'dt'        => 'name',
			'formatter' => function( $d, $row ) {
				return htmlentities($d);
			}
		),
		array(
			'db'        => 'auth',
			'dt'        => 'auth'
		),
		array(
			'db'        => 'duration',
			'dt'        => 'duration',
			'formatter' => function( $d, $row ) {
				return PlaytimeCon($d);
			}
		),
		array(
			'db'        => 'premium',
			'dt'        => 'premium'
		),
		array(
			'db'        => 'html_motd_disabled',
			'dt'        => 'html_motd_disabled'
		),
		array(
			'db'        => 'os',
			'dt'        => 'os'
		),
		array(
			'db'        => 'server_ip',
			'dt'        => 'server_ip'
		)
	);

	$extraCondition = "`country_code` = '".$_GET['id']."'";
	$extraCondition .= " AND " . $where;
	require('ssp.class.php');

	echo json_encode(
		SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition, $groupBy = '', $where = '', $records)
	);
}

if (isset($_GET['type']) && $_GET['type'] == 'c') { // connections for single server View

	$primaryKey = 'id';

	$columns = array(
		array(
			'db'        => 'id',
			'dt'        => 'id'
		),
		array(
			'db'        => 'name',
			'dt'        => 'name',
			'formatter' => function( $d, $row ) {
				return htmlentities($d);
			}
		),
		array(
			'db'        => 'auth',
			'dt'        => 'auth'
		),
		array(
			'db'        => 'connect_time',
			'dt'        => 'connect_time',
			'formatter' => function( $d, $row ) {
				return date('h:i:s a m/d/Y',$d);
			}
		),
		array(
			'db'        => 'connect_method',
			'dt'        => 'connect_method',
			'formatter' => function( $d, $row ) {
				return ConnMethod($d);
			}
		),
		array(
			'db'        => 'duration',
			'dt'        => 'duration',
			'formatter' => function( $d, $row ) {
				return PlaytimeCon($d);
			}
		),
		array(
			'db'        => 'country',
			'dt'        => 'country'
		),
		array(
			'db'        => 'premium',
			'dt'        => 'premium'
		),
		array(
			'db'        => 'html_motd_disabled',
			'dt'        => 'html_motd_disabled'
		),
		array(
			'db'        => 'os',
			'dt'        => 'os'
		),
		array(
			'db'        => 'server_ip',
			'dt'        => 'server_ip'
		)
	);

	#$extraCondition = "`server_ip` = '".$_GET['server']."'";
	$extraCondition = $where;

	require('ssp.class.php');

	echo json_encode(
		SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition, $groupBy = '', $where = '', $records)
	);
}

if (isset($_GET['type']) && $_GET['type'] == 'u') { // Unique Players for single server View

	$primaryKey = 'id';
	$columns = array(
		array(
			'db'        => 'id',
			'dt'        => 'id'
		),
		array(
			'db'        => 'name',
			'dt'        => 'name',
			'formatter' => function( $d, $row ) {
				return htmlentities($d);
			}
		),
		array(
			'db'        => 'auth',
			'dt'        => 'auth'
		),
		array(
			'db'        => 'SUM(duration)',
			'dt'        => 'duration',
			'formatter' => function( $d, $row ) {
				return PlaytimeCon($d);
			}
		),
		array(
			'db'        => 'COUNT(auth)',
			'dt'        => 'total',
			'as'        => 'total',
			'formatter' => function( $d, $row ) {
				return number_format($d);
			}
		),
		array(
			'db'        => 'MAX(connect_time)',
			'dt'        => 'connect_time',
			'as'        => 'connect_time',
			'formatter' => function( $d, $row ) {
				return date('h:i:s a m/d/Y',$d);
			}
		),
		array(
			'db'        => 'country',
			'dt'        => 'country'
		),
		array(
			'db'        => 'premium',
			'dt'        => 'premium'
		),
		array(
			'db'        => 'html_motd_disabled',
			'dt'        => 'html_motd_disabled'
		),
		array(
			'db'        => 'os',
			'dt'        => 'os'
		),
		array(
			'db'        => 'server_ip',
			'dt'        => 'server_ip'
		)
	);

	$groupBy = "GROUP BY auth";

	require('ssp.class.php');

	echo json_encode(
		SSP::simple( $_GET, $db_conn, $table, $primaryKey, $columns, $joinQuery = '', $extraCondition = '', $groupBy, $where, $records)
	);
}
