<?php

function listComments($clientId, $reviewId)
{
    $columns = "Commentaire_id,Commentaire,Auteur,Date,Client_id,Revue_id,Contact_id,Prochaine_relance,Fichier";
    $sqlComment = "SELECT $columns FROM webcommercial_commentaire WHERE Client_id='$clientId' AND Revue_id='$reviewId' ORDER BY Commentaire_id DESC;";
    $rowsComment = querySQL($sqlComment, $GLOBALS['connection']);

    foreach ($rowsComment as $rowComment) {

        $commId = $rowComment['Commentaire_id'];
        $comment = $rowComment['Commentaire'];
        $contact = getContactName($orderId);
        $author = $rowComment['Auteur'];
        $dateComm = date("d/m/Y", strtotime($rowComment['Date']));
        $contactId = $rowComment['Contact_id'];
        $contact = getContactData($contactId);
        $contactMail = $contact['email'];
        $phone = $contact['phone'];
        $contactName = $contact['lname'] . " " . $contact['fname'];
        $jobTitle = $contact['job'];

        $mailtoLink = generateLink($rowComment['AdresseMail'], $contactMail);
        $dateNextYMD = $rowComment['Prochaine_relance'];
        if (isDateValid($dateNextYMD)) {

            $dateNext = date("d/m/Y", strtotime($dateNextYMD));
            //$dateNext = generateLink("searchDate.php?dueDate=" . $dateNextYMD, $dateNext); //link not ready
        } else
            $dateNext = "Aucune";

        if ($rowComment['Fichier'] == "NULL")
            $fileLink = "Aucun";
        else {
            $fileImage = generateImage("../png/attachment.png", basename($rowComment['Fichier']), 24, 24);
            $fileLink = generateLink($rowComment['Fichier'], $fileImage);
        }

        $editImage = generateImage("../png/edit.png", "Modifier", 24, 24);
        $editLink = generateLink("commentEdit.php?id=" . $commId, $editImage);
        $deleteImage = generateImage("../png/delete.png", "Supprimer", 24, 24);
        $deleteLink = generateLink("commentDelete.php?id=" . $commId, $deleteImage, "_self", "return confirm('Supprimer commentaire ?')");
        $links = $editLink . " " . $deleteLink;

        $cells = array($comment, $author, $dateComm, $contactName, $jobTitle, $mailtoLink, $phone, $dateNext, $fileLink);
        if (isAuthor($author) || isAdmin())
            array_push($cells, $links);
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

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$clientId = filter_input(INPUT_GET, "clientId");
$reviewId = filter_input(INPUT_GET, "reviewId");

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

        $clientName = getClientName($clientId);
        $reviewName = getReviewName($reviewId);

        $style = file_get_contents("../html/commentList.html");
        $style = str_replace("{client}", $clientName, $style);
        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("../html/admin.html", $adminImage);
            echo $adminLink;
        }
    
        $charset = mysqli_set_charset($connection, "utf8");
        $charsetR = mysqli_set_charset($connectionR, "utf8");

        if ($charset === FALSE || $charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $companyLink = generateLink("clientReviews.php?id=" . $clientId, $clientName);
        $reviewLink = generateLink("reviewClients.php?id=" . $reviewId, $reviewName);

        echo "<h2>Client: " . $companyLink . "</h2>";
        echo "<h2>Revue: " . $reviewLink . "</h2>";

        echo "<table>";

        $cells = array("Commentaire","Auteur","Date commentaire","Nom du contact","Fonction","E-mail","Téléphone","Prochaine relance","Fichier");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $form = addCommentForm("../html/commentAdd.html", $clientId, $reviewId);
        echo $form;

        listComments($clientId, $reviewId);

        echo "</table><br><br><br>";
        echo "</html>";
    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
    $connectionR->close();
}

?>
