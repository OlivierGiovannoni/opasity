<?php

function findOrders($price, $paidBool)
{
    $price = str_replace("€", "", $price);
    if ($paidBool == 1)
        $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE PrixHT=$price AND Reglement='' ORDER BY DateEmission DESC;";
    else
        $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE PrixHT=$price ORDER BY DateEmission DESC;";
    $rowsOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);

    foreach ($rowsOrder as $rowOrder) {

        $orderId = $rowOrder['Commande'];
        $orderIdShort = getOrderIdShort($orderId);
        $final = findReview($orderId);
        $paid = isItPaid($orderId);
        $created = date("d/m/Y", strtotime($rowOrder['DateEmission']));
        $details = getOrderDetails($orderId, $orderIdShort);
        $reviewName = $final['Name'] . " " . $final['Year'];
        $phone = getPhoneNumber($orderId, $details['clientId']);
        $comment = selectLastComment($orderId);

        $orderLink = generateLink("allComments.php?id=" . $orderId, $orderIdShort);
        $reviewLink = generateLink("searchReviewOrders.php?id=" . $final['Id'], $reviewName);
        $companyLink = generateLink("searchClientOrders.php?id=" . $details['clientId'], $details['companyName']);

        $paidCompta = ($paid['compta'] == "R" ? "Oui" : "Non");
        $paidBase = ($paid['base'] ==  "R" ? "Oui" : "Non");

        $cells = array($orderLink, $created, $reviewLink, $details['priceRaw'], $paidCompta, $companyLink, $details['contactName'], $phone, $paidBase, $comment['text'], $comment['date']);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helperFunctions.php";

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

$price = filter_input(INPUT_GET, "price");
$getPaid = filter_input(INPUT_GET, "paid");
$price = sanitizeInput($price);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");

    $style = str_replace("{type}", "montant", $style);
    $style = str_replace("{query}", $price, $style);

    echo $style;

    $paidText = ($getPaid == 0 ? "Afficher tout les contrats" : "Afficher tout les contrats non-reglés");
    $paidBool = ($getPaid == 1 ? 0 : 1);
    $paidLink = generateLink("searchPrice.php?price=" . $price . "&paid=" . $paidBool, $paidText);

    echo "<h1>Contrats trouvés:</h1>";
    echo $paidLink;
    echo "<table>";

    $cells = array("Contrat","Date enregistrement","Revue","Prix HT","Payé compta","Nom de l'entreprise","Nom du contact","Numéro de téléphone","Payé base","Commentaire","Date commentaire");
    $cells = generateRow($cells, true);
    foreach ($cells as $cell)
        echo $cell;

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    findOrders($price, $paidBool);

    echo "</table><br><br><br>";
    echo "</body>";
    echo "</html>";

    $connectionR->close();
    $connectionW->close();
}

?>
