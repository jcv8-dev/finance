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
                <img src="assets/img/stonks.bmp" class="img-fluid rounded shadow-box-sm" style="width: 100%;">
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
        function colorizeTable(id, aufsteigend) {
            $("#"+id+" tr").each(function() {
                let columnValues = [];
                $(this).find("td").each(function() {
                    let valueWithSymbol = $(this).text();
                    let numericValue = parseFloat(valueWithSymbol.replace(/[^0-9.-]+/g,""))/100;
                    columnValues.push(numericValue);
                });

                let min = Math.min(...columnValues);
                let max = Math.max(...columnValues);

                $(this).find("td").each(function(cellIndex) {
                    let valueWithSymbol = $(this).text();
                    let numericValue = parseFloat(valueWithSymbol.replace(/[^0-9.-]+/g,""))/100;
                    let relativeValue = (numericValue - min) / (max - min);
                    let red = Math.floor(255 * (1 - relativeValue)* 1.9 + 80);
                    let green = Math.floor(255 * relativeValue * 1.5 + 10);
                    if(numericValue > 0){
                        console.log("min: "+min)
                        console.log("max: "+max)
                        console.log("relativeValue: "+relativeValue)
                        console.log("numericValue: "+numericValue)
                        // console.log("red: "+red)
                        // console.log("green: "+green)
                    }



                    if(!aufsteigend){
                        let temp = red
                        red = green
                        green = temp
                    }
                    if(numericValue !== 0){
                        $(this).css("background-color", `rgba(${red}, ${green}, 20, 0.7)`);
                    }

                    //TODO fix colors
                });
            });
        }


    </script>
</html>
