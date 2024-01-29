<?php
require_once 'protect.php';
require_once 'assets/scripts/readDB.php';
require_once 'assets/scripts/helper.php';
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
    <div class="modal" tabindex="-1" id="editEntryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eintrag bearbeiten</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateEntryForm">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 pb-3"><input type="date" id="editFormDate" class="form-control shadow-box-sm" aria-label="Datum" required></div>
                                <div class="col-12 pb-2"><input type="text" id="editFormBetrag" class="form-control shadow-box-sm" placeholder="Betrag" aria-label="Betrag" required></div>
                                <div class="col-12 pb-2"><?php selectKonto("Von", $id = "editFormQuelle");?></div>
                                <div class="col-12 pb-2"><?php selectKonto("Nach", $id = "editFormZiel");?></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <button id="deletebuchung" type="button" onclick="notImplemented()" class="btn btn-outline-danger col me-2">Löschen</button>
                        <button id="submitEditUebertrag" type="button" class="btn btn-outline-primary col">Speichern</button>
                    </div>
                </div>
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
