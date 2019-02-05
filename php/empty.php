<?php

function emptySpaces()
{
    $sqlEmpty = "SELECT Commentaire_id,Reglement FROM webcontrat_commentaire WHERE Commentaire='             ';";
    $rowsEmpty = querySQL($sqlEmpty, $GLOBALS['connectionW']);

    foreach ($rowsEmpty as $rowEmpty) {

        $commId = $rowEmpty['Commentaire_id'];
        $status = $rowEmpty['Reglement'];
        $sqlPaid = "UPDATE webcontrat_commentaire SET Commentaire='Le contrat à été passé en payé.' WHERE Commentaire_id='$commId';";
        $sqlUnpaid = "UPDATE webcontrat_commentaire SET Commentaire='Le contrat à été passé en non-payé.' WHERE Commentaire_id='$commId';";
        if ($status === "R")
            querySQL($sqlPaid, $GLOBALS['connectionW'], false);
        else
            querySQL($sqlUnpaid, $GLOBALS ('connectionW'], false);
    }
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

        emptySpaces();
    } else
        displayLogin("Veuillez vous connecter.");

    $connectionW->close();
}

?>