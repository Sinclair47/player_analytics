<?php

//Set encoding
ini_set('default_charset', 'utf-8');

//Database Info
define("DB_HOST",  'localhost');
define("DB_USER",  'USER');
define("DB_PASS",  'PASSWORD');
define("DB_NAME",  'DATABASE');
define("DB_PORT",  '3306');

$Home = "/";
$Title = "Title";
$Show_Max_Countries = 10; # Top 10 Countries

const STEAM_APIKEY  = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

#optional - replace ip with your server name
$server_names = array(
    "your_ip:port"  => "Server name",
    "2.2.2.2"       => "TRADE & IDLE SERVER",
    "3.3.3.3:20715" => "WaffleTown",
    "4.4.4.4"       => "Chew Chew Train - 24/7 ChewChew",
    "5.5.5.5"       => "Black Server",
    
  );



## DO NOT TOUCH ANYTHING BELOW HERE ! ##

function ServerName($key, $server_names)
{
  if (array_key_exists($key, $server_names)) {
    return $server_names[$key];
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

  $data = file_get_contents($url);
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
