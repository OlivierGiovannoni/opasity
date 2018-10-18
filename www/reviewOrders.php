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

function getOrderDetails($orderId)
{
    if ($GLOBALS['getPaid'] == "on")
        $sqlOrder = "SELECT Commande,Client_id,PrixHT,PrixTTC FROM webcontrat_contrat;";
    else
        $sqlOrder = "SELECT Commande,Client_id,PrixHT,PrixTTC FROM webcontrat_contrat WHERE Reglement='';";
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

                $sqlClient = "SELECT NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
                if ($resultClient = $GLOBALS['connection']->query($sqlClient)) {

                    $rowClient = mysqli_fetch_array($resultClient);
                    $companyName = $rowClient['NomSociete'];
                    $contactName = $rowClient['NomContact1'];
                    echo "<td>" . $companyName . "</td>";
                    echo "<td>" . $contactName . "</td></tr>";
                }
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

function findOrders()
{
    $hiddenId = $GLOBALS['hiddenId'];
    $sql = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$hiddenId';";
    if ($result = $GLOBALS['connection']->query($sql)) {

        while ($row = mysqli_fetch_array($result)) {

            $orderId = $row['Info_id'];
            $orderIdSmall = substr($orderId, 2, 2) . substr($orderId, 10, 4);

//$darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";

            echo "<tr><td>" . $orderIdSmall . "</td>";
            getOrderDetails($orderIdSmall);
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

$style = file_get_contents("search.html");

if ($darkBool)
    $style = str_replace("search.css", "dark.css", $style);

echo $style;
echo "<i><h1>Contrats dans la revue $reviewName:</h1></i>";
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Contrat</th>";
echo "<th>Prix HT</th>";
//echo "<th>Payé</th>";
echo "<th>Nom de l'entreprise</th>";
echo "<th>Nom du contact</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    findOrders();
}

echo "</table>";
echo "</html>";

?>
