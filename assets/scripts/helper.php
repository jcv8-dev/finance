<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "secrets.php";

function db(){
    global $secret;

    $servername = $secret["dbServer"];
    $username = $secret["dbUser"];
    $password = $secret["dbPass"];
    $dbname = $secret["dbName"];

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


function printHeader($active){
    // associate url with Title
    $sites = array(
        "index" => "Übersicht",
        "einnahmen" => "Einnahmen",
        "ausgaben" => "Ausgaben",
        "konten" => "Konten",
        "settings" => "Einstellungen",
        "static"=> "S"
    );
    echo '<div class="row w-100">
            <h1 class="py-2 px-4 col-11 pr prh pe-2">One of the Tools of all Time</h1>
            <div class="col-1 justify-content-end p-0">
                <form class="w-fit-content mx-auto" method="post" action="">
                    <input type="hidden" name="logout" value="true" /> 
                    <a class="end-0 button btn btn-outline-primary my-3 bg-pr-h bc p-1" onclick="this.parentNode.submit();"><img alt="exit pictogram" src="assets/img/exit.svg" width="30px"> </a>
                </form>
            </div>
        </div>
        <ul class="nav nav-tabs ps-2 bc">';
    // navbar with tabs
    foreach($sites as $url => $title){
        echo '<li class="nav-item">';
        if($active == $url){
            echo '<a class="nav-link active pr-h bc" aria-current="page" href="#">'.$title.'</a>';
        } else {
            echo '<a class="nav-link pr-h tc bc-h" href="'.$url.'">'.$title.'</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

function printFooter($startTime){
    echo "
        <div class=\"container\">
            <div class=\"row py-2\">
                <div class=\"col tc\">";
                    $endTime = microtime(true);
                    $executionTime = ff($endTime - $startTime);
                    echo "PHP Execution Time: $executionTime seconds</div>";
    echo "
                </div>
            </div>
        </div>
   ";
}

function head($title){
    echo '<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="assets/scripts/3rdparty/bootstrap-5.3.2.min.css" rel="stylesheet">
        <script src="assets/scripts/3rdparty/bootstrap-5.3.2.bundle.min.js"></script>
        <script src="assets/scripts/3rdparty/jquery-3.7.1.min.js"></script>
        <title>'.$title.'</title>
        <link rel="stylesheet" href="assets/styles.css">
        <link rel="stylesheet" href="assets/theme.css">
    </head>';
}

function selectKonto($title, $id = "selectKonto") {
    // Input Select für alle Konten. Title = Vorausgewählte Disabled Option
    $conn = db();
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
    $conn = db();
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
            echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-1">
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
            <div class="col-6 text-end pe-2 ps-1"><p>Aktuell</p></div>
            <div class="col-6 text-end pe-3 ps-0 pe-1"><p>'.ff($startbetrag + $einnahmen - abs($ausgaben) + $uebertrag).'&nbsp;€</p></div>
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
            echo '<tr>
                      <th scope="row">'.$i++.'</th>
                      <td>'.$row["datum"].'</td>
                      <td>'.ff($row["betrag"]).'&nbsp;€</td>
                      <td>'.$row["quellKonto"].'</td>
                      <td>'.$row["zielKonto"].'</td>
                      <td class="px-0" id="edit"><button type="button" onclick=editEntry("'.$row["id"].'") class="btn p-2"><img alt="edit pictogram" src="assets/img/edit.svg" height="22px"></button></td>
                     </tr>';
        }
    }
    echo '</tbody>
         </table>';
}

function listKategorien($einnahme) {
    // Erzeuge liste aller Kategorien (Differenzierung nach Einnahme/Ausgabe mit 1/0)
    $conn = db();
    $sql = "SELECT id, kategorieBezeichnung FROM kategorie WHERE einnahme = $einnahme";
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
    $sql = "SELECT id, kontoBezeichnung FROM konten";
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
    if($einnahme == "1"){
        $einnahmeModifier = "> 0";
        $order = "ASC";
    } else {
        $einnahmeModifier = "< 0";
        $order = "DESC";
    }
    $conn = db();

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
            echo "<tr id=" . $row["id"] . ">";
            echo "<th scope=\"row\">$i</th>";
            echo "<td class='px-2' id='datum'>" . $row["datum"] . "</td>" .
                "<td class='px-2 text-end' id='betrag'>" . ff(abs($betrag)) . "&nbsp;€</td>" .
                "<td class='px-2' id='konto'>" . $row["kontoBezeichnung"] . "</td>" .
                "<td class='px-2 atext-center' id='kategorie'>" . $row["kategorieBezeichnung"] . "</td>" .
                "<td class='px-2' id='kommentar'>" . $row["kommentar"] . "</td>" .
                "<td class='px-0' id='edit'><button type='button' onclick='editEntry(" . $row["id"] . "," . $einnahme . ")' class='btn p-2'><img alt='edit pictogram' src='assets/img/edit.svg' height='22px'></button></td>" .
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
    $sql = "select kategorieBezeichnung,id from kategorie where kategorie.einnahme = $e;";
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
        </tr>
      </thead>
      <tbody>';
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $kategorie = $row["kategorieBezeichnung"];
            $id = $row["id"];
            echo "<tr><th scope='row'>$kategorie</th>";

            for($monat = 1; $monat <= 12; $monat++){
                echo "<td>".sumByKategorieMonat($id, $monat)."</td>";
            }
            echo "</tr>";
        }
    }
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
    return ff($sum)."&nbsp;€";
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
    $restbudget = ff(monthlyTotal(1, $month) - monthlyTotal(0, $month) - 100);
    echo "<div class=\'\'><h2 class='text-center my-2 tc'>Diesen Monat noch verfügbar: $restbudget&nbsp;€</h2></div>";
}

function ff($float){
    // format numbers as 1.234,56
    return number_format($float, "2", ",", ".");
}

function readCookie($key){
    if(isset($_COOKIE[$key])){
        return $_COOKIE[$key];
    }
    return "";
}

function monthlyBudgetSelector(){
    echo '<div class="form-check">
    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
    <label class="form-check-label" for="flexRadioDefault1">
        <input type="text" class="w-25 border rounded bga">
        Fester Betrag [€]
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
    <label class="form-check-label" for="flexRadioDefault2">
    <input type="text" class="w-25 border rounded bga">
      Dynamisch [%]
    </label>
  </div>';
}

function getAvailableYears(){
    
}

