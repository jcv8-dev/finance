<?php

if(!file_exists(__DIR__."/assets/scripts/upload/secrets.php")){
    header("Location: setup");
}

require "check.php";
require_once "assets/scripts/helper.php";

function loginAlert(){
    global $failed;
    if(isset($_GET["cookie"])) $reason = "The Cookie has expired";
    if(isset($_GET["session"])) $reason = "The Session has expired";
    if(isset($failed) && $failed) $reason = "Invalid credentials";

    if(isset($reason)){
        echo "<div class=\"alert alert-warning alert-dismissible fade show col-10 col-sm-6 col-lg-3 mx-auto border rounded\" role=\"alert\">
          $reason
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
    }
}

?>
<html>
<head>
    <?php head("Finance - Login"); ?>
</head>
<body>
<div class="container">
    <form id="login-form" method="post" target="_self">
        <div class="row">
            <h1 class="py-2 px-4 col-10 pr prh w-fit-content mx-auto">One of the Tools of all Time</h1>
        </div>
        <div class="container">
            <div class="row pb-2">
                <input style="height: 38px" class="col-10 col-sm-6 col-lg-3 mx-auto border rounded" type="text" name="user" placeholder="Username" required>
            </div>
            <div class="row pb-2">
                <input style="height: 38px" class="col-10 col-sm-6 col-lg-3 mx-auto border rounded" type="password" name="password" placeholder="Password" required>
            </div>
            <div class="row pb-2">
                <input class="btn btn-outline-primary col-10 col-sm-6 col-lg-3 mx-auto" type="submit" value="Sign In">
            </div>
        </div>
    </form>
    <?php loginAlert(); ?>
</div>
</body>
</html>

