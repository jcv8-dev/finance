<?php
//require "assets/scripts/helper.php";
require_once "assets/scripts/secrets.php";
global $secret;

if(session_status()!=PHP_SESSION_ACTIVE){
    session_start();
}


if (isset($_POST["logout"])) {
    unset($_COOKIE['userHash']);
    setcookie('userHash', '', -1, '/');
    session_destroy();
    unset($_SESSION);
}

$loginCookie = $_COOKIE["userHash"] ?? "not_set";
$session_valid = isset($_SESSION["user"]);
$cookie_valid = password_verify(array_key_first($secret["loginPass"]),$loginCookie);

if (!$session_valid && !$cookie_valid){
    if(!$cookie_valid){
        redirLogin("cookie");
    }
    if(!$session_valid){
        redirLogin("session");
    }
}

function redirLogin($reason){
    header("Location: login?$reason");
    exit();
}

if($cookie_valid){
    $auth = "Cookie";
}

if($session_valid){
    $auth = "Session";
}