<?php
    /*Database credentials*/
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'daywolf');
    define('DB_PASSWORD', 'DogeWow77');
    define('DB_NAME', 'messages');

    /*connect to the MySQL database if possible*/
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if($mysqli === false)
    {
        die("ERROR: Could not connect. " . $mysqli->connect_error);
    }
?>