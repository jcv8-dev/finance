<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/csv');

require_once __DIR__."/db.php";
require_once __DIR__."/../../protect.php";

$db = db();
$filename = "finance-dump_" . date('Y-m-d') . ".csv";

$tables = [];
$result = $db->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$fp = fopen('php://memory', 'w');

foreach ($tables as $table) {

    $delim = ",";
    $query = "SELECT * FROM $table";
    $result = $db->query($query);
    fputcsv($fp, [$table], $delim);

    $fields = mysqli_fetch_fields($result);
    $field_names = [];
    foreach ($fields as $field) {
        $field_names[] = $field->name;
    }
    fputcsv($fp, $field_names, $delim);

    while ($row = $result->fetch_assoc()) {
        fputcsv($fp, $row, $delim);
    }

    // Free result set
    $result->free();
//    fpassthru($fp);
}

fseek($fp, 0);
header('Content-Disposition: attachment; filename="' . $filename . '";');

fpassthru($fp);