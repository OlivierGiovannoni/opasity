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
$credentialsW = getCredentials("credentialsW.txt"));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']);

function updater($val, $id)
{
    $sqlHelper = "UPDATE webcontrat_commentaire SET NumTelephone='$val' WHERE Commentaire_id=$id;";
    if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

        // UPDATE output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
    }
}

function reZero()
{
        $sqlHelper = "SELECT Commentaire_id,NumTelephone FROM webcontrat_commentaire;";
        if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

            while ($rowHelper = mysqli_fetch_array($resultHelper)) {

                $len = strlen($rowHelper['NumTelephone']);
                if ($len === 9) {

                    $phone = "0" . $rowHelper['NumTelephone'];
                    updater($phone, $rowHelper['Commentaire_id']);
                }
            }
        } else {
            echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
        }
}

mysqli_set_charset($connectionW, "utf8");
reZero();
//unlink(__FILE__);

?>
