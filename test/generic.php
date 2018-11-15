<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$contractId = filter_input(INPUT_POST, "contractId");
$getPaid = filter_input(INPUT_POST, "paidBool");

$contractId = testInput($contractId);

$supportPart = substr($contractId, 0, 2);
$contractPart = substr($contractId, 2, 4);
if (strlen($contractId) === 4)
    $contractPart = $contractId;

function credsArr($credsStr)
{
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

$credsFile = "../credentials.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB READ

$credsFileW = "../credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

// (Array, Bool, Bool, Array) { return (Array); }
function generateTable($lines, $trOpen, $trClose, $types)
{
}

// (String, String) { return (Array); }
function sqlQuery($sql, $connection)
{
    if ($result = $GLOBALS[$connection]->query($sql)) {

        $rows = mysqli_fetch_all($result);
        return ($rows);
    }
    echo "MySQL query error: " . $sql . ": " . $GLOBALS[$connection]->error;
    return (NULL);
}

// (String, String, String, String) { return (String); }
function generateInput($type, $name, $id, $value)
{
}

// (String, String, String, Array, Array, Array, Array) { return (Array); }
function generateForm($target, $action, $method, $types, $names, $ids, $values)
{
}

function getPhoneNumber($orderId, $clientId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = sqlQuery($sqlComment, "connectionW");

    if ($rowComment['NumTelephone'] == "") {
        $sqlPhone = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY id DESC;";
        $rowPhone = sqlQuery($sqlPhone, "connectionR");
        return ($rowPhone['Tel']);
    }
    return ($rowComment['NumTelephone']);
}

function selectLastComment($orderId, $orderIdShort, $paidStr)
{
    $sqlComment = "SELECT Commentaire_id,Date,Commentaire FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";

    $rowComment = sqlQuery($sqlComment, "connectionW");

    echo "<td>" . $rowComment['Commentaire'] . "</td>";
    echo "<td>" . $rowComment['Date'] . "</td></tr>";
}

function getOrderDetails($orderId, $orderIdShort)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";

    $rowOrder = sqlQuery($sqlOrder, "connectionR");
    while ($rowOrder) {

        $orderFull = $rowOrder['Commande'];

        $clientId = $rowOrder['Client_id'];
        $priceRaw = $rowOrder['PrixHT'];
        echo "<td>" . $priceRaw . "</td>";
        if ($rowOrder['Reglement'] == "R")
            echo "<td id=\"isPaid\">Oui</td>";
        else
            echo "<td id=\"isNotPaid\">Non</td>";

        $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";

        $rowClient = sqlQuery($sqlClient, "connectionR");
        $companyName = $rowClient['NomSociete'];
        $contactName = $rowClient['NomContact1'];
        $phoneNb = getPhoneNumber($orderId, $clientId);
        $clientForm = "<form target=\"_blank\" action=\"searchClientOrders.php\" method=\"post\">";
        $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $rowClient['id'] . "\">";
        $clientInput = "<input type=\"submit\" id=\"tableSub\" name=\"clientName\" value=\"" . $companyName . "\">";
        $closeForm = "</form>";
        echo "<td>" . $clientForm . $clientHidden . $clientInput . $closeForm . "</td>";
        echo "<td>" . $contactName . "</td>";
        echo "<td>" . $phoneNb . "</td>";
        selectLastComment($orderId, $orderIdShort, $rowOrder['Reglement']);
    }
}

function findReview($infoId)
{
    $sqlReviewInfo = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";

    $rowReviewInfo = sqlQuery($sqlReviewInfo, "connectionR");
    $finalId = $rowReviewInfo['Revue_id'];
    $sqlReview = "SELECT id,Nom,Annee FROM webcontrat_revue WHERE id='$finalId';";
 
    $rowReview = sqlQuery($sqlReview, "connectionR");
    $finalName = $rowReview['Nom'];
    $finalId = $rowReview['id'];
    $finalYear = $rowReview['Annee'];
    $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear);
    return ($final);
}

function findOrder($supportPart, $contractPart, $contractId)
{
    $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE Commande LIKE '%$contractPart' ORDER BY DateEmission DESC LIMIT 100;";

    $rowOrder = sqlQuery($sqlOrder, "connectionR");
    while ($rowOrder) {

        $orderId = $rowOrder['Commande'];
        $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
        $final = findReview($orderId);

        /* $types = array(); */
        /* $names = array(); */
        /* $ids = array(); */
        /* $values = array(); */
        $commentForm = "<form target=\"_blank\" action=\"allComments.php\" method=\"post\" target=\"_blank\">";
        $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
        $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
        $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
        $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $orderIdShort . "\">";

        $reviewForm = "<form target=\"_blank\" action=\"reviewOrders.php\" method=\"post\">";
        $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
        $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
        $reviewInput = "<input type=\"submit\" id=\"tableSub\" name=\"reviewName\" value=\"" . $final['Name'] . ' ' . $final['Year'] . "\">";

        $closeForm = "</form>";

        $newDate = date("d/m/Y", strtotime($rowOrder['DateEmission']));

        echo "<tr><td>" . $commentForm . $paidHidden . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";
        echo "<td>" . $newDate . "</td>";
        echo "<td>" . $reviewForm . $paidHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";
        getOrderDetails($orderId, $orderIdShort);
    }
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");

    $style = str_replace("searchLight.css", "searchDark.css", $style);
    $style = str_replace("{type}", "contrat", $style);
    $style = str_replace("{query}", $contractId, $style);

    echo $style;
    echo "<i><h1>Contrats trouvés:</h1></i>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Date enregistrement</th>";
    echo "<th>Revue</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Payé</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Numéro de télephone</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "</tr>";

    if (mysqli_set_charset($connectionR, "utf8") === TRUE)
        findOrder($supportPart, $contractPart, $contractId);
    else
        die("MySQL SET CHARSET error: ". $connection->error);
    $GLOBALS["connectionR"]->close();
    $GLOBALS["connectionW"]->close();
    echo "</table>";
    echo "</html>";
}

?>
