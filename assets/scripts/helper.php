<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "secrets.php";
global $secret;
$servername = $secret["dbServer"];
$username = $secret["dbUser"];
$password = $secret["dbPass"];
$dbname = $secret["dbName"];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function printHeader($active){
    // associate url with Title
    $sites = array(
        "/finance" => "Übersicht",
        "einnahmen" => "Einnahmen",
        "ausgaben" => "Ausgaben",
        "konten" => "Konten",
        "settings" => "Einstellungen"
    );
    echo '<h1 class="p-2">One of the Tools of all Time</h1>
        <ul class="nav nav-tabs ps-2">';
    // navbar with tabs
    foreach($sites as $url => $title){
        echo '<li class="nav-item">';
        if($active == $url){
            echo '<a class="nav-link active" aria-current="page" href="#">'.$title.'</a>';
        } else {
            echo '<a class="nav-link" href="'.$url.'">'.$title.'</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function selectKonto($title, $id = "selectKonto") {
    // Input Select für alle Konten. Title = Vorausgewählte Disabled Option
    global $conn;
    $sql = "SELECT id, kontoBezeichnung FROM konten";
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
    global $conn;
    $sql = "SELECT id, kategorieBezeichnung FROM kategorie where einnahme = $einnahme";
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
    global $conn;
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
            echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-1">
            <div class="card shadow-box-sm" style="width: auto;" id="'.$row["id"].'">
            <div class="card-header"><b>';
            echo $row["kontoBezeichnung"];
            echo '</b></div>
            <div class="card-body p-2">
            <div class="row">
            <div class="col-6 text-end pe-2 ps-1"><p>Jahresbeginn</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($startbetrag).'€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Einnahmen</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($einnahmen).'€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Ausgaben</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($ausgaben).'€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Übertrag</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.("xx,xx").'€</p></div>
            <div class="col-6 text-end pe-2 ps-1"><p>Aktuell</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($startbetrag + $einnahmen - abs($ausgaben)).'€</p></div>
            </div>
            </div>
            </div>
            </div>';
        }
    }
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
    for($i = 0; $i < 2; $i++){
        echo '<tr>
                  <th scope="row">1</th>
                  <td>01.01.2024</td>
                  <td>123,25€</td>
                  <td>Girokonto</td>
                  <td>Bar</td>
                  <td class="px-0" id="edit"><button type="button" onclick="editEntry("'.$i.'") class="btn p-2"><img src="assets/img/edit.svg" height="22px"></button></td>
                 </tr>';
    }
    echo '</tbody>
         </table>';
}

function listKategorien($einnahme) {
    // Erzeuge liste aller Kategorien (Differenzierung nach Einnahme/Ausgabe mit 1/0)
    global $conn;
    $sql = "SELECT id, kategorieBezeichnung FROM kategorie WHERE einnahme = $einnahme";
    $result = $conn->query($sql);
    $kategorien = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $kategorien[$row["id"]] = $row["kategorieBezeichnung"];
        }
    }
    echo '<ul class="list-group">';
    echo '<li class="list-group-item container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><b>'; echo $einnahme == 0 ? "Ausgaben" : "Einnahmen" ; echo'</b></div></div></li>';
    foreach($kategorien as $id => $kategorie){
        echo '<button class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p>'.$kategorie. '</p></div><div class="col-2 p-0"><img src="assets/img/edit.svg" height="22px" class="m-1 "> </div></div></button>';
    }
    echo '<button type="button" onclick="prepareKategorieModal('; echo $einnahme == 0 ? "false" : "true"; echo ')" class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p>+ Hinzufügen</p></div></div></button>';
    echo '</ul>';
}

function listKonten() {
    // Erzeuge liste aller Konten
    global $conn;
    $sql = "SELECT id, kontoBezeichnung FROM konten";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $konten[$row["id"]] = $row["kontoBezeichnung"];
        }
    }
    echo '<ul class="list-group">';
    echo '<li class="list-group-item container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><b>Konten</b></div></div></li>';
    foreach($konten as $id => $konto){
        echo '<button class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p class="">'.$konto. '</p></div><div class="col-2 p-0"><img src="assets/img/edit.svg" height="22px" class="m-1"> </div></div></button>';
    }
    echo '<button type="button" data-bs-toggle="modal" data-bs-target="#newKonto" class="list-group-item list-group-item-action container py-1"><div class="row"><div class="col-10 p-1 d-flex align-items-center"><p>+ Hinzufügen</p></div></div></button>';    echo '</ul>';
}

function listBuchungen($einnahme) {
    // Erzeuge liste aller Buchungen (Differenzierung nach Einnahme/Ausgabe mit 1/0)
    // Reihenfolge andersherum weil ausgabe als negativer Betrag gespeichert wird.
    if($einnahme == "1"){
        $einnahmeModifier = "> 0";
        $order = "ASC";
    } else {
        $einnahmeModifier = "< 0";
        $order = "DESC";
    }
    global $conn;

    // sortierung aus cookie lesen
    $col = readCookie("order".$einnahme);
    if($col == ""){$col = "datum";}
    
    $sql = "SELECT buchungen.id, datum, betrag, kontoBezeichnung, kategorie.kategorieBezeichnung, kommentar FROM buchungen INNER JOIN kategorie on buchungen.kategorieid = kategorie.id INNER JOIN konten on buchungen.kontoid = konten.id where betrag $einnahmeModifier ORDER BY $col $order";
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
        while ($row = $result->fetch_assoc()) {
            $sum += $row["betrag"];
            $betrag = $row["betrag"];
            echo "<tr id=".$row["id"].">";
            echo "<th scope=\"row\">$i</th>";
            echo "<td class='px-2' id='datum'>".$row["datum"]."</td>".
            "<td class='px-2 text-end' id='betrag'>".ff(abs($betrag))."€</td>".
            "<td class='px-2' id='konto'>".$row["kontoBezeichnung"]."</td>".
            "<td class='px-2 atext-center' id='kategorie'>".$row["kategorieBezeichnung"]."</td>".
            "<td class='px-2' id='kommentar'>".$row["kommentar"]."</td>".
            "<td class='px-0' id='edit'><button type='button' onclick='editEntry(".$row["id"]. ")' class='btn p-2'><img src='assets/img/edit.svg' height='22px'></button></td>" .
            "</tr>";
            $i++;
        }
        //ergebniszeile
        echo "<th scope=\"row\"></th>";
        echo "<td>Summe</td>".
            "<td class='text-end'>".ff(abs($sum))."€</td>".
            "<td></td>".
            "<td></td>".
            "<td></td>".
            "<td></td>".
            "</tr>";
        echo "</tbody></table>";
    }
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
        echo "<input type='checkbox' class='btn-check' id='$year' autocomplete='off'>
        <label class='btn btn-outline-primary mb-1' for='$year'>".$year."</label>";
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
    global $conn;
    $title = $einnahme == true ? "Einnahmen" : "Ausgaben";
    $e = $einnahme == true ? "1" : "0";
    $sql = "select kategorieBezeichnung from kategorie where kategorie.einnahme = $e;";
    $result = $conn->query($sql);
    echo '<div class="container py-1 px-3 mt-2 border rounded border-dark-subtle shadow-box overflow-x-auto">';
    echo "<h2>$title nach Monat</h2>";
    echo '
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
        </tr>
      </thead>
      <tbody>';
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $kategorie = $row["kategorieBezeichnung"];
            echo "<tr><th scope='row'>$kategorie</th>";

            for($monat = 1; $monat <= 12; $monat++){
                echo "<td>".sumByKategorieMonat($kategorie, $monat)."</td>";
            }
            echo "</tr>";
        }
    }
    echo "</tbody></table></div>";
}

function sumByKategorieMonat($kategorie, $monat){
    //TODO
    return ff(mt_rand(0,500))."€";
}

function ff($float){
    // format Float as 1.234,56
    return number_format($float, "2", ",", ".");
}

function readCookie($key){
    if(isset($_COOKIE[$key])){
        return $_COOKIE[$key];
    }
    return "";
}

?>