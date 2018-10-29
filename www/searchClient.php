<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$clientName = filter_input(INPUT_POST, "clientName");
$darkBool = filter_input(INPUT_POST, "darkBool");

$clientName = testInput($clientName);

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function findClient($clientName)
{
    $sqlClient = "SELECT id,NomSociete,Tel,NomContact1 FROM webcontrat_client WHERE NomSociete LIKE '%$clientName%';";
    if ($resultClient = $GLOBALS['connection']->query($sqlClient)) {

        while ($rowClient = mysqli_fetch_array($resultClient)) {

            echo "<tr><td>" . $rowClient['NomSociete'] . "</td>";
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
    $style = file_get_contents("search.html");

    if ($darkBool == "true")
        $style = str_replace("searchLight.css", "searchDark.css", $style);

    $style = str_replace("{type}", "client", $style);
    $style = str_replace("{query}", $clientName, $style);

    echo $style;
    echo "<i><h1>Contrats trouvés:</h1></i>";
    echo "<table style=\"width:100%\">";
    echo "<tr>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>Nom du contact</th>";
    echo "<th>Numéro de télephone</th>";
    echo "</tr>";

    findClient($clientName);

    echo "</table>";
    echo "</html>";
}


?>
