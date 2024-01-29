<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "secrets.php";
require_once "protect.php";
require_once "db.php";

function selectKonto($title, $id = "selectKonto") {
    // Input Select für alle Konten. Title = Vorausgewählte Disabled Option
    $conn = db();
    $sql = "SELECT id, kontoBezeichnung FROM konten order by kontoBezeichnung";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $konten[$row["id"]] = $row["kontoBezeichnung"];
        }
    }
    echo '<select class="form-select shadow-box-sm" aria-label="Konto" id="'.$id.'" required>
            <option selected disabled value="0">'.$title.'</option>';
    foreach ($konten as $id => $name) {
        echo "<option value='$id'>$name</option>";
    }
                
    echo '</select>';
}


function selectKategorie($title, $einnahme, $id = "selectKategorie") {
    // Input Select für alle Kategorien (Differenzierung nach Einnahme/Ausgabe mit 1/0). Title = Vorausgewählte Disabled Option
    $conn = db();
    $sql = "SELECT id, kategorieBezeichnung FROM kategorie where einnahme = $einnahme ORDER BY kategorieBezeichnung";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $kategorien[$row["id"]] = $row["kategorieBezeichnung"];
        }
    }
    echo '<select class="form-select shadow-box-sm" aria-label="Kategorie" id="'.$id.'" required>
            <option selected disabled value="0">'.$title.'</option>';
    foreach ($kategorien as $id => $name) {
        echo "<option value='$id'>$name</option>";
    }   
    echo '</select>';
}

function kontoCards() {
    // Bootstrap Cards für alle Konten
    $conn = db();
    $sql = "SELECT * FROM konten";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ausgabensql = "SELECT SUM(betrag) from buchungen where betrag < 0 and kontoid = ".$row["id"];
            $einnahmensql = "SELECT SUM(betrag) from buchungen where betrag > 0 and kontoid = ".$row["id"];
            $ausgaben = $conn->query($ausgabensql);
            $einnahmen = $conn->query($einnahmensql);
            $ausgaben = $ausgaben->fetch_assoc()["SUM(betrag)"];
            $ausgaben = $ausgaben != 0 ? abs($ausgaben) : 0;
            $einnahmen = $einnahmen->fetch_assoc()["SUM(betrag)"];
            $einnahmen = $einnahmen != 0 ? abs($einnahmen) : 0;
            $startbetrag = $row["startbetrag"];
            $uebertrag = getUebertrag($row["id"], $conn);
            echo '<div class="col-6 col-sm-6 col-md-4 col-lg-3 p-1">
            <div class="card shadow-box-sm" style="width: auto;" id="'.$row["id"].'">
            <div class="card-header"><b>';
            echo $row["kontoBezeichnung"];
            echo '</b></div>
            <div class="card-body p-2">
            <div class="row">
            <div class="col-6 text-end pe-2 ps-1"><p>Jahresbeginn</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($startbetrag).'&nbsp;€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Einnahmen</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($einnahmen).'&nbsp;€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Ausgaben</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($ausgaben).'&nbsp;€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Übertrag</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($uebertrag).'&nbsp;€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p class="fw-bold">Aktuell</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p class="fw-bold">'.ff($startbetrag + $einnahmen - abs($ausgaben) + $uebertrag).'&nbsp;€</p></div>
            </div>
            </div>
            </div>
            </div>';
        }
    }
}
function getUebertrag($id, $conn){
    $eingangquery = "SELECT SUM(betrag) from uebertrag where zielid = $id";
    $ausgangquery = "SELECT SUM(betrag) from uebertrag where quelleid = $id";
    $eingang = $conn->query($eingangquery)->fetch_assoc()["SUM(betrag)"];
    $eingangValue = ($eingang !== null) ? $eingang : 0;
    $ausgang = $conn->query($ausgangquery)->fetch_assoc()["SUM(betrag)"];
    $ausgangValue = ($ausgang !== null) ? $ausgang : 0;
    return $eingangValue-$ausgangValue;
}

function listUebertraege(){
    echo '<table class="table table-striped">
              <thead>
              <tr>
                  <th scope="col">#</th>
                  <th scope="col">Datum</th>
                  <th scope="col">Betrag</th>
                  <th scope="col">Von</th>
                  <th scope="col">Nach</th>
                  <th class="px-0" scope="col" style="width: 1rem;"> </th>
              </tr>
              </thead>
              <tbody>';
    $conn = db();
    $sql = "SELECT uebertrag.id, datum, betrag, quelle.kontoBezeichnung as quellKonto, ziel.kontoBezeichnung as zielKonto FROM uebertrag inner join konten as quelle on uebertrag.quelleid = quelle.id inner join konten as ziel on uebertrag.zielid = ziel.id;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            $i++;
            $id = $row["id"];
            $date = $row["datum"];
            $betrag = ff($row["betrag"]);
            $quelle = $row["quellKonto"];
            $ziel = $row["zielKonto"];
            echo "<tr id=\"$id\">
                      <th scope=\"row\">$i</th>
                      <td id=\"datum\">$date</td>
                      <td id=\"betrag\">$betrag&nbsp;€</td>
                      <td id=\"quelle\">$quelle</td>
                      <td id=\"ziel\">$ziel</td>
                      <td id=\"edit\" class=\"px-0\" id=\"edit\"><button type=\"button\" onclick=editEntry(\"$id\",\"uebertrag\") class=\"btn p-2\"><img alt=\"edit pictogram\" src=\"assets/img/edit.svg\" height=\"22px\"></button></td>
                     </tr>";
        }
    }
    echo "</tbody></table>";
}

function listKategorien($einnahme) {
    // Erzeuge liste aller Kategorien (Differenzierung nach Einnahme/Ausgabe mit 1/0)
    $conn = db();
    $sql = "SELECT id, kategorieBezeichnung FROM kategorie WHERE einnahme = $einnahme ORDER BY kategorieBezeichnung";
    $result = $conn->query($sql);
    $kategorien = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $kategorien[$row["id"]] = $row["kategorieBezeichnung"];
        }
    }
    echo '<ul class="list-group shadow-box">';
    echo '<li class="list-group-item container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><b>'; echo $einnahme == 0 ? "Ausgaben" : "Einnahmen" ; echo'</b></div></div></li>';
    foreach($kategorien as $id => $kategorie){
        echo '<button class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p>'.$kategorie. '</p></div><div class="col-2 p-0"><img alt="edit pictogram" src="assets/img/edit.svg" height="22px" class="m-1 "> </div></div></button>';
    }
    echo '<button type="button" onclick="prepareKategorieModal('; echo $einnahme == 0 ? "false" : "true"; echo ')" class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p>+ Hinzufügen</p></div></div></button>';
    echo '</ul>';
}

function listKonten() {
    // Erzeuge liste aller Konten
    $conn = db();
    $sql = "SELECT id, kontoBezeichnung FROM konten ORDER BY kontoBezeichnung";
    $result = $conn->query($sql);
    echo '<ul class="list-group shadow-box">';
    echo '<li class="list-group-item container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><b>Konten</b></div></div></li>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $konten[$row["id"]] = $row["kontoBezeichnung"];
        }
        foreach($konten as $id => $konto){
            echo '<button class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p class="">'.$konto. '</p></div><div class="col-2 p-0"><img alt="edit pictogram" src="assets/img/edit.svg" height="22px" class="m-1"> </div></div></button>';
        }
    }


    echo '<button type="button" data-bs-toggle="modal" data-bs-target="#newKonto" class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p>+ Hinzufügen</p></div></div></button>';    echo '</ul>';
}

function listBuchungen($einnahme) {
    // Erzeuge liste aller Buchungen (Differenzierung nach Einnahme/Ausgabe mit 1/0)
    // Reihenfolge andersherum weil ausgabe als negativer Betrag gespeichert wird.
    $key = "order".$einnahme;
    $col = readCookie($key);

    if($col == ""){$col = "datum";}

    $betragModifier = "> 0";
    $order = "ASC";

    if($einnahme == "0"){
        // Ausgaben auswählen
        $betragModifier = "< 0";

        // Ausgaben sortierung anpassen (Ausgaben sind negative werte)
        if($col == "betrag"){
            $order = "DESC";
        }
    }

    // Für Sortierung nach Datum neueste zuerst
    if($col == "datum" || $col == "created"){
        $order = "DESC";
    }


    $conn = db();

    // sortierung aus cookie lesen

    $sql = "SELECT buchungen.id, datum, betrag, kontoBezeichnung, kategorie.kategorieBezeichnung, kommentar FROM buchungen INNER JOIN kategorie on buchungen.kategorieid = kategorie.id INNER JOIN konten on buchungen.kontoid = konten.id where betrag $betragModifier ORDER BY $col $order";
    $result = $conn->query($sql);
    $sum = 0;
    echo '<table class="table table-sm table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th class="px-2" scope="col" >Datum</th>
                    <th class="px-2 text-end" scope="col">Betrag</th>
                    <th class="px-2" scope="col">Konto</th>
                    <th class="px-2" scope="col">Kategorie</th>
                    <th class="px-2" scope="col">Kommentar</th>
                    <th class="px-0" scope="col" style="width: 1rem;"> </th>
                </tr>
                </thead>';
    if ($result->num_rows > 0) {
        echo "<tbody>";
        $i = 1;
        if($einnahme == 1){
            $type = "einnahme";
        } else {
            $type = "ausgabe";
        }
        while ($row = $result->fetch_assoc()) {
            $sum += $row["betrag"];
            $betrag = $row["betrag"];
            $date = $row["datum"];
            $konto = $row["kontoBezeichnung"];
            $kategorie = $row["kategorieBezeichnung"];
            $betrag = ff(abs($betrag));
            $id = $row["id"];
            $kommentar = $row["kommentar"];
            echo "<tr id=" . $row["id"] . ">";
            echo "<th scope=\"row\">$i</th>";
            echo "<td class=\"px-2\" id=\"datum\"> $date </td>" .
                "<td class=\"px-2 text-end\" id=\"betrag\">$betrag&nbsp;€</td>" .
                "<td class=\"px-2\" id=\"konto\">$konto</td>" .
                "<td class=\"px-2 atext-center\" id=\"kategorie\">$kategorie</td>" .
                "<td class=\"px-2\" id=\"kommentar\">$kommentar</td>" .
                "<td class=\"px-0\" id=\"edit\">".
                "<button type=\"button\" class=\"btn p-2\" onclick=editEntry($id,\"$type\")>".
                    "<img alt=\"edit pictogram\" src=\"assets/img/edit.svg\" height=\"22px\">".
                "</button></td>" .
                "</tr>";
            $i++;
        }
    }
        //ergebniszeile
        echo "<th scope=\"row\"></th>";
        echo "<td>Summe</td>".
            "<td class='text-end'>".ff(abs($sum))."&nbsp;€</td>".
            "<td></td>".
            "<td></td>".
            "<td></td>".
            "<td></td>".
            "</tr>";
        echo "</tbody></table>";

}

function selectOrder($einnahme, $id = "filterReihenfolge"){
    // Select für Reihenfolge von Buchungen. Liest Wert aus Cookie.
    $selected = readCookie("order".$einnahme);
    echo '<select class="form-select shadow-box-sm" aria-label="Reihenfolge" id="'.$id.'">
        <option disabled value="0">Reihenfolge</option>';
    echo "<option "; echo $selected=="datum" ? "selected " : " "; echo "value='datum'>Buchungsdatum</option>";
    echo "<option "; echo $selected=="created" ? "selected " : " "; echo "value='created'>Erstellungsdatum</option>";
    echo "<option "; echo $selected=="betrag" ? "selected " : " "; echo "value='betrag'>Betrag</option>";
    echo "<option "; echo $selected=="kategorieBezeichnung" ? "selected " : " "; echo "value='kategorieBezeichnung'>Kategorie</option>";
    echo "<option "; echo $selected=="kommentar" ? "selected " : " "; echo "value='kommentar'>Kommentar</option>";
    echo "</select>";
}

function yearRadio(){
    //TODO Funktionalität
    


    for($i = 0; $i < 5; $i++){
        $year = 2023 + $i;
        echo '<div class="form-check">';
        echo "<input class=\"form-check-input\" type=\"radio\" name=\"flexRadioDefault\" id=\"$year\">
        <label class=\"form-check-label\" for=\"$year\">
        $year
        </label>";
        echo "</div>";
    }
    
}

function hideSensitive(){
    //TODO Funktionalität
    echo '<form name="sensitiveData">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Datum</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Betrag</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Konto</label>
              </div>
              <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Kategorie</label>
              </div>
              <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Kommentar</label>
        </div></form>
        ';
}

function monthlyCategory($einnahme, $id="monthlyTable"){
    $conn = db();
    $title = $einnahme ? "Einnahmen" : "Ausgaben";
    $e = $einnahme ? "1" : "0";
    $sql = "select kategorieBezeichnung,id from kategorie where kategorie.einnahme = $e ORDER BY kategorieBezeichnung;";
    $result = $conn->query($sql);
    echo '<div class="container-xl py-1 px-3 mt-2 border rounded shadow-box tc">';
    echo "<h2 class='pt-2'>$title</h2>";
    echo '<div class="overflow-x-auto ">
    <table class="table" id="'.$id.'">
      <thead>
        <tr>
            <th scope="col">Kategorie</th>
            <th scope="col">Jan.</th>
            <th scope="col">Feb.</th>
            <th scope="col">Mär.</th>
            <th scope="col">Apr.</th>
            <th scope="col">Mai</th>
            <th scope="col">Jun.</th>
            <th scope="col">Jul.</th>
            <th scope="col">Aug.</th>
            <th scope="col">Sept.</th>
            <th scope="col">Okt.</th>
            <th scope="col">Nov.</th>
            <th scope="col">Dez.</th>
            <th scope="col">Sum.</th>
        </tr>
      </thead>
      <tbody>';
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $kategorie = $row["kategorieBezeichnung"];
            $id = $row["id"];
            echo "<tr><th scope='row'>$kategorie</th>";
            $total = 0;
            for($monat = 1; $monat <= 12; $monat++){
                $sum = sumByKategorieMonat($id, $monat);
                echo "<td>".ff($sum)."&nbsp;€</td>";
                $total += $sum;
            }
            echo "<td>".ff($total)."&nbsp;€</td>";
            echo "</tr>";
        }
    }
    echo "<tr><th scope='row'>Summe</th>";
    for($monat = 1; $monat <= 12; $monat++){
        $sum = monthlyTotal($einnahme, $monat);
        echo "<td>".ff($sum)."&nbsp;€</td>";
    }
    echo "</tr>";
    echo "</tbody></table></div></div>";
}

function sumByKategorieMonat($kategorie, $monat){
    $conn = db();
    $sql = "SELECT betrag from buchungen where kategorieid = $kategorie and MONTH(datum) = $monat and YEAR(datum) = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $sum = 0;
    foreach ($result as $id =>$value){
        $sum += abs($value["betrag"]);
    }
    return $sum;
}

function monthlyTotal($einnahme, $monat){
    $conn = db();
    if($einnahme == "1"){
        $einnahmeModifier = "> 0";
    } else {
        $einnahmeModifier = "< 0";
    }
    $sql = "SELECT betrag from buchungen where betrag $einnahmeModifier and MONTH(datum) = $monat and YEAR(datum) = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $sum = 0;
    foreach ($result as $key=>$value){
        $sum += abs($value["betrag"]);
    }
    return $sum;
}

function printMonthlyBudget(){
    $month = date("m");
    $restbudget = ff(monthlyTotal(1, $month) - monthlyTotal(0, $month) - 200);
    echo "<div class=\'\'><h2 class='text-center my-2 tc'>Diesen Monat noch verfügbar: $restbudget&nbsp;€</h2></div>";
}

function getAvailableYears(){
    
}

