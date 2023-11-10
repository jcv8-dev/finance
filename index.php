<?php
require 'assets/scripts/helper.php';
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
?>

<!doctype html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <title>Finance - Übersicht</title>
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <?php printHeader("/finance"); ?>
        <div class="container px-2 mb-4">
            <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <img src="assets/img/stonks.bmp" class="img-fluid rounded shadow-box-sm" style="width: 100%;">
            </div>
            <div>
                <?php monthlyCategory(true, $id="einnahmenMonatTable")?>
            </div>
            <div>
                <?php monthlyCategory(false, $id="ausgabenMonatTable")?>
            </div>
            <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <input type="file" accept="image/*" capture="camera">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
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
