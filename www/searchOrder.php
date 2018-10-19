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

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function findReview($infoId)
{
    $sql = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    if ($result = $GLOBALS['connection']->query($sql)) {

        $row = mysqli_fetch_array($result);
        $finalId = $row['Revue_id'];
        $sql = "SELECT id,Nom FROM webcontrat_revue WHERE id='$finalId';";
        if ($result = $GLOBALS['connection']->query($sql)) {

            $row = mysqli_fetch_array($result);
            $finalName = $row['Nom'];
            $finalId = $row['id'];
            $final = array('Name' => $finalName, 'Id' => $finalId);
            return ($final);
        } else {
            echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;

    }
}

function selectLastComment($orderIdShort, $orderId)
{
    // last comment clicked displays all comments, target="_blank"
    $sqlComment = "SELECT Date,Commentaire FROM webcontrat_commentaire WHERE Commande='$orderId' AND Dernier_commentaire=1;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);

        $reviewForm = "<form action=\"allComments.php\" method=\"post\">";
        $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
        $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
        $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $rowComment['Commentaire'] . "\">";
        $closeForm = "</form>";

        echo "<td>" . $reviewForm . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";
        echo "<td>" . $rowComment['Date'] . "</td></tr>";
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

function getOrderDetails($orderId)
{
    if ($GLOBALS['getPaid'] == "on")
        $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat;";
    else
        $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Reglement='';";
    if ($resultOrder = $GLOBALS['connection']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $orderFull = $rowOrder['Commande'];
            $orderSupport = substr($orderId, 0, 2);
            $orderNumber = substr($orderId, 2, 4);
            $orderSupport = substr_compare($orderFull, $orderSupport, 2, 2);
            $orderNumber = substr_compare($orderFull, $orderNumber, 10, 4);

            if (!$orderSupport && !$orderNumber) {

                $clientId = $rowOrder['Client_id'];
                $priceRaw = $rowOrder['PrixHT'];
                echo "<td>" . $priceRaw . "</td>";
                if ($rowOrder['Reglement'] == "R")
                    echo "<td id=\"isPaid\">Oui</td>";
                else
                    echo "<td id=\"isNotPaid\">Non</td>";

                $sqlClient = "SELECT NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
                if ($resultClient = $GLOBALS['connection']->query($sqlClient)) {

                    $rowClient = mysqli_fetch_array($resultClient);
                    $companyName = $rowClient['NomSociete'];
                    $contactName = $rowClient['NomContact1'];
                    echo "<td>" . $companyName . "</td>";
                    echo "<td>" . $contactName . "</td>";
                    selectLastComment($orderId, $rowOrder['Commande']);
                }
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

function findOrder()
{

    if ($GLOBALS['getPaid'] == "on")
        $sql = "SELECT Commande FROM webcontrat_contrat;";
    else
        $sql = "SELECT Commande FROM webcontrat_contrat WHERE Reglement='';";
    if ($result = $GLOBALS['connection']->query($sql)) {

        while ($row = mysqli_fetch_array($result)) {

            $supportRet = substr_compare($row['Commande'], $GLOBALS['supportPart'], 2, 2, TRUE);
            $contractRet = substr_compare($row['Commande'], $GLOBALS['contractPart'], 10, 4, TRUE);

            if (!$supportRet && !$contractRet) {

                $orderId = $row['Commande'];
                $orderIdShort = $GLOBALS['supportPart'] . $GLOBALS['contractPart'];
                $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
                $final = findReview($orderId);
                $reviewForm = "<form action=\"reviewOrders.php\" method=\"post\">";
                $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
                $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
                $reviewInput = "<input type=\"submit\" id=\"tableSub\" name=\"reviewName\" value=\"" . $final['Name'] . "\">";
                $closeForm = "</form>";

                echo "<tr><td>" . $orderIdShort . "</td>";
                echo "<td>" . $reviewForm . $darkBool . $paidHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";
                getOrderDetails($orderIdShort);
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

$style = file_get_contents("search.html");

if ($darkBool == "true")
    $style = str_replace("searchLight.css", "searchDark.css", $style);

echo $style;
echo "<i><h1>Contrats trouvés:</h1></i>";
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Contrat</th>";
echo "<th>Revue</th>";
echo "<th>Prix HT</th>";
echo "<th>Payé</th>";
echo "<th>Nom de l'entreprise</th>";
echo "<th>Nom du contact</th>";
echo "<th>Commentaire</th>";
echo "<th>Date commentaire</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    findOrder();
}

echo "</table>";
echo "</html>";

?>
