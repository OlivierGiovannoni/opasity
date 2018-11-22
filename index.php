<?php

$mainHTML = file_get_contents("main.html");
echo $mainHTML;
$today = date("Y-m-d");

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

$credsFile = "./credentials.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB READ

$credsFileW = "./credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

function selectLastComment($orderIdShort, $orderId)
{
    $sqlComment = "SELECT Commentaire,AdresseMail FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);

        $mail = $rowComment['AdresseMail'];
        echo "<td><a href=\"mailto:$mail\">" . $mail . "</a></td>";
        echo "<td>" . $rowComment['Commentaire'] . "</td>";
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connectionR']->error;
    }
}

function getOrderDetails($orderId, $orderIdShort, $final)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $orderFull = $rowOrder['Commande'];

            $clientId = $rowOrder['Client_id'];
            $priceRaw = $rowOrder['PrixHT'];

            $reviewForm = "<form target=\"_blank\" action=\"reviewOrders.php\" method=\"post\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
            $pubHidden = "<input type=\"hidden\" name=\"published\" value=\"" . $final['Pub'] . "\">";
            $reviewInput = "<input type=\"submit\" id=\"tableSub\" name=\"reviewName\" value=\"" . $final['Name'] . " " . $final['Year'] . "\">";
            $closeForm = "</form>";

            echo "<td>" . $reviewForm . $reviewHidden . $reviewInput . $closeForm . "</td>";
            echo "<td>" . $priceRaw . "</td>";
            if ($rowOrder['Reglement'] == "R")
                echo "<td id=\"isPaid\">Oui</td>";
            else
                echo "<td id=\"isNotPaid\">Non</td>";

            $sqlClient = "SELECT NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
            if ($resultClient = $GLOBALS['connectionR']->query($sqlClient)) {

                $rowClient = mysqli_fetch_array($resultClient);
                $companyName = $rowClient['NomSociete'];
                $contactName = $rowClient['NomContact1'];
                echo "<td>" . $companyName . "</td>";
                selectLastComment($orderIdShort, $orderId);
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connectionR']->error;
    }
}

function findReview($infoId)
{
    $sql = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    if ($result = $GLOBALS['connectionR']->query($sql)) {

        $row = mysqli_fetch_array($result);
        $finalId = $row['Revue_id'];
        $sql = "SELECT id,Nom,Annee,Paru FROM webcontrat_revue WHERE id='$finalId';";
        if ($result = $GLOBALS['connectionR']->query($sql)) {

            $row = mysqli_fetch_array($result);
            $finalName = $row['Nom'];
            $finalId = $row['id'];
            $finalYear = $row['Annee'];
            $finalPub = $row['Paru'];
            $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear, 'Pub' => $finalPub);
            return ($final);
        } else {
            echo "Query error: ". $sql ." // ". $GLOBALS['connectionR']->error;
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connectionR']->error;
    }
}

function findDates($dueDate)
{
    //$sqlDate = "SELECT Commande,Commande_courte,Date,Prochaine_relance FROM webcontrat_commentaire WHERE Prochaine_relance<='$dueDate' AND DernierCom=1 ORDER BY Prochaine_relance DESC;";
    $sqlDate = "SELECT Commande,Commande_courte,Date,Prochaine_relance FROM webcontrat_commentaire ORDER BY Prochaine_relance DESC;";
    if ($resultDate = $GLOBALS['connectionW']->query($sqlDate)) {

        while ($rowDate = mysqli_fetch_array($resultDate)) {

            $orderId = $rowDate['Commande'];
            $orderIdShort = $rowDate['Commande_courte'];

            $commentForm = "<form target=\"_blank\" action=\"allComments.php\" method=\"post\" target=\"_blank\">";
            $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
            $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
            $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $orderIdShort . "\">";            
            $closeForm = "</form>";

            echo "<td>" . $commentForm . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";

            $final = findReview($orderId);
            getOrderDetails($orderId, $orderIdShort, $final);
            echo "<td>" . $rowDate['Date'] . "</td>";
            echo "<td>" . $rowDate['Prochaine_relance'] . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlDate ." // ". $GLOBALS['connectionR']->error;
    }
    $GLOBALS['connectionR']->close();
    $GLOBALS['connectionW']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $newDate = date("d/m/Y", strtotime($today));

    echo "<i><h1>Contrats à relancer le " . $newDate . ":</h1></i>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Revue</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Payé</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>E-mail</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "<th>Prochaine relance</th>";
    echo "</tr>";

    if (mysqli_set_charset($connectionR, "utf8") === TRUE)
        findDates($today);
    else
        die("MySQL SET CHARSET error: ". $connection->error);

    echo "</table>";
    echo "</html>";
}

?>