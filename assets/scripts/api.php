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

if(isset($_POST["type"])){
    echo "It's Alive\n";
    if($_POST["type"] == "einnahme"){
        insertBuchung($conn, true);
    } elseif ($_POST["type"] == "ausgabe"){
        insertBuchung($conn, false);
    } elseif ($_POST["type"] == "addKategorie"){
        addKategorie($conn, $_POST["einnahme"]);
    } elseif ($_POST["type"] == "addKonto") {
        addKonto($conn);
    } elseif ($_POST["type"] == "editBuchung") {
        echo "editBuchung";
        editBuchung($conn, $_POST["einnahme"]);
    } elseif($_POST["type"] == "setCookie"){
        //expire after 100 years :)
        setcookie($_POST["key"], $_POST["value"],time()+3075840000);
    }
} else {
    echo "not triggered";
}

function getBuchungen($conn){
    $sql = "SELECT datum, betrag, konten.kontenBezeichnung FROM buchungen inner join konten on konten.id = buchungen.kontenid";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
    }
}

function editBuchung(mysqli $conn, $einnahme)
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

function insertBuchung($conn, $einnahme){
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

function addKategorie($conn, $einnahme){
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

function addKonto($conn){
    $sql = "INSERT INTO konten (kontoBezeichnung, startbetrag) VALUE (?, ?)";
    $stmt = $conn->prepare($sql);
    $name = $_POST["name"];
    $betrag = $_POST["startbetrag"];
    $stmt->bind_param("si", $name, $betrag);

    if ($stmt->execute()) {
        echo "Row inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}