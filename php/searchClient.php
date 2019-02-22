<?php

function findClient($clientName)
{
    $columns = "id,NomSociete,Tel,NomContact1";
    $sqlClient = "SELECT $columns FROM webcontrat_client WHERE NomSociete LIKE '%$clientName%';";
    $rowsClient = querySQL($sqlClient, $GLOBALS['connectionR']);

    foreach ($rowsClient as $rowClient) {

        $companyName = $rowClient['NomSociete'];
        $clientId = $rowClient['id'];
        $companyLink = generateLink("searchClientOrders.php?id=" . $clientId, $companyName);
        $contactName = $rowClient['NomContact1'];
        $phone = $rowClient['Tel'];

        $cells = array($companyLink, $contactName, $phone);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE READ

$clientName = filter_input(INPUT_GET, "clientName");
$clientName = sanitizeInput($clientName);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/search.html");

        $style = str_replace("{type}", "client", $style);
        $style = str_replace("{query}", $clientName, $style);

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("userList.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Clients trouvés:</h1>";
        echo "<table>";

        $cells = array("Nom de l'entreprise","Nom du contact","Numéro de téléphone");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $charsetR = mysqli_set_charset($connectionR, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);

        findClient($clientName);

        echo "</table><br><br><br>";
        echo "</html>";

    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
}

?>
