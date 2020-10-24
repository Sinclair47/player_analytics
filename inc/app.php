<?php

define("DB_TABLE_PA", "player_analytics");
#define("DB_TABLE_PA", "pax");

$dir = dirname(realpath(__FILE__));
require_once $dir . '/config.php';
if(!is_file($dir . '/config_db.php')) {
	echo "<h2>You need to rename your config_db_RENAME_ME.php file fo config_db.php in /inc</h2>";
	die;
}
require_once $dir . '/config_db.php';

require_once $dir . '/util.php';
require_once $dir . '/FileSystemCache.php';
require_once $dir . '/database.class.php';

#quick simple check
if(!is_array($server_names) || !is_array($staff_group_names) || !is_array($staff_group_names) || !is_array($databases)) {
	die("You have an error in your config");
}
if(!empty($staff_whitelist) && !empty($staff_blacklist)) {
	die("<h2>You can not combine white and black list!</h2>");
}
if(!is_numeric($hide_inactive_servers_days)) {
	die("<h2>Your inactive servers var is incorrect. Use numbers like 7!</h2>");
}

$db = new DbManager($databases);
#$database = new medoo($db->getCurrentDb());
#pr($db->getCurrentDb()); die;
$database = new Database($db->getCurrentDb());

FileSystemCache::$cacheDir = $dir.'/../cache';

/**
 * 
 */
class DbManager
{
	protected $current;
	protected $count;
	private $databases;

	function __construct($databases)
	{
		$this->current = 0; # first db aka default
		$this->count = count($databases);
		$this->databases = $databases;
	}

	
	public function getCurrentDb() {
		$db_index = Util::getCookie("db");
		#pr($current_db); die;
		if(is_numeric($db_index)) {
			$this->current = $db_index;
		}
		return $this->databases[$this->current];
	}

	// function setCurrentDb($index = null) { #TODO COOKIE!
	//     if(isset($index) && array_key_exists($index, $databases)) {
	//         $this->current = $this->database[$index];
	//     }        
	// }

	public function getDbIndices() {
		return array_keys($this->databases);
	}

	public function getCurrentDbIndex() {
		return $this->current;
	}

	public function getDbName($index) {
		return $this->databases[$index]['friendlyName'];
	}

	public function count() {
		return $this->count;
	}
} # DbManager end


/**
*	returns ip and dates (from cookies) in sql format
*/
function getIpDatesSql($include_sql_where = false) {
	$where = "";
	if($include_sql_where) {
		$where = "WHERE ";
	}

	$ips =  getServerIpsSql();
	$dates =  getDateRangeSql();

	if(!empty($ips)) {
		$where .= $ips;
		if(!empty($dates)) {
			$where .=  " AND " . $dates;
		}
		return $where;
	} else {
		if(!empty($dates)) {
			$where .= $dates;
			return $where;
		}
	}

	return "";
}

/**
* Returns a sql condition for server_ip
* server_ip IN (values)
*
*/
function getServerIpsSql($include_sql_where = false, $pre = '', $post = '') {
	$server_ips = Util::getCookieJson("server");
	$where = "";
	
	
	if($include_sql_where) {
		$where .= "WHERE ";
	}
	if(!empty($pre)) {
		$where .= " ".$pre." ";
	}

	if(isset($server_ips)) {
		$where .= " server_ip IN (";
		foreach($server_ips as $ip) {
			$where .= "'".ms_escape_string($ip)."',";
		}
		$where = rtrim($where, ",");
		$where .= ") ";
		
		if(!empty($post)) {
			$where .= $post." ";
		}
		return $where;          
	}
	return "";
}


/**
* Returns a sql condition for connect_date
* connect_date BETWEEN date_start AND date_end
*
*/
function getDateRangeSql($include_sql_where = false) {
	$dates = Util::getCookieJson("dates");
	if(!array($dates)) {
		return "";
	}
	$where = "";
	
	if($include_sql_where) {
		$where = "WHERE ";
	}

	if(isset($dates)) {
		$where .= " `connect_date` BETWEEN";
		$where .= " '". ms_escape_string($dates['start']) . "' AND '" . ms_escape_string($dates['end']). "' ";            
		return $where;          
	}
	return "";
}

function ms_escape_string($data) {
	if ( !isset($data) or empty($data) ) return '';
	if ( is_numeric($data) ) return $data;

	$non_displayables = array(
		'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/',             // url encoded 16-31
		'/[\x00-\x08]/',            // 00-08
		'/\x0b/',                   // 11
		'/\x0c/',                   // 12
		'/[\x0e-\x1f]/'             // 14-31
	);
	foreach ( $non_displayables as $regex )
		$data = preg_replace( $regex, '', $data );
	$data = str_replace("'", "''", $data );
	return $data;
}

// function ReadCacher($cache_key = null, $data = null, $cache_time = 600, $group = '') {
// 	$key = FileSystemCache::generateCacheKey(sha1(array($cache_key)), $group);
// 	$data = FileSystemCache::retrieve($key);
// 	if($data === false) {
// 		$data = $database->single();
// 		FileSystemCache::store($key, $data, $cache_time);
// 	}
// }
function ServerName($key, $server_names)
{
	if (array_key_exists($key, $server_names)) {
		return $server_names[$key];
	}
	return $key;
}

function KeyToValue($key, $value, $return_empty = false) {
	if (array_key_exists($key, $value)) {
		return $value[$key];
	}
	if($return_empty) {
		return "";
	}
	return $key;
}

function SteamTo64($key) 
{ 
	if (preg_match('/^\[U:[0-9]:([0-9]+)\]$/', $key, $matches)) {
		$key = '7656'.(1197960265728 + $matches[1]);
		return $key;
	}
	else {
		$key = '7656'.(((substr($key, 10)) * 2) + 1197960265728 + (substr($key, 8, 1)));
		return $key;
	}
}

function ToSteam64($key) 
{
	$key = ((substr($key, 4) - 1197960265728) / 2);
	if(strpos( $key, "." )) {$int = 1;}
	else{$int = 0;}
	$key = 'STEAM_0:'.$int.':'.floor($key);
	return $key; 
}

function GetPlayerInformation($key)
{
	$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".STEAM_APIKEY."&steamids=".$key."&format=json";

	$data = @file_get_contents($url);
	$information = json_decode($data, true);

	return $information['response']['players'][0];
}

function StatCon($key,$lock)
{
	if ($lock == 0) {
		return "$key";
	}
	elseif ($key == 0) {
		return "0";
	}
	else {
		return round("$key"/"$lock", 2);
	}
}

function PlaytimeCon($seconds)
{
	return floor($seconds/3600).gmdate(':i:s', $seconds);
}

function PlaytimeConDashboard($seconds) {
	return "deactivated";
	#echo $seconds."<br>";
	$date_format = '%aD %H:%ih';
	if($seconds > 5184000) {# 2 months
		$date_format = '%mM %dD %H:%ih';
	}
	if($seconds > 31536000) {# 1 Year
		$date_format = '%yY %mM %H:%ih';
	}
	$dtF = new \DateTime('@0');
	$dtT = new \DateTime("@$seconds");
	return $dtF->diff($dtT)->format($date_format);
}

function FlagToName($flags, $staff_group_names) {
	if (array_key_exists($flags, $staff_group_names)) {
		return $staff_group_names[$flags];
	}
	return $flags;
}

function ConnMethod($key) 
{
	if (preg_match("/quickplay/", $key)) {
		$key = 'quickplay';
	}
	if (preg_match("/quickpick/", $key)) {
		$key = 'quickpick';
	}

	$ConnMethod = array("steam" => "SteamURL","serverbrowser_history" => "History","serverbrowser_favorites" => "Favorites","serverbrowser_internet" => "Internet","quickplay" => "Quickplay","quickpick" => "Quickpick","serverbrowser_lan" => "Lan","serverbrowser_friends" => "Friends","matchmaking" => "Matchmaking","redirect" => "Redirect","" => "Console");

	if (isset($ConnMethod[$key])) {
		return $ConnMethod[$key];
	}
	return $key;
}
