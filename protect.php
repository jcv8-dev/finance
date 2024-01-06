<?php
if(session_status()!=PHP_SESSION_ACTIVE){
    session_start();
}


if (isset($_POST["logout"])) {
    session_destroy();
    unset($_SESSION);
}

if (!isset($_SESSION["user"])) {
    header("Location: login");
    exit();
}