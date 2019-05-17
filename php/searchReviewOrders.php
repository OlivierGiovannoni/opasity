<?php

function findOrders($revueId, $paidBool)
{
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$revueId';";
    $rowsOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);
    sort($rowsOrder);

    foreach ($rowsOrder as $order) {

        $orderId = $order['Info_id'];
        $orderIdShort =  getOrderIdShort($orderId);

        $paid = isItPaid($orderId);

        if ($paid['compta'] == "R" && $paidBool == 1)
            continue ;

        $details = getOrderDetails($orderId);
        $phone = getPhoneNumber($orderId, $details['clientId']);
        $comment = selectLastComment($orderId);
        $dateNextYMD = $comment['reminder'];
        if (isDateValid($dateNextYMD)) {

            $dateNext = date("d/m/Y", strtotime($dateNextYMD));
            $dateNext = generateLink("searchDate.php?dueDate=" . $dateNextYMD, $dateNext);
        } else
            $dateNext = "Aucune";

        $orderLink = generateLink("commentList.php?id=" . $orderId, $orderIdShort);
        $companyLink = generateLink("searchClientOrders.php?id=" . $details['clientId'], $details['companyName']);
        $mailtoLink = generateLink("mailto:" . $comment['email'], $comment['email']);

        $paidCompta = ($paid['compta'] == "R" ? "Oui" : "Non");
        $paidBase = ($paid['base'] ==  "R" ? "Oui" : "Non");

        $cells = array($orderLink, $details['creation'], $details['priceRaw'], $paidCompta, $companyLink, $details['contactName'], $phone, $paidBase, $mailtoLink, $comment['text'], $comment['date'], $dateNext);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;

    }
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE READ

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

$reviewId = filter_input(INPUT_GET, "id");
$getPaid = filter_input(INPUT_GET, "paid");

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
        $style = str_replace("{type}", "revue", $style);

        $review = getReviewInfo($reviewId);
        $reviewName = $review['name'] . " " . $review['year'];
        $published = $review['published'];
        $pubColor = ($published == 1 ? "isPub" : "isNotPub");
        $pubText = ($published == 1 ? "parue" : "non-parue");

        $style = str_replace("{query}", $reviewName, $style);

        $paidText = ($getPaid == 0 ? "Afficher tout les contrats" : "Afficher tout les contrats non-reglés");
        $paidBool = ($getPaid == 1 ? 0 : 1);
        $paidLink = generateLink("searchReviewOrders.php?id=" . $reviewId . "&paid=" . $paidBool, $paidText, "_self");

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("userList.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Contrats dans la revue " . $reviewName . "</h1>";
        echo "<h2 id=\"" . $pubColor . "\">Revue " . $pubText . "</h2>";
        echo "<h3>Nombre de contrats: " . getNbOrders($reviewId) . "</h3>";
        echo "<h3>Chiffre d'affaire total: " . getTotalPrice($reviewId) . "</h3>";
        echo "<h3>Chiffre d'affaire non-paye: " . getUnpaidPrice($reviewId) . "</h3>";
        echo $paidLink;
        echo "<table>";

        $cells = array("Contrat","Date enregistrement","Prix HT","Payé compta","Nom de l'entreprise","Nom du contact","Numéro de téléphone","Payé base","E-mail","Commentaire","Date commentaire","Prochaine relance");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        findOrders($reviewId, $paidBool);

        echo "</table><br><br><br>";
    } else
        displayLogin("Veuillez vous connecter.");
}

?>
