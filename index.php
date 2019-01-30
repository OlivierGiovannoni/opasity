<?php

function findDates($dueDate)
{
    $sqlDate = "SELECT Commentaire_id,Commentaire,Commande,Commande_courte,Date,Prochaine_relance,AdresseMail,Reglement FROM webcontrat_commentaire WHERE Prochaine_relance<='$dueDate' AND DernierCom=1 ORDER BY Prochaine_relance ASC;";
    $rowsDate = querySQL($sqlDate, $GLOBALS['connectionW']);

    foreach ($rowsDate as $rowDate) {
        $paid = isItPaid($rowDate['Commande']);
        if ($paid == "R")
            continue ;
        $orderId = $rowDate['Commande'];
        $orderIdShort = $rowDate['Commande_courte'];

        $orderLink = generateLink("php/allComments.php?id=" . $orderId, $orderIdShort); // Generate <a> link with orderId as id and orderIdShort as text
        $final = findReview($orderId); // Find review details related to the order
        $details = getOrderDetails($orderId); // Get order and client details related to the order
        $companyLink = generateLink("php/searchClientOrders.php?id=" . $details['clientId'], $details['companyName']);
        $reviewLink = $final['Name'] . " " . $final['Year'];
        $reviewLink = generateLink("php/searchReviewOrders.php?id=" . $final['Id'], $reviewLink); // Generate <a> link with reviewId as id and reviewName Year as text
        $mailtoLink = generateLink("mailto:" . $rowDate['AdresseMail'], $rowDate['AdresseMail']); // Generate <a> mailto link
        $dateComm = date("d/m/Y", strtotime($rowDate['Date'])); // Comment date
        $dateNext = date("d/m/Y", strtotime($rowDate['Prochaine_relance'])); // Next date
        $deleteLink = generateLink("php/deleteComment.php?id=" . $rowDate['Commentaire_id'], "Supprimer", "_self", "return confirm('Supprimer commentaire ?')"); // Create <a> link to delete comment
        if ($dateNext == "00/00/0000" || $dateNext == "01/01/1970")
            $dateNext = "Aucune"; // If date is invalid/NULL show "Aucune"
        if ($rowDate['Reglement'] == "R")
            $paid = "Oui";
        else
            $paid = "Non";

        $cells = array($orderLink, $reviewLink, $details['priceRaw'], $companyLink, $paid, $mailtoLink, $rowDate['Commentaire'], $dateComm, $dateNext, $deleteLink);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "php/helperFunctions.php";

$credentials = getCredentials("./credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB READ

$credentialsW = getCredentials("./credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isset($_COOKIE['author'])) {

        $mainHTML = file_get_contents("html/index.html");
        echo $mainHTML;

        $today = date("Y-m-d");
        $newDate = date("d/m/Y", strtotime($today));

        echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
        echo "<table>";

        $cells = array("Contrat","Revue","PrixHT","Nom de l'entreprise","Payé base","E-mail","Commentaire","Date commentaire","Prochaine relance","Supprimer commentaire");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $charsetR = mysqli_set_charset($connectionR, "utf8");
        $charsetW = mysqli_set_charset($connectionW, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);
        else if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connectionW->error);

        findDates($today);

        echo "</table><br><br><br>";
    } else {

        $loginHTML = file_get_contents("html/login.html");
        echo "Veuillez vous connecter pour pouvoir utiliser cet outil.";
        echo $loginHTML;
    }

    $connectionR->close();
    $connectionW->close();
}

?>