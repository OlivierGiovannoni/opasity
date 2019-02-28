<?php

function userClients($userId)
{
    $columns = "DateCreation,NomSociete,Addr1,Addr2,CP,Ville,Pays,TelSociete,SIRET,CodeAPE";
    $sqlClients = "SELECT Client_id,DateAcces FROM webcommercial_permissions_client WHERE User_id='$userId' AND Autorisation=1;";
    $rowsIds  = querySQL($sqlClients, $GLOBALS['connection']);

    foreach ($rowsIds as $rowId) {

        $clientId = $rowId['Client_id'];
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

        $contactsLink = generateLink("clientContacts.php?id=" . $clientId, $clientName);

        $cells = array($contactsLink, $address1, $address2, $zipCode, $city, $country, $phone, $siretCode, $apeCode, $createdAt);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $username = $_COOKIE['author'];
        $userId = getUserId($username);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Clients de $username", $style);
        echo $style;

        echo "<h2>Liste des clients de: $username</h2>";
        echo "<table>";

        $cells = array("Nom du client","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création","Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        userClients($userId);
    } else
        header("Location: index.php");
    $connection->close();
}

?>
