<?php
require_once __DIR__.'/protect.php';
require_once __DIR__.'/assets/scripts/readDB.php';
require_once __DIR__.'/assets/scripts/helper.php';
header("Cache-Control: no-cache, must-revalidate");
$startTime = microtime(true);
?>

<!doctype html>
<html lang="de">
<?php head("Finance - Einstellungen"); ?>
    <body>
        <?php printHeader("settings"); ?>
        <div class="container px-2">
            <div class="container py-1 px-3 mt-2 border rounded shadow-box">
                <div class="row">
                    <h2 class="mt-2">Kategorien & Konten</h2>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12 py-1 px-2">
                        <?php listKategorien(1); ?>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12 py-1 px-2">
                        <?php listKategorien(0); ?>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12 py-1 px-2">
                        <?php listKonten(); ?>
                    </div>
                </div>
            </div>
            <div class="container py-1 px-3 mt-2 border rounded shadow-box">
                <div class="row">
                    <h2 class="mt-1">Einstellungen</h2>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-12 py-1 px-3">
                    <div class="p-2 border rounded h-100 shadow-box">
                        <div class="w-100"><h3>Angezeigter Zeitraum</h3></div>
                        <?php yearRadio(); ?>
                    </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 py-1 px-3">
                    <div class="p-2 border rounded h-100 shadow-box">
                        <div class="w-100"><h3>Angezeigte Daten</h3></div>
                        <?php hideSensitive(); ?>
                    </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 py-1 px-3">
                        <div class="p-2 border rounded h-100 shadow-box">
                        <div class="w-100"><h3>Berechnung Monatsbudget</h3></div>
                        <?php monthlyBudgetSelector(); ?>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="my-2" style="width: fit-content">
                        <button type="button" onclick="notImplemented()" id="submitSettings" class="btn btn-outline-primary">Speichern</button>
                    </div>
                </div>
            </div>
            <div class="container py-1 px-3 mt-2 border rounded shadow-box">
                <div class="row">
                    <h2 class="mt-1">Management</h2>
                </div>
                <div class="row">
                    <div class="my-2" style="width: fit-content">
                        <button type="button" onclick="openInBackground('/assets/scripts/export')" id="submitSettings" class="btn btn-outline-primary">Daten exportieren (CSV)</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="newEinnahmenKategorie">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Neue Kategorie für Einnahmen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="einnahmenKategorieForm">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12"><input type="text" id="einnahmeKategorieName" class="form-control shadow-box-sm" placeholder="Name der Kategorie" aria-label="Kommentar" required></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="addEinnahmeKategorie();" class="btn btn-outline-primary">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="newAusgabenKategorie">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Neue Kategorie für Ausgaben</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="ausgabenKategorieForm">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12"><input type="text" id="ausgabeKategorieName" class="form-control shadow-box-sm" placeholder="Name der Kategorie" aria-label="Kommentar" required></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="addAusgabeKategorie();" class="btn btn-outline-primary">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="addKategorieModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addKategorieModalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addKategorieForm">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <input type="text" id="addKategorieName" class="form-control shadow-box-sm" placeholder="Name der Kategorie" aria-label="Kommentar" required>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="" id="addKategorieModalSubmit" class="btn btn-outline-primary">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="newKonto">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Neues Konto hinzufügen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="kontoForm">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 pb-3"><input type="text" id="kontoName" class="form-control shadow-box-sm" placeholder="Name des Kontos" aria-label="Kommentar" required></div>
                                    <div class="col-12 pb-2"><input type="text" id="startBetrag" class="form-control shadow-box-sm" placeholder="Aktueller Kontostand" aria-label="Kommentar" required></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="addKonto();" class="btn btn-outline-primary">Speichern</button>
                    </div>
                </div>
            </div>
        </div>

    </body>
<?php
printFooter($startTime);
?>
    <script src="assets/scripts/scripts.js">

    </script>
</html>
