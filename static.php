<?php

// Start measuring execution time
$startTime = microtime(true);

require 'assets/scripts/helper.php';
require 'protect.php';

header("Cache-Control: no-cache, must-revalidate");
?>
<!doctype html>
<html lang="de">
<?php head("Finance - Static"); ?>
    <body>
        <?php printHeader("static"); ?>
        <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">Hello World!<br>
        <?php

        // Stop measuring execution time
        $endTime = microtime(true);

        // Calculate the execution time
        $executionTime = ($endTime - $startTime);

        // Output the execution time
        echo "PHP Execution Time: {$executionTime} seconds</div>";
        ?>
    </body>
    </html
