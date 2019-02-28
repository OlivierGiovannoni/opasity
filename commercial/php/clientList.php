<?php

function fetchClients($author)
{
    $columns = "id,Createur,NomSociete,Addr1,Addr2,CP,Ville,Pays,TelSociete,SIRET,CodeAPE,DateCreation";
    if (isAdmin()) {

        $sqlClients = "SELECT $columns FROM webcommercial_client ORDER BY DateCreation DESC;";
        $rowsClients = querySQL($sqlClients, $GLOBALS['connection']);

        foreach ($rowsClients as $rowClient) {

            $clientId = $rowClient['id'];
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
            $createdBy = $rowClient['Createur'];

            $contactsLink = generateLink("clientContacts.php?id=" . $clientId, $clientName);

            $cells = array($contactsLink, $address1, $address2, $zipCode, $city, $country, $phone, $siretCode, $apeCode, $createdAt, $createdBy);
            $cells = generateRow($cells);
            foreach ($cells as $cell)
                echo $cell;
        }
    } else {

        $userId = getUserId($author);
        $sqlIds = "SELECT Client_id FROM webcommercial_permissions_client WHERE User_id='$userId' AND Autorisation=1;";
        $rowsIds = querySQL($sqlIds, $GLOBALS['connection']);

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
}

require_once "helper.php";

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

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Mes clients", $style);
        echo $style;

        echo "<h2>Liste des clients</h2>";
        echo "<table>";

        $createImage = generateImage("../png/add.png", "Nouveau client", 24, 24);
        $createLink = generateLink("../html/clientCreate.html", $createImage);
        echo $createLink;

        if (isAdmin())
            $cells = array("Nom du client","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création","Crée par","Interagir");
        else
            $cells = array("Nom du client","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création","Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        fetchClients($_COOKIE['author']);

        echo "</table><br><br><br>";
    } else
        header("Location: index.php");

    $connection->close();
}

?>
