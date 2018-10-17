<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$orderId = filter_input(INPUT_POST, "orderId");
$getPaid = filter_input(INPUT_POST, "hiddenPaid");
$darkBool = filter_input(INPUT_POST, "darkBool");

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function getOrderDetails()
{
    $orderId = $GLOBALS['orderId'];
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
                $priceTaxes = $rowOrder['PrixTTC'];
                echo "<tr><td>" . $orderId . "</td>";
                echo "<td>" . $priceRaw . "</td>";
                echo "<td>" . $priceTaxes. "</td>";

                $sqlClient = "SELECT NomSociete,NomContact1,Addr11 FROM webcontrat_client WHERE id='$clientId';";
                if ($resultClient = $GLOBALS['connection']->query($sqlClient)) {

                    $rowClient = mysqli_fetch_array($resultClient);
                    $companyName = $rowClient['NomSociete'];
                    $contactName = $rowClient['NomContact1'];
                    $address = $rowClient['Addr11'];
                    echo "<td>" . $companyName . "</td>";
                    echo "<td>" . $contactName . "</td>";
                    echo "<td>" . $address . "</td></tr>";
                }
            }
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
echo "<i><h1>Contrats trouvés:</h1></i>";
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Numéro contrat</th>";
//echo "<th>Payé</th>";
echo "<th>Prix HT</th>";
echo "<th>Prix TTC</th>";
echo "<th>Nom de l'entreprise</th>";
echo "<th>Nom du contact</th>";
echo "<th>Adresse</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    getOrderDetails();
}

echo "</table>";
echo "</html>";

?>
