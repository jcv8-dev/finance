<?php
if(!file_exists(__DIR__."/upload/secrets.php")){
    header("Location: setup");
}
require_once "upload/secrets.php";
function db(){
    global $secret;

    $servername = $secret["dbServer"];
    $username = $secret["dbUser"];
    $password = $secret["dbPass"];
    $dbname = $secret["dbName"];

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function readCookie($key){
    if(isset($_COOKIE[$key])){
        return $_COOKIE[$key];
    }
    return "";
}