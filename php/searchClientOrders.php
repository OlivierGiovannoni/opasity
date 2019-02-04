<?php

function findClientOrders($clientId)
{
    $sqlOrders = "SELECT Reglement,DateEmission,Commande FROM webcontrat_contrat WHERE Client_id='$clientId' ORDER BY DateEmission DESC LIMIT 100;";
    $rowsOrders = querySQL($sqlOrders, $GLOBALS['connectionR']);

    foreach ($rowsOrders as $rowOrders) {

            $orderId = $rowOrders['Commande'];
            $orderIdShort = getOrderIdShort($orderId);
            $final = findReview($orderId);

            $details = getOrderDetails($orderId);

            $orderLink = generateLink("commentList.php?id=" . $orderId, $orderIdShort);
            $reviewLink = generateLink("searchReviewOrders.php?id=" . $final['Id'], $final['Name']);
            $paid = isItPaid($orderId);
            $phone = getPhoneNumber($orderId, $clientId);
            $comment = selectLastComment($orderId, true);

            $paidCompta = ($paid['compta'] == "R" ? "Oui" : "Non");
            $paidBase = ($paid['base'] ==  "R" ? "Oui" : "Non");

            $cells = array($orderLink, $rowOrders['DateEmission'], $reviewLink, $details['priceRaw'], $paidCompta, $details['contactName'], $phone, $paidBase, $comment['text'], $comment['date'], $comment['reminder']);
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

$clientId = filter_input(INPUT_GET, "id");
$clientName = getCompanyName($clientId);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charsetR = mysqli_set_charset($connectionR, "utf8");
        $charsetW = mysqli_set_charset($connectionW, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);
        else if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connectionW->error);

        $style = file_get_contents("../html/search.html");

        $style = str_replace("{type}", "client", $style);
        $style = str_replace("{query}", $clientName, $style);

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("admin.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Contrats de l'entreprise : $clientName</h1>";
        echo "<table>";

        $cells = array("Contrat","Date creation","Revue","Prix HT","Payé compta","Nom du contact","Téléphone","Payé base","Commentaire","Date commentaire","Prochaine relance");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        findClientOrders($clientId);

        echo "</table><br><br><br>";
        echo "</html>";
    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
    $connectionW->close();
}

?>