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
                $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">";
                $orderForm = "<form action=\"orderDetails.php\" method=\"post\">";
                $orderInput = "<input type=\"submit\" name=\"orderId\" value=\"" . substr($orderId, 2, 2) . substr($orderId, 10, 4) . "\">";
                $final = findReview($orderId);
                $reviewForm = "<form action=\"reviewOrders.php\" method=\"post\">";
                $paidHidden = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">";
                $reviewHidden = "<input type=\"hidden\" name=\"hiddenId\" value=\"" . $final['Id'] . "\">";
                $reviewInput = "<input type=\"submit\" name=\"reviewName\" value=\"" . $final['Name'] . "\">";
                $closeForm = "</form>";

                echo "<tr><td>" . $orderForm . $darkBool . $orderInput . $closeForm . "</td>";
                echo "<td>" . $reviewForm . $darkBool . $paidHidden . $reviewHidden . $reviewInput . $closeForm . "</td></tr>";
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
echo "<th>Contrat</th>";
echo "<th>Revue</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    findOrder();
}

echo "</table>";
echo "</html>";

?>
