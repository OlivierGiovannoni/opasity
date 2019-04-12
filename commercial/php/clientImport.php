<?php

function searchClients($name, $userId)
{
    $columns = "DateCreation,NomSociete,Addr1,Addr2,CP,Ville,Pays,TelSociete,SIRET,CodeAPE";
    $sqlClients = "SELECT Client_id,DateAcces FROM webcommercial_permissions_client WHERE User_id='$userId' AND Autorisation=1;";
    $rowsIds  = querySQL($sqlClients, $GLOBALS['connection']);

    foreach ($rowsIds as $rowId) {

        $clientId = $rowId['Client_id'];
        $sqlClient = "SELECT $columns FROM webcommercial_client WHERE id='$clientId' ORDER BY DateCreation DESC;";
        $rowClient = querySQL($sqlClient, $GLOBALS['connection'], true, true);

        $clientName = $rowClient['NomSociete'];
        $clientNameChk = skipAccents($clientName);
        $cludes = stristr($clientNameChk, $name);

        if ($cludes !== FALSE) {

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

            $importImage = generateImage("../png/add.png", "Importer", 24, 24);
            $importLink = generateLink("clientImport.php?clientId=" . $clientId, $importImage);

            $reviewsLink = generateLink("clientReviews.php?clientId=" . $clientId, $clientName);

            $contactsImage = generateImage("../png/client.png", "Contacts", 24, 24);
            $contactsLink = generateLink("clientContacts.php?id=" . $clientId, $contactsImage);

            $cells = array($importLink, $reviewsLink, $contactsLink, $address1, $address2, $zipCode, $city, $country, $phone, $siretCode, $apeCode, $createdAt);
            $cells = generateRow($cells);
            foreach ($cells as $cell)
                echo $cell;
        }
    }
    
}

function importClient($userId, $clientId, $reviewId)
{
    $createdAt = date("Y-m-d");

    $rowNames = "Client_id,User_id,DateAcces,Autorisation";
    $rowValues = "'$clientId','$userId','$createdAt',1";
    $sqlPerm = "INSERT INTO webcommercial_permissions_client ($rowNames) VALUES ($rowValues);";
    querySQL($sqlPerm, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.

    $rowNames = "Client_id,Revue_id";
    $rowValues = "'$clientId','$reviewId'";
    $sqlReview = "INSERT INTO webcommercial_client_revue ($rowNames) VALUES ($rowValues);";
    querySQL($sqlReview, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.

    header("Location: reviewClients.php?reviewId=" . $reviewId);    
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$query = filter_input(INPUT_GET, "name");
$reviewId = $_SESSION['reviewId'];
$clientId = filter_input(INPUT_GET, "clientId");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $userId = getUserId($_SESSION['author']);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Importer", $style);
        echo $style;

        $input = file_get_contents("../html/searchbar.html");
        $input = str_replace("{query}", $query, $input);
        echo $input;

        echo "<table>";

        $cells = array("Importer","Nom de l'entreprise","Contacts","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        if (isset($clientId))
            importClient($userId, $clientId, $reviewId);
        else
            searchClients($query, $userId);
    }
    else
        header("Location: index.php");
    $connection->close();
}

?>
