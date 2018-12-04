<?php

function credsArr($credsStr)
{
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

$credsFileW = "../credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']);

$columns = filter_input(INPUT_GET, "columns");
$table = filter_input(INPUT_GET, "table");
$separator = filter_input(INPUT_GET, "separator");

function reEncode($columns, $table, $separator)
{
    $columns = explode($separator, $columns);
    foreach ($columns as $column) {

        $sqlHelper = "UPDATE $table SET $column = CONVERT( CAST($column AS BINARY) USING utf8);";
        if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
        }
    }
}

mysqli_set_charset($connectionW, "utf8");
reEncode($columns, $table, $separator);
//unlink(__FILE__);

?>
