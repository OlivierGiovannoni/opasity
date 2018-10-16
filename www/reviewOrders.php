<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$reviewName = filter_input(INPUT_POST, "reviewName");
$hiddenId = filter_input(INPUT_POST, "hiddenId");
$reviewName = testInput($reviewName);
$hiddenId = testInput($hiddenId);

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function findOrders()
{
    $hiddenId = $GLOBALS['hiddenId'];
    $sql = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$hiddenId';";
    if ($result = $GLOBALS['connection']->query($sql)) {

        while ($row = mysqli_fetch_array($result)) {

            $orderId = $row['Info_id'];
            $orderForm = "<form action=\"orderDetails.php\" method=\"post\">";
            $orderInput = "<input type=\"submit\" name=\"orderId\" value=\"" . substr($orderId, 2, 2) . substr($orderId, 10, 4) . "\">";
            $closeForm = "</form>";

            echo "<tr><td>" . $orderForm . $orderInput . $closeForm . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sql ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

$style = file_get_contents("search.html");
echo $style;
echo "<i><h1>Contrats dans la revue $reviewName:</h1></i>";
echo "<table style=\"width:100%\">";
echo "<tr>";
echo "<th>Contrat</th>";
echo "</tr>";

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    findOrders();
}

echo "</table>";
echo "</html>";

?>
