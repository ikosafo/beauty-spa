<?php // config.php
//define('URLROOT', 'https://taymac.net/'); 
if (!defined('URLROOT')) {
    define('URLROOT', 'http://beauty-spa.local/');
}

define('WA_VERSION', 'v22.0');                          // From your curl command
define('WA_PHONE_NUMBER_ID', '892625530596718');       // From "Phone number ID"
define('WA_ACCESS_TOKEN', 'EAAQh24ntseoBP8sX16zZBvDMcGJDM4fKDnZCG4X4PxEdWzOg9l9d9zDY69ZCBQM4MbwVlhiUBWFypcp1CoViHrY9DqTtsZC8o8f6i4SjuSyRpNobX8LuznDeYx8HpoFicBArEzPr9qplMBlyHqf0QLXXrdvzHq4FRihayYQAEQe1gD3quRStUQ1NTCBzFRoO9pvOer27NBy7jdVDZCgBuUIs9AoO5BWupPsSJEW7lZAByOuZCLe6INFHM61DH9hqSkcy9PEU8LvbvpPGB4iKqZCZA');
define('WA_TO_NUMBER', '233557824143');


date_default_timezone_set('UTC');
$mysqli = new mysqli('localhost', 'root', 'root', 'beauty-spa');


if ($mysqli->connect_errno) {
    echo "cannot connect MYSQLI error no{$mysqli->connect_errno}:{$mysqli->connect_errno}";
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

