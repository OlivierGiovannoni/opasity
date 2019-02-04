<?php

function setUnpaid($orderId)
{
    $today = date("Y-m-d");
    $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
    $checkEmpty = checkEmpty($orderId);
    if ($checkEmpty === 0) {
        $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom,Reglement";
        $rowValues = "'             ','dev','$today','$orderId','$orderIdShort','1970-01-01','','','NULL',1,''";
        $sqlUnpaid = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
        querySQL($sqlUnpaid, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.
    } else {
        $sqlUnpaid = "UPDATE webcontrat_commentaire SET Reglement='' WHERE Commande='$orderId';";
        querySQL($sqlUnpaid, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.
    }
    header("Location: commentList.php?id=" . $orderId);
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB

$orderId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        setUnpaid($orderId);
    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
}

?>
