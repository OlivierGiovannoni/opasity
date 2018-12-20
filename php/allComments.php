<?php

$orderId = filter_input(INPUT_GET, "id");

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

function addUnpaidForm($htmlFileName, $orderId, $orderIdShort, $clientId, $paidStr)
{
    // Get file data.
    $htmlFileData = file_get_contents($htmlFileName);
    // Check if order is paid, to choose whether to display the <form> or not.
    if ($paidStr == "") {
        // Uncomment <form> region.
        $htmlFileData = str_replace("<!-- UNPAID", "", $htmlFileData);
        $htmlFileData = str_replace("-->", "", $htmlFileData);
        // Replace fake variables with real values.
        $htmlFileData = str_replace("{orderId}", $orderId, $htmlFileData);
        $htmlFileData = str_replace("{orderIdShort}", $orderIdShort, $htmlFileData);
        $htmlFileData = str_replace("{clientId}", $clientId, $htmlFileData);
    }
    echo $htmlFileData;
}

function findReview($infoId)
{
    $sqlReviewInfo = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    if ($resultReviewInfo = $GLOBALS['connectionR']->query($sqlReviewInfo)) {
        $rowReviewInfo = querySQL($sqlReviewInfo);
        $rowReviewInfo = mysqli_fetch_array($resultReviewInfo);
        $finalId = $rowReviewInfo['Revue_id'];
        $sqlReview = "SELECT id,Nom,Annee FROM webcontrat_revue WHERE id='$finalId';";
        if ($resultReview = $GLOBALS['connectionR']->query($sqlReview)) {

            $rowReview = mysqli_fetch_array($resultReview);
            $finalName = $rowReview['Nom'];
            $finalId = $rowReview['id'];
            $finalYear = $rowReview['Annee'];
            $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear);
            return ($final);
        } else {
            echo "Query error: ". $sqlReview ." // ". $GLOBALS['connectionR']->error;
        }
    } else {
        echo "Query error: ". $sqlReviewInfo ." // ". $GLOBALS['connectionR']->error;

    }
}

function getContactName($orderId)
{
    $sqlContactId = "SELECT Client_id,PrixHT FROM webcontrat_contrat WHERE Commande='$orderId' ORDER BY DateEmission DESC;";
    if ($resultContactId = $GLOBALS['connectionR']->query($sqlContactId)) {

        $rowContactId = mysqli_fetch_array($resultContactId);
        $contactId = $rowContactId['Client_id'];
        $sqlContactName = "SELECT NomContact1,NomSociete FROM webcontrat_client WHERE id='$contactId' ORDER BY DateCreation DESC;";
        if ($resultContactName = $GLOBALS['connectionR']->query($sqlContactName)) {

            $rowContactName = mysqli_fetch_array($resultContactName);
            $contactName = $rowContactName['NomSociete'];
            return (array('id' => $contactId, 'name' => $contactName, 'price' => $rowContactId['PrixHT']));
        } else {
            echo "Query error: ". $sqlContactName ." // ". $GLOBALS['connectionR']->error;
        }
    } else {
        echo "Query error: ". $sqlContactId ." // ". $GLOBALS['connectionR']->error;
    }
}


function listComments()
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $sqlComment = "SELECT Commentaire_id,Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance,Fichier FROM webcontrat_commentaire WHERE Commande_courte='$orderIdShort' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        while ($rowComment = mysqli_fetch_array($resultComment)) {

            $contact = getContactName($GLOBALS['orderId']);
            echo "<tr><td>" . $rowComment['Commentaire'] . "</td>";
            echo "<td>" . $rowComment['Auteur'] . "</td>";
            echo "<td>" . date("d/m/Y", strtotime($rowComment['Date'])) . "</td>";

            echo "<td>" . $contact['name'] . "</td>";
            $mailHref = "<a href=\"mailto:" . $rowComment['AdresseMail'] . "\">" . $rowComment['AdresseMail'] . "</a>";
            echo "<td>" . $mailHref . "</td>";
            echo "<td>" . $rowComment['NumTelephone'] . "</td>";

            if ($rowComment['Prochaine_relance'] == "1970-01-01" || $rowComment['Prochaine_relance'] == "0000-00-00")
                $newDate = "Aucune";
            else
                $newDate = date("d/m/Y", strtotime($rowComment['Prochaine_relance']));
            echo "<td>" . $newDate . "</td>";
            $fileHref = "<a href=" . $rowComment['Fichier'] . ">" . basename($rowComment['Fichier']) . "</a>";
            if ($rowComment['Fichier'] == "NULL")
                echo "<td>Aucun</td>";
            else
                echo "<td>" . $fileHref . "</td>";

            
            $deleteForm = "<form action=\"deleteComment.php\" method=\"post\">";
            $deleteId = "<input type=\"hidden\" name=\"commId\" value=\"" . $rowComment['Commentaire_id'] . "\">";
            $deleteSub = "<input type=\"submit\" value=\"Supprimer\" name=\"delConfirm\" onclick=\"return confirm('Supprimer le commentaire?');\"><br>";
            $closeForm = "</form>";

            echo "<td>" . $deleteForm . $deleteId . $deleteSub . $closeForm . "</td></tr>";

        }
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionR']->error;
    }
    $GLOBALS['connectionR']->close();
    $GLOBALS['connectionW']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    $style = file_get_contents("../html/allComments.html");
    $orderIdShort = getOrderIdShort($orderId);
    $style = str_replace("{order}", $orderIdShort, $style);

    echo $style;

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    $client = getContactName($orderId);
    echo "<h1>Contrat : " . $orderIdShort . " Montant : " . $client['price'] . "</h1>";
    $revue = findReview($orderId);

    echo "<h2 " . ($paidStr == "R" ? "style=color:#008800" : "style=color:#FF0000") . ">" . ($paidStr == "R" ? "Contrat reglé compta" : "Contrat non-reglé compta") . "</h2>";
    echo "<h2 " . ($paidBase == "R" ? "style=color:#008800" : "style=color:#FF0000") . ">" . ($paidBase == "R" ? "Contrat reglé base" : "Contrat non-reglé base") . "</h2>";
    echo "<h2>Paru sur: " . $revue['Name'] . "</h2>";
    echo "<h2>Client: " . $client['name'] . " (" . $client['id'] . ")</h2>";

    echo "<table>";
    $cells = array("Commentaire","Auteur","Date commentaire","Nom de l'entreprise","E-mail","Téléphone","Prochaine relance","Fichier","Supprimer commentaire");//tmp fichier
    $cells = generateRow($cells, true);
    foreach ($cells as $cell)
        echo $cell;

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    listComments();
    addUnpaidForm("../html/addComment.html", $orderId, $orderIdShort, $clientId, $paidStr);

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
