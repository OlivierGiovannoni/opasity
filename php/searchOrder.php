<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$contractId = filter_input(INPUT_POST, "contractId"); // CODE CONTRAT ex: GI4468
$getPaid = filter_input(INPUT_POST, "paidBool");
$darkBool = filter_input(INPUT_POST, "darkBool");

$contractId = testInput($contractId);

$supportPart = substr($contractId, 0, 2); // PARTIE SUPPORT ex: GI
$contractPart = substr($contractId, 2, 4); // PARTIE CONTRAT ex: 4468

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

$credsFile = "../credentialsW.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connectionW = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB WRITE

function splitEvery($str, $every)
{
    $mul = 1;
    for ($pos = 0; $pos < strlen($str); $pos++){
        if ($pos == ($every * $mul)) {
            $pos = strpos($str, " ", $pos);
            $str = substr_replace($str, "\n", $pos, 0);
            $mul++;
        }
    }
    return ($str);
}


function getPhoneNumber($orderId, $clientId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {
        $rowComment = mysqli_fetch_array($resultComment);

        if ($rowComment['NumTelephone'] == "") {
            $sqlPhone = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY id DESC;";
            if ($resultPhone = $GLOBALS['connectionR']->query($sqlPhone)) {
                $rowPhone = mysqli_fetch_array($resultPhone);
                return ($rowPhone['Tel']);
            } else {
                echo "Query error: ". $sqlPhone ." // ". $GLOBALS['connectionR']->error;
            }
        }
        return ($rowComment['NumTelephone']);
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connectionR']->error;
    }
}

function selectLastComment($orderId, $orderIdShort, $paidStr)
{
    $sqlComment = "SELECT Commentaire_id,Date,Commentaire FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);

        $reviewForm = "<form target=\"_blank\" action=\"allComments.php\" method=\"post\" target=\"_blank\">";
        $darkHidden = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
        $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $paidStr . "\">";
        $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
        $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
        $idLastHidden = "<input type=\"hidden\" name=\"commentId\" value=\"" . $rowComment['Commentaire_id'] . "\">";

        $comment = $rowComment['Commentaire'];
        if (!$comment) {
            $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"". ($paidStr == "R" ? "Liste" : "Nouv.") ." comm(s)\">";
        } else {
            if (strlen($comment) > 32)
                $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . splitEvery($comment, 32) . "\">";
            else
                $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $comment . "\">";
        }
        $closeForm = "</form>";

        echo "<td>" . $reviewForm . $darkHidden . $paidHidden . $idHidden . $idShortHidden . $idLastHidden . $commentInput . $closeForm . "</td>";
        echo "<td>" . $rowComment['Date'] . "</td></tr>";
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionR']->error;
    }
}

function getOrderDetails($orderId, $orderIdShort)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $orderFull = $rowOrder['Commande'];

            $clientId = $rowOrder['Client_id'];
            $priceRaw = $rowOrder['PrixHT'];
            echo "<td>" . $priceRaw . "</td>";
            if ($rowOrder['Reglement'] == "R")
                echo "<td id=\"isPaid\">Oui</td>";
            else
                echo "<td id=\"isNotPaid\">Non</td>";

            $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
            if ($resultClient = $GLOBALS['connectionR']->query($sqlClient)) {

                $rowClient = mysqli_fetch_array($resultClient);
                $companyName = $rowClient['NomSociete'];
                $contactName = $rowClient['NomContact1'];
                $phoneNb = getPhoneNumber($orderId, $clientId);
                $clientForm = "<form target=\"_blank\" action=\"searchClientOrders.php\" method=\"post\">";
                /* $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">"; */
                /* $getPaidOrders = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">"; */
                $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $rowClient['id'] . "\">";
                $clientInput = "<input style=\"font-size=11px\" type=\"submit\" id=\"tableSub\" name=\"clientName\" value=\"" . $companyName . "\">";
                $closeForm = "</form>";
                echo "<td>" . $clientForm . /* $darkBool . $getPaidOrders . */$clientHidden . $clientInput . $closeForm . "</td>";
                //echo "<td>" . $companyName . "</td>";
                echo "<td>" . $contactName . "</td>";
                echo "<td>" . $phoneNb . "</td>";
                selectLastComment($orderId, $orderIdShort, $rowOrder['Reglement']);
            }
        }
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connectionR']->error;
    }
}

function findReview($infoId)
{
    $sqlReviewInfo = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    if ($resultReviewInfo = $GLOBALS['connectionR']->query($sqlReviewInfo)) {

        $rowReviewInfo = mysqli_fetch_array($resultReviewInfo);
        $finalId = $rowReviewInfo['Revue_id'];
        $sqlReview = "SELECT id,Nom,Annee FROM webcontrat_revue WHERE id='$finalId';";
        if ($resultReview = $GLOBALS['connectionR']->query($sqlReview)) {

            $rowReview = mysqli_fetch_array($resultReview);
            $finalName = $rowReview['Nom'];
            $finalId = $rowReview['id'];
            $finalYear = $rowReview['Annee'];
            $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear);
            return ($final);
        } else {
            echo "Query error: ". $sqlReview ." // ". $GLOBALS['connectionR']->error;
        }
    } else {
        echo "Query error: ". $sqlReviewInfo ." // ". $GLOBALS['connectionR']->error;

    }
}

function findOrder($supportPart, $contractPart, $contractId)
{
    if (strlen($contractId) === 6)
        $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE Commande LIKE '__" . $supportPart . "______" . $contractPart . "';";
    else if (strlen($contractId) === 4)
        $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE Commande LIKE '%$contractId' ORDER BY DateEmission DESC LIMIT 100;";
    else if (strlen($contractId) === 2)
        $sqlOrder = "SELECT DateEmission,Commande FROM webcontrat_contrat WHERE Commande LIKE '__" . $supportPart . "%' ORDER BY DateEmission DESC LIMIT 100;";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $orderId = $rowOrder['Commande'];
            $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
            $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
            $final = findReview($orderId);

            $commentForm = "<form target=\"_blank\" action=\"allComments.php\" method=\"post\" target=\"_blank\">";
            $darkHidden = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
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

            echo "<tr><td>" . $commentForm . $darkHidden . $paidHidden . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";
            echo "<td>" . $newDate . "</td>";
            echo "<td>" . $reviewForm . $darkBool . $paidHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";
            getOrderDetails($orderId, $orderIdShort);
        }
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connectionR']->error;
    }
    $GLOBALS['connectionR']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");

    if ($darkBool == "true")
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

    echo "</table>";
    echo "</html>";
}

?>
