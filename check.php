<?php

session_start();

require_once __DIR__."/assets/scripts/upload/secrets.php";

global $secret;
$failed = false;

if (isset($_POST["user"]) && !isset($_SESSION["user"])) {
    $users = $secret["loginPass"];


    // Check credentials
    if (isset($users[$_POST["user"]]) && $users[$_POST["user"]] == $_POST["password"]) {
        $_SESSION["user"] = $_POST["user"];
        $userHash = password_hash($_POST["user"], PASSWORD_DEFAULT);
        setcookie("userHash",$userHash,time()+$secret["loginRetentionSeconds"]);
    }

    // failed login flag
    if (!isset($_SESSION["user"])) { $failed = true; }
}



if (isset($_SESSION["user"]) || password_verify($secret["loginPass"][0],$_COOKIE["userHash"])) {
    header("Location: /index");
    exit();
}