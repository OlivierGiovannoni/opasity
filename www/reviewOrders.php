
<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$reviewName = filter_input(INPUT_POST, "reviewName");
$darkBool = filter_input(INPUT_POST, "darkBool");
$hiddenId = filter_input(INPUT_POST, "hiddenId");
$getPaid = filter_input(INPUT_POST, "hiddenPaid");

$reviewName = testInput($reviewName);
$hiddenId = testInput($hiddenId);

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

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

function selectLastComment($orderId, $orderIdShort, $paidStr)
{
    $sqlComment = "SELECT Date,Commentaire FROM webcontrat_commentaire WHERE Commande='$orderId' AND Dernier_commentaire=1;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);

        $reviewForm = "<form action=\"allComments.php\" method=\"post\" target=\"_blank\">";
        $darkHidden = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
        $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $paidStr . "\">";
        $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
        $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";

        $comment = $rowComment['Commentaire'];
        if (!$comment && $paidStr != "R") {
            $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"Nouveau commentaire\">";
        } else {
            if (strlen($comment) > 32)
                $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . splitEvery($comment, 32) . "...\">";
            else
                $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $comment . "\">";    
        }
        $closeForm = "</form>";

        echo "<td>" . $reviewForm . $darkHidden . $paidHidden . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";
        echo "<td>" . $rowComment['Date'] . "</td>";
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connection']->error;
    }
}

function getPhoneNumber($orderId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' AND Dernier_commentaire=1;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {
        $rowComment = mysqli_fetch_array($resultComment);
        return ($rowComment['NumTelephone']);
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

function getOrderDetails($orderId, $orderIdShort)
{
    if ($GLOBALS['getPaid'] == "on")
        $sqlOrder = "SELECT Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    else
        $sqlOrder = "SELECT Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId' AND Reglement='';";
    if ($resultOrder = $GLOBALS['connection']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            $clientId = $rowOrder['Client_id'];
            $priceRaw = $rowOrder['PrixHT'];

            $commentForm = "<form action=\"allComments.php\" method=\"post\" target=\"_blank\">";
            $darkHidden = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
            $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
            $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
            $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
            $commentInput = "<input type=\"submit\" id=\"tableSub\" name=\"comment\" value=\"" . $orderIdShort . "\">";            
            $closeForm = "</form>";

            echo "<td>" . $commentForm . $darkHidden . $paidHidden . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";

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
                $phoneNb =  getPhoneNumber($orderId);
                echo "<td>" . $companyName . "</td>";
                echo "<td>" . $contactName . "</td>";
                echo "<td>" . $phoneNb . "</td>";
                selectLastComment($orderId, $orderIdShort, $rowOrder['Reglement']);
            } else {
                echo "Query error: ". $sqlClient ." // ". $GLOBALS['connection']->error;
            }
        }
    } else {
            echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connection']->error;
    }
}

function findOrders()
{
    $hiddenId = $GLOBALS['hiddenId'];
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$hiddenId';";
    if ($result = $GLOBALS['connection']->query($sqlOrder)) {

        while ($rowOrder = mysqli_fetch_array($result)) {

            $orderId = $rowOrder['Info_id'];
            $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);

            getOrderDetails($orderId, $orderIdShort);
        }
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("search.html");

    if ($darkBool == "true")
        $style = str_replace("searchLight.css", "searchDark.css", $style);

    $style = str_replace("{type}", "revue", $style);
    $style = str_replace("{query}", $reviewName, $style);

    echo $style;
    echo "<i><h1>Contrats dans la revue " . $reviewName . "</h1></i>";
    echo "<table style=\"width:100%\">";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Payé</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Numéro de télephone</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "</tr>";

    findOrders();

    echo "</table>";
    echo "</html>";
}

?>
