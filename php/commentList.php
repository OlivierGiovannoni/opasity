<?php

function listComments($orderId)
{
    $columns = "Commentaire_id,Commande,Commande_courte,Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance,Fichier,DernierCom";
    $sqlComment = "SELECT $columns FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowsComment = querySQL($sqlComment, $GLOBALS['connectionW']);

    foreach ($rowsComment as $rowComment) {

        $comment = $rowComment['Commentaire'];
        if ($comment === "             ")
            continue ;

        $commId = $rowComment['Commentaire_id'];
        $comment = $rowComment['Commentaire'];
        $contact = getContactName($orderId);
        $author = $rowComment['Auteur'];
        $dateComm = date("d/m/Y", strtotime($rowComment['Date']));
        $mailtoLink = generateLink($rowComment['AdresseMail'], $rowComment['AdresseMail']);
        $dateNextYMD = $rowComment['Prochaine_relance'];
        if (isDateValid($dateNextYMD)) {

            $dateNext = date("d/m/Y", strtotime($dateNextYMD));
            $dateNext = generateLink("searchDate.php?dueDate=" . $dateNextYMD, $dateNext);
        }
        else
            $dateNext = "Aucune";

        if ($rowComment['Fichier'] == "NULL")
            $fileLink = "Aucun";
        else
            $fileLink = generateLink($rowComment['Fichier'], basename($rowComment['Fichier']));

        $editImage = generateImage("../png/edit.png", "Modifier", 24, 24);
        $editLink = generateLink("commentEdit.php?id=" . $commId, $editImage);
        $deleteImage = generateImage("../png/delete.png", "Supprimer", 24, 24);
        $deleteLink = generateLink("commentDelete.php?id=" . $commId, $deleteImage, "_self", "return confirm('Supprimer commentaire ?')");
        $links = $editLink . " " . $deleteLink;

        if ($orderId === "") {

            $commOrderId = $rowComment['Commande'];
            $commOrderIdShort = $rowComment['Commande_courte'];
            $cells = array($commId, $commOrderId, $commOrderIdShort, $comment, $author, $dateComm, $contact['name'], $mailtoLink, $rowComment['NumTelephone'], $dateNext, $fileLink);
        } else
          $cells = array($comment, $author, $dateComm, $contact['name'], $mailtoLink, $rowComment['NumTelephone'], $dateNext, $fileLink);
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
    $credentials['database']); // CONNEXION A LA DB READ

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

$orderId = filter_input(INPUT_GET, "id");
$orderIdShort = getOrderIdShort($orderId);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/commentList.html");
        $style = str_replace("{order}", $orderIdShort, $style);
        echo $style;

        if (isAdmin()) {

            $adminImage = generateImage("../png/admin.png", "Menu administrateur");
            $adminLink = generateLink("admin.php", $adminImage);
            echo $adminLink;
        }
    
        $charsetR = mysqli_set_charset($connectionR, "utf8");
        $charsetW = mysqli_set_charset($connectionW, "utf8");

        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);
        else if ($charsetW === FALSE)
            die("MySQL SET CHARSET error: ". $connectionW->error);

        $client = getContactName($orderId);
        echo "<h1>Contrat : " . $orderIdShort . " Montant : " . $client['price'] . "</h1>";
        $review = findReview($orderId);

        $paid = isItPaid($orderId);
        $colorCompta = ($paid['compta'] === "R" ? "#008800" : "#FF0000");
        $textCompta = ($paid['compta'] === "R" ? "Contrat reglé compta" : "Contrat non-reglé compta");
        $colorBase = ($paid['base'] === "R" ? "#008800" : "#FF0000");
        $textBase = ($paid['base'] === "R" ? "Contrat reglé base" : "Contrat non-reglé base");
        $h2style = "style=\"color:";

        $reviewName = $review['Name'] . " " . $review['Year'];
        $reviewLink = generateLink("searchReviewOrders.php?id=" . $review['Id'], $reviewName);
        $companyLink = generateLink("searchClientOrders.php?id=" . $client['id'], $client['name']);

        echo "<h2 " . $h2style . $colorCompta . "\">" . $textCompta . "</h2>";
        echo "<h2 " . $h2style . $colorBase . "\">" . $textBase . "</h2>";
        echo "<h2>Paru sur: " . $reviewLink . "</h2>";
        echo "<h2>Client: " . $companyLink . "</h2>";

        echo "<table>";

        if ($orderId === "") {

            $cells = array("ID","Commande","Commande courte","Commentaire","Auteur","Date commentaire","Nom de l'entreprise","E-mail","Téléphone","Prochaine relance","Fichier","Interagir");

            $repairImage = generateImage("../png/repair.png", "Remettre en ordre", 32, 32);
            $repairLink = generateLink("../commentSort.php", $repairImage, "_self");
            echo $repairLink;
        } else
            $cells = array("Commentaire","Auteur","Date commentaire","Nom de l'entreprise","E-mail","Téléphone","Prochaine relance","Fichier","Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        $phone = getPhoneNumber($orderId, $client['id']); // For the $form phone placeholder, takes existing phone value.
        $form = addUnpaidForm("../html/commentAdd.html", $orderId, $orderIdShort, $client['id'], $phone, $paid['compta']);
        echo $form;

        listComments($orderId);

        echo "</table><br><br><br>";
        echo "</html>";
    } else
        displayLogin("Veuillez vous connecter.");

    $connectionR->close();
    $connectionW->close();
}

?>