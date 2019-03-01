<?php

function reviewClients($reviewId)
{
    $columns = "Revue_id,Gerant_id";
    $sqlClients = "SELECT $columns FROM webcommercial_client_revue WHERE Client_id='$clientId';";
    $rowsClients = querySQL($sqlClients, $GLOBALS['connection']);

    foreach ($rowsClients as $rowClient) {

        $clientId = $rowClient['id'];
        $columns = "DateCreation,NomSociete,Addr1,Addr2,CP,Ville,Pays,TelSociete,SIRET,CodeAPE";
        $sqlClient = "SELECT $columns FROM webcommercial_client WHERE id='$clientId' ORDER BY DateCreation DESC;";
        $rowClient = querySQL($sqlClient, $GLOBALS['connection'], true, true);

        $clientName = $rowClient['NomSociete'];
        $address1 = $rowClient['Addr1'];
        $address2 = $rowClient['Addr2'];
        $zipCode = $rowClient['CP'];
        $city = $rowClient['Ville'];
        $country = $rowClient['Pays'];
        $phone = $rowClient['TelSociete'];
        $siretCode = $rowClient['SIRET'];
        $apeCode = $rowClient['CodeAPE'];
        $createdAtYMD = $rowClient['DateCreation'];
        $createdAt = date("d/m/Y", strtotime($createdAtYMD));

        $contactsImage = generateImage("../png/client.png", "Contacts", 24, 24);
        $contactsLink = generateLink("clientContacts.php?id=" . $clientId, $contactsImage);

        $cells = array($clientName, $contactsLink, $address1, $address2, $zipCode, $city, $country, $phone, $siretCode, $apeCode, $createdAt);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$reviewId = filter_input(INPUT_GET, "reviewId");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $reviewName = getReviewName($reviewId);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Clients dans la revue $reviewName", $style);
        echo $style;

        echo "<h2>Clients dans la revue $reviewName</h2>";
        echo "<table>";

        $createImage = generateImage("../png/add.png", "Nouveau client", 24, 24);
        $createLink = generateLink("../html/clientCreate.php?reviewId=" . $reviewId, $createImage);
        echo $createLink;

        $cells = array("Nom du client","Contacts","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        reviewClients($reviewId);

        echo "</table><br><br><br>";
    } else
        header("Location: index.php");

    $connection->close();
}


?>
