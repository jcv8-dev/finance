<?php
session_start();
require "assets/scripts/secrets.php";
global $secret;

if (isset($_POST["user"]) && !isset($_SESSION["user"])) {
    $users = $secret["loginPass"];

    // (B2) CHECK & VERIFY
    if (isset($users[$_POST["user"]]) && $users[$_POST["user"]] == $_POST["password"]) {
        $_SESSION["user"] = $_POST["user"];
    }

    // (B3) FAILED LOGIN FLAG
    if (!isset($_SESSION["user"])) { $failed = true; }
}

// (C) REDIRECT TO HOME PAGE IF SIGNED IN - SET YOUR OWN !
if (isset($_SESSION["user"])) {
    header("Location: index");
    exit();
}