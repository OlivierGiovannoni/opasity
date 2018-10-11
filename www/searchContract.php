<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$contractId = filter_input(INPUT_POST, "contractId"); // CODE CONTRAT ex: GI4468
$getPaid = filter_input(INPUT_POST, "paidBool");
$contractId = testInput($contractId);
$supportPart = substr($contractId, 0, 2); // PARTIE SUPPORT ex: GI
$contractPart = substr($contractId, 2, 4); // PARTIE CONTRAT ex: 4468

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function findSameReview($infoId)
{

    $sql = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    if ($result = $GLOBALS['connection']->query($sql)) {

        $row = mysqli_fetch_array($result);
        $finalId = $row['Revue_id'];
        $sql = "SELECT Nom FROM webcontrat_revue WHERE id='$finalId';";
        if ($result = $GLOBALS['connection']->query($sql)) {

            $row = mysqli_fetch_array($result);
            $finalName = $row['Nom'];
            return ($finalName);
        } else {
            echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
}

function findContract()
{

    $sql = "SELECT Commande FROM webcontrat_contrat;";
    if ($result = $GLOBALS['connection']->query($sql)) {

        while ($row = mysqli_fetch_array($result)) {

            $supportRet = substr_compare($row['Commande'], $GLOBALS['supportPart'], 2, 2, TRUE);
            $contractRet = substr_compare($row['Commande'], $GLOBALS['contractPart'], 10, 4, TRUE);

            if (!$supportRet && !$contractRet) {
                $orderId = $row['Commande'];
                echo "<tr><td><a href=\"orderDetails.php\">" . $orderId . "</a></td>";
                echo "<td><a href=\"reviewOrders.php\">" . findSameReview($orderId) . "</a></td></tr>";
            }
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

$style = file_get_contents("style.html");
echo $style;
//    <td><a href="#"></a></td>
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Contrat</th>";
echo "<th>Revue</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    findContract();
}

echo "</table>";

?>
