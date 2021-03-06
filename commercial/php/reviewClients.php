<?php

function reviewClients($reviewId)
{
    $columns = "Client_id";
    $sqlClients = "SELECT $columns FROM webcommercial_client_revue WHERE Revue_id='$reviewId';";
    $rowsClients = querySQL($sqlClients, $GLOBALS['connection']);

    foreach ($rowsClients as $rowClient) {

        $clientId = $rowClient['Client_id'];
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

        $commentData = selectLastComment($reviewId, $clientId);
        $comment = $commentData['text'];
        $dateNextYMD = $commentData['next'];

        if (isDateValid($dateNextYMD)) {

            $dateNext = date("d/m/Y", strtotime($dateNextYMD));
            $dateNext = generateLink("searchDate.php?dueDate=" . $dateNextYMD, $dateNext);
        }
        $clientLink = generateLink("commentList.php?clientId=" . $clientId . "&reviewId=" . $reviewId, $clientName);

        $contactsImage = generateImage("../png/client.png", "Contacts", 24, 24);
        $contactsLink = generateLink("clientContacts.php?id=" . $clientId, $contactsImage);

        $cells = array($clientLink, $contactsLink, $address1, $address2, $zipCode, $city, $country, $phone, $siretCode, $apeCode, $createdAt, $comment, $dateNext);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE READ

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$reviewId = filter_input(INPUT_GET, "reviewId");
$_SESSION['reviewId'] = $reviewId;

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");
        $charsetR = mysqli_set_charset($connectionR, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);
        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);

        $reviewName = getReviewName($reviewId);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Clients dans la revue $reviewName", $style);
        echo $style;

        echo "<h2>Clients dans la revue $reviewName</h2>";
        echo "<table>";

        $createImage = generateImage("../png/add.png", "Nouveau client", 24, 24);
        $createLink = generateLink("clientCreate.php?reviewId=" . $reviewId, $createImage);
        echo $createLink;

        $importLink = generateLink("../html/clientSearch.html", "Importer client existant");
        echo $importLink;

        $cells = array("Nom du client","Contacts","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création","Commentaire","Prochaine relance");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        reviewClients($reviewId);

        echo "</table><br><br><br>";
    } else
        header("Location: index.php");

    $connection->close();
    $connectionR->close();
}


?>
