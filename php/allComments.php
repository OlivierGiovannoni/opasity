<?php

function listComments($orderId, $orderIdShort)
{
    $sqlComment = "SELECT Commentaire_id,Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance,Fichier,DernierCom FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowsComment = querySQL($sqlComment, $GLOBALS['connectionW']);

    foreach ($rowsComment as $rowComment) {

        if ($rowComment['Commentaire'] == "             ")
            continue ;
        $contact = getContactName($orderId);
        $dateComm = date("d/m/Y", strtotime($rowComment['Date']));
        $mailHref = generateLink($rowComment['AdresseMail'], $rowComment['AdresseMail']);

        if ($rowComment['Prochaine_relance'] == "1970-01-01" || $rowComment['Prochaine_relance'] == "0000-00-00")
            $dateNext = "Aucune";
        else
            $dateNext = date("d/m/Y", strtotime($rowComment['Prochaine_relance']));

        if ($rowComment['Fichier'] == "NULL")
            $fileHref = "Aucun";
        else
            $fileHref = generateLink($rowComment['Fichier'], basename($rowComment['Fichier']));

        $deleteLink = generateLink("deleteComment.php?id=" . $rowComment['Commentaire_id'], "Supprimer", "_self", "return confirm('Supprimer commentaire ?')"); // Create <a> link to delete comment

        $cells = array($rowComment['Commentaire'], $rowComment['Auteur'], $dateComm, $contact['name'], $mailHref, $rowComment['NumTelephone'], $dateNext, $fileHref, $deleteLink);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helperFunctions.php";

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

    $style = file_get_contents("../html/allComments.html");
    $style = str_replace("{order}", $orderIdShort, $style);
    echo $style;

    
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

    $cells = array("Commentaire","Auteur","Date commentaire","Nom de l'entreprise","E-mail","Téléphone","Prochaine relance","Fichier","Supprimer commentaire");
    $cells = generateRow($cells, true);
    foreach ($cells as $cell)
        echo $cell;


    $phone = getPhoneNumber($orderId, $client['id']); // For the $form phone placeholder, takes existing phone value.
    listComments($orderId, $orderIdShort);
    $form = addUnpaidForm("../html/addComment.html", $orderId, $orderIdShort, $client['id'], $phone, $paid['compta']);
    echo $form;

    echo "</table><br><br><br>";
    echo "</html>";

    $connectionR->close();
    $connectionW->close();
}

?>
