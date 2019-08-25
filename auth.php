<?php
require_once (__DIR__ . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "auth.php");

$action = $_GET["action"];

switch ($action){
    case "login":
        Login($Auth);
        break;

    case "logout":
        Logout($Auth);
        break;
}

function Login(SteamAuth $auth){
    if( !$auth->IsUserLoggedIn() ) {
        $auth->RedirectLogin();
        exit;
    }
    header("Location: ./index.php");
}

function Logout(SteamAuth $auth){
    if($auth->IsUserLoggedIn())
        $auth->Logout();
    echo "Logged out successfully. ";
}