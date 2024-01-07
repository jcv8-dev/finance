<?php
//require "assets/scripts/helper.php";
require "assets/scripts/secrets.php";
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

if (!isset($_SESSION["user"]) && !password_verify(array_key_first($secret["loginPass"]),$_COOKIE["userHash"])) {
    header("Location: login");
    exit();
}