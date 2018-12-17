<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$dueDate = filter_input(INPUT_POST, "dueDate");

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

function isItPaid($orderId, $table, $connection)
{
    $sqlPaid = "SELECT Reglement FROM $table WHERE Commande='$orderId';";
    if ($resultPaid = $GLOBALS[$connection]->query($sqlPaid)) {

        $rows = mysqli_num_rows($resultPaid);
        if ($rows === 0)
            return ("R");
        $rowPaid = mysqli_fetch_array($resultPaid);
        return ($rowPaid['Reglement']);
    } else {
        echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connectionR']->error;
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
            if ($rowOrder['Reglement'] == "R" )
                echo "<td id=\"isPaid\">Oui</td>";
            else
                echo "<td id=\"isNotPaid\">Non</td>";
            $sqlClient = "SELECT NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
            if ($resultClient = $GLOBALS['connectionR']->query($sqlClient)) {

                $rowClient = mysqli_fetch_array($resultClient);
                $companyName = $rowClient['NomSociete'];
                $contactName = $rowClient['NomContact1'];
                echo "<td>" . $companyName . "</td>";
                echo "<td>" . $contactName . "</td>";
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
    $sqlDate = "SELECT Commentaire,Commande,Reglement,Commande_courte,Date FROM webcontrat_commentaire WHERE Prochaine_relance='$dueDate' AND DernierCom=1;";
    if ($resultDate = $GLOBALS['connectionW']->query($sqlDate)) {

        while ($rowDate = mysqli_fetch_array($resultDate)) {

            $orderId = $rowDate['Commande'];
            $orderIdShort = $rowDate['Commande_courte'];
            $getPaid = isItPaid($orderId, "webcontrat_contrat", "connectionR");
            $getPaidBase = isItPaid($orderId, "webcontrat_commentaire", "connectionW");

            $commentForm = "<form target=\"_blank\" action=\"allComments.php\" method=\"post\" target=\"_blank\">";
            $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $getPaid . "\">";
            $paidHiddenBase = "<input type=\"hidden\" name=\"hiddenPaidBase\" value=\"" . $getPaidBase . "\">";
            $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
            $idShortHidden = "<input type=\"submit\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
            $closeForm = "</form>";

            echo "<tr><td>" . $commentForm . $paidHidden . $paidHiddenBase . $idHidden . $idShortHidden . $closeForm . "</td>";
            $final = findReview($orderId);

            $reviewForm = "<form target=\"_blank\" action=\"searchReviewOrders.php\" method=\"post\">";
            $getPaidOrders = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $getPaid . "\">";
            $pubHidden = "<input type=\"hidden\" name=\"published\" value=\"" . $final['Pub'] . "\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
            $reviewInput = "<input type=\"submit\" name=\"reviewName\" value=\"" . $final['Name'] . ' ' . $final['Year'] . "\">";
            $closeForm = "</form>";

            echo "<td>" . $reviewForm . $getPaidOrders . $pubHidden . $reviewHidden . $reviewInput . $closeForm . "</td>";

            getOrderDetails($orderId, $orderIdShort);
            $newDate = date("d/m/Y", strtotime($rowDate['Date']));
            $paidCompta = isItPaid($rowDate['Commande'], "webcontrat_contrat", "connectionR");
            if ($rowDate['Reglement'] == "R")
                echo "<td id=\"isPaid\">Oui</td>";
            else if ($paidCompta == "R")
                echo "<td id=\"isPaid\">Oui</td>";                
            else
                echo "<td id=\"isNotPaid\">Non</td>";
            echo "<td>" . $rowDate['Commentaire'] . "</td>";
            echo "<td>" . $newDate . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlDate ." // ". $GLOBALS['connectionR']->error;
    }
    $GLOBALS['connectionR']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");

    $style = str_replace("{type}", "date", $style);
    $style = str_replace("{query}", $dueDate, $style);

    $newDate = date("d/m/Y", strtotime($dueDate));

    echo $style;
    echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Revue</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Payé compta</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Payé base</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "</tr>";

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    findDates($dueDate);

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
