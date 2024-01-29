<?php
if(!file_exists("assets/scripts/upload/secrets.php")){
//    echo "db redir setup";
    header("Location: setup");
}
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