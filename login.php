<?php
require "check.php";

if (isset($failed)) {
    echo '<div id="login-bad">Invalid user or password.</div>';
}

?>
<form id="login-form" method="post" target="_self">
    <h1>One of the Tools of all Time</h1>
    <label for="user">User</label>
    <input type="text" name="user" required>
    <label for="password">Password</label>
    <input type="password" name="password" required>
    <input type="submit" value="Sign In">
</form>
