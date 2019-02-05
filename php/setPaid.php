<?php

function setPaid($orderId, $author)
{
    $today = date("Y-m-d");
    $orderIdShort = getOrderIdShort($orderId);
    $checkEmpty = checkEmpty($orderId);
    if ($checkEmpty) {

        $nowTime = date("H:i:s");
        $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom,Reglement";
        $rowValues = "'Le contrat à été passé en payé à $nowTime.','$author','$today','$orderId','$orderIdShort','1970-01-01','','','NULL',1,'R'";
        $sqlPaid = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
        querySQL($sqlPaid, $GLOBALS['connectionW'], false); // INSERT output doesn't need to be fetched.
    } else {
        $sqlPaid = "UPDATE webcontrat_commentaire SET Reglement='R' WHERE Commande='$orderId';";
        querySQL($sqlPaid, $GLOBALS['connectionW'], false); // UPDATE output doesn't need to be fetched.
    }
    header("Location: commentList.php?id=" . $orderId);
}

require_once "helper.php";

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB

$orderId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charsetW = mysqli_set_charset($connectionW, "utf8");

        if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connectionW->error);

        setPaid($orderId, $_COOKIE['author']);
    } else
        displayLogin("Veuillez vous connecter.");

    $connectionW->close();
}

?>
