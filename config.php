<?php // config.php
//define('URLROOT', 'https://taymac.net/'); 
if (!defined('URLROOT')) {
    define('URLROOT', 'http://beauty-spa.local/');
}


date_default_timezone_set('UTC');
$mysqli = new mysqli('localhost', 'root', 'root', 'beauty-spa');


if ($mysqli->connect_errno) {
    echo "cannot connect MYSQLI error no{$mysqli->connect_errno}:{$mysqli->connect_errno}";
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

