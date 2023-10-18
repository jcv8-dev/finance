<?php 
require 'assets/scripts/helper.php';
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
?>

<!doctype html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-control" content="no-cache">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>Finance - Konten</title>
    <link rel="stylesheet" href="assets/styles.css">
  </head>
  <body>
    <?php printHeader("konten"); ?>
    <div class="container px-2">
      <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
        <div class="row">
          <?php kontoCards(); ?>
        </div>
      </div>
      <div class="container py-2 px-3 mt-2 border rounded border-dark-subtle shadow-box">
        <form>
          <div class="row">
            <div class="col-md-3 col-6 pb-2 px-1">
              <input type="date" id="datePicker" class="form-control shadow-box-sm" aria-label="Date">
            </div>
            <div class="col-md-3 col-6 pb-2 px-1">
              <input type="text" class="form-control shadow-box-sm" placeholder="Betrag" aria-label="Betrag">
            </div>
            <div class="col-md-3 col-6 pb-2 px-1">
              <?php selectKonto("Von"); ?>
            </div>
            <div class="col-md-3 col-6 pb-2 px-1">
              <?php selectKonto("Nach"); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-9 col-sm-8 px-1"></div>
            <div class="col-md-3 col-sm-4 px-1">
              <button type="submit" class="btn btn-outline-primary w-100 shadow-box-sm">Übertrag buchen</button>
            </div>
          </div>
        </form>
      </div>
      <div class="container-sm py-2 px-3 mt-2 border rounded border-dark-subtle shadow-box">
        <div class="container p-0 overflow-x-auto">
          <table class="table table-striped">
              <thead>
              <tr>
                  <th scope="col">#</th>
                  <th scope="col">Datum</th>
                  <th scope="col">Betrag</th>
                  <th scope="col">Von</th>
                  <th scope="col">Nach</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                  <th scope="row">1</th>
                  <td>01.01.2024</td>
                  <td>123,25€</td>
                  <td>Girokonto</td>
                  <td>Bar</td>
              </tr>
              <tr>
                  <th scope="row">2</th>
                  <td>02.01.2024</td>
                  <td>135.456,55€</td>
                  <td>Bar</td>
                  <td>Kreditkarte</td>
              </tr>
              <tr>
                  <th scope="row">3</th>
                  <td>03.01.2024</td>
                  <td>6,90€</td>
                  <td>Kreditkarte</td>
                  <td>Girokonto 2</td>
              </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
  <script>
    $(document).ready(function() {
        document.getElementById('datePicker').valueAsDate = new Date();
    });
    
  </script>
</html>
