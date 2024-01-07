<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . "SteamAuth.class.php");
require_once(__DIR__ . DIRECTORY_SEPARATOR .".." . DIRECTORY_SEPARATOR . "config.php");

$AdminsList = require_once (__DIR__ . DIRECTORY_SEPARATOR  . ".." . DIRECTORY_SEPARATOR . "admins.php");
$Auth = new SteamAuth();
//$Auth = new SteamAuth("http://localhost/auth.php");

$Auth->SetOnLoginCallback(function($cid) use ($AdminsList) {
    return in_array($cid, $AdminsList);
    //_logFailedLogin($steamid);
});

$Auth->init();
