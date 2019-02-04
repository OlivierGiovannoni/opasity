<?php

function findDates($dueDate)
{
    $sqlDate = "SELECT Commentaire,Commande,Reglement,Commande_courte,Date FROM webcontrat_commentaire WHERE Prochaine_relance='$dueDate' AND DernierCom=1;";
    $rowsDate = querySQL($sqlDate, $GLOBALS['connectionW']);

    foreach ($rowsDate as $rowDate) {

        $orderId = $rowDate['Commande'];
        $orderIdShort = $rowDate['Commande_courte'];

        $final = findReview($orderId);
        $details = getOrderDetails($orderId, $orderIdShort);
        $newDate = date("d/m/Y", strtotime($rowDate['Date']));
        $paid = isItPaid($rowDate['Commande']);
        $paidCompta = ($paid['compta'] === "R" ? "Oui" : "Non");
        if ($rowDate['Reglement'] == "R")
            $paidBase = "Oui";
        else if ($paid['compta'] == "R")
            $paidBase = "Oui";
        else
            $paidBase = "Non";

        $orderLink = generateLink("commentList.php?id=" . $orderId, $orderIdShort);
        $reviewLink = generateLink("searchReviewOrders.php?id=" . $final['Id'], $final['Name']);
        $companyLink = generateLink("searchClientOrders.php?id=" . $details['clientId'], $details['companyName']);

        $cells = array($orderLink, $reviewLink, $details['priceRaw'], $paidCompta, $companyLink, $details['contactName'], $paidBase, $rowDate['Commentaire'], $newDate);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB READ

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

$dueDate = filter_input(INPUT_GET, "dueDate");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/search.html");

        $style = str_replace("{type}", "date", $style);
        $style = str_replace("{query}", $dueDate, $style);

        $newDate = date("d/m/Y", strtotime($dueDate));

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("admin.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
        echo "<table>";

        $cells = array("Contrat","Revue","PrixHT","Payé compta","Nom de l'entreprise","Nom du contact","Payé base","Commentaire","Date commentaire");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $charsetR = mysqli_set_charset($connectionR, "utf8");
        $charsetW = mysqli_set_charset($connectionW, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);
        else if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connectionW->error);

        findDates($dueDate);

        echo "</table><br><br><br>";
        echo "</html>";


    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
    $connectionW->close();
}

?>
