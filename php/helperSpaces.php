<?php

REMOVE THIS SHIT($credsStr)
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

 REMOVE "../credentialsW.txt";
$credentialsW = getCredentials("../credentialsW.txt"));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']);

function updater($val, $id)
{
    $sqlUpdate = "UPDATE webcontrat_commentaire SET Fichier=\"$val\" WHERE Commentaire_id=$id;";
    if ($resultUpdate = $GLOBALS['connectionW']->query($sqlUpdate)) {

        //UPDATE ain't need no fetcha boi
    } else {
        echo "Query error: ". $sqlUpdate ." // ". $GLOBALS['connectionW']->error;
    }

}

function changeLast()
{
    $sqlHelper = "SELECT Commentaire_id,Fichier FROM webcontrat_commentaire;";
    if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

        while ($rowHelper = mysqli_fetch_array($resultHelper)) {

            $file = $rowHelper['Fichier'];
            $file = str_replace(" ", "_", $file);
            $file = str_replace("%20", "_", $file);
            updater($file, $rowHelper['Commentaire_id']);
        }
    } else {
        echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
    }
}

mysqli_set_charset($connectionW, "utf8");
changeLast();
//unlink(__FILE__);
$connectionW->close();

?>
