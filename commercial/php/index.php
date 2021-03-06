<?php

function findDates($dueDate)
{
    $columns = "Commentaire_id,Commentaire,Auteur,Date,Client_id,Revue_id,Contact_id,Prochaine_relance,Acceptee,Fichier";
    $sqlDate = "SELECT $columns FROM webcommercial_commentaire WHERE Prochaine_relance<='$dueDate' AND DernierCom=1 ORDER BY Prochaine_relance ASC;";
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

        $file = $rowDate['Fichier'];
        if ($file === "NULL")
            $fileLink = "Aucun";
        else {

            $fileShort = basename($file);
            $attachmentImage = generateImage("../png/attachment.png", $fileShort, 24, 24);
            $attachmentLink = generateLink($file, $attachmentImage);
            $comment = $attachmentLink . " " . $comment;
            $fileImage = generateImage("../png/attachment.png", $fileShort, 24, 24);
            $fileLink = generateLink($file, $fileImage);
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

        $indexHTML = file_get_contents("../html/index.html");
        echo $indexHTML;
        $toolsHTML = file_get_contents("../html/tools.html");

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("../html/admin.html", $adminImage);
            echo $adminLink;
        }

        if (isMask()) {

            $unmaskImage = generateImage("../png/user.png", "Revenir sur mon compte", "_self");
            $unmaskLink = generateLink("./userMaskOff.php", $unmaskImage);
            echo $unmaskLink;
        }

        echo $toolsHTML;

        $today = date("Y-m-d");
        $newDate = date("d/m/Y", strtotime($today));

        echo "<h1>Clients à relancer le " . $newDate . ":</h1>";
        echo "<table>";

        $cells = array("Nom de l'entreprise","Revue","Nom du contact","Fonction","Téléphone","E-mail","Commentaire","Date commentaire","Prochaine relance","Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        findDates($today);

        echo "</table><br><br><br>";
    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
    $connectionR->close();
}

?>