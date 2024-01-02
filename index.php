<?php
require 'assets/scripts/helper.php';
require 'protect.php';
header("Cache-Control: no-cache, must-revalidate");
$startTime = microtime(true);
?>

<!doctype html>
<html lang="de">
    <?php head("Finance"); ?>
    <body>
        <?php printHeader("index"); ?>
        <div class="container-xl px-2 mb-4">
            <div class="container-xl py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <?php printMonthlyBudget(); ?>
            </div>
            <?php monthlyCategory(true, $id="einnahmenMonatTable")?>
            <?php monthlyCategory(false, $id="ausgabenMonatTable")?>
            <div class="container-xl py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <input type="file" accept="image/*" capture="camera">
            </div>
            <div class="container-xl py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <img src="assets/img/stonks.bmp" alt="stonks" class="img-fluid rounded shadow-box-sm" style="width: 100%;">
            </div>
        </div>
    </body>
    <?php
    printFooter($startTime);
    ?>
    <script src="assets/scripts/scripts.js"></script>
    <script>
        $(document).ready(function() {
            colorizeTable("einnahmenMonatTable", true);
            colorizeTable("ausgabenMonatTable", false);
        });
    </script>
</html>
