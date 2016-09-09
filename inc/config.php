<?php

# Player Analytics config file
# If you have suggestions or you found a bug -> contact me: thechaoscoder+player-analytics[at]gmail.com (replace [at] with @, bot protection)
# or open an issue here https://github.com/theChaosCoder/player_analytics



//Set encoding
ini_set('default_charset', 'utf-8');

$Title = "Player Analytics";
$Show_Max_Countries = 10; # Top 10 Countries
$hide_inactive_servers_days = 0; # Hide servers that are 'inactive' since X Days

const STEAM_APIKEY  = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; # add your key (optional)

# optional - replace ip with your server name
$server_names = [
    "your_ip:port"  => "Server name",
    "2.2.2.2"       => "TRADE & IDLE SERVER",
    "3.3.3.3:20715" => "WaffleTown",
    "4.4.4.4"       => "Chew Chew Train - 24/7 ChewChew",
];

# A name that will appears in nav below the ip
# Only usefull if you don't want to use server_names
$server_sub_names = [
    "your_ip:port"  => "Server name",
    "2.2.2.2"       => "TRADE & IDLE SERVER",
    "3.3.3.3:20715" => "WaffleTown",
];

# Replace flags like z, bce with a name like VIP, Admin etc.
$staff_group_names = [
    #"z"   => "Super Admin (z)",
    #"bce"  => "VIP (bce)",
];


# Show only records with the following flags:
$staff_whitelist = [
    #"z",
    #"abc",
];
# You can not combine white and black list!
# Hide records with the following flags:
$staff_blacklist = [
    #"z",
    #"abc",
];
