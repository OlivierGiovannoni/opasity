<?php

function findOrder($supportPart, $contractPart, $contractId)
{
    $columns = "DateEmission,Commande";
    if (strlen($contractId) === 6)
        $sqlOrder = "SELECT $columns FROM webcontrat_contrat WHERE Commande LIKE '__" . $supportPart . "______" . $contractPart . "';";
    else if (strlen($contractId) === 4)
        $sqlOrder = "SELECT $columns FROM webcontrat_contrat WHERE Commande LIKE '%" . $contractPart . "' ORDER BY DateEmission DESC;";
    else if (strlen($contractId) === 2)
        $sqlOrder = "SELECT $columns FROM webcontrat_contrat WHERE Commande LIKE '__" . $supportPart . "%' ORDER BY DateEmission DESC;";
    else
        return ;
    $rowsOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);

    foreach ($rowsOrder as $rowOrder) {
        $orderId = $rowOrder['Commande'];
        $orderIdShort = getOrderIdShort($orderId);
        $final = findReview($orderId);
        $paid = isItPaid($orderId);
        $newDate = date("d/m/Y", strtotime($rowOrder['DateEmission']));
        $details = getOrderDetails($orderId);
        $phone = getPhoneNumber($orderId, $details['clientId']);
        $comment = selectLastComment($orderId, true);

        $paidCompta = ($paid['compta'] == "R" ? "Oui" : "Non");
        $paidBase = ($paid['base'] ==  "R" ? "Oui" : "Non");

        $orderLink = generateLink("commentList.php?id=" . $orderId, $orderIdShort);
        $reviewLink = generateLink("searchReviewOrders.php?id=" . $final['Id'], $final['Name']);
        $companyLink = generateLink("searchClientOrders.php?id=" . $details['clientId'], $details['companyName']);

        $cells = array($orderLink, $newDate, $reviewLink, $details['priceRaw'], $paidCompta, $companyLink, $details['contactName'], $phone, $paidBase, $comment['text'], $comment['date']);
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
    $credentials['database']); // CONNECT TO DATABASE READ

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

$contractId = filter_input(INPUT_GET, "contractId"); // CODE CONTRAT ex: GI4468
$contractId = sanitizeInput($contractId);

$supportPart = substr($contractId, 0, 2); // PARTIE SUPPORT ex: GI
$contractPart = substr($contractId, 2, 4); // PARTIE CONTRAT ex: 4468

if (strlen($contractId) === 4)
    $contractPart = $contractId;

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/search.html");

        $style = str_replace("{type}", "contrat", $style);
        $style = str_replace("{query}", $contractId, $style);

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("userList.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Contrats trouvés:</h1>";
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

        findOrder($supportPart, $contractPart, $contractId);

        echo "</table><br><br><br>";
        echo "</body>";
        echo "</html>";


    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
    $connectionW->close();
}

?>
