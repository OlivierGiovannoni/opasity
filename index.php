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

function getOrderDetails($orderId, $orderIdShort, $final)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $orderFull = $rowOrder['Commande'];

            $clientId = $rowOrder['Client_id'];
            $priceRaw = $rowOrder['PrixHT'];

            $reviewForm = "<form target=\"_blank\" action=\"php/searchReviewOrders.php\" method=\"post\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
            $pubHidden = "<input type=\"hidden\" name=\"published\" value=\"" . $final['Pub'] . "\">";
            $reviewInput = "<input type=\"submit\" name=\"reviewName\" value=\"" . $final['Name'] . " " . $final['Year'] . "\">";
            $closeForm = "</form>";

            echo "<td>" . $reviewForm . $pubHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";
            echo "<td>" . $priceRaw . "</td>";
            $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
            if ($resultClient = $GLOBALS['connectionR']->query($sqlClient)) {

                $rowClient = mysqli_fetch_array($resultClient);
                $companyName = $rowClient['NomSociete'];
                $contactName = $rowClient['NomContact1'];

                $clientForm = "<form target=\"_blank\" action=\"php/searchClientOrders.php\" method=\"post\">";
                $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $rowClient['id'] . "\">";
                $clientInput = "<input type=\"submit\" name=\"clientName\" value=\"" . $companyName . "\">";
                $closeForm = "</form>";
                echo "<td>" . $clientForm . $clientHidden . $clientInput . $closeForm . "</td>";
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

function isItPaid($orderId)
{
    $sqlPaid = "SELECT Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultPaid = $GLOBALS['connectionR']->query($sqlPaid)) {

        $rowPaid = mysqli_fetch_array($resultPaid);
        return ($rowPaid['Reglement']);
    } else {
        echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connectionR']->error;
    }
}

function findDates($dueDate)
{
    $sqlDate = "SELECT Commentaire_id,Commentaire,Commande,Commande_courte,Date,Prochaine_relance,AdresseMail,Reglement,DernierCom FROM webcontrat_commentaire WHERE Prochaine_relance<='$dueDate' AND DernierCom=1 ORDER BY Prochaine_relance ASC;";
    if ($resultDate = $GLOBALS['connectionW']->query($sqlDate)) {

        while ($rowDate = mysqli_fetch_array($resultDate)) {

            $paid = isItPaid($rowDate['Commande']);
            if ($paid == "R")
                continue ;
            $orderId = $rowDate['Commande'];
            $orderIdShort = $rowDate['Commande_courte'];

            $commentForm = "<form target=\"_blank\" action=\"php/allComments.php\" method=\"post\" target=\"_blank\">";
            //$paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $paid . "\">";
            $paidBaseHidden = "<input type=\"hidden\" name=\"hiddenPaidBase\" value=\"" . $rowDate['Reglement'] . "\">";
            $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
            $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
            $commentInput = "<input type=\"submit\" name=\"comment\" value=\"" . $orderIdShort . "\">";            
            $closeForm = "</form>";

            echo "<td>" . $commentForm . $paidBaseHidden . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";

            $final = findReview($orderId);
            echo "<td>" . $rowDate['DernierCom'] . "</td>";
            getOrderDetails($orderId, $orderIdShort, $final);
            if ($rowDate['Reglement'] == "R")
                echo "<td id=\"isPaid\">Oui</td>";
            else
                echo "<td id=\"isNotPaid\">Non</td>";
            $mail = $rowDate['AdresseMail'];
            echo "<td><a href=\"mailto:$mail\">" . $mail . "</a></td>";
            echo "<td>" . $rowDate['Commentaire'] . "</td>";
            $newDate = date("d/m/Y", strtotime($rowDate['Date']));
            echo "<td>" . $newDate . "</td>";
            $newDate = date("d/m/Y", strtotime($rowDate['Prochaine_relance']));
            if ($newDate == "00/00/0000" || $newDate == "01/01/1970")
                echo "<td>Aucune</td>";
            else
                echo "<td>" . $newDate . "</td>";

            $deleteForm = "<form action=\"php/deleteComment.php\" method=\"post\">";
            $deleteId = "<input type=\"hidden\" name=\"commId\" value=\"" . $rowDate['Commentaire_id'] . "\">";
            $deleteSub = "<input type=\"submit\" value=\"Supprimer\" name=\"delConfirm\" onclick=\"return confirm('Supprimer le commentaire?');\"><br>";
            $closeForm = "</form>";

            echo "<td>" . $deleteForm . $deleteId . $deleteSub . $closeForm . "</td></tr>";

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

    echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Revue</th>";
    echo "<th>DernierCom</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Payé base</th>";
    echo "<th>E-mail</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "<th>Prochaine relance</th>";
    echo "<th>Supprimer commentaire</th>";
    echo "</tr>";

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    findDates($today);

    echo "</table>";
    echo "</html>";
}

?>