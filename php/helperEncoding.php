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

function updater($id, $column)
{
    $sqlHelper = "UPDATE webcontrat_commentaire SET $column = CONVERT(CAST($column AS BINARY) USING utf8) WHERE Commentaire_id=$id;";
    if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

        // UPDATE output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
    }
}

function reEncode()
{
        $sqlHelper = "SELECT Commentaire_id FROM webcontrat_commentaire;";
        if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

            while ($rowHelper = mysqli_fetch_array($resultHelper)) {

                updater($rowHelper['Commentaire_id'], "Commentaire");
                updater($rowHelper['Commentaire_id'], "Fichier");
            }
        } else {
            echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
        }
}

mysqli_set_charset($connectionW, "utf8");
reEncode();
//unlink(__FILE__);

?>
