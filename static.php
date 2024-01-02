<?php
require 'assets/scripts/helper.php';
require 'protect.php';
header("Cache-Control: no-cache, must-revalidate");
$startTime = microtime(true);
?>
<!doctype html>
<html lang="de">
<?php head("Finance - Static"); ?>
    <body>
        <?php printHeader("static"); ?>
        <div class="container py-1 px-3 mt-2 border rounded shadow-box">Hello World!</div>
    </body>
<?php
printFooter($startTime);
?>
    </html
