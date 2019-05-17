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

        $column = "Revue_id";
        $sqlReview = "SELECT $column FROM webcommercial_client_revue WHERE Client_id='$clientId' ORDER BY id DESC;";
        $rowReview = querySQL($sqlReview, $GLOBALS['connection'], true, true);
        $reviewId = $rowReview['Revue_id'];

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
        $reviewsImage = generateImage("../png/review.png", "Revues", 24, 24);
        $reviewsLink = generateLink("clientReviews.php?clientId=" . $clientId, $clientName);

        $contactsImage = generateImage("../png/client.png", "Contacts", 24, 24);
        $contactsLink = generateLink("clientContacts.php?id=" . $clientId, $contactsImage);

        $cells = array($reviewsLink, $contactsLink, $address1, $address2, $zipCode, $city, $country, $phone, $siretCode, $apeCode, $createdAt, $comment, $dateNext);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

session_start();

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

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $username = $_SESSION['author'];
        $userId = getUserId($username);

        $input = file_get_contents("../html/clientSearch.html");
        $input = str_replace("{query}", "", $input);
        $input = str_replace("{replace}", "false", $input);
        echo $input;

        echo "<h2>Mes clients</h2>";
        echo "<table>";

        $cells = array("Nom de l'entreprise","Contacts","Adresse 1","Adresse 2","Code postal","Ville","Pays","Téléphone","SIRET","Code APE","Date création","Commentaire","Prochaine relance");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        userClients($userId);
    } else
        header("Location: index.php");
    $connection->close();
}

?>
