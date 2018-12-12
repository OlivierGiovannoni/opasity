<?php

$orderId = filter_input(INPUT_POST, "hiddenId");

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

$credsFile = "../credentialsW.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB

function checkEmpty($orderId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcontrat_commentaire WHERE Commande='$orderId';";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        return (mysqli_num_rows($resultComment));
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connection']->error;
    }
}

function setPaid($orderId)
{
    $today = date("Y-m-d");
    $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
    $checkEmpty = checkEmpty($orderId);
    if ($checkEmpty === 0) {
        $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom,Reglement";
        $rowValues = "'             ','dev','$today','$orderId','$orderIdShort','1970-01-01','','','NULL',1,'R'";
        $sqlPaid = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
        if ($resultPaid = $GLOBALS['connection']->query($sqlPaid)) {

            // INSERT output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connectionW']->error; 
        }

    } else {
        $sqlPaid = "UPDATE webcontrat_commentaire SET Reglement='R' WHERE Commande='$orderId';";
        if ($resultPaid = $GLOBALS['connection']->query($sqlPaid)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connection']->error;
        }
    }
    echo "Le contrat à été passé en 'reglé' avec succès. ";
    echo "<a  href=\"../index.php\">Retourner au menu</a>";
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    $charset = mysqli_set_charset($connection, "utf8");

    if ($charset === FALSE)
        die("MySQL SET CHARSET error: ". $connection->error);

    setPaid($orderId);
}

?>
