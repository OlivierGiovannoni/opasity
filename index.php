<?php

$mainHTML = file_get_contents("main.html");
echo $mainHTML;
$today = date("Y-m-d");

function getOrderDetails($orderId, $orderIdShort, $final)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);

    while ($rowOrder) {

        $orderFull = $rowOrder['Commande'];

        $clientId = $rowOrder['Client_id'];
        $priceRaw = $rowOrder['PrixHT'];

        $reviewForm = "<form target=\"_blank\" action=\"php/searchReviewOrders.php\" method=\"post\">";
        $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
        $pubHidden = "<input type=\"hidden\" name=\"published\" value=\"" . $final['Pub'] . "\">";
        $reviewInput = "<input type=\"submit\" name=\"reviewName\" value=\"" . $final['Name'] . " " . $final['Year'] . "\">";
        $closeForm = "</form>";

        echo "<td>" . $reviewForm . $pubHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";
        echo "<td>" . $priceRaw . "</td>";
        $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
        if ($resultClient = $GLOBALS['connectionR']->query($sqlClient)) {

            $rowClient = mysqli_fetch_array($resultClient);
            $companyName = $rowClient['NomSociete'];
            $contactName = $rowClient['NomContact1'];

            $clientForm = "<form target=\"_blank\" action=\"php/searchClientOrders.php\" method=\"post\">";
            $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $rowClient['id'] . "\">";
            $clientInput = "<input type=\"submit\" name=\"clientName\" value=\"" . $companyName . "\">";
            $closeForm = "</form>";
            echo "<td>" . $clientForm . $clientHidden . $clientInput . $closeForm . "</td>";
        }
    }
}

function findReview($infoId)
{
    $sqlInfoReview = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    $rowInfoReview = querySQL($sqlInfoReview, $GLOBALS['connectionR'], true);
    $finalId = $rowInfoReview['Revue_id'];
    $sqlReview = "SELECT id,Nom,Annee,Paru FROM webcontrat_revue WHERE id='$finalId';";
    $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true);
    $finalName = $rowReview['Nom'];
    $finalId = $rowReview['id'];
    $finalYear = $rowReview['Annee'];
    $finalPub = $rowReview['Paru'];
    $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear, 'Pub' => $finalPub);
    return ($final);
}

function isItPaid($orderId)
{
    $sqlPaid = "SELECT Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowPaid = querySQL($sqlPaid, $GLOBALS['connectionR'], true);
    return ($rowPaid['Reglement']);
}

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

        $orderLink = generateLink("php/allComments.php?id=" . $orderIdShort, "_blank", $orderIdShort);
        $orderLink = generateCell($orderLink);
        echo $orderLink;
        $final = findReview($orderId);
        getOrderDetails($orderId, $orderIdShort, $final);
        if ($rowDate['Reglement'] == "R")
            echo "<td id=\"isPaid\">Oui</td>";
        else
            echo "<td id=\"isNotPaid\">Non</td>";
        $mail = $rowDate['AdresseMail'];
        echo "<td><a href=\"mailto:$mail\">" . $mail . "</a></td>";
        echo generateCell($rowDate['Commentaire']);
        $newDate = date("d/m/Y", strtotime($rowDate['Date']));
        echo generateCell($newDate);
        $newDate = date("d/m/Y", strtotime($rowDate['Prochaine_relance']));
        if ($newDate == "00/00/0000" || $newDate == "01/01/1970")
            echo generateCell("Aucune");
        else
            echo generateCell($newDate);

        $deleteLink = generateLink("php/deleteComment.php?id=" . $rowDate['Commentaire_id'], "_blank", "Supprimer");
        $deleteLink = generateCell($deleteLink);
        echo $deleteLink;
        echo "</tr>";
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
    $newDate = date("d/m/Y", strtotime($today));

    echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
    echo "<table>";
    echo "<tr>";
    echo generateCell("Contrat", true);
    echo generateCell("Revue", true);
    echo generateCell("Prix HT", true);
    echo generateCell("Nom de l'entreprise", true);
    echo generateCell("Payé base", true);
    echo generateCell("E-mail", true);
    echo generateCell("Commentaire", true);
    echo generateCell("Date commentaire", true);
    echo generateCell("Prochaine relance", true);
    echo generateCell("Supprimer commentaire", true);
    echo "</tr>";

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    findDates($today);

    echo "</table>";
    echo "</html>";

    $connectionR->close();
    $connectionW->close();
}

?>