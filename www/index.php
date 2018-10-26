<?php

$mainHTML = file_get_contents("main.html");
echo $mainHTML;
$today = date("Y-m-d");

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function selectLastComment($orderIdShort, $orderId, $paidStr)
{
    $sqlComment = "SELECT Date,Commentaire FROM webcontrat_commentaire WHERE Commande='$orderId' AND Dernier_commentaire=1;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);

        $commentForm = "<form action=\"allComments.php\" method=\"post\" target=\"_blank\">";
        $darkHidden = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
        $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $paidStr . "\">";
        $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
        $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";

        $comment = $rowComment['Commentaire'];
        if (!$comment && $paidStr != "R") {
            $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"Nouveau commentaire\">";
        } else {
            if (strlen($comment) > 32)
                $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . substr($comment, 0, 32) . "...\">";
            else
                $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $comment . "\">";
        }
        $closeForm = "</form>";

        echo "<td>" . $commentForm . $darkHidden . $paidHidden . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";
        echo "<td>" . $rowComment['Date'] . "</td></tr>";
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

function getOrderDetails($orderId, $orderIdShort, $final)
{
    $sqlOrder = "SELECT Commande,Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultOrder = $GLOBALS['connection']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $orderFull = $rowOrder['Commande'];

            $clientId = $rowOrder['Client_id'];
            $priceRaw = $rowOrder['PrixHT'];

            $reviewForm = "<form action=\"reviewOrders.php\" method=\"post\">";
            //$paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
            $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
            $reviewInput = "<input type=\"submit\" id=\"tableSub\" name=\"reviewName\" value=\"" . $final['Name'] . "\">";
            $closeForm = "</form>";

            echo "<td>" . $reviewForm . $reviewHidden . $reviewInput . $closeForm . "</td>";
            echo "<td>" . $priceRaw . "</td>";

            $sqlClient = "SELECT NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
            if ($resultClient = $GLOBALS['connection']->query($sqlClient)) {

                $rowClient = mysqli_fetch_array($resultClient);
                $companyName = $rowClient['NomSociete'];
                $contactName = $rowClient['NomContact1'];
                echo "<td>" . $companyName . "</td>";
                echo "<td>" . $contactName . "</td>";
                selectLastComment($orderIdShort, $orderId, $rowOrder['Reglement']);
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

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

function findDates($dueDate)
{
    $sqlDate = "SELECT Commentaire,Commande,Commande_courte FROM webcontrat_commentaire WHERE Prochaine_relance='$dueDate';";
    if ($resultDate = $GLOBALS['connection']->query($sqlDate)) {

        while ($rowDate = mysqli_fetch_array($resultDate)) {

            $orderId = $rowDate['Commande'];
            $orderIdShort = $rowDate['Commande_courte'];
            echo "<tr><td>" . $orderIdShort . "</td>";
            $final = findReview($orderId);
            getOrderDetails($orderId, $orderIdShort, $final);
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $today = "2018-10-22";

    echo "<i><h1>Contrats à relancer le " . $today . ":</h1></i>";
    echo "<table style=\"width:100%\">";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Revue</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "</tr>";

    findDates($today);

    echo "</table>";
    echo "</html>";
}

?>