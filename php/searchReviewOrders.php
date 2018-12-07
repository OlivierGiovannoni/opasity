<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$reviewName = filter_input(INPUT_POST, "reviewName");
$hiddenId = filter_input(INPUT_POST, "hiddenId");
$published = filter_input(INPUT_POST, "published");
$getPaid = filter_input(INPUT_POST, "hiddenPaid");

$reviewName = testInput($reviewName);

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

        $rowPaid = mysqli_fetch_array($resultPaid);
        return ($rowPaid['Reglement']);
    } else {
        echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connectionR']->error;
    }
}

function selectLastComment($orderId, $orderIdShort, $paidStr)
{
    $sqlComment = "SELECT Commentaire_id,Commande,Reglement,Date,Commentaire,AdresseMail,Prochaine_relance FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);

        $mail = $rowComment['AdresseMail'];
        if ($rowComment['Reglement'] == "R" )
            echo "<td id=\"isPaid\">Oui</td>";
        else if ($paidStr == "R")
            echo "<td id=\"isPaid\">Oui</td>";
        else
            echo "<td id=\"isNotPaid\">Non</td>";
        echo "<td><a href=\"mailto:$mail\">" . $mail . "</a></td>";
        echo "<td>" . $rowComment['Commentaire'] . "</td>";
        echo "<td>" . date("d/m/Y", strtotime($rowComment['Date'])) . "</td>";
        echo "<td>" . date("d/m/Y", strtotime($rowComment['Prochaine_relance'])) . "</td></tr>";
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionR']->error;
    }
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

function getOrderDetails($orderId, $orderIdShort)
{
    if ($GLOBALS['getPaid'] == "on")
        $sqlOrder = "SELECT Client_id,PrixHT,Reglement,DateEmission FROM webcontrat_contrat WHERE Commande='$orderId';";
    else
        $sqlOrder = "SELECT Client_id,PrixHT,Reglement,DateEmission FROM webcontrat_contrat WHERE Commande='$orderId' AND Reglement='';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        
        while ($rowOrder = mysqli_fetch_array($resultOrder)) {

            if ($rowOrder['PrixHT'] > 0) {
                $clientId = $rowOrder['Client_id'];
                $priceRaw = $rowOrder['PrixHT'];

                $isPaid = ($rowOrder['Reglement'] == "R" ? "on" : "");
                $getPaidBase = isItPaid($orderId, "webcontrat_commentaire", "connectionW");

                $commentForm = "<form target=\"_blank\" action=\"allComments.php\" method=\"post\" target=\"_blank\">";
                $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $isPaid . "\">";
                $paidHiddenBase = "<input type=\"hidden\" name=\"hiddenPaidBase\" value=\"" . ($getPaidBase == "R" ? "on" : "") . "\">";
                $idHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $orderId . "\">";
                $idShortHidden = "<input type=\"hidden\" name=\"hiddenIdShort\" value=\"" . $orderIdShort . "\">";
                $commentInput = "<input type=\"submit\" name=\"comment\" value=\"" . $orderIdShort . "\">";            
                $closeForm = "</form>";

                $newDate = date("d/m/Y", strtotime($rowOrder['DateEmission']));
            
                echo "<tr><td>" . $commentForm . $paidHidden . $paidHiddenBase . $idHidden . $idShortHidden . $commentInput . $closeForm . "</td>";
                echo "<td>" . $newDate . "</td>";
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
                    $clientId = $rowClient['id'];
                    $phoneNb =  getPhoneNumber($orderId, $clientId);
                    $clientForm = "<form target=\"_blank\" action=\"searchClientOrders.php\" method=\"post\">";
                    $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $rowClient['id'] . "\">";
                    $clientInput = "<input type=\"submit\" name=\"clientName\" value=\"" . $companyName . "\">";
                    $closeForm = "</form>";
                    echo "<td>" . $clientForm . $clientHidden . $clientInput . $closeForm . "</td>";
                    echo "<td>" . $contactName . "</td>";
                    echo "<td>" . $phoneNb . "</td>";
                    selectLastComment($orderId, $orderIdShort, $rowOrder['Reglement']);
                } else {
                    echo "Query error: ". $sqlClient ." // ". $GLOBALS['connectionR']->error;
                }
            }
        }
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connectionR']->error;
    }
}

function findOrders($revueId)
{
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$revueId';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        $rowOrder = mysqli_fetch_all($resultOrder);
        sort($rowOrder);
        foreach ($rowOrder as $order) {

            $orderId = $order[0];
            $supportPart = substr($orderId, 2, 2);
            $contractPart = substr($orderId, 10, 4);
            $orderIdShort =  $supportPart . $contractPart;

            getOrderDetails($orderId, $orderIdShort);
        }
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connectionR']->error;
    }
    $GLOBALS['connectionR']->close();
}

function getNbOrders($revueId)
{
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$revueId';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        $rowsOrder = mysqli_num_rows($resultOrder);
        return ($rowsOrder);
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connectionR']->error;
    }    

}

function getUnitPrice($orderId)
{
    $sqlPrice = "SELECT PrixHT FROM webcontrat_contrat WHERE Commande='$orderId';";
    if ($resultPrice = $GLOBALS['connectionR']->query($sqlPrice)) {

        $rowPrice = mysqli_fetch_array($resultPrice);
        return ($rowPrice['PrixHT']);
    } else {
        echo "Query error: ". $sqlPrice ." // ". $GLOBALS['connectionR']->error;
    }
}

function getTotalPrice($revueId)
{
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$revueId';";
    if ($resultOrder = $GLOBALS['connectionR']->query($sqlOrder)) {

        $rowOrder = mysqli_fetch_all($resultOrder);
        sort($rowOrder);
        $totalPrice = 0;
        foreach ($rowOrder as $order) {

            $orderId = $order[0];
            $totalPrice += getUnitPrice($orderId);
        }
        return ($totalPrice);
    } else {
        echo "Query error: ". $sqlOrder ." // ". $GLOBALS['connectionR']->error;
    }    
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");
    
    $style = str_replace("{type}", "revue", $style);
    $style = str_replace("{query}", $reviewName, $style);

    $showPaid = file_get_contents("../html/showPaid.html");

    $currFile = basename(__FILE__);
    $showPaid = str_replace("{action.php}", $currFile, $showPaid);
    $showPaid = str_replace("{reviewName}", $reviewName, $showPaid);
    $showPaid = str_replace("{hiddenId}", $hiddenId, $showPaid);
    $showPaid = str_replace("{published}", $published, $showPaid);
    $showPaid = str_replace("{getPaid}", ($getPaid == "on" ? "" : "on"), $showPaid);
    $showPaid = str_replace("{btnText}", ($getPaid == "on" ? "Afficher tout les non-reglés" : "Afficher tout les contrats"), $showPaid);

    echo $style;
    echo "<h1>Contrats dans la revue " . $reviewName . "</h1>";
    echo "<h2 id=\"" . ($published == 1 ? "isPub" : "isNotPub") . "\">Revue" . ($published == 1 ? " parue " : " non-parue ") . "</h2>";
    echo "<h3>Nombre de contrats: " . getNbOrders($hiddenId) . "</h3>";
    echo "<h3>Chiffre d'affaire total: " . getTotalPrice($hiddenId) . "</h3>";
    echo $showPaid;
    echo "<table>";
    echo "<tr>";
    echo "<th>Contrat</th>";
    echo "<th>Date enregistrement</th>";
    echo "<th>Prix HT</th>";
    echo "<th>Payé compta</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Numéro de télephone</th>";
    echo "<th>Payé base</th>";
    echo "<th>E-mail</th>";
    echo "<th>Commentaire</th>";
    echo "<th>Date commentaire</th>";
    echo "<th>Prochaine relance</th>";
    echo "</tr>";

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    findOrders($hiddenId);

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
