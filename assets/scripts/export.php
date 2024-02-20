<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/csv; charset=utf-8');

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

    // fetch all rows
    $query = "SELECT * FROM $table";
    $result = $db->query($query);

    // add table name to csv
    fputcsv($fp, [$table], $delim);

    // get columns from result
    $fields = mysqli_fetch_fields($result);
    $field_info = [];
    foreach ($fields as $field) {
        // Append suffixes to primary key and foreign key columns
        $field_name = $field->name;
        if ($field->flags & MYSQLI_PRI_KEY_FLAG) {
            $field_name .= ".PRIMARY_KEY";
        }
        if ($field->flags & MYSQLI_AUTO_INCREMENT_FLAG) {
            $field_name .= ".AUTO_INCREMENT";
        }
        // Append data type to the field name
        $field_info[] = $field_name . "(" . $field->type . ")";
    }

    // Fetch foreign key constraints
    $foreign_key_names = [];
    $foreign_key_query = "SELECT
                               COLUMN_NAME,
                               CONSTRAINT_NAME,
                               REFERENCED_TABLE_NAME,
                               REFERENCED_COLUMN_NAME
                           FROM
                               INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                           WHERE
                               TABLE_NAME = '$table' AND
                               CONSTRAINT_NAME != 'PRIMARY'";
    $foreign_key_result = $db->query($foreign_key_query);
    while ($row = $foreign_key_result->fetch_assoc()) {
        $foreign_key_names[$row['COLUMN_NAME']] = "->" . $row['REFERENCED_TABLE_NAME'] . "(" . $row['REFERENCED_COLUMN_NAME'] . ")";
    }

    // Append foreign key information to the column names
    $fields_ = [];
    foreach ($field_info as $field_name) {
        if (preg_match('/^(.*?)\((.*?)\)$/', $field_name, $matches)) {
            $column_name = $matches[1];
            if (isset($foreign_key_names[$column_name])) {
                $fields_[] = $field_name.$foreign_key_names[$column_name];
            } else {
                $fields_[] = $field_name;
            }
        }
    }
    // add columns with datatype and key constraints to csv
    fputcsv($fp, $fields_ , $delim);

    // add all rows to csv
    while ($row = $result->fetch_assoc()) {
        fputcsv($fp, $row, $delim);
    }

    // Free result set
    $result->free();
}

// black magic to download a file that's not empty :)
fseek($fp, 0);
header('Content-Disposition: attachment; filename="' . $filename . '";');

fpassthru($fp);