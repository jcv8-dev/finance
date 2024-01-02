<?php
require 'assets/scripts/helper.php';
require 'protect.php';
header("Cache-Control: no-cache, must-revalidate");
$startTime = microtime(true);
?>

<!doctype html>
<html lang="de">
  <?php head("Finance - Konten"); ?>
  <body>
    <?php printHeader("konten"); ?>
    <div class="container px-2">
      <div class="container py-1 px-3 mt-2 border rounded shadow-box">
        <div class="row">
          <?php kontoCards(); ?>
        </div>
      </div>
      <div class="container py-2 px-3 mt-2 border rounded shadow-box">
        <form id="uebertragForm">
          <div class="row">
            <div class="col-md-3 col-6 pb-2 px-1">
              <input type="date" id="uebertragFormDate" class="form-control shadow-box-sm" aria-label="Date">
            </div>
            <div class="col-md-3 col-6 pb-2 px-1">
              <input type="text" id="uebertragFormBetrag" class="form-control shadow-box-sm" placeholder="Betrag" aria-label="Betrag">
            </div>
            <div class="col-md-3 col-6 pb-2 px-1">
              <?php selectKonto("Von", $id="uebertragFormSource"); ?>
            </div>
            <div class="col-md-3 col-6 pb-2 px-1">
              <?php selectKonto("Nach", $id="uebertragFormDestination"); ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-9 col-sm-8 px-1"></div>
            <div class="col-md-3 col-sm-4 px-1">
              <button type="button" onclick="addUebertrag()" class="btn btn-outline-primary w-100 shadow-box-sm">Übertrag buchen</button>
            </div>
          </div>
        </form>
      </div>
      <div class="container-sm py-2 px-3 mt-2 border rounded shadow-box">
        <div class="container p-0 overflow-x-auto">
          <?php listUebertraege(); ?>
        </div>
      </div>
    </div>

  </body>
  <?php
  printFooter($startTime);
  ?>
  <script>
    $(document).ready(function() {
        document.getElementById('uebertragFormDate').valueAsDate = new Date();
    });
  </script>
  <script src="assets/scripts/scripts.js"></script>
</html>
