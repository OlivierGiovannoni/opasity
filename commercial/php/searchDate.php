<?php

function findDates($dueDate)
{
    $columns = "Commentaire_id,Commentaire,Auteur,Date,Client_id,Revue_id,Contact_id,Prochaine_relance,Acceptee,Fichier";
    $sqlDate = "SELECT $columns FROM webcommercial_commentaire WHERE Prochaine_relance<='$dueDate' AND DernierCom=1;";
    $rowsDate = querySQL($sqlDate, $GLOBALS['connection']);

    foreach ($rowsDate as $rowDate) {

        $author = $rowDate['Auteur'];

        if (!isAuthor($author))
            continue ;

        $clientId = $rowDate['Client_id'];
        $reviewId = $rowDate['Revue_id'];
        $contactId = $rowDate['Contact_id'];
        $commId = $rowDate['Commentaire_id'];
        $comment = $rowDate['Commentaire'];

        $clientName = getClientName($clientId);
        $clientLink = generateLink("commentList.php?reviewId=" . $reviewId . "&clientId=" . $clientId, $clientName);

        $clientReviewsImage = generateImage("../png/review.png", "Revues de $clientName", 24, 24);
        $clientReviewsLink = generateLink("clientReviews.php?clientId=" . $clientId, $clientReviewsImage);

        $reviewTitle = getReviewName($reviewId);
        $reviewLink = generateLink("reviewClients.php?reviewId=" . $reviewId, $reviewTitle);

        $dateComm = date("d/m/Y", strtotime($rowDate['Date']));

        $contact = getContactData($contactId);
        $contactMail = $contact['email'];
        $phone = $contact['phone'];
        $contactName = $contact['lname'] . " " . $contact['fname'];
        $jobTitle = $contact['job'];

        $mailtoLink = generateLink($contactMail, $contactMail);
        $dateNextYMD = $rowDate['Prochaine_relance'];
        if (isDateValid($dateNextYMD)) {

            $dateNext = date("d/m/Y", strtotime($dateNextYMD));
            $dateNext = generateLink("searchDate.php?dueDate=" . $dateNextYMD, $dateNext);
        } else
            $dateNext = "Aucune";

        if ($rowDate['Fichier'] == "NULL")
            $fileLink = "Aucun";
        else {
            $fileImage = generateImage("../png/attachment.png", basename($rowDate['Fichier']), 24, 24);
            $fileLink = generateLink($rowDate['Fichier'], $fileImage);
        }

        $editImage = generateImage("../png/edit.png", "Modifier", 24, 24);
        $editLink = generateLink("commentEdit.php?id=" . $commId, $editImage);
        $deleteImage = generateImage("../png/delete.png", "Supprimer", 24, 24);
        $deleteLink = generateLink("commentDelete.php?id=" . $commId, $deleteImage, "_self", "return confirm('Supprimer commentaire ?')");

        $clientLinks = $clientLink . " " . $clientReviewsLink;
        $links = $editLink . " " . $deleteLink;

        $cells = array($clientLinks, $reviewLink, $contactName, $jobTitle, $phone, $mailtoLink, $comment, $dateComm, $dateNext, $links);
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

$dueDate = filter_input(INPUT_GET, "dueDate");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/search.html");

        $style = str_replace("{type}", "date", $style);
        $style = str_replace("{query}", $dueDate, $style);

        $newDate = date("d/m/Y", strtotime($dueDate));

        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("userList.php", $adminImage);
            echo $adminLink;
        }

        echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
        echo "<table>";

        $cells = array("Contrat","Revue","PrixHT","Payé compta","Nom de l'entreprise","Nom du contact","Payé base","Commentaire","Date commentaire");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $charsetR = mysqli_set_charset($connectionR, "utf8");
        $charsetW = mysqli_set_charset($connection, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);
        else if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        findDates($dueDate);

        echo "</table><br><br><br>";
        echo "</html>";


    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
    $connectionR->close();
}

?>
