<?php
require "check.php";
require "assets/scripts/helper.php";

if (isset($failed)) {
    echo '<div id="login-bad">Invalid user or password.</div>';
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
                <input class="col-10 col-sm-6 col-lg-3 mx-auto" type="text" name="user" placeholder="Username" required>
            </div>
            <div class="row pb-2">
                <input class="col-10 col-sm-6 col-lg-3 mx-auto" type="password" name="password" placeholder="Password" required>
            </div>
            <div class="row pb-2">
                <input class="col-10 col-sm-6 col-lg-3 mx-auto" type="submit" value="Sign In">
            </div>
        </div>

    </form>
</div>
</body>
</html>

