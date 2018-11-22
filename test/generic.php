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

// (String, String, Bool) { return (Array); }
function sqlQuery($sql, $connection, $hasResults)
{
    // If there is no MySQL query error
    if ($result = $GLOBALS[$connection]->query($sql)) {

        // If the query returns results...
        if ($hasResults === true) {

            // ...fetch ALL rows into an array...
            $rows = mysqli_fetch_all($result);
            // ...and return it, to be processed by the parents function.
            return ($rows);
        } else {
            // Else just return NULL because it's empty anyway.
            return (NULL);
        }
    }
    echo "MySQL query error: " . $sql . ": " . $GLOBALS[$connection]->error;
    return (NULL);
}

// (String, String, String, String) { return (String); }
function generateInput($type, $name, $id, $value)
{
    // Partially hardcoded input generator.
    $input = "<input type=\"" . $type . "\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\">";
    // This returned data must be pushed into an array in the parent function.
    return ($input);
}

// (String, String, String, Array) { return (Array); }
function generateForm($target, $action, $method, $inputs)
{
    $form = array();
    $formOpen = "<form target=\"" . $target . "\"action=\"" . $action . "\"method=\"" . $method . "\">";
    $form = array_push($form, $formOpen);
    // Take every input.
    foreach ($inputs as $input) {

        // Push every <input> into the form array.
        $form = array_push($form, $input);
    }
    $formClose = "</form>";
    $form = array_push($form, $formClose);
    // Finished creating the form, send it back.
    return ($form);
}

// (Array, String) { return (Array); }
function generateTable($rows, $type)
{
    $table = array();
    // New row.
    $table = array_push($table, "<tr>");

    $typeOpen = "<{$type}>";
    $typeClose = "</{$type}>";
    foreach ($rows as $row) {

        // Add <??> and </??> to the row.
        $final = $typeOpen . $row . $typeClose;
        // Push the complete row in the array.
        $table = array_push($table, $final);
    }
    // End row.
    $table = array_push($table, "</tr>");
    return ($table);
}

function getPhoneNumber($orderId, $clientId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = sqlQuery($sqlComment, "connectionW", true);

    if ($rowComment['NumTelephone'] == "") {
        $sqlPhone = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY id DESC;";
        $rowPhone = sqlQuery($sqlPhone, "connectionR", true);
        return ($rowPhone['Tel']);
    }
    return ($rowComment['NumTelephone']);
}

function selectLastComment($orderId, $orderIdShort, $paidStr)
{
    $sqlComment = "SELECT Commentaire_id,Date,Commentaire FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";

    $rowComment = sqlQuery($sqlComment, "connectionW", true);

    echo "<td>" . $rowComment['Commentaire'] . "</td>";
    echo "<td>" . $rowComment['Date'] . "</td></tr>";
}

function getOrderDetails($orderId, $orderIdShort)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";

    $rowOrder = sqlQuery($sqlOrder, "connectionR", true);
    while ($rowOrder) {

        $orderFull = $rowOrder['Commande'];

        $clientId = $rowOrder['Client_id'];
        $priceRaw = $rowOrder['PrixHT'];
        echo "<td>" . $priceRaw . "</td>";
        if ($rowOrder['Reglement'] == "R")
            echo "<td id=\"isPaid\">Oui</td>";
        else if ($rowOrder['Reglement'] == "A")
            echo "<td id=\"isCancelled\">Annulé</td>";
        else
            echo "<td id=\"isNotPaid\">Non</td>";

        $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";

        $rowClient = sqlQuery($sqlClient, "connectionR", true);
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

    $rowReviewInfo = sqlQuery($sqlReviewInfo, "connectionR", true);
    $finalId = $rowReviewInfo['Revue_id'];
    $sqlReview = "SELECT id,Nom,Annee FROM webcontrat_revue WHERE id='$finalId';";
 
    $rowReview = sqlQuery($sqlReview, "connectionR", true);
    $finalName = $rowReview['Nom'];
    $finalId = $rowReview['id'];
    $finalYear = $rowReview['Annee'];
    $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear);
    return ($final);
}

function findOrder($supportPart, $contractPart, $contractId)
{
    $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE Commande LIKE '%$contractPart' ORDER BY DateEmission DESC LIMIT 100;";

    $rowOrder = sqlQuery($sqlOrder, "connectionR", true);
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
