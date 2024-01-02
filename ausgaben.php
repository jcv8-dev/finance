<?php
require 'assets/scripts/helper.php';
require 'protect.php';
header("Cache-Control: no-cache, must-revalidate");
$startTime = microtime(true);
?>

<!doctype html>
<html lang="de">
<?php head("Finance - Ausgaben"); ?>
<body>
<?php printHeader("ausgaben"); ?>
<div class="container px-2">
    <div class="container py-2 px-3 mt-2 border rounded border-dark-subtle shadow-box">
        <form name="neueBuchungForm">
            <div class="row">
                <div class="col-md-3 col-6 pb-2 px-1">
                    <input type="date" id="datePicker" class="form-control shadow-box-sm" aria-label="Date">
                </div>
                <div class="col-md-3 col-6 pb-2 px-1">
                    <input type="text" id="betrag" class="form-control shadow-box-sm" placeholder="Betrag"
                           aria-label="Betrag">
                </div>
                <div class="col-md-3 col-6 pb-2 px-1">
                    <?php selectKonto("Konto"); ?>
                </div>
                <div class="col-md-3 col-6 pb-2 px-1">
                    <?php selectKategorie("Kategorie", 0); ?>
                </div>
                <div class="col-12 pb-2 px-1">
                    <input type="text" id="kommentar" class="form-control shadow-box-sm" placeholder="Kommentar"
                           aria-label="Kommentar">
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-sm-8 px-1"></div>
                <div class="col-md-3 col-sm-4 px-1">
                    <button type="button" onclick="insertBuchung(false);"
                            class="btn btn-outline-primary w-100 shadow-box-sm" id="submit-ausgabe">Ausgabe buchen
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="container py-2 px-3 mt-2 border rounded border-dark-subtle shadow-box">
        <form>
            <div class="row">
                <div class="col-4 px-1">
                    <input type="text" id="filterSuche" class="form-control shadow-box-sm" placeholder="Suche">
                </div>
                <div class="col-5">
                    <?php selectOrder("1"); ?>
                </div>
                <div class="col-3 px-1">
                    <button type="button" id="submit-filter" onclick="submitFilter(false)"
                            class="btn btn-outline-primary w-100 shadow-box-sm">Anwenden
                    </button>
                </div>
            </div>
        </form>
        <div class="container p-0 overflow-x-auto">
            <?php listBuchungen("0"); ?>
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
                                <div class="col-12 pb-3"><input type="date" id="editFormDate"
                                                                class="form-control shadow-box-sm" aria-label="Datum"
                                                                required></div>
                                <div class="col-12 pb-2"><input type="text" id="editFormBetrag"
                                                                class="form-control shadow-box-sm" placeholder="Betrag"
                                                                aria-label="Betrag" required></div>
                                <div class="col-12 pb-2"><?php selectKonto("Konto", $id = "editFormKonto"); ?></div>
                                <div class="col-12 pb-2"><?php selectKategorie("Kategorie", 0, $id = "editFormKategorie"); ?></div>
                                <div class="col-12 pb-2"><input type="text" id="editFormKommentar"
                                                                class="form-control shadow-box-sm"
                                                                placeholder="Kommentar" aria-label="Kommentar" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <button id="deleteBuchung" type="button" onclick="notImplemented()"
                                class="btn btn-outline-danger col me-2">Löschen
                        </button>
                        <button id="submitEditBuchung" type="button" class="btn btn-primary col">Speichern</button>
                    </div>
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
    $(document).ready(function () {
        document.getElementById('datePicker').valueAsDate = new Date();
    });
</script>
<script src="assets/scripts/scripts.js"></script>
</html>
