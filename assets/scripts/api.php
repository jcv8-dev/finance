<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "secrets.php";

function db():mysqli{
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



if(isset($_POST["type"])){
    echo "It's Alive\n";
    if($_POST["type"] == "einnahme"){
        insertBuchung(db(), true);
    } elseif ($_POST["type"] == "ausgabe"){
        insertBuchung(db(), false);
    } elseif ($_POST["type"] == "addKategorie"){
        addKategorie(db(), $_POST["einnahme"]);
    } elseif ($_POST["type"] == "addKonto") {
        addKonto(db());
    } elseif ($_POST["type"] == "editBuchung") {
        echo "editBuchung";
        editBuchung(db(), $_POST["einnahme"]);
    } elseif ($_POST["type"] == "addUebertrag") {
        echo "addUebertrag";
        addUebertrag(db());
    } elseif($_POST["type"] == "setCookie"){
        //expire after 100 years :)
        setcookie($_POST["key"], $_POST["value"],time()+3075840000);
    }
} else {
    echo "not triggered";
}

function getBuchungen($conn):void{
    $sql = "SELECT datum, betrag, konten.kontoBezeichnung FROM buchungen inner join konten on konten.id = buchungen.kontoid";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
    }
}

function editBuchung(mysqli $conn, $einnahme):void
{
    $sql = "UPDATE buchungen SET datum = ?, betrag = ?, kontoid = ?, kategorieid = ?, kommentar = ? where id = ?;";
    $stmt = $conn->prepare($sql);
    // Werte aus $_POST[] lesen
    //TODO Sanitize + Userfeedback
    $betrag = abs(floatval(str_replace(",", ".",$_POST["betrag"])));
    $kommentar = $_POST["kommentar"];
    $kategorieid = intval($_POST["kategorieid"]);
    $kontoid = intval($_POST["kontoid"]);
    // Differenzierung nach Einnahme/Ausgabe
    if(!$einnahme){
        $betrag = -$betrag;
    }
    $stmt->bind_param("sdiisi",$_POST["date"], $betrag, $kontoid, $kategorieid, $kommentar, $_POST["id"]);
    //echo "Konto".$kategorieid. " Kategorie: ", $kontoid;

    // Disable Foreign Key Checks //TODO warum gehts ned mit?
    $disableChecks = "SET foreign_key_checks = 0;";
    $enableChecks = "SET foreign_key_checks = 1;";
    $disableChecksStmt = $conn->prepare($disableChecks);
    $enableChecksStmt = $conn->prepare($enableChecks);

    $disableChecksStmt->execute();
    if ($stmt->execute()) {
        echo "Row updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $enableChecksStmt->execute();
    $stmt->close();
    $conn->close();
}

function insertBuchung($conn, $einnahme):void{
    $sql = "INSERT INTO buchungen (datum, betrag, kategorieid, kontoid, kommentar) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // Werte aus $_POST[] lesen //TODO Sanitize & Userfeedback
    $betrag = abs(floatval(str_replace(",", ".",$_POST["betrag"])));
    $kommentar = $_POST["kommentar"];
    $kategorieid = intval($_POST["kategorieid"]);
    $kontoid = intval($_POST["kontoid"]);
    //Differenzierung Einnahme/Ausgabe
    if(!$einnahme){
        $betrag = -$betrag;
    }

    $stmt->bind_param("sdiis",$_POST["date"], $betrag, $kategorieid, $kontoid, $kommentar);

    if ($stmt->execute()) {
        echo "Row inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

function addKategorie($conn, $einnahme):void{
    $sql = "INSERT INTO kategorie (kategorieBezeichnung, einnahme) VALUE (?, ?)";
    $stmt = $conn->prepare($sql);
    $name = $_POST["name"];
    $stmt->bind_param("si", $name, $einnahme);

    if ($stmt->execute()) {
        echo "Row inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

function addKonto($conn):void{
    $sql = "INSERT INTO konten (kontoBezeichnung, startbetrag) VALUE (?, ?)";
    $stmt = $conn->prepare($sql);
    $name = $_POST["name"];
    $betrag = $_POST["startbetrag"];
    echo $betrag;
    $betrag = str_replace(".","",$betrag);
    $betrag = str_replace(",",".",$betrag);
    $stmt->bind_param("sd", $name, $betrag);

    if ($stmt->execute()) {
        echo "Row inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

function addUebertrag($conn):void{
    $sql = "INSERT INTO uebertrag (datum, betrag, quelleid, zielid) value (?,?,?,?);";
    $stmt = $conn->prepare($sql);
    $date = $_POST["date"];
    $betrag = abs(floatval(str_replace(",", ".",$_POST["betrag"])));
    $quelleid = intval($_POST["source"]);
    $zielid = intval($_POST["destination"]);
    $stmt->bind_param("sdii", $date,$betrag,$quelleid,$zielid);
    if ($stmt->execute()){
        echo "Row inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}