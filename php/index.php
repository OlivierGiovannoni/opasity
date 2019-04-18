<?php

function findDates($dueDate)
{
    $columns = "Commentaire_id,Commentaire,Auteur,Commande,Date,Prochaine_relance,AdresseMail,Reglement,Fichier";
    $sqlDate = "SELECT $columns FROM webcontrat_commentaire WHERE Prochaine_relance<='$dueDate' AND DernierCom=1 ORDER BY Prochaine_relance ASC;";
    $rowsDate = querySQL($sqlDate, $GLOBALS['connectionW']);

    foreach ($rowsDate as $rowDate) {

        $orderId = $rowDate['Commande'];
        $paid = isItPaid($orderId);

        if ($paid['compta'] === "R")
            continue ;

        $orderIdShort = getOrderIdShort($orderId);
        $comment = $rowDate['Commentaire'];
        $commId = $rowDate['Commentaire_id'];
        $author = $rowDate['Auteur'];
        $file = $rowDate['Fichier'];
        $fileShort = basename($file);
        $paidBase = ($paid['base'] === "R" ? "Oui" : "Non");
        $final = findReview($orderId); // Find review details related to the order
        $details = getOrderDetails($orderId); // Get order and client details related to the order

        $orderLink = generateLink("commentList.php?id=" . $orderId, $orderIdShort); // Generate <a> link with orderId as id and orderIdShort as text
        $companyLink = generateLink("searchClientOrders.php?id=" . $details['clientId'], $details['companyName']); // Generate <a> link with clientId as id and companyName as text
        $reviewLink = $final['Name'] . " " . $final['Year'];
        $reviewLink = generateLink("searchReviewOrders.php?id=" . $final['Id'], $reviewLink); // Generate <a> link with reviewId as id and reviewName Year as text
        $mailtoLink = generateLink("mailto:" . $rowDate['AdresseMail'], $rowDate['AdresseMail']); // Generate <a> mailto link
        $dateComm = date("d/m/Y", strtotime($rowDate['Date'])); // Comment date
        $dateNextYMD = $rowDate['Prochaine_relance']; // Next reminder date
        if (isDateValid($dateNextYMD)) {

            $dateNext = date("d/m/Y", strtotime($dateNextYMD));
            $dateNext = generateLink("searchDate.php?dueDate=" . $dateNextYMD, $dateNext);
        } else
            $dateNext = "Aucune";
        $attachmentImage = generateImage("../png/attachment.png", $fileShort, 24, 24); // Create attachment icon
        $attachmentLink = generateLink($file, $attachmentImage); // Create <a> link that leads to attached file
        if ($file !== "NULL" && $file !== "")
            $comment = $attachmentLink . " " . $comment;
        $editImage = generateImage("../png/edit.png", "Modifier", 24, 24); // Create edit icon
        $editLink = generateLink("commentEdit.php?id=" . $commId, $editImage); // Create <a> link to edit comment
        $deleteImage = generateImage("../png/delete.png", "Supprimer", 24, 24); // Create delete icon
        $deleteLink = generateLink("commentDelete.php?id=" . $commId, $deleteImage, "_self", "return confirm('Supprimer commentaire ?')"); // Create <a> link to delete comment
        $links = $editLink . " " . $deleteLink;

        $cells = array($orderLink, $reviewLink, $details['priceRaw'], $companyLink, $paidBase, $mailtoLink, $comment, $author, $dateComm, $dateNext);
        if (isAuthor($author) || isAdmin()) // If user is Author or Admin
            array_push($cells, $links); // Display edit/delete links
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

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charsetR = mysqli_set_charset($connectionR, "utf8");
        $charsetW = mysqli_set_charset($connectionW, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);
        else if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connectionW->error);

        $indexHTML = file_get_contents("../html/index.html");
        echo $indexHTML;
        $toolsHTML = file_get_contents("../html/tools.html");

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("userList.php", $adminImage);
            echo $adminLink;
        }

        echo $toolsHTML;

        $today = date("Y-m-d");
        $newDate = date("d/m/Y", strtotime($today));

        echo "<h1>Contrats à relancer le " . $newDate . ":</h1>";
        echo "<table>";

        $cells = array("Contrat","Revue","PrixHT","Nom de l'entreprise","Payé base","E-mail","Commentaire","Auteur","Date commentaire","Prochaine relance","Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        findDates($today);

        echo "</table><br><br><br>";
    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
    $connectionW->close();
}

?>