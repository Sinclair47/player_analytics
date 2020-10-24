<?php

require_once 'util.php';

#set ip
if(isset($_GET['server'])) { #server should be ip:port
    Util::setCookie("server", $_GET['server']);
}

#get ip
if(isset($_GET['get_server_ip'])) {
    return Util::getCookie("server");
}