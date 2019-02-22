<?php

function setAccepted($orderId, $author)
{
    $today = date("Y-m-d");
    $orderIdShort = getOrderIdShort($orderId);
    $checkEmpty = checkEmpty($orderId);
    if ($checkEmpty) {

        $nowTime = date("H:i:s");
        $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom,Reglement";
        $rowValues = "'Le contrat à été passé en accepté à $nowTime.','$author','$today','$orderId','$orderIdShort','1970-01-01','','','NULL',1,'R'";
        $sqlPaid = "INSERT INTO webcommercial_commentaire ($rowNames) VALUES ($rowValues);";
        querySQL($sqlPaid, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.
    } else {
        $sqlPaid = "UPDATE webcommercial_commentaire SET Reglement='R' WHERE Commande='$orderId';";
        querySQL($sqlPaid, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.
    }
    header("Location: commentList.php?id=" . $orderId);
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE

$orderId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        setAccepted($orderId, $_COOKIE['author']);
    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
}

?>
