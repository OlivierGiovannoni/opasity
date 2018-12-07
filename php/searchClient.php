<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$clientName = filter_input(INPUT_POST, "clientName");

$clientName = testInput($clientName);

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

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB

function findClient($clientName)
{
    $sqlClient = "SELECT id,NomSociete,Tel,NomContact1 FROM webcontrat_client WHERE NomSociete LIKE '%$clientName%';";
    if ($resultClient = $GLOBALS['connection']->query($sqlClient)) {

        while ($rowClient = mysqli_fetch_array($resultClient)) {

            $clientForm = "<form target=\"_blank\" action=\"searchClientOrders.php\" method=\"post\">";
            $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $rowClient['id'] . "\">";
            $clientSubmit = "<input type=\"submit\" name=\"clientName\" value=\"" . $rowClient['NomSociete'] . "\">";
            $closeForm = "</form>";
            echo "<tr><td>" . $clientForm . $clientHidden . $clientSubmit . $closeForm . "</td>";
            echo "<td>" . $rowClient['NomContact1'] . "</td>";
            echo "<td>" . $rowClient['Tel'] . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlClient ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/search.html");

    $style = str_replace("{type}", "client", $style);
    $style = str_replace("{query}", $clientName, $style);

    echo $style;
    echo "<h1>Clients trouvés:</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Numéro de télephone</th>";
    echo "</tr>";

    $charset = mysqli_set_charset($connection, "utf8");

    if ($charset === FALSE)
        die("MySQL SET CHARSET error: ". $connection->error);

    findClient($clientName);

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
