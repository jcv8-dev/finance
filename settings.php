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
        <title>Finance - Einstellungen</title>
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <?php printHeader("settings"); ?>
        <div class="container px-2">
            <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <div class="row">
                    <h2 class="mt-2">Kategorisierung</h2>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 py-1 px-1">
                        <?php listKategorien(1); ?>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 py-1 px-1">
                        <?php listKategorien(0); ?>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 py-1 px-1">
                        <?php listKonten(); ?>
                    </div>
                </div>
            </div>
            <div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box">
                <div class="row">
                    <h2 class="mt-1">Angezeigte Daten</h2>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-12 py-1 px-1">
                        <div class="w-100"><h3>Zeitraum</h3></div>
                        <?php yearRadio(); ?>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 py-1 px-1 ">
                        <div class="w-100"><h3>Datentyp</h3></div>
                        <?php hideSensitive(); ?>
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
                        <button type="button" onclick="addEinnahmeKategorie();" class="btn btn-primary">Speichern</button>
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
                        <button type="button" onclick="addAusgabeKategorie();" class="btn btn-primary">Speichern</button>
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
                        <button type="button" onclick="" id="addKategorieModalSubmit" class="btn btn-primary">Speichern</button>
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
                        <button type="button" onclick="addKonto();" class="btn btn-primary">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
    <script src="assets/scripts/scripts.js">

    </script>
</html>
