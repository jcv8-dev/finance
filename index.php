<?php
require 'assets/scripts/helper.php';
?>

<!doctype html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <title>Finance - Übersicht</title>
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <?php printHeader("/finance"); ?>
        <div class="container px-2">
            <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <img src="assets/img/stonks.bmp" class="img-fluid rounded shadow-box-sm" style="width: 100%;">
            </div>
            <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <input type="file" accept="image/*" capture="camera">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>
